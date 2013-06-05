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


class MySQLCreateTableStatementImpl extends AbstractCreateTableStatementImpl {

    // can be set to NULL to use engine by default
    public static $STORAGE_ENGINE__DEFAULT = 'InnoDB';

    protected function maxLength4VariableLengthString() {
        return 65535;
    }

    protected function assembleLongString(ColumnMetaData $column) {
        $column->type->databaseType = 'TEXT';
    }

    protected function assembleBigInteger(ColumnMetaData $column) {
        $column->type->databaseType = 'BIGINT';
    }

    protected function prepareCreateTableStatement4Column(DataSourceHandler $handler, ColumnMetaData $column) {
        $sql = parent::prepareCreateTableStatement4Column($handler, $column);

        // adding column comment
        if (isset($column->description)) {
            $sql .= ' COMMENT ' . $handler->formatStringValue($column->description);
        }

        return $sql;
    }

    protected function assembleTableOptions(DataSourceHandler $handler, DatasetMetaData $dataset, $indent, &$sql) {
        parent::assembleTableOptions($handler, $dataset, $indent, $sql);

        if (isset(self::$STORAGE_ENGINE__DEFAULT)) {
            $sql .= "\nENGINE = " . self::$STORAGE_ENGINE__DEFAULT;
        }
        if (isset($dataset->description)) {
            $sql .= "\nCOMMENT = " . $handler->formatStringValue($dataset->description);
        }
    }
}
