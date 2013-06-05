<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
* 
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
