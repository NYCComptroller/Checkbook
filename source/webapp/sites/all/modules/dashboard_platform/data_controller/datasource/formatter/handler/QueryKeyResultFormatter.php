<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class QueryKeyResultFormatter extends AbstractArrayResultFormatter {

    private $keyColumnNames = NULL;
    private $isColumnValueUnique = TRUE;

    public function __construct($keyColumnNames, $isColumnValueUnique = TRUE, ResultFormatter $parent = NULL) {
        parent::__construct($parent);

        $this->keyColumnNames = is_array($keyColumnNames) ? $keyColumnNames : array($keyColumnNames);
        $this->isColumnValueUnique = $isColumnValueUnique;
    }

    public function __clone() {
        parent::__clone();

        $this->keyColumnNames = ArrayHelper::cloneArray($this->keyColumnNames);
    }

    public function formatRecord(array &$records = NULL, $record) {
        $result = parent::formatRecord($records, $record);
        if ($result) {
            $this->errorUnsupportedChainOfResultFormatters();
        }

        $recordKey = NULL;
        foreach ($this->keyColumnNames as $columnName) {
            $recordKey[] = isset($record[$columnName]) ? $record[$columnName] : NULL;
        }

        $key = ArrayHelper::prepareCompositeKey($recordKey);
        if (isset($records[$key])) {
            if ($this->isColumnValueUnique) {
                throw new IllegalArgumentException(t(
                	'Found several records for the key: @key',
                    array('@key' => ArrayHelper::printArray($recordKey, ', ', TRUE, TRUE))));
            }

            $records[$key][] = $record;
        }
        else {
            if ($this->isColumnValueUnique) {
                $records[$key] = $record;
            }
            else {
                $records[$key][] = $record;
            }
        }

        return TRUE;
    }
}
