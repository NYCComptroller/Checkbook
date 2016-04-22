<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 4/8/16
 * Time: 3:13 PM
 */

abstract class AbstractWidgetRepository implements IWidgetRepository {

    protected $sqlConfigName;
    protected $statementName;

    function __construct($sqlConfigName, $statementName) {
        $this->sqlConfigName = $sqlConfigName;
        $this->statementName = $statementName;
    }

    public function getWidgetData($parameters, $limit, $order_by) {

        // 1. Get Data
        $data = $this->getData($parameters, $limit, $order_by);
        // 2. Call Factory
        $factory = new EntityFactory();
        $entities = $factory->create($data);
        return $entities;
    }

    public function getWidgetDataCount($parameters) {
        // 1. Get Data
        $data = $this->getData($parameters, null, null);
        // 2. Call Factory
        $results = $data->fetchAll();
        return $results[0]->record_count;
    }

    private function getData($parameters, $limit, $order_by)
    {
        try {
            $results = SqlUtil::buildExecuteSqlQuery($parameters, $limit, $order_by, $this->sqlConfigName, $this->statementName);
        }
        catch (Exception $e) {
            log_error("Error in function AbstractWidgetRepository::getData() \nError getting data from controller: \n" . $e->getMessage());
            throw $e;
        }
        return $results;
    }
}