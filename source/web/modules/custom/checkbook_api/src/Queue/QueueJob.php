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

namespace Drupal\checkbook_api\Queue;

use Drupal\checkbook_api\config\ConfigUtil;
use Drupal\checkbook_api\Criteria\SearchCriteria;
use Drupal\checkbook_api\Utilities\APIUtil;
use Drupal\checkbook_log\LogHelper;
use Drupal\Core\File\FileSystemInterface;
use Exception;

/**
 * Class to process jobs in Queue
 */
class QueueJob {

  private $jobDetails;

  private $logId;

  private $fileOutputDir;

  private $tmpFileOutputDir;

  private $recordCount;

  private $csvFileLimit;

  private $xmlFileLimit;

  private $responseFormat;

  function __construct($jobDetails) {
    $this->jobDetails = $jobDetails;
    $this->csvFileLimit = 500000;
    $this->xmlFileLimit = 250000;
    $this->recordCount = $this->getRecordCount();
  }


  /**
   * If the number of records for a data feeds request is greater than 1
   * million, the application will split the export file into multiple CSV
   * files with a maximum of 1 million per file and provide 1 compressed file.
   * If the number of records for a data feeds request is less than 1 million,
   * the application will generate only one CSV file containing all the records
   *
   * @throws \Drupal\checkbook_api\Queue\JobRecoveryException
   */
  function processJob() {

    try {
      $this->prepareQueueJob();
      $commands = NULL;
      switch($this->responseFormat) {
        case "csv":
          if ($this->recordCount > $this->csvFileLimit) {
            $this->runCSVCommands();
            $compressed_filename  = $this->prepareFileName();
            $commands[$compressed_filename][] = $this->getMoveCommand($compressed_filename, 'zip');
          }
          else {
            $filename = $this->prepareFileName();
            $commands[$filename][] = $this->getCSVJobCommand($filename);
            //running the previous created command
            $this->processCommands($commands);
            //reset commands since previous line has run the commands that were there
            $commands = array();

            //adding header to file created in previous command above this
            $this->addCSVHeader($filename);

            $commands[$filename][] = $this->getMoveCommand($filename);
          }
          break;
        case "xml":
          if ($this->recordCount > $this->xmlFileLimit) {
            $this->runXMLJobCommands();
            $compressed_filename  = $this->prepareFileName();
            $commands[$compressed_filename][] = $this->getMoveCommand($compressed_filename, 'zip');
          }
          else{
            $filename = $this->prepareFileName();
            $this->runXMLJobCommand($filename);
            $commands[$filename][] = $this->getMoveCommand($filename, 'xml.zip');
          }
          break;
      }
      $this->processCommands($commands);
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
   * Generates the unix commands to create multiple files from the data source directly. Runs those as well.
   */
  private function runCSVCommands() {

    $num_files = ceil($this->recordCount/$this->csvFileLimit);
    $commands = array();

    $compressed_filename  = $this->prepareFileName();

    for ($i = 0; $i < $num_files; $i++) {
      $offset = $i * $this->csvFileLimit;
      $filename = $this->prepareFileName() . '_part_' . $i;

      // SQL command.
      $commands[$filename][] = $this->getCSVJobCommand($filename, $this->csvFileLimit, $offset);
      $this->processCommands($commands);
      $commands = array();

      // Append header command (used to be sed command returned).
      $this->addCSVHeader($filename);

      // Append file to zip and delete the file.
      $commands[$filename][] = $this->getAppendToZipAndRemoveCommand($filename, $compressed_filename);
      $this->processCommands($commands);
      $commands = array();
    }
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
    if (stripos($this->jobDetails['name'], '_nycha')) {
      $database = "checkbook_nycha";
    }

    if (stripos($this->jobDetails['name'], '_oge')) {
      $database = "checkbook_oge";
    }

    $command = _checkbook_psql_command($database);
    $command .= " -c \"\\\\COPY (" . $query . ") TO '"
      . $file
      . "'  WITH DELIMITER ',' CSV \" 2>&1";
    LogHelper::log_notice("DataFeeds :: QueueJob::getCSVJobCommand() cmd: ".$command);
    return $command;
  }

  /**
   * Given the filename, will add CSV header row.
   * @param $filename
   */
  private function addCSVHeader($filename) {
    $response_format = $this->responseFormat;
    $request_criteria = $this->jobDetails['request_criteria'];
    $search_criteria = new SearchCriteria($request_criteria, $response_format);
    $configuration = ConfigUtil::getConfiguration($request_criteria['global']['type_of_data'], $search_criteria->getConfigKey());
    $configured_response_columns = get_object_vars($configuration->dataset->displayConfiguration->$response_format->elementsColumn);
    $response_columns = is_array($request_criteria['responseColumns']) ? $request_criteria['responseColumns'] : array_keys($configured_response_columns);
    $csv_headers = '"' . implode('","', $response_columns) . '"';
    $file = $this->getFullPathToFile($filename,$this->tmpFileOutputDir);
    //below replaces the following command:- " sed -i '1s;^;" . $csv_headers . "\\".PHP_EOL.";' " . $file;
    APIUtil::prependToFile($file,$csv_headers . "\\".PHP_EOL.";");
    LogHelper::log_notice("DataFeeds :: QueueJob::addCSVHeader() calling APIUtil::prependToFile with headers: ".$csv_headers);
  }

  /**
   * Given the filename, runs commands to execute for xml file creation,
   * This function modifies the sql to handle derived columns dynamically.
   */
  private function runXMLJobCommands() {
    $query = $this->jobDetails['data_command'];
    $request_criteria = $this->jobDetails['request_criteria'];
    $search_criteria = new SearchCriteria($request_criteria, $this->responseFormat);
    $config = ConfigUtil::getConfiguration($request_criteria['global']['type_of_data'], $search_criteria->getConfigKey());

    // Map tags and build SQL.
    $rowParentElement = $config->dataset->displayConfiguration->xml->rowParentElement;
    $elementsColumn = $config->dataset->displayConfiguration->xml->elementsColumn;
    $elementsColumn =  (array)$elementsColumn;
    $columnMappings = array_flip($elementsColumn);

    $end = strpos($query, 'FROM');
    $select_part = substr($query,0,$end);
    $select_part = str_replace("SELECT", "", $select_part);
    $sql_parts = explode(",", $select_part);

    $new_select_part = "'<" . $rowParentElement . ">'";
    foreach ($sql_parts as $sql_part) {
      $sql_part = trim($sql_part);
      $is_derived_column = strpos(strtoupper($sql_part), "CASE WHEN") !== FALSE;

      //get column and alias
      $alias = "";
      if ($is_derived_column) {
        $pos = strripos($sql_part, " AS");
        $column = trim(str_replace("AS","", substr($sql_part, $pos)));
      }
      else {
        $pos = strripos($sql_part, " AS");
        $pos = $pos !== FALSE ? $pos : strlen($sql_part);
        $column = substr($sql_part, 0, $pos);
      }

      if (strpos($sql_part,".") !== false) {
        $alias_pos = strpos($sql_part,".");
        $alias = substr($sql_part, $alias_pos-2, 3);
        $column = str_replace($alias,"",$column);
      }

      // Handle derived columns.
      $tag = $columnMappings[$column] == "" ? $column : $columnMappings[$column];

      if ($tag) {
        //column open tag
        $new_select_part .= "\n || '<".$tag.">' || ";
        if ($is_derived_column) {
          $sql_part = substr_replace($sql_part, "", $pos);
          $cast_section = "regexp_replace(COALESCE(CAST(" . $sql_part . " AS VARCHAR), ''), '[\u0080-\u00ff]', '', 'g')";
          $new_select_part .= "REPLACE(REPLACE(REPLACE(". $cast_section .",'&','&amp;'), '>', '&gt;'), '<', '&lt;')";
        }
        else {
          $new_select_part .= "REPLACE(REPLACE(REPLACE(regexp_replace(COALESCE(CAST(" . $alias . $column . " AS VARCHAR), ''), '[\u0080-\u00ff]', '', 'g'), '&', '&amp;'), '>', '&gt;'), '<', '&lt;')";
        }

        //column close tag
        $new_select_part .= " || '</" . $tag . ">'";
      }
    }
    $new_select_part .= " || '</" . $rowParentElement . ">'";
    $new_select_part = "SELECT " . ltrim($new_select_part,"\n || ") . "\n";
    $query = substr_replace($query, $new_select_part, 0, $end);

    // Map tags and build SQL.
    $rootElement = $config->dataset->displayConfiguration->xml->rootElement;

    // Open/close tags.
    $open_tags = "<?xml version=\"1.0\"?><response><status><result>success</result></status>";
    $open_tags .= "<result_records><record_count>" . $this->getRecordCount() . "</record_count><" . $rootElement . ">";
    $close_tags = "</".$rootElement."></result_records></response>";

    $commands = [];

    $database = 'checkbook';
    if (stripos($this->jobDetails['name'], '_nycha')) {
      $database = "checkbook_nycha";
    }

    if(stripos($this->jobDetails['name'], '_oge')) {
      $database = "checkbook_oge";
    }

    $num_files = ceil($this->recordCount/$this->xmlFileLimit);
    $compressed_filename  = $this->prepareFileName();

    for ($i = 0; $i < $num_files; $i++) {
      $offset = $i * $this->xmlFileLimit;
      $filename = $this->prepareFileName() . '_part_' . $i;
      $file = $this->getFullPathToFile($filename,$this->tmpFileOutputDir);
      $updated_query = $query . " LIMIT " . $this->xmlFileLimit . " OFFSET " . $offset;

      // SQL command.
      $command = _checkbook_psql_command($database);
      $command .= " -c \"\\\\COPY (" . $updated_query . ") TO '"
        . $file
        . "' \" ";
      LogHelper::log_notice("DataFeeds :: QueueJob::getXMLJobCommand() cmd: ".$command);

      $commands[$filename][] = $command;

      $this->processCommands($commands);
      $commands = array();

      // Prepend open tags command (replaced -  " sed -i '1i " . $open_tags . "' " . $file;).
      APIUtil::prependToFile($file,$open_tags);

      // Append close tags command (replaced - " sed -i '$"."a" . $close_tags . "' " . $file;).
      APIUtil::appendToFile($file,$close_tags);

      // Append file to zip and delete the file.
      $commands[$filename][] = $this->getAppendToZipAndRemoveCommand($filename, $compressed_filename);
      $this->processCommands($commands);
      $commands = array();
    }
  }

  /**
   * Given the filename, returns an array of commands to execute for xml file creation,
   * This function modifies the sql to handle derived columns dynamically.
   *
   * @param $filename
   */
  private function runXMLJobCommand($filename) {
    $query = $this->jobDetails['data_command'];
    $request_criteria = $this->jobDetails['request_criteria'];
    $search_criteria = new SearchCriteria($request_criteria, $this->responseFormat);
    $config = ConfigUtil::getConfiguration($request_criteria['global']['type_of_data'], $search_criteria->getConfigKey());

    //map tags and build sql
    $rowParentElement = $config->dataset->displayConfiguration->xml->rowParentElement;
    $elementsColumn = $config->dataset->displayConfiguration->xml->elementsColumn;
    $elementsColumn =  (array)$elementsColumn;
    $columnMappings = array_flip($elementsColumn);

    $end = strpos($query, 'FROM');
    $select_part = substr($query,0,$end);
    $select_part = str_replace("SELECT", "", $select_part);
    $sql_parts = explode(",", $select_part);

    $new_select_part = "'<" . $rowParentElement . ">'";
    foreach ($sql_parts as $sql_part) {
      $sql_part = trim($sql_part);
      $is_derived_column = strpos(strtoupper($sql_part), "CASE WHEN") !== FALSE;

      //get column and alias
      $alias = "";
      if ($is_derived_column) {
        $pos = strpos($sql_part, " AS");
        $column = trim(str_replace("AS", "", substr($sql_part,$pos)));
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

      if ($tag) {
        // Column open tag.
        $new_select_part .= "\n || '<" . $tag . ">' || ";
        if ($is_derived_column) {
          $sql_part = substr_replace($sql_part, "", $pos);
          $cast_section = "regexp_replace(COALESCE(CAST(" . $sql_part . " AS VARCHAR),''), '[\u0080-\u00ff]', '', 'g')";
          $new_select_part .= "REPLACE(REPLACE(REPLACE(". $cast_section .",'&','&amp;'),'>','&gt;'),'<','&lt;')";
        }
        else {
          $new_select_part .= "REPLACE(REPLACE(REPLACE(regexp_replace(COALESCE(CAST(" . $alias . $column . " AS VARCHAR),''), '[\u0080-\u00ff]', '', 'g'),'&','&amp;'),'>','&gt;'),'<','&lt;')";
        }

        // Column close tag.
        $new_select_part .= " || '</" . $tag . ">'";
      }
    }
    $new_select_part .= " || '</" . $rowParentElement . ">'";
    $new_select_part = "SELECT " . ltrim($new_select_part,"\n || ") . "\n";
    $query = substr_replace($query, $new_select_part, 0, $end);

    //map tags and build sql
    $rootElement = $config->dataset->displayConfiguration->xml->rootElement;

    //open/close tags
    $open_tags = "<?xml version=\"1.0\"?><response><status><result>success</result></status>";
    $open_tags .= "<result_records><record_count>" . $this->getRecordCount() . "</record_count><" . $rootElement . ">";
    $close_tags = "</" . $rootElement . "></result_records></response>";

    $file = $this->getFullPathToFile($filename,$this->tmpFileOutputDir);
    $commands = [];

    $database = 'checkbook';
    if (stripos($this->jobDetails['name'], '_nycha')) {
      $database = "checkbook_nycha";
    }

    if (stripos($this->jobDetails['name'], '_oge')) {
      $database = "checkbook_oge";
    }

    //sql command
    $command = _checkbook_psql_command($database);
    $command .= " -c \"\\\\COPY (" . $query . ") TO '"
      . $file
      . "' \" ";
    LogHelper::log_notice("DataFeeds :: QueueJob::getXMLJobCommand() cmd: ".$command);
    $commands[$filename][] = $command;
    $this->processCommands($commands);
    //reset commands as previous line has run those
    $commands = array();

    //prepend open tags command. Replaced the following command:- "sed -i '1i " . $open_tags . "' " . $file;
    APIUtil::prependToFile($file,$open_tags);

    //append close tags command. Replaced the following command:- "sed -i '$"."a" . $close_tags . "' " . $file;
    APIUtil::appendToFile($file,$close_tags);

    //Zip file
    $commands[$filename][] = "zip $file.zip $file";
    $commands[$filename][] = "rm $file";
    $this->processCommands($commands);
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

    $output_file = ' ' . $this->getFullPathToFile($file_name, $this->tmpFileOutputDir);

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
        return \Drupal::root() . '/' . $this->fileOutputDir . '/' . $filename . '.' . $format;
      case $this->tmpFileOutputDir:
        return $this->tmpFileOutputDir .'/'. $filename . '.' . $format;
      default:
        return null;
    }
  }

  /**
   * Returns the name of output file generated
   * @return string
   */
  function getFilename() {
    static $app_file_name = NULL;
    if (!isset($app_file_name)) {
      switch ($this->responseFormat){
        case 'xml':
          if($this->recordCount > $this->xmlFileLimit){
            $app_file_name = $this->prepareFilePath() . '/' . $this->prepareFileName() . '.zip';
          }else{
            $app_file_name = $this->prepareFilePath() . '/' . $this->prepareFileName() . '.' . $this->responseFormat . '.zip';
          }
          break;
        case 'csv':
          if($this->recordCount > $this->csvFileLimit){
            $app_file_name = $this->prepareFilePath() . '/' . $this->prepareFileName() . '.zip';
          }else{
            $app_file_name = $this->prepareFilePath() . '/' . $this->prepareFileName() . '.' . $this->responseFormat;
          }
          break;
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
    if (isset($this->fileOutputDir)) {
      return;
    }

    $dir = \Drupal::state()->get('file_public_path','sites/default/files')
          . '/' . \Drupal::config('check_book')->get('data_feeds')['output_file_dir'];

    //@ToDo: testing symlink for datafeeds directory since it is symlink
    //$this->prepareDirectory($dir);

    $paths = explode('/', $this->prepareFilePath());
    foreach ($paths as $path) {
      $dir .= '/' . $path;
      $this->prepareDirectory($dir);
    }
    $this->fileOutputDir = $dir;
  }

  /**
   * Prepares the tmp directory for output
   *
   * @throws \Drupal\checkbook_api\Queue\JobRecoveryException
   */
  private function prepareTmpFileOutputDir() {
    if (isset($this->tmpFileOutputDir)) {
      return;
    }

    $checkbook_tempdir = \Drupal::config('check_book')->get('tmpdir');
    $tmpDir =  (isset($checkbook_tempdir) && is_dir($checkbook_tempdir)) ? rtrim($checkbook_tempdir,'/') : '/tmp';


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
    if(!\Drupal::service('file_system')->preparedirectory($dir, FileSystemInterface::CREATE_DIRECTORY)){
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


