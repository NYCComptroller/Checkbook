<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 3/31/16
 * Time: 11:33 AM
 */

class SqlUtil {


    public static function buildExecuteSqlQuery($statement, $parameters, $limit, $order_by, $type) {

        try {

            $model = SqlModelFactory::getSqlStatementModel($statement, $parameters, $limit, $order_by, $type);
            $results = self::executeSqlQuery($model);
        }
        catch (Exception $e) {
            log_error("Error in SqlUtil::buildExecuteSqlQuery(): \n" . $e->getMessage());
            throw $e;
        }
        return $results;
    }

    public static function getSqlQuery($statement, $parameters, $limit, $order_by, $type) {

        try {
            $model = self::getSqlStatementModel($statement, $parameters, $limit, $order_by, $type);
            $query = $model->query;
        }
        catch (Exception $e) {
            log_error("Error in SqlUtil::getSqlQuery(): \n" . $e->getMessage());
            throw $e;
        }
        return $query;
    }

    public static function getSqlStatementModel($statement, $parameters, $limit, $order_by, $type) {

        try {
            $model = SqlModelFactory::getSqlStatementModel($statement, $parameters, $limit, $order_by, $type);
        }
        catch (Exception $e) {
            log_error("Error in SqlUtil::getSqlQuery(): \n" . $e->getMessage());
            throw $e;
        }
        return $model;
    }

    public static function executeSqlQuery(sqlStatementModel $model) {

        try {
            $results = self::executeSqlFetchAssoc($model->query, $model->datasource);
        }
        catch (Exception $e) {
            log_error("Error in SqlUtil::executeSqlQuery(): \n" . $e->getMessage());
            throw $e;
        }
        return $results;
    }

    public static function prepareSqlQuery($sql, $parameters, $limit, $order_by) {
        $where = self::buildWhereClause($parameters);
        $where = isset($where) ? "\nWHERE {$where}" : "";
        $order_by = isset($order_by) ? "\nORDER BY {$order_by}" : "";
        $limit = isset($limit) ? "\nLIMIT {$limit}" : "";
        $query = "{$sql}{$where}{$order_by}{$limit}";

        return $query;
    }

    public static function executeSqlFetchAssoc($query, $data_source = 'checkbook', $db_name = "main")
    {
        $results = null;

        //Get connection
        try {
            $connection = Database::getConnection($db_name, $data_source);
        }
        catch(Exception $e) {
            LogHelper::log_error("Error getting connection. Exception is :".$e);
            throw $e;
        }

        //execute SQL
        try {
            $results = $connection->query($query);
        }
        catch (Exception $e) {
            LogHelper::log_error("Error executing DB query for generating: " . $query . ". Exception is: " . $e);
            throw $e;
        }

        return $results;
    }
} 