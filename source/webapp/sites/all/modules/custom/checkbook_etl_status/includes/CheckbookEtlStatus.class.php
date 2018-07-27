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
   * @var string
   */
  public $success = 'Success';

  /**
   * @param $format
   * @return false|string
   */
  public function date($format)
  {
    return date($format);
  }

  /**
   * @param $url
   * @return bool|string
   */
  public function get_contents($url)
  {
    return file_get_contents($url);
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

    date_default_timezone_set('America/New_York');

    if (defined('CHECKBOOK_DEV_VSL4')) {
      return $this->sendmail();
    }
    if (!isset($conf['etl_status_recipients'])) {
      //error_log("ETL STATUS MAIL CRON skips. Reason: \$conf['etl_status_recipients'] not defined");
      return false;
    }

    if ('uat-checkbook-nyc.reisys.com' !== parse_url($base_url, PHP_URL_HOST)) {
      //error_log("ETL STATUS MAIL CRON skips. Reason: domain is not uat-checkbook-nyc.reisys.com");
      return false;
    }

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
    $result = $local_api->etl_status();
    $result['source'] = 'UAT';
    return $result;
  }

  /**
   * @return bool|mixed
   */
  public function getProdStatus()
  {
    try {
      $prod_json_status = $this->get_contents('https://www.checkbooknyc.com/json_api/etl_status');
      $prod_status = json_decode($prod_json_status, true);
      $prod_status['source'] = 'PROD';
      return $prod_status;
    } catch (Exception $e) {
      error_log($e->getMessage());
    }
    return false;
  }

  /**
   * @param $date
   * @return false|string
   */
  public function niceDisplayDateDiff($date)
  {
    if (!$date) {
      return 'never';
    }
    $date1 = date_create($date);
    $interval = date_diff($date1, date_create());
    return $interval->format('%a day(s) %h hour(s) ago');
  }

  /**
   * @param $data
   * @return string
   */
  public function formatStatus($data)
  {
    $now = $this->timeNow();

    if (!empty($data['success']) && true == $data['success']) {
      $data['hint'] = $this->niceDisplayDateDiff($data['data']);

      if (self::LAST_RUN_SUCCESS_PERIOD < ($now - strtotime($data['data']))) {
        $data['hint'] = 'Last success: '.$data['hint'];
        $data['success'] = false;
      }
    } else {
      $data['success'] = false;
      $data['hint'] = 'Could not get data from server';
    }
    return $data;
  }

  /**
   * @param $message
   * @return bool
   */
  public function mail(&$message)
  {
    $uat_status = $this->getUatStatus();
    $prod_status = $this->getProdStatus();

    $message['body'] =
      [
        'uat_status' => $this->formatStatus($uat_status),
        'prod_status' => $this->formatStatus($prod_status),
      ];

    $date = $this->date('Y-m-d');

    $message['subject'] = 'ETL Status: '.$this->success." ($date)";

    return true;
  }
}
