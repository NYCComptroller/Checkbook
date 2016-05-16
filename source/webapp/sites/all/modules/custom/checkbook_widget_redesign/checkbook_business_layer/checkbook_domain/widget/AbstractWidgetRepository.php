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
    protected $countStatementName;

    private $widgetSqlModel;
    private $headerSqlModel;

    function __construct($sqlConfig) {
        $this->sqlConfigName = $sqlConfig->sqlConfigName;
        $this->statementName = $sqlConfig->statementName;
        $this->countStatementName = $sqlConfig->countStatementName;
    }

    /**
     * Returns the widget data
     * @param $parameters
     * @param $limit
     * @param $order_by
     * @return mixed
     */
    public function getWidgetData($parameters, $limit, $order_by) {
        // 1. Get Data
        $data = $this->_getData($parameters, $limit, $order_by);
        // 2. Call Factory
        $factory = new EntityFactory();
        $entities = $factory->create($data);
        return $entities;
    }

    /**
     * Returns total number of records for the widget
     * @param $parameters
     * @return mixed
     */
    public function getTotalRowCount($parameters) {
        // 1. Get Data
        $data = $this->_getTotalRowCount($parameters);
        // 2. Call Factory
        $results = $data->fetchAll();
        return $results[0]->record_count;
    }

    /**
     * Returns count for widget header using $countStatementName statement or default row count
     * @param $parameters
     * @return mixed
     */
    public function getHeaderCount($parameters) {
        //Default count
        if(!isset($this->countStatementName)) {
            return null;
        }
        $data = $this->_getHeaderCount($parameters, null, null);
        $results = $data->fetchAll();
        return $results[0]->record_count;
    }

    /**
     * Returns records from executing SQL specified in $statementName
     * @param $parameters
     * @param $limit
     * @param $order_by
     * @return DatabaseStatementInterface|null
     * @throws Exception
     */
    private function _getData($parameters, $limit, $order_by)
    {
        try {
            self::_prepareWidgetSqlModel($parameters, $limit, $order_by);
            $results = SqlUtil::executeSqlQuery($this->widgetSqlModel);
        }
        catch (Exception $e) {
            log_error("Error in function AbstractWidgetRepository::getData() \nError getting data from controller: \n" . $e->getMessage());
            throw $e;
        }
        return $results;
    }

    /**
     * Returns the count of records returned from the SQL in $statementName
     * @param $parameters
     * @return DatabaseStatementInterface|null
     * @throws Exception
     */
    private function _getTotalRowCount($parameters)
    {
        try {
            self::_prepareWidgetSqlModel($parameters);
            $results = SqlUtil::executeCountSqlQuery($this->widgetSqlModel);
        }
        catch (Exception $e) {
            log_error("Error in function AbstractWidgetRepository::getRowCount() \nError getting data from controller: \n" . $e->getMessage());
            throw $e;
        }
        return $results;
    }

    /**
     * Gets the count of data using $countStatementName or if not provided,
     * the count of the records returned from the SQL in $statementName
     * @param $parameters
     * @return DatabaseStatementInterface|null
     * @throws Exception
     */
    private function _getHeaderCount($parameters)
    {
        try {
            $this->headerSqlModel = SqlModelFactory::getSqlStatementModel($parameters, null, null, $this->sqlConfigName, $this->countStatementName);
            $results = SqlUtil::executeSqlQuery($this->headerSqlModel);
        }
        catch (Exception $e) {
            log_error("Error in function AbstractWidgetRepository::getRowCount() \nError getting data from controller: \n" . $e->getMessage());
            throw $e;
        }
        return $results;
    }

    private function _prepareWidgetSqlModel($parameters, $limit = null, $order_by = null)
    {
        if($this->widgetSqlModel == null) {
            $this->widgetSqlModel = SqlModelFactory::getSqlStatementModel($parameters, $limit, $order_by, $this->sqlConfigName, $this->statementName);
        }
    }
}