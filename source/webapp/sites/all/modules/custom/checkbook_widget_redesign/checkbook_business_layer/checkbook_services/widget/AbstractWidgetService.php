<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 4/8/16
 * Time: 3:06 PM
 */

abstract class AbstractWidgetService implements IWidgetService {

    private $repository;

    function __construct($sqlConfigName, $statementName) {

        $this->repository = new WidgetRepository($sqlConfigName, $statementName);
    }

    public function getWidgetData($parameters, $limit, $order_by) {
        // 1. Call Repository
        $data = $this->repository->getWidgetData($parameters, $limit, $order_by);
        // 2. Calculate Derived Columns
        $data = $this->implDerivedColumns($data);
        return $data;

    }

    public function getWidgetDataCount($parameters, $limit, $order_by) {
        // 1. Call Repository
        $count = $this->repository->getWidgetDataCount($parameters, $limit, $order_by);
        return $count;
    }

    abstract public function implDerivedColumns($data);
}