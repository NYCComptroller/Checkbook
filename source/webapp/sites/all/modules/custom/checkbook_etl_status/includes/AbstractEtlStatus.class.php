<?php

/**
 * Class AbstractEtlStatus
 */
abstract class AbstractEtlStatus
{
  /**
   * @return mixed
   */
  abstract static function pushStatus();

  /**
   * @return mixed
   */
  abstract static function getStatus();

  /**
   * @param $data
   * @return bool
   * @throws \MongoDB\Exception\InvalidArgumentException
   * @throws \MongoDB\Exception\UnsupportedException
   */
  protected static function pushToMongo($data)
  {
    if (!class_exists('CheckbookMongo')) {
      LogHelper::log_warn('Could not init CheckbookMongo');
      return false;
    }

    if (!$db = CheckbookMongo::getDb()) {
      LogHelper::log_warn('Could not connect to Mongodb database');
      return false;
    }

    $date = date('Ymd', time());
    $data = array_merge([
      '_id' => $data['env'].'_'.$date,
      'date' => $date
    ], $data);

    $data['connections'] = self::get_connections_info();

    try {
      $etl_status_table = $db->selectCollection('etl-status');
      $etl_status_table->replaceOne(['_id' => $data['_id']], $data, ['upsert' => true]);
      return TRUE;
    } catch (\Exception $exception) {
      LogHelper::log_warn($exception->getMessage());
    }
    return FALSE;
  }

  /**
   * @return array
   */
  private static function get_connections_info()
  {
    global $conf, $databases;
    $connections_info = [];
    if (!empty($databases['default']['default']['host'])) {
      $connections_info['mysql'] = $databases['default']['default']['host']
        . '|' . $databases['default']['default']['database'];
    }
    if (!empty($databases['checkbook']['main']['host'])) {
      $connections_info['psql_main'] = $databases['checkbook']['main']['host']
        . '|' . $databases['checkbook']['main']['database'];
    }
    if (!empty($databases['checkbook']['etl']['host'])) {
      $connections_info['psql_etl'] = $databases['checkbook']['etl']['host']
        . '|' . $databases['checkbook']['etl']['database'];
    }
    if (!empty($databases['checkbook_oge']['main']['host'])) {
      $connections_info['psql_oge'] = $databases['checkbook_oge']['main']['host']
        . '|' . $databases['checkbook_oge']['main']['database'];
    }
    if (!empty($databases['checkbook_nycha']['main']['host'])) {
      $connections_info['psql_nycha'] = $databases['checkbook_nycha']['main']['host']
        . '|' . $databases['checkbook_nycha']['main']['database'];
    }
    if (!empty($conf['check_book']['solr']['url'])) {
      $solr_url = $conf['check_book']['solr']['url'];
      $connections_info['solr'] = substr($solr_url, 0, stripos($solr_url, '/solr/') + 6)
        . '|' . substr($solr_url, $pos = stripos($solr_url, '/solr/') + 6, strlen($solr_url) - $pos - 1);
    }
    return $connections_info;
  }

  /**
   * @param $env
   * @return array|bool|object|null
   * @throws \MongoDB\Exception\InvalidArgumentException
   * @throws \MongoDB\Exception\UnsupportedException
   */
  protected static function getStatusByEnv($env)
  {
    if (!class_exists('CheckbookMongo')) {
      LogHelper::log_warn('Could not init CheckbookMongo');
      return false;
    }

    if (!$db = CheckbookMongo::getDb()) {
      LogHelper::log_warn('Could not connect to Mongodb database');
      return false;
    }

    try {
      $etl_status_table = $db->selectCollection('etl-status');
      $filter = ['env'=>$env];
      return $etl_status_table->findOne($filter, ['sort' => ['date'=>-1]]);
    } catch (\Exception $exception) {
      LogHelper::log_warn($exception->getMessage());
    }
  }
}
