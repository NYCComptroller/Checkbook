<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
