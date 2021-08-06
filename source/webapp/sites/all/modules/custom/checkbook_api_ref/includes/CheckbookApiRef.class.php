<?php

/**
 * Class CheckbookApiRef
 */
class CheckbookApiRef
{
  /**
   * @var string
   */
  public static $message_body = '';

  /**
    * @var string
    */
  public $successSubject = 'No changes';

  /**
   * Last ETL must successfully finish within last 12 hours
   */
  const SUCCESS_IF_RUN_LESS_THAN_X_SECONDS_AGO = 60 * 60 * 12;

  const CRON_LAST_RUN_DRUPAL_VAR = 'checkbook_api_ref_last_run';

  /**
   * @param $format
   * @return false|string
   */
  public function get_date($format)
  {
    return date($format, self::timeNow());
  }

  /**
   * @return int
   */
  public function timeNow()
  {
    return time();
  }

  /**
   * @param $csvfile
   * @param $result
   * @param $flag
   * @param $total_records
   * @param $force_quote
   * @return int
   */

 // Process mysql results array and write to csv file
  public function generateCsvFiles($csvfile,$result,$flag,$total_records,$force_quote)
  {
    //Get the column names.
    $columnNames = array();
    if (!empty($result) && $flag == 0) {
      $firstRow = $result[0];
      foreach ($firstRow as $colName => $val) {
        $columnNames[] = $colName;
        $total_records--;
        $flag =1;
      }
    }
    //write column names to the file.
    fputcsv($csvfile, $columnNames);
    foreach ($result as $row) {
      // Wrap code values with quotes so that leading zeros are displayed in csv
      $row[$force_quote] = "=\"" . $row[$force_quote] . "\"";
      fputcsv($csvfile, $row);
      $total_records--;
    }
    return($total_records);
  }


  /**
   * @param $message
   * @return array
   */
  public function generateRefFiles()
  {

    global $conf;

    $return = [
      'error' => false,
      'files' => [],
    ];

    ini_set('max_execution_time',60*60);

    $dir = variable_get('file_public_path', 'sites/default/files') . '/' . $conf['check_book']['data_feeds']['output_file_dir'];
    $dir = realpath($dir);
    if (!file_prepare_directory($dir, FILE_CREATE_DIRECTORY)) {
      $return['error'] = "Could not prepare directory $dir for generating reference data.";
      return $return;
    }

    $dir .= '/' . $conf['check_book']['ref_data_dir'];
    if (!file_prepare_directory($dir, FILE_CREATE_DIRECTORY)) {
      $return['error'] = "Could not prepare directory $dir for generating reference data.";
      return $return;
    }

    $ref_files_list = json_decode(file_get_contents(__DIR__.'/../config/ref_file_sql.json'));
    foreach($ref_files_list as $filename => $ref_file) {
      if ($ref_file->disabled) {
        continue;
      }

      $file = $dir . '/' . $filename . '.csv';
      $data_source = $ref_file->database ?? 'checkbook';
      $record_count_sql = "SELECT COUNT(*) as record_count FROM ( " . $ref_file->sql . ")  sub_query";
      $record_count = _checkbook_project_execute_sql_by_data_source($record_count_sql, $data_source);
      $total_records = $record_count[0]['record_count'];
      $flag = 0;
      // If record count is less than 100000 process as is
      if ($total_records <= 100000) {
        $result = _checkbook_project_execute_sql_by_data_source($ref_file->sql, $data_source);
        $file = fopen($file, 'w');
        self::generateCsvFiles($file,$result,$flag,$total_records,$ref_file->force_quote[0]);
        fclose($file);
        unset($result);
      }
      // if record count is greater than 100000 limit the mysql results
      // and process the csv in batch to prevent memory issue
      else{
        $startLimit = 100000;
        $offset = 0;
        $flag = 0;
        if (file_exists($file)){
          unlink($file);
        }
        $file = fopen($file, 'a+');
        while($total_records > 0 ) {
          $php_sql = str_replace('\\','',$ref_file->sql);
          $limit = ' LIMIT '.$startLimit .' OFFSET ' . $offset;
          $result = _checkbook_project_execute_sql_by_data_source($php_sql.$limit, $data_source);
          $offset = $startLimit+1;
          $total_records_update = self::generateCsvFiles($file,$result,$flag,$total_records,$ref_file->force_quote[0]);
          $total_records = $total_records_update;
          $flag =1;
        }
        fclose($file);
      }

      $file_info = [];
      $file = $dir . '/' . $filename . '.csv';
      $file_info['error'] = false;
      $file_info['old_timestamp'] = filemtime($file);
      if ($file_info['old_timestamp']) {
        $file_info['old_timestamp'] = date('Y-m-d', $file_info['old_timestamp']);
      }
      $file_info['old_filesize'] = filesize($file);
      $file_new = $file.'.new';
      $db_name = 'main';

      try{
        $file_info['new_filesize'] = filesize($file_new);
        $file_info['new_timestamp'] = filemtime($file_new);
        if ($file_info['new_filesize']) {
          $file_info['new_timestamp'] = date('Y-m-d', $file_info['new_timestamp']);
          if ($file_info['new_filesize'] !== $file_info['old_filesize']) {
            if (rename($file_new, $file)) {
                $file_info['updated'] = true;
                if ('No changes' == $this->successSubject) {
                  $this->successSubject = 'Updated';
                }
            }
            else {
                $file_info['error'] = 'Could not replace old file with new one: mv ' . $file_new .' ' .  $file;
                $this->successSubject = 'Fail';
            }
          } 
          else {
            $file_info['info'] = 'New file is same as old one, no changes needed';
            $file_info['updated'] = false;
            if(file_exists($file_new)){
              unlink($file_new);
            }
          }
        }   
        else {
          $file_info['warning'] = 'Newly generated file is zero byte size, keeping old file intact ';
          if(file_exists($file_new)){
            unlink($file_new);
          }
        }
      } catch(Exception $ex){
        $file_info['error'] = "Could not run sql command: $command Error:".$ex->getMessage();
        $this->successSubject = 'Fail';
      }

      $php_sql = str_replace('\\','',$ref_file->sql);
      $file_info['sample'] = _checkbook_project_execute_sql($php_sql.' LIMIT 5', $db_name, $data_source);
      $return['files'][$filename] = $file_info;
    }
    return $return;
  }


  /**
   * @param $message
   * @return array
   */
  public function gatherData(&$message)
  {
    global $conf;
    $result = self::generateRefFiles();
    $msg['body'] =
    [
        'status' => $this->successSubject,
        'error' => $result['error'],
        'files' => $result['files'],
    ];

    $date = self::get_date('m-d-Y');
    $msg['subject'] = "[{$conf['CHECKBOOK_ENV']}] API Ref Files Generated: " . $this->successSubject . " ($date)";
    $message = array_merge($message, $msg);
    $message['to'] = $conf['checkbook_dev_group_email'];
    return true;
  }
  /**
   * @return bool
   */
  public function run_cron()
  {
    global $conf;

    date_default_timezone_set('America/New_York');
    //always run cron for developer
    if (defined('CHECKBOOK_DEV')) {
      return self::sendmail();
    }

    if (!isset($conf['checkbook_dev_group_email'])) {
      //error_log("ETL STATUS MAIL CRON skips. Reason: \$conf['checkbook_dev_group_email'] not defined");
      return false;
    }

    $today = self::get_date('Y-m-d');
    $current_week = self::get_date('Y-W');
    $current_hour = (int)self::get_date('H');
    $first_monday_of_current_month = date('Y-m-d', strtotime("first monday of this month"));

    //If it is production, then run cron only on the first monday of the current month
    if (defined('CHECKBOOK_PROD') && $today !== $first_monday_of_current_month) {
      return false;
    }

    //If it is an internal environment, then run cron only on a Monday
    if (defined('CHECKBOOK_DEV') && variable_get(self::CRON_LAST_RUN_DRUPAL_VAR) == $current_week) {
      return false;
    }

    if (variable_get(self::CRON_LAST_RUN_DRUPAL_VAR) == $today) {
      //error_log("ETL STATUS MAIL CRON skips. Reason: already ran today :: $today :: ".variable_get($variable_name));
      return false;
    }

    if ($current_hour < 9 || $current_hour > 10) {
      //error_log("ETL STATUS MAIL CRON skips. Reason: will run between 9 AM and 11 AM EST :: current hour: $current_hour");
      return false;
    }

    variable_set(self::CRON_LAST_RUN_DRUPAL_VAR, $today);
    return self::sendmail();
  }

  /**
   * @return bool
   */
  public function sendmail()
  {
    global $conf;

    $from = $conf['email_from'];

    if (isset($conf['checkbook_dev_group_email'])) {
      $to = $conf['checkbook_dev_group_email'];
      try{
        drupal_mail('checkbook_api_ref', 'send-status', $to, null, ['dev_mode'=> false], $from);
      } catch(Exception $ex2){
        error_log($ex2->getMessage());
      }
    }
    return true;
  }

}
