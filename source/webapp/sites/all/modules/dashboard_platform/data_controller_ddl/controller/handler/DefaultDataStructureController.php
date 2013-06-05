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


class DefaultDataStructureController extends AbstractDataStructureController {

    protected function checkDatasetStructurePermission($datasetName) {
        $metamodel = data_controller_get_metamodel();

        $dataset = $metamodel->getDataset($datasetName);

        $this->checkDataSourceStructurePermission($dataset->datasourceName);
    }

    protected function checkDataSourceStructurePermission($datasourceName) {
        $environment_metamodel = data_controller_get_environment_metamodel();

        $datasource = $environment_metamodel->getDataSource($datasourceName);

        if ($datasource->isReadOnly()) {
            throw new IllegalStateException(t(
                'Structure manipulation is not permitted for the data source: @datasourceName',
                array('@datasourceName' => $datasource->publicName)));
        }
    }

    public function createDatabase($datasourceName, array $options = NULL) {
        $datasourceName = StringHelper::trim($datasourceName);

        $callcontext = $this->prepareCallContext();

        $request = new CreateDatabaseRequest($datasourceName, $options);

        LogHelper::log_debug($request);

        return $this->getDataSourceStructureHandler($datasourceName)->createDatabase($callcontext, $request);
    }

    public function dropDatabase($datasourceName) {
        $datasourceName = StringHelper::trim($datasourceName);

        $this->checkDataSourceStructurePermission($datasourceName);

        $callcontext = $this->prepareCallContext();

        $request = new DropDatabaseRequest($datasourceName);

        LogHelper::log_debug($request);

        return $this->getDataSourceStructureHandler($datasourceName)->dropDatabase($callcontext, $request);
    }

    public function createDatasetStorage($datasetName) {
        $datasetName = StringHelper::trim($datasetName);

        $this->checkDatasetStructurePermission($datasetName);

        $callcontext = $this->prepareCallContext();

        $request = new CreateDatasetStorageRequest($datasetName);

        LogHelper::log_debug($request);

        $this->getDataSourceStructureHandlerByDatasetName($datasetName)->createDatasetStorage($callcontext, $request);
    }

    public function truncateDatasetStorage($datasetName) {
        $datasetName = StringHelper::trim($datasetName);

        $this->checkDatasetStructurePermission($datasetName);

        $callcontext = $this->prepareCallContext();

        $request = new TruncateDatasetStorageRequest($datasetName);

        LogHelper::log_debug($request);

        $this->getDataSourceStructureHandlerByDatasetName($datasetName)->truncateDatasetStorage($callcontext, $request);
    }

    public function dropDatasetStorage($datasetName) {
        $datasetName = StringHelper::trim($datasetName);

        $this->checkDatasetStructurePermission($datasetName);

        $callcontext = $this->prepareCallContext();

        $request = new DropDatasetStorageRequest($datasetName);

        LogHelper::log_debug($request);

        $this->getDataSourceStructureHandlerByDatasetName($datasetName)->dropDatasetStorage($callcontext, $request);
    }

    protected function prepareCubeStorageDatasetNames(CubeMetaData $cube, $returnFactsDatasetFirst) {
        $datasetNames = array();

        $metamodel = data_controller_get_metamodel();

        $sourceDataset = $metamodel->getDataset($cube->sourceDatasetName);

        // adding level datasets
        if (isset($cube->dimensions)) {
            foreach ($cube->dimensions as $dimension) {
                foreach ($dimension->levels as $level) {
                    if (!isset($level->sourceColumnName)) {
                        continue;
                    }

                    $sourceColumn = $sourceDataset->getColumn($level->sourceColumnName);
                    if (isset($sourceColumn->type->sourceApplicationType)) {
                        list($referencedDatasetName) = ReferencePathHelper::splitReference($sourceColumn->type->sourceApplicationType);
                        if (isset($referencedDatasetName)) {
                            continue;
                        }
                    }

                    if (!isset($level->datasetName)) {
                        continue;
                    }

                    $levelDataset = $metamodel->getDataset($level->datasetName);
                    if (isset($levelDataset->storageType) && ($levelDataset->storageType == DatasetMetaData::$STORAGE_TYPE__DATA_CONTROLLER_MANAGED)) {
                        ArrayHelper::addUniqueValue($datasetNames, $level->datasetName);
                    }
                }
            }
        }

        // adding cube source dataset
        if (isset($sourceDataset->storageType) && ($sourceDataset->storageType == DatasetMetaData::$STORAGE_TYPE__DATA_CONTROLLER_MANAGED)) {
            if ($returnFactsDatasetFirst) {
                array_unshift($datasetNames, $sourceDataset->name);
            }
            else {
                $datasetNames[] = $sourceDataset->name;
            }
        }

        return (count($datasetNames) == 0) ? NULL : $datasetNames;
    }

    public function createCubeStorage($cubeName) {
        $cubeName = StringHelper::trim($cubeName);

        $metamodel = data_controller_get_metamodel();

        $cube = $metamodel->getCube($cubeName);

        $datasetNames = $this->prepareCubeStorageDatasetNames($cube, FALSE);
        if (isset($datasetNames)) {
            foreach ($datasetNames as $datasetName) {
                $this->createDatasetStorage($datasetName);
            }
        }
    }

    public function truncateCubeStorage($cubeName) {
        $cubeName = StringHelper::trim($cubeName);

        $metamodel = data_controller_get_metamodel();

        $cube = $metamodel->getCube($cubeName);

        $datasetNames = $this->prepareCubeStorageDatasetNames($cube, TRUE);
        if (isset($datasetNames)) {
            foreach ($datasetNames as $datasetName) {
                $this->truncateDatasetStorage($datasetName);
            }
        }
    }

    public function dropCubeStorage($cubeName) {
        $cubeName = StringHelper::trim($cubeName);

        $metamodel = data_controller_get_metamodel();

        $cube = $metamodel->getCube($cubeName);

        $datasetNames = $this->prepareCubeStorageDatasetNames($cube, TRUE);
        if (isset($datasetNames)) {
            foreach ($datasetNames as $datasetName) {
                $this->dropDatasetStorage($datasetName);
            }
        }
    }
}
