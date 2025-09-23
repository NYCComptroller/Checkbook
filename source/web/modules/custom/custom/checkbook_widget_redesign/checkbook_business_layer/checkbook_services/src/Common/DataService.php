<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\checkbook_services\Common;

use Drupal\checkbook_domain\Sql\SqlEntityRepository;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_log\LogHelper;

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
    public function configure($dataFunction, $parameters, $limit = null, $orderBy = null, $sqlConfigPath = null) {
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
    public function setSqlConfigPath($sqlConfigPath): static
    {
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
        $dataSource = Datasource::getCurrent();
        $cacheKey = 'get_by_dataset_' . $dataSource . '_' .md5(serialize([$parameters, $limit, $orderBy, $fnData, $dataSource]));
        if ($data = _checkbook_dmemcache_get($cacheKey)) {
          LogHelper::log_info("Get Cached data in DataService::getByDataset with cachekey " . $cacheKey);
          return $data;
        }
        $data = $this->getRepository()->getByDataset($parameters, $limit, $orderBy, $fnData);
        LogHelper::log_info("Set Cached data in DataService::getByDataset with cachekey " . $cacheKey);
        _checkbook_dmemcache_set($cacheKey, $data);
        return $data;
    }

    public function getByDatasetRowCount($parameters = null) {
        $parameters = isset($parameters) ? $parameters : $this->parameters;
        $fnData = $this->fnData;
        LogHelper::log_info("Get By RecordCount: ".$fnData);
        $dataSource = Datasource::getCurrent();
        $cacheKey = 'get_by_record_count_' . '_' .md5(serialize([$parameters, $fnData, $dataSource]));
        if ($count = _checkbook_dmemcache_get($cacheKey)) {
          LogHelper::log_info("Get Cached data in DataService::getByDatasetRowCount with cachekey " . $cacheKey);
          return $count;
        }
        $count = $this->getRepository()->getByDatasetRowCount($parameters, $fnData);
        LogHelper::log_info("Set Cached data in DataService::getByDatasetRowCount with cachekey " . $cacheKey);
        _checkbook_dmemcache_set($cacheKey, $count);
        return $count;
    }

    public function getRepository() {
        if(!isset($this->repository)) {
            $this->repository = new SqlEntityRepository($this->sqlConfigPath);
        }
        return $this->repository;
    }
}
