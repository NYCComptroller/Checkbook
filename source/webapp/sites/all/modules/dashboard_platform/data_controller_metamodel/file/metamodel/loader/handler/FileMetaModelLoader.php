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




class FileMetaModelLoader extends AbstractFileMetaModelLoader {

    public function load(AbstractMetaModelFactory $factory, AbstractMetaModel $metamodel, array $filters = NULL, $finalAttempt) {
        LogHelper::log_info(t('Loading Meta Model from configuration files ...'));

        return parent::load($factory, $metamodel, $filters, $finalAttempt);
    }

    protected function getMetaModelFolderName() {
        return 'metamodel';
    }

    protected function merge(AbstractMetaModel $metamodel, array $filters = NULL, $namespace, __AbstractFileMetaModelLoader_Source $source) {
        $this->mergeWithDatasets($metamodel, $filters, $namespace, $source);
        $this->mergeWithReferences($metamodel, $filters, $namespace, $source);
        $this->mergeWithCubes($metamodel, $filters, $namespace, $source);
    }

    protected function mergeWithDatasets(AbstractMetaModel $metamodel, array $filters = NULL, $namespace, __AbstractFileMetaModelLoader_Source $source) {
        if (!isset($source->content->datasets)) {
            return;
        }

        $loaderName = $this->getName();

        foreach ($source->content->datasets as $sourceDatasetName => $sourceDataset) {
            $dataset = $this->mergeWithDataset($metamodel, $filters, $namespace, $sourceDatasetName, $sourceDataset);

            // adding system properties
            if (isset($dataset)) {
                $dataset->loader = $loaderName;
                $dataset->loadedFromFile = $source->filename;
                $dataset->version = $source->datetime;
            }
        }
    }

    protected function mergeWithDataset(AbstractMetaModel $metamodel, array $filters = NULL, $namespace, $sourceDatasetName, $sourceDataset) {
        $datasetName = NameSpaceHelper::resolveNameSpace($namespace, $sourceDatasetName);

        // dataset/datasource/name
        if (!isset($sourceDataset->datasourceName)) {
            throw new IllegalStateException(t(
                "'@datasetName' dataset definition does not contain a reference to datasource",
                array('@datasetName' => (isset($sourceDataset->publicName) ? $sourceDataset->publicName : $datasetName))));
        }
        $sourceDataset->datasourceName = NameSpaceHelper::resolveNameSpace($namespace, $sourceDataset->datasourceName);

        // dataset/cache/datasource/name
        if (isset($sourceDataset->cache->datasourceName)) {
            $sourceDataset->cache->datasourceName = NameSpaceHelper::resolveNameSpace($namespace, $sourceDataset->cache->datasourceName);
        }

        $dataset = new DatasetMetaData();
        $dataset->name = $datasetName;
        $dataset->initializeFrom($sourceDataset);

        $isDatasetAcceptable = $this->isMetaDataAcceptable($dataset, $filters);

        if ($isDatasetAcceptable) {
            $metamodel->registerDataset($dataset);
        }

        return $isDatasetAcceptable ? $dataset : NULL;
    }

    protected function mergeWithReferences(AbstractMetaModel $metamodel, array $filters = NULL, $namespace, __AbstractFileMetaModelLoader_Source $source) {
        if (!isset($source->content->references)) {
            return;
        }

        $loaderName = $this->getName();

        foreach ($source->content->references as $sourceReferenceName => $sourceReference) {
            $reference = $this->mergeWithReference($metamodel, $filters, $namespace, $sourceReferenceName, $sourceReference);

            // adding system properties
            $reference->loader = $loaderName;
            $reference->loadedFromFile = $source->filename;
        }
    }

    protected function mergeWithReference(AbstractMetaModel $metamodel, array $filters = NULL, $namespace, $sourceReferenceName, $sourceReference) {
        $referenceName = NameSpaceHelper::resolveNameSpace($namespace, $sourceReferenceName);

        $reference = $metamodel->findReference($referenceName);
        if (isset($reference)) {
            $metamodel->unregisterReference($referenceName);
        }
        else {
            $reference = new DatasetReference();
            $reference->name = $referenceName;
        }

        // reference[]/dataset/name
        foreach ($sourceReference as $pointIndex => $sourcePoint) {
            if (!isset($sourcePoint->datasetName)) {
                throw new IllegalStateException(t(
                    "'@referenceName' reference point definition (index: @pointIndex) does not contain a reference to dataset",
                    array('@referenceName' => $referenceName, '@pointIndex' => $pointIndex)));
            }

            $datasetName = NameSpaceHelper::resolveNameSpace($namespace, $sourcePoint->datasetName);

            $referencePoint = $reference->initiatePoint();
            if (isset($sourcePoint->columnNames)) {
                foreach ($sourcePoint->columnNames as $columnName) {
                    $referencePointColumn = $referencePoint->initiateColumn();
                    $referencePointColumn->datasetName = $datasetName;
                    $referencePointColumn->columnName = $columnName;
                    $referencePoint->registerColumnInstance($referencePointColumn);
                }
            }
            else {
                $referencePointColumn = $referencePoint->initiateColumn();
                $referencePointColumn->datasetName = $datasetName;
                $referencePoint->registerColumnInstance($referencePointColumn);
            }

            $reference->registerPointInstance($referencePoint);
        }

        $metamodel->registerReference($reference);

        return $reference;
    }

    protected function mergeWithCubes(AbstractMetaModel $metamodel, array $filters = NULL, $namespace, __AbstractFileMetaModelLoader_Source $source) {
        if (!isset($source->content->cubes)) {
            return;
        }

        $loaderName = $this->getName();

        foreach ($source->content->cubes as $sourceCubeName => $sourceCube) {
            $cube = $this->mergeWithCube($metamodel, $filters, $namespace, $sourceCubeName, $sourceCube);

            // adding system properties
            $cube->loader = $loaderName;
            $cube->loadedFromFile = $source->filename;
        }
    }

    protected function mergeWithCube(AbstractMetaModel $metamodel, array $filters = NULL, $namespace, $sourceCubeName, $sourceCube) {
        $cubeName = NameSpaceHelper::resolveNameSpace($namespace, $sourceCubeName);

        // cube/sourceDataset/Name
        if (!isset($sourceCube->sourceDatasetName)) {
            throw new IllegalStateException(t(
                "'@cubeName' cube definition does not contain a reference to source dataset",
                array('@cubeName' => (isset($sourceCube->publicName) ? $sourceCube->publicName : $cubeName))));
        }
        $sourceCube->sourceDatasetName = NameSpaceHelper::resolveNameSpace($namespace, $sourceCube->sourceDatasetName);

        // fix dimensions
        if (isset($sourceCube->dimensions)) {
            foreach ($sourceCube->dimensions as $dimension) {
                if (isset($dimension->levels)) {
                    foreach ($dimension->levels as $level) {
                        // cube/dimension/level/dataset/name
                        if (!isset($level->datasetName)) {
                            continue;
                        }
                        $level->datasetName = NameSpaceHelper::resolveNameSpace($namespace, $level->datasetName);
                    }
                }
            }
        }

        // cube/region/dataset/name
        if (isset($sourceCube->regions)) {
            foreach ($sourceCube->regions as $regionName => $region) {
                if (!isset($region->datasetName)) {
                    throw new IllegalStateException(t(
                        "'@regionName' region of '@cubeName' cube does not contain a reference to dataset",
                        array('@cubeName' => (isset($sourceCube->publicName) ? $sourceCube->publicName : $cubeName), '@regionName' => $regionName)));
                }
                $region->datasetName = NameSpaceHelper::resolveNameSpace($namespace, $region->datasetName);
            }
        }

        $cube = new CubeMetaData();
        $cube->name = $cubeName;
        $cube->initializeFrom($sourceCube);

        $metamodel->registerCube($cube);

        return $cube;
    }
}
