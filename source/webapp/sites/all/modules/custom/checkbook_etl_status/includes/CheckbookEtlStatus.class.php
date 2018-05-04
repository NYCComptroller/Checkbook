<?php

/**
 * Class CheckbookEtlStatus
 */
class CheckbookEtlStatus
{
  /**
   * @param $format
   * @return false|string
   */
  public function date($format)
  {
    return date($format);
  }

  /**
   * @return bool
   */
  public function run_cron()
  {
    global $conf;
    global $base_url;

    if (!isset($conf['etl_status_recipients'])) {
      //error_log("ETL STATUS MAIL CRON skips. Reason: \$conf['etl_status_recipients'] not defined");
      return false;
    }

    if ('uat-checkbook-nyc.reisys.com' !== parse_url($base_url, PHP_URL_HOST)) {
      //error_log("ETL STATUS MAIL CRON skips. Reason: domain is not uat-checkbook-nyc.reisys.com");
      return false;
    }

    date_default_timezone_set('America/New_York');
    $variable_name = 'checkbook_etl_status_last_run';

    $today = $this->date('Y-m-d');
    $current_hour = (int)$this->date('H');

    if (variable_get($variable_name) == $today) {
      //error_log("ETL STATUS MAIL CRON skips. Reason: already ran today :: $today :: ".variable_get($variable_name));
      return false;
    }

    if ($current_hour < 7) {
      //error_log("ETL STATUS MAIL CRON skips. Reason: will run after 8:00 AM :: current hour: $current_hour");
      return false;
    }

    variable_set($variable_name, $today);
    return $this->sendmail();
  }

  /**
   * @return bool
   */
  public function sendmail()
  {
    global $conf;

    $to = $conf['etl_status_recipients'];
    $response = drupal_mail('checkbook_etl_status', "send-status", $to,
      null, [], 'checkbook@reisys.com', TRUE);
    //error_log($response);
    return true;
  }

  /**
   * @return array
   */
  public function getUatStatus()
  {
    $local_api = new \checkbook_json_api\CheckBookJsonApi();
    return $local_api->etl_status();
  }

  /**
   * @param $message
   * @return bool
   */
  public function mail(&$message)
  {

    $uat_result = '<span style="color:red">FAIL</span>';
    $today_date = $this->date('Y-m-d');
    $uat_status = $this->getUatStatus();
    if ((true == $uat_status['success']) && ($today_date == $uat_status['data'])) {
      $uat_result = 'SUCCESS (ran today ' . $today_date . ')';
    } elseif (isset($uat_status['data'])) {
      $uat_result = 'FAIL (last successful run ' . $uat_status['data'] . ')';
    }
    $prod_status = 'UNKNOWN';

    $message['subject'] = 'ETL Status Report';
    $message['body'][] = <<<EOM
UAT  ETL STATUS:\t\t{$uat_result}
PROD ETL STATUS:\t{$prod_status}
EOM;
    return true;
  }
}
