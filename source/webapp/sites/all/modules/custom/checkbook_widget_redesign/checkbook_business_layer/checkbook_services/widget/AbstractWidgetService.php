<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 4/8/16
 * Time: 3:06 PM
 */

class SqlConfig {
    public $sqlConfigName;
    public $statementName;
    public $countStatementName;
}

abstract class AbstractWidgetService implements IWidgetService {

    private $repository;
    private $sqlConfig;

    function __construct($sqlConfig) {
        $this->sqlConfig = $sqlConfig;
        $this->repository = new WidgetRepository($sqlConfig);
    }

    public function getWidgetData($parameters, $limit, $orderBy) {
        // 1. Call Repository
        $data = $this->repository->getWidgetData($parameters, $limit, $orderBy);
        return $data;
    }

    public function getWidgetDataCount($parameters) {
        // 1. Call Repository
        $data = $this->repository->getTotalRowCount($parameters);
        return $data;
    }

    public function getWidgetHeaderCount($parameters) {
        // 1. Call Repository
        $count = $this->repository->getHeaderCount($parameters);
        return $count;
    }

    public function implementDerivedColumns($data) {
        foreach($data as $row_key => $row_value) {
            foreach($row_value as $col_key => $col_value) {
                $value = $this->implDerivedColumn($col_key,$row_value);
                if(isset($value)) {
                    $data[$row_key][$col_key] = $value;
                }
            }
        }
        return $data;
    }

    abstract public function implDerivedColumn($column_name,$row);
}