<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class OraclePrepareManipulationStatementBatchImpl extends AbstractPrepareManipulationStatementBatchImpl {

    public function prepare(DataSourceHandler $handler, array $sqls) {
        $indent = str_pad('', Statement::$INDENT_NESTED);

        $block = NULL;
        foreach ($sqls as $sql) {
            $block .= "\n$indent" . StringHelper::indent($sql, Statement::$INDENT_NESTED, FALSE) . ';';
        }

        return "BEGIN$block\nEND;";
    }
}
