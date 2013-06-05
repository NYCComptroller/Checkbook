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


class ColumnTypeAutoDetector extends AbstractDataAutoDetector {

    public function __construct($maximumRecordCount = NULL) {
        parent::__construct(NULL, $maximumRecordCount);
    }

    public function submitRecord(RecordMetaData $recordMetaData, $recordNumber, array &$record) {
        parent::submitRecord($recordMetaData, $recordNumber, $record);

        if (isset($this->maximumRecordCount) && ($this->processedRecordCount >= $this->maximumRecordCount)) {
            return;
        }

        $datatypeFactory = DataTypeFactory::getInstance();

        foreach ($recordMetaData->getColumns(FALSE) as $column) {
            if (!isset($record[$column->columnIndex])) {
                continue;
            }
            $columnValue = $record[$column->columnIndex];

            // calculating length of the column value
            $column->type->length = MathHelper::max(
                (isset($column->type->length) ? $column->type->length : NULL),
                strlen($columnValue));

            if (isset($column->type->applicationType) && ($column->type->applicationType === StringDataTypeHandler::$DATA_TYPE)) {
                continue;
            }

            $columnDataType = $datatypeFactory->autoDetectDataType($columnValue, DATA_TYPE__ALL);
            if (isset($column->type->applicationType)) {
                try {
                    $column->type->applicationType = $datatypeFactory->selectDataType(
                        array($column->type->applicationType, $columnDataType));
                }
                catch (IncompatibleDataTypeException $e) {
                    // if the two types are incompatible only 'string' type can resolve the problem
                    $column->type->applicationType = StringDataTypeHandler::$DATA_TYPE;
                }
            }
            else  {
                $column->type->applicationType = $columnDataType;
            }

            // calculating scale for numeric columns
            $handler = $datatypeFactory->getHandler($column->type->applicationType);
            if ($handler instanceof AbstractNumberDataTypeHandler) {
                $numericColumnValue = $handler->castValue($columnValue);

                $decimalSeparatorIndex = strpos($numericColumnValue, $handler->decimalSeparator);
                if ($decimalSeparatorIndex !== FALSE) {
                    $scale = strlen($numericColumnValue) - $decimalSeparatorIndex - 1;

                    if (!isset($column->type->scale) || ($column->type->scale < $scale)) {
                        $column->type->scale = $scale;
                    }
                }
            }
        }

        $this->processedRecordCount++;
    }

    public function afterProcessingRecords(RecordMetaData $recordMetaData, $fileProcessedCompletely) {
        parent::afterProcessingRecords($recordMetaData, $fileProcessedCompletely);

        // 'fixing' values of some type definition properties
        foreach ($recordMetaData->getColumns(FALSE) as $column) {
            if ((isset($column->type->scale)) && ($column->type->applicationType == PercentDataTypeHandler::$DATA_TYPE)) {
                $column->type->scale -= 2;
                if ($column->type->scale < 0) {
                    $column->type->scale = 0;
                }
            }
        }
    }
}
