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

namespace Drupal\data_controller_postgresql\Handler;

use Drupal\data_controller\Common\Datatype\Handler\BooleanDataTypeHandler;
use Drupal\data_controller\Common\Datatype\Handler\DateDataTypeHandler;
use Drupal\data_controller\Common\Datatype\Handler\DateTimeDataTypeHandler;
use Drupal\data_controller\Common\Datatype\Handler\IntegerDataTypeHandler;
use Drupal\data_controller\Common\Datatype\Handler\NumberDataTypeHandler;
use Drupal\data_controller\Common\Datatype\Handler\StringDataTypeHandler;
use Drupal\data_controller\Common\Datatype\Handler\TimeDataTypeHandler;
use Drupal\data_controller\Common\Object\Exception\UnsupportedOperationException;
use Drupal\data_controller_sql\Datasource\Handler\Impl\DefaultImpl\AbstractPDOQueryStatementExecutionCallback;


class PostgreSQLQueryStatementExecutionCallback extends AbstractPDOQueryStatementExecutionCallback {

    //public function calculateApplicationDataType(ColumnMetaData $column) {
  public function calculateApplicationDataType($column) {
        $columnDataType = NULL;

        switch ($column->type->databaseType) {
            // support for standard types
            case 'varchar':
            case 'text':
            case 'bpchar':
                $columnDataType = StringDataTypeHandler::$DATA_TYPE;
                break;
            case 'bit':
            case 'int2':
            case 'int4':
            case 'int8':
                $columnDataType = IntegerDataTypeHandler::$DATA_TYPE;
                break;
            case 'float4':
            case 'float8':
            case 'numeric':
            case 'money':
                $columnDataType = NumberDataTypeHandler::$DATA_TYPE;
                break;
            case 'bool':
                $columnDataType = BooleanDataTypeHandler::$DATA_TYPE;
                break;
            case 'date':
                $columnDataType = DateDataTypeHandler::$DATA_TYPE;
                break;
            case 'time':
                $columnDataType = TimeDataTypeHandler::$DATA_TYPE;
                break;
            case 'timestamp':
                $columnDataType = DateTimeDataTypeHandler::$DATA_TYPE;
                break;

            // support for entity names
            case 'name':
                $columnDataType = StringDataTypeHandler::$DATA_TYPE;
                break;

            default:
                throw new UnsupportedOperationException(t(
                    "Unsupported data type for '@columnName' column: @dataType",
                    array('@columnName' => $column->name, '@dataType' => $column->type->databaseType)));
        }

        return $columnDataType;
    }
}
