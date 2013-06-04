<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class ScreenDataSubmitter extends AbstractDataSubmitter {

    public function submitRecord(RecordMetaData $recordMetaData, $recordNumber, array &$record) {
        parent::submitRecord($recordMetaData, $recordNumber, $record);

        echo t("record[@recordNumber]:\n", array('@recordNumber' => $recordNumber));
        foreach ($record as $columnName => $columnValue) {
            echo "  $columnName = $columnValue\n";
        }
    }
}
