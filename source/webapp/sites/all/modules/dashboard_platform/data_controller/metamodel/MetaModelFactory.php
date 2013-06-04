<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
