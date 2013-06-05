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


abstract class AbstractDataSubmitter extends AbstractObject {

    public static $EVENT_NAME__START = 'start';
    public static $EVENT_NAME__PREPARE_COLUMN = 'prepareMetaDataColumn';
    public static $EVENT_NAME__BEFORE_PROCESSING = 'beforeProcessingRecords';
    public static $EVENT_NAME__BEFORE_SUBMIT = 'beforeRecordSubmit';
    public static $EVENT_NAME__ON_SUBMIT = 'submitRecord';
    public static $EVENT_NAME__AFTER_SUBMIT = 'afterRecordSubmit';
    public static $EVENT_NAME__AFTER_PROCESSING = 'afterProcessingRecords';
    public static $EVENT_NAME__FINISH = 'finish';
    public static $EVENT_NAME__ABORT = 'abort';

    public function start() {
        return TRUE;
    }

    public function prepareMetaDataColumn(RecordMetaData $recordMetaData, ColumnMetaData $column, $originalColumnName) {}

    public function beforeProcessingRecords(RecordMetaData $recordMetaData, AbstractDataProvider $dataProvider) {
        return TRUE;
    }

    public function beforeRecordSubmit(RecordMetaData $recordMetaData, $recordNumber, array &$record) {
        return TRUE;
    }

    public function submitRecord(RecordMetaData $recordMetaData, $recordNumber, array &$record) {}

    public function afterRecordSubmit(RecordMetaData $recordMetaData, $recordNumber, array &$record) {}

    public function afterProcessingRecords(RecordMetaData $recordMetaData, $fileProcessedCompletely) {}

    public function finish() {}

    public function abort() {}
}
