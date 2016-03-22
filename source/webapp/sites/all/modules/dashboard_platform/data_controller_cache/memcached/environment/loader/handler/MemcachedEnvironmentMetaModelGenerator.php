<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 3/22/16
 * Time: 4:15 PM
 */

class MemcachedEnvironmentMetaModelGenerator extends AbstractMetaModelLoader {

    public function load(AbstractMetaModelFactory $factory, AbstractMetaModel $environment_metamodel, array $filters = NULL, $finalAttempt) {
        LogHelper::log_notice(t('Generating Environment Meta Model for Memcached cache ...'));

        $datasourceCount = 0;

        $datasource = new DataSourceMetaData();
        $datasource->name = NameSpaceHelper::addNameSpace(MemcachedHandler::$CACHE__TYPE, DefaultCacheFactory::$DATASOURCE_NAME__DEFAULT);
        $datasource->type = MemcachedHandler::$CACHE__TYPE;
        $environment_metamodel->registerDataSource($datasource);
        $datasourceCount++;

        LogHelper::log_info(t('Generated @datasourceCount data sources', array('@datasourceCount' => $datasourceCount)));

        return self::LOAD_STATE__SUCCESSFUL;
    }
}
