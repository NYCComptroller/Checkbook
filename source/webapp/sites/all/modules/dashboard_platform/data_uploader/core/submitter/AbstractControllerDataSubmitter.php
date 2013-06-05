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


abstract class AbstractControllerDataSubmitter extends AbstractDataSubmitter {

    public static $BATCH_SIZE = 500;

    protected $datasetName = NULL;
    protected $truncateBeforeProceed = NULL;

    protected $recordsHolder = NULL;

    public $insertedRecordCount = 0;
    public $updatedRecordCount = 0;
    public $deletedRecordCount = 0;

    public function __construct($datasetName, $truncateBeforeProceed = FALSE) {
        parent::__construct();
        $this->datasetName = $datasetName;
        $this->truncateBeforeProceed = $truncateBeforeProceed;

        $this->recordsHolder = new IndexedRecordsHolder();
    }

    public function setVersion($version) {
        $this->recordsHolder->version = $version;
    }

    abstract protected function truncateStorage();

    public function beforeProcessingRecords(RecordMetaData $recordMetaData, AbstractDataProvider $dataProvider) {
        $result = parent::beforeProcessingRecords($recordMetaData, $dataProvider);

        if ($result && $this->truncateBeforeProceed) {
                $this->truncateStorage();
        }

        return $result;
    }

    abstract protected function submitRecordBatch(RecordMetaData $recordMetaData);

    public function submitRecord(RecordMetaData $recordMetaData, $recordNumber, array &$record) {
        parent::submitRecord($recordMetaData, $recordNumber, $record);

        $this->recordsHolder->registerRecordColumnValues($record);

        if (count($this->recordsHolder->records) >= self::$BATCH_SIZE) {
            $this->submitRecordBatch($recordMetaData);
            unset($this->recordsHolder->records);
        }
    }

    public function afterProcessingRecords(RecordMetaData $recordMetaData, $fileProcessedCompletely) {
        parent::afterProcessingRecords($recordMetaData, $fileProcessedCompletely);

        if (isset($this->recordsHolder->records)) {
            $this->submitRecordBatch($recordMetaData);
            unset($this->recordsHolder->records);
        }
    }
}
