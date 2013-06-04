<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class NoStartTransactionStatementImpl extends AbstractStartTransactionStatementImpl {

    public function generate(DataSourceHandler $handler, DataSourceMetaData $datasource) {
        // there is no statement to start transaction in this database
        return NULL;
    }
}
