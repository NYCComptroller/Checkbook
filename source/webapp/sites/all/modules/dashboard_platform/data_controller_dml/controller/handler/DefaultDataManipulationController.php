<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DefaultDataManipulationController extends AbstractDataManipulationController {

    protected function checkDatasetManipulationPermission($datasetName) {
        $environment_metamodel = data_controller_get_environment_metamodel();
        $metamodel = data_controller_get_metamodel();

        $dataset = $metamodel->getDataset($datasetName);
        $datasource = $environment_metamodel->getDataSource($dataset->datasourceName);

        if ($datasource->isReadOnly()) {
            throw new IllegalStateException(t(
                'Data manipulation is not permitted for the data source: @datasourceName',
                array('@datasourceName' => $datasource->publicName)));
        }
    }

    public function insertDatasetRecords($datasetName, AssociativeRecordsHolder $recordsHolder) {
        $datasetName = StringHelper::trim($datasetName);

        $this->checkDatasetManipulationPermission($datasetName);

        $callcontext = $this->prepareCallContext();

        $request = new DatasetInsertRequest($datasetName, $recordsHolder);

        LogHelper::log_debug($request);

        return $this->getDataSourceManipulationHandlerByDatasetName($datasetName)->insertDatasetRecords($callcontext, $request);
    }

    public function updateDatasetRecords($datasetName, AssociativeRecordsHolder $recordsHolder) {
        $datasetName = StringHelper::trim($datasetName);

        $this->checkDatasetManipulationPermission($datasetName);

        $callcontext = $this->prepareCallContext();

        $request = new DatasetUpdateRequest($datasetName, $recordsHolder);

        LogHelper::log_debug($request);

        return $this->getDataSourceManipulationHandlerByDatasetName($datasetName)->updateDatasetRecords($callcontext, $request);
    }

    public function insertOrUpdateOrDeleteDatasetRecords($datasetName, AssociativeRecordsHolder $recordsHolder) {
        $datasetName = StringHelper::trim($datasetName);

        $this->checkDatasetManipulationPermission($datasetName);

        $callcontext = $this->prepareCallContext();

        $request = new DatasetUpdateRequest($datasetName, $recordsHolder);

        LogHelper::log_debug($request);

        return $this->getDataSourceManipulationHandlerByDatasetName($datasetName)->insertOrUpdateOrDeleteDatasetRecords($callcontext, $request);
    }

    public function deleteDatasetRecords($datasetName, AssociativeRecordsHolder $recordsHolder) {
        $datasetName = StringHelper::trim($datasetName);

        $this->checkDatasetManipulationPermission($datasetName);

        $callcontext = $this->prepareCallContext();

        $request = new DatasetDeleteRequest($datasetName, $recordsHolder);

        LogHelper::log_debug($request);

        return $this->getDataSourceManipulationHandlerByDatasetName($datasetName)->deleteDatasetRecords($callcontext, $request);
    }

    public function insertDatasetRecordBatch($datasetName, IndexedRecordsHolder $recordsHolder) {
        $datasetName = StringHelper::trim($datasetName);

        $this->checkDatasetManipulationPermission($datasetName);

        $callcontext = $this->prepareCallContext();

        $request = new DatasetInsertRequest($datasetName, $recordsHolder);

        LogHelper::log_debug($request);

        return $this->getDataSourceManipulationHandlerByDatasetName($datasetName)->insertDatasetRecords($callcontext, $request);
    }

    public function updateDatasetRecordBatch($datasetName, IndexedRecordsHolder $recordsHolder) {
        $datasetName = StringHelper::trim($datasetName);

        $this->checkDatasetManipulationPermission($datasetName);

        $callcontext = $this->prepareCallContext();

        $request = new DatasetUpdateRequest($datasetName, $recordsHolder);

        LogHelper::log_debug($request);

        return $this->getDataSourceManipulationHandlerByDatasetName($datasetName)->updateDatasetRecords($callcontext, $request);
    }

    public function insertOrUpdateOrDeleteDatasetRecordBatch($datasetName, IndexedRecordsHolder $recordsHolder) {
        $datasetName = StringHelper::trim($datasetName);

        $this->checkDatasetManipulationPermission($datasetName);

        $callcontext = $this->prepareCallContext();

        $request = new DatasetUpdateRequest($datasetName, $recordsHolder);

        LogHelper::log_debug($request);

        return $this->getDataSourceManipulationHandlerByDatasetName($datasetName)->insertOrUpdateOrDeleteDatasetRecords($callcontext, $request);
    }

    public function deleteDatasetRecordBatch($datasetName, IndexedRecordsHolder $recordsHolder) {
        $datasetName = StringHelper::trim($datasetName);

        $this->checkDatasetManipulationPermission($datasetName);

        $callcontext = $this->prepareCallContext();

        $request = new DatasetDeleteRequest($datasetName, $recordsHolder);

        LogHelper::log_debug($request);

        return $this->getDataSourceManipulationHandlerByDatasetName($datasetName)->deleteDatasetRecords($callcontext, $request);
    }
}
