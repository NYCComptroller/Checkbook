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


class DataParserException extends IllegalStateException {}

abstract class AbstractDataParser extends AbstractObject {

    /**
     * @var RecordMetaData
     */
    public $metadata = NULL;
    public $isHeaderPresent = TRUE;

    public $skipRecordCount = 0;
    public $limitRecordCount = NULL;

    protected function initiateMetaData() {
        return new DatasetMetaData();
    }

    abstract protected function loadMetaData(AbstractDataProvider $dataProvider, array $dataSubmitters = NULL);

    protected function prepareMetaData(AbstractDataProvider $dataProvider, array $dataSubmitters = NULL) {
        $this->loadMetaData($dataProvider, $dataSubmitters);
        if (!isset($this->metadata)) {
            throw new IllegalStateException(t('Meta data is not provided'));
        }
    }

    protected function startProcessing(array $dataSubmitters = NULL) {
        if (isset($dataSubmitters)) {
            foreach ($dataSubmitters as $dataSubmitter) {
                if (!$dataSubmitter->start()) {
                    return FALSE;
                }
            }
        }

        return TRUE;
    }

    protected function prepareMetaDataColumn(array $dataSubmitters = NULL, RecordMetaData $recordMetaData, ColumnMetaData $column, $originalColumnName) {
        if (isset($dataSubmitters)) {
            foreach ($dataSubmitters as $dataSubmitter) {
                $dataSubmitter->prepareMetaDataColumn($recordMetaData, $column, $originalColumnName);
            }
        }
    }

    protected function beforeProcessingRecords(array $dataSubmitters = NULL, AbstractDataProvider $dataProvider) {
        if (isset($dataSubmitters)) {
            foreach ($dataSubmitters as $dataSubmitter) {
                if (!$dataSubmitter->beforeProcessingRecords($this->metadata, $dataProvider)) {
                    return FALSE;
                }
            }
        }

        return TRUE;
    }

    protected function beforeProcessingRecord(array $dataSubmitters = NULL, $recordNumber, array &$record) {
        if (isset($dataSubmitters)) {
            foreach ($dataSubmitters as $dataSubmitter) {
                if (!$dataSubmitter->beforeRecordSubmit($this->metadata, $recordNumber, $record)) {
                    return FALSE;
                }
            }
        }

        return TRUE;
    }

    protected function processRecord(array $dataSubmitters = NULL, $recordNumber, array &$record) {
        if (isset($dataSubmitters)) {
            foreach ($dataSubmitters as $dataSubmitter) {
                $dataSubmitter->submitRecord($this->metadata, $recordNumber, $record);
            }
        }
    }

    protected function afterProcessingRecord(array $dataSubmitters = NULL, $recordNumber, array &$record) {
        if (isset($dataSubmitters)) {
            foreach ($dataSubmitters as $dataSubmitter) {
                $dataSubmitter->afterRecordSubmit($this->metadata, $recordNumber, $record);
            }
        }
    }

    protected function afterProcessingRecords(array $dataSubmitters = NULL, $fileProcessedCompletely) {
        if (isset($dataSubmitters)) {
            foreach ($dataSubmitters as $dataSubmitter) {
                $dataSubmitter->afterProcessingRecords($this->metadata, $fileProcessedCompletely);
            }
        }
    }

    protected function finishProcessing(array $dataSubmitters = NULL) {
        if (isset($dataSubmitters)) {
            foreach ($dataSubmitters as $dataSubmitter) {
                $dataSubmitter->finish();
            }
        }
    }

    protected function abortProcessing(array $dataSubmitters = NULL) {
        if (isset($dataSubmitters)) {
            foreach ($dataSubmitters as $dataSubmitter) {
                $dataSubmitter->abort();
            }
        }
    }

    abstract public function parse(AbstractDataProvider $dataProvider, array $dataSubmitters = NULL);
}
