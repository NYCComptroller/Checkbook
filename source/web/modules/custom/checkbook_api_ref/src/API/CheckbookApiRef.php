<?php

namespace Drupal\checkbook_api_ref\API;

use DateTime;
use Drupal\checkbook_log\LogHelper;
use Drupal\Core\File\FileSystemInterface;
use Exception;

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
   * @param $message
   * @return array
   */
  public function gatherData(&$message)
  {
    $result = self::generateRefFiles();
    $msg['body'] =
      [
        'status' => $this->successSubject,
        'error' => $result['error'],
        'files' => $result['files'],
      ];

    $date = self::get_date('m-d-Y');
    $CHECKBOOK_ENV = \Drupal::config('check_book')->get('CHECKBOOK_ENV') ?? null;
    $msg['subject'] = "[".$CHECKBOOK_ENV."] API Ref Files Generated: " . $this->successSubject . " ($date)";
    $message = array_merge($message, $msg);
    $checkbook_dev_group_email = \Drupal::config('check_book')->get('checkbook_dev_group_email') ?? null;
    $message['to'] = $checkbook_dev_group_email;
    //var_dump($message['body']['files']);
    return $message;
  }

// Process mysql results array and write to csv file
  public function generateCsvFiles($csvfile,$result,$flag,$total_records,$force_quote=null)
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
      if (isset($row[$force_quote])) {
        $row[$force_quote] = "=\"" . $row[$force_quote] . "\"";
      }
      fputcsv($csvfile, $row);
      $total_records--;
    }
    return($total_records);
  }

  public function generateRefFiles()
  {

    $return = [
      'error' => false,
      'files' => [],
    ];

    ini_set('max_execution_time',60*60);

    $dir = \Drupal::state()->get('file_public_path','sites/default/files') . '/' . \Drupal::config('check_book')->get('data_feeds')['output_file_dir'];
    if(!\Drupal::service('file_system')->preparedirectory($dir, \Drupal\Core\File\FileSystemInterface::CREATE_DIRECTORY)){
      $return['error'] = "Could not prepare directory $dir for generating reference data.";
      return $return;
    }

    $dir .= '/' . \Drupal::config('check_book')->get('ref_data_dir');
    if(!\Drupal::service('file_system')->preparedirectory($dir, \Drupal\Core\File\FileSystemInterface::CREATE_DIRECTORY)){
      $return['error'] = "Could not prepare directory $dir for generating reference data.";
      return $return;
    }

    $checkbook_api_ref_path = \Drupal::service('extension.list.module')->getPath('checkbook_api_ref');
    $ref_files_list = json_decode(file_get_contents(realpath($checkbook_api_ref_path) . "/config/ref_file_sql.json"));

    foreach($ref_files_list as $filename => $ref_file) {
      if (isset($ref_file->disabled) && $ref_file->disabled) {
        continue;
      }

      $new_file = $dir . '/' . $filename . '.csv';
      $old_file = $dir . '/' . $filename . '_old.csv';
      if(file_exists($new_file)){
        copy($new_file, $old_file);
      }
      $data_source = $ref_file->database ?? 'checkbook';
      $record_count_sql = "SELECT COUNT(*) as record_count FROM ( " . $ref_file->sql . ")  sub_query";
      $record_count = _checkbook_project_execute_sql_by_data_source($record_count_sql, $data_source);
      $total_records = $record_count[0]['record_count'];
      $flag = 0;
      // If record count is less than 100000 process as is
      if ($total_records <= 100000) {
        $result = _checkbook_project_execute_sql_by_data_source($ref_file->sql, $data_source);
        $file = fopen($new_file, 'w');
        self::generateCsvFiles($file,$result,$flag,$total_records,$ref_file->force_quote[0]??null);
        fclose($file);
        unset($result);
      }
      // if record count is greater than 100000 limit the mysql results
      // and process the csv in batch to prevent memory issue
      else{
        $startLimit = 100000;
        $offset = 0;
        $flag = 0;
        $file = fopen($new_file, 'w');
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
      $file_info['error'] = false;
      $file_info['old_timestamp'] = file_exists($old_file) ? filemtime($old_file) : filemtime($new_file);
      if ($file_info['old_timestamp']) {
        $file_info['old_timestamp'] = date('Y-m-d', $file_info['old_timestamp']);
      }
      $file_info['old_filesize'] = file_exists($old_file) ? filesize($old_file): filesize($new_file);

      try{
        $file_info['new_filesize'] = filesize($new_file);
        $file_info['new_timestamp'] = filemtime($new_file);
        if ($file_info['new_filesize']) {
          $file_info['new_timestamp'] = date('Y-m-d', $file_info['new_timestamp']);
          if ($file_info['new_filesize'] !== $file_info['old_filesize']) {
            $file_info['updated'] = true;
            if ('No changes' == $this->successSubject) {
              $this->successSubject = 'Updated';
            }
          }
          else {
            $file_info['info'] = 'New file is same as old one, no changes needed';
            $file_info['updated'] = false;
            if(file_exists($old_file)){
              rename($old_file, $new_file);
            }
          }
        }
        else {
          $file_info['warning'] = 'Newly generated file is zero byte size, keeping old file intact ';
          if(file_exists($old_file)){
            rename($old_file, $new_file);
          }
        }
      } catch(Exception $ex){
        $file_info['error'] = "Error:".$ex->getMessage();
        $this->successSubject = 'Fail';
      }

      if(file_exists($old_file)){
        unlink($old_file);
      }

      $php_sql = str_replace('\\','',$ref_file->sql) .' LIMIT 5';
      $file_info['sample'] = _checkbook_project_execute_sql_by_data_source($php_sql, $data_source);
      $return['files'][$filename] = $file_info;
    }

    return $return;
  }

  public function api_ref_cron()
  {
    //For cron timings details refer to https://tracker.reisystems.com/browse/NYCCHKBK-9894
    date_default_timezone_set('America/New_York');

    //always run cron for developer
    $CHECKBOOK_ENV = \Drupal::config('check_book')->get('CHECKBOOK_ENV') ?? null;

    $checkbook_dev_group_email = \Drupal::config('check_book')->get('checkbook_dev_group_email') ?? null;
    if (!isset($checkbook_dev_group_email)) {
      return false;
    }

    $today = self::get_date('Y-m-d');
    $monday_of_current_week = date('Y-m-d', strtotime("monday this week"));
    $config = \Drupal::service('config.factory')->getEditable('variable_get_set.api');
    $cron_last_run_week = new DateTime($config->get(self::CRON_LAST_RUN_DRUPAL_VAR));
    $cron_last_run_week = $cron_last_run_week->format("W");
    $current_hour = (int)self::get_date('H');
    $first_monday_of_current_month = date('Y-m-d', strtotime("first monday of this month"));

    //If it is staging or production environment, then run cron only on the first monday of every month
    if (isset($CHECKBOOK_ENV) && ($CHECKBOOK_ENV === "PROD" || $CHECKBOOK_ENV === "STAGE") && $today !== $first_monday_of_current_month) {
     return false;
    }

    //If it is an internal environment, then run cron only once every Monday
    $internal_environments = array("DEV","QA","UAT");
    if (isset($CHECKBOOK_ENV) && in_array($CHECKBOOK_ENV, $internal_environments) && $today !== $monday_of_current_week) {
      return false;
    }

    $config = \Drupal::service('config.factory')->getEditable('variable_get_set.api');
    if ($config->get(self::CRON_LAST_RUN_DRUPAL_VAR) == $today) {
      return false;
    }

    if ($current_hour < 9 || $current_hour > 10) {
      return false;
    }

    $config = \Drupal::service('config.factory')->getEditable('variable_get_set.api');
    $config->set(self::CRON_LAST_RUN_DRUPAL_VAR, $today);
    $config->save();

    return self::sendmail();
  }

  /**
     * @return bool
     */
    public function sendmail()
    {
      $from = \Drupal::config('system.site')->get('mail');
      $checkbook_dev_group_email = \Drupal::config('check_book')->get('checkbook_dev_group_email') ?? null;
      LogHelper::log_warn("checkbook_dev_group_email is: $checkbook_dev_group_email");
      if (isset($checkbook_dev_group_email)) {
        $to = $checkbook_dev_group_email;
        try{
          \Drupal::service('plugin.manager.mail')->mail('checkbook_api_ref', 'send-status', $to, null, array('dev_mode'=> false));
        } catch(Exception $ex2){
          LogHelper::log_error($ex2->getMessage());
        }
      }
      return true;
    }
}
