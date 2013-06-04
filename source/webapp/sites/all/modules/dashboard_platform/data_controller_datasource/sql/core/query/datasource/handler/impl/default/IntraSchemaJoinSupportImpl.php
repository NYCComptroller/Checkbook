<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class IntraSchemaJoinSupportImpl extends AbstractCheckJoinSupportImpl {

    public function check(DataSourceMetaData $datasourceA, DataSourceMetaData $datasourceB) {
        list($isDataSourceCompatible, $isTypeCompatible, $isHostCompatible, $isDatabaseCompatible) =
            parent::check($datasourceA, $datasourceB);

        return array(
            $isDataSourceCompatible,
            $isTypeCompatible,
            $isHostCompatible,
            // if data sources and corresponding handlers are compatible and host is the same we can join tables (even from different databases)
            $isDataSourceCompatible && $isTypeCompatible && $isHostCompatible);
    }
}
