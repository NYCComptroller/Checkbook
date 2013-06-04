<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DrupalDatabaseEnvironmentMetaModelGenerator extends AbstractMetaModelLoader {

    public static $DATASOURCE_NAME__DEFAULT = 'default:default';

    public function load(AbstractMetaModelFactory $factory, AbstractMetaModel $environment_metamodel, array $filters = NULL, $finalAttempt) {
        LogHelper::log_notice(t('Generating Environment Meta Model for Drupal database connections ...'));

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
