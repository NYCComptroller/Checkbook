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

    private $recordCount;

    private $fileLimit;

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

        $this->prepareFileOutputDir();

        $request_criteria = $this->jobDetails['request_criteria'];
        $response_format = $request_criteria['global']['response_format'];

        switch($response_format) {
            case "csv":
                if($this->recordCount > $this->fileLimit) {
                    $commands = $this->getCSVCommands();
                    $file_names = (array_keys($commands));
                    $this->processCommands($commands);
                    $this->generateCompressedFile($file_names);
                    // TODO - Delete csv/xml parts
                }
                else {
                    $filename = $this->prepareFileName();
                    $commands[$filename][] = $this->getCSVJobCommand($filename);
                    $this->processCommands($commands);
                }
                break;
            case "xml":
                $filename = $this->prepareFileName();
                $commands = $this->getXMLJobCommands($filename);
                $this->processCommands($commands);
                break;
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
        $file_limit = $this->fileLimit;
        $commands = array();

        for($i=0;$i<$num_files;$i++) {
            $limit = (($i+1)*$file_limit)-1;
            $offset = $i*$this->fileLimit;
            $filename = $this->prepareFileName().'_part_'.$i;

            //sql command
            $command = $this->getCSVJobCommand($filename, $limit, $offset);
            $commands[$filename][] = $command;

            //append header command
            $command = $this->getCSVHeaderCommand($filename);
            $commands[$filename][] = $command;
        }
        return $commands;
    }

    /**
     * Generates the unix commands to create the files from the data source directly.
     * This handles multiple files by executing sql with pagination.
     * @param $filename
     * @param string $limit
     * @param int $offset
     * @return array
     */
    private function getCSVJobCommand($filename, $limit = 'ALL',$offset = 0) {
        global $conf;

        $command = $this->jobDetails['data_command'];
        $command .= " LIMIT " . $limit . " OFFSET " . $offset;

        $file = DRUPAL_ROOT . '/' . $this->fileOutputDir . '/' . $filename . '.csv';
        $command = $conf['check_book']['data_feeds']['command']
            . " -c \"\\\\COPY (" . $command . ") TO '"
            . $file
            . "'  WITH DELIMITER ',' CSV QUOTE '\\\"' ESCAPE '\\\"' \" ";

        LogHelper::log_debug("{$this->logId}: Command for job {$this->jobDetails['job_id']}:'" . $command . "'");

        return $command;
    }

    /**
     * Given the filename, will return an executable command ot add CSV header row.
     * @param $filename
     * @return string
     */
    private function getCSVHeaderCommand($filename) {

        $request_criteria = $this->jobDetails['request_criteria'];
        $response_format = $request_criteria['global']['response_format'];
        $search_criteria = new SearchCriteria($request_criteria, $response_format);
        $configuration = ConfigUtil::getConfiguration($request_criteria['global']['type_of_data'], $search_criteria->getConfigKey());
        $configured_response_columns = get_object_vars($configuration->dataset->displayConfiguration->$response_format->elementsColumn);
        $response_columns = is_array($request_criteria['responseColumns']) ? $request_criteria['responseColumns'] : array_keys($configured_response_columns);
        $csv_headers = '"' . implode('","', $response_columns) . '"';
        $command = "sed -i 1i'" . $csv_headers . "' " . DRUPAL_ROOT . '/' . $this->fileOutputDir . '/' . $filename . '.csv';
        
        return $command;
    }

    /**
     * Given the filename, returns an array of commands to execute for xml file creation,
     * This function modifies the sql to handle derived columns dynamically.
     *
     * @param $filename
     * @return array
     */
    function getXMLJobCommands($filename) {
        global $conf;

        $query = $this->jobDetails['data_command'];
        //Handle this special case for now
        $query = str_replace("expenditure_object_name,","expenditure_object_names,",$query);
        $request_criteria = $this->jobDetails['request_criteria'];
        $response_format = $request_criteria['global']['response_format'];
        $search_criteria = new SearchCriteria($request_criteria, $response_format);
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
                $new_select_part .= str_replace($alias . $column,"COALESCE(CAST(" . $alias . $column . " AS VARCHAR),'')",$sql_part);
            }
            else {
                $new_select_part .= "COALESCE(CAST(" . $alias . $column . " AS VARCHAR),'')";
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

        $file = DRUPAL_ROOT . '/' . $this->fileOutputDir . '/' . $filename . '.xml';
        $commands = array();

        //replace '<' and '>' to allow escaping of db columns with these tags
        $query = str_replace("<","|LT|",$query);
        $query = str_replace(">","|GT|",$query);
        $open_tags = str_replace("<","|LT|",$open_tags);
        $open_tags = str_replace(">","|GT|",$open_tags);
        $close_tags = str_replace("<","|LT|",$close_tags);
        $close_tags = str_replace(">","|GT|",$close_tags);

        //sql command
        $command = $conf['check_book']['data_feeds']['command']
            . " -c \"\\\\COPY (" . $query . ") TO '"
            . $file
            . "' \" ";
        $commands[$filename][] = $command;

        //prepend open tags command
        $command = "sed -i '1i " . $open_tags . "' " . $file;
        $commands[$filename][] = $command;

        //append close tags command
        $command = "sed -i '$"."a" . $close_tags . "' " . $file;
        $commands[$filename][] = $command;

        //escape '&' for xml compatibility
        $command = "sed -i 's/&/&amp;/g' " . $file;
        $commands[$filename][] = $command;

        //escape '<' for xml compatibility
        $command = "sed -i 's/</\&lt;/g' " . $file;
        $commands[$filename][] = $command;

        //escape '>' for xml compatibility
        $command = "sed -i 's/>/\&gt;/g' " . $file;
        $commands[$filename][] = $command;

        //put back the '<' tags
        $command = "sed -i 's/|LT|/</g' " . $file;
        $commands[$filename][] = $command;

        //put back the '>' tags
        $command = "sed -i 's/|GT|/>/g' " . $file;
        $commands[$filename][] = $command;
        
        return $commands;

    /**
     * Executes the shell commands with error logging
     * @param $commands
     * @throws JobRecoveryException
     */
    function processCommands($commands) {

        try {
            foreach($commands as $filename=>$command) {
                foreach($commands[$filename] as $com) {
                    shell_exec($com);
                }
            }
        }
        catch (Exception $e) {
            LogHelper::log_error("{$this->logId}: Exception occured while processing job '{$this->jobDetails['job_id']}' Exception is: " . $e);
            throw new JobRecoveryException("{$this->logId}: Exception occured while processing job '{$this->jobDetails['job_id']}' Exception is :" . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $file_names
     */
    private function generateCompressedFile($file_names) {
        $request_criteria = $this->jobDetails['request_criteria'];
        $response_format = $request_criteria['global']['response_format'];
        $output_file = "";
        foreach($file_names as $file_name) {
            $output_file .= ' ' . DRUPAL_ROOT . '/' . $this->fileOutputDir . '/' . $file_name . '.' . $response_format;
        }
        $compress_file = DRUPAL_ROOT . '/' . $this->fileOutputDir . '/' . $this->prepareFileName() . '.zip';

        $cmd = "zip -j $compress_file $output_file ";
        LogHelper::log_debug("{$this->logId}: Started compressing output file: " . $cmd);
        shell_exec($cmd);
        if (!is_file($compress_file)) {
            LogHelper::log_error("{$this->logId}: Could not generate compress file $compress_file");
        }
        else {
            LogHelper::log_debug("{$this->logId}: Completed compressing output file.");
        }
    }

    /**
    * @return string
    */
    function getFilename() {
        static $app_file_name = NULL;
        $request_criteria = $this->jobDetails['request_criteria'];
        $response_format = $request_criteria['global']['response_format'];
        if (!isset($app_file_name)) {
            if($response_format == "csv" && $this->recordCount > $this->fileLimit) {
                $app_file_name = $this->prepareFilePath() . '/' . $this->prepareFileName() . '.zip';
            }
            else {

                $app_file_name = $this->prepareFilePath() . '/' . $this->prepareFileName() . '.' . $response_format;
            }
        }

        return $app_file_name;
    }

  /**
   *
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
