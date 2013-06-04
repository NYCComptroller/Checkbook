<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractExecuteQueryStatementImpl extends AbstractObject {

    abstract public function execute(
            DataSourceHandler $handler,
            DataControllerCallContext $callcontext,
            $connection, $sql,
            __SQLDataSourceHandler__AbstractQueryCallbackProxy $callbackInstance);
}

abstract class AbstractQueryStatementExecutionCallback extends AbstractObject {

    abstract public function fetchNextRecord($connection, $statement);

    abstract public function getColumnCount($connection, $statement);

    abstract public function getColumnMetaData($connection, $statement, $columnIndex);

    abstract public function calculateApplicationDataType(ColumnMetaData $column);
}
