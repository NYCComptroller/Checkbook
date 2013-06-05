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
