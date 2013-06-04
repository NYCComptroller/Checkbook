<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
