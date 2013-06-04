<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractDataManipulationController extends AbstractDataController implements DataManipulationController {

    /**
     * @param string $datasetName
     * @return DataSourceManipulationHandler
     */
    protected function getDataSourceManipulationHandlerByDatasetName($datasetName) {
        return $this->getDataSourceHandlerByDatasetName($datasetName);
    }

    /**
     * @param DatasetMetaData $dataset
     * @return DataSourceManipulationHandler
     */
    protected function getDataSourceManipulationHandlerByDataset(DatasetMetaData $dataset) {
        return $this->getDataSourceHandlerByDataset($dataset);
    }

    /**
     * @param string $datasourceName
     * @return DataSourceManipulationHandler
     */
    protected function getDataSourceManipulationHandler($datasourceName) {
        return $this->getDataSourceHandler($datasourceName);
    }

    protected function lookupDataSourceHandler($type) {
        return DataSourceManipulationFactory::getInstance()->getHandler($type);
    }
}
