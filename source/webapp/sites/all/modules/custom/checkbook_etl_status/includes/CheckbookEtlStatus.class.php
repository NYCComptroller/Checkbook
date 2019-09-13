<?php

/**
 * Class CheckbookEtlStatus
 */
class CheckbookEtlStatus
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
   *
   */
  const CRON_LAST_RUN_DRUPAL_VAR = 'checkbook_etl_status_last_run';

  /**
   * List of conf db connections
   */
  const CONNECTIONS_KEYS = [
    'mysql',
    'psql_main',
    'psql_etl',
    'psql_oge',
    'psql_nycha',
    'solr',
  ];

  /**
   * @param $format
   * @return false|string
   */
  public function get_date($format)
  {
    return date($format, $this->timeNow());
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

    date_default_timezone_set('America/New_York');

//        always run cron for developer
    if (defined('ETL_STATUS_OUT') && ETL_STATUS_OUT) {
      return $this->sendmail();
    }
    if (!isset($conf['checkbook_dev_group_email'])) {
      //error_log("ETL STATUS MAIL CRON skips. Reason: \$conf['checkbook_dev_group_email'] not defined");
      return false;
    }

    if (empty($conf['CHECKBOOK_ENV']) || !in_array($conf['CHECKBOOK_ENV'], ['UAT', 'PHPUNIT'])) {
      // we run this cron only on UAT and PHPUNIT
      return false;
    }

    $today = $this->get_date('Y-m-d');
    $current_hour = (int)$this->get_date('H');

    if (variable_get(self::CRON_LAST_RUN_DRUPAL_VAR) == $today) {
      //error_log("ETL STATUS MAIL CRON skips. Reason: already ran today :: $today :: ".variable_get($variable_name));
      return false;
    }

    if ($current_hour < 9 || $current_hour > 10) {
      //error_log("ETL STATUS MAIL CRON skips. Reason: will run between 9 AM and 11 AM EST :: current hour: $current_hour");
      return false;
    }

    variable_set(self::CRON_LAST_RUN_DRUPAL_VAR, $today);
    return $this->sendmail();
  }

  /**
   * @return bool
   */
  public function sendmail()
  {
    global $conf;

    $from = $conf['email_from'];

    if (isset($conf['checkbook_dev_group_email'])) {
      $to_dev = $conf['checkbook_dev_group_email'];

      try {
        drupal_mail('checkbook_etl_status', 'send-dev-status', $to_dev, null, ['dev_mode' => true], $from);
      } catch (Exception $ex1) {
        error_log($ex1->getMessage());
      }
    }

    if (isset($conf['checkbook_client_emails'])) {
      $to_client = $conf['checkbook_client_emails'];
      try {
        drupal_mail('checkbook_etl_status', 'send-client-status', $to_client, null, ['dev_mode' => false], $from);
      } catch (Exception $ex2) {
        error_log($ex2->getMessage());
      }
    }

    return true;
  }

  /**
   * @return array
   */
  public function getUatStatus()
  {
    global $conf;
    if (empty($conf['CHECKBOOK_ENV']) || !in_array($conf['CHECKBOOK_ENV'], ['UAT', 'PHPUNIT'])) {
      return false;
    }
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
    if (is_numeric($date)) {
      $date = date('c', $date);
    }
    $date1 = date_create($date);
    $interval = date_diff($date1, date_create($this->get_date("Y-m-d H:i:s")));
    return $interval->format('%a day(s) %h hour(s) ago');
  }

  /**
   * @param $data
   * @return string
   */
  public function formatStatus($data)
  {
    global $conf;
    $now = $this->timeNow();

    if (!empty($data['success']) && true == $data['success']) {
      $data['hint'] = $this->niceDisplayDateDiff($data['data']);

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

    if (!empty($data['invalid_records_timestamp'])) {
      if (!defined('CHECKBOOK_DEV') && (($now - $data['invalid_records_timestamp']) > self::SUCCESS_IF_RUN_LESS_THAN_X_SECONDS_AGO)) {
        unset($data['invalid_records_timestamp']);
        if (!empty($data['invalid_records'])) {
          unset($data['invalid_records']);
        }
      } else {
        if (isset($conf['etl-status-skip-invalid-records-reasons'])) {
          $filtered_invalid_records = [];
          $skipped = 0;
          $limitReached = false;
          foreach ($data['invalid_records'] as $line) {
            if (!in_array($line[3], $conf['etl-status-skip-invalid-records-reasons'])) {
              $filtered_invalid_records[] = $line;
            } elseif (++$skipped > $conf['etl-status-skip-invalid-records-limit']) {
              if ('Success' == self::$successSubject) {
                self::$successSubject = 'Needs attention - too many invalid reasons skipped (' .
                  $conf['etl-status-skip-invalid-records-limit'] . '+)';
              }
              $limitReached = true;
              break;
            }
          }
          if (!$limitReached) {
            if (1 < sizeof($filtered_invalid_records)) {
              if ('Success' == self::$successSubject) {
                self::$successSubject = 'Needs attention';
              }
              $data['invalid_records'] = $filtered_invalid_records;
            } else {
              unset($data['invalid_records']);
              unset($data['invalid_records_timestamp']);
            }
          }
        } else {
          if ('Success' == self::$successSubject) {
            self::$successSubject = 'Needs attention';
          }
        }
      }
    }

    if (!empty($data['audit_status_timestamp'])) {
      if (!defined('CHECKBOOK_DEV') && (($now - $data['audit_status_timestamp']) > self::SUCCESS_IF_RUN_LESS_THAN_X_SECONDS_AGO)) {
        unset($data['audit_status_timestamp']);
        if (!empty($data['audit_status'])) {
          unset($data['audit_status']);
        }
      } else {
        $allGood = ['OK'];
        $data['audit_status_time_diff'] = $this->niceDisplayDateDiff($data['audit_status_timestamp']);
        if (($allGood !== $data['audit_status']) && ('Success' == self::$successSubject)) {
          self::$successSubject = 'Needs attention';
        }
      }
    }

    if (!empty($data['match_status_timestamp'])) {
      if (!defined('CHECKBOOK_DEV') && (($now - $data['match_status_timestamp']) > self::SUCCESS_IF_RUN_LESS_THAN_X_SECONDS_AGO)) {
        unset($data['match_status_timestamp']);
        if (!empty($data['match_status'])) {
          unset($data['match_status']);
        }
      }
    }

    return $data;
  }

  /**
   * @return array
   */
  public function getConnectionConfigs()
  {
    global $conf;

    $return = [];
    if (empty($conf['etl-status-footer']['line1'])) {
      return $return;
    }

    foreach ($conf['etl-status-footer']['line1'] as $env => $url) {
      try {
        $prod_json_status = $this->get_contents($url . 'json_api/etl_status');
        $json = json_decode($prod_json_status, true);
        if (!empty($json['connections'])) {
          $return[$env] = $json['connections'];
        }
      } catch (Exception $e) {
        error_log($e->getMessage());
      }
    }
    return $return;
  }

  /**
   * @return bool
   * @throws \MongoDB\Exception\InvalidArgumentException
   * @throws \MongoDB\Exception\UnsupportedException
   */
  private function updateContractRegexes()
  {
    if (!class_exists('CheckbookMongo') || !$db = CheckbookMongo::getDb()) {
      LogHelper::log_notice('Could not init CheckbookMongo');
      return false;
    }

    $query = "select actual_pattern
                from   etl.ref_file_name_pattern
              where  data_source_code IN ('C','M','SC','SF','SS','SV');";

    $regex_rules = _checkbook_project_execute_sql($query, 'etl');
    if (!$regex_rules) {
      return false;
    }
    $conf_collection = $db->selectCollection('configs');
    $conf_collection->replaceOne(['_id' => 'fisa_contract_regex'], ['rules' => $regex_rules]);
  }

  /**
   * @return array|bool|object|null
   * @throws \MongoDB\Exception\InvalidArgumentException
   * @throws \MongoDB\Exception\UnsupportedException
   */
  private function getFisaFileList()
  {
    global $conf;
    if (!class_exists('CheckbookMongo') || !$db = CheckbookMongo::getDb()) {
      LogHelper::log_notice('Could not init CheckbookMongo');
      return false;
    }

    if ('UAT' == $conf['CHECKBOOK_ENV'] || ($conf['update_fisa_mongo_regex'] ?? false)) {
      $this->updateContractRegexes();
    }

    $collection = $db->selectCollection('etlstatuslogs');

    $yesterday = date('Ymd', time() - 24 * 60 * 60);
    $files = $collection->findOne(['date' => $yesterday]);

    if (!$files) {
      return false;
    }

    $regex_rules = $db->selectCollection('configs')->findOne(['_id' => 'fisa_contract_regex']);
    if (!$regex_rules) {
      LogHelper::log_notice('Could not load regex rules from mongo');
      return false;
    }

    $lines = [];
    foreach ($files['lines'] as $line) {
      $pass = false;
      $row = [];
      list($row['lines'], $row['bytes'], $row['filename']) = [$line[0], $line[1], basename($line[2])];
      foreach($regex_rules['rules'] as $rule){
        $pass += preg_match('/'.$rule['actual_pattern'].'/', $row['filename']);
      }
      if ($pass) {
        $lines[$row['filename']] = $row;
      }

    }
    ksort($lines);
    $files['lines'] = $lines;

    return $files ?? false;
  }

  /**
   * @param $message
   * @return bool
   * @throws \MongoDB\Exception\InvalidArgumentException
   * @throws \MongoDB\Exception\UnsupportedException
   */
  public function gatherData(&$message)
  {
    global $conf;

    if (!self::$message_body) {
      $uat_status = $this->formatStatus($this->getUatStatus());
      $prod_status = $this->formatStatus($this->getProdStatus());
      $connections = $this->getConnectionConfigs();

      self::$message_body =
        [
          'uat_status' => $uat_status,
          'prod_status' => $prod_status,
          'subject' => self::$successSubject,
          'connections' => $connections,
          'fisa_files' => $this->getFisaFileList(),
          'connection_keys' => self::CONNECTIONS_KEYS,
        ];
    }

    $msg = [];
    $msg['body'] = self::$message_body;

    $date = $this->get_date('Y-m-d');

    $msg['subject'] = "[{$conf['CHECKBOOK_ENV']}] ETL Status: " . self::$successSubject . " ($date)";

    $message = array_merge($message, $msg);

    return true;
  }
}
