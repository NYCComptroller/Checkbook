<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DefaultTruncateTableStatementImpl extends AbstractTruncateTableStatementImpl {

    public function generate(DataSourceHandler $handler, DatasetMetaData $dataset) {
        // NOTE: we cannot use TRUNCATE TABLE because this operation has to be part of a transaction
        // Also truncate does to work in some databases when there are FOREIGN KEYs pointing to this table
        return 'DELETE FROM ' . $dataset->source;
    }
}
