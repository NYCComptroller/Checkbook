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


class DrupalDatabaseEnvironmentMetaModelGenerator extends AbstractMetaModelLoader {

    public static $DATASOURCE_NAME__DEFAULT = 'default:default';

    public function load(AbstractMetaModelFactory $factory, AbstractMetaModel $environment_metamodel, array $filters = NULL, $finalAttempt) {
        LogHelper::log_info(t('Generating Environment Meta Model for Drupal database connections ...'));

        global $databases;

        $datasourceCount = 0;
        foreach ($databases as $namespace => $connections) {
            foreach ($connections as $datasourceNameOnly => $connection) {
                $datasource = new DataSourceMetaData();
                $datasource->name = NameSpaceHelper::addNameSpace($namespace, $datasourceNameOnly);
                $datasource->system = TRUE;
                $datasource->readonly = FALSE;
                // setting required properties
                $this->setDataSourceProperty($datasource, $connection, 'type', 'driver');
                // setting other provided properties
                $this->setDataSourceExtensionProperties($datasource, $connection);

                // registering the data source
                $environment_metamodel->registerDataSource($datasource);
                $datasourceCount++;
            }
        }

        // Default database connection is shared because we store common utilities and dimensions there
        $defaultDataSource = $environment_metamodel->getDataSource(self::$DATASOURCE_NAME__DEFAULT);
        $defaultDataSource->shared = TRUE;

        LogHelper::log_info(t('Generated @datasourceCount data sources', array('@datasourceCount' => $datasourceCount)));

        return self::LOAD_STATE__SUCCESSFUL;
    }

    protected function setDataSourceProperty(DataSourceMetaData $datasource, array &$connection, $datasourcePropertyName, $connectionPropertyName) {
        if (!isset($connection[$connectionPropertyName])) {
            return;
        }

        $datasource->$datasourcePropertyName = $connection[$connectionPropertyName];
        unset($connection[$connectionPropertyName]);
    }

    protected function setDataSourceExtensionProperties(DataSourceMetaData $datasource, array $connection) {
        foreach ($connection as $propertyName => $propertyValue) {
            $datasource->$propertyName = $propertyValue;
        }
    }
}
