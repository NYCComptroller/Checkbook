<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class ContentTypeDatasetSourceAssembler extends AbstractDatasetSourceAssembler {

    public static $DATASET_SOURCE_ASSEMBLER__TYPE = 'drupal/ContentType';

    private static $TABLE_ALIAS__JOIN = 'ct';

    private $isAliasPrefixUsed = NULL;
    private $columnAliasPrefix = NULL;
    private $tableAliasPrefix = NULL;

    public function __construct($config, $tableAliasPrefix = NULL) {
        parent::__construct($config);

        $this->isAliasPrefixUsed = isset($tableAliasPrefix);
        $this->columnAliasPrefix = (isset($tableAliasPrefix) ? ($tableAliasPrefix . '_') : '');
        $this->tableAliasPrefix = self::$TABLE_ALIAS__JOIN . (isset($tableAliasPrefix) ? ('_' . $tableAliasPrefix) : '');
    }

    public function prepareTableAlias($tableIndex) {
        return $this->tableAliasPrefix . $tableIndex;
    }

    public function prepareColumnAlias($columnName) {
        return $this->columnAliasPrefix . $columnName;
    }

    public function assemble(AbstractSQLDataSourceQueryHandler $datasourceHandler, DataControllerCallContext $callcontext, DatasetMetaData $dataset, array $columnNames = NULL) {
        $retrieveAllFields = !isset($columnNames);
        $supportFields = $retrieveAllFields || (count($columnNames) > 0);

        $contentTypeName = $this->config->drupal['type'];

        $maxTableIndex = isset($this->config->supportedTables) ? count($this->config->supportedTables) : 0;
        $nodeTableIndexOffset = 0;
        $nodeRevisionTableIndexOffset = $nodeTableIndexOffset + 1;

        // content type variables
        $contentTypeTableIndex = NULL;
        // pre-arranged table configurations
        $selectedTables = NULL;
        // preparing table configurations which we need to support this request/response
        if (isset($this->config->supportedTables)) {
            $tableIndex = 0;
            foreach ($this->config->supportedTables as $tableName => $supportedTable) {
                // checking if we need to use any fields from the table
                $selectedFields = NULL;
                if ($supportFields) {
                    foreach ($supportedTable->supportedFields as $supportedField) {
                        $fieldName = $supportedField->name;

                        if ($retrieveAllFields) {
                            $selectedFields[] = $fieldName;
                        }
                        else {
                            $columnNamePrefix = $fieldName . '_';
                            foreach ($columnNames as $columnName) {
                                if (($columnName === $fieldName) || (strpos($columnName, $columnNamePrefix) === 0)) {
                                    $selectedFields[] = $fieldName;
                                    break;
                                }
                            }
                        }
                    }
                }

                if ($supportedTable->storage == CONTENT_DB_STORAGE_PER_CONTENT_TYPE) {
                    $contentTypeTableIndex = $tableIndex;
                }

                // TODO use predefined class
                $selectedTable = new stdClass();
                $selectedTable->name = $tableName;
                $selectedTable->selectedFields = $selectedFields;

                $selectedTables[$tableIndex] = $selectedTable;

                $tableIndex++;
            }
        }

        // flags for system fields
        $isNodeIdentifierFieldRequired = $supportFields && ($retrieveAllFields || (array_search('nid', $columnNames) !== FALSE));
        $isVersionIdentifierFieldRequired = $supportFields && ($retrieveAllFields || (array_search('vid', $columnNames) !== FALSE));
        $isTitleFieldRequired = $supportFields && ($retrieveAllFields || (array_search('title', $columnNames) !== FALSE));
        $isBodyFieldRequired = $supportFields && ($retrieveAllFields || (array_search('body', $columnNames) !== FALSE));

        $contentTypeTableConfig = isset($contentTypeTableIndex) ? $selectedTables[$contentTypeTableIndex] : NULL;

        // do we need to use any columns from main content type table?
        $isContentTypeTableUsed = isset($contentTypeTableConfig) && isset($contentTypeTableConfig->selectedFields);
        // checking if we need to use 'node' table
        $isNodeTableRequired = $isTitleFieldRequired;
        // checking if we need to use 'node_revisions' table
        $isNodeRevisionsTableRequired = $isBodyFieldRequired;
        // if node revision table is used ...
        if ($isNodeRevisionsTableRequired) {
            // ... we should not use 'node' table. Title and body could be retrieved from node revision table
            $isNodeTableRequired = FALSE;
        }
        // if 'node' table is not used we need to use content type table to identify required records
        if (!$isNodeTableRequired) {
            if (isset($contentTypeTableIndex)) {
                $isContentTypeTableUsed = TRUE;
            }
            else {
                $isNodeTableRequired = TRUE;
            }
        }
        // checking if we need to filter data by node type instead of records in content type table
        $isNodeTableFilteredByType = $isNodeTableRequired && !$isContentTypeTableUsed;

        $statement = new Statement();
        $systemIdentifierTableSection = NULL;
        $systemPropertyTableSection = NULL;
        $initialRecordSetTableIndex = NULL;

        // adding a table to identify initial set of records for the content type
        if ($isContentTypeTableUsed) {
            $contentTypeTableSection = new TableSection($contentTypeTableConfig->name, $this->prepareTableAlias($contentTypeTableIndex));
            $statement->tables[] = $contentTypeTableSection;
            $this->addTableSelectedFields($contentTypeTableSection, $contentTypeTableConfig);

            $systemIdentifierTableSection = $contentTypeTableSection;
            $initialRecordSetTableIndex = $contentTypeTableIndex;
        }

        // adding node
        if ($isNodeTableRequired) {
            $nodeTableIndex = $maxTableIndex + $nodeTableIndexOffset;
            $nodeTableAlias = $this->prepareTableAlias($nodeTableIndex);

            $nodeTableSection = new TableSection('node', $nodeTableAlias);
            $statement->tables[] = $nodeTableSection;
            if ($isNodeTableFilteredByType) {
                $joinValue = new ExactConditionSectionValue(" = '$contentTypeName'");
                if ($this->isAliasPrefixUsed) {
                    $nodeTableSection->conditions[] = new JoinConditionSection('type', $joinValue);
                }
                else {
                    $statement->conditions[] = new WhereConditionSection($nodeTableAlias, 'type', $joinValue);
                }
            }

            if (isset($initialRecordSetTableIndex)) {
                $nodeTableSection->conditions[] = new JoinConditionSection(
                    'nid', new TableColumnConditionSectionValue($this->prepareTableAlias($contentTypeTableIndex), 'nid'));
            }
            else {
                $systemIdentifierTableSection = $nodeTableSection;
                $initialRecordSetTableIndex = $nodeTableIndex;
            }
            $systemPropertyTableSection = $nodeTableSection;
        }

        // adding support for fields which are stored outside of main content type table
        if (isset($selectedTables)) {
            foreach ($selectedTables as $tableIndex => $selectedTable) {
                $tableName = $selectedTable->name;
                $supportedTable = $this->config->supportedTables[$tableName];

                if ($supportedTable->storage != CONTENT_DB_STORAGE_PER_FIELD) {
                    continue;
                }

                if (isset($selectedTable->selectedFields)) {
                    $tableSection = new TableSection($tableName, $this->prepareTableAlias($tableIndex));
                    $statement->tables[] = $tableSection;
                    $this->addTableSelectedFields($tableSection, $selectedTable);
                    $tableSection->conditions[] = new JoinConditionSection(
                        'vid', new TableColumnConditionSectionValue($this->prepareTableAlias($initialRecordSetTableIndex), 'vid'));
                }
            }
        }

        // adding node revisions
        if ($isNodeRevisionsTableRequired) {
            $nodeRevisionTableIndex = $maxTableIndex + $nodeRevisionTableIndexOffset;

            $nodeRevisionTableSection = new TableSection('node_revisions', $this->prepareTableAlias($nodeRevisionTableIndex));
            $statement->tables[] = $nodeRevisionTableSection;
            $nodeRevisionTableSection->conditions[] = new JoinConditionSection(
                'vid', new TableColumnConditionSectionValue($this->prepareTableAlias($initialRecordSetTableIndex), 'vid'));

            $systemPropertyTableSection = $nodeRevisionTableSection;
        }

        // adding system fields
        if ($isNodeIdentifierFieldRequired && !$this->isAliasPrefixUsed) {
            $systemIdentifierTableSection->columns[] = new ColumnSection('nid', $this->prepareColumnAlias('nid'));
        }
        if ($isVersionIdentifierFieldRequired) {
            $systemIdentifierTableSection->columns[] = new ColumnSection('vid', $this->prepareColumnAlias('vid'));
        }
        if ($isTitleFieldRequired) {
            $systemPropertyTableSection->columns[] = new ColumnSection('title', $this->prepareColumnAlias('title'));
        }
        if ($isBodyFieldRequired) {
            $systemPropertyTableSection->columns[] = new ColumnSection('body', $this->prepareColumnAlias('body'));
        }

        // processing de-referencing for requested fields
        if ($supportFields && isset($selectedTables)) {
            foreach ($selectedTables as $tableIndex => $selectedTable) {
                $tableName = $selectedTable->name;
                $supportedTable = $this->config->supportedTables[$tableName];

                if (isset($selectedTable->selectedFields)) {
                    foreach ($selectedTable->selectedFields as $fieldName) {
                        $supportedField = $supportedTable->supportedFields[$fieldName];
                        $originalFieldName = $supportedField->original_name;

                        $fieldType = $this->config->drupal['fields'][$originalFieldName]['type'];
                        $scriptName = __DIR__ . "/type/$fieldType.php";
                        if (file_exists($scriptName)) {
                            // TODO use hook API here
                            require_once($scriptName);

                            // preparing function name
                            $functionName = "ContentTypeDatasetSourceAssembler_$fieldType";
                            // executing the function to de-refer the field
                            call_user_func_array(
                                $functionName,
                                array($this, $callcontext, $columnNames, $statement, $tableIndex, $supportedField));
                        }
                    }
                }
            }
        }

        return $statement;
    }

    protected function addTableSelectedFields(TableSection $tableSection, $selectedTable) {
        if (!isset($selectedTable->selectedFields)) {
            return;
        }

        $tableName = $selectedTable->name;
        $supportedTable = $this->config->supportedTables[$tableName];

        foreach ($supportedTable->supportedFields as $supportedField) {
            $fieldName = $supportedField->name;

            if (array_search($fieldName, $selectedTable->selectedFields) !== FALSE) {
                $databaseFieldName = $supportedField->column;
                $fieldAlias = $this->prepareColumnAlias($fieldName);
                $tableSection->columns[] = new ColumnSection($databaseFieldName, $fieldAlias);
            }
        }
    }

    public function registerContentTypeInStack(DataControllerCallContext $callcontext, $contentTypeName) {
        if (isset($callcontext->assembler->contentType->stack)
                && (array_search($contentTypeName, $callcontext->assembler->contentType->stack) !== FALSE)) {
            return FALSE;
        }

        $callcontext->assembler->contentType->stack[] = $contentTypeName;

        return TRUE;
    }

    public function unregisterContentTypeFromStack(DataControllerCallContext $callcontext, $contentTypeName) {
        // removing previous registration
        array_pop($callcontext->assembler->contentType->stack);
    }
}
