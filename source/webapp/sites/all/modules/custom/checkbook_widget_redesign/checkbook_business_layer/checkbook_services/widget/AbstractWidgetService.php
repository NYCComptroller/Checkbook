<?php

class SqlConfig {
    public $sqlConfigName;
    public $statementName;
    public $countStatementName;
}

abstract class AbstractWidgetService implements IWidgetService {

    private $sqlConfigPath;
    private $statementName;
    private $countStatementName;
    private $repository;

    function __construct($sqlConfig) {
        $this->repository = new SqlEntityRepository($sqlConfig);
        $this->sqlConfigPath = $sqlConfig->sqlConfigName;
        $this->statementName = $sqlConfig->statementName;
        $this->countStatementName = $sqlConfig->countStatementName;
    }

    /**
     * Returns the data after executing the Sql
     * @param $parameters
     * @param $limit
     * @param $orderBy
     * @param null $statementName
     * @param null $sqlConfigPath
     * @return mixed
     */
    public function getWidgetData($parameters, $limit, $orderBy, $statementName = null, $sqlConfigPath = null) {
        $sqlConfigPath = $sqlConfigPath ?: $this->sqlConfigPath;
        $statementName = $statementName ?: $this->statementName;
        $data = $this->repository->getData($parameters, $limit, $orderBy, $statementName, $sqlConfigPath);
        return $data;
    }

    /**
     * Returns the count of data after executing the Sql
     * @param $parameters
     * @param null $statementName
     * @param null $sqlConfigPath
     * @return mixed
     */
    public function getWidgetDataCount($parameters, $statementName = null, $sqlConfigPath = null) {
        $sqlConfigPath = $sqlConfigPath ?: $this->sqlConfigPath;
        $statementName = $statementName ?: $this->statementName;
        $data = $this->repository->getDataCount($parameters, $statementName, $sqlConfigPath);
        return $data;
    }

    /**
     * Returns count for widget header using $countStatementName statement or default row count
     * @param $parameters
     * @return mixed|null
     */
    public function getWidgetHeaderCount($parameters) {
        if(!isset($this->countStatementName)) {
            return null;
        }
        $data = $this->repository->getDataCount($parameters, $this->countStatementName, $this->sqlConfigPath);
        return $data;
    }

    abstract public function implDerivedColumn($column_name,$row);
}