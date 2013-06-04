<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractDataController extends AbstractObject implements DataController {

    /**
     * @return DataControllerCallContext
     */
    protected function prepareCallContext() {
        return new DataControllerCallContext();
    }

    /**
     * @param string $datasetName
     * @return DataSourceHandler
     */
    protected function getDataSourceHandlerByDatasetName($datasetName) {
        $metamodel = data_controller_get_metamodel();

        $dataset = $metamodel->getDataset($datasetName);

        return $this->getDataSourceHandlerByDataset($dataset);
    }

    /**
     * @param DatasetMetaData $dataset
     * @return DataSourceHandler
     */
    protected function getDataSourceHandlerByDataset(DatasetMetaData $dataset) {
        $environment_metamodel = data_controller_get_environment_metamodel();

        $datasource = $environment_metamodel->getDataSource($dataset->datasourceName);

        return $this->getDataSourceHandler($datasource->name);
    }

    /**
     * @param string $datasourceName
     * @return DataSourceHandler
     */
    protected function getDataSourceHandler($datasourceName) {
        $environment_metamodel = data_controller_get_environment_metamodel();

        $datasource = $environment_metamodel->getDataSource($datasourceName);

        return $this->lookupDataSourceHandler($datasource->type);
    }

    /**
     * @param string $type
     * @return DataSourceHandler
     */
    abstract protected function lookupDataSourceHandler($type);
}
