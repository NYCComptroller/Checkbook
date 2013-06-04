<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




interface DatasetSourceAssembler {

    // if $columnNames == NULL it means that receiving code needs ALL available columns
    // if count($columnNames) == 0 it means that receiving code does not need support for columns
    function assemble(AbstractSQLDataSourceQueryHandler $datasourceHandler, DataControllerCallContext $callcontext, DatasetMetaData $dataset, array $columnNames = NULL);
}
