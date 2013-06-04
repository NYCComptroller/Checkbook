<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
