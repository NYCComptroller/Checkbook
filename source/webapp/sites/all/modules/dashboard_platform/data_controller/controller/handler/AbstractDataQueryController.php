<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractDataQueryController extends AbstractDataController implements DataQueryController {

    /**
     * @param string $cubeName
     * @return DataSourceQueryHandler
     */
    protected function getDataSourceQueryHandlerByCubeName($cubeName) {
        $metamodel = data_controller_get_metamodel();

        $cube = $metamodel->getCube($cubeName);

        return $this->getDataSourceQueryHandlerByCube($cube);
    }

    /**
     * @param CubeMetaData $cube
     * @return DataSourceQueryHandler
     */
    protected function getDataSourceQueryHandlerByCube(CubeMetaData $cube) {
        return $this->getDataSourceQueryHandlerByDatasetName($cube->sourceDatasetName);
    }

    /**
     * @param string $datasetName
     * @return DataSourceQueryHandler
     */
    protected function getDataSourceQueryHandlerByDatasetName($datasetName) {
        return $this->getDataSourceHandlerByDatasetName($datasetName);
    }

    /**
     * @param DatasetMetaData $dataset
     * @return DataSourceQueryHandler
     */
    protected function getDataSourceQueryHandlerByDataset(DatasetMetaData $dataset) {
        return $this->getDataSourceHandlerByDataset($dataset);
    }

    /**
     * @param string $datasourceName
     * @return DataSourceQueryHandler
     */
    protected function getDataSourceQueryHandler($datasourceName) {
        return $this->getDataSourceHandler($datasourceName);
    }

    protected function lookupDataSourceHandler($type) {
        return DataSourceQueryFactory::getInstance()->getHandler($type);
    }

    public function queryDataset($datasetName, $columns = NULL, $parameters = NULL, $orderBy = NULL, $startWith = 0, $limit = NULL, ResultFormatter $resultFormatter = NULL) {
        $request = new DataQueryControllerDatasetRequest();
        $request->initializeFrom($datasetName, $columns, $parameters, $orderBy, $startWith, $limit, $resultFormatter);

        return $this->query($request);
    }

    public function queryCube($datasetName, $columns, $parameters = NULL, $orderBy = NULL, $startWith = 0, $limit = NULL, ResultFormatter $resultFormatter = NULL) {
        $request = new DataQueryControllerCubeRequest();
        $request->initializeFrom($datasetName, $columns, $parameters, $orderBy, $startWith, $limit, $resultFormatter);

        return $this->query($request);
    }

    public function countDatasetRecords($datasetName, $parameters = NULL, ResultFormatter $resultFormatter = NULL) {
        $request = new DataQueryControllerDatasetRequest();
        $request->initializeFrom($datasetName, NULL, $parameters, NULL, 0, NULL, $resultFormatter);

        return $this->countRecords($request);
    }

    public function countCubeRecords($datasetName, $columns, $parameters = NULL, ResultFormatter $resultFormatter = NULL) {
        $request = new DataQueryControllerCubeRequest();
        // Note: even if we want we cannot remove $columns parameter for cube record number calculation.
        // That is because $columns are used to identify columns for aggregation
        $request->initializeFrom($datasetName, $columns, $parameters, NULL, 0, NULL, $resultFormatter);

        return $this->countRecords($request);
    }
}
