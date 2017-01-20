<?php

abstract class DataService implements IDataService {

    private $repository;
    private $fnData;
    private $parameters;
    private $limit;
    private $orderBy;
    private $sqlConfigPath;

    public function setSqlConfigPath($sqlConfigPath) {
        $this->sqlConfigPath = $sqlConfigPath;
        return $this;
    }

    public function setDataFunction($fnData) {
        $this->fnData = $fnData;
        return $this;
    }

    public function setParameters($parameters) {
        $this->parameters = $parameters;
        return $this;
    }

    public function setLimit($limit) {
        $this->limit = $limit;
        return $this;
    }

    public function setOrderBy($orderBy) {
        $this->orderBy = $orderBy;
        return $this;
    }

    public function getByDataset($parameters = null, $limit = null, $orderBy = null) {
        $parameters = isset($parameters) ? $parameters : $this->parameters;
        $limit = isset($limit) ? $limit : $this->limit;
        $orderBy = isset($orderBy) ? $orderBy : $this->orderBy;
        $fnData = $this->fnData;
//        log_error("Get By DataSet: \n\n".$fnData."\n\n");
        return $this->getRepository()->getByDataset($parameters, $limit, $orderBy, $fnData);
    }

    public function getByDatasetRowCount($parameters = null) {
        $parameters = isset($parameters) ? $parameters : $this->parameters;
        $fnData = $this->fnData;
//        log_error("Get By RecordCount: \n\n".$fnData."\n\n");
        return $this->getRepository()->getByDatasetRowCount($parameters, $fnData);
    }

    private function getRepository() {
        if(!isset($this->repository)) {
            $this->repository = new SqlEntityRepository($this->sqlConfigPath);
        }
        return $this->repository;
    }
}