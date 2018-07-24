<?php

abstract class DataService implements IDataService {

    private $repository;
    private $fnData;
    private $parameters;
    private $limit;
    private $orderBy;
    private $sqlConfigPath;

    /**
     * @param $dataFunction
     * @param $parameters
     * @param null $limit
     * @param null $orderBy
     * @param $sqlConfigPath
     * @return DataService
     */
    public function configure($dataFunction, $parameters, $limit = null, $orderBy = null, $sqlConfigPath) {
        return static::setDataFunction($dataFunction)
            ->setSqlConfigPath($sqlConfigPath)
            ->setParameters($parameters)
            ->setLimit($limit)
            ->setOrderBy($orderBy);
    }

    /**
     * @param $sqlConfigPath
     * @return DataService
     */
    public function setSqlConfigPath($sqlConfigPath) {
        $this->sqlConfigPath = $sqlConfigPath;
        return $this;
    }

    /**
     * @param $fnData
     * @return DataService
     */
    public function setDataFunction($fnData) {
        $this->fnData = $fnData;
        return $this;
    }

    /**
     * @param $parameters
     * @return DataService
     */
    public function setParameters($parameters) {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * @param $limit
     * @return DataService
     */
    public function setLimit($limit) {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param $orderBy
     * @return DataService
     */
    public function setOrderBy($orderBy) {
        $this->orderBy = $orderBy;
        return $this;
    }

    public function getByDataset($parameters = null, $limit = null, $orderBy = null) {
        $parameters = isset($parameters) ? $parameters : $this->parameters;
        $limit = isset($limit) ? $limit : $this->limit;
        $orderBy = isset($orderBy) ? $orderBy : $this->orderBy;
        $fnData = $this->fnData;
        LogHelper::log_info("Get By DataSet: ".$fnData);
        $cacheKey = 'get_by_dataset_'.md5(serialize([$parameters, $limit, $orderBy, $fnData]));
        if ($data = dmemcache_get($cacheKey)) {
          LogHelper::log_info($cacheKey.' CACHE HIT!');
          return $data;
        }
        $data = $this->getRepository()->getByDataset($parameters, $limit, $orderBy, $fnData);
        dmemcache_set($cacheKey, $data, 54000);
        return $data;
    }

    public function getByDatasetRowCount($parameters = null) {
        $parameters = isset($parameters) ? $parameters : $this->parameters;
        $fnData = $this->fnData;
        LogHelper::log_info("Get By RecordCount: ".$fnData);
        $cacheKey = 'record_count_'.md5(serialize([$parameters, $fnData]));
        if ($data = dmemcache_get($cacheKey)) {
          LogHelper::log_info($cacheKey.' CACHE HIT!');
          return $data;
        }
        $data = $this->getRepository()->getByDatasetRowCount($parameters, $fnData);
        dmemcache_set($cacheKey, $data, 54000);
        return $data;
    }

    private function getRepository() {
        if(!isset($this->repository)) {
            $this->repository = new SqlEntityRepository($this->sqlConfigPath);
        }
        return $this->repository;
    }
}
