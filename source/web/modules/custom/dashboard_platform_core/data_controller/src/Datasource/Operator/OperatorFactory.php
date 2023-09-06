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

namespace Drupal\data_controller\Datasource\Operator;

use Drupal\data_controller\Common\Pattern\Factory\AbstractFactory;
use Drupal\data_controller\Datasource\Operator\Factory\DefaultOperatorFactory;
use Drupal\data_controller\Datasource\Operator\Handler\AbstractOperatorMetaData;

abstract class OperatorFactory extends AbstractFactory {

    /**
     * @var null
     */
    private static $factory = NULL;

    /**
     * @param mixed ...$params
     *     A string containing name of an operator.
     *     A variable number of arguments which are passed to corresponding operator handler instance.
     *     Instead of a variable number of arguments, you may also pass a single array containing the arguments.
     * @return mixed
     */
    public abstract function initiateHandler(...$params);

    /**
     * @static
     * @return OperatorFactory
     */
    public static function getInstance() {
        if (!isset(self::$factory)) {
            self::$factory = new DefaultOperatorFactory();
        }

        return self::$factory;
    }

    /**
     * @param $operatorName
     * @return mixed
     */
    public abstract function isSupported($operatorName);

    /**
     * @return mixed
     */
    public abstract function getSupportedOperators();

    /**
     * @param $operatorName
     * @return AbstractOperatorMetaData
     */
    public abstract function getOperatorMetaData($operatorName);
}
