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


class EmptyFactRecordSkipper extends AbstractDataSubmitter {

    protected $factIndexes = NULL;

    public function beforeProcessingRecords(RecordMetaData $recordMetaData, AbstractDataProvider $dataProvider) {
        $result = parent::beforeProcessingRecords($recordMetaData, $dataProvider);

        if ($result) {
            foreach ($recordMetaData->getColumns() as $column) {
                if (isset($column->columnCategory) && ($column->columnCategory === DatasetColumnCategories::FACT)) {
                    $this->factIndexes[] = $column->columnIndex;
                }
            }
        }

        return $result;
    }

    protected function checkIfFactsEmpty(RecordMetaData $recordMetaData, array &$record) {
        // checking if the record has values for at least one fact column
        foreach ($this->factIndexes as $columnIndex) {
            if (isset($record[$columnIndex])) {
                return FALSE;
            }
        }

        return TRUE;
    }

    public function beforeRecordSubmit(RecordMetaData $recordMetaData, $recordNumber, array &$record) {
        $result = parent::beforeRecordSubmit($recordMetaData, $recordNumber, $record);

        if ($result && isset($this->factIndexes)) {
            $areFactsEmpty = $this->checkIfFactsEmpty($recordMetaData, $record);
            if ($areFactsEmpty) {
                drupal_set_message(
                    t('A record in line @lineNumber was ignored because it did not contain values for any facts', array('@lineNumber' => $recordNumber)),
                    'warning');

                $result = FALSE;
            }
        }

        return $result;
    }
}
