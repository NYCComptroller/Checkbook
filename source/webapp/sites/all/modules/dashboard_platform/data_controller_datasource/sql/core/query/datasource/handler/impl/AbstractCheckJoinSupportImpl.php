<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractCheckJoinSupportImpl extends AbstractObject {

    public function check(DataSourceMetaData $datasourceA, DataSourceMetaData $datasourceB) {
        // if the two objects point to the same configuration
        $isDataSourceCompatible = $datasourceA->name == $datasourceB->name;
        $isTypeCompatible = $isHostCompatible = $isDatabaseCompatible = $isDataSourceCompatible;

        if (!$isDataSourceCompatible) {
            $datasourceHandlerA = DataSourceQueryFactory::getInstance()->getHandler($datasourceA->type);
            $datasourceHandlerB = DataSourceQueryFactory::getInstance()->getHandler($datasourceB->type);

            // checking if handler type is the same
            $isTypeCompatible = $datasourceA->type == $datasourceB->type;
            // checking if handler type is different but database type is the same
            if (!$isTypeCompatible) {
                $isTypeCompatible = $datasourceHandlerA->getDataSourceType() == $datasourceHandlerB->getDataSourceType();
            }

            if ($isTypeCompatible) {
                $isDataSourceCompatible = $isTypeCompatible;

                // if host name is present for both data sources we compare them. Otherwise just ignoring
                $isHostCompatible = (isset($datasourceA->host) && isset($datasourceB->host))
                    ? ($datasourceA->host == $datasourceB->host)
                    : TRUE;
                // if port number is present for both data sources we compare them. Otherwise just ignoring
                if ($isHostCompatible && isset($datasourceA->port) && isset($datasourceB->port)) {
                    $isHostCompatible = $datasourceA->port == $datasourceB->port;
                }

                if ($isHostCompatible) {
                    // if database name is present for both data sources we compare them. Otherwise just ignoring
                    $isDatabaseCompatible = (isset($datasourceA->database) && isset($datasourceB->database))
                        ? ($datasourceA->database == $datasourceB->database)
                        : TRUE;
                }
            }
        }

        return array($isDataSourceCompatible, $isTypeCompatible, $isHostCompatible, $isDatabaseCompatible);
    }
}
