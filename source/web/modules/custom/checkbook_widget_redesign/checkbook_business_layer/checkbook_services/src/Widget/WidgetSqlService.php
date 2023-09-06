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

use Drupal\checkbook_domain\Widget\IWidgetRepository;
use Drupal\checkbook_domain\Widget\WidgetRepository;
use Drupal\checkbook_log\LogHelper;
use Exception;
use http\Exception\InvalidArgumentException;

/**
 * TODO
 * Need to define IwidgetRepository class file path
 * LogHelper class file path
 */

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
        return $this->getRepository()->getWidgetData($parameters, $limit, $orderBy);
    }

    /**
     * Returns records from specified configured datasource
     * Returns total number of records for the widget
     * @param $parameters
     * @return mixed
     * @throws Exception
     */
    public function getWidgetDataCount($parameters) {
        return $this->getRepository()->getTotalRowCount($parameters);
    }

    /**
     * Returns count for widget header using specified datasource or default row count
     * @param $parameters
     * @return mixed
     * @throws Exception
     */
    public function getWidgetHeaderCount($parameters) {
      if(!($this->repository instanceof WidgetRepository)) {
          return null;
        }
        return $this->getRepository()->getHeaderCount($parameters);
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
