<?php

namespace Drupal\checkbook_infrastructure_layer\DataAccess;

use Drupal\checkbook_infrastructure_layer\DataAccess\Factory\SqlModelFactory;
use Drupal\checkbook_infrastructure_layer\DataAccess\Models\SqlStatementModel;
use Drupal\checkbook_log\LogHelper;
use Drupal\Core\Database\Database;
use Drupal\Core\Database\StatementInterface;

class SqlUtil {

  public static function buildExecuteSqlQuery($parameters, $limit, $order_by, $sqlConfigName, $statementName) {

    try {

      $model = SqlModelFactory::getSqlStatementModel($parameters, $limit, $order_by, $sqlConfigName, $statementName);
      LogHelper::log_info("SQL Statement Name: " . $statementName . "\nSQL Trace:\n" . $model->query . "\n");
      $results = self::executeSqlQuery($model);
    } catch (\Exception $e) {
      LogHelper::log_error("Error in SqlUtil::buildExecuteSqlQuery(): \n" . $e->getMessage());
      throw $e;
    }
    return $results;
  }

  public static function getSqlQuery($parameters, $limit, $order_by, $sqlConfigName, $statementName) {

    try {
      $model = self::getSqlStatementModel($parameters, $limit, $order_by, $sqlConfigName, $statementName);
      $query = $model->query;
    } catch (\Exception $e) {
      LogHelper::log_error("Error in SqlUtil::getSqlQuery(): \n" . $e->getMessage());
      throw $e;
    }
    return $query;
  }

  public static function getSqlStatementModel($parameters, $limit, $order_by, $sqlConfigName, $statementName) {

    try {
      $model = SqlModelFactory::getSqlStatementModel($parameters, $limit, $order_by, $sqlConfigName, $statementName);
    } catch (\Exception $e) {
      LogHelper::log_error("Error in SqlUtil::getSqlQuery(): \n" . $e->getMessage());
      throw $e;
    }
    return $model;
  }

  /**
   * @param sqlStatementModel $model
   *
   * @return StatementInterface
   * @throws \Exception
   */
  public static function executeSqlQuery(sqlStatementModel $model) {

    try {
      $results = self::executeSqlFetchAssoc($model->query, $model->datasource);
      LogHelper::log_info("SQL Statement Name: " . $model->name . "\nSQL Trace:\n" . $model->query . "\n");
    } catch (\Exception $e) {
      LogHelper::log_error("Error in SqlUtil::executeSqlQuery(): \n" . $e->getMessage());
      throw $e;
    }
    return $results;
  }

  /**
   * @param sqlStatementModel $model
   *
   * @return StatementInterface
   * @throws \Exception
   */
  public static function executeCountSqlQuery(sqlStatementModel $model) {

    try {
      $results = self::executeSqlFetchAssoc($model->countQuery, $model->datasource);
      LogHelper::log_notice("SQL Statement Name: " . $model->name . "\nSQL Trace:\n" . $model->countQuery . "\n");
    } catch (\Exception $e) {
      LogHelper::log_error("Error in SqlUtil::executeCountSqlQuery(): \n" . $e->getMessage());
      throw $e;
    }
    return $results;
  }

  public static function executeSqlFetchAssoc($query, $data_source = 'checkbook', $db_name = "main") {
    $results = NULL;

    //Get connection
    try {
      $connection = Database::getConnection($db_name, $data_source);
    } catch (\Exception $e) {
      LogHelper::log_error("Error getting connection. Exception is :" . $e);
      throw $e;
    }

    //execute SQL
    try {
      $results = $connection->query($query);
    } catch (\Exception $e) {
      LogHelper::log_error("Error executing DB query for generating: " . $query . ". Exception is: " . $e);
      throw $e;
    }

    return $results;
  }

}
