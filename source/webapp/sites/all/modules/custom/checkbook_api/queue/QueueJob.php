<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
*
* Copyright (C) 2012, 2013 New York City
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
*
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


/**
 * Class to process jobs in Queue
 */
class QueueJob {

    private $jobDetails;

    private $logId;

    private $fileOutputDir;

    private $tmpFileOutputDir;

    private $recordCount;

    private $fileLimit;

    private $responseFormat;

    function __construct($jobDetails) {
        $this->jobDetails = $jobDetails;
        $this->fileLimit = 1000000;
        $this->recordCount = $this->getRecordCount();
    }


    /**
     * If the number of records for a data feeds request is greater than 1 million,
     * the application will split the export file into multiple CSV files with
     * a maximum of 1 million per file and provide 1 compressed file.
     * If the number of records for a data feeds request is less than 1 million,
     * the application will generate only one CSV file containing all the records
     */
    function processJob() {

        try {
            $this->prepareQueueJob();
            switch($this->responseFormat) {
                case "csv":
                    if($this->recordCount > $this->fileLimit) {
                        $commands = $this->getCSVCommands();
                        $compressed_filename  = $this->prepareFileName();
                        $commands[$compressed_filename][] = $this->getMoveCommand($compressed_filename, 'zip');
                        $this->processCommands($commands);
                    }
                    else {
                        $filename = $this->prepareFileName();
                        $commands[$filename][] = $this->getCSVJobCommand($filename);
                        $commands[$filename][] = $this->getCSVHeaderCommand($filename);
                        $commands[$filename][] = $this->getMoveCommand($filename);
                        $this->processCommands($commands);
                    }
                    break;
                case "xml":
                    $filename = $this->prepareFileName();
                    $commands = $this->getXMLJobCommands($filename);
                    $commands[$filename][] = $this->getMoveCommand($filename);
                    $this->processCommands($commands);
                    break;
            }
        }
        catch (Exception $e) {
            LogHelper::log_error("{$this->logId}: Exception occurred while processing job '{$this->jobDetails['job_id']}' Exception is: " . $e);
            throw new JobRecoveryException("{$this->logId}: Exception occurred while processing job '{$this->jobDetails['job_id']}' Exception is :" . $e->getMessage(), $e->getCode(), $e);
        }
  }

    /**
     * Function to return the records count of the user request
     * @return Record
     */
    private function getRecordCount() {

        if(!isset($this->recordCount)) {
            $query = $this->jobDetails['data_command'];
            $from_position = strpos($query, 'FROM');
            $order_by_position = strpos($query, 'ORDER BY');
            $from_part = substr($query,$from_position, $order_by_position - $from_position);

            $query = "SELECT COUNT(*) as record_count " . $from_part;

            $db_name = "main";
            $data_source = "checkbook";

            if(stripos($this->jobDetails['name'], '_nycha')) {
              $data_source = "checkbook_nycha";
            }

            if(stripos($this->jobDetails['name'], '_oge')) {
              $data_source = "checkbook_oge";
            }

            $results = _checkbook_project_execute_sql($query, $db_name,  $data_source);
            $this->recordCount =  $results[0]["record_count"];
        }

        return $this->recordCount;
    }

    /**
     * Generates the unix commands to create multiple files from the data source directly.
     * @return array
     */
    private function getCSVCommands() {

        $num_files = ceil($this->recordCount/$this->fileLimit);
        $commands = array();

        $compressed_filename  = $this->prepareFileName();

        for($i=0;$i<$num_files;$i++) {
            $offset = $i*$this->fileLimit;
            $filename = $this->prepareFileName().'_part_'.$i;

            //sql command
            $commands[$filename][] = $this->getCSVJobCommand($filename, $this->fileLimit, $offset);

            //append header command
            $commands[$filename][] = $this->getCSVHeaderCommand($filename);

            //append file to zip and delete the file
            $commands[$filename][] = $this->getAppendToZipAndRemoveCommand($filename, $compressed_filename);
        }
        return $commands;
    }

    /**
     * Generates the unix commands to create the files from the data source directly.
     * This handles multiple files by executing sql with pagination.
     * @param $filename
     * @param string $limit
     * @param int $offset
     * @return string
     */
    private function getCSVJobCommand($filename, $limit = 'ALL',$offset = 0) {
        $file = $this->getFullPathToFile($filename,$this->tmpFileOutputDir);

        $query = $this->jobDetails['data_command'];
        $query .= " LIMIT " . $limit . " OFFSET " . $offset;

        $database = 'checkbook';
        if(stripos($this->jobDetails['name'], '_nycha')) {
          $database = "checkbook_nycha";
        }

        if(stripos($this->jobDetails['name'], '_oge')) {
          $database = "checkbook_oge";
        }

        $command = _checkbook_psql_command($database);
        $command .= " -c \"\\\\COPY (" . $query . ") TO '"
                . $file
                . "'  WITH DELIMITER ',' CSV QUOTE '\\\"' ESCAPE '\\\"' \" ";

        return $command;
    }

    /**
     * Given the filename, will return an executable command ot add CSV header row.
     * @param $filename
     * @return string
     */
    private function getCSVHeaderCommand($filename) {
        $response_format = $this->responseFormat;
        $request_criteria = $this->jobDetails['request_criteria'];
        $search_criteria = new SearchCriteria($request_criteria, $response_format);
        $configuration = ConfigUtil::getConfiguration($request_criteria['global']['type_of_data'], $search_criteria->getConfigKey());
        $configured_response_columns = get_object_vars($configuration->dataset->displayConfiguration->$response_format->elementsColumn);
        $response_columns = is_array($request_criteria['responseColumns']) ? $request_criteria['responseColumns'] : array_keys($configured_response_columns);
        $csv_headers = '"' . implode('","', $response_columns) . '"';
        $file = $this->getFullPathToFile($filename,$this->tmpFileOutputDir);
        $command = "sed -i '1s;^;" . $csv_headers . "\\".PHP_EOL.";' " . $file;
        return $command;
    }

    /**
     * Given the filename, returns an array of commands to execute for xml file creation,
     * This function modifies the sql to handle derived columns dynamically.
     *
     * @param $filename
     * @return array
     */
    private function getXMLJobCommands($filename) {
        $query = $this->jobDetails['data_command'];
        $request_criteria = $this->jobDetails['request_criteria'];
        $search_criteria = new SearchCriteria($request_criteria, $this->responseFormat);
        $config = ConfigUtil::getConfiguration($request_criteria['global']['type_of_data'], $search_criteria->getConfigKey());

        //map tags and build sql
        $rootElement = $config->dataset->displayConfiguration->xml->rootElement;
        $rowParentElement = $config->dataset->displayConfiguration->xml->rowParentElement;
        $elementsColumn = $config->dataset->displayConfiguration->xml->elementsColumn;
        $elementsColumn =  (array)$elementsColumn;
        $columnMappings = array_flip($elementsColumn);

        $end = strpos($query, 'FROM');
        $select_part = substr($query,0,$end);
        $select_part = str_replace("SELECT", "", $select_part);
        $sql_parts = explode(",", $select_part);

        $new_select_part = "'<".$rowParentElement.">'";
        foreach($sql_parts as $sql_part) {
            $sql_part = trim($sql_part);
            $is_derived_column = strpos(strtoupper($sql_part), "CASE WHEN") !== FALSE;

            //get column and alias
            $alias = "";
            if($is_derived_column) {
                $pos = strpos($sql_part, " AS");
                $column = trim(str_replace("AS","",substr($sql_part,$pos)));
            }
            else {
                $pos = strpos($sql_part, " AS");
                $pos = $pos !== FALSE ? $pos : strlen($sql_part);
                $column = substr($sql_part, 0, $pos);
            }

            if (strpos($sql_part,".") !== false) {
                $alias_pos = strpos($sql_part,".");
                $alias = substr($sql_part, $alias_pos-2, 3);
                $column = str_replace($alias,"",$column);
            }

            //Handle derived columns
            $tag = $columnMappings[$column] == "" ? $column : $columnMappings[$column];

            //column open tag
            $new_select_part .= "\n||'<".$tag.">' || ";

            if ($is_derived_column) {
                $sql_part = substr_replace($sql_part, "", $pos);
                $new_select_part .= str_replace($alias . $column,"REPLACE(REPLACE(REPLACE(COALESCE(CAST(" . $alias . $column . " AS VARCHAR),''),'&','&amp;'),'>','&gt;'),'<','&lt;')",$sql_part);
            }
            else {
                $new_select_part .= "REPLACE(REPLACE(REPLACE(COALESCE(CAST(" . $alias . $column . " AS VARCHAR),''),'&','&amp;'),'>','&gt;'),'<','&lt;')";
            }

            //column close tag
            $new_select_part .= " || '</".$tag.">'";
        }
        $new_select_part .= "||'</".$rowParentElement.">'";
        $new_select_part = "SELECT ".ltrim($new_select_part,"\n||")."\n";
        $query = substr_replace($query, $new_select_part, 0, $end);

        //open/close tags
        $open_tags = "<?xml version=\"1.0\"?><response><status><result>success</result></status>";
        $open_tags .= "<result_records><record_count>".$this->getRecordCount()."</record_count><".$rootElement.">";
        $close_tags = "</".$rootElement."></result_records></response>";

        $file = $this->getFullPathToFile($filename,$this->tmpFileOutputDir);
        $commands = array();

        //sql command
        $command = _checkbook_psql_command();
        $command .= " -c \"\\\\COPY (" . $query . ") TO '"
                    . $file
                    . "' \" ";
        $commands[$filename][] = $command;

        //prepend open tags command
        $command = "sed -i '1i " . $open_tags . "' " . $file;
        $commands[$filename][] = $command;

        //append close tags command
        $command = "sed -i '$"."a" . $close_tags . "' " . $file;
        $commands[$filename][] = $command;

        //xmllint command to format the xml
        $formatted_filename = $this->tmpFileOutputDir .'/formatted_'. $filename . '.xml';
        $maxmem = 1024 * 1024 * 500;  // 500 MB
        $command = "xmllint --format $file --output $formatted_filename --maxmem $maxmem";
        $commands[$filename][] = $command;

        //move the formatted file back
        $command = "mv $formatted_filename $file";
        $commands[$filename][] = $command;

        return $commands;
    }

    /**
     * Function to get the command that moves the file from tmp directory
     * to the data feeds directory.
     * @param $filename
     * @param $format
     * @return mixed
     */
    private function getMoveCommand($filename, $format = '') {
        $tmp_file = $this->getFullPathToFile($filename,$this->tmpFileOutputDir, $format);
        $file = $this->getFullPathToFile($filename,$this->fileOutputDir, $format);
        $command = "mv $tmp_file $file";
        return $command;
    }

    /**
     * Build the command to append file into a zip
     * @param $file_name
     * @param $compressed_filename
     * @return string
     */
    private function getAppendToZipAndRemoveCommand($file_name, $compressed_filename) {

        $output_file = ' '.$this->getFullPathToFile($file_name, $this->tmpFileOutputDir);

        $compressed_file = $this->tmpFileOutputDir . '/' . $compressed_filename . '.zip';
        /**
         * -j
         * Store just the name of a saved file (junk the path), and do not store directory names.
         * By default, zip will store the full path (relative to the current directory).
         *
         */

        $command = "zip -j $compressed_file $output_file; rm -f $output_file";
        return $command;
    }

    /**
     * Executes the shell commands with error logging
     * @param $commands
     * @throws JobRecoveryException
     */
    private function processCommands($commands) {

        try {
            foreach($commands as $filename=>$command) {
                foreach($commands[$filename] as $com) {
                    LogHelper::log_debug("{$this->logId}: Executing command for job {$this->jobDetails['job_id']}:'" . $com . "'");
                    shell_exec($com);
                }
            }
        }
        catch (Exception $e) {
            LogHelper::log_error("{$this->logId}: Exception occurred while processing job '{$this->jobDetails['job_id']}' Exception is: " . $e);
            throw new JobRecoveryException("{$this->logId}: Exception occurred while processing job '{$this->jobDetails['job_id']}' Exception is :" . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Function to get full path to the output file
     * @param $filename
     * @param $directory
     * @param $format
     * @return null|string
     */
    private function getFullPathToFile($filename,$directory, $format = '') {
        $format = $format ?: $this->responseFormat;
        switch($directory) {
            case $this->fileOutputDir:
                return DRUPAL_ROOT . '/' . $this->fileOutputDir . '/' . $filename . '.' . $format;
            case $this->tmpFileOutputDir:
                return $this->tmpFileOutputDir .'/'. $filename . '.' . $format;
            default:
                return null;
        }
    }

    /**
    * @return string
    */
    function getFilename() {
        static $app_file_name = NULL;
        if (!isset($app_file_name)) {
            if($this->responseFormat == "csv" && $this->recordCount > $this->fileLimit) {
                $app_file_name = $this->prepareFilePath() . '/' . $this->prepareFileName() . '.zip';
            }
            else {
                $app_file_name = $this->prepareFilePath() . '/' . $this->prepareFileName() . '.' . $this->responseFormat;
            }
        }

        return $app_file_name;
    }

    /**
     * Function to prepare the file directories and response format variables
     */
    private function prepareQueueJob() {
        $request_criteria = $this->jobDetails['request_criteria'];
        $this->responseFormat = $request_criteria['global']['response_format'];
        $this->prepareTmpFileOutputDir();
        $this->prepareFileOutputDir();
    }

    /**
    *Prepares the data feeds directory for output
    */
    private function prepareFileOutputDir() {
        global $conf;
        if (isset($this->fileOutputDir)) {
            return;
        }

        $dir = variable_get('file_public_path', 'sites/default/files')
        . '/' . $conf['check_book']['data_feeds']['output_file_dir'];

        $this->prepareDirectory($dir);

        $paths = explode('/', $this->prepareFilePath());
        foreach ($paths as $path) {
            $dir .= '/' . $path;
            $this->prepareDirectory($dir);
        }
        $this->fileOutputDir = $dir;
    }

    /**
     * Prepares the tmp directory for output
     */
    private function prepareTmpFileOutputDir() {
        global $conf;

        if (isset($this->tmpFileOutputDir)) {
            return;
        }

        $tmpDir =  (isset($conf['check_book']['tmpdir']) && is_dir($conf['check_book']['tmpdir'])) ? rtrim($conf['check_book']['tmpdir'],'/') : '/tmp';

        if(!is_writable($tmpDir)){
            throw new JobRecoveryException("{$this->logId}: Could not prepare file output directory {$tmpDir}.Should check if this directory is writable.");
        }

        $this->tmpFileOutputDir = $tmpDir;
    }

  /**
   * @param $dir
   * @throws JobRecoveryException
   */
  private function prepareDirectory($dir) {
    if (!file_prepare_directory($dir, FILE_CREATE_DIRECTORY)) {
      throw new JobRecoveryException("{$this->logId}: Could not prepare file output directory $dir.Should check if this directory is writable.");
    }
  }

  /**
   * @return string
   */
  private function prepareFilePath() {
    static $file_path = NULL;
    if (!isset($file_path)) {
      $file_path = $this->jobDetails['name'] . '/' . date('Y-m-d');
    }

    return $file_path;
  }

  /**
   * @return string
   */
  private function prepareFileName() {
    static $file_name = NULL;
    if (!isset($file_name)) {
      $file_name = $this->jobDetails['name'] . '_' . $this->jobDetails['job_id'] . '_' . date('mdY_His');
    }

    return $file_name;
  }

  /**
   * @param $log_id
   */
  function setLogId($log_id) {
    $this->logId = $log_id;
  }


}

class JobRecoveryException extends Exception {
}
