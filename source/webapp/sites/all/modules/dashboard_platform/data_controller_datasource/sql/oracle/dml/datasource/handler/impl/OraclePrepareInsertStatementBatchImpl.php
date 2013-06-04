<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class OraclePrepareInsertStatementBatchImpl extends AbstractPrepareInsertStatementBatchImpl {

    public function prepare(DataSourceHandler $handler, $tableName, array $columnNames, array $records) {
        $header = "INTO $tableName (" . implode(', ', $columnNames) . ') VALUES';

        $sql = NULL;
        foreach ($records as $record) {
            $sql .= "\n  $header (" . implode(', ', $record) . ')';
        }

        $sql = "INSERT ALL$sql\nSELECT * FROM dual";

        return $sql;
    }
}
