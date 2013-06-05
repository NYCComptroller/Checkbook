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


class PDOExecuteQueryStatementImpl extends AbstractExecuteQueryStatementImpl {

    public function execute(
            DataSourceHandler $handler,
            DataControllerCallContext $callcontext,
            $connection, $sql,
            __SQLDataSourceHandler__AbstractQueryCallbackProxy $callbackInstance) {

        $statement = $connection->query($sql);
        try {
            $result = $callbackInstance->callback($callcontext, $connection, $statement);
        }
        catch (Exception $e) {
            $statement->closeCursor();
            throw $e;
        }
        $statement->closeCursor();

        return $result;
    }
}

abstract class AbstractPDOQueryStatementExecutionCallback extends AbstractQueryStatementExecutionCallback {

    public function fetchNextRecord($connection, $statement) {
        return $statement->fetch(PDO::FETCH_NUM);
    }

    public function getColumnCount($connection, $statement) {
        return $statement->columnCount();
    }

    /**
     * @param $statement
     * @param $columnIndex
     * @return ColumnMetaData
     */
    public function getColumnMetaData($connection, $statement, $columnIndex) {
        $statementColumnMetaData = $statement->getColumnMeta($columnIndex);
        if ($statementColumnMetaData === FALSE) {
            return FALSE;
        }

        $column = new ColumnMetaData();
        $column->name = $statementColumnMetaData['name'];
        $column->type->databaseType = $statementColumnMetaData['native_type'];

        // TODO add support for $column->type->length, ...->precision, ...->scale

        return $column;
    }
}
