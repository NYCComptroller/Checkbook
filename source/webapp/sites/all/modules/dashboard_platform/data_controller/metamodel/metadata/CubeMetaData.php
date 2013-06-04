<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class CubeMetaData extends AbstractMetaData {

    public $sourceDatasetName = NULL;
    /**
     * @var DatasetMetaData
     */
    public $sourceDataset = NULL; // populated when source dataset meta data is loaded
    /**
     * @var DimensionMetaData[]
     */
    public $dimensions = NULL;
    /**
     * @var MeasureMetaData[]
     */
    public $measures = NULL;

    public function __clone() {
        parent::__clone();

        $this->dimensions = ArrayHelper::cloneArray($this->dimensions);
        $this->measures = ArrayHelper::cloneArray($this->measures);
    }

    public function finalize() {
        parent::finalize();

        if (isset($this->dimensions)) {
            foreach ($this->dimensions as $dimension) {
                $dimension->finalize();
            }
        }

        if (isset($this->measures)) {
            foreach ($this->measures as $measure) {
                $measure->finalize();
            }
        }
    }

    public function isComplete() {
        $complete = parent::isComplete() && isset($this->sourceDataset) && $this->sourceDataset->isComplete();

        for ($i = 0, $count = count($this->dimensions); $complete && ($i < $count); $i++) {
            $dimension = $this->dimensions[$i];
            $complete = $dimension->isComplete();
        }

        if ($complete) {
            foreach ($this->measures as $measure) {
                if ($measure->isComplete() !== TRUE) {
                    return FALSE;
                }
            }
        }

        return $complete;
    }

    public function initializeFrom($sourceCube) {
        parent::initializeFrom($sourceCube);

        // preparing dataset property
        $sourceSourceDataset = ObjectHelper::getPropertyValue($sourceCube, 'sourceDataset');
        if (isset($sourceSourceDataset)) {
            $this->initializeSourceDatasetFrom($sourceSourceDataset);
        }

        // preparing list of dimensions
        $sourceDimensions = ObjectHelper::getPropertyValue($sourceCube, 'dimensions');
        if (isset($sourceDimensions)) {
            $this->initializeDimensionsFrom($sourceDimensions);
        }

        // preparing list of measures
        $sourceMeasures = ObjectHelper::getPropertyValue($sourceCube, 'measures');
        if (isset($sourceMeasures)) {
            $this->initializeMeasuresFrom($sourceMeasures);
        }
    }

    public function initializeSourceDatasetFrom($sourceSourceDataset) {
        if (isset($sourceSourceDataset)) {
            if (!isset($this->sourceDataset)) {
                $this->initiateSourceDataset();
            }
            $this->sourceDataset->initializeFrom($sourceSourceDataset);
        }
    }

    public function initiateSourceDataset() {
        $this->sourceDataset = new CubeSourceDatasetMetaData();

        return $this->sourceDataset;
    }

    public function initializeDimensionsFrom($sourceDimensions) {
        if (isset($sourceDimensions)) {
            foreach ($sourceDimensions as $sourceDimension) {
                $sourceDimensionName = ObjectHelper::getPropertyValue($sourceDimension, 'name');

                $dimension = $this->findDimension($sourceDimensionName);
                if (!isset($dimension)) {
                    $dimension = $this->registerDimension($sourceDimensionName);
                }
                $dimension->initializeFrom($sourceDimension);
            }
        }
    }

    public function initiateDimension() {
        return new DimensionMetaData();
    }

    public function registerDimension($dimensionName) {
        $dimension = $this->initiateDimension();
        $dimension->name = $dimensionName;

        $this->registerDimensionInstance($dimension);

        return $dimension;
    }

    public function registerDimensionInstance(DimensionMetaData $unregisteredDimension) {
        $existingDimension = $this->findDimension($unregisteredDimension->name);
        if (isset($existingDimension)) {
            $this->errorDimensionFound($existingDimension);
        }

        $this->dimensions[] = $unregisteredDimension;
    }

    public function unregisterDimension($dimensionName) {
        if (isset($this->dimensions)) {
            for ($i = 0, $count = count($this->dimensions); $i < $count; $i++) {
                $dimension = $this->dimensions[$i];
                if ($dimension->name === $dimensionName) {
                    unset($this->dimensions[$i]);
                    return;
                }
            }
        }

        $this->errorDimensionNotFound($dimensionName);
    }

    public function findDimension($dimensionName) {
        if (isset($this->dimensions)) {
            foreach ($this->dimensions as $dimension) {
                if ($dimension->name === $dimensionName) {
                    return $dimension;
                }
            }
        }

        return NULL;
    }

    public function getDimension($dimensionName) {
        $dimension = $this->findDimension($dimensionName);
        if (!isset($dimension)) {
            $this->errorDimensionNotFound($dimensionName);
        }

        return $dimension;
    }

    public function findDimensionAndLevelIndexBySourceColumnName($sourceColumnName) {
        $dimensionAndLevelIndex = NULL;

        if (isset($this->dimensions)) {
            foreach ($this->dimensions as $dimension) {
                foreach ($dimension->levels as $levelIndex => $level) {
                    if ($level->sourceColumnName == $sourceColumnName) {
                        if (isset($dimensionAndLevelIndex)) {
                            $this->errorSeveralDimensionsFoundBySourceColumnName($sourceColumnName);
                        }

                        $dimensionAndLevelIndex = array($dimension, $levelIndex);
                    }
                }
            }
        }

        return $dimensionAndLevelIndex;
    }

    public function getDimensionAndLevelIndexBySourceColumnName($sourceColumnName) {
        $dimensionAndLevelIndex = $this->findDimensionAndLevelIndexBySourceColumnName($sourceColumnName);
        if (!isset($dimensionAndLevelIndex)) {
            $this->errorDimensionNotFoundBySourceColumnName($sourceColumnName);
        }

        return $dimensionAndLevelIndex;
    }

    public function getDimensionCount() {
        return isset($this->dimensions) ? count($this->dimensions) : 0;
    }

    protected function errorDimensionFound($dimension) {
        throw new IllegalArgumentException(t(
        	"Dimension '@dimensionName' has been already registered in '@cubeName' cube",
            array('@dimensionName' => $dimension->name, '@cubeName' => $this->publicName)));
    }

    protected function errorDimensionNotFound($dimensionName) {
        throw new IllegalArgumentException(t(
        	"Dimension '@dimensionName' is not registered in '@cubeName' cube",
            array('@dimensionName' => $dimensionName, '@cubeName' => $this->publicName)));
    }

    protected function errorSeveralDimensionsFoundBySourceColumnName($sourceColumnName) {
        throw new IllegalArgumentException(t(
        	"Found several dimensions in '@cubeName' cube by the source column name: @sourceColumnName",
            array('@sourceColumnName' => $sourceColumnName, '@cubeName' => $this->publicName)));
    }

    protected function errorDimensionNotFoundBySourceColumnName($sourceColumnName) {
        throw new IllegalArgumentException(t(
        	"Cannot find dimension in '@cubeName' cube by the source column name: @sourceColumnName",
            array('@sourceColumnName' => $sourceColumnName, '@cubeName' => $this->publicName)));
    }

    public function initializeMeasuresFrom($sourceMeasures) {
        if (isset($sourceMeasures)) {
            foreach ($sourceMeasures as $sourceMeasureName => $sourceMeasure) {
                $measure = $this->findMeasure($sourceMeasureName);
                if (!isset($measure)) {
                    $measure = $this->registerMeasure($sourceMeasureName);
                }

                $measure->initializeFrom($sourceMeasure);
            }
        }
    }

    public function initiateMeasure() {
        return new MeasureMetaData();
    }

    public function registerMeasure($measureName) {
        $measure = $this->initiateMeasure();
        $measure->name = $measureName;

        $this->registerMeasureInstance($measure);

        return $measure;
    }

    public function registerMeasureInstance(MeasureMetaData $unregisteredMeasure) {
        $existingMeasure = $this->findMeasure($unregisteredMeasure->name);
        if (isset($existingMeasure)) {
            $this->errorMeasureFound($existingMeasure);
        }

        $this->measures[$unregisteredMeasure->name] = $unregisteredMeasure;
    }

    public function unregisterMeasure($measureName) {
        if (isset($this->measures[$measureName])) {
            unset($this->measures[$measureName]);
        }

        $this->errorMeasureNotFound($measureName);
    }

    /**
     * @param $measureName
     * @return MeasureMetaData
     */
    public function findMeasure($measureName) {
        return isset($this->measures[$measureName])
            ? $this->measures[$measureName]
            : NULL;
    }

    /**
     * @param $measureName
     * @return MeasureMetaData
     */
    public function getMeasure($measureName) {
        $measure = $this->findMeasure($measureName);
        if (!isset($measure)) {
            $this->errorMeasureNotFound($measureName);
        }

        return $measure;
    }

    public function getMeasureCount() {
        return isset($this->measures) ? count($this->measures) : 0;
    }

    protected function errorMeasureFound($measure) {
        throw new IllegalArgumentException(t(
        	"Measure '@measureName' has been already registered in '@cubeName' cube",
            array('@measureName' => $measure->name, '@cubeName' => $this->publicName)));
    }

    protected function errorMeasureNotFound($measureName) {
        throw new IllegalArgumentException(t(
        	"Measure '@measureName' is not registered in '@cubeName' cube",
            array('@measureName' => $measureName, '@cubeName' => $this->publicName)));
    }
}

class CubeSourceDatasetMetaData extends DatasetMetaData {

    public function __construct() {
        parent::__construct();
        $this->system = TRUE;
    }

    public function initiateColumn() {
        return new CubeSourceDatasetColumnMetaData();
    }
}

class CubeSourceDatasetColumnMetaData extends ColumnMetaData {

}
