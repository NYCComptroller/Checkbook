<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class MySQLDataSourceOwnerImpl extends AbstractDataSourceOwnerImpl {

    public function prepare(DataSourceHandler $handler, DataSourceMetaData $datasource) {
        return $datasource->database;
    }
}
