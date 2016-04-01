<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 2/19/16
 * Time: 4:37 PM
 */

class SpendingService {

    function GetCitywideChecksData($parameters, $limit, $order_by) {
        $repo = new CheckRepository();
        $checks = $repo->GetCitywideChecks($parameters, $limit, $order_by);
        return $checks;
    }

    function GetMwbeChecksData($parameters, $limit, $order_by) {
        $repo = new CheckRepository();
        $checks = $repo->GetMwbeChecks($parameters, $limit, $order_by);
        return $checks;
    }
} 