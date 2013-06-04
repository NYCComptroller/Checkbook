<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class EmptyRecordSkipper extends AbstractDataSubmitter {

    protected function checkIfRecordEmpty(RecordMetaData $recordMetaData, array &$record) {
        // checking if the record has values for at least one column
        foreach ($recordMetaData->getColumns() as $column) {
            if (isset($record[$column->columnIndex])) {
                return FALSE;
            }
        }

        return TRUE;
    }

    public function beforeRecordSubmit(RecordMetaData $recordMetaData, $recordNumber, array &$record) {
        $result = parent::beforeRecordSubmit($recordMetaData, $recordNumber, $record);

        if ($result) {
            $isEmpty = $this->checkIfRecordEmpty($recordMetaData, $record);
            if ($isEmpty) {
                drupal_set_message(t('Empty record in line @lineNumber was ignored', array('@lineNumber' => $recordNumber)), 'warning');

                $result = FALSE;
            }
        }

        return $result;
    }
}
