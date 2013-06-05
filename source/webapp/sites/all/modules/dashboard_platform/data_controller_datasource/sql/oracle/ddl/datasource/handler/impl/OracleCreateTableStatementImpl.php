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


class OracleCreateTableStatementImpl extends AbstractCreateTableStatementImpl {

    protected function maxLength4VariableLengthString() {
        return 4000;
    }

    protected function assembleVariableLengthString(ColumnMetaData $column) {
        $column->type->databaseType = 'VARCHAR2' . '(' . $column->type->length . ')';
    }

    protected function assembleLongString(ColumnMetaData $column) {
        $column->type->databaseType = 'CLOB';
    }

    protected function assembleInteger(ColumnMetaData $column) {
        $column->type->databaseType = 'NUMBER(10)';
    }

    protected function assembleBigInteger(ColumnMetaData $column) {
        $column->type->databaseType = 'NUMBER(20)';
    }

    protected function assembleNumber(ColumnMetaData $column, $selectedScale) {
        $column->type->databaseType = "NUMBER({$column->type->precision}, $selectedScale)";
    }

    protected function assembleColumnComment(DataSourceHandler $handler, DatasetMetaData $dataset, ColumnMetaData $column) {
        return "COMMENT ON COLUMN $dataset->source.$column->name IS " . $handler->formatStringValue($column->description);
    }

    protected function assembleTableComment(DataSourceHandler $handler, DatasetMetaData $dataset) {
        return "COMMENT ON TABLE $dataset->source IS " . $handler->formatStringValue($dataset->description);
    }

    protected function prepareCreateTableStatement(DataSourceHandler $handler, DatasetMetaData $dataset) {
        $sql = array(parent::prepareCreateTableStatement($handler, $dataset));

        foreach ($dataset->getColumns() as $column) {
            if (isset($column->description)) {
                $sql[] = $this->assembleColumnComment($handler, $dataset, $column);
            }
        }

        if (isset($dataset->description)) {
            $sql[] = $this->assembleTableComment($handler, $dataset);
        }

        return $sql;
    }
}
