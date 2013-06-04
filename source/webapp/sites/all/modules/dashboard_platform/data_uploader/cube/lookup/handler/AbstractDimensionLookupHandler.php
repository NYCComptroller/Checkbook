<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractDimensionLookupHandler extends AbstractObject implements DimensionLookupHandler {

    // FIXME use $SHARED_LOOKUP constant as global functionality
    // identifies if lookup tables are shared across several applications
    public static $SHARED_LOOKUP = FALSE;

    protected $datatype = NULL;

    protected $cachedRecordMetaDatas = NULL;

    protected $cachedLookupValues = NULL;

    public function __construct($datatype) {
        parent::__construct();
        $this->datatype = $datatype;
    }
    // *****************************************************************************************************************************
    //
    // Supporting functions for prepareDatasetColumnLookupIds()
    //
    // *****************************************************************************************************************************
    public static function prepareLookupKey($items) {
        $lookupKey = NULL;

        if (is_array($items)) {
            $adjustedItems = NULL;
            foreach ($items as $item) {
                $adjustedItems[] = strtoupper($item);
            }

            $lookupKey = ArrayHelper::prepareCompositeKey($adjustedItems);
        }
        else {
            $lookupKey = strtoupper($items);
        }

        return $lookupKey;
    }

    protected function selectLookupValuesWithMissingIdentifier(array &$lookupValues) {
        $missingIdentifierLookupValues = NULL;

        foreach ($lookupValues as $lookupKey => $lookupValue) {
            if (isset($lookupValue->identifier)) {
                continue;
            }

            $missingIdentifierLookupValues[$lookupKey] = $lookupValue;
        }

        return $missingIdentifierLookupValues;
    }

    protected function prepareLookupCacheKey($datasetName) {
        return $datasetName;
    }

    protected function guaranteeSpaceInIdentifierCache($datasetName) {
        $lookupCacheKey = $this->prepareLookupCacheKey($datasetName);

        // there is no cache for the dataset yet
        if (!isset($this->cachedLookupValues[$lookupCacheKey])) {
            return;
        }

        if (count($this->cachedLookupValues[$lookupCacheKey]) > AbstractControllerDataSubmitter::$BATCH_SIZE) {
            // deleting first third of the array
            $offset = (int) (AbstractControllerDataSubmitter::$BATCH_SIZE / 3);
            $length = AbstractControllerDataSubmitter::$BATCH_SIZE - $offset;
            $this->cachedLookupValues[$lookupCacheKey] = array_slice($this->cachedLookupValues[$lookupCacheKey], $offset, $length, TRUE);
        }
    }

    protected function loadIdentifiers($lookupDatasetName, array $uniqueSetColumns, array &$lookupValues, $physicalLoadOnly = FALSE) {
        $dataQueryController = data_controller_get_instance();
        $metamodel = data_controller_get_metamodel();

        $lookupDataset = $metamodel->getDataset($lookupDatasetName);
        $identifierColumnName = $lookupDataset->getKeyColumn()->name;

        $lookupCacheKey = $this->prepareLookupCacheKey($lookupDataset->name);

        $missingIdentifiers = NULL;
        foreach ($lookupValues as $lookupKey => $lookupValue) {
            if (isset($lookupValue->identifier)) {
                continue;
            }

            if (!$physicalLoadOnly) {
                if (isset($this->cachedLookupValues[$lookupCacheKey][$lookupKey])) {
                    $lookupValues[$lookupKey]->identifier = $this->cachedLookupValues[$lookupCacheKey][$lookupKey];
                    continue;
                }
            }

            $properties = NULL;
            foreach ($uniqueSetColumns as $column) {
                $columnName = $column->name;
                $properties[$columnName] = $lookupValue->$columnName;
            }

            $missingIdentifiers[$lookupKey] = $properties;
        }
        if (!isset($missingIdentifiers)) {
            return;
        }

        $isCompositeUniqueSet = count($uniqueSetColumns) > 1;

        // preparing columns for the query
        $queryColumns = array($identifierColumnName);
        foreach ($uniqueSetColumns as $column) {
            ArrayHelper::addUniqueValue($queryColumns, $column->name);
        }

        // preparing parameters for the query
        $queryParameters = NULL;
        if ($isCompositeUniqueSet) {
            $queryParameters = array_values($missingIdentifiers);
        }
        else {
            $propertyName = $uniqueSetColumns[0]->name;

            $propertyParameter = NULL;
            foreach ($missingIdentifiers as $lookupProperties) {
                $propertyParameter[] = $lookupProperties[$propertyName];
            }

            $queryParameters = array($propertyName => $propertyParameter);
        }

        // loading data from database for 'missing' records
        $loadedLookupProperties = $dataQueryController->queryDataset($lookupDataset->name, $queryColumns, $queryParameters);

        // processing found records
        if (isset($loadedLookupProperties)) {
            foreach ($loadedLookupProperties as $lookupProperties) {
                $id = $lookupProperties[$identifierColumnName];

                // preparing lookup key
                $keyItems = NULL;
                foreach ($uniqueSetColumns as $column) {
                    $keyItems[] = $lookupProperties[$column->name];
                }
                $lookupKey = self::prepareLookupKey($keyItems);

                if (!isset($lookupValues[$lookupKey])) {
                    // this could happen when we have values with leading zeros in one source and without in another
                    continue;
                }
                $lookupValues[$lookupKey]->identifier = $id;

                // storing the value into cache for further usage
                $this->cachedLookupValues[$lookupCacheKey][$lookupKey] = $id;
            }
            $this->guaranteeSpaceInIdentifierCache($lookupDataset->name);
        }
    }

    protected function prepareRecordMetaData4LookupDataset($lookupDatasetName, $identifierColumnName, array $uniqueSetColumns, array $propertyColumns = NULL) {
        $metadataCacheKey = $lookupDatasetName;

        // checking cache first
        if (isset($this->cachedRecordMetaDatas[$metadataCacheKey])) {
            return $this->cachedRecordMetaDatas[$metadataCacheKey];
        }

        $lookupRecordMetaData = new RecordMetaData();

        $columnIdentifier = $lookupRecordMetaData->registerColumn($identifierColumnName);
        $columnIdentifier->initializeTypeFrom(Sequence::getSequenceColumnType());

        foreach ($uniqueSetColumns as $column) {
            $columnValue = $lookupRecordMetaData->registerColumn($column->name);
            $columnValue->initializeTypeFrom($column->type);
            $columnValue->key = TRUE;
        }

        if (isset($propertyColumns)) {
            foreach ($propertyColumns as $column) {
                $columnValue = $lookupRecordMetaData->registerColumn($column->name);
                $columnValue->initializeTypeFrom($column->type);
            }
        }

        // storing in the cache for the future
        $this->cachedRecordMetaDatas[$metadataCacheKey] = $lookupRecordMetaData;

        return $lookupRecordMetaData;
    }

    protected function storeLookupValues($lookupDatasetName, array $uniqueSetColumns, $propertyColumns, $sequenceName, array &$lookupValues) {
        $dataManipulationController = data_controller_dml_get_instance();
        $metamodel = data_controller_get_metamodel();

        $lookupDataset = $metamodel->getDataset($lookupDatasetName);
        $identifierColumnName = $lookupDataset->getKeyColumn()->name;

        $lookupCacheKey = $this->prepareLookupCacheKey($lookupDataset->name);

        // generating identifiers for source table
        $identifiers = Sequence::getNextSequenceValues($sequenceName, count($lookupValues));

        // preparing insert operation configuration
        $recordsHolder = new IndexedRecordsHolder();
        $recordsHolder->recordMetaData = $this->prepareRecordMetaData4LookupDataset($lookupDataset->name, $identifierColumnName, $uniqueSetColumns, $propertyColumns);

        // preparing records for insert operation
        foreach ($lookupValues as $lookupKey => $lookupValue) {
            $identifier = array_pop($identifiers);

            if (!self::$SHARED_LOOKUP) {
                $lookupValues[$lookupKey]->identifier = $identifier;

                // storing the value into cache for further usage
                $this->cachedLookupValues[$lookupCacheKey][$lookupKey] = $identifier;
            }

            $record = array($identifier);

            foreach ($uniqueSetColumns as $column) {
                $columnName = $column->name;
                $record[] = $lookupValue->$columnName;
            }

            if (isset($propertyColumns)) {
                $lookupValue = $lookupValues[$lookupKey];
                foreach ($propertyColumns as $column) {
                    $record[] = $lookupValue->{$column->name};
                }
            }

            $recordsHolder->registerRecordColumnValues($record);
        }
        $this->guaranteeSpaceInIdentifierCache($lookupDataset->name);

        // FIXME support third parameter 'ignoreIfExists'
        // storing 'missing' records
        $dataManipulationController->insertDatasetRecordBatch($lookupDataset->name, $recordsHolder, self::$SHARED_LOOKUP);
    }

    protected function prepareIdentifiers($lookupDatasetName, array $uniqueSetColumns, $propertyColumns, $sequenceName, array &$lookupValues) {
        $this->loadIdentifiers($lookupDatasetName, $uniqueSetColumns, $lookupValues);

        $missingIdentifierLookupValues = $this->selectLookupValuesWithMissingIdentifier($lookupValues);
        if (isset($missingIdentifierLookupValues)) {
            $this->storeLookupValues($lookupDatasetName, $uniqueSetColumns, $propertyColumns, $sequenceName, $missingIdentifierLookupValues);
            if (self::$SHARED_LOOKUP) {
                // because we used 'ignoreIfExists' insert some records were not inserted because another thread had done that
                // as result of that it is possible that some generated identifiers will be wasted
                $this->loadIdentifiers($lookupDatasetName, $uniqueSetColumns, $lookupValues, TRUE);
            }
        }
    }

    // *****************************************************************************************************************************
    //
    // Supporting functions to implement unprepareDimension()
    //
    // *****************************************************************************************************************************
    public function unprepareDimension(MetaModel $metamodel, DatasetMetaData $dataset, $columnName) {}


    // *****************************************************************************************************************************
    //
    // Supporting functions to implement adjustReferencePointColumn()
    //
    // *****************************************************************************************************************************
    public function adjustReferencePointColumn(AbstractMetaModel $metamodel, $datasetName, $columnName) {
        $shared = FALSE;

        return array($datasetName, $columnName, $shared);
    }
}


abstract class DimensionLookupHandler__AbstractLookupValue extends AbstractObject {

    public $identifier = NULL;
}

class DimensionLookupHandler__LookupValue extends DimensionLookupHandler__AbstractLookupValue {

    public function getProperty($propertyName) {
        return $this->$propertyName;
    }

    public function setProperty($propertyName, $propertyValue) {
        $this->$propertyName = $propertyValue;
    }
}
