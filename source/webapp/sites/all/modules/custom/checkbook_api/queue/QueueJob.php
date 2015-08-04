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

        if($this->recordCount > $this->fileLimit) {
            $commands = $this->getCommands();
            $file_names = (array_keys($commands));

            foreach($commands as $filename=>$command) {
                $this->processCommand($filename, $command);
            }

            $this->generateCompressedFile($file_names);
            // TODO - Delete csv/xml parts
        }
        else {
            $filename = $this->prepareFileName();
            $command = $this->getJobCommand($filename);
            $this->processCommand($filename, $command);
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
     * Function will process a single file by number of records
     * @throws JobRecoveryException
     */
    private function processCommand($filename, $command) {

        try {

            LogHelper::log_debug("{$this->logId}: Executing job command.");
            shell_exec($command);
            LogHelper::log_debug("{$this->logId}: Completed executing job command.");

            $generated_csv_file = $this->fileOutputDir . '/' . $filename . '.csv';

            if (!is_file($generated_csv_file) || !is_writable($generated_csv_file)) {
                $msg = "{$this->logId}: Generated CSV out put file '{$generated_csv_file}' either do not exist or not writable.";
                LogHelper::log_error($msg);
                throw new Exception($msg);
            }
            else {
                LogHelper::log_debug("{$this->logId}: Generated CSV out put file '{$generated_csv_file}'.");
            }

            $request_criteria = $this->jobDetails['request_criteria'];
            $response_format = $request_criteria['global']['response_format'];
            LogHelper::log_debug("{$this->logId}: Response file format is " . $response_format);
            $search_criteria = new SearchCriteria($request_criteria, $response_format);
            $configuration = ConfigUtil::getConfiguration($request_criteria['global']['type_of_data'], $search_criteria->getConfigKey());

            $configured_response_columns = get_object_vars($configuration->dataset->displayConfiguration->$response_format->elementsColumn);
            $response_columns = is_array($request_criteria['responseColumns']) ? $request_criteria['responseColumns'] : array_keys($configured_response_columns);

            if ($response_format == 'csv') {
                $csv_headers = '"' . implode('","', $response_columns) . '"';
                LogHelper::log_debug("{$this->logId}: csvHeaders '{$csv_headers}'.");

                $cmd = "sed -i 1i'" . $csv_headers . "' " . DRUPAL_ROOT . '/' . $generated_csv_file;
                LogHelper::log_debug("{$this->logId}: Adjusting CSV headers for file: " . $cmd);
                shell_exec($cmd);
                LogHelper::log_debug("{$this->logId}: Updated CSV headers for file.");
            }
            else {
                if ($response_format == 'xml') {
                    $xml_file_name = $filename . '.xml';
                    LogHelper::log_debug("{$this->logId}: Started converting csv file : " . $generated_csv_file . ' to xml file.');
                    $this->generateXMLData($generated_csv_file, $xml_file_name, $response_columns, $configuration);
                    LogHelper::log_debug("{$this->logId}: Completed converting csv file : " . $generated_csv_file . ' to xml file.');
                }
            }
        }
        catch (Exception $e) {
            LogHelper::log_error("{$this->logId}: Exception occured while processing job '{$this->jobDetails['job_id']}' Exception is: " . $e);
            throw new JobRecoveryException("{$this->logId}: Exception occured while processing job '{$this->jobDetails['job_id']}' Exception is :" . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Generates the unix commands to create multiple files from the data source directly.
     * @return array
     */
    private function getCommands() {

        $num_files = ceil($this->recordCount/$this->fileLimit);
        $file_limit = $this->fileLimit;
        $commands = array();

        for($i=0;$i<$num_files;$i++) {
            $limit = (($i+1)*$file_limit)-1;
            $offset = $i*$this->fileLimit;
            $filename = $this->prepareFileName().'_part_'.$i;
            $command = $this->getJobCommand($filename, $limit,$offset);
            $commands[$filename] = $command;
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
    private function getJobCommand($filename, $limit = 'ALL',$offset = 0) {
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
   * @param $csv_file_path
   * @param $xml_file_name
   * @param $response_columns
   * @param $configuration
   * @throws JobRecoveryException
   */
  private function generateXMLData($csv_file_path, $xml_file_name, $response_columns, $configuration) {
    global $conf;
      ini_set('max_execution_time',90);

    $csv_file_handle = @fopen($csv_file_path, 'r');
    if (!$csv_file_handle) {
      $msg = "{$this->logId}: Could not get read handle to file '{$csv_file_path}' while converting to xml.";
      LogHelper::log_error($msg);
      throw new JobRecoveryException($msg);
    }

    if (ob_get_level() == 0) {
      ob_start();
    }

    $xml_file = $this->fileOutputDir . '/' . $xml_file_name;
    $xml_file_handle = @fopen($xml_file, 'w');

    if (!$xml_file_handle) {
      $msg = "{$this->logId}: Could not get write handle to file '{$xml_file}' while converting to xml.";
      LogHelper::log_error($msg);
      throw new JobRecoveryException($msg);
    }

    $root_element = $configuration->dataset->displayConfiguration->xml->rootElement;
    $record_count = 0;
    // TODO - check to avoid this.
    while (($data1 = fgetcsv($csv_file_handle, 10000, ',')) !== FALSE) {
      $record_count++;
    }
    fclose($csv_file_handle);
    LogHelper::log_debug("{$this->logId}: Total Records in '{$csv_file_path}' file: {$record_count}.");

    // Write headers:
    fwrite($xml_file_handle, '<?xml version="1.0"?><response><status><result>success</result></status>');

    // Start results:
    fwrite($xml_file_handle, '<result_records><record_count>' . $record_count . '</record_count><' . $root_element . '>');

    // Write records:
    $data_records = array();
    $record_buffer_count = 0;
    $saved_buffered_records = FALSE;
    $csv_file_handle = @fopen($csv_file_path, 'r');

    $xml_configuration = $configuration->dataset->displayConfiguration->xml;
    $xml_elements_configuration = $xml_configuration->elementsColumn;

    while (($row_data = fgetcsv($csv_file_handle, 10000, ',')) !== FALSE) {
      if ($saved_buffered_records) {
        $saved_buffered_records = FALSE;
      }

      $record = array();
      $i = 0;
      foreach ($response_columns as $response_column) {
        $record[$xml_elements_configuration->$response_column] = $row_data[$i];
        $i++;
      }

      $data_records[] = $record;
      $record_buffer_count++;

      if ($record_buffer_count == 5000) {
        LogHelper::log_debug("{$this->logId}: Writing 5000 records to file.");
        $this->saveXMLData($xml_file_handle, $data_records, $response_columns, $xml_configuration);
        $saved_buffered_records = TRUE;
        $record_buffer_count = 0;
        $data_records = array();

        sleep(1);
        ob_flush();
      }
    }

    // Save any unsaved data:
    if (!$saved_buffered_records) {
      LogHelper::log_debug("{$this->logId}: Writing rest of last $record_buffer_count records to file.");
      $this->saveXMLData($xml_file_handle, $data_records, $response_columns, $xml_configuration);
    }

    // Close document:
    fwrite($xml_file_handle, '</' . $root_element . '></result_records></response>');

    fclose($csv_file_handle);
    fclose($xml_file_handle);

    ob_end_flush();

    /*if(!@chmod($xmlFile,0644)){
      LogHelper::log_error("{$this->logId}: Could not update permissions to 0644 to file $xmlFile generated by db.");
    }*/
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
   * @param $xml_file_handle
   * @param $data_records
   * @param $response_columns
   * @param $xml_configuration
   */
  private function saveXMLData($xml_file_handle, $data_records, $response_columns, $xml_configuration) {
    // Save records:
    $xml_formatter = new XMLFormatter($data_records, $response_columns, $xml_configuration);
    $formatted_data = $xml_formatter->formatData();

    fwrite($xml_file_handle, $formatted_data);
  }

    /**
    * @return string
    */
    function getFilename() {
        static $app_file_name = NULL;
        if (!isset($app_file_name)) {
            if($this->recordCount > $this->fileLimit) {
                $app_file_name = $this->prepareFilePath() . '/' . $this->prepareFileName() . '.zip';
            }
            else {
                $request_criteria = $this->jobDetails['request_criteria'];
                $response_format = $request_criteria['global']['response_format'];
                $app_file_name = $this->prepareFilePath() . '/' . $this->prepareFileName() . '.' . $response_format;
            }
        }

        return $app_file_name;
    }

  /**
   * @return string
   */
  private function getCSVFilename() {
    return $db_file_name = $this->prepareFileName() . '.csv';
  }

  /**
   * @return string
   */
  private function getXMLFilename() {
    return $db_file_name = $this->prepareFileName() . '.xml';
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
