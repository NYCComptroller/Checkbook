<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class ReferenceDimensionDatasetAssembler extends AbstractDatasetSourceAssembler {

    public static $DATASET_SOURCE_ASSEMBLER__TYPE = 'dimension/reference';

    // this class can be called recursively to support referenced datasets
    private static $TABLE_ALIAS_SUFFIX__SEQUENCE = 0;

    public function assemble(AbstractSQLDataSourceQueryHandler $datasourceHandler, DataControllerCallContext $callcontext, DatasetMetaData $dataset, array $columnNames = NULL) {
        $statement = new Statement();

        $TABLE_ALIAS__FACTS = 'dsf';
        $TABLE_ALIAS__COLUMN_JOIN = 'cj';

        $metamodel = data_controller_get_metamodel();

        $cubeName = $dataset->name;
        $cube = $metamodel->getCube($cubeName);

        $factsDataset = $metamodel->getDataset($cube->sourceDatasetName);

        self::$TABLE_ALIAS_SUFFIX__SEQUENCE++;
        $factsTable = new DatasetSection($factsDataset, $TABLE_ALIAS__FACTS . self::$TABLE_ALIAS_SUFFIX__SEQUENCE);
        $statement->tables[] = $factsTable;

        foreach ($dataset->getColumns() as $column) {
            $columnName = $column->name;

            $columnDefaultAlias = NULL;
            if (isset($columnNames)) {
                $columnDefaultAlias = array_search($columnName, $columnNames);
                if ($columnDefaultAlias === FALSE) {
                    continue;
                }

                // fixing the alias if the list of columns is not associative array
                if (is_int($columnDefaultAlias)) {
                    $columnDefaultAlias = NULL;
                }
            }

            // FIXME the following code does not work in the following situation:
            //   1) we need to query a dataset which is defined in GovDashboard using file upload
            //   2) column names are not provided
            //   3) for columns which are references to lookup, value from the primary key from lookup table is returned
            //      even if in definition of the dataset we have columns with the following application type: name@lookup, code @lookup and etc

            $isReferenceUsed = FALSE;
            $handler = DimensionLookupFactory::getInstance()->findHandler($column->type->applicationType);
            if (isset($handler)) {
                // FIXME remove this implementation when we implement 'Executable Tree' functionality
                list($connectedDatasetName, $connectedColumnName) = $handler->adjustReferencePointColumn($metamodel, $factsDataset->name, $columnName);
                if (($connectedDatasetName != $factsDataset->name) || ($connectedColumnName != $columnName)) {
                    self::$TABLE_ALIAS_SUFFIX__SEQUENCE++;
                    $columnJoinAliasPrefix = $TABLE_ALIAS__COLUMN_JOIN . self::$TABLE_ALIAS_SUFFIX__SEQUENCE;

                    $connectedDataset = $metamodel->getDataset($connectedDatasetName);
                    $connectedDatasetKeyColumnName = $connectedDataset->getKeyColumn()->name;

                    // preparing list of columns we want to get from connected dataset
                    $connectedDatasetColumnNames = NULL;
                    ArrayHelper::addUniqueValue($connectedDatasetColumnNames, $connectedDatasetKeyColumnName);
                    ArrayHelper::addUniqueValue($connectedDatasetColumnNames, $connectedColumnName);

                    $connectedStatement = $datasourceHandler->assembleDatasetSourceStatement($callcontext, $connectedDataset, $connectedDatasetColumnNames);
                    $connectedStatement->addTableAliasPrefix($columnJoinAliasPrefix);

                    $connectedTable = $connectedStatement->getColumnTable($connectedColumnName);
                    // registering the column for facts table
                    $factsTableColumn = new ColumnSection($columnName, 'fact_' . $columnName);
                    $factsTableColumn->visible = FALSE;
                    $factsTable->columns[] = $factsTableColumn;
                    // adjusting key column from lookup
                    $connectedDatasetKeyColumn = $connectedTable->getColumn($connectedDatasetKeyColumnName);
                    $connectedDatasetKeyColumn->alias = 'lookup_' . $connectedDatasetKeyColumnName;
                    $connectedDatasetKeyColumn->visible = FALSE;
                    // adjusting value column from lookup
                    $connectedDatasetValueColumn = $connectedTable->getColumn($connectedColumnName);
                    $connectedDatasetValueColumn->alias = $columnName;
                    // the key column could be the same as value column
                    $connectedDatasetValueColumn->visible = TRUE;
                    // new value column which uses composite name as column alias
                    if (isset($columnDefaultAlias)) {
                        $connectedDatasetValueColumn2 = new ColumnSection(
                            $connectedColumnName,
                            ReferencePathHelper::assembleDatabaseColumnName($datasourceHandler->getMaximumEntityNameLength(), $columnDefaultAlias));
                        $connectedDatasetValueColumn2->visible = FALSE;
                        $connectedTable->columns[] = $connectedDatasetValueColumn2;
                    }

                    // linking the lookup table with the facts table
                    $connectedTable->conditions[] = new JoinConditionSection(
                        $connectedDatasetKeyColumn->alias, new TableColumnConditionSectionValue($factsTable->alias, $columnName));

                    $statement->merge($connectedStatement);

                    $isReferenceUsed = TRUE;
                }
            }

            if (!$isReferenceUsed) {
                $factsTable->columns[] = new ColumnSection($columnName);
            }
        }

        return $statement;
    }
}
