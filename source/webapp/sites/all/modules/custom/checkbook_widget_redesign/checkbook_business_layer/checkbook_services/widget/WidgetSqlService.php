<?php

abstract class WidgetSqlService extends WidgetService {

    private $repository;

    function __construct($widgetConfig, IWidgetRepository $repository = null) {
        parent::__construct($widgetConfig);
        $this->repository = ($repository) ?: new WidgetRepository($this->widgetConfig->sqlConfig);
    }

    /**
     * Returns the widget data
     * @param $parameters
     * @param $limit
     * @param $orderBy
     * @return mixed
     * @throws Exception
     */
    public function getWidgetData($parameters, $limit, $orderBy) {
        $data = $this->getRepository()->getWidgetData($parameters, $limit, $orderBy);
        return $data;
    }

    /**
     * Returns records from specified configured datasource
     * Returns total number of records for the widget
     * @param $parameters
     * @return mixed
     * @throws Exception
     */
    public function getWidgetDataCount($parameters) {
        $data = $this->getRepository()->getTotalRowCount($parameters);
        return $data;
    }

    /**
     * Returns count for widget header using specified datasource or default row count
     * @param $parameters
     * @return mixed
     * @throws Exception
     */
    public function getWidgetHeaderCount($parameters) {
        if(!($this->repository instanceof WidgetRepository)) return null;
        $count = $this->getRepository()->getHeaderCount($parameters);
        return $count;
    }

    /**
     * Function performs type-specific interface validation on service.
     * Returns the service if valid else throws an exception
     * @return IWidgetRepository
     * @throws Exception
     */
    private function getRepository() {
        if($this->repository instanceof IWidgetRepository) {
            return $this->repository;
        }
        else {
            LogHelper::log_error("Error in AbstractWidgetSqlService invalid type, expected IWidgetRepository");
            throw new InvalidArgumentException("Error in AbstractWidgetSqlService invalid type, expected IWidgetRepository");
        }
    }
}
