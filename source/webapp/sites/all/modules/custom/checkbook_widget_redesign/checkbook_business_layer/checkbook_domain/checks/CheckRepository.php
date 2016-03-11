<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 3/11/16
 * Time: 11:19 AM
 */

class CheckRepository {

    function GetChecks($parameters, $limit, $order_by)
    {
        $data = self::GetChecksData($parameters, $limit, $order_by);
        $factory = new CheckFactory();
        $entities = $factory->create(new Check(),$data);

        return $entities;
    }

    private function GetChecksData($parameters, $limit, $order_by)
    {
        //make DB call
        $results = null;
        $where = self::_build_where_clause($parameters);
        $query = "
            SELECT check_eft_issued_date,
                   vendor_name,
                   agency_name,
                   check_amount,
                   expenditure_object_name,
                   agency_id,
                   vendor_id,
                   expenditure_object_id,
                   department_name
              FROM disbursement_line_item_details
              WHERE {$where}
              ORDER BY {$order_by}
              LIMIT {$limit}";

        try {
            $results = self::_execute_sql_fetch_assoc($query);
        }
        catch (Exception $e) {
            log_error("Error in function GetCitywideChecksData() \nError getting data from controller: \n" . $e->getMessage());
            throw $e;
        }
        return $results;
    }

    private function _execute_sql_fetch_assoc($query, $db_name = "main", $data_source = 'checkbook')
    {
        $connection = $results = null;

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
//            $results = $connection->query($query)->fetchAssoc();
        }
        catch (Exception $e) {
            LogHelper::log_error("Error executing DB query for generating: " . $query . ". Exception is: " . $e);
            throw $e;
        }

        return $results;
    }

    private function _build_where_clause($parameters) {
        $where = null;
        foreach($parameters as $key => $value) {
            $where .= !isset($where) ? " {$key} = {$value}" : " AND {$key} = {$value}";
        }
        return $where;
    }
}