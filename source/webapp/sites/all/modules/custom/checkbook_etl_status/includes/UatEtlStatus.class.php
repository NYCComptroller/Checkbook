<?php

/**
 * Class UatEtlStatus
 */
class UatEtlStatus extends AbstractEtlStatus
{
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
    $data = 0;

    try {
      $response = self::get_etl_status();
    } catch (Exception $e) {
      self::$success = FALSE;
      self::$errors[] = 'Error getting UAT ETL status: '.$e->getMessage();
    }

    if (!empty($response) && $response[0]['last_successful_run']) {
      $data = $response[0]['last_successful_run'];
    }

    $todb = [
      'env' => 'UAT',
      'source' => 'UAT',
      'success' => self::$success,
      'data' => $data,
      'errors' => self::$errors,
      'info' => 'Last successful UAT ETL run date'
    ];

    return self::pushToMongo($todb);
  }

  /**
   * @return array
   * @throws \Exception
   */
  private static function get_etl_status()
  {
    if (!defined('CHECKBOOK_NO_DB_CACHE')) {
      define('CHECKBOOK_NO_DB_CACHE', true);
    }
    $query = "SELECT DISTINCT
                  MAX(refresh_end_date :: TIMESTAMP) AS last_successful_run
                FROM etl.refresh_shards_status
                WHERE latest_flag = 1";
    $response = _checkbook_project_execute_sql($query, 'etl');
    return $response;
  }

  /**
   * @return array|bool|mixed|object|null
   * @throws \MongoDB\Exception\InvalidArgumentException
   * @throws \MongoDB\Exception\UnsupportedException
   */
  public static function getStatus()
  {
    return self::getStatusByEnv('UAT');
  }
}
