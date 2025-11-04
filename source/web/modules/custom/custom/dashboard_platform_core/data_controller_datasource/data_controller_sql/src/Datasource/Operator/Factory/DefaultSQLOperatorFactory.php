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

namespace Drupal\data_controller_sql\Datasource\Operator\Factory;

use Drupal\data_controller\Common\Object\Exception\IllegalArgumentException;
use Drupal\data_controller\Datasource\DataSourceQueryHandler;
use Drupal\data_controller\Datasource\Operator\OperatorHandler;
use Drupal\data_controller_sql\Datasource\Operator\SQLOperatorFactory;


class DefaultSQLOperatorFactory extends SQLOperatorFactory {

    protected function getHandlerClassName($operatorClassName): string
    {
      $test = explode('\\',$operatorClassName);

      $sqlOperatorClassName = '\Drupal\data_controller_sql\Datasource\Operator\Handler\\'.'SQL_' . end($test);
        if (!class_exists($sqlOperatorClassName)) {
            throw new IllegalArgumentException(t(
                'Unsupported SQL-driven extension for the operator: @className',
                array('@className' => $operatorClassName)));
        }

        return $sqlOperatorClassName;
    }

    public function getHandler(DataSourceQueryHandler $datasourceQueryHandler, OperatorHandler $operatorHandler) {
        $operatorClassName = get_class($operatorHandler);

        $sqlOperatorClassName = $this->getHandlerClassName($operatorClassName);

        return new $sqlOperatorClassName($datasourceQueryHandler, $operatorHandler);
    }
}
