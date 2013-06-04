<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DefaultPrepareInsertStatementBatchImpl extends AbstractPrepareInsertStatementBatchImpl {

    public function prepare(DataSourceHandler $handler, $tableName, array $columnNames, array $records) {
        $indent = str_pad('', Statement::$INDENT_INSERT_VALUES);

        $sql = NULL;
        foreach ($records as $record) {
            if (isset($sql)) {
                $sql .= ",\n{$indent}       ";
            }

            $sql .= '(' . implode(', ', $record) . ')';
        }

        $sql = "INSERT INTO $tableName (" . implode(', ', $columnNames) . ")\n{$indent}VALUES " . $sql;

        return $sql;
    }
}
