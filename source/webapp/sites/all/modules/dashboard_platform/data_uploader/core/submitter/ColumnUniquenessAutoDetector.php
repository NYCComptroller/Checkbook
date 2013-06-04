<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class ColumnUniquenessAutoDetector extends AbstractDataAutoDetector {

    protected $columnUniqueValues = NULL;

    protected function checkAcceptableValue($value) {
        // checking if the value is acceptable as unique. The following rules are implemented
        //   - no spaces
        $isAcceptable = TRUE;

        if (strpos($value, ' ') !== FALSE) {
            $isAcceptable = FALSE;
        }

        return $isAcceptable;
    }

    public function submitRecord(RecordMetaData $recordMetaData, $recordNumber, array &$record) {
        parent::submitRecord($recordMetaData, $recordNumber, $record);

        if (isset($this->maximumRecordCount) && ($this->processedRecordCount >= $this->maximumRecordCount)) {
            return;
        }

        foreach ($recordMetaData->getColumns(FALSE) as $columnIndex => $column) {
            // we do not need to work with the column. It does not contain unique values
            if (isset($this->columnUniqueValues[$columnIndex]) && ($this->columnUniqueValues[$columnIndex] === FALSE)) {
                continue;
            }

            // if the column contains NULL we should not consider it as containing unique values (our alternative to several primary keys per table)
            if (!isset($record[$columnIndex])) {
                $this->columnUniqueValues[$columnIndex] = FALSE;
                continue;
            }

            $columnValue = $record[$columnIndex];
            if (!$this->checkAcceptableValue($columnValue) || isset($this->columnUniqueValues[$columnIndex][$columnValue])) {
                $this->columnUniqueValues[$columnIndex] = FALSE;
                continue;
            }

            $this->columnUniqueValues[$columnIndex][$columnValue] = TRUE;
        }

        $this->processedRecordCount++;
    }

    public function afterProcessingRecords(RecordMetaData $recordMetaData, $fileProcessedCompletely) {
        parent::afterProcessingRecords($recordMetaData, $fileProcessedCompletely);

        // we need to process 'minimum' number of records or process while file to mark columns as unique
        if (!isset($this->minimumRecordCount) || ($fileProcessedCompletely || ($this->processedRecordCount >= $this->minimumRecordCount))) {
            foreach ($recordMetaData->getColumns(FALSE) as $columnIndex => $column) {
                if (!isset($this->columnUniqueValues[$columnIndex]) || ($this->columnUniqueValues[$columnIndex] === FALSE)) {
                    continue;
                }

                if (isset($column->type->applicationType)
                        && (($column->type->applicationType == StringDataTypeHandler::$DATA_TYPE) || ($column->type->applicationType == IntegerDataTypeHandler::$DATA_TYPE))) {
                    $column->containsUniqueValues = TRUE;
                }
            }
        }
    }
}
