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
   * Last ETL must successfully finish within last 12 hours
   */
  const SUCCESS_IF_RUN_LESS_THAN_X_SECONDS_AGO = 60 * 60 * 12;


  /**
   * @param $format
   * @return false|string
   */
  public function get_date($format)
  {
    return date($format, self::timeNow());
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


  public function getEtlStatistics(){
    $sql = "SELECT * FROM jobs WHERE DATE(load_end_time) = CURRENT_DATE - 1";
    $results = _checkbook_project_execute_sql_by_data_source($sql, 'etl_statistics');
    $etlStatus = [];
    foreach($results as $result){
      $etlStatus['database_name']['host_environment'] = array(
        'Database' => $result['database_name'],
        'Environment' => $result['host_environment'],
        'Last Run Date' => $result['load_end_time'],
        'Last Run Success?' => '',
        'Last Success Date' => $result['load_end_time'],
        'Last File Load Date' => $result['last_successful_load_date'],
        'Shards Refreshed?' => $result['shard_refresh_flag_yn'],
        'Solr Refreshed?' => $result['index_refresh_flag_yn'],);
    }
    return $etlStatus;
  }

  /**
   * @param $message
   * @return bool
   */
  public function gatherData(&$message)
  {
    global $conf;
    if (!self::$message_body) {
      $data = self::getEtlStatistics();
      $formattedStatus = self::formatStatus($data);

      self::$message_body =
        [
          //'uat_status' => $uat_status,
          //'prod_status' => $prod_status,
          'status' => $formattedStatus,
          'subject' => self::$successSubject,
        ];
    }

    $msg = [];
    $msg['body'] = self::$message_body;

    $date = self::get_date('Y-m-d');

    $msg['subject'] = "[{$conf['CHECKBOOK_ENV']}] ETL Status: " . self::$successSubject . " ($date)";

    $message = array_merge($message, $msg);

    return $message;
  }

  /**
   * @param $data
   * @return string
   */
  public function formatStatus($data)
  { var_dump($data);
    global $conf;
    $now = self::timeNow();

    if (!empty($data['success']) && true == $data['success']) {
      $data['hint'] = self::niceDisplayDateDiff($data['data']);

      if (($now - strtotime($data['data'])) > self::SUCCESS_IF_RUN_LESS_THAN_X_SECONDS_AGO) {
        $data['hint'] = 'Last success: ' . $data['hint'];
        $data['success'] = false;
        self::$successSubject = 'Fail';
      }
    } else {
      self::$successSubject = 'Fail';
      $data['success'] = false;
      $data['hint'] = 'Could not get data from server';
    }
    return $data;
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
    if (is_numeric($date)) {
      $date = date('c', $date);
    }
    $date1 = date_create($date);
    $interval = date_diff($date1, date_create(self::get_date("Y-m-d H:i:s")));
    return $interval->format('%a day(s) %h hour(s) ago');
  }

}
