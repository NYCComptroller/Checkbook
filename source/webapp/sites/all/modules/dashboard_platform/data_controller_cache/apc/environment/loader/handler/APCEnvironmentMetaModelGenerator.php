<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class APCEnvironmentMetaModelGenerator extends AbstractMetaModelLoader {

    public function load(AbstractMetaModelFactory $factory, AbstractMetaModel $environment_metamodel, array $filters = NULL, $finalAttempt) {
        LogHelper::log_notice(t('Generating Environment Meta Model for APC cache ...'));

        $datasourceCount = 0;

        $datasource = new DataSourceMetaData();
        $datasource->name = NameSpaceHelper::addNameSpace(APCHandler::$CACHE__TYPE, DefaultCacheFactory::$DATASOURCE_NAME__DEFAULT);
        $datasource->type = APCHandler::$CACHE__TYPE;
        $environment_metamodel->registerDataSource($datasource);
        $datasourceCount++;

        LogHelper::log_info(t('Generated @datasourceCount data sources', array('@datasourceCount' => $datasourceCount)));

        return self::LOAD_STATE__SUCCESSFUL;
    }
}
