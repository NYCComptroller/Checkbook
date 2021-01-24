<?php

/**
 * Class CheckbookEtlStatistics
 */
class CheckbookEtlStatistics
{
  /**
 * @var string
 */
  public static $message_body = '';

  /**
   * @var string
   */
  public static $successSubject = 'Success';

  /**
   * @var string
   */
  public static $prodStatus = 'Success';

  /**
   * @var string
   */
  public static $uatStatus = 'Success';

  /**
   * Last ETL must successfully finish within last 12 hours
   */
  const SUCCESS_IF_RUN_LESS_THAN_X_SECONDS_AGO = 60 * 60 * 12;

  const CRON_LAST_RUN_DRUPAL_VAR = 'checkbook_etl_status_last_run';

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
   * @param $environment
   * @return array
   */
  public function getEtlStatistics($environment){
    //Get ETL Statistics in the last 12 hours
    /*$sql = "SELECT *,
            CASE WHEN (process_abort_flag_yn = 'N' AND shard_refresh_flag_yn = 'Y' AND index_refresh_flag_yn = 'Y') 
                  THEN 'Success'
	               ELSE 'Fail'
            END AS status FROM jobs WHERE load_end_time >= (NOW() - INTERVAL '12 hours' ) AND host_environment = '{$environment}'";
    */

    $sql = "SELECT database_name, host_environment, 
                  ((CURRENT_DATE-1)::text||' 21:00:00.0')::TIMESTAMP 	AS last_run_date, 
                  CASE 
		                   WHEN last_success_date > ((CURRENT_DATE-2)::text||' 21:00:00.0')::TIMESTAMP
			                  THEN (CASE WHEN success_yn = 'N' THEN 'Fail' ELSE 'Success' END) 
			                  ELSE 'Fail'
			            END as last_run_success,
			            last_success_date,
                  last_successful_load_date,
                  shard_refresh_flag_yn,
                  index_refresh_flag_yn,
                  process_abort_flag_yn
            FROM latest_stats_vw WHERE host_environment = '{$environment}'";
    $results = _checkbook_project_execute_sql_by_data_source($sql, 'etl_statistics');log_error($results);
    $databases = array('checkbook' => 'Citywide', 'checkbook_ogent' => 'NYCEDC', 'checkbook_nycha' => 'NYCHA');
    $etlStatus = [];
    foreach($results as $result){
      $etlStatus[$result['database_name']] = array(
        'Database' => $databases[$result['database_name']],
        'Environment' => $result['host_environment'],
        'Last Run Date' => $result['last_run_date'],
        'Last Run Success?' => $result['last_run_success'],
        'Last Success Date' => $result['last_success_date'],
        'Last File Load Date' => $result['last_successful_load_date'],
        'Shards Refreshed?' => $result['shard_refresh_flag_yn'],
        'Solr Refreshed?' => $result['index_refresh_flag_yn'],
        'All Files Processed?' => $result['process_abort_flag_yn'],
      );

      if($environment == 'PROD'){
        self::$prodStatus = (self::$prodStatus == 'Fail') ? self::$prodStatus : $result['last_run_success'];
      }

      if($environment == 'UAT'){
        self::$uatStatus = (self::$uatStatus == 'Fail') ? self::$uatStatus : $result['last_run_success'];
      }
    }
    $data = [];
    foreach($databases as $key => $value){
      $data[$key] = $etlStatus[$key];
    }
    return $data;
  }

  /**
   * @param $message
   * @return string
   */
  public function gatherData(&$message)
  {
    if (!self::$message_body) {
      $prodStats = self::getEtlStatistics('PROD');
      $uatStats = self::getEtlStatistics('UAT');

      if(self::$prodStatus == 'Fail' && self::$uatStatus == 'Fail'){
        self::$successSubject = "PROD Fail";
      }else if(self::$uatStatus == 'Fail' && self::$prodStatus == 'Success'){
        self::$successSubject = "UAT Fail";
      }else if(self::$prodStatus == 'Fail' && self::$uatStatus == 'Success'){
        self::$successSubject = "PROD Fail";
      }

      self::$message_body =
        [
          'uat_stats' => $uatStats,
          'prod_stats' => $prodStats,
          'subject' => self::$successSubject,
        ];
    }

    $msg = [];
    $msg['body'] = self::$message_body;

    $date = self::get_date('m-d-Y');
    $msg['subject'] = "ETL Status: " . self::$successSubject . " ($date)";

    $message = array_merge($message, $msg);

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
        //error_log("ETL STATUS MAIL CRON skips. Reason: \$conf['checkbook_dev_group_email'] not defined");
        return false;
      }

      if (empty($conf['CHECKBOOK_ENV']) || !in_array($conf['CHECKBOOK_ENV'], ['DEV2'])) {
        // we run this cron only on DEV2 and PHPUNIT
        return false;
      }

      $today = self::get_date('Y-m-d');
      $current_hour = (int)self::get_date('H');

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

      if (isset($conf['checkbook_dev_group_email'])){
        $to_dev = $conf['checkbook_dev_group_email'];

        try{
          drupal_mail('checkbook_etl_notification', 'send-dev-status', $to_dev, null, ['dev_mode'=> true], $from);
        } catch(Exception $ex1){
          error_log($ex1->getMessage());
        }
      }

      if (isset($conf['checkbook_ETL_emails'])) {
        $to_client = $conf['checkbook_ETL_emails'];
        try{
          drupal_mail('checkbook_etl_notification', 'send-client-status', $to_client, null, ['dev_mode'=> false], $from);
        } catch(Exception $ex2){
          error_log($ex2->getMessage());
        }
      }
      return true;
    }

}
