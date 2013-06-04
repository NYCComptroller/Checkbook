<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractInitializePDOConnectionImpl extends AbstractInitializeConnectionImpl {

    public function initialize(DataSourceHandler $handler, DataSourceMetaData $datasource) {
        $connection = $this->initializePDOConnection($handler, $datasource);
        // we want to see an exception in case of a database error
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $connection;
    }

    abstract protected function initializePDOConnection(DataSourceHandler $handler, DataSourceMetaData $datasource);
}
