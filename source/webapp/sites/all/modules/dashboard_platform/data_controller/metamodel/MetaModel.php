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




class MetaModel extends AbstractMetaModel {

    /**
     * @var DatasetMetaData[]
     */
    public $datasets = [];
    /**
     * @var DatasetReference[]
     */
    public $references = [];
    /**
     * @var CubeMetaData[]
     */
    public $cubes = [];

    public function __clone() {
        parent::__clone();

        $this->datasets = ArrayHelper::cloneArray($this->datasets);
        $this->references = ArrayHelper::cloneArray($this->references);
        $this->cubes = ArrayHelper::cloneArray($this->cubes);
    }

    protected function finalize() {
        parent::finalize();

        $this->finalizeDatasets($this->datasets);
        $this->finalizeReferences($this->references);
        $this->finalizeCubes($this->cubes);
    }

    protected function validate() {
        parent::validate();

        $this->validateDatasets($this->datasets);
        $this->validateReferences($this->references);
        $this->validateCubes($this->cubes);
    }

    // *****************************************************************************************************************************
    //   Dataset
    // *****************************************************************************************************************************
    public function findDataset($datasetName) {
        if (isset($this->datasets)) {
            if (isset($this->datasets[$datasetName])) {
                return $this->datasets[$datasetName];
            }

            // using alternative way to find the dataset -> by alias
            foreach ($this->datasets as $dataset) {
                if ($dataset->isAliasMatched($datasetName)) {
                    return $dataset;
                }
            }
        }

        return NULL;
    }

  /**
   * @param $datasetName
   * @return DatasetMetaData
   * @throws IllegalArgumentException
   */
    public function getDataset($datasetName) {
        $dataset = $this->findDataset($datasetName);
        if (!isset($dataset)) {
            $this->errorDatasetNotFound($datasetName);
        }

        return $dataset;
    }

    public function findDatasetsByNamespacelessName($datasetNamespacelessName) {
        $datasets = NULL;

        foreach ($this->datasets as $datasetName => $dataset) {
            list(, $datasetNameOnly) = NameSpaceHelper::splitAlias($datasetName);
            if ($datasetNameOnly == $datasetNamespacelessName) {
                $datasets[$datasetName] = $dataset;
            }
        }

        return $datasets;
    }

    protected function finalizeDatasets(array &$datasets) {
        foreach ($datasets as $dataset) {
            $dataset->finalize();
        }
    }

    protected function validateDataset(DatasetMetaData $dataset) {
        $environment_metamodel = data_controller_get_environment_metamodel();

        // dataset/datasourceName
        $datasourceName = isset($dataset->datasourceName) ? $dataset->datasourceName : NULL;
        if (!isset($datasourceName) || ($environment_metamodel->findDataSource($datasourceName) == NULL)) {
            LogHelper::log_error($environment_metamodel);
            LogHelper::log_error($dataset);
            throw new IllegalStateException(t(
                "DataSource '@datasourceName' for dataset '@datasetName' cannot be resolved",
                array('@datasourceName' => $datasourceName, '@datasetName' => $dataset->publicName)));
        }
    }

    protected function validateDatasets(array &$datasets) {
        foreach ($datasets as $dataset) {
            $this->validateDataset($dataset);
        }
    }

    public function registerDataset(DatasetMetaData $dataset) {
        $this->checkAssemblingStarted();

        if (!isset($dataset->name)) {
            LogHelper::log_error($dataset);
            throw new IllegalArgumentException(t('Dataset name has not been defined'));
        }
        $datasetName = $dataset->name;
        NameSpaceHelper::checkAlias($datasetName);

        if (isset($this->datasets[$datasetName])) {
            if ($dataset->temporary) {
                unset($this->datasets[$datasetName]);
            }
            else {
                throw new IllegalArgumentException(t(
                    'Dataset with name @datasetName has already been defined',
                    array('@datasetName' => $dataset->publicName)));
            }
        }

        // registering references to lookups based on column type
        foreach ($dataset->getColumns() as $column) {
            list($lookupDatasetName) = ReferencePathHelper::splitReference($column->type->applicationType);
            if (isset($lookupDatasetName)) {
                $referenceName = $lookupDatasetName;
                // FIXME it would be better if we provide the primary key (check findReferencesByDatasetName where we fix this issue)
                $this->registerSimpleReferencePoint($referenceName, $lookupDatasetName, /* Primary Key */ NULL);
                $this->registerSimpleReferencePoint($referenceName, $dataset->name, $column->name);
            }
        }

        $this->datasets[$datasetName] = $dataset;
    }

    public function unregisterDataset($datasetName) {
        $this->checkAssemblingStarted();

        if (!isset($this->datasets[$datasetName])) {
            $this->errorDatasetNotFound($datasetName);
        }

        $dataset = $this->datasets[$datasetName];

        // removing references to this dataset
        foreach ($this->references as $reference) {
            foreach ($reference->points as $referencePointIndex => $referencePoint) {
                $isThisDatasetFound = FALSE;
                $isOtherDatasetFound = FALSE;
                foreach ($referencePoint->columns as $referencePointColumn) {
                    if ($referencePointColumn->datasetName == $datasetName) {
                        $isThisDatasetFound = TRUE;
                    }
                    else {
                        $isOtherDatasetFound = TRUE;
                    }
                }
                if ($isThisDatasetFound) {
                    if ($isOtherDatasetFound) {
                        throw new IllegalStateException(t(
                            "'@datasetName' dataset cannot be unregistered. Meta model contains unremovable reference: @referenceName",
                            array('@datasetName' => $dataset->publicName, '@referenceName' => $reference->publicName)));
                    }
                    else {
                        unset($reference->points[$referencePointIndex]);
                    }
                }
            }
        }

        unset($this->datasets[$datasetName]);

        return $dataset;
    }

    protected function errorDatasetNotFound($datasetName) {
        throw new IllegalArgumentException(t("Could not find '@datasetName' dataset definition", array('@datasetName' => $datasetName)));
    }

    // *****************************************************************************************************************************
    //   Dataset Reference
    // *****************************************************************************************************************************
    /**
     * @param $referenceName
     * @return DatasetReference|null
     */
    public function findReference($referenceName) {
        return isset($this->references[$referenceName])
            ? $this->references[$referenceName]
            : NULL;
    }

  /**
   * @param $referenceName
   * @return DatasetReference
   * @throws IllegalArgumentException
   */
    public function getReference($referenceName) {
        $reference = $this->findReference($referenceName);
        if (!isset($reference)) {
            $this->errorReferenceNotFound($referenceName);
        }

        return $reference;
    }

  /**
   * @param $datasetName
   * @return DatasetReference[]|null
   * @throws IllegalArgumentException
   * @throws UnsupportedOperationException
   */
    public function findReferencesByDatasetName($datasetName) {
        $references = NULL;
        foreach ($this->references as $reference) {
            foreach ($reference->points as $referencePoint) {
                foreach ($referencePoint->columns as $referencePointColumn) {
                    if ($referencePointColumn->datasetName == $datasetName) {
                        $references[] = $reference;
                        continue 3;
                    }
                }
            }
        }
        if (!isset($references)) {
            return NULL;
        }

        $environment_metamodel = NULL;

        $dataset = $this->getDataset($datasetName);
        $datasourceName = $dataset->datasourceName;

        // eliminating references from different data sources
        $selectedReferences = NULL;
        foreach ($references as $reference) {
            $selectedReference = new DatasetReference();
            foreach ($reference->points as $referencePoint) {
                $isPointSelected = TRUE;
                foreach ($referencePoint->columns as $referencePointColumn) {
                    // FIXME fixing references (to resolve the issue we need to post process configuration)
                    if (!isset($referencePointColumn->columnName)) {
                        $referencePointColumn->columnName = $this->getDataset($referencePointColumn->datasetName)->getKeyColumn()->name;
                    }

                    if ($referencePointColumn->datasetName == $dataset->name) {
                        continue;
                    }

                    $columnDataset = $this->getDataset($referencePointColumn->datasetName);
                    // this dataset is shared across different data sources
                    if ($columnDataset->isShared()) {
                        continue;
                    }

                    $columnDataSourceName = $columnDataset->datasourceName;
                    // this dataset is from the same data source
                    if ($columnDataSourceName == $datasourceName) {
                        continue;
                    }

                    if (!isset($environment_metamodel)) {
                        $environment_metamodel = data_controller_get_environment_metamodel();
                    }
                    $datasource = $environment_metamodel->getDataSource($columnDataSourceName);
                    if (!$datasource->isShared()) {
                        $isPointSelected = FALSE;
                        break;
                    }
                }
                if ($isPointSelected) {
                    $selectedReference->points[] = $referencePoint;
                }
            }

            if ($selectedReference->getPointCount() > 1) {
                // preparing properties of selected reference
                $selectedReference->initializeInstanceFrom($reference);
                $selectedReferences[] = $selectedReference;
            }
        }

        return $selectedReferences;
    }

    protected function finalizeReferences(array &$references) {
        foreach ($references as $reference) {
            $reference->finalize();
        }
    }

    protected function validateReference(DatasetReference $reference) {
        // checking if dataset name is valid for all reference points
//        $pointCount = 0;
//        foreach ($reference->points as $referencePoint) {
//            // FIXME do not check for dataset name. Simplify code which needed to be changed to accommodate such check (Example: post processing of loaded configuration)
//            /*
//            foreach ($referencePoint->columns as $referencePointColumn) {
//                $dataset = $this->findDataset($referencePointColumn->datasetName);
//                if (!isset($dataset)) {
//                    LogHelper::log_error($this->datasets);
//                    throw new IllegalStateException(t(
//                        "Dataset '@datasetName' for '@referenceName' reference cannot be resolved",
//                        array('@datasetName' => $referencePointColumn->datasetName, '@referenceName' => $reference->publicName)));
//                }
//            }*/
//
//            $pointCount++;
//        }
    }

    protected function validateReferences(array &$references) {
        foreach ($references as $reference) {
            $this->validateReference($reference);
        }
    }

    public function registerReference(DatasetReference $reference) {
        $this->checkAssemblingStarted();

        if (!isset($reference->name)) {
            LogHelper::log_error($reference);
            throw new IllegalArgumentException(t('Reference name has not been defined'));
        }

        $referenceName = $reference->name;
        NameSpaceHelper::checkAlias($referenceName);

        if (isset($this->references[$referenceName])) {
            throw new IllegalArgumentException(t(
                'Reference with name @referenceName has already been defined',
                array('@referenceName' => $reference->publicName)));
        }

        $this->references[$referenceName] = $reference;
    }

    public function unregisterReference($referenceName) {
        $this->checkAssemblingStarted();

        if (!isset($this->references[$referenceName])) {
            $this->errorReferenceNotFound($referenceName);
        }

        $reference = $this->references[$referenceName];

        unset($this->references[$referenceName]);

        return $reference;
    }

    public function registerSimpleReferencePoint($referenceName, $datasetName, $columnName) {
        $reference = $this->findReference($referenceName);
        if (isset($reference)) {
            $this->unregisterReference($referenceName);
        }
        else {
            $reference = new DatasetReference();
            $reference->name = $referenceName;
        }

        $referencePoint = $reference->initiatePoint();
        $referencePointColumn = $referencePoint->initiateColumn();
        $referencePointColumn->datasetName = $datasetName;
        $referencePointColumn->columnName = $columnName;
        $referencePoint->registerColumnInstance($referencePointColumn);
        $reference->registerPointInstance($referencePoint);

        $this->registerReference($reference);
    }

    protected function errorReferenceNotFound($referenceName) {
        throw new IllegalArgumentException(t("Could not find '@referenceName' reference definition", array('@referenceName' => $referenceName)));
    }

    // *****************************************************************************************************************************
    //   Cube
    // *****************************************************************************************************************************
    public function findCube($cubeName) {
        return isset($this->cubes[$cubeName])
            ? $this->cubes[$cubeName]
            : NULL;
    }

  /**
   * @param $cubeName
   * @return CubeMetaData
   * @throws IllegalArgumentException
   */
    public function getCube($cubeName) {
        $cube = $this->findCube($cubeName);
        if (!isset($cube)) {
            $this->errorCubeNotFound($cubeName);
        }

        return $cube;
    }

    /**
     * @param $datasetName
     * @return CubeMetaData
     */
    public function findCubeByDatasetName($datasetName) {
        $dataset = $this->findDataset($datasetName);

        if (isset($dataset)) {
            foreach ($this->cubes as $cube) {
                if ($cube->sourceDatasetName == $dataset->name) {
                    return $cube;
                }
            }
        }

        return NULL;
    }

    /**
     * @throws IllegalArgumentException
     * @param $datasetName
     * @return CubeMetaData
     */
    public function getCubeByDatasetName($datasetName) {
        $cube = $this->findCubeByDatasetName($datasetName);
        if (!isset($cube)) {
            $dataset = $this->getDataset($datasetName);
            throw new IllegalArgumentException(t(
                'Could not find a cube for the dataset: @datasetName',
                array('@datasetName' => $dataset->publicName)));
        }

        return $cube;
    }

    protected function finalizeCubes(array &$cubes) {
        foreach ($cubes as $cube) {
            $cube->finalize();
        }
    }

    protected function validateCube(CubeMetaData $cube) {
        $cubeDatasetName = $cube->sourceDatasetName;
        if (!isset($this->datasets[$cubeDatasetName])) {
            LogHelper::log_error($this->datasets);
            throw new IllegalStateException(t(
            	"Source dataset '@datasetName' for cube '@cubeName' cannot be resolved",
                array('@datasetName' => $cubeDatasetName, '@cubeName' => $cube->publicName)));
        }

        if (isset($cube->dimensions)) {
            foreach ($cube->dimensions as $dimension) {
                foreach ($dimension->levels as $levelIndex => $level) {
                    // first level should have a reference to to source key
                    if (($levelIndex == 0) && !isset($level->sourceColumnName)) {
                        LogHelper::log_error($dimension);
                        throw new IllegalStateException(t(
                        	"First level (@levelName) in '@cubeName' cube in '@dimensionName' dimension should have a reference to source dataset ('sourceColumnName' attribute)",
                            array('@cubeName' => $cube->publicName, '@dimensionName' => $dimension->publicName, '@levelName' => $level->publicName)));

                    }

                    if (!isset($level->datasetName)) {
                        continue;
                    }

                    $datasetName = $level->datasetName;
                    $dataset = $this->findDataset($datasetName);
                    if (!isset($dataset)) {
                        LogHelper::log_error($this->datasets);
                        throw new IllegalStateException(t(
                        	"Dataset '@datasetName' for cube '@cubeName' dimension '@dimensionName' level '@levelName' cannot be resolved",
                            array('@datasetName' => $datasetName, '@cubeName' => $cube->publicName, '@dimensionName' => $dimension->publicName, '@levelName' => $level->publicName)));
                    }

                    // FIXME remove the following functionality
                    // setting key field for each level
                    if (!isset($level->key)) {
                        $keyColumn = $dataset->findKeyColumn();
                        if (isset($keyColumn)) {
                            $level->key = $keyColumn->name;
                        }
                        else {
                            LogHelper::log_debug($dataset);
                            LogHelper::log_error($dimension);
                            throw new IllegalStateException(t(
                                "Could not identify 'key' attribute to access '@datasetName' dataset records for '@levelName' level of '@dimensionName' dimension of '@cubeName' cube",
                                array('@datasetName' => $dataset->publicName, '@cubeName' => $cube->publicName, '@dimensionName' => $dimension->publicName, '@levelName' => $level->publicName)));
                        }
                    }
                }
            }
        }
    }

    protected function validateCubes(array &$cubes) {
        foreach ($cubes as $cube) {
            $this->validateCube($cube);
        }
    }

    public function registerCube(CubeMetaData $cube) {
        $this->checkAssemblingStarted();

        if (!isset($cube->name)) {
            LogHelper::log_error($cube);
            throw new IllegalArgumentException(t('Cube name has not been defined'));
        }

        $cubeName = $cube->name;
        NameSpaceHelper::checkAlias($cubeName);

        if (isset($this->cubes[$cubeName])) {
            if ($cube->temporary) {
                unset($this->cubes[$cubeName]);
            }
            else {
                throw new IllegalArgumentException(t(
                    'Cube with name @cubeName has already been defined',
                    array('@cubeName' => $cube->publicName)));
            }
        }

        if (!$cube->temporary) {
            // we support only one cube per dataset
            $cube2 = $this->findCubeByDatasetName($cube->sourceDatasetName);
            if (isset($cube2)) {
                throw new IllegalArgumentException(t(
                    "Found several cubes for '@datasetName' dataset: ['@cubeName1', '@cubeName2']",
                    array('@datasetName' => $cube->sourceDatasetName, '@cubeName1' => $cube->publicName, '@cubeName2' => $cube2->publicName)));
            }
        }

        $this->cubes[$cubeName] = $cube;
    }

    public function unregisterCube($cubeName) {
        $this->checkAssemblingStarted();

        if (!isset($this->cubes[$cubeName])) {
            $this->errorCubeNotFound($cubeName);
        }

        $cube = $this->cubes[$cubeName];

        unset($this->cubes[$cubeName]);

        return $cube;
    }

    protected function errorCubeNotFound($cubeName) {
        throw new IllegalArgumentException(t("Could not find '@cubeName' cube definition", array('@cubeName' => $cubeName)));
    }
}
