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

namespace Drupal\checkbook_services\Widget;

use Drupal\checkbook_log\LogHelper;
use Drupal\checkbook_services\Common\IDataService;
use Exception;
use http\Exception\InvalidArgumentException;

abstract class WidgetDataService extends WidgetService {

    private $dataService;

    function __construct($widgetConfig, IDataService $dataService = null) {

        parent::__construct($widgetConfig);
        $this->dataService = ($dataService) ?: $this->initializeDataService();
    }

    /**
     * Function to allow the client to initialize the data service
     * @return mixed
     */
    abstract function initializeDataService();

    /**
     * Returns the widget data
     * @param $parameters
     * @param $limit
     * @param $orderBy
     * @return mixed
     * @throws Exception
     */
    public function getWidgetData($parameters, $limit, $orderBy) {
        $fnData = $this->widgetConfig->dataFunc;
        return $this->getService()->$fnData($parameters, $limit, $orderBy)->getByDataset();
    }

    /**
     * Returns records from specified configured datasource
     * Returns total number of records for the widget for the dataset row count
     * @param $parameters
     * @return mixed
     * @throws Exception
     */
    public function getWidgetDataCount($parameters) {
        $fnData = $this->widgetConfig->dataFunc;
        return $this->getService()->$fnData($parameters)->getByDatasetRowCount();
    }

    /**
     * Returns count for widget header using specified datasource or default row count
     * @param $parameters
     * @return mixed
     * @throws Exception
     */
    public function getWidgetHeaderCount($parameters) {
        $fnCount = isset($this->widgetConfig->countFunc) ? $this->widgetConfig->countFunc : $this->widgetConfig->dataFunc;
        return $this->getService()->$fnCount($parameters)->getByDatasetRowCount();
    }

    /**
     * Function performs type-specific interface validation on service.
     * Returns the service if valid else throws an exception
     * @return IDataService
     * @throws Exception
     */
    private function getService() {
        if($this->dataService instanceof IDataService) {
            return $this->dataService;
        }
        else {
            LogHelper::log_error("Error in AbstractWidgetDataService invalid type, expected ISqlDataService");
            throw new InvalidArgumentException("Error in AbstractWidgetDataService invalid type, expected ISqlDataService");
        }
    }
}
