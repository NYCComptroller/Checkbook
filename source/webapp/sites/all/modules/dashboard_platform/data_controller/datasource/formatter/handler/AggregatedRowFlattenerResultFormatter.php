<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class AggregatedRowFlattenerResultFormatter extends RowFlattenerResultFormatter {

    public static $PROPERTY_NAME_PREFIX__SUBJECT_AGGREGATED = 'aggregated';

    public function postFormatRecords(array &$records = NULL) {
        parent::postFormatRecords($records);

        if (!isset($records)) {
            return;
        }

        foreach ($records as &$record) {
            foreach ($this->adjustedSubjectPropertyNames as $subjectPropertyName) {
                $aggregation = 0.0;
                foreach ($record as $propertyName => $propertyValue) {
                    if (strpos($propertyName, $subjectPropertyName) === 0) {
                        $aggregation += $propertyValue;
                    }
                }

                $record[self::$PROPERTY_NAME_PREFIX__SUBJECT_AGGREGATED . '_' . $subjectPropertyName] = $aggregation;
            }
        }
    }
}
