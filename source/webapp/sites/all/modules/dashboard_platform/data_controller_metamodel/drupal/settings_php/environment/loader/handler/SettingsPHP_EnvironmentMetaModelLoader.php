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


class SettingsPHP_EnvironmentMetaModelLoader extends AbstractMetaModelLoader {

    public function load(AbstractMetaModelFactory $factory, AbstractMetaModel $environment_metamodel, array $filters = NULL, $finalAttempt) {
        LogHelper::log_notice(t('Loading Environment Meta Model from settings.php ...'));

        $datasourceCount = 0;

        $configurationDataSources = Environment::getInstance()->getConfigurationSection('Data Sources');
        if (isset($configurationDataSources)) {
            foreach ($configurationDataSources as $namespace => $sourceDataSources) {
                foreach ($sourceDataSources as $datasourceName => $sourceDataSource) {
                    $datasourceName = NameSpaceHelper::resolveNameSpace($namespace, $datasourceName);

                    $datasource = new DataSourceMetaData();
                    $datasource->name = $datasourceName;
                    $datasource->system = TRUE;
                    $datasource->readonly = TRUE;
                    $datasource->initializeFrom($sourceDataSource);

                    $environment_metamodel->registerDataSource($datasource);

                    $datasourceCount++;
                }
            }
        }

        LogHelper::log_info(t('Processed @datasourceCount data sources', array('@datasourceCount' => $datasourceCount)));

        return self::LOAD_STATE__SUCCESSFUL;
    }
}
