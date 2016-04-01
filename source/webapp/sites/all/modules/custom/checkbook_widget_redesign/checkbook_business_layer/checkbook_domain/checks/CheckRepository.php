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
            $type = "checks";
            $statement = "GetCityWideChecks";
            $results = SqlUtil::buildExecuteSqlQuery($statement, $parameters, $limit, $order_by, $type);
        }
        catch (Exception $e) {
            log_error("Error in function GetCitywideChecksData() \nError getting data from controller: \n" . $e->getMessage());
            throw $e;
        }
        return $results;
    }

    private function GetMwbeChecksData($parameters, $limit, $order_by)
    {
        try {
            $type = "checks";
            $statement = "GetMwbeChecks";
            $results = SqlUtil::buildExecuteSqlQuery($statement, $parameters, $limit, $order_by, $type);
        }
        catch (Exception $e) {
            log_error("Error in function GetMwbeChecksData() \nError getting data from controller: \n" . $e->getMessage());
            throw $e;
        }
        return $results;
    }
}
