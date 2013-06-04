<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class CouchDBDataSourceHandler extends AbstractCouchDBDataSourceHandler {

    public static $DATASOURCE__TYPE = 'CouchDB';

    private static $DATASET__METADATA = '__metadata_couchdb';

    private static $PROPERTY__DOCUMENT_IDENTIFIER__SCHEMA_TEMPLATE = '~schema';

    protected function getMetaDataStorageProperties(DataControllerCallContext $callcontext, $sourceDatasetName) {
        $metamodel = data_controller_get_metamodel();

        $dataset = $metamodel->getDataset($sourceDatasetName);

        $databaseName = $dataset->source->database;

        $namespace = NameSpaceHelper::getNameSpace($dataset->name);

        $metadataDatasetName = NameSpaceHelper::addNameSpace($namespace, self::$DATASET__METADATA);
        $schemaDocumentId = self::$PROPERTY__DOCUMENT_IDENTIFIER__SCHEMA_TEMPLATE . '@' . $databaseName;

        return array($metadataDatasetName, $schemaDocumentId);
    }

    public function getDatasetMetaData(DataControllerCallContext $callcontext, DatasetMetaData $dataset) {
        // TODO Change implementation of the method and work with provided $dataset
        list($metadataDatasetName, $schemaDocumentId) = $this->getMetaDataStorageProperties($callcontext, $dataset->name);

        // loading schema for the dataset
        $schemaLoadRequest = new DatasetQueryRequest($metadataDatasetName);
        $schemaLoadRequest->addQueryValue(0, '_id', $schemaDocumentId);
        $schema = $this->queryDataset($callcontext, $schemaLoadRequest, NULL);

        $metadata = new DatasetMetaData();
        if (isset($schema)) {
            foreach ($schema[0]->properties as $propertyName => $property) {
                $column = $metadata->registerColumn($propertyName);
                // TODO support other properties of ColumnType class
                $column->type->applicationType = $property->type;
                $column->key = $property->key;
            }
        }
    }

    // FIXME this method is not supported any more
    protected function createDatasetSourceStorage(DataControllerCallContext $callcontext, $datasource, $dataset) {
        $this->createInternalDatabase($datasource, $dataset->source->database);
    }

    public function createDatasetStorage(DataControllerCallContext $callcontext, CreateDatasetStorageRequest $request) {
        list($metadataDatasetName, $schemaDocumentId) = $this->getMetaDataStorageProperties($callcontext, $request->datasetName);

        $recordsHolder = new AssociativeRecordsHolder();

        // creating a schema template using provided metadata
        $schema = $recordsHolder->initiateRecord();
        $schema->setColumnValue('_id', $schemaDocumentId);
        // preparing properties
        $schemaProperties = new stdClass();
        foreach ($request->dataset->columns as $column) {
            $property = new stdClass();
            // TODO Support other properties of ColumnType class
            $property->type = $column->type->applicationType;
            $property->key = $column->isKey();

            $schemaProperties->{$column->name} = $property;
        }
        $schema->setColumnValue('properties', $schemaProperties);

        // storing the template
        $insertRequest = new DatasetInsertRequest($metadataDatasetName, $recordsHolder);
        $this->insertDatasetRecords($callcontext, $insertRequest);
    }

    // FIXME this method is not supported any more
    protected function dropDatasetSourceStorage(DataControllerCallContext $callcontext, $datasource, $datasetSource) {
        $this->dropInternalDatabase($datasource, $datasetSource->database);
    }

    public function dropDatasetStorage(DataControllerCallContext $callcontext, DropDatasetStorageRequest $request) {
        parent::dropDatasetStorage($callcontext, $request);

        list($metadataDatasetName, $schemaDocumentId) = $this->getMetaDataStorageProperties($callcontext, $request->datasetName);

        $recordsHolder = new AssociativeRecordsHolder();

        // preparing schema templete for deleting
        $schema = $recordsHolder->initiateRecord();
        $schema->_id = $schemaDocumentId;

        // deleting template
        $deleteRequest = new DatasetDeleteRequest($metadataDatasetName, $recordsHolder);
        $this->deleteDatasetRecords($callcontext, $deleteRequest);
    }

    public function prepareCubeMetaData(DataControllerCallContext $callcontext, CubeMetaData $cube) {
        // TODO implement this function
    }

    protected function prepareDocumentIdentifiers($request) {
        $ids = NULL;

        if (isset($request->queries)) {
            // TODO Support several/composite parameters
            if (count($request->queries) > 1) {
                throw new UnsupportedOperationException(t('Several parameters are not supported yet'));
            }

            foreach ($request->queries as $query) {
                foreach ($query as $value) {
                    foreach ($value as $v) {
                        $ids[] = $v;
                    }
                }
            }
        }

        return $ids;
    }

    protected function prepareDatasetQuery(DataControllerCallContext $callcontext, $request, $databaseName) {
        $keys = $this->prepareDocumentIdentifiers($request);
        $isKeyDefined = isset($keys);
        $isKeySingle = $isKeyDefined && (count($keys) === 1);
        $isKeyList = $isKeyDefined && (count($keys) > 1);

        // preparing request URL
        $url = '/' . $databaseName . '/' . ($isKeySingle ? $keys[0] : '_all_docs?include_docs=true') ;

        // preparing request body
        $requestBody = NULL;
        if ($isKeyList) {
            $requestBody = '{"keys": ["' . implode('", "', $keys) . '"]}';
        }

        return array($url, $requestBody);
    }

    public function queryDataset(DataControllerCallContext $callcontext, DatasetQueryRequest $request, ResultFormatter $resultFormatter) {
        $records = NULL;

        $environment_metamodel = data_controller_get_environment_metamodel();
        $metamodel = data_controller_get_metamodel();

        $datasetName = $request->getDatasetName();
        $dataset = $metamodel->getDataset($datasetName);
        $datasource = $environment_metamodel->getDataSource($dataset->datasourceName);

        $databaseName = $dataset->source->database;

        list($url, $requestBody) = $this->prepareDatasetQuery($callcontext, $request, $databaseName);
        list($url, $manulSortingRequired) = $this->applyOrderBy($url, $request);
        $manualPaginationRequired = $manulSortingRequired;
        if (!$manualPaginationRequired) {
            $url = $this->applyPagination($url, $request);
        }

        $serverRequest->url = $url;
        $serverRequest->body = $requestBody;
        // executing the server request
        $serverResponse = $this->communicateWithServer($datasource, $serverRequest);

        if (isset($serverResponse->rows)) {
            foreach ($serverResponse->rows as $record) {
                $this->processRecord($records, $record, $request->columns, TRUE, $resultFormatter);
            }
        }
        else {
            $this->processRecord($records, $serverResponse, $request->columns, FALSE, $resultFormatter);
        }
        $resultFormatter->postFormatRecords($records);

        if ($manulSortingRequired) {
            sort_records($records, $request->sortingConfigurations);
        }

        if ($manualPaginationRequired) {
            $records = paginate_records($records, $request->startWith, $request->limit);
        }

        return $records;
    }

    public function countDatasetRecords(DataControllerCallContext $callcontext, DatasetCountRequest $request, ResultFormatter $resultFormatter) {
        $count = 0;

        $environment_metamodel = data_controller_get_environment_metamodel();
        $metamodel = data_controller_get_metamodel();

        $datasetName = $request->getDatasetName();
        $dataset = $metamodel->getDataset($datasetName);
        $datasource = $environment_metamodel->getDataSource($dataset->datasourceName);

        // preparing keys to access records
        $keys = $this->prepareDocumentIdentifiers($request);
        $isKeyDefined = isset($keys);
        $isKeySingle = $isKeyDefined && (count($keys) === 1);
        $isKeyList = $isKeyDefined && (count($keys) > 1);

        // preparing URL
        $url = '/' . $dataset->source->database;
        if ($isKeyDefined) {
            $url .= '/';
            if ($isKeySingle) {
                $url .= $keys[0];
            }
            else {
                $url .= '_all_docs?include_docs=false';
            }
        }

        // preparing property name to access server side calculated value
        $recordNumberPropertyName = $isKeyDefined ? NULL : 'doc_count';

        // preparing request body if any
        $requestBody = NULL;
        if ($isKeyList) {
            $requestBody = '{"keys": ["' . implode('", "', $keys) . '"]}';
        }

        $serverRequest->url = $url;
        $serverRequest->body = $requestBody;
        // executing the server request
        $serverResponse = $this->communicateWithServer($datasource, $serverRequest);

        // calculating number of records
        if (isset($recordNumberPropertyName)) {
            // server-calculated value
            $count = $serverResponse->$recordNumberPropertyName;
        }
        else {
            // manual record counting
            if (isset($serverResponse->rows)) {
                foreach ($serverResponse->rows as $record) {
                    $count += ($this->checkDocumentExistence($record, FALSE) ? 1 : 0);
                }
            }
            else {
                $count = $this->checkDocumentExistence($serverResponse, FALSE) ? 1 : 0;
            }
        }

        return $count;
    }

    protected function prepareDatasetRecords4Submission(DataControllerCallContext $callcontext, $request) {
        $preparedRecords = NULL;

        $datasetName = $request->getDatasetName();

        $isOperationInsert = $request->getOperationName() == DatasetInsertRequest::$OPERATION__INSERT;
        $isOperationDelete = $request->getOperationName() == DatasetDeleteRequest::$OPERATION__DELETE;

        $recordMetaData = $request->recordsHolder->recordMetaData;
        $keyColumn = $recordMetaData->findKeyColumn();
        $keyColumnName = isset($keyColumn) ? $keyColumn->name : NULL;

        $documentIds = NULL;
        if ($request->recordsHolder instanceof IndexedRecordsHolder) {
            $columnCount = $recordMetaData->getColumnCount();

            foreach ($request->recordsHolder->records as $record) {
                $preparedRecord = NULL;

                for ($i = 0; $i < $columnCount; $i++) {
                    $column = $recordMetaData->columns[$i];

                    $columnValue = $record[$i];
                    if (isset($columnValue)) {
                        $preparedRecord->{$column->name} = $columnValue;
                    }
                }

                // preparing document identifier
                if (!isset($preparedRecord->_id) && isset($keyColumnName)) {
                    $preparedRecord->_id = $preparedRecord->$keyColumnName;
                }

                // collecting document identifiers to load last revisions
                if (!$isOperationInsert) {
                    if (isset($preparedRecord->_id)) {
                        $documentIds[] = $preparedRecord->_id;
                    }
                    else {
                        LogHelper::log_error($preparedRecord);
                        throw new IllegalArgumentException(t('Could not find document identifier for the document'));
                    }
                }

                $preparedRecords[] = $preparedRecord;
            }

        }
        else {
            foreach ($request->recordsHolder->records as $record) {
                // preparing document identifier
                if (!isset($record->_id) && isset($keyColumnName)) {
                    $record->_id = $record->$keyColumnName;
                }

                // collecting document identifiers to load last revisions
                if (!$isOperationInsert) {
                    if (isset($record->_id)) {
                        $documentIds[] = $record->_id;
                    }
                    else {
                        LogHelper::log_error($record);
                        throw new IllegalArgumentException(t('Could not find document identifier for the document'));
                    }
                }

                $preparedRecords[] = $record;
            }
        }

        if (!$isOperationInsert) {
            // loading previous revisions
            $revisions = NULL;
            if (isset($documentIds)) {
                $revisionRequest = new DatasetQueryRequest($datasetName);
                $revisionRequest->addQueryValues(0, '_id', $documentIds);

                $revisionResponse = $this->queryDataset($callcontext, $revisionRequest, NULL);

                if (isset($revisionResponse)) {
                    foreach ($revisionResponse as $revision) {
                        $revisions[$revision->_id] = $revision->_rev;
                    }
                }
            }

            foreach ($preparedRecords as $record) {
                // setting revision
                if ((isset($revisions) && isset($revisions[$record->_id]))) {
                    $record->_rev = $revisions[$record->_id];
                }
                else {
                    throw new IllegalArgumentException(t(
                    	'Could not find last revision for the document: @id',
                        array('@id' => $record->_id)));
                }

                if ($isOperationDelete) {
                    $record->_deleted = TRUE;
                }
            }
        }

        return $preparedRecords;
    }

    protected function modifyDatasetRecords(DataControllerCallContext $callcontext, $request) {
        $environment_metamodel = data_controller_get_environment_metamodel();
        $metamodel = data_controller_get_metamodel();

        $datasetName = $request->datasetName;
        $dataset = $metamodel->getDataset($datasetName);
        $datasource = $environment_metamodel->getDataSource($dataset->datasourceName);

        $records = $this->prepareDatasetRecords4Submission($callcontext, $request);

        return $this->submitDatabaseRecords($datasource, $dataset->source->database, $records);
    }

    public function insertDatasetRecords(DataControllerCallContext $callcontext, AbstractDatasetManipulationRequest $request) {
        return $this->modifyDatasetRecords($callcontext, $request);
    }

    public function updateDatasetRecords(DataControllerCallContext $callcontext, AbstractDatasetManipulationRequest $request) {
        return $this->modifyDatasetRecords($callcontext, $request);
    }

    public function deleteDatasetRecords(DataControllerCallContext $callcontext, AbstractDatasetManipulationRequest $request) {
        return $this->modifyDatasetRecords($callcontext, $request);
    }

    public function queryCube(DataControllerCallContext $callcontext, CubeQueryRequest $request, ResultFormatter $resultFormatter) {
        $records = NULL;

        $environment_metamodel = data_controller_get_environment_metamodel();
        $metamodel = data_controller_get_metamodel();

        $cubeName = $request->getCubeName();
        $cube = $metamodel->getCube($cubeName);

        $cubeDatasetName = $cube->sourceDatasetName;
        $cubeDataset = $metamodel->getDataset($cubeDatasetName);
        $datasource = $environment_metamodel->getDataSource($cubeDataset->datasourceName);

        $designDocumentName = NameSpaceHelper::removeNameSpace($cubeName);
        $viewName = NameSpaceHelper::removeNameSpace($cubeName);
        $url = '/' . $cubeDataset->source->database . "/_design/$designDocumentName/_view/$viewName";

        $dimensionCount = $cube->getDimensionCount();

        $queryKeys = NULL;
        // TODO list of dimensions could be empty
        foreach ($cube->dimensions as $dimension) {
            $queryDimension = $request->findDimensionQuery($dimension->name);

            if (isset($queryDimension)) {
                $queryKeys[] = $queryDimension->values;
            }
            else {
                $queryKeys[] = NULL;
            }
        }

        // TODO develop more comprehensive validation or mapping
        if ($cube->getMeasureCount() != 1) {
            throw new UnsupportedOperationException(t('Only one measure is supported'));
        }
        $cubeMeasurePropertyName = NULL;
        foreach ($cube->measures as $measureName => $measure) {
            $cubeMeasurePropertyName = $measureName;
        }

        // preparing set of keys to access data
        $requestKeys = NULL;
        $this->prepareCubeRequestKeys($requestKeys, $queryKeys, 0, NULL);

        // preparing server requests
        if (isset($requestKeys)) {
            foreach ($requestKeys as $requestKey) {
                $parameterKey = '';
                foreach ($requestKey as $dimensionKey) {
                    $parameterKey .= self::prepareSingleValue($dimensionKey);
                }
                $parameterKey = '[' . substr($parameterKey, 0, strlen($parameterKey) - 1) . ']';

                $serverRequest = NULL;
                $serverRequest->url = $url . "?key=$parameterKey";

                // executing the server request
                $serverResponse = $this->communicateWithServer($datasource, $serverRequest);
                $this->checkDocumentExistence($serverResponse, TRUE);

                if (isset($serverResponse->rows[0])) {
                    $record = NULL;
                    // adding dimension-related properties
                    for ($i = 0; $i < $dimensionCount; $i++) {
                        // we should have data for a dimension to report related property
                        if (!isset($requestKey[$i])) {
                            continue;
                        }

                        $dimension = $cube->dimensions[$i];
                        $dimensionKey = $requestKey[$i];

                        // FIXME there is no support for targetKey any more
                        $record[$dimension->targetKey] = $dimensionKey;
                    }
                    // adding measure value
                    $record[$cubeMeasurePropertyName] = $serverResponse->rows[0]->value;

                    $records[] = $record;
                }
            }
        }

        return $records;
    }

    protected function prepareCubeRequestKeys(&$requestKeys, $queryKeys, $loopIndex, $keyTemplate) {
        $indexCount = count($queryKeys);
        $indexData = $queryKeys[$loopIndex];

        $count = is_array($indexData) ? count($indexData) : 1;
        for ($i = 0; $i < $count; $i++) {
            $itemData = is_array($indexData) ? $indexData[$i] : $indexData;

            $record = $keyTemplate;
            $record[] = $itemData;

            if ($loopIndex == ($indexCount -1)) {
                $requestKeys[] = $record;
            }
            else {
                $this->prepareCubeRequestKeys($requestKeys, $queryKeys, $loopIndex + 1, $record);
            }
        }
    }

}
