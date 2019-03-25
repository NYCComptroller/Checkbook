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


class CubeMetaData extends AbstractMetaData {

  /**
   * @var null
   */
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

  /**
   * @var array
   */
  public $regions = [];

  /**
   *
   */
  public function __clone() {
        parent::__clone();

        $this->dimensions = ArrayHelper::cloneArray($this->dimensions);
        $this->measures = ArrayHelper::cloneArray($this->measures);
    }

  /**
   *
   */
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

  /**
   * @return bool|null
   */
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

  /**
   * @param $sourceCube
   */
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

  /**
   * @param $sourceSourceDataset
   * @throws IllegalArgumentException
   */
  public function initializeSourceDatasetFrom($sourceSourceDataset) {
        if (isset($sourceSourceDataset)) {
            if (!isset($this->sourceDataset)) {
                $this->initiateSourceDataset();
            }
            $this->sourceDataset->initializeFrom($sourceSourceDataset);
        }
    }

  /**
   * @return CubeSourceDatasetMetaData|DatasetMetaData
   */
  public function initiateSourceDataset() {
        $this->sourceDataset = new CubeSourceDatasetMetaData();

        return $this->sourceDataset;
    }

  /**
   * @param $sourceDimensions
   */
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

  /**
   * @return DimensionMetaData
   */
  public function initiateDimension() {
        return new DimensionMetaData();
    }

  /**
   * @param $dimensionName
   * @return DimensionMetaData
   */
  public function registerDimension($dimensionName) {
        $dimension = $this->initiateDimension();
        $dimension->name = $dimensionName;

        $this->registerDimensionInstance($dimension);

        return $dimension;
    }

  /**
   * @param DimensionMetaData $unregisteredDimension
   * @throws IllegalArgumentException
   */
  public function registerDimensionInstance(DimensionMetaData $unregisteredDimension) {
        $existingDimension = $this->findDimension($unregisteredDimension->name);
        if (isset($existingDimension)) {
            $this->errorDimensionFound($existingDimension);
        }

        $this->dimensions[] = $unregisteredDimension;
    }

  /**
   * @param $dimensionName
   * @throws IllegalArgumentException
   */
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

  /**
   * @param $dimensionName
   * @return DimensionMetaData|null
   */
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

  /**
   * @param $dimensionName
   * @return DimensionMetaData|null
   * @throws IllegalArgumentException
   */
  public function getDimension($dimensionName) {
        $dimension = $this->findDimension($dimensionName);
        if (!isset($dimension)) {
            $this->errorDimensionNotFound($dimensionName);
        }

        return $dimension;
    }

  /**
   * @param $sourceColumnName
   * @return array|null
   * @throws IllegalArgumentException
   */
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

  /**
   * @param $sourceColumnName
   * @return array|null
   * @throws IllegalArgumentException
   */
  public function getDimensionAndLevelIndexBySourceColumnName($sourceColumnName) {
        $dimensionAndLevelIndex = $this->findDimensionAndLevelIndexBySourceColumnName($sourceColumnName);
        if (!isset($dimensionAndLevelIndex)) {
            $this->errorDimensionNotFoundBySourceColumnName($sourceColumnName);
        }

        return $dimensionAndLevelIndex;
    }

  /**
   * @return int
   */
  public function getDimensionCount() {
        return isset($this->dimensions) ? count($this->dimensions) : 0;
    }

  /**
   * @param $dimension
   * @throws IllegalArgumentException
   */
  protected function errorDimensionFound($dimension) {
        throw new IllegalArgumentException(t(
        	"Dimension '@dimensionName' has been already registered in '@cubeName' cube",
            array('@dimensionName' => $dimension->name, '@cubeName' => $this->publicName)));
    }

  /**
   * @param $dimensionName
   * @throws IllegalArgumentException
   */
  protected function errorDimensionNotFound($dimensionName) {
        throw new IllegalArgumentException(t(
        	"Dimension '@dimensionName' is not registered in '@cubeName' cube",
            array('@dimensionName' => $dimensionName, '@cubeName' => $this->publicName)));
    }

  /**
   * @param $sourceColumnName
   * @throws IllegalArgumentException
   */
  protected function errorSeveralDimensionsFoundBySourceColumnName($sourceColumnName) {
        throw new IllegalArgumentException(t(
        	"Found several dimensions in '@cubeName' cube by the source column name: @sourceColumnName",
            array('@sourceColumnName' => $sourceColumnName, '@cubeName' => $this->publicName)));
    }

  /**
   * @param $sourceColumnName
   * @throws IllegalArgumentException
   */
  protected function errorDimensionNotFoundBySourceColumnName($sourceColumnName) {
        throw new IllegalArgumentException(t(
        	"Cannot find dimension in '@cubeName' cube by the source column name: @sourceColumnName",
            array('@sourceColumnName' => $sourceColumnName, '@cubeName' => $this->publicName)));
    }

  /**
   * @param $sourceMeasures
   */
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

  /**
   * @return MeasureMetaData
   */
  public function initiateMeasure() {
        return new MeasureMetaData();
    }

  /**
   * @param $measureName
   * @return MeasureMetaData
   */
  public function registerMeasure($measureName) {
        $measure = $this->initiateMeasure();
        $measure->name = $measureName;

        $this->registerMeasureInstance($measure);

        return $measure;
    }

  /**
   * @param MeasureMetaData $unregisteredMeasure
   * @throws IllegalArgumentException
   */
  public function registerMeasureInstance(MeasureMetaData $unregisteredMeasure) {
        $existingMeasure = $this->findMeasure($unregisteredMeasure->name);
        if (isset($existingMeasure)) {
            $this->errorMeasureFound($existingMeasure);
        }

        $this->measures[$unregisteredMeasure->name] = $unregisteredMeasure;
    }

  /**
   * @param $measureName
   * @throws IllegalArgumentException
   */
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

  /**
   * @return int
   */
  public function getMeasureCount() {
        return isset($this->measures) ? count($this->measures) : 0;
    }

  /**
   * @param $measure
   * @throws IllegalArgumentException
   */
  protected function errorMeasureFound($measure) {
        throw new IllegalArgumentException(t(
        	"Measure '@measureName' has been already registered in '@cubeName' cube",
            array('@measureName' => $measure->name, '@cubeName' => $this->publicName)));
    }

  /**
   * @param $measureName
   * @throws IllegalArgumentException
   */
  protected function errorMeasureNotFound($measureName) {
        throw new IllegalArgumentException(t(
        	"Measure '@measureName' is not registered in '@cubeName' cube",
            array('@measureName' => $measureName, '@cubeName' => $this->publicName)));
    }
}

/**
 * Class CubeSourceDatasetMetaData
 */
class CubeSourceDatasetMetaData extends DatasetMetaData {

  /**
   * CubeSourceDatasetMetaData constructor.
   */
  public function __construct() {
        parent::__construct();
        $this->system = TRUE;
    }

  /**
   * @return ColumnMetaData|CubeSourceDatasetColumnMetaData
   */
  public function initiateColumn() {
        return new CubeSourceDatasetColumnMetaData();
    }
}

/**
 * Class CubeSourceDatasetColumnMetaData
 */
class CubeSourceDatasetColumnMetaData extends ColumnMetaData {

}
