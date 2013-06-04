<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class OracleDropDatabaseStatementImpl extends AbstractDropDatabaseStatementImpl {

    public function generate(DataSourceHandler $handler, DataSourceMetaData $datasource) {
        // do NOT use 'CASCADE' option. It is not safe. We should not have any tables in the schema if we want to drop it
        return 'DROP USER ' . $this->databaseName;
    }
}
