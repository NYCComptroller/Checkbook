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

abstract class __CubeQueryRequest_AbstractElement extends AbstractObject {

    public $requestColumnIndex = NULL;
}

abstract class __CubeQueryRequest_AbstractDimension extends __CubeQueryRequest_AbstractElement {

    public $dimensionName = NULL;
    public $levelName = NULL;

    public function __construct($dimensionName, $levelName) {
        parent::__construct();
        $this->dimensionName = $dimensionName;
        $this->levelName = $levelName;
    }

    abstract public function getPropertyNames();
}

class __CubeQueryRequest_Property extends __CubeQueryRequest_AbstractElement {

    public $name = NULL;

    public function __construct($name) {
        parent::__construct();
        $this->name = $name;
    }
}

class __CubeQueryRequest_Dimension extends __CubeQueryRequest_AbstractDimension {

    public $properties = NULL;

    public function getPropertyNames() {
        $propertyNames = NULL;

        if (isset($this->properties)) {
            foreach ($this->properties as $property) {
                $propertyNames[] = $property->name;
            }
        }

        return $propertyNames;
    }

    protected function findProperty($name) {
        if (isset($this->properties)) {
            foreach ($this->properties as $property) {
                if ($property->name === $name) {
                    return $property;
                }
            }
        }

        return NULL;
    }

    public function registerProperty($requestColumnIndex, $name) {
        if ($this->findProperty($name) != NULL) {
            throw new IllegalArgumentException(t('The property has been registered already: @propertyName', array('@propertyName' => $name)));
        }

        $property = new __CubeQueryRequest_Property($name);
        $property->requestColumnIndex = $requestColumnIndex;

        $this->properties[] = $property;
    }
}

class __CubeQueryRequest_PropertyValue extends __CubeQueryRequest_Property {

    public $values = NULL;

    public function addPropertyValue($value) {
        $this->values[] = $value;
    }
}

class __CubeQueryRequest_DimensionQuery extends __CubeQueryRequest_AbstractDimension {

    public $values = NULL;

    public function getPropertyNames() {
        $propertyNames = NULL;

        if (isset($this->values)) {
            foreach ($this->values as $element) {
                if (isset($element->name)) {
                    $propertyNames[] = $element->name;
                }
            }
        }

        return $propertyNames;
    }

    protected function findPropertyValueElement($name) {
        if (isset($this->values)) {
            foreach ($this->values as $element) {
                if ($element->name == $name) {
                    return $element;
                }
            }
        }

        return NULL;
    }

    public function addPropertyValue($name, $value) {
        $element = $this->findPropertyValueElement($name);
        if (!isset($element)) {
            $element = new __CubeQueryRequest_PropertyValue($name);
            $this->values[] = $element;
        }
        $element->addPropertyValue($value);
    }

    public function addPropertyValues($name, $values) {
        foreach ($values as $value) {
            $this->addPropertyValue($name, $value);
        }
    }
}

class __CubeQueryRequest_SourceDatasetPropertyQuery extends __CubeQueryRequest_AbstractElement {

    public $propertyName = NULL;
    public $values = NULL;

    public function __construct($propertyName) {
        parent::__construct();
        $this->propertyName = $propertyName;
    }

    public function addValue($value) {
        $this->values[] = $value;
    }

    public function addValues($values) {
        foreach ($values as $value) {
            $this->addValue($value);
        }
    }
}

class __CubeQueryRequest_Measure extends __CubeQueryRequest_AbstractElement {

    public $measureName = NULL;

    public function __construct($measureName) {
        parent::__construct();
        $this->measureName = $measureName;
    }
}

class __CubeQueryRequest_MeasureQuery extends __CubeQueryRequest_Measure {

    public $values = NULL;

    public function addValue($value) {
        $this->values[] = $value;
    }

    public function addValues($values) {
        foreach ($values as $value) {
            $this->addValue($value);
        }
    }
}

class __CubeQueryRequest_SortingConfiguration extends __PropertyBasedComparator_AbstractSortingConfiguration {

    private $elementName = NULL; // dimension or measure name
    private $subElementName = NULL; // level name
    private $elementPropertyName = NULL;

    public function __construct($columnName, $isSortAscending) {
        list($this->elementName, $this->subElementName, $this->elementPropertyName) = ParameterHelper::splitName($columnName);
        parent::__construct($columnName, $isSortAscending);
    }

    protected function checkPropertyName() {
        // checking dimension or measure
        StringDataTypeHandler::checkValueAsWord($this->elementName);
        // checking level
        StringDataTypeHandler::checkValueAsWord($this->subElementName);
        // checking property
        StringDataTypeHandler::checkValueAsWord($this->elementPropertyName);
    }

    public function formatPropertyNameAsDatabaseColumnName($maximumLength) {
        return ParameterHelper::assembleDatabaseColumnName($maximumLength, $this->elementName, $this->subElementName, $this->elementPropertyName);
    }

    public function getElementName() {
        return $this->elementName;
    }

    public function getSubElementName() {
        return $this->subElementName;
    }

    public function getElementPropertyName() {
        return $this->elementPropertyName;
    }
}
