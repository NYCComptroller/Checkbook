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

        $checks = $repo->GetChecks($parameters, $limit, $order_by);
        return $checks;
    }
} 