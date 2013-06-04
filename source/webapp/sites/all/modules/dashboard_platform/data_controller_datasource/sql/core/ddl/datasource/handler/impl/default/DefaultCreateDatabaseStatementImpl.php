<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DefaultCreateDatabaseStatementImpl extends AbstractCreateDatabaseStatementImpl {

    public function generate(DataSourceHandler $handler, DataSourceMetaData $datasource, array $options = NULL) {
        return 'CREATE DATABASE ' . $datasource->database;
    }
}
