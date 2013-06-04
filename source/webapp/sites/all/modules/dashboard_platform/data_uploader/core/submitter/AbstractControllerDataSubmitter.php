<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
