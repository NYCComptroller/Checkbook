<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class ColumnValueTrimmer extends AbstractDataSubmitter {

    private $maxValueLength;

    public function __construct($maxValueLength) {
        parent::__construct();
        $this->maxValueLength = $maxValueLength;
    }

    public function beforeRecordSubmit(RecordMetaData $recordMetaData, $recordNumber, array &$record) {
        $result = parent::beforeRecordSubmit($recordMetaData, $recordNumber, $record);

        if ($result) {
            foreach ($record as &$columnValue) {
                if (strlen($columnValue) > $this->maxValueLength) {
                    $columnValue = substr($columnValue, 0, MathHelper::max($this->maxValueLength - 3, 0)) . '...';
                }
            }
        }

        return $result;
    }
}
