<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractSystemTableMetaModelLoader extends AbstractMetaModelLoader {

    const PROPERTY__TABLE_NAME = 'table_name';
    const PROPERTY__COLUMN_NAME = 'column_name';
    const PROPERTY__COLUMN_INDEX = 'column_index';
    const PROPERTY__COLUMN_TYPE = 'column_type';

    abstract protected function selectedDataSourceType();

    abstract protected function prepareColumnsMetaDataProperties(DataSourceMetaData $datasource, array $tableNames);

    public function load(AbstractMetaModelFactory $factory, AbstractMetaModel $metamodel, array $filters = NULL, $finalAttempt) {
        $selectedDataSourceType = $this->selectedDataSourceType();

        LogHelper::log_notice(t("Finalizing Dataset Meta Data for '@databaseType' database connections ...", array('@databaseType' => $selectedDataSourceType)));

        if ($finalAttempt === FALSE) {
           return self::LOAD_STATE__POSTPONED;
        }

        $finalizedDatasetCount = 0;

        $environment_metamodel = data_controller_get_environment_metamodel();

        // processing all database connections
        foreach ($environment_metamodel->datasources as $datasource) {
            if ($datasource->type !== $selectedDataSourceType) {
                continue;
            }

            // selecting datasets which could be processed for the selected connection
            $selectedSources = NULL;
            foreach ($metamodel->datasets as $dataset) {
                // the dataset should belong to the selected data source
                if ($dataset->datasourceName !== $datasource->name) {
                    continue;
                }

                // the dataset has to be of type table
                if (DatasetTypeHelper::detectDatasetSourceType($dataset) !== DatasetTypeHelper::DATASET_SOURCE_TYPE__TABLE) {
                    continue;
                }

                // whole dataset meta data was prepared using different method. There is nothing else can be done
                if ($dataset->isComplete()) {
                    continue;
                }

                $tableName = strtolower($dataset->source);

                // invalidating existing column indexes
                $dataset->invalidateColumnIndexes();

                // there could be several datasets for one table
                $selectedSources[$tableName][] = $dataset;
            }
            if (!isset($selectedSources)) {
                continue;
            }

            $datasourceHandler = DataSourceQueryFactory::getInstance()->getHandler($datasource->type);
            $metadataCallback = $datasourceHandler->prepareQueryStatementExecutionCallbackInstance();

            // processing meta data for selected datasets
            $columnsMetaDataProperties = $this->prepareColumnsMetaDataProperties($datasource, array_keys($selectedSources));
            if (isset($columnsMetaDataProperties)) {
                foreach ($columnsMetaDataProperties as $columnMetaDataProperties) {
                    $tableName = strtolower($columnMetaDataProperties[self::PROPERTY__TABLE_NAME]);

                    $datasets = $selectedSources[$tableName];
                    foreach ($datasets as $dataset) {
                        $column = new ColumnMetaData();
                        $column->name = strtolower($columnMetaDataProperties[self::PROPERTY__COLUMN_NAME]);
                        $column->columnIndex = $columnMetaDataProperties[self::PROPERTY__COLUMN_INDEX];
                        // preparing column type
                        $column->type->databaseType = $columnMetaDataProperties[self::PROPERTY__COLUMN_TYPE];
                        $column->type->applicationType = $metadataCallback->calculateApplicationDataType($column);
                        // checking if the column is a system column which should be invisible
                        if (substr_compare($column->name, DatasetSystemColumnNames::COLUMN_NAME_PREFIX, 0, strlen(DatasetSystemColumnNames::COLUMN_NAME_PREFIX)) === 0) {
                            $column->visible = FALSE;
                        }

                        $dataset->initializeColumnFrom($column);
                    }
                }
            }

            // marking all selected datasets as completed
            foreach ($selectedSources as $datasets) {
                foreach ($datasets as $dataset) {
                    $dataset->markAsComplete();
                    $finalizedDatasetCount++;
                }
            }
        }

        LogHelper::log_info(t('Finalized @datasetCount dataset meta data configurations', array('@datasetCount' => $finalizedDatasetCount)));

        return self::LOAD_STATE__SUCCESSFUL;
    }
}
