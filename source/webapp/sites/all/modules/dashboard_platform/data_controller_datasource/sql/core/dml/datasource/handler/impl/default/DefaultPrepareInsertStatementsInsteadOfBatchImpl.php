<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DefaultPrepareInsertStatementsInsteadOfBatchImpl extends AbstractPrepareInsertStatementBatchImpl {

    public function prepare(DataSourceHandler $handler, $tableName, array $columnNames, array $records) {
        $sqls = NULL;

        $header = "INSERT INTO $tableName (" . implode(', ', $columnNames) . ') VALUES ';

        foreach ($records as $record) {
            $sqls[] = $header . '(' . implode(', ', $record) . ')';
        }

        return $sqls;
    }
}
