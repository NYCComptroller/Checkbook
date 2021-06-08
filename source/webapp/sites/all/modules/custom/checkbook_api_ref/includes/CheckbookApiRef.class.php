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
   * Last ETL must successfully finish within last 12 hours
   */
  const SUCCESS_IF_RUN_LESS_THAN_X_SECONDS_AGO = 60 * 60 * 12;

  const CRON_LAST_RUN_DRUPAL_VAR = 'checkbook_api_ref_status_last_run';

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
   * @param $message
   * @return array
   */
  public function generateRefFiles()
  {

    global $conf;

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

      $file_info = [];
      $file = $dir . '/' . $filename . '.csv';
      $file_info['error'] = false;

      $data_source = $ref_file->database??'checkbook';
      $result =_checkbook_project_execute_sql_by_data_source ($ref_file->sql,$data_source);

      //Get the column names.
      $columnNames = array();
      if(!empty($result)){
        $firstRow = $result[0];
        foreach($firstRow as $colName => $val){
          $columnNames[] = $colName;
        }
      }
      $file = fopen($file, 'w');
      //write column names to the file.
      fputcsv($file, $columnNames);

      foreach ($result as $row) {
        // Wrap code values with quotes so that leading zeros are displayed in csv
        $row[$ref_file->force_quote[0]]= "=\"" .$row[$ref_file->force_quote[0]]. "\"";
        fputcsv($file, $row);
      }
      fclose( $file);
      unset($result);
    }

    $return['success'] = "Ref Files Generated";
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
    //var_dump ($result);

    $msg = [];
    $msg['body'] = "Test API REF FILE Generated";

    self::$message_body =
      [
        'file_generation_status' => "Generated ref files"
      ];

    $date = self::get_date('m-d-Y');
    $msg['subject'] = $conf['CHECKBOOK_ENV']."API REF FILES GENERATED: " . self::$successSubject . " ($date)";
    $msg['body'] = self::$message_body;
    $message = array_merge($message, $msg);

    //Send Status to all recipients when ETL Statistics are not empty
    $message['to'] = $conf['checkbook_dev_group_email'];
    return $message;
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
      //error_log("API REF CRON skips. Reason: \$conf['checkbook_dev_group_email'] not defined");
      return false;
    }

    if (empty($conf['CHECKBOOK_ENV']) || !in_array($conf['CHECKBOOK_ENV'], ['DEV2'])) {
      // we run this cron only on DEV2 and PHPUNIT
      return false;
    }

    $this_month = self::get_date('M');
    $current_hour = self::get_date('w');
    LogHelper::log_info($this_month);
    LogHelper::log_info($current_hour);

    $this_month = self::get_date('Y-m');
    $current_hour = (int)self::get_date('H');

    if (variable_get(self::CRON_LAST_RUN_DRUPAL_VAR) == $this_month) {
      //error_log("API CRON skips. Reason: already ran this month :: $this_month :: ".variable_get($variable_name));
      return false;
    }

    //if ($current_hour < 9 || $current_hour > 10) {
      //error_log("API CRON skips. Reason: will run between 9 AM and 11 AM EST :: current hour: $current_hour");
      //return false;
    //}
    variable_set(self::CRON_LAST_RUN_DRUPAL_VAR, $this_month);
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
