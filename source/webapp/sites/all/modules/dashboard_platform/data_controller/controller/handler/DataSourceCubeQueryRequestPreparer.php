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


class DataSourceCubeQueryRequestPreparer extends AbstractObject {

  /**
   * @param DataQueryControllerCubeRequest $request
   * @return CubeQueryRequest
   * @throws IllegalArgumentException
   * @throws UnsupportedOperationException
   */
  public function prepareCubeQueryRequest(DataQueryControllerCubeRequest $request) {
        $cube = $this->getCube($request);

        $datasourceRequest = new CubeQueryRequest($cube->name);

        $this->prepareCubeRequestColumns($datasourceRequest, $cube, $request->columns);
        $this->prepareCubeRequestQueries($datasourceRequest, $cube, $request->parameters);
        $datasourceRequest->addOrderByColumns($request->orderBy,$request->sortSourceByNull);
        $datasourceRequest->setPagination($request->limit, $request->startWith);
        $datasourceRequest->addLogicalOrColumns($request->logicalOrColumns);

        $this->useApplicableCubeRegions($datasourceRequest, $cube);

        return $datasourceRequest;
    }

  /**
   * @param DataQueryControllerCubeRequest $request
   * @return CubeQueryRequest
   * @throws IllegalArgumentException
   * @throws UnsupportedOperationException
   */
  public function prepareCubeCountRequest(DataQueryControllerCubeRequest $request) {
        $cube = $this->getCube($request);

        $datasourceRequest = new CubeQueryRequest($cube->name);

        $datasourceRequest->addLogicalOrColumns($request->logicalOrColumns);

        $this->prepareCubeRequestColumns($datasourceRequest, $cube, $request->columns);
        $this->prepareCubeRequestQueries($datasourceRequest, $cube, $request->parameters);

        $this->useApplicableCubeRegions($datasourceRequest, $cube);

        return $datasourceRequest;
    }

  /**
   * @param DataQueryControllerCubeRequest $request
   * @return CubeMetaData
   * @throws IllegalArgumentException
   */
  protected function getCube(DataQueryControllerCubeRequest $request) {
        $metamodel = data_controller_get_metamodel();

        return $metamodel->getCubeByDatasetName($request->datasetName);
    }

  /**
   * @param CubeQueryRequest $request
   * @param CubeMetaData $cube
   * @param array $columnNames
   * @throws IllegalArgumentException
   */
  protected function prepareCubeRequestColumns(CubeQueryRequest $request, CubeMetaData $cube, array $columnNames) {
        $metamodel = data_controller_get_metamodel();

        foreach ($columnNames as $requestColumnIndex => $columnName) {
            list($elementName, $subElementName, $propertyName) = ParameterHelper::splitName($columnName);

            list($referencedDatasetName, $referencedElementName) = ReferencePathHelper::splitReference($elementName);
            // checking that referenced cube exists
            $referencedCube = isset($referencedDatasetName)
                ? $metamodel->getCubeByDatasetName($referencedDatasetName)
                : NULL;

            if (isset($subElementName)) {
                if (isset($referencedCube)) {
                    throw new IllegalArgumentException(t('Referenced dimensions are not supported'));
                }

                // checking the level exists
//                $dimension = $cube->getDimension($elementName);
//                $level = $dimension->getLevel($subElementName);

                // adding the level
                if (isset($propertyName)) {
                    $request->addDimensionLevelProperty($requestColumnIndex, $elementName, $subElementName, $propertyName);
                }
                else {
                    $request->addDimensionLevel($requestColumnIndex, $elementName, $subElementName);
                }
            }
            else {
                $selectedRequest = $request;
                if (isset($referencedCube)) {
                    // checking the measure exists in the referenced cube
//                    $measure = $referencedCube->getMeasure($referencedElementName);
                    // preparing referenced request
                    $selectedRequest = $request->registerReferencedRequest($referencedCube->name);
                }
//                else {
                    // checking the measure exists
//                    $measure = $cube->getMeasure($referencedElementName);
//                }
                // adding the measure
                $selectedRequest->addMeasure($requestColumnIndex, $referencedElementName);
            }
        }
    }

  /**
   * @param CubeQueryRequest $request
   * @param CubeMetaData $cube
   * @param array|NULL $parameters
   * @throws IllegalArgumentException
   */
  protected function prepareCubeRequestQueries(CubeQueryRequest $request, CubeMetaData $cube, array $parameters = NULL) {
        if (!isset($parameters)) {
            return;
        }

        $metamodel = data_controller_get_metamodel();

        foreach ($parameters as $parameterName => $parameterValues) {
            list($elementName, $subElementName, $propertyName) = ParameterHelper::splitName($parameterName);

            list($referencedDatasetName, $referencedElementName) = ReferencePathHelper::splitReference($elementName);

            // checking that referenced cube exists
            $referencedCube = isset($referencedDatasetName)
                ? $metamodel->getCubeByDatasetName($referencedDatasetName)
                : NULL;


            if (isset($subElementName)) {
                if (isset($referencedCube)) {
                    throw new IllegalArgumentException(t('Referenced dimensions are not supported'));
                }

                // checking the level exists
//                $dimension = $cube->getDimension($elementName);
//                $level = $dimension->getLevel($subElementName);

                // adding the dimension level related query
                $request->addDimensionLevelPropertyQueryValues($elementName, $subElementName, $propertyName, $parameterValues);
            }
            else {
                $selectedRequest = $request;
                if (isset($referencedCube)) {
                    $measure = $referencedCube->findMeasure($referencedElementName);

                    $selectedRequest = $request->registerReferencedRequest($referencedCube->name);
                }
                else {
                    // checking if the measure exists
                    $measure = $cube->findMeasure($referencedElementName);
                }

                if (isset($measure)) {
                    // adding measure query
                    $selectedRequest->addMeasureQueryValues($referencedElementName, $parameterValues);
                }
                else {
                    // adding dataset column-based query
                    $selectedRequest->addSourceDatasetPropertyQueryValues($referencedElementName, $parameterValues);
                }
            }
        }
    }

  /**
   * @param CubeQueryRequest $request
   * @param CubeMetaData $cube
   * @throws IllegalArgumentException
   * @throws UnsupportedOperationException
   */
  protected function useApplicableCubeRegions(CubeQueryRequest $request, CubeMetaData $cube) {
        $metamodel = data_controller_get_metamodel();

        if (!isset($cube->regions)) {
            return;
        }

        // FIXME add support for measures in query list. Selected region needs to suport not only returning measures but also querying onces
        $isExactMatchRequired = FALSE;
        if (isset($request->measures)) {
            foreach ($request->measures as $requestMeasure) {
                $measureName = $requestMeasure->measureName;

                $cubeMeasure = $cube->findMeasure($measureName);
                if (isset($cubeMeasure) && isset($cubeMeasure->aggregationType)) {
                    switch ($cubeMeasure->aggregationType) {
                        case MeasureTypes::ADDITIVE:
                            break;
                        case MeasureTypes::SEMI_ADDITIVE:
                        case MeasureTypes::NON_ADDITIVE:
                            $isExactMatchRequired = TRUE;
                            break;
                        default:
                            throw new UnsupportedOperationException(t(
                                'Unsupported measure aggregation type: @measureAggregationType',
                                array('@measureAggregationType' => $cubeMeasure->aggregationType)));
                    }
                }
            }
        }

        // collecting possible eligible regions
        $eligibleRegionNames = NULL;
        foreach ($cube->regions as $regionName => $region) {
            // checking if the region supports all requested measures
            if (isset($request->measures)) {
                foreach ($request->measures as $requestMeasure) {
                    if (!isset($region->measures[$requestMeasure->measureName])) {
                        continue 2;
                    }
                }
            }

            $eligibleRegionNames[] = $regionName;
        }
        if (!isset($eligibleRegionNames)) {
            return;
        }

        // filtering eligible regions based on requested or queried dimensions
        if (isset($request->dimensions)) {
            $this->excludeIneligibleRegions($cube, $eligibleRegionNames, $request->dimensions, $isExactMatchRequired);
        }
        if (isset($request->queries)) {
            $this->excludeIneligibleRegions($cube, $eligibleRegionNames, $request->queries, $isExactMatchRequired);
        }

        // do we still have any regions which could be used for the request
        if (count($eligibleRegionNames) === 0) {
            return;
        }

        // we select first region suitable for the request
        $selectedRegionName = reset($eligibleRegionNames);
        $selectedRegion = $cube->regions->$selectedRegionName;

        // preparing new cube configuration
        $regionCube = new CubeMetaData();
        $regionCube->name = "{$cube->name}_using_{$selectedRegionName}_region";
        // source dataset
        $regionCube->sourceDatasetName = $selectedRegion->datasetName;
        // dimensions
        if (isset($cube->dimensions)) {
            foreach ($cube->dimensions as $dimension) {
                $dimensionName = $dimension->name;

                if (!isset($selectedRegion->dimensions->$dimensionName)) {
                    continue;
                }

                $regionCubeDimension = $regionCube->registerDimension($dimensionName);

                $selectedRegionDimension = $selectedRegion->dimensions->$dimensionName;
                if (isset($selectedRegionDimension->levels)) {
                    // we need to prepare new dimension which contains levels which are supported by this region
                    $sourceLevel = NULL;
                    $isSelectedLevelFound = FALSE;
                    foreach ($dimension->levels as $level) {
                        $levelName = $level->name;

                        if (!$isSelectedLevelFound && isset($level->sourceColumnName)) {
                            $sourceLevel = $level;
                        }

                        $isLevelPresent = isset($selectedRegionDimension->levels->$levelName);
                        if ($isLevelPresent) {
                            if ($isSelectedLevelFound) {
                                throw new UnsupportedOperationException(t(
                                	"Only one level is supported yet for each dimension in '@selectedRegionName' region of '@cubeName' cube",
                                    array('@selectedRegionName' => $selectedRegionName, '@cubeName' => $cube->publicName)));
                            }

                            $isSelectedLevelFound = TRUE;
                        }

                        if ($isSelectedLevelFound) {
                            $regionLevel = $regionCubeDimension->registerLevel($levelName);
                            $regionLevel->initializeFrom($level);

                            if ($isLevelPresent) {
                                $regionLevel->sourceColumnName = $sourceLevel->sourceColumnName;
                            }
                            elseif (isset($regionLevel->sourceColumnName)) {
                                // we cannot support source key on consecutive levels. It is only supported for 'virtual' cubes
                                unset($regionLevel->sourceColumnName);
                            }
                        }
                    }
                }
                else {
                    $regionCubeDimension->initializeFrom($dimension);
                }
            }
        }
        // measures
        if (isset($request->measures)) {
            foreach ($request->measures as $requestMeasure) {
                $measureName = $requestMeasure->measureName;

                $cubeMeasure = $cube->getMeasure($measureName);

                $regionMeasure = $regionCube->registerMeasure($measureName);
                $regionMeasure->initializeFrom($cubeMeasure);
            }
        }

        // FIXME the following code will throw an exception if we try to reuse the same region during execution of a PHP script
        // registering the cube in meta model
        $regionCube->temporary = TRUE;
        // FIXME when loading meta model automatically create cubes for all regions
        $metamodel->registerCube($regionCube);

        // updating the request to use new cube
        LogHelper::log_notice(t("Using '@selectedRegionName' region of '@cubeName' cube", array('@selectedRegionName' => $selectedRegionName, '@cubeName' => $cube->name)));
        LogHelper::log_info(t('Creating temporary cube to satisfy this request: @regionCubeName', array('@regionCubeName' => $regionCube->name)));
        // FIXME create new request and delete the following method in request class
        $request->setCubeName($regionCube->name);
    }

  /**
   * @param CubeMetaData $cube
   * @param $eligibleRegionNames
   * @param array $requestDimensions
   * @param $isExactMatchRequired
   */
  protected function excludeIneligibleRegions(CubeMetaData $cube, &$eligibleRegionNames, array $requestDimensions, $isExactMatchRequired) {
        // checking each request dimension
        foreach ($requestDimensions as $requestDimension) {
            $requestDimensionName = $requestDimension->dimensionName;
            $requestDimensionLevelName = $requestDimension->levelName;

            // checking each eligible region
            foreach ($eligibleRegionNames as $eligibleRegionNameIndex => $eligibleRegionName) {
                $eligibleRegion = $cube->regions->$eligibleRegionName;

                $isRegionEligible = FALSE;
                // is the dimension present in the region?
                if (isset($eligibleRegion->dimensions->$requestDimensionName)) {
                    $eligibleRegionDimension = $eligibleRegion->dimensions->$requestDimensionName;

                    // if levels are defined we need to check if any aggregation is done on required level or any lower level
                    if (isset($eligibleRegionDimension->levels)) {
                        $dimension = $cube->findDimension($requestDimensionName);
                        if (isset($dimension)) {
                            $matchingLevelName = NULL;
                            foreach ($dimension->levels as $level) {
                                $levelName = $level->name;

                                if (isset($eligibleRegionDimension->levels->$levelName)) {
                                    $matchingLevelName = $levelName;
                                }

                                if ($requestDimensionLevelName === $levelName) {
                                    if (isset($matchingLevelName)) {
                                        if ($isExactMatchRequired) {
                                            if ($requestDimensionLevelName === $matchingLevelName) {
                                                $isRegionEligible = TRUE;
                                            }
                                        }
                                        else {
                                            $isRegionEligible = TRUE;
                                        }
                                    }

                                    break;
                                }
                            }
                        }
                    }
                    else {
                        $isRegionEligible = TRUE;
                    }
                }

                if (!$isRegionEligible) {
                    unset($eligibleRegionNames[$eligibleRegionNameIndex]);
                }
            }
        }
    }
}
