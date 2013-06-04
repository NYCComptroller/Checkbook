<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class PrimaryKeyAutoDetector extends AbstractDataSubmitter {

    public function afterProcessingRecords(RecordMetaData $recordMetaData, $fileProcessedCompletely) {
        parent::afterProcessingRecords($recordMetaData, $fileProcessedCompletely);

        $primaryKeyColumnIndex = NULL;

        $columns = $recordMetaData->getColumns();
        foreach ($columns as $columnIndex => $column) {
            if (!$column->isUnique()) {
                continue;
            }

            if (!isset($primaryKeyColumnIndex) || ($columnIndex < $primaryKeyColumnIndex)) {
                $primaryKeyColumnIndex = $columnIndex;
            }
        }

        // 01/22/2013 I decided to support only first column as possible primary key.
        // Previous solution could select any unique column from the middle of the dataset
        // That was incorrect in most cases
        if (isset($primaryKeyColumnIndex) && ($primaryKeyColumnIndex == 0)) {
            $columns[$primaryKeyColumnIndex]->key = TRUE;
        }
    }
}
