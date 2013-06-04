<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class PDOExecuteStatementImpl extends AbstractExecuteStatementImpl {

    public function executeIndividualStatement(DataSourceHandler $handler, $connection, $sql) {
        return $connection->exec($sql);
    }
}
