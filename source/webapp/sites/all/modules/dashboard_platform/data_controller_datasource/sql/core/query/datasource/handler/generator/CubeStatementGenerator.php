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


class CubeStatementGenerator extends AbstractObject {

  /**
   * @var string
   */
  protected static $TABLE_ALIAS__REFERENCED = 'r';

    /*
     * Utility function for prepareSelectedCubeQueryStatement() method.
     * Collects information about selected columns and applied conditions for each dataset for further join operation
     */
  /**
   * @param array|NULL $datasetConfigs
   * @param $index
   * @param DatasetMetaData|NULL $dataset
   * @param null $columnName
   * @param AbstractConditionSection|NULL $condition
   * @throws IllegalArgumentException
   * @throws IllegalStateException
   */
  private function registerDatasetConfig(array &$datasetConfigs = NULL, $index, DatasetMetaData $dataset = NULL, $columnName = NULL, AbstractConditionSection $condition = NULL) {
        if (isset($datasetConfigs[$index])) {
            $datasetConfig = $datasetConfigs[$index];
        }
        else {
            // TODO create a class
            $datasetConfig = new stdClass();
            $datasetConfig->dataset = NULL;
            $datasetConfig->usedColumnNames = NULL;
            $datasetConfig->conditions = NULL;

            $datasetConfigs[$index] = $datasetConfig;
        }

        if (isset($dataset)) {
            if (isset($datasetConfig->dataset)) {
                if ($datasetConfig->dataset->name !== $dataset->name) {
                    throw new IllegalStateException(t(
                    	'Inconsistent dataset configuration: [@datasetName, @tableDatasetName]',
                        array('@datasetName' => $dataset->publicName, '@tableDatasetName' => $datasetConfig->dataset->publicName)));
                }
            }
            else {
                $datasetConfig->dataset = $dataset;
            }
        }

        if (isset($columnName)) {
            ArrayHelper::addUniqueValue($datasetConfig->usedColumnNames, $columnName);
        }

        if (isset($condition)) {
            $datasetConfig->conditions[] = $condition;
        }
    }

    /*
     * Prepares a statement object which represents a request to facts table
     */
  /**
   * @param AbstractSQLDataSourceQueryHandler $datasourceHandler
   * @param DataControllerCallContext $callcontext
   * @param CubeQueryRequest $request
   * @return Statement
   * @throws IllegalArgumentException
   * @throws IllegalStateException
   * @throws UnsupportedOperationException
   */
  protected function prepareSelectedCubeQueryStatement(AbstractSQLDataSourceQueryHandler $datasourceHandler, DataControllerCallContext $callcontext, CubeQueryRequest $request) {
        $metamodel = data_controller_get_metamodel();

        // loading cube configuration
        $cubeName = $request->getCubeName();
        $cube = $metamodel->getCube($cubeName);

        // loading cube source configuration
        $cubeDatasetName = $cube->sourceDatasetName;
        $cubeDataset = $metamodel->getDataset($cubeDatasetName);

        // table alias prefix for level datasets
        $TABLE_ALIAS__SOURCE = 's';
        $tableIndex = 1;
        if (isset($cube->dimensions)) {
            foreach ($cube->dimensions as $dimension) {
                $tableIndex += $dimension->getLevelCount();
            }
        }

        // to store configuration for each accessed table
        $datasetConfigs = NULL;

        // preparing cube source dataset configuration
        $this->registerDatasetConfig($datasetConfigs, 0, $cubeDataset, NULL, NULL);

        // statement for aggregation portion of final sql
        $aggrStatement = new Statement();

        // adding support for source dataset property queries
        $sourceDatasetPropertyQueries = $request->findSourceDatasetPropertyQueries();
        if (isset($sourceDatasetPropertyQueries)) {
            foreach ($sourceDatasetPropertyQueries as $sourceDatasetPropertyQuery) {
                $propertyName = $sourceDatasetPropertyQuery->propertyName;
                foreach ($sourceDatasetPropertyQuery->values as $propertyValue) {
                    $this->registerDatasetConfig($datasetConfigs, 0, NULL, $propertyName, NULL);
                    $aggrStatement->conditions[] = new WhereConditionSection(
                        $TABLE_ALIAS__SOURCE . '0',
                        $propertyName,
                        new ExactConditionSectionValue(
                            $datasourceHandler->formatOperatorValue($callcontext, $request, $cubeDataset->name, $propertyName, $propertyValue),
                          $propertyValue),
                        $cubeDataset->getColumn($propertyName)->type->applicationType??null
                      );
                }
            }
        }

        // preparing list of columns which are required to group data for the aggregation
        $aggrSelectColumns = NULL;
        if (isset($cube->dimensions)) {
            foreach ($cube->dimensions as $dimension) {
                $dimensionName = $dimension->name;

                $queryDimension = $request->findDimensionQuery($dimensionName);
                $outputDimension = $request->findDimension($dimensionName);

                // processing ONLY dimensions which are part of request query or output
                if (!isset($queryDimension) && !isset($outputDimension)) {
                    continue;
                }

                $isDimensionLevelUsed = FALSE;

                // We navigate starting from the highest level because it is possible to have several references to source
                // From efficiency prospective we need to use reference to source from the highest level possible
                // because we would not need to join with lower level datasets
                // Example (year_id and date_id columns are in source):
                //     * efficient way: year->SOURCE
                //     * inefficient way: year->quarter->month->date->SOURCE
                $levels = $dimension->levels;
                for ($i = count($levels) - 1; $i >= 0; $i--) {
                    $level = $levels[$i];
                    $levelName = $level->name;

                    $isQueryDimensionLevel = isset($queryDimension) && ($queryDimension->levelName === $levelName);
                    $isOutputDimensionLevel = isset($outputDimension) && ($outputDimension->levelName === $levelName);
                    if (!$isQueryDimensionLevel && !$isOutputDimensionLevel && !$isDimensionLevelUsed) {
                        continue;
                    }

                    $lowerLevelKeyColumnName = isset($level->sourceColumnName)
                        ? NULL
                        : (isset($level->parentKey) ? $level->parentKey : $level->key);

                    // joining with current level dataset ... if necessary
                    $queryPropertyNames = isset($queryDimension) ? $queryDimension->getPropertyNames() : NULL;
                    if ($isDimensionLevelUsed || ($isQueryDimensionLevel && isset($queryPropertyNames))) {
                        $levelDataset = $metamodel->getDataset($level->datasetName);

                        // registering the level dataset
                        $tableIndex--;
                        $this->registerDatasetConfig($datasetConfigs, $tableIndex, $levelDataset, NULL, NULL);

                        if ($isQueryDimensionLevel && isset($queryPropertyNames)) {
                            foreach ($queryDimension->values as $propertyValue) {
                                if (isset($propertyValue->name)) {
                                    $this->registerDatasetConfig($datasetConfigs, $tableIndex, NULL, $propertyValue->name, NULL);
                                }

                                $conditionPropertyName = isset($propertyValue->name) ? $propertyValue->name : $levelDataset->getKeyColumn()->name;
                                foreach ($propertyValue->values as $value) {
                                    $aggrStatement->conditions[] = new WhereConditionSection(
                                        $TABLE_ALIAS__SOURCE . $tableIndex,
                                        $conditionPropertyName,
                                        new ExactConditionSectionValue(
                                            $datasourceHandler->formatOperatorValue($callcontext, $request, $levelDataset->name, $conditionPropertyName, $value)));
                                }
                            }
                        }
                    }

                    // adding returning columns
                    if ($isOutputDimensionLevel) {
                        $responseColumnName = ParameterHelper::assembleParameterName($outputDimension->dimensionName, $outputDimension->levelName);
                        $databaseColumnName = ParameterHelper::assembleDatabaseColumnName(
                            $datasourceHandler->getMaximumEntityNameLength(),
                            ($request->referenced ? ReferencePathHelper::assembleReference($cubeDataset->name, $outputDimension->dimensionName) : $outputDimension->dimensionName),
                            $outputDimension->levelName);
                        $callcontext->columnMapping[$databaseColumnName] = $responseColumnName;

                        if (isset($level->sourceColumnName)) {
                            $aggrSelectColumns[0][] = new ColumnSection(
                                ReferencePathHelper::assembleDatabaseColumnName($datasourceHandler->getMaximumEntityNameLength(), $level->sourceColumnName),
                                $databaseColumnName);
                            $this->registerDatasetConfig($datasetConfigs, 0, NULL, $level->sourceColumnName, NULL);
                        }
                        else {
                            $lowerLevelTableIndex = $tableIndex - 1;
                            $aggrSelectColumns[$lowerLevelTableIndex][] = new ColumnSection($lowerLevelKeyColumnName, $databaseColumnName);
                            $this->registerDatasetConfig($datasetConfigs, $lowerLevelTableIndex, NULL, $lowerLevelKeyColumnName, NULL);
                        }
                    }

                    if (isset($level->sourceColumnName)) {
                        // joining with source
                        if ($isDimensionLevelUsed || $isQueryDimensionLevel) {
                            $this->registerDatasetConfig($datasetConfigs, 0, NULL, $level->sourceColumnName, NULL);

                            if ($isQueryDimensionLevel && !isset($queryPropertyNames)) {
                                // applying conditions to master source
                                foreach ($queryDimension->values as $propertyValue) {
                                    foreach ($propertyValue->values as $value) {
                                        $aggrStatement->conditions[] = new WhereConditionSection(
                                            $TABLE_ALIAS__SOURCE . '0',
                                            $level->sourceColumnName,
                                            new ExactConditionSectionValue(
                                                $datasourceHandler->formatOperatorValue($callcontext, $request, $cubeDataset->name, $level->sourceColumnName, $value)));
                                    }
                                }
                            }
                            else {
                                // linking the level dataset with master source
                                $this->registerDatasetConfig(
                                    $datasetConfigs,
                                    $tableIndex, NULL,
                                    $level->key,
                                    new JoinConditionSection(
                                        $level->key,
                                        new TableColumnConditionSectionValue($TABLE_ALIAS__SOURCE . '0', $level->sourceColumnName)));
                            }
                        }

                        // we do not need to go through rest of the levels
                        break;
                    }
                    else {
                        if ($isQueryDimensionLevel && !isset($queryPropertyNames)) {
                            $lowerLevelTableIndex = $tableIndex - 1;
                            $this->registerDatasetConfig($datasetConfigs, $lowerLevelTableIndex, NULL, $lowerLevelKeyColumnName, NULL);
                            $lowerlevelDatasetName = $levels[$i - 1]->datasetName;
                            foreach ($queryDimension->values as $propertyValue) {
                                foreach ($propertyValue->value as $value) {
                                    $aggrStatement->conditions[] = new WhereConditionSection(
                                        $TABLE_ALIAS__SOURCE . $lowerLevelTableIndex,
                                        $lowerLevelKeyColumnName,
                                        new ExactConditionSectionValue(
                                            $datasourceHandler->formatOperatorValue($callcontext, $request, $lowerlevelDatasetName, $lowerLevelKeyColumnName, $value)));
                                }
                            }
                        }

                        // FIXME simplify this condition. We just need to check if current level dataset is used
                        if ($isDimensionLevelUsed || ($isQueryDimensionLevel && isset($queryPropertyNames))) {
                            // joining with lower level
                            $lowerLevelTableIndex = $tableIndex - 1;
                            $this->registerDatasetConfig($datasetConfigs, $lowerLevelTableIndex, NULL, $lowerLevelKeyColumnName, NULL);
                            $this->registerDatasetConfig(
                                $datasetConfigs,
                                $tableIndex, NULL,
                                $level->key,
                                new JoinConditionSection(
                                    $level->key,
                                    new TableColumnConditionSectionValue($TABLE_ALIAS__SOURCE . $lowerLevelTableIndex, $lowerLevelKeyColumnName)));
                        }
                    }

                    $isDimensionLevelUsed = TRUE;
                }
            }
        }

        // preparing list of measures which are calculated in the aggregation, preparing support for measure conditions
        $aggrSelectMeasureColumns = NULL;
        foreach ($cube->measures as $cubeMeasure) {
            $measureName = $cubeMeasure->name;

            $selectedMeasure = $request->findMeasure($measureName);
            $queriedMeasure = $request->findMeasureQuery($measureName);
            if (!isset($selectedMeasure) && !isset($queriedMeasure)) {
                continue;
            }

            if ($request->referenced) {
                $measureName = ReferencePathHelper::assembleReference($cubeDataset->name, $measureName);
            }
            $databaseColumnName = ParameterHelper::assembleDatabaseColumnName(
                $datasourceHandler->getMaximumEntityNameLength(), $measureName);

            $columnSection = new CompositeColumnSection($cubeMeasure->function, $databaseColumnName);

            if (isset($selectedMeasure)) {
                $callcontext->columnMapping[$databaseColumnName] = $measureName;
                $aggrSelectMeasureColumns[] = $columnSection;
            }

            if (isset($queriedMeasure)) {
                foreach ($queriedMeasure->values as $measureValue) {
                    $aggrStatement->havingConditions[] = new HavingConditionSection(
                        $columnSection,
                        new ExactConditionSectionValue(
                            $datasourceHandler->formatOperatorValue($callcontext, $request, $cubeDataset->name, NULL, $measureValue)));
                }
            }

            // looking for possible columns in the measure function. We need to retrieve those from the database
            $columnNames = $columnSection->parseColumns();
            if (isset($columnNames)) {
                foreach ($columnNames as $columnName) {
                    $this->registerDatasetConfig($datasetConfigs, 0, NULL, $columnName, NULL);
                }
            }
        }

        // sorting configuration to support joins in correct order
        ksort($datasetConfigs, SORT_NUMERIC);

        // preparing dataset source statements
        foreach ($datasetConfigs as $orderIndex => $datasetConfig) {
            $tableStatement = $datasourceHandler->prepareDatasetSourceStatement($callcontext, $datasetConfig->dataset, $datasetConfig->usedColumnNames);

            // adding join conditions
            if (isset($datasetConfig->conditions)) {
                foreach ($datasetConfig->conditions as $condition) {
                    $tableStatement->getColumnTable($condition->subjectColumnName)->conditions[] = $condition;
                }
            }

            // we do not need to return any columns from the table by default
            foreach ($tableStatement->tables as $table) {
                if (isset($table->columns)) {
                    foreach ($table->columns as $column) {
                        $column->visible = FALSE;
                    }
                }
                else {
                    $table->columns = []; // We do not need any columns
                }
            }
            // preparing the table columns which we want to return
            if (isset($aggrSelectColumns[$orderIndex])) {
                $tableSelectColumns = $aggrSelectColumns[$orderIndex];
              /**
               * @var AbstractSelectColumnSection $tableSelectColumn
               */
                foreach ($tableSelectColumns as $tableSelectColumn) {
                    // looking for a table in the statement which provides the column for SELECT section
                    $tableSection = $tableStatement->getColumnTable($tableSelectColumn->name);
                    $attachedColumn = $tableSelectColumn->attachTo($tableSection);

                    $aggrStatement->groupByColumns[] = new GroupByColumnSection($attachedColumn);
                }
            }

            // preparing measures which we want to return. Adding those measures to facts table
            if (($orderIndex == 0) && isset($aggrSelectMeasureColumns)) {
              /**
               * @var AbstractSelectColumnSection $tableSelectMeasureColumn
               */
                foreach ($aggrSelectMeasureColumns as $tableSelectMeasureColumn) {
                    $columnNames = $tableSelectMeasureColumn->parseColumns();
                    // searching which table contains the column
                    $tableSection = NULL;
                    if (isset($columnNames)) {
                        foreach ($columnNames as $columnName) {
                            $formattedColumnAlias = ReferencePathHelper::assembleDatabaseColumnName(
                                $datasourceHandler->getMaximumEntityNameLength(), $columnName);
                            foreach ($tableStatement->tables as $table) {
                                if ($table->findColumnByAlias($formattedColumnAlias) != NULL) {
                                    if (isset($tableSection)) {
                                        if ($tableSection->alias !== $table->alias) {
                                            // FIXME we should not have such functionality
                                            // checking if the same column is used for several times in a table under different aliases
                                            $tableSectionColumns = $tableSection->findColumns($formattedColumnAlias);
                                            $tableColumns = $table->findColumns($formattedColumnAlias);
                                            $isTableSelected = FALSE;
                                            if (($tableSectionColumns > 0) && ($tableColumns > 0)) {
                                                if ($tableSectionColumns > $tableColumns) {
                                                    $tableSection = $table;
                                                    $isTableSelected = TRUE;
                                                }
                                                elseif ($tableColumns > $tableSectionColumns) {
                                                    $isTableSelected = TRUE;
                                                }
                                            }

                                            if (!$isTableSelected) {
                                                throw new UnsupportedOperationException(t('Aggregation function bases on several tables'));
                                            }
                                        }
                                    }
                                    else {
                                        $tableSection = $table;
                                    }
                                }
                            }
                        }
                    }
                    if (!isset($tableSection)) {
                        $tableSection = $tableStatement->tables[0];
                    }

                    $tableSelectMeasureColumn->attachTo($tableSection);
                }
            }

            // updating join statement table aliases
            $sourceTableAlias = $TABLE_ALIAS__SOURCE . $orderIndex;
            foreach ($tableStatement->tables as $table) {
                $oldTableAlias = $table->alias;
                $newTableAlias = $sourceTableAlias . (isset($oldTableAlias) ? '_' . $oldTableAlias : '');
                $tableStatement->updateTableAlias($oldTableAlias, $newTableAlias);

                // updating statement conditions which are used to join levels
                foreach ($datasetConfigs as $nextOrderIndex => $nextDatasetConfig) {
                    if (($nextOrderIndex <= $orderIndex) || !isset($nextDatasetConfig->conditions)) {
                        continue;
                    }

                    foreach ($nextDatasetConfig->conditions as $condition) {
                        if (($condition instanceof JoinConditionSection)
                                && ($condition->joinValue instanceof TableColumnConditionSectionValue)
                                && ($condition->joinValue->tableAlias === $sourceTableAlias)
                                && (($table->findColumn($condition->joinValue->columnName) != NULL) || (count($tableStatement->tables) === 1))) {
                            $condition->joinValue->tableAlias = $newTableAlias;
                        }
                    }
                }

                // updating aggregation statement conditions
                if (isset($aggrStatement->conditions)) {
                    foreach ($aggrStatement->conditions as $condition) {
                        if (($condition->subjectTableAlias === $sourceTableAlias) && ($table->findColumn($condition->subjectColumnName) != NULL)) {
                            // checking if any other table in the statement support the column as an alias
                            $otherColumnFound = FALSE;
                            foreach ($tableStatement->tables as $subjectColumnTable) {
                                $subjectColumn = $subjectColumnTable->findColumnByAlias($condition->subjectColumnName);
                                if (isset($subjectColumn) && ($subjectColumn instanceof ColumnSection)) {
                                    if ($subjectColumnTable->alias != $table->alias) {
                                        $condition->subjectTableAlias = $sourceTableAlias . (isset($subjectColumnTable->alias) ? '_' . $subjectColumnTable->alias : '');
                                        $condition->subjectColumnName = $subjectColumn->name;

                                        $otherColumnFound = TRUE;
                                    }
                                }
                            }

                            if (!$otherColumnFound) {
                                $condition->subjectTableAlias = $newTableAlias;
                            }
                        }
                    }
                }
            }

            $aggrStatement->merge($tableStatement);
        }

        return $aggrStatement;
    }

  /**
   * @param AbstractSQLDataSourceQueryHandler $datasourceHandler
   * @param DataControllerCallContext $callcontext
   * @param Statement $combinedStatement
   * @param array $datasetMappedCubeRequests
   * @param ReferenceLink $link
   * @throws IllegalArgumentException
   * @throws IllegalStateException
   * @throws UnsupportedOperationException
   */
  protected function prepareReferencedCubeQueryStatement(
        AbstractSQLDataSourceQueryHandler $datasourceHandler, DataControllerCallContext $callcontext,
            Statement $combinedStatement, array $datasetMappedCubeRequests, ReferenceLink $link) {

        if (!isset($link->nestedLinks)) {
            return;
        }

        $metamodel = data_controller_get_metamodel();

        foreach ($link->nestedLinks as $referencedLink) {
            $referencedRequest = NULL;
            if (isset($datasetMappedCubeRequests[$referencedLink->dataset->name])) {
                $referencedRequest = clone $datasetMappedCubeRequests[$referencedLink->dataset->name];
            }
            else {
                // checking if there is corresponding cube for the referenced dataset
                $possibleReferencedCube = $metamodel->findCubeByDatasetName($referencedLink->dataset->name);
                if (isset($possibleReferencedCube)) {
                    $referencedRequest = new CubeQueryRequest($possibleReferencedCube->name);
                    $referencedRequest->referenced = TRUE;

                    $datasetMappedCubeRequests[$possibleReferencedCube->sourceDatasetName] = $referencedRequest;
                }
            }

            if (isset($referencedRequest)) {
                // preparing parent cube
                $parentRequest = $datasetMappedCubeRequests[$link->dataset->name];
                $parentCubeName = $parentRequest->getCubeName();
                $parentCube = $metamodel->getCube($parentCubeName);

                // preparing referenced cube
                $referencedCubeName = $referencedRequest->getCubeName();
                $referencedCube = $metamodel->getCube($referencedCubeName);

                // adding required dimensions
                $joinConditions = NULL;
                foreach ($referencedLink->parentColumnNames as $columnIndex => $parentColumnName) {
                    // looking for a dimension in parent cube
                    list($parentDimension, /* ignoring level index */) = $parentCube->getDimensionAndLevelIndexBySourceColumnName($parentColumnName);

                    // looking for a dimension in referenced cube
                    $referencedColumnName = $referencedLink->columnNames[$columnIndex];
                    list($referencedDimension, /* ignoring level index */) = $referencedCube->getDimensionAndLevelIndexBySourceColumnName($referencedColumnName);

                    // checking if this dimension is part of query portion of parent request
                    $parentRequestDimensionQuery = $parentRequest->findDimensionQuery($parentDimension->name);
                    if (isset($parentRequestDimensionQuery)) {
                        // preparing level used in this query
                        $parentRequestDimensionLevelIndex4Query = $parentDimension->getLevelIndex($parentRequestDimensionQuery->levelName);

                        // preparing level to be used for the query in referenced request (using the same level index as for parent dimension)
                        $referencedDimensionLevel4Query = $referencedDimension->levels[$parentRequestDimensionLevelIndex4Query];

                        // copying the query request to referenced cube
                        $referencedRequestDimensionQuery = new __CubeQueryRequest_DimensionQuery($referencedDimension->name, $referencedDimensionLevel4Query->name);
                        $referencedRequestDimensionQuery->values = $parentRequestDimensionQuery->values;
                        $referencedRequest->importDimensionQueryFrom($referencedRequestDimensionQuery);
                    }

                    // checking if there is a related query for parent column name
                    $parentRequestSourceDatasetQuery = $parentRequest->findSourceDatasetPropertyQuery($parentColumnName);
                    if (isset($parentRequestSourceDatasetQuery)) {
                        // copying the query request to referenced cube
                        $referencedRequest->addSourceDatasetPropertyQueryValues($referencedColumnName, $parentRequestSourceDatasetQuery->values);
                    }

                    // checking if this dimension is part of parent request
                    $parentRequestDimension = $parentRequest->findDimension($parentDimension->name);
                    if (!isset($parentRequestDimension)) {
                        // because this dimension is not in list of returned columns we should not use it to link with referenced cube
                        continue;
                    }
                    $selectedParentDimensionLevelIndex = $parentDimension->getLevelIndex($parentRequestDimension->levelName);
                    $selectedParentDimensionLevel = $parentDimension->levels[$selectedParentDimensionLevelIndex];

                    // preparing level from referenced cube (using the same level index as for parent dimension)
                    $selectedReferencedDimensionLevel = $referencedDimension->levels[$selectedParentDimensionLevelIndex];

                    $referencedRequest->addDimensionLevel(
                        NULL, // TODO support requestColumnIndex here
                        $referencedDimension->name, $selectedReferencedDimensionLevel->name);

                    $parentDatabaseColumnName = ParameterHelper::assembleDatabaseColumnName(
                        $datasourceHandler->getMaximumEntityNameLength(),
                        ($parentRequest->referenced ? ReferencePathHelper::assembleReference($parentCube->sourceDatasetName, $parentDimension->name) : $parentDimension->name),
                        $selectedParentDimensionLevel->name);

                    $referencedDatabaseColumnName = ParameterHelper::assembleDatabaseColumnName(
                        $datasourceHandler->getMaximumEntityNameLength(),
                        ReferencePathHelper::assembleReference($referencedCube->sourceDatasetName, $referencedDimension->name), $selectedReferencedDimensionLevel->name);

                    $joinConditions[] = new JoinConditionSection(
                        $referencedDatabaseColumnName, new TableColumnConditionSectionValue(self::$TABLE_ALIAS__REFERENCED . $link->linkId, $parentDatabaseColumnName));
                }
                if (!isset($joinConditions)) {
                    throw new IllegalArgumentException(t(
                        "There is no common columns to join '@datasetNameA' and '@datasetNameB' datasets",
                        array('@datasetNameA' => $parentCube->publicName, '@datasetNameB' => $referencedCube->publicName)));
                }

                // preparing aggregation statement for referenced cube
                $referencedAggregationStatement = $this->prepareSelectedCubeQueryStatement($datasourceHandler, $callcontext, $referencedRequest);
                list($isSubqueryRequired, $assembledReferencedCubeSections) = $referencedAggregationStatement->prepareSections(NULL);
                $referencedCubeSubquerySection = new SubquerySection(
                    Statement::assemble($isSubqueryRequired, NULL, $assembledReferencedCubeSections, Statement::$INDENT_LEFT_OUTER_JOIN_SUBQUERY, FALSE),
                    self::$TABLE_ALIAS__REFERENCED . $referencedLink->linkId);

                // preparing columns which are returned by referenced aggregation
                foreach ($referencedAggregationStatement->tables as $table) {
                    if (!isset($table->columns)) {
                        continue;
                    }

                    foreach ($table->columns as $column) {
                        if (!$column->visible) {
                            continue;
                        }

                        $referencedCubeSubquerySection->columns[] = new ColumnSection($column->alias);
                    }
                }

                // linking with parent cube
                foreach ($joinConditions as $joinCondition) {
                    // we do not need to return columns which are used to join with parent cube
                    $referencedCubeSubquerySection->getColumn($joinCondition->subjectColumnName)->visible = FALSE;

                    $referencedCubeSubquerySection->conditions[] = $joinCondition;
                }

                // adding to resulting statement
                $combinedStatement->tables[] = $referencedCubeSubquerySection;

                // applying referenced cubes measure conditions on resulting statement as well
                $measureQueries = $referencedRequest->findMeasureQueries();
                if (isset($measureQueries)) {
                    foreach ($measureQueries as $measureQuery) {
                        $measureName = ReferencePathHelper::assembleReference($referencedCube->sourceDatasetName, $measureQuery->measureName);
                        $measureDatabaseColumnName = ReferencePathHelper::assembleDatabaseColumnName(
                            $datasourceHandler->getMaximumEntityNameLength(), $measureName);

                        foreach ($measureQuery->values as $measureValue) {
                            $combinedStatement->conditions[] = new WhereConditionSection(
                                self::$TABLE_ALIAS__REFERENCED . $referencedLink->linkId,
                                $measureDatabaseColumnName,
                                    new ExactConditionSectionValue(
                                        $datasourceHandler->formatOperatorValue($callcontext, $referencedRequest, $referencedCube->sourceDatasetName, NULL, $measureValue)));
                        }
                    }
                }
            }
            else {
                throw new UnsupportedOperationException(t('Cube joins using intermediate dataset is not supported yet'));

                // preparing statement for intermediate dataset
                $requiredColumnNames = $referencedLink->columnNames;
                $referencedIntermediateDatasetStatement = $datasourceHandler->prepareDatasetSourceStatement($callcontext, $referencedLink->dataset, $requiredColumnNames);

                // adding condition to join with parent statement
                $referencedIntermediateDatasetTableSection = $referencedIntermediateDatasetStatement->tables[0];
                foreach ($referencedLink->columnNames as $columnIndex => $referencedColumnName) {
                    $referencedDatabaseColumnName = $referencedColumnName;

                    $parentColumnName = $referencedLink->parentColumnNames[$columnIndex];
                    $parentDatabaseColumnName = $parentColumnName;

                    $referencedIntermediateDatasetTableSection->conditions[] = new JoinConditionSection(
                        $referencedDatabaseColumnName, new TableColumnConditionSectionValue(self::$TABLE_ALIAS__REFERENCED . $link->linkId, $parentDatabaseColumnName));
                }

                $combinedStatement->merge($referencedIntermediateDatasetStatement);
            }

            // recursively check nested levels
            $this->prepareReferencedCubeQueryStatement($datasourceHandler, $callcontext, $combinedStatement, $datasetMappedCubeRequests, $referencedLink);
        }
    }

  /**
   * @param AbstractSQLDataSourceQueryHandler $datasourceHandler
   * @param DataControllerCallContext $callcontext
   * @param CubeQueryRequest $request
   * @return Statement
   * @throws IllegalArgumentException
   * @throws IllegalStateException
   * @throws UnsupportedOperationException
   */
  public function generateStatement(AbstractSQLDataSourceQueryHandler $datasourceHandler, DataControllerCallContext $callcontext, CubeQueryRequest $request) {
        $statement = $this->prepareSelectedCubeQueryStatement($datasourceHandler, $callcontext, $request);
        if (!isset($request->referencedRequests)) {
            return $statement;
        }

        $combinedStatement = new Statement();

        $metamodel = data_controller_get_metamodel();

        $cubeName = $request->getCubeName();
        $cube = $metamodel->getCube($cubeName);

        $datasetMappedCubeRequests = array($cube->sourceDatasetName => $request);

        // preparing list of reference paths
        $referencePaths = NULL;
        foreach ($request->referencedRequests as $referencedRequest) {
            $referencedCubeName = $referencedRequest->getCubeName();
            $referencedCube = $metamodel->getCube($referencedCubeName);
            $referencedDatasetName = $referencedCube->sourceDatasetName;

            $referencePath = ReferencePathHelper::assembleReference($referencedDatasetName, NULL);
            $referencePaths[$referencePath] = TRUE; // TRUE - required reference

            $datasetMappedCubeRequests[$referencedDatasetName] = $referencedRequest;
        }

        // finding ways to link the referenced cubes
        $linkBuilder = new ReferenceLinkBuilder();
        $link = $linkBuilder->prepareReferenceBranches($cube->sourceDatasetName, $referencePaths);

        // preparing primary cube aggregation statement
        list($isSubqueryRequired, $assembledPrimaryCubeAggregationSections) = $statement->prepareSections(NULL);
        $primaryCubeAggregationTableSection = new SubquerySection(
            Statement::assemble($isSubqueryRequired, NULL, $assembledPrimaryCubeAggregationSections, Statement::$INDENT_SUBQUERY, FALSE),
            self::$TABLE_ALIAS__REFERENCED . $link->linkId);

        // adding columns which are returned by primary aggregation
        foreach ($statement->tables as $table) {
            if (!isset($table->columns)) {
                continue;
            }

            foreach ($table->columns as $column) {
                if (!$column->visible) {
                    continue;
                }

                $primaryCubeAggregationTableSection->columns[] = new ColumnSection($column->alias);
            }
        }

        // registering primary cube statement in resulting statement
        $combinedStatement->tables[] = $primaryCubeAggregationTableSection;

        $this->prepareReferencedCubeQueryStatement($datasourceHandler, $callcontext, $combinedStatement, $datasetMappedCubeRequests, $link);

        return $combinedStatement;
    }
}
