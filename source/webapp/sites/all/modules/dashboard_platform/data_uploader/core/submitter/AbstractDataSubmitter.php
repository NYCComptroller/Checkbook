<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
