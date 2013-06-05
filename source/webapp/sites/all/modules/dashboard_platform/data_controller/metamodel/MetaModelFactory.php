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




class MetaModelFactory extends AbstractMetaModelFactory {

    private static $factory = NULL;

    /**
     * @static
     * @return MetaModelFactory
     */
    public static function getInstance() {
        if (!isset(self::$factory)) {
            self::$factory = new MetaModelFactory();
        }

        return self::$factory;
    }

    protected function getMetaModelPublicNamePrefix() {
        return NULL;
    }

    protected function getMetaModelHookName() {
        return 'dc_metamodel_loader';
    }

    protected function initiateMetaModel() {
        return new MetaModel();
    }

    protected function loadCachedMetaModel() {
        $metamodel = parent::loadCachedMetaModel();
        if (isset($metamodel)) {
            $this->restoringDatasetReferences($metamodel);
        }

        return $metamodel;
    }

    protected function restoringDatasetReferences(MetaModel $metamodel) {
        // restoring references to ...
        foreach ($metamodel->cubes as $cube) {
            // ... cube source dataset
            $cube->sourceDataset = $metamodel->getDataset($cube->sourceDatasetName);

            if (isset($cube->dimensions)) {
                foreach ($cube->dimensions as $dimension) {
                    foreach ($dimension->levels as $level) {
                        // ... level datasets
                        if (isset($level->datasetName)) {
                            $level->dataset = $metamodel->getDataset($level->datasetName);
                        }
                    }
                }
            }
        }
    }

    protected function cacheMetaModel(AbstractMetaModel $metamodel = NULL) {
        if (isset($metamodel)) {
            // we do not want to affect passed object
            $metamodel = clone $metamodel;

            $this->removeTemporaryCubes($metamodel);
            $this->removeTemporaryDatasets($metamodel);
            $this->removeDatasetReferences($metamodel);
        }

        parent::cacheMetaModel($metamodel);
    }

    protected function removeTemporaryCubes(MetaModel $metamodel) {
        foreach ($metamodel->cubes as $cube) {
            if ($cube->temporary) {
                $metamodel->unregisterCube($cube->name);
            }
        }
    }

    protected function removeTemporaryDatasets(MetaModel $metamodel) {
        foreach ($metamodel->datasets as $dataset) {
            if ($dataset->temporary) {
                $metamodel->unregisterDataset($dataset->name);
            }
        }
    }

    protected function removeDatasetReferences(MetaModel $metamodel) {
        // removing references to ...
        foreach ($metamodel->cubes as $cube) {
            // ... cube source dataset
            unset($cube->sourceDataset);

            if (isset($cube->dimensions)) {
                foreach ($cube->dimensions as $dimension) {
                    foreach ($dimension->levels as $level) {
                        // ... level datasets
                        unset($level->dataset);
                    }
                }
            }
        }
    }
}
