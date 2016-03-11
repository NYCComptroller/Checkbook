<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 2/19/16
 * Time: 4:37 PM
 */

class SpendingService {

    function GetCitywideChecksData() {

        $repo = new CheckRepository();

        $parameters = array("check_eft_issued_nyc_year_id" => 117);
        $limit = 5;
        $order_by = "check_amount DESC";

        $checks = $repo->GetCitywideChecks($parameters, $limit, $order_by);
        return $checks;
    }

    function GetMwbeChecksData() {

        $repo = new CheckRepository();

        $parameters = array("check_eft_issued_nyc_year_id" => 117);
        $limit = 5;
        $order_by = "check_amount DESC";

        $checks = $repo->GetMwbeChecks($parameters, $limit, $order_by);
        return $checks;
    }

//    function TestSql() {
//
//        $repo = new CheckRepository();
//
//        $domain = "checks";
//        $statement = "GetCityWideChecks";
//        $sql = $repo->GetSqlConfig($domain, $statement);
//        return $sql;
//    }
} 