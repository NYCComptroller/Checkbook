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


class DefaultPrepareUpdateStatementImpl extends AbstractPrepareUpdateStatementImpl {

    protected function prepareColumnExpressions(array $columnValues, $delimiter) {
        $s = NULL;

        foreach ($columnValues as $columnName => $value) {
            if (isset($s)) {
                $s .= $delimiter;
            }

            $s .=  $columnName . ' = ' . $value;
        }

        return $s;
    }

    public function prepare(DataSourceHandler $handler, $tableName, array $setColumnValues = NULL, array $whereColumnValues = NULL) {
        // we do not need to update any columns. Just ignoring this request
        if (!isset($setColumnValues)) {
            return NULL;
        }

        $sql = "UPDATE $tableName SET " . $this->prepareColumnExpressions($setColumnValues, ', ');
        if (isset($whereColumnValues)) {
            $sql .= ' WHERE ' . $this->prepareColumnExpressions($whereColumnValues, ' AND ');
        }

        return $sql;
    }
}
