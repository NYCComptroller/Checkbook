<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 2/19/16
 * Time: 4:37 PM
 */

class SpendingWidgetService {
}

//class SpendingService extends AbstractWidgetService {
//
//    function GetCitywideChecksData($parameters, $limit, $order_by) {
//        $repo = new CheckRepository();
//        $checks = $repo->GetCitywideChecks($parameters, $limit, $order_by);
//        return $checks;
//    }
//
//    function GetMwbeChecksData($parameters, $limit, $order_by) {
//        $repo = new CheckRepository();
//        $checks = $repo->GetMwbeChecks($parameters, $limit, $order_by);
//        return $checks;
//    }
//
//    public function GetWidgetDataset($parameters, $limit, $order_by)
//    {
//        $repo = new CheckRepository();
//        $checks = $repo->GetMwbeChecks($parameters, $limit, $order_by);
//        return $checks;
//    }
//
//    public function GetWidgetTotalCount($parameters, $limit, $order_by)
//    {
//        // TODO: Implement GetTotalCount() method.
//    }
//}
//
//abstract class AbstractWidgetService {
//
//    protected $statementName;
//    protected $classType;
//
//    function __construct($classType) {
//        $this->classType = $classType;
//    }
//
//    abstract public function GetWidgetDataset($parameters, $limit, $order_by);
//    abstract public function GetWidgetTotalCount($parameters, $limit, $order_by);
//}