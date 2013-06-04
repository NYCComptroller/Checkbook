<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class SampleDataPreparer extends AbstractSubsetDataSubmitter {

    public $records = NULL;

    public function submitRecord(RecordMetaData $recordMetaData, $recordNumber, array &$record) {
        parent::submitRecord($recordMetaData, $recordNumber, $record);

        if ($this->skippedRecordCount < $this->skipRecordCount) {
            $this->skippedRecordCount++;
            return;
        }

        if (isset($this->limitRecordCount) && ($this->processedRecordCount >= $this->limitRecordCount)) {
            return;
        }

        $this->records[] = $record;
        $this->processedRecordCount++;
    }

    public function afterProcessingRecords(RecordMetaData $recordMetaData, $fileProcessedCompletely) {
        parent::afterProcessingRecords($recordMetaData, $fileProcessedCompletely);

        $datatypeFactory = DataTypeFactory::getInstance();

        // converting sample data to appropriate type & reformatting array structure
        if (isset($this->records)) {
            $columns = $recordMetaData->getColumns(FALSE);
            foreach ($this->records as &$record) {
                foreach ($columns as $column) {
                    $value = isset($record[$column->columnIndex]) ? $record[$column->columnIndex] : NULL;
                    unset($record[$column->columnIndex]);

                    if (isset($value)) {
                        if (!isset($column->type->applicationType)) {
                            throw new IllegalStateException(t(
                                "Could not prepare the value for preview of '@columnName' column: @value",
                                array('@columnName' => $column->publicName, '@value' => $value)));
                        }

                        $record[$column->name] = $datatypeFactory->getHandler($column->type->applicationType)->castValue($value);
                    }
                    else {
                        $record[$column->name] = NULL;
                    }
                }
            }
        }
    }
}
