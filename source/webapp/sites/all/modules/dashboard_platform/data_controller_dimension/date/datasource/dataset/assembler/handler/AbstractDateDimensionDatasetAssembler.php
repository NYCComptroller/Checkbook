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


abstract class AbstractDateDimensionDatasetAssembler extends AbstractDatasetSourceAssembler {

    protected static function selectColumn(TableSection $table, array &$requestedColumnNames = NULL, $alwaysInclude, $columnName, $compositeColumnName, $columnAlias = NULL) {
        $includeAllColumns = !isset($requestedColumnNames);

        $columnIdentity = isset($columnAlias) ? $columnAlias : $columnName;
        $isColumnPresent = isset($requestedColumnNames[$columnIdentity]);

        if ($alwaysInclude || $includeAllColumns || $isColumnPresent) {
            $column = $compositeColumnName
                ? new CompositeColumnSection($columnName, $columnAlias)
                : new ColumnSection($columnName, $columnAlias);
            // we included the column only because it is mandatory ($alwaysInclude == TRUE)
            // but because the column is not in the list and not all columns are requested we hide it
            $column->visible = $includeAllColumns || ($isColumnPresent && $requestedColumnNames[$columnIdentity]);

            $table->columns[] = $column;

            if ($isColumnPresent) {
                unset($requestedColumnNames[$columnIdentity]);
            }

            return $column;
        }

        return NULL;
    }

    protected static function prepareRequestedColumnNames($columnNames) {
        $requestedColumnNames = NULL;

        if (isset($columnNames)) {
            foreach ($columnNames as $columnName) {
                $requestedColumnNames[$columnName] = TRUE;
            }
        }

        return $requestedColumnNames;
    }

    protected static function registerDependentColumnName(array &$requestedColumnNames = NULL, $columnName) {
        if (!isset($requestedColumnNames[$columnName])) {
            // if the column is not present we add one but it should be invisible
            $requestedColumnNames[$columnName] = FALSE;
        }
    }
}
