<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
* 
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


abstract class AbstractDataControllerProxy extends AbstractFactory {

  /**
   * @var |null
   */
  protected $instance = NULL;

  /**
   * AbstractDataControllerProxy constructor.
   */
  protected function __construct() {
        parent::__construct();

        $this->instance = $this->prepareProxiedInstance();
    }

  /**
   * @return mixed
   */
  abstract protected function prepareProxiedInstance();

  /**
   * @param $methodName
   * @param $args
   * @return mixed
   */
  public function __call($methodName, $args) {
        $timeStart = microtime(TRUE);
        $result = call_user_func_array(array($this->instance, $methodName), $args);
        LogHelper::log_info(t(
            'Data Controller execution time for @methodName(): !executionTime',
            array('@methodName' => $methodName, '!executionTime' => ExecutionPerformanceHelper::formatExecutionTime($timeStart))));

        return $result;
    }
}
