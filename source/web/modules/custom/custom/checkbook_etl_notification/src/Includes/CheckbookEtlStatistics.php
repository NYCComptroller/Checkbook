<?php

namespace Drupal\checkbook_etl_notification\Includes;

use Drupal\checkbook_log\LogHelper;
use Exception;

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
   * @var string
   */
  public static $recipients = 'All';

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
   * @return array/NULL
   */
  public function getEtlStatistics($environment){

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
                  process_abort_flag_yn,
                  process_error_count
            FROM latest_stats_vw WHERE host_environment = '{$environment}'";
    $results = _checkbook_project_execute_sql_by_data_source($sql, 'etl_statistics');
    if(is_countable($results) && count($results) > 0) {
      $databases = array('checkbook' => 'Citywide', 'checkbook_ogent' => 'NYCEDC', 'checkbook_nycha' => 'NYCHA');
      $etlStatus = [];
      foreach ($results as $result) {
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
          'Process Errors?' => $result['process_error_count']
        );

        if ($environment == 'PROD') {
          self::$prodStatus = (self::$prodStatus == 'Fail') ? self::$prodStatus : $result['last_run_success'];
        }

        if ($environment == 'UAT') {
          self::$uatStatus = (self::$uatStatus == 'Fail') ? self::$uatStatus : $result['last_run_success'];
        }
      }
      $data = [];
      foreach ($databases as $key => $value) {
        $data[$key] = $etlStatus[$key];
      }
      return $data;
    }else{
      if ($environment == 'PROD') {
        self::$prodStatus = "There are no Statistics found.";
      }

      if ($environment == 'UAT') {
        self::$uatStatus = "There are no Statistics found.";
      }
      return NULL;
    }
  }

  /**
   * @param $message
   * @return array
   */
  public function gatherData(&$message)
  {
    if (!self::$message_body) {
      $prodStats = self::getEtlStatistics('PROD');
      $uatStats = self::getEtlStatistics('UAT');

      if(isset($prodStats) && isset($uatStats)) {
        if (self::$prodStatus == 'Fail' && self::$uatStatus == 'Fail') {
          self::$successSubject = "PROD Fail";
        } else if (self::$uatStatus == 'Fail' && self::$prodStatus == 'Success') {
          self::$successSubject = "UAT Fail";
        } else if (self::$prodStatus == 'Fail' && self::$uatStatus == 'Success') {
          self::$successSubject = "PROD Fail";
        }
      }else{
        if(!isset($prodStats) && !isset($uatStats)){
          self::$successSubject = "Stats not Found";
        }else if(!isset($prodStats) && isset($uatStats)){
          self::$successSubject = "PROD Stats not Found";
        }else if(isset($prodStats) && !isset($uatStats)){
          self::$successSubject = "UAT Stats not Found";
        }
        self::$recipients = "Dev";
      }

      self::$message_body =
        [
          'uat_stats' => $uatStats,
          'prod_stats' => $prodStats,
          'subject' => self::$successSubject,
          'uat_status' => self::$uatStatus,
          'prod_status' => self::$prodStatus,
          'recipients' => self::$recipients
        ];
    }

    $msg = [];
    $msg['body'] = self::$message_body;

    $date = self::get_date('m-d-Y');
    $msg['subject'] = "ETL Status: " . self::$successSubject . " ($date)";

    $message = array_merge($message, $msg);

    //Send Status to all recipients when ETL Statistics are not empty

    $checkbook_ETL_emails = \Drupal::config('check_book')->get('checkbook_ETL_emails') ?? null;
    if(self::$recipients == 'All' && isset($checkbook_ETL_emails)){
      $message['to'] = $checkbook_ETL_emails;
    }

    return $message;
  }

    /**
     * @return bool
     */
    public function run_cron()
    {
      //global $conf;
      date_default_timezone_set('America/New_York');

      //always run cron for developer
      if (defined('CHECKBOOK_DEV')) {
        return self::sendmail();
      }

      $checkbook_dev_group_email = \Drupal::config('check_book')->get('checkbook_dev_group_email') ?? null;
      if (!isset($checkbook_dev_group_email)) {
        LogHelper::log_notice("ETL STATUS MAIL CRON skips. Reason: \$config['check_book']['checkbook_dev_group_email'] not defined");
        return false;
      }

      $CHECKBOOK_ENV = \Drupal::config('check_book')->get('CHECKBOOK_ENV') ?? null;
      if (empty($CHECKBOOK_ENV) || !in_array($CHECKBOOK_ENV, ['DEV'])) {
        // we run this cron only on DEV2 and PHPUNIT
        LogHelper::log_warn("CHECKBOOK_ENV empty or not DEV");
        return false;
      }

      $today = self::get_date('Y-m-d');
      $current_hour = (int)self::get_date('H');

      $config = \Drupal::service('config.factory')->getEditable('variable_get_set.api');

      if ($config->get(self::CRON_LAST_RUN_DRUPAL_VAR) == $today) {
        LogHelper::log_notice("ETL STATUS MAIL CRON skips. Reason: already ran today :: $today :: " . $config->get(self::CRON_LAST_RUN_DRUPAL_VAR));
        return false;
      }

      if ($current_hour < 9 || $current_hour > 10) {
        LogHelper::log_notice("ETL STATUS MAIL CRON skips. Reason: will run between 9 AM and 11 AM EST :: current hour: $current_hour");
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
          \Drupal::service('plugin.manager.mail')->mail('checkbook_etl_notification', 'send-status', $to, null, array('dev_mode'=> false));
        } catch(Exception $ex2){
          LogHelper::log_error($ex2->getMessage());
        }
      }
      return true;
    }

}
