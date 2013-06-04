<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractExecuteStatementImpl extends AbstractObject {

    public function execute(DataSourceHandler $handler, $connection, $sql) {
        $affectedRecordCount = 0;

        if (isset($sql)) {
            if (is_array($sql)) {
                foreach ($sql as $individualStatement) {
                    $affectedRecordCount += $this->executeIndividualStatement($handler, $connection, $individualStatement);
                }
            }
            else {
                $affectedRecordCount += $this->executeIndividualStatement($handler, $connection, $sql);
            }
        }

        return $affectedRecordCount;
    }

    abstract protected function executeIndividualStatement(DataSourceHandler $handler, $connection, $sql);
}
