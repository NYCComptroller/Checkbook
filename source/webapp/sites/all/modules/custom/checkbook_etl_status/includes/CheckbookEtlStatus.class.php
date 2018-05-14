<?php

/**
 * Class CheckbookEtlStatus
 */
class CheckbookEtlStatus
{
  /**
   * Last ETL must successfully finish within last 12 hours
   */
  const LAST_RUN_SUCCESS_PERIOD = 60 * 60 * 12;

  /**
   * @param $format
   * @return false|string
   */
  public function date($format)
  {
    return date($format);
  }

  /**
   * @return int
   */
  public function timeNow()
  {
    return time();
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
    drupal_mail('checkbook_etl_status', "send-status", $to,
      null, [], 'checkbook@reisys.com', TRUE);
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
   * @return bool|mixed
   */
  public function getProdStatus()
  {
    try {
      $prod_json_status = file_get_contents('https://www.checkbooknyc.com/json_api/etl_status');
      $prod_status = json_decode($prod_json_status, true);
      return $prod_status;
    } catch (Exception $e) {
      error_log($e->getMessage());
    }
    return false;
  }

  /**
   * @param $data
   * @return string
   */
  public function formatStatus($data)
  {
    $result = 'FAIL (unknown)';
    $now = $this->timeNow();
    if (!empty($data['success']) && true == $data['success']) {
      if (self::LAST_RUN_SUCCESS_PERIOD > ($now - strtotime($data['data']))) {
        $result = 'SUCCESS (ran within last 12 hours :: ' . $data['data'] . ')';
      } else {
        $result = 'FAIL (last successful run ' . $data['data'] . ')';
      }
    }
    return $result;
  }

  /**
   * @param $message
   * @return bool
   */
  public function mail(&$message)
  {
    $uat_result = $this->formatStatus($this->getUatStatus());
    $prod_status = $this->formatStatus($this->getProdStatus());

    $message['subject'] = 'ETL Status Report';
    $message['body'][] = <<<EOM
UAT  ETL STATUS:\t{$uat_result}
PROD ETL STATUS:\t{$prod_status}
EOM;
    return true;
  }
}
