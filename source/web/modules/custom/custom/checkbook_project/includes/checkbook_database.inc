<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_log\LogHelper;
use Drupal\Core\Database\Database;
use Drupal\data_controller\Datasource\Formatter\ResultFormatter;
use Drupal\data_controller_sql\Datasource\Handler\AbstractSQLDataSourceHandler;
use Drupal\data_controller_sql\Logger\Listener\StatementLogMessageKeeper;

/**
 * query the dataset and get data ...
 * @param string $dataset
 * @param array|string $columns
 * @param null $parameters
 * @return mixed
 */
function _checkbook_project_querydataset($dataset, $columns, $parameters = null){
  try {
    if (function_exists('_checkbook_project_execute_dataset_test')) {
      // the following function is only available when phpunit runs
      return _checkbook_project_execute_dataset_test();
    }
    $dataController = data_controller_get_instance();
    $dataset = _update_dataset_datasource($dataset);
    if(isset($parameters) && is_array($parameters)){
      foreach ($parameters as $param => $value){
        $parameters[$param] = pg_escape_string ($value);
      }
    }
    $data = $dataController->queryDataset($dataset, $columns, $parameters, NULL, 0, NULL, NULL);
  } catch (Exception $exception) {
    LogHelper::log_error("Error getting data from the controller: \n" . $exception->getMessage()/*, $e*/);
  }
  return $data;
}

/**
 * Function to get prepared DB query
 *
 * @param $isList Indicator for list query or cube query
 * @param $datasetName Dataset Name
 * @param null $columns Required Colummns
 * @param null $parameters Parameters
 * @param null $orderBy Sort Column
 * @param int $startWith Offset
 * @param null $limit Limit
 * @param null|ResultFormatter $resultFormatter ResultFormatter
 * @return null Query
 * @throws Exception
 */
function get_db_query($isList, $datasetName, $columns = NULL, $parameters = NULL, $orderBy = NULL, $startWith = 0, $limit = NULL, ResultFormatter $resultFormatter = NULL){
  $sqlQuery = NULL;

  //Since we are disabling execution mode below, need to make sure meta model is loaded.
  data_controller_get_metamodel();

  $executionMode = AbstractSQLDataSourceHandler::$STATEMENT_EXECUTION_MODE;
  try {
    AbstractSQLDataSourceHandler::$STATEMENT_EXECUTION_MODE = AbstractSQLDataSourceHandler::STATEMENT_EXECUTION_MODE__IGNORE;
    StatementLogMessageKeeper::reset();

    get_db_results($isList, $datasetName, $columns, $parameters, $orderBy, $startWith, $limit, $resultFormatter);

    $sqlQuery = $isList ? StatementLogMessageKeeper::$statements['dataset.query'][0] : StatementLogMessageKeeper::$statements['cube.query'][0];
  } catch (Exception $e) {
    AbstractSQLDataSourceHandler::$STATEMENT_EXECUTION_MODE = $executionMode;
    throw $e;
  }
  AbstractSQLDataSourceHandler::$STATEMENT_EXECUTION_MODE = $executionMode;

  return $sqlQuery;
}

/**
 * Function to get DB results
 *
 * @param $isList Indicator for list query or cube query
 * @param $datasetName Dataset Name
 * @param null $columns Required Colummns
 * @param null $parameters Parameters
 * @param null $orderBy Sort Column
 * @param int $startWith Offset
 * @param null $limit Limit
 * @param null|ResultFormatter $resultFormatter ResultFormatter
 * @return DB results
 */
function get_db_results($isList, $datasetName, $columns = NULL, $parameters = NULL, $orderBy = NULL, $startWith = 0, $limit = NULL, ResultFormatter $resultFormatter = NULL){
  $controller = data_controller_get_instance();
  $records = NULL;
  if ($isList) {
    $records = $controller->queryDataset($datasetName, $columns, $parameters, $orderBy, $startWith, $limit, $resultFormatter);
  } else {
    $records = $controller->queryCube($datasetName, $columns, $parameters, $orderBy, $startWith, $limit, $resultFormatter);
  }
  return $records;
}

/**
 * @param $sql
 * @param string $db_name
 * @param string $data_source
 * @return array|bool|mixed|void
 */
function _checkbook_project_execute_sql($sql, string $db_name = "main", string $data_source = Datasource::CITYWIDE) {
  if(isset($sql)) {
    $cacheKey = '_checkbook_query_' . $data_source . '_' . md5($sql . $db_name . $data_source);
    if ($result = _checkbook_dmemcache_get($cacheKey)) {
      LogHelper::log_info($sql);
      LogHelper::log_info(' PROCESSED CACHED (' . sizeof($result) . ' records)');
      return $result;
    }

    if (!(bool)Database::getConnectionInfo($data_source)) {
      LogHelper::log_error("No DB configuration found for: \$databases['$data_source']['$db_name']");
    } else {
      $connection = Database::getConnection($db_name, $data_source);
      $connectionOptions = $connection->getConnectionOptions();
      LogHelper::log_notice("Querying database \$databases['$data_source']['$db_name'] (" . $connectionOptions['host'] . ')');
      LogHelper::log_notice($sql);

      try {
        $results = $connection->query($sql, [], ['allow_delimiter_in_query' => TRUE, 'allow_square_brackets' => TRUE])->fetchAll();
      } catch (Exception $e) {
        LogHelper::log_error("Error executing DB query for generating: " . $sql . ". Exception is: " . $e);
        return;
      }

      $output = array();
      if ($results) {
        foreach ($results as $row) {
          $output[] = get_object_vars($row);
        }
      }

      LogHelper::log_info("Processed " . sizeof($output) . " records (_checkbook_project_execute_sql)");
      if (13 > sizeof($output) || (13 > sizeof($output[0]) && 1000 > sizeof($output))) {
        LogHelper::log_info("Calling _checkbook_dmemcache_set in _checkbook_project_execute_sql with cachekey " . $cacheKey);
        _checkbook_dmemcache_set($cacheKey, $output);
      }
      return $output;
    }
    return null;
  }
}

/**
 * @param $sql
 * @param string $data_source
 * @return array|bool|mixed
 */
function _checkbook_project_execute_sql_by_data_source($sql, string $data_source = Datasource::CITYWIDE){
  return _checkbook_project_execute_sql($sql, 'main', $data_source);
}

/**
 * Function to check if records exists
 * @param $nid
 * @return bool
 */
function _checkbook_project_recordsExists($nid){
  static $recordExists = array();

  if (isset($recordExists[$nid])) {
    return $recordExists[$nid];
  }

  LogHelper::log_info('Loading node # ' . $nid);

  $startTime = microtime(TRUE);
  $node = _widget_node_load_file($nid);

  widget_config($node);

  $node->widgetConfig->getTotalDataCount = true;
  $node->widgetConfig->getData = false;

  //prepare anything we'll need before loading
  widget_prepare($node);
  //invoke widget specific prepare
  widget_invoke($node, 'widget_prepare');
  widget_data($node);

  $recordCount = $node->totalDataCount;
  $recordExist = ($recordCount > 0);
  $recordExists[$nid] = $recordExist;

  LogHelper::log_debug("Record Count time for node $nid:" . (1000 * (microtime(TRUE) - $startTime)) . " msec. Total Records: $recordCount");
  return $recordExist;
}

/**
 * Function to return record count
 * @param $node (processed node config)
 * @return int
 */
function _checkbook_project_recordCount($node){
  static $recordCounts = array();

  if (isset($recordCounts[$node->nid])) {
    return $recordCounts[$node->nid];
  }

  $startTime = microtime(TRUE);

  $node->widgetConfig->getTotalDataCount = true;
  $node->widgetConfig->getData = false;

  load_widget_data($node, NULL);

  $recordCount = $node->totalDataCount;
  $recordCounts[$node->nid] = $recordCount;

  LogHelper::log_info("Record Count time for node $node->nid:" . (1000 * (microtime(TRUE) - $startTime)) . " msec. Total Records: $recordCount");
  return $recordCount;
}


/**
 * @param $key
 * @param string $bin
 * @param null $mc
 * @return bool|mixed
 */
function _checkbook_dmemcache_get($key, $bin = 'cache', $mc = NULL){
//    MOCK CACHE FOR PHPUNIT
  if (defined('PHPUNIT_RUNNING') && PHPUNIT_RUNNING) {
    global $phpunit_cache;
    return !empty($phpunit_cache[$key]) ? $phpunit_cache[$key] : false;
  }

// manually disabled, ex. during etl status
  if (defined('CHECKBOOK_NO_DB_CACHE') && CHECKBOOK_NO_DB_CACHE) {
    return false;
  }

//    NO CACHE FOR ADMINS
  if (\Drupal::currentUser()->isAuthenticated()) {
    return false;
  }

  if (\Drupal::hasService('memcache.factory')) {
    $memcache = \Drupal::service('memcache.factory')->get();
    if ($memcache) {
      return $memcache->get($key);
    }
  }

  LogHelper::log_warn('Memcache module is disabled.');
  return FALSE;
}

/**
 * @param $key
 * @param $value
 * @param int $exp - set to 15 hrs
 * @param string $bin
 * @param null $mc
 * @return bool
 */
function _checkbook_dmemcache_set($key, $value, $exp = 54000, $bin = 'cache', $mc = NULL){
  if (defined('PHPUNIT_RUNNING') && PHPUNIT_RUNNING) {
    global $phpunit_cache;
    $phpunit_cache[$key] = $value;
    return true;
  }
  if (\Drupal::currentUser()->isAuthenticated() || !$value) {
    return TRUE;
  }

  LogHelper::log_info($key . ' in memcache! ' . md5(json_encode($value)));

  if (\Drupal::hasService('memcache.factory')) {
    $memcache = \Drupal::service('memcache.factory')->get();
    if ($memcache) {
      return $memcache->set($key, $value, $exp);
    }
  }

  LogHelper::log_warn('Memcache module is disabled.');
  return FALSE;
}

/**
 * @param string $database
 * @return mixed|string
 */
function _checkbook_psql_command($database = 'checkbook'){
  //global $conf, $databases;

  // extra check empty db
  if (!$database) {
    $database = 'checkbook';
  }

  $checkbook_db_info = Database::getConnectionInfo($database);

  if (!isset($checkbook_db_info)) {
   LogHelper::log_warn('$databases[' . $database . '] not found');
    return '';
  } else {
    LogHelper::log_notice('Using $databases[' . $database . '] database');
  }

  $command = \Drupal::config('check_book')->get('data_feeds')['command'] ." ".$checkbook_db_info['main']['database']." ";

  return $command;
}

/**
 * @param $datasetName
 * @param bool $keepOriginalDatasource
 * @return mixed
 */
function _update_dataset_datasource($datasetName,$keepOriginalDatasource = false){
  if($keepOriginalDatasource == true){
    return $datasetName;
  }
  $refURL = RequestUtilities::getRefUrl();
  $datasource = isset($refURL) ? RequestUtilities::get('datasource', ['q' => $refURL]) : RequestUtilities::get('datasource');

  if('checkbook_oge' === $datasource){
    $database = str_replace("checkbook:", "checkbook_oge:",$datasetName);
  }else if('checkbook_nycha' === $datasource){
    $database = str_replace("checkbook:", "checkbook_nycha:",$datasetName);
  }else{
    $database = $datasetName;
  }
  return $database;
}

/**
 * @param $isList Indicator for list query or cube query
 * @param $datasetName Dataset Name
 * @param $columns Required Colummns
 * @param null $parameters Parameters
 * @param null|ResultFormatter $resultFormatter ResultFormatter
 * @return integer Count
 */
function get_db_result_count($isList, $datasetName, $columns, $parameters = NULL, ResultFormatter $resultFormatter = NULL){
  $controller = data_controller_get_instance();
  $recordCount = NULL;
  if ($isList) {
    $recordCount = $controller->countDatasetRecords($datasetName, $parameters, $resultFormatter);
  } else {
    $recordCount = $controller->countCubeRecords($datasetName, $columns, $parameters, $resultFormatter);
  }

  return $recordCount;
}


