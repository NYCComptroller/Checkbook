<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
* 
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
