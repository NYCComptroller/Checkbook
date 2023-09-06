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

namespace Drupal\data_controller_sql\Datasource\Dataset\Assembler\Statement;

class ColumnNameHelper {

    /*
     * Splits column name into table alias and table column name
     */
    public static function splitColumnName($columnName) {
        $index = strrpos($columnName, '.');

        return ($index === FALSE)
            ? array(NULL, $columnName)
            : array(substr($columnName, 0, $index), substr($columnName, $index + 1));
    }

    /*
     * Combines table alias and table column name
     */
    public static function combineColumnName($tableAlias, $columnName) {
        return isset($tableAlias) ? $tableAlias . '.' . $columnName : $columnName;
    }
}
