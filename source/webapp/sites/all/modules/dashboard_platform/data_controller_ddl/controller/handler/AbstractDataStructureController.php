<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractDataStructureController extends AbstractDataController implements DataStructureController {

    /**
     * @param string $datasetName
     * @return DataSourceStructureHandler
     */
    protected function getDataSourceStructureHandlerByDatasetName($datasetName) {
        return $this->getDataSourceHandlerByDatasetName($datasetName);
    }

    /**
     * @param DatasetMetaData $dataset
     * @return DataSourceStructureHandler
     */
    protected function getDataSourceStructureHandlerByDataset(DatasetMetaData $dataset) {
        return $this->getDataSourceHandlerByDataset($dataset);
    }

    /**
     * @param string $datasourceName
     * @return DataSourceStructureHandler
     */
    protected function getDataSourceStructureHandler($datasourceName) {
        return $this->getDataSourceHandler($datasourceName);
    }

    protected function lookupDataSourceHandler($type) {
        return DataSourceStructureFactory::getInstance()->getHandler($type);
    }
}
