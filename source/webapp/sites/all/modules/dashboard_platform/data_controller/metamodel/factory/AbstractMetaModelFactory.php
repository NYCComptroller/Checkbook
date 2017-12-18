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




abstract class AbstractMetaModelFactory extends AbstractFactory {

    private $globalModificationStarted = FALSE;

    /**
     * @var AbstractMetaModel
     */
    private $cachedMetaModel = NULL;

    private $loaders = NULL;

    private $adhocFilters = NULL;

    protected function __construct() {
        parent::__construct();
        $this->initiateLoaders();
    }

    // *****************************************************************************************************************************
    // * Meta Model Name
    // *****************************************************************************************************************************
    abstract protected function getMetaModelPublicNamePrefix();

    protected function getMetaModelName() {
        $prefix = $this->getMetaModelPublicNamePrefix();

        return (isset($prefix) ? "$prefix " : '') . 'Meta Model';
    }

    // *****************************************************************************************************************************
    // * Filters for Meta Model Loaders
    // *****************************************************************************************************************************
    protected function getMetaModelFilterHookName() {
        return $this->getMetaModelHookName() . '_filter';
    }

    protected function processMetaModelFilters(array &$processedFilters = NULL, array $filters = NULL) {
        if (!isset($filters)) {
            return;
        }

        foreach ($filters as $className => $properties) {
            foreach ($properties as $propertyName => $values) {
                $uniqueValues = isset($processedFilters[$className][$propertyName])
                    ? $processedFilters[$className][$propertyName]
                    : NULL;
                if ($uniqueValues === FALSE) {
                    // this property should be ignored
                }
                else {
                    foreach ($values as $value) {
                        if (isset($value)) {
                            ArrayHelper::addUniqueValue($uniqueValues, $value);
                        }
                        else {
                            // if there is at least one NULL value we ignore the property completely
                            $uniqueValues = FALSE;
                            break;
                        }
                    }
                }

                $processedFilters[$className][$propertyName] = $uniqueValues;
            }
        }
    }

    protected function getMetaModelFilters() {
        $hookName = $this->getMetaModelFilterHookName();

        $preparedFilters = NULL;

        // processing preset filters
        $this->processMetaModelFilters($preparedFilters, module_invoke_all($hookName));
        // processing ad hoc filters
        if (isset($this->adhocFilters)) {
            $this->processMetaModelFilters($preparedFilters, $this->adhocFilters);
        }

        if (!isset($preparedFilters)) {
            return NULL;
        }

        // removing all filters which should be ignored
        $filters = NULL;
        foreach ($preparedFilters as $className => $properties) {
            foreach ($properties as $propertyName => $values) {
                if ($values === FALSE) {
                    continue;
                }

                $filters[$className][$propertyName] = $values;
            }
        }

        return $filters;
    }

    public function registerAdHocMetaModelFilter($className, $propertyName, $propertyValue) {
        $this->adhocFilters[$className][$propertyName][] = $propertyValue;

        $this->cachedMetaModel = NULL;
    }

    // *****************************************************************************************************************************
    // * Meta Model Loaders
    // *****************************************************************************************************************************
    abstract protected function getMetaModelHookName();

    protected function initiateLoaders() {
        $hookName = $this->getMetaModelHookName();

        $loaderConfigurations = module_invoke_all($hookName);
        foreach ($loaderConfigurations as $loaderConfiguration) {
            $classname = $loaderConfiguration['classname'];

            $loader = new $classname();
            $this->registerLoader($loader);
        }
    }

    protected function registerLoader(MetaModelLoader $loader) {
        $this->loaders[] = $loader;
    }

    /**
     * @param $loaderName
     * @return MetaModelLoader
     */
    public function getLoader($loaderName) {
        if (isset($this->loaders)) {
            foreach ($this->loaders as $loader) {
                if ($loader->getName() === $loaderName) {
                    return $loader;
                }
            }
        }

        throw new IllegalArgumentException(t("Could not find '@loaderName' meta model loader", array('@loaderName' => $loaderName)));
    }

    // *****************************************************************************************************************************
    // * Loading Meta Model
    // *****************************************************************************************************************************
    abstract protected function initiateMetaModel();

    protected function loadMetaModel(AbstractMetaModel $metamodel) {
        $metaModelName = $this->getMetaModelName();

        LogHelper::log_info(t('Loading @metamodelName ...', array('@metamodelName' => $metaModelName)));

        $metamodelTimeStart = microtime(TRUE);
        $metamodelMemoryUsage = memory_get_usage();

        if (isset($this->loaders)) {
            // preparing each loader for load operation
            foreach ($this->loaders as $loader) {
                $loader->prepare($this, $metamodel);
            }

            $filters = $this->getMetaModelFilters();

            // creating a copy of list of loaders. A loader is removed from the list once corresponding load operation is completed
            $loaders = $this->loaders;

            $finalAttempt = FALSE;

            $index = $postponedLoaderCounter = 0;
            while (($count = count($loaders)) > 0) {
                if ($index >= $count) {
                    if ($postponedLoaderCounter >= $count) {
                        if ($finalAttempt) {
                            // ALL loaders were postponed. There is no data which they depend on
                            break;
                        }
                        else {
                            $finalAttempt = TRUE;
                        }
                    }

                    // resetting indexes to start from first loader
                    $index = $postponedLoaderCounter = 0;
                }
                elseif ($count == 1) {
                    // to avoid receiving 'postponed' status from last loader
                    $finalAttempt = TRUE;
                }

                $loader = $loaders[$index];
                $loaderClassName = get_class($loader);

                $loaderTimeStart = microtime(TRUE);
                $state = $loader->load($this, $metamodel, $filters, $finalAttempt);
                LogHelper::log_info(t(
                    "'@loaderClassName' Meta Model Loader execution time: !executionTime",
                    array('@loaderClassName' => $loaderClassName, '!executionTime' => ExecutionPerformanceHelper::formatExecutionTime($loaderTimeStart))));
                switch ($state) {
                    case AbstractMetaModelLoader::LOAD_STATE__SUCCESSFUL:
                    case AbstractMetaModelLoader::LOAD_STATE__SKIPPED:
                        unset($loaders[$index]);
                        $loaders = array_values($loaders); // re-indexing the array
                        $postponedLoaderCounter = 0;
                        $finalAttempt = FALSE;
                        break;
                    case AbstractMetaModelLoader::LOAD_STATE__POSTPONED:
                        LogHelper::log_notice(t("Execution of '@loaderClassName' Meta Model Loader is postponed", array('@loaderClassName' => $loaderClassName)));
                        $index++;
                        $postponedLoaderCounter++;
                        break;
                    default:
                        throw new IllegalStateException(t(
                            "'@loaderClassName' Meta Model Loader returned unsupported state: @stateName",
                            array('@loaderClassName' => $loaderClassName, '@stateName' => $state)));
                }
            }

            // finalizing loading operation
            foreach ($this->loaders as $loader) {
                $loader->finalize($this, $metamodel);
            }
        }

        LogHelper::log_info(t(
            '@metamodelName loading time: !loadingTime; Memory consumed: !memoryUsage',
            array(
                '@metamodelName' => $metaModelName,
                '!loadingTime' => ExecutionPerformanceHelper::formatExecutionTime($metamodelTimeStart),
                '!memoryUsage' => (memory_get_usage() - $metamodelMemoryUsage))));
    }

    // *****************************************************************************************************************************
    // * Caching Meta Model
    // *****************************************************************************************************************************
    protected function getCachePrefix() {
        $prefix = NameSpaceHelper::$NAME_SPACE__DEFAULT;

        $filters = $this->getMetaModelFilters();
        if (isset($filters)) {
            $suffix = NULL;

            ksort($filters);
            foreach ($filters as $className => $properties) {
                if (isset($suffix)) {
                    $suffix .= ',';
                }
                $suffix .= $className . '{';

                $propertySuffix = NULL;

                ksort($properties);
                foreach ($properties as $propertyName => $filterValues) {
                    sort($filterValues);

                    if (isset($propertySuffix)) {
                        $propertySuffix .= ',';
                    }
                    $propertySuffix .= $propertyName . '=[' . implode(',', $filterValues) . ']';
                }

                $suffix .= $propertySuffix . '}';
            }

            $prefix .= '[' . $suffix . ']';
        }

        return $prefix;
    }

    protected function getMetaModelCacheHandler() {
        return CacheFactory::getInstance()->getSharedCacheHandler($this->getCachePrefix());
    }

    /**
     * @return AbstractMetaModel|null
     */
    protected function loadCachedMetaModel() {
        $metamodelName = $this->getMetaModelName();

        $cacheHandler = $this->getMetaModelCacheHandler();

        return $cacheHandler->getValue($metamodelName);
    }

    protected function cacheMetaModel(AbstractMetaModel $metamodel = NULL) {
        $cacheHandler = $this->getMetaModelCacheHandler();
        $cacheHandler->setValue($this->getMetaModelName(), $metamodel);
    }

    protected function releaseCachedMetaModel() {
        $this->cacheMetaModel(NULL);
        $this->cachedMetaModel = NULL;
    }

    protected function findCachedMetaModel() {
        // checking internal cache first
        $metamodel = $this->cachedMetaModel;

        // checking external cache
        if (!isset($metamodel)) {
            $metamodel = $this->loadCachedMetaModel();
            if (isset($metamodel)) {
                if ($this->globalModificationStarted) {
                    $metamodel->startAssembling();
                }
                $this->cachedMetaModel = $metamodel;
            }
        }

        return $metamodel;
    }

    /**
     * @return MetaModel
     */
    public function getMetaModel() {
        // loading metamodel from cache
        $metamodel = $this->findCachedMetaModel();

        // assembling meta model
        if (!isset($metamodel)) {
            $metamodel = $this->initiateMetaModel();
            // we do not need to start modification because the meta model has not been assembled yet

            $this->loadMetaModel($metamodel);
            if (!$this->globalModificationStarted) {
                $metamodel->markAsAssembled();
                // storing loaded meta model into external cache
                $this->cacheMetaModel($metamodel);
            }

            // storing loaded meta model into internal cache
            $this->cachedMetaModel = $metamodel;
        }

        return $metamodel;
    }

    // *****************************************************************************************************************************
    // * Global Changes to Meta Model
    // *****************************************************************************************************************************
    public function startGlobalModification() {
        if ($this->globalModificationStarted) {
            throw new IllegalStateException(t('Meta Model modification has already been started'));
        }

        $this->globalModificationStarted = TRUE;

        if (isset($this->cachedMetaModel)) {
            $this->cachedMetaModel->startAssembling();
        }
    }

    public function finishGlobalModification($commit) {
        if (!$this->globalModificationStarted) {
            throw new IllegalStateException(t('Meta Model modification has not been started'));
        }

        $this->globalModificationStarted = FALSE;

        if (isset($this->cachedMetaModel)) {
            if ($commit) {
                $this->cachedMetaModel->markAsAssembled();

                // checking if someone updated the meta model while we were in global modification transaction
                $externalCachedMetaModel = $this->loadCachedMetaModel();
                if (isset($externalCachedMetaModel)) {
                    if ($externalCachedMetaModel->version == $this->cachedMetaModel->version) {
                        $this->cacheMetaModel($this->cachedMetaModel);
                    }
                    else {
                        // we need to remove data from external cache
                        // because different version of the meta model was changed and the cached meta model becomes obsolete
                        $this->releaseCachedMetaModel();
                    }
                }
            }
            else {
                // rolling back whatever we tried to change without removing external cache
                $this->cachedMetaModel = NULL;
            }
        }
    }
}
