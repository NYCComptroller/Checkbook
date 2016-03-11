<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 3/11/16
 * Time: 11:19 AM
 */

class CheckRepository {

    function GetCityWideChecks($parameters, $limit, $order_by)
    {
        $data = self::GetCityWideChecksData($parameters, $limit, $order_by);
        $factory = new CheckFactory();
        $entities = $factory->create(new Check(),$data);

        return $entities;
    }

    function GetMwbeChecks($parameters, $limit, $order_by)
    {
        $data = self::GetMwbeChecksData($parameters, $limit, $order_by);
        $factory = new CheckFactory();
        $entities = $factory->create(new MwbeCheck(),$data);

        return $entities;
    }
    private function GetCityWideChecksData($parameters, $limit, $order_by)
    {
        try {
            $domain = "checks";
            $statement = "GetCityWideChecks";

//            $sql_config = self::LoadSqlConfig($domain, $statement);

            $query = self::BuildSqlQuery($domain, $statement, $parameters, $limit, $order_by);
            $results = self::_execute_sql_fetch_assoc($query);
        }
        catch (Exception $e) {
            log_error("Error in function GetCitywideChecksData() \nError getting data from controller: \n" . $e->getMessage());
            throw $e;
        }
        return $results;
    }

    private function GetMwbeChecksData($parameters, $limit, $order_by)
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

    private function BuildSqlQuery($domain, $statement, $parameters, $limit, $order_by) {

        $node = self::LoadSqlConfig($domain, $statement);

//        $statement = (string)$node->attributes()->name;
//        $datasource = (string)$node->attributes()->datasource;
        $sql = trim((string)$node->sql);

        $where = self::_build_where_clause($parameters);
        $where = isset($where) ? "\nWHERE {$where}" : "";
        $order_by = isset($order_by) ? "\nORDER BY {$order_by}" : "";
        $limit = isset($limit) ? "\nLIMIT {$limit}" : "";
        $query = "{$sql}{$where}{$order_by}{$limit}";

        return $query;
    }

    private function GetDataSourceByConfig($config) {
        return (string)$config->attributes()->datasource;
    }

    private function LoadSqlConfig($domain, $statement) {
        $file = realpath(drupal_get_path('module', 'checkbook_domain')) . '/sql/'.$domain.'.xml';
        $xml = simplexml_load_file($file);
        $node = $xml->xpath('/statements/statement[@name="'.$statement.'"]')[0];
        return $node;
    }

    private function _execute_sql_fetch_assoc($query, $data_source = 'checkbook', $db_name = "main")
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

//class SqlConfiguration {
//
//    private $config_xml;
//
//    function __construct($domain)
//    {
//        $file = realpath(drupal_get_path('module', 'checkbook_domain')) . '/sql/'.$domain.'.xml';
//        $this->config_xml = simplexml_load_file($file);
//    }
//
//    function get_config() {
//        if(isset($this->config_xml)) {
//            return $this->config_xml;
//        }
//    }
//
//    function get_statement_config($name) {
//
//        $node = $this->config_xml->xpath('/statements/statement[@name="'.$name.'"]')[0];
//        return $node;
//    }
//
//    function get_statement($name) {
//        $sql = trim((string)$this->config_xml->sql);
//        return $sql;
//    }
//
//
//}