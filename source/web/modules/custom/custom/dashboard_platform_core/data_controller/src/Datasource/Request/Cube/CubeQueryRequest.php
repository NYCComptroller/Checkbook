<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\data_controller\Datasource\Request\Cube;

use Drupal\data_controller\Common\Datatype\Handler\StringDataTypeHandler;
use Drupal\data_controller\Common\Object\Exception\IllegalArgumentException;
use Drupal\data_controller\Common\Object\Manipulation\ArrayHelper;
use Drupal\data_controller\Datasource\Request\AbstractQueryRequest;

class CubeQueryRequest extends AbstractQueryRequest {

    public $referenced = FALSE;

    public $dimensions = NULL;
    public $measures = NULL;

    /**
     * @var CubeQueryRequest[]
     */
    public $referencedRequests = NULL;

    public function __clone() {
        parent::__clone();
        $this->referencedRequests = ArrayHelper::cloneArray($this->referencedRequests);
    }

    public function getCubeName() {
        return $this->sourceName;
    }

    public function setCubeName($newCubeName) {
        $this->sourceName = $newCubeName;
    }

    /**
     * @param $dimensionName
     * @return __CubeQueryRequest_Dimension
     */
    public function findDimension($dimensionName) {
        if (isset($this->dimensions)) {
            foreach ($this->dimensions as $dimension) {
                if ($dimension->dimensionName === $dimensionName) {
                    return $dimension;
                }
            }
        }

        return NULL;
    }

    public function importDimensionFrom(__CubeQueryRequest_Dimension $sourceDimension) {
        if ($this->findDimension($sourceDimension->dimensionName) != NULL) {
            throw new IllegalArgumentException(t(
                "Dimension '@dimensionName' has been registered already",
                array('@dimensionName' => $sourceDimension->dimensionName)));
        }

        $this->dimensions[] = $sourceDimension;
    }

    public function addDimensionLevel($requestColumnIndex, $dimensionName, $levelName) {
        $dimension = $this->findDimension($dimensionName);
        if (isset($dimension)) {
            if ($dimension->levelName !== $levelName) {
                throw new IllegalArgumentException(t(
                	"'@dimensionName' dimension is locked on '@previousLevelName' level. '@levelName' level cannot be supported for this request",
                    array('@dimensionName' => $dimensionName, '@previousLevelName' => $dimension->levelName, '@levelName' => $levelName)));
            }
        }
        else {
            StringDataTypeHandler::checkValueAsWord($dimensionName);
            StringDataTypeHandler::checkValueAsWord($levelName);

            $dimension = new __CubeQueryRequest_Dimension($dimensionName, $levelName);

            $this->dimensions[] = $dimension;
        }

        if (isset($requestColumnIndex)) {
            $dimension->requestColumnIndex = $requestColumnIndex;
        }

        return $dimension;
    }

    public function addDimensionLevelProperty($requestColumnIndex, $dimensionName, $levelName, $propertyName) {
        StringDataTypeHandler::checkValueAsWord($propertyName);

        $dimension = $this->addDimensionLevel(NULL, $dimensionName, $levelName)->registerProperty($requestColumnIndex, $propertyName);
    }

    /**
     * @param $measureName
     * @return __CubeQueryRequest_Measure
     */
    public function findMeasure($measureName) {
        if (isset($this->measures)) {
            foreach ($this->measures as $measure) {
                if ($measure->measureName === $measureName) {
                    return $measure;
                }
            }
        }

        return NULL;
    }

    public function importMeasureFrom(__CubeQueryRequest_Measure $sourceMeasure) {
        if ($this->findMeasure($sourceMeasure->measureName) != NULL) {
            throw new IllegalArgumentException(t(
                "Measure '@measureName' has been registered already",
                array('@measureName' => $sourceMeasure->measureName)));
        }

        $this->measures[] = $sourceMeasure;
    }

    public function importMeasuresFrom(CubeQueryRequest $sourceQueryRequest) {
        if (isset($sourceQueryRequest->measures)) {
            foreach ($sourceQueryRequest->measures as $measure) {
                $this->importMeasureFrom($measure);
            }
        }
    }

    public function addMeasure($requestColumnIndex, $measureName) {
        if ($this->findMeasure($measureName) != NULL) {
            throw new IllegalArgumentException(t(
                "Measure '@measureName' has been registered already",
                array('@measureName' => $measureName)));
        }

        StringDataTypeHandler::checkValueAsWord($measureName);

        $measure = new __CubeQueryRequest_Measure($measureName);
        $measure->requestColumnIndex = $requestColumnIndex;

        $this->measures[] = $measure;
    }

    public function findDimensionQuery($dimensionName) {
        $dimensionQueries = $this->findDimensionQueries();
        if (isset($dimensionQueries)) {
            foreach ($dimensionQueries as $query) {
                if ($query->dimensionName === $dimensionName) {
                    return $query;
                }
            }
        }

        return NULL;
    }

    /**
     * @return __CubeQueryRequest_DimensionQuery[]|null
     */
    public function findDimensionQueries() {
        $queries = NULL;

        if (isset($this->queries)) {
            foreach ($this->queries as $query) {
                if ($query instanceof __CubeQueryRequest_DimensionQuery) {
                    $queries[] = $query;
                }
            }
        }

        return $queries;
    }

    protected function addDimensionLevelQuery($dimensionName, $levelName) {
        $query = $this->findDimensionQuery($dimensionName);
        if (isset($query)) {
            if ($query->levelName !== $levelName) {
                throw new IllegalArgumentException(t(
                	"'@dimensionName' dimension is locked on '@previousLevelName' level. '@levelName' level cannot be supported for this request",
                    array('@dimensionName' => $dimensionName, '@previousLevelName' => $query->levelName, '@levelName' => $levelName)));
            }
        }
        else {
            StringDataTypeHandler::checkValueAsWord($dimensionName);
            StringDataTypeHandler::checkValueAsWord($levelName);

            $query = new __CubeQueryRequest_DimensionQuery($dimensionName, $levelName);

            $this->queries[] = $query;
        }

        return $query;
    }

    public function importDimensionQueryFrom(__CubeQueryRequest_DimensionQuery $sourceDimensionQuery) {
        if ($this->findDimensionQuery($sourceDimensionQuery->dimensionName) != NULL) {
            throw new IllegalArgumentException(t(
                "Query for '@dimensionName' dimension has been registered already",
                array('@dimensionName' => $sourceDimensionQuery->dimensionName)));
        }

        $this->queries[] = $sourceDimensionQuery;
    }

    public function addDimensionLevelQueryValue($dimensionName, $levelName, $value) {
        $this->addDimensionLevelPropertyQueryValue($dimensionName, $levelName, NULL, $value);
    }

    public function addDimensionLevelQueryValues($dimensionName, $levelName, $values) {
        foreach ($values as $value) {
            $this->addDimensionLevelQueryValue($dimensionName, $levelName, $value);
        }
    }

    public function addDimensionLevelPropertyQueryValue($dimensionName, $levelName, $propertyName, $value) {
        $this->addDimensionLevelQuery($dimensionName, $levelName)->addPropertyValue($propertyName, $value);
    }

    public function addDimensionLevelPropertyQueryValues($dimensionName, $levelName, $propertyName, $values) {
        $this->addDimensionLevelQuery($dimensionName, $levelName)->addPropertyValues($propertyName, $values);
    }

    public function findSourceDatasetPropertyQuery($propertyName) {
        $sourceDatasetPropertyQueries = $this->findSourceDatasetPropertyQueries();
        if (isset($sourceDatasetPropertyQueries)) {
            foreach ($sourceDatasetPropertyQueries as $query) {
                if ($query->propertyName === $propertyName) {
                    return $query;
                }
            }
        }

        return NULL;
    }

    public function findSourceDatasetPropertyQueries() {
        $queries = NULL;

        if (isset($this->queries)) {
            foreach ($this->queries as $query) {
                if ($query instanceof __CubeQueryRequest_SourceDatasetPropertyQuery) {
                    $queries[] = $query;
                }
            }
        }

        return $queries;
    }

    protected function importSourceDatasetPropertyQueryFrom(__CubeQueryRequest_SourceDatasetPropertyQuery $sourceSourceDatasetPropertyQuery) {
        if ($this->findSourceDatasetPropertyQuery($sourceSourceDatasetPropertyQuery->propertyName) != NULL) {
            throw new IllegalArgumentException(t(
                "Query for '@propertyName' source dataset property has been registered already",
                array('@propertyName' => $sourceSourceDatasetPropertyQuery->propertyName)));
        }

        $this->queries[] = $sourceSourceDatasetPropertyQuery;

    }

    public function importSourceDatasetPropertyQueriesFrom(CubeQueryRequest $sourceQueryRequest) {
        $sourceSourceDatasetPropertyQueries = $sourceQueryRequest->findSourceDatasetPropertyQueries();
        if (isset($sourceSourceDatasetPropertyQueries)) {
            foreach ($sourceSourceDatasetPropertyQueries as $sourceSourceDatasetPropertyQuery) {
                $this->importSourceDatasetPropertyQueryFrom($sourceSourceDatasetPropertyQuery);
            }
        }
    }

    protected function addSourceDatasetPropertyQuery($propertyName) {
        $query = $this->findSourceDatasetPropertyQuery($propertyName);
        if (!isset($query)) {
            StringDataTypeHandler::checkValueAsWord($propertyName);

            $query = new __CubeQueryRequest_SourceDatasetPropertyQuery($propertyName);

            $this->queries[] = $query;
        }

        return $query;
    }

    public function addSourceDatasetPropertyQueryValue($propertyName, $value) {
        $this->addSourceDatasetPropertyQuery($propertyName)->addValue($value);
    }

    public function addSourceDatasetPropertyQueryValues($propertyName, $values) {
        $this->addSourceDatasetPropertyQuery($propertyName)->addValues($values);
    }

    /**
     * @param $measureName
     * @return __CubeQueryRequest_MeasureQuery|null
     */
    public function findMeasureQuery($measureName) {
        $measureQueries = $this->findMeasureQueries();
        if (isset($measureQueries)) {
            foreach ($measureQueries as $query) {
                if ($query->measureName === $measureName) {
                    return $query;
                }
            }
        }

        return NULL;
    }

    /**
     * @return __CubeQueryRequest_MeasureQuery[]|null
     */
    public function findMeasureQueries() {
        $queries = NULL;

        if (isset($this->queries)) {
            foreach ($this->queries as $query) {
                if ($query instanceof __CubeQueryRequest_MeasureQuery) {
                    $queries[] = $query;
                }
            }
        }

        return $queries;
    }

    protected function importMeasureQueryFrom(__CubeQueryRequest_MeasureQuery $sourceMeasureQuery) {
        if ($this->findMeasureQuery($sourceMeasureQuery->measureName) != NULL) {
            throw new IllegalArgumentException(t(
                "Query for '@measureName' measure has been registered already",
                array('@measureName' => $sourceMeasureQuery->measureName)));
        }

        $this->queries[] = $sourceMeasureQuery;

    }

    public function importMeasureQueriesFrom(CubeQueryRequest $sourceQueryRequest) {
        $sourceMeasureQueries = $sourceQueryRequest->findMeasureQueries();
        if (isset($sourceMeasureQueries)) {
            foreach ($sourceMeasureQueries as $sourceMeasureQuery) {
                $this->importMeasureQueryFrom($sourceMeasureQuery);
            }
        }
    }

    protected function addMeasureQuery($measureName) {
        $query = $this->findMeasureQuery($measureName);
        if (!isset($query)) {
            StringDataTypeHandler::checkValueAsWord($measureName);

            $query = new __CubeQueryRequest_MeasureQuery($measureName);

            $this->queries[] = $query;
        }

        return $query;
    }

    public function addMeasureQueryValue($measureName, $value) {
        $this->addMeasureQuery($measureName)->addValue($value);
    }

    public function addMeasureQueryValues($measureName, $values) {
        $this->addMeasureQuery($measureName)->addValues($values);
    }

    public function findLevelSortingColumns($dimensionName, $levelName) {
        $columns = NULL;

        if (isset($this->sortingConfigurations)) {
            foreach ($this->sortingConfigurations as $sortingConfiguration) {
                // checking dimension
                if ($sortingConfiguration->getElementName() != $dimensionName) {
                    continue;
                }

                // checking level
                if ($sortingConfiguration->getSubElementName() != $levelName) {
                    continue;
                }

                $propertyName = $sortingConfiguration->getElementPropertyName();
                if (isset($propertyName)) {
                    $columns[] = $propertyName;
                }
            }
        }

        return $columns;
    }

    protected function initiateSortingConfiguration($columnName, $isSortAscending,$sql=NULL) {
        return new __CubeQueryRequest_SortingConfiguration($columnName, $isSortAscending);
    }

    public function registerReferencedRequest($cubeName) {
        if (isset($this->referencedRequests[$cubeName])) {
            return $this->referencedRequests[$cubeName];
        }

        $request = new CubeQueryRequest($cubeName);
        $request->referenced = TRUE;

        $this->referencedRequests[$cubeName] = $request;

        return $request;
    }
}
