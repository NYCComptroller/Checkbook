<?php

/**
 * Class ProdEtlStatus
 */
class ProdEtlStatus extends AbstractEtlStatus
{
  /**
   * @var array
   */
  private static $dataSourceLastSuccess = [];
  /**
   * @var bool
   */
  private static $success = TRUE;
  /**
   * @var array
   */
  private static $errors = [];

  /**
   *
   */
  public static function pushStatus()
  {
    global $conf;

    try {
      $etl_status = file_get_contents($conf['etl-status-path'] . 'etl_status.txt');
      list(, $date) = explode(',', $etl_status);
      $etl_status = trim($date);
    } catch (\Exception $exception) {
      $etl_status = '';
      self::$success = FALSE;
      self::$errors[] = 'Error processing PROD etl_status.txt: ' . $exception->getMessage();
    }

    try {
      $invalid_records_csv_path = $conf['etl-status-path'] . 'invalid_records_details.csv';
      $invalid_records = array_map('str_getcsv', file($invalid_records_csv_path));
      $invalid_records_timestamp = filemtime($invalid_records_csv_path);
    } catch (\Exception $exception) {
      $invalid_records_timestamp = 0;
      $invalid_records = [];
      self::$success = FALSE;
      self::$errors[] = 'Error processing PROD invalid_records_details.csv: ' . $exception->getMessage();
    }

    try {
      $audit_status_csv_path = $conf['etl-status-path'] . 'audit_status.txt';
      $audit_status = array_map('trim', file($audit_status_csv_path));
      $audit_status_timestamp = filemtime($audit_status_csv_path);
    } catch (\Exception $exception) {
      $audit_status_timestamp = 0;
      $audit_status = [];
      self::$success = FALSE;
      self::$errors[] = 'Error processing PROD audit_status.csv: ' . $exception->getMessage();
    }

    try {
      $match_status_timestamp = filemtime($conf['etl-status-path'] . 'file_data_statistics.csv');
      if (defined('PHPUNIT_RUNNING') || (60 * 60 * 12 > (time() - $match_status_timestamp))) {
        $match_status = self::getMatchStatus();
      }
    } catch (\Exception $exception) {
      $match_status = FALSE;
      self::$success = FALSE;
      self::$errors[] = 'Error processing PROD file_data_statistics.csv: ' . $exception->getMessage();
    }

    $todb = [
      'env' => 'PROD',
      'source' => 'PROD',
      'success' => self::$success,
      'data' => $etl_status,
      'errors' => self::$errors,
      'invalid_records' => $invalid_records,
      'invalid_records_timestamp' => $invalid_records_timestamp,
      'audit_status' => $audit_status,
      'audit_status_timestamp' => $audit_status_timestamp,
      'match_status' => $match_status,
      'match_status_timestamp' => $match_status_timestamp,
    ];
    return self::pushToMongo($todb);
  }

  /**
   * @return array|bool
   */
  private static function getMatchStatus()
  {
    global $conf;

    $match_status = [];
    $match_status_csv_path = $conf['etl-status-path'] . 'file_data_statistics.csv';
    $data_source_name_file_path = $conf['etl-status-path'] . 'data_source_name.txt';
    $data_source_last_success_file_path = $conf['etl-status-path'] . 'data_source_last_success.txt';
    $data_source_last_success_md5 = 0;

    if (is_file($match_status_csv_path) && is_file($data_source_name_file_path)) {
//      reading last success dates for each data source
      if (is_file($data_source_last_success_file_path)) {
        $data = file_get_contents($data_source_last_success_file_path);
        $data_source_last_success = unserialize($data);
        $data_source_last_success_md5 = md5($data);
        unset($data);
      } else {
        $data_source_last_success = [];
      }

      if (self::$dataSourceLastSuccess) {
//                    phpunit
        $data_source_last_success = unserialize(self::$dataSourceLastSuccess);
      }

//                reading full list of data source names
      $data_source_names = array_map('trim', file($data_source_name_file_path));
      if ('data_source_name' == $data_source_names[0]) {
//                    removing first (header) line
        array_shift($data_source_names);
      }

//                filling $match_status with zeros
      foreach ($data_source_names as $source) {
        $match_status[$source] = false;
      }

//                reading today etl status result - list of successfully ran data sources
      $match_status_csv = array_map('trim', file($match_status_csv_path));
      foreach ($match_status_csv as $line) {
        list($source,) = explode(',', $line);
        if (in_array($source, ['Data Source Name', 'data_source_name'])) {
//                        skipping header
          continue;
        }

//                    removing successfully ran data source names from output
        if (in_array($source, $data_source_names)) {
          unset($match_status[$source]);
          $data_source_last_success[$source] = date('Y-m-d', time());
        } else {
          $match_status[$source] = 'unknown data source name';
        }
      }

//                checking last success run date
      foreach ($match_status as $source => $value) {
        if ($value) {
//                        skipping preset values like 'unknown data source name'
          continue;
        }
//                   fail
        if (empty($data_source_last_success[$source])) {
          $match_status[$source] = 'unknown';
        } else {
          $interval = date_diff(
            date_create($data_source_last_success[$source]),
            date_create(date('Y-m-d', time())));
          $match_status[$source] = $interval->format('%a days ago');
        }
      }

      $data = serialize($data_source_last_success);
      if ($data_source_last_success && (md5($data) !== $data_source_last_success_md5)) {
        if (!defined('PHPUNIT_RUNNING')) {
          if (!file_put_contents($data_source_last_success_file_path, $data)) {
            \LogHelper::log_error('Could not write file: ' . $data_source_last_success_file_path);
          }
        } else {
          self::$dataSourceLastSuccess = $data;
        }
      }
    } else {
      return false;
    }

    return $match_status;
  }

  /**
   * @return array|bool|mixed|object|null
   * @throws \MongoDB\Exception\InvalidArgumentException
   * @throws \MongoDB\Exception\UnsupportedException
   */
  public static function getStatus()
  {
    return self::getStatusByEnv('PROD');
  }
}
