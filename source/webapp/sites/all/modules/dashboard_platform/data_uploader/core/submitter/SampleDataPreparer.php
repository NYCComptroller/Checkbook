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
