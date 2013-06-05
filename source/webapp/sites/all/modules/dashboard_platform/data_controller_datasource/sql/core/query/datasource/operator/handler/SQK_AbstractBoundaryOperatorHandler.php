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


abstract class SQK_AbstractBoundaryOperatorHandler extends SQL_AbstractOperatorHandler {

    protected static $OPERATOR_VARIABLE_NAME__CALCULATED = 'boundary:value';

    abstract protected function isSortAscending();

    protected function adjustCalculatedValue($value) {
        return $value;
    }

    protected function prepareExpression(DataControllerCallContext $callcontext, AbstractRequest $request, $datasetName, $columnName, $columnDataType) {
        $boundaryValue = $this->prepareBoundaryValue($callcontext, $request, $datasetName, $columnName, $columnDataType);

        $operator = OperatorFactory::getInstance()->initiateHandler(EqualOperatorHandler::$OPERATOR__NAME, $boundaryValue);
        $sqlOperatorHandler = SQLOperatorFactory::getInstance()->getHandler($this->datasourceHandler, $operator);

        return $sqlOperatorHandler->format($callcontext, $request, $datasetName, $columnName, $columnDataType);
    }

    public function prepareBoundaryValue(DataControllerCallContext $callcontext, AbstractRequest $request, $datasetName, $columnName) {
        $boundaryValue = NULL;
        if ($this->operatorHandler->wasValueCalculated(self::$OPERATOR_VARIABLE_NAME__CALCULATED)) {
            // the value has been calculated already
            $boundaryValue = $this->operatorHandler->getCalculatedValue(self::$OPERATOR_VARIABLE_NAME__CALCULATED);
        }
        else {
            if ($request instanceof CubeQueryRequest) {
                $boundaryValue = $this->selectBoundary4CubeRequest($callcontext, $request, $datasetName, $columnName);
            }
            elseif ($request instanceof AbstractDatasetQueryRequest) {
                $boundaryValue = $this->selectBoundary4DatasetRequest($callcontext, $request, $datasetName, $columnName);
            }
            else {
                throw new UnsupportedOperationException(t("'@classname' class is not supported", array('@classname' => get_class($request))));
            }

            $this->operatorHandler->setCalculatedValue(self::$OPERATOR_VARIABLE_NAME__CALCULATED, $boundaryValue);
        }

        return $boundaryValue;
    }

    protected function selectBoundary4DatasetRequest(DataControllerCallContext $callcontext, AbstractDatasetQueryRequest $request, $datasetName, $columnName) {
        // looking for an index where this instance is used. Rest of the queries will be ignored.
        // Data for ignored queries will be calculated during subsequent requests to different instances of this class
        $selectedIndex = NULL;
        if (isset($request->queries)) {
            foreach ($request->queries as $index => $query) {
                foreach ($query as $values) {
                    foreach ($values as $value) {
                        if ($this->operatorHandler === $value) {
                            $selectedIndex = $index;
                            break 3;
                        }
                    }
                }
            }
        }

        $expressionRequest = new DatasetQueryRequest($request->getDatasetName());
        // returning only observing column
        $expressionRequest->addColumn($columnName);
        // excluding records with NULL value for the observing column
        $expressionRequest->addQueryValue(
            0,
            $columnName, data_controller_get_operator_factory_instance()->initiateHandler(NotEqualOperatorHandler::$OPERATOR__NAME, NULL));
        // adding support for queries except this operator handler
        if (isset($selectedIndex)) {
            foreach ($request->queries[$selectedIndex] as $name => $values) {
                foreach ($values as $value) {
                    if ($value->isSubsetBased()) {
                        // skipping other instances which are based on subset of data if their weight is greater than of this operator
                        if ((isset($value->weight)) && ($value->weight > $this->operatorHandler->weight)) {
                            continue;
                        }
                    }

                    if ($this->operatorHandler === $value) {
                        continue;
                    }

                    $expressionRequest->addQueryValue(0, $name, $value);
                }
            }
        }
        else {
            // we have this situation because we call this operator for a request where thus operator is not used
        }
        // sorting data
        $expressionRequest->addOrderByColumn(
            PropertyBasedComparator_DefaultSortingConfiguration::assembleDirectionalPropertyName($columnName, $this->isSortAscending()));
        // limiting response to one record
        $expressionRequest->setPagination(1, 0);

        return $this->processDatasetExpressionRequest($callcontext, $expressionRequest, $columnName);
    }

    protected function processDatasetExpressionRequest(DataControllerCallContext $callcontext, DatasetQueryRequest $expressionRequest, $columnName) {
        $result = $this->datasourceHandler->queryDataset($callcontext, $expressionRequest, new PassthroughResultFormatter());

        return isset($result) ? $this->adjustCalculatedValue($result[0][$columnName]) : NULL;
    }

    protected function selectBoundary4CubeRequest(DataControllerCallContext $callcontext, CubeQueryRequest $request, $datasetName, $columnName) {
        $isSortAscending = $this->isSortAscending();

        $resultColumnName = NULL;

        // preparing new cube meta data
        $expressionRequest = new CubeQueryRequest($request->getCubeName());

        // copying ONLY some query objects (excluding at least a reference to this operator)
        // -- dimension queries
        $dimensionQueries = $request->findDimensionQueries();
        if (isset($dimensionQueries)) {
            foreach ($dimensionQueries as $query) {
                foreach ($query->values as $propertyValue) {
                    foreach ($propertyValue->values as $value) {
                        if ($value->isSubsetBased()) {
                            // skipping other instances which are based on subset of data if their weight is greater than of this operator
                            if ((isset($value->weight)) && ($value->weight > $this->operatorHandler->weight)) {
                                continue;
                            }
                        }

                        // updating request configuration for the value supported by this class
                        if ($this->operatorHandler === $value) {
                            $resultColumnName = ParameterHelper::assembleParameterName($query->dimensionName, $query->levelName, $propertyValue->name);

                            // returning only observing property of the dimension level
                            $expressionRequest->addDimensionLevelProperty(0, $query->dimensionName, $query->levelName, $propertyValue->name);
                            // ... and excluding NULL values from evaluation
                            $expressionRequest->addDimensionLevelPropertyQueryValue(
                                $query->dimensionName, $query->levelName, $propertyValue->name,
                                data_controller_get_operator_factory_instance()->initiateHandler(NotEqualOperatorHandler::$OPERATOR__NAME, NULL));
                            // sorting data
                            $expressionRequest->addOrderByColumn(
                                PropertyBasedComparator_DefaultSortingConfiguration::assembleDirectionalPropertyName($resultColumnName, $isSortAscending));
                        }
                        else {
                            $expressionRequest->addDimensionLevelPropertyQueryValue($query->dimensionName, $query->levelName, $propertyValue->name, $value);
                        }
                    }
                }
            }
        }
        // -- source dataset property queries
        $sourceDatasetPropertyQueries = $request->findSourceDatasetPropertyQueries();
        if (isset($sourceDatasetPropertyQueries)) {
            foreach ($sourceDatasetPropertyQueries as $query) {
                foreach ($query->values as $value) {
                    if ($value->isSubsetBased()) {
                        throw new UnsupportedOperationException(t('Boundary-related operator cannot filter cube source dataset property values yet'));
                    }
                }

                $expressionRequest->queries[] = clone $query;
            }
        }
        // -- measure queries
        $measureQueries = $request->findMeasureQueries();
        if (isset($measureQueries)) {
            foreach ($measureQueries as $query) {
                foreach ($query->values as $value) {
                    if ($value->isSubsetBased()) {
                        throw new UnsupportedOperationException(t('Boundary-related operator cannot filter measure values yet'));
                    }
                }

                $expressionRequest->queries[] = clone $query;
            }
        }

        // limiting response to one record
        $expressionRequest->setPagination(1, 0);

        return $this->processCubeExpressionRequest($callcontext, $expressionRequest, $resultColumnName);
    }

    protected function processCubeExpressionRequest(DataControllerCallContext $callcontext, CubeQueryRequest $expressionRequest, $resultColumnName) {
        $result = $this->datasourceHandler->queryCube($callcontext, $expressionRequest, new PassthroughResultFormatter());

        $selectedValue = $this->processCubeExpressionRequestResult($resultColumnName, $result);
        if (isset($selectedValue)) {
            $selectedValue = $this->adjustCalculatedValue($selectedValue);
        }

        return $selectedValue;
    }

    protected function processCubeExpressionRequestResult($resultColumnName, array $result = NULL) {
        return isset($result) ? $result[0][$resultColumnName] : NULL;
    }
}
