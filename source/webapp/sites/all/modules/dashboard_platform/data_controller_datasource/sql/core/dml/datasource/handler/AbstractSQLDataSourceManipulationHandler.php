<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractSQLDataSourceManipulationHandler extends AbstractSQLDataSourceHandler implements DataSourceManipulationHandler {

    protected function adjustReferencedDataType4Casting($datasetName, $columnName) {
        return Sequence::getSequenceColumnType()->applicationType;
    }

    protected function executeStatement(DataSourceMetaData $datasource, $sql) {
        $affectedRecordCount = parent::executeStatement($datasource, $sql);

        LogHelper::log_info(t('Execution affected @count record(s)', array('@count' => $affectedRecordCount)));

        return $affectedRecordCount;
    }

    protected function executeManipulationStatementBatch(DataSourceMetaData $datasource, $sqls) {
        $sql = (count($sqls) == 1)
            ? $sqls
            : $this->getExtension('prepareManipulationStatementBatch')->prepare($this, $sqls);

        LogHelper::log_info(new StatementLogMessage('table.DML', $sql));
        return $this->executeStatement($datasource, $sql);
    }

    protected function prepareInsertDatasetRecordStatements(DataControllerCallContext $callcontext, AbstractDatasetManipulationRequest $request) {
        $dataset = DatasetTypeHelper::getTableDataset($request->datasetName);
        $tableName = $dataset->source;

        $isRecordIndexed = $request->recordsHolder instanceof IndexedRecordsHolder;
        $isVersionSupported = isset($request->recordsHolder->version);

        $columns = isset($request->recordsHolder->recordMetaData)
            ? $request->recordsHolder->recordMetaData->getColumns()
            : $dataset->getColumns();

        // preparing list of column names
        $columnNames = NULL;
        foreach ($columns as $column) {
            $columnNames[] = $column->name;
        }
        if ($isVersionSupported) {
            $columnNames[] = DatasetSystemColumnNames::VERSION;
        }

        $formattedRecords = NULL;
        foreach ($request->recordsHolder->records as $record) {
            $formattedRecord = NULL;
            foreach ($columns as $columnIndex => $column) {
                $columnIdentifier = $isRecordIndexed ? $columnIndex : $column->name;

                $formattedRecord[] = $this->formatValue($column->type->applicationType, $record->getColumnValue($columnIdentifier));
            }
            if ($isVersionSupported) {
                $formattedRecord[] = $request->recordsHolder->version;
            }

            $formattedRecords[] = $formattedRecord;
        }

        // for one record we do not need batch complexity
        if (count($formattedRecords) <= 1) {
            $defaultImplementation = new DefaultPrepareInsertStatementsInsteadOfBatchImpl();
            return $defaultImplementation->prepare($this, $tableName, $columnNames, $formattedRecords);
        }
        else {
            return $this->getExtension('prepareInsertStatementBatch')->prepare($this, $tableName, $columnNames, $formattedRecords);
        }
    }

    public function insertDatasetRecords(DataControllerCallContext $callcontext, AbstractDatasetManipulationRequest $request) {
        $environment_metamodel = data_controller_get_environment_metamodel();

        $dataset = DatasetTypeHelper::getTableDataset($request->datasetName);
        $datasource = $environment_metamodel->getDataSource($dataset->datasourceName);

        $sqls = $this->prepareInsertDatasetRecordStatements($callcontext, $request);

        return $this->executeManipulationStatementBatch($datasource, $sqls);
    }

    protected function prepareUpdateDatasetRecordStatements(DataControllerCallContext $callcontext, AbstractDatasetManipulationRequest $request) {
        $dataset = DatasetTypeHelper::getTableDataset($request->datasetName);
        $tableName = $dataset->source;

        $isRecordIndexed = $request->recordsHolder instanceof IndexedRecordsHolder;
        $isVersionSupported = isset($request->recordsHolder->version);

        $recordMetaData = isset($request->recordsHolder->recordMetaData) ? $request->recordsHolder->recordMetaData : $dataset;

        $sqls = NULL;

        $cachedColumns = NULL;
        foreach ($request->recordsHolder->records as $record) {
            $columnValueSet = $isRecordIndexed ? $record->columnValues : $record;

            $set = $where = NULL;
            foreach ($columnValueSet as $columnIdentifier => $value) {
                $column = isset($cachedColumns[$columnIdentifier]) ? $cachedColumns[$columnIdentifier] : NULL;
                if (!isset($column)) {
                    if ($isRecordIndexed) {
                        $columnIndex = $columnIdentifier;
                        $column = $recordMetaData->getColumnByIndex($columnIndex);
                    }
                    else {
                        $columnName = $columnIdentifier;
                        $column = $recordMetaData->getColumn($columnName);
                    }
                    $cachedColumns[$columnIdentifier] = $column;
                }

                $formattedValue = $this->formatValue($column->type->applicationType, $value);
                if ($column->isKey()) {
                    $where[$column->name] = $formattedValue;
                }
                else {
                    $set[$column->name] = $formattedValue;
                }
            }
            // adding support for system columns
            if ($isVersionSupported) {
                $set[DatasetSystemColumnNames::VERSION] = $request->recordsHolder->version;
            }

            // preventing unsafe operation
            if (!isset($where)) {
                throw new IllegalArgumentException(t('Unsafe UPDATE operation (multiple records might be updated)'));
            }

            ArrayHelper::appendValue($sqls, $this->getExtension('prepareUpdateStatement')->prepare($this, $tableName, $set, $where));
        }

        return $sqls;
    }

    public function updateDatasetRecords(DataControllerCallContext $callcontext, AbstractDatasetManipulationRequest $request) {
        $environment_metamodel = data_controller_get_environment_metamodel();

        $dataset = DatasetTypeHelper::getTableDataset($request->datasetName);
        $datasource = $environment_metamodel->getDataSource($dataset->datasourceName);

        $sqls = $this->prepareUpdateDatasetRecordStatements($callcontext, $request);

        return isset($sqls) ? $this->executeManipulationStatementBatch($datasource, $sqls) : 0;
    }

    protected function prepareDeleteDatasetRecordStatements(DataControllerCallContext $callcontext, AbstractDatasetManipulationRequest $request) {
        $dataset = DatasetTypeHelper::getTableDataset($request->datasetName);
        $tableName = $dataset->source;

        $isRecordIndexed = $request->recordsHolder instanceof IndexedRecordsHolder;

        $recordMetaData = isset($request->recordsHolder->recordMetaData) ? $request->recordsHolder->recordMetaData : $dataset;
        $keyColumns = $recordMetaData->getKeyColumns();

        // a request can be more efficient for single column key
        $deleteKeys = NULL;
        if (count($keyColumns) == 1) {
            list($keyColumnIndex, $keyColumn) = each($keyColumns);
            $keyColumnIdentifier = $isRecordIndexed ? $keyColumnIndex : $keyColumn->name;

            $keyValues = NULL;
            foreach ($request->recordsHolder->records as $record) {
                $keyValues[] = $this->formatValue($keyColumn->type->applicationType, $record->getColumnValue($keyColumnIdentifier, TRUE));
            }

            $deleteKeys[] = array($keyColumn->name => $keyValues);
        }
        else {
            foreach ($request->recordsHolder->records as $record) {
                $deleteKey = NULL;
                foreach ($keyColumns as $keyColumnIndex => $keyColumn) {
                    $keyColumnIdentifier = $isRecordIndexed ? $keyColumnIndex : $keyColumn->name;
                    $keyColumnValue = $this->formatValue($keyColumn->type->applicationType, $record->getColumnValue($keyColumnIdentifier, TRUE));

                    $deleteKey[$keyColumn->name] = $keyColumnValue;
                }

                $deleteKeys[] = $deleteKey;
            }
        }

        return $this->getExtension('prepareDeleteStatementBatch')->prepare($this, $tableName, $deleteKeys);
    }

    public function deleteDatasetRecords(DataControllerCallContext $callcontext, AbstractDatasetManipulationRequest $request) {
        $environment_metamodel = data_controller_get_environment_metamodel();

        $dataset = DatasetTypeHelper::getTableDataset($request->datasetName);
        $datasource = $environment_metamodel->getDataSource($dataset->datasourceName);

        $sqls = $this->prepareDeleteDatasetRecordStatements($callcontext, $request);

        return isset($sqls) ? $this->executeManipulationStatementBatch($datasource, $sqls) : 0;
    }

    private function prepareRecordHolder(AbstractDatasetManipulationRequest $request, array &$keyedRecords = NULL, array $keys = NULL) {
        $recordHolder = ($request->recordsHolder instanceof IndexedRecordsHolder)
            ? new IndexedRecordsHolder()
            : new AssociativeRecordsHolder();
        $recordHolder->recordMetaData = $request->recordsHolder->recordMetaData;
        $recordHolder->version = $request->recordsHolder->version;

        // registering records
        foreach ($keys as $key => $flag) {
            $recordHolder->registerRecordInstance($keyedRecords[$key]);
        }

        return $recordHolder;
    }

    public function insertOrUpdateOrDeleteDatasetRecords(DataControllerCallContext $callcontext, AbstractDatasetManipulationRequest $request) {
        $environment_metamodel = data_controller_get_environment_metamodel();

        $datasourceQueryHandler = DataSourceQueryFactory::getInstance()->getHandler($this->getDataSourceType());

        $dataset = DatasetTypeHelper::getTableDataset($request->datasetName);
        $datasource = $environment_metamodel->getDataSource($dataset->datasourceName);
        $recordMetaData = isset($request->recordsHolder->recordMetaData) ? $request->recordsHolder->recordMetaData : $dataset;

        $isRecordIndexed = $request->recordsHolder instanceof IndexedRecordsHolder;

        $keyColumnNames = $recordMetaData->getKeyColumnNames();
        $nonkeyColumnNames = $recordMetaData->findNonKeyColumnNames();

        // preparing a request to find existing records
        $queryRequest = new DatasetQueryRequest($request->datasetName);
        // loading only key columns from database
        $queryRequest->addColumns($keyColumnNames);
        // a request can be more efficient for single column key
        if (count($keyColumnNames) == 1) {
            list($keyColumnIndex, $keyColumnName) = each($keyColumnNames);
            $keyColumnIdentifier = $isRecordIndexed ? $keyColumnIndex : $keyColumnName;

            $keyValues = NULL;
            foreach ($request->recordsHolder->records as $record) {
                ArrayHelper::addUniqueValue($keyValues, $record->getColumnValue($keyColumnIdentifier, TRUE));
            }

            $queryRequest->addQueryValue(
                0,
                $keyColumnName, OperatorFactory::getInstance()->initiateHandler(EqualOperatorHandler::$OPERATOR__NAME, array($keyValues)));
        }
        else {
            for ($i = 0, $count = count($request->recordsHolder->records); $i < $count; $i++) {
                $record = $request->recordsHolder->records[$i];
                foreach ($keyColumnNames as $keyColumnIndex => $keyColumnName) {
                    $keyColumnIdentifier = $isRecordIndexed ? $keyColumnIndex : $keyColumnName;

                    $keyColumnValue = $record->getColumnValue($keyColumnIdentifier, TRUE);
                    $queryRequest->addQueryValue(
                        $i,
                        $keyColumnName, OperatorFactory::getInstance()->initiateHandler(EqualOperatorHandler::$OPERATOR__NAME, $keyColumnValue));
                }
            }
        }

        // loading existing records ... if any
        $existingRecords = $datasourceQueryHandler->queryDataset($callcontext, $queryRequest, new QueryKeyResultFormatter($keyColumnNames));

        // sorting out records for insert, update and delete operations
        $keyedRecords = $insertedRecordKeys = $updatedRecordKeys = $deletedRecordKeys = NULL;
        foreach ($request->recordsHolder->records as $record) {
            $keyParts = NULL;
            foreach ($keyColumnNames as $keyColumnIndex => $keyColumnName) {
                $keyColumnIdentifier = $isRecordIndexed ? $keyColumnIndex : $keyColumnName;
                $keyParts[] = $record->getColumnValue($keyColumnIdentifier, TRUE);
            }
            $key = ArrayHelper::prepareCompositeKey($keyParts);
            $keyedRecords[$key] = $record;

            // checking if the record has to be deleted
            $isDeletable = TRUE;
            if (isset($nonkeyColumnNames)) {
                foreach ($nonkeyColumnNames as $columnIndex => $columnName) {
                    $columnIdentifier = $isRecordIndexed ? $columnIndex : $columnName;
                    if ($record->getColumnValue($columnIdentifier) != NULL) {
                        $isDeletable = FALSE;
                        break;
                    }
                }
            }
            else {
                // the dataset has NO non-key columns. We should not delete these records
                $isDeletable = FALSE;
            }

            if ($isDeletable) {
                unset($insertedRecordKeys[$key]);
                unset($updatedRecordKeys[$key]);
                // the record physically present in database and needs to be deleted
                if (isset($existingRecords[$key])) {
                    unset($existingRecords[$key]);
                    $deletedRecordKeys[$key] = TRUE;
                }
            }
            elseif (isset($insertedRecordKeys[$key])) {
                // the key has been already used to insert a record within this batch. This record needs to be part of update operation
                $updatedRecordKeys[$key] = TRUE;
            }
            elseif (isset($existingRecords[$key])) {
                $updatedRecordKeys[$key] = TRUE;
            }
            else {
                $insertedRecordKeys[$key] = TRUE;
            }
        }

        $sqls = NULL;

        // deleting existing records
        $deletedRecordCount = 0;
        if (isset($deletedRecordKeys)) {
            $deleteRecordHolder = $this->prepareRecordHolder($request, $keyedRecords, $deletedRecordKeys);
            // preparing request
            $deleteRequest = new DatasetDeleteRequest($request->datasetName, $deleteRecordHolder);
            // preparing statements to delete records from the database
            ArrayHelper::appendValue($sqls, $this->prepareDeleteDatasetRecordStatements($callcontext, $deleteRequest));

            $deletedRecordCount = count($deleteRecordHolder->records);
        }

        // inserting new records
        $insertedRecordCount = 0;
        if (isset($insertedRecordKeys)) {
            $insertRecordHolder = $this->prepareRecordHolder($request, $keyedRecords, $insertedRecordKeys);
            // preparing request
            $insertRequest = new DatasetInsertRequest($request->datasetName, $insertRecordHolder);
            // preparing statements to insert records into the database
            ArrayHelper::appendValue($sqls, $this->prepareInsertDatasetRecordStatements($callcontext, $insertRequest));

            $insertedRecordCount = count($insertRecordHolder->records);
        }

        // updating existing records
        $updatedRecordCount = 0;
        if (isset($updatedRecordKeys)) {
            $updateRecordHolder = $this->prepareRecordHolder($request, $keyedRecords, $updatedRecordKeys);
            // preparing request
            $updateRequest = new DatasetUpdateRequest($request->datasetName, $updateRecordHolder);
            // preparing statements to update records in the database
            ArrayHelper::appendValue($sqls, $this->prepareUpdateDatasetRecordStatements($callcontext, $updateRequest));

            $updatedRecordCount = count($updateRecordHolder->records);
        }

        $affectedRecordCount = $this->executeManipulationStatementBatch($datasource, $sqls);
        if (($insertedRecordCount + $updatedRecordCount + $deletedRecordCount) < $affectedRecordCount) {
            throw new IllegalStateException(t('Number of affected records is greater than expected number of inserted, updated and deleted records'));
        }

        return array($insertedRecordCount, $updatedRecordCount, $deletedRecordCount);
    }
}
