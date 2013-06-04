<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class OCIInitializeConnectionImpl extends AbstractInitializeConnectionImpl {

    public function initialize(DataSourceHandler $handler, DataSourceMetaData $datasource) {
        if (!isset($datasource->database)) {
            throw new IllegalStateException(t('Entry name from tnsnames.ora is not provided'));
        }

        $connection = OCIImplHelper::oci_connect($datasource->username, $datasource->password, $datasource->database);

        $sql = array(
            'ALTER SESSION SET NLS_SORT=ASCII7_AI',
            'ALTER SESSION SET NLS_COMP=LINGUISTIC');

        $statementExecutor = new OCIExecuteStatementImpl();
        $statementExecutor->execute($handler, $connection, $sql);

        return $connection;
    }
}
