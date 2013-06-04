<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
