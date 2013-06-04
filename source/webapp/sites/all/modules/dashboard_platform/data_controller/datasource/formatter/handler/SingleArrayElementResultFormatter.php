<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class SingleArrayElementResultFormatter extends AbstractArrayResultFormatter {

    private $propertyName = NULL;

    public function __construct($propertyName = NULL, ResultFormatter $parent = NULL) {
        parent::__construct($parent);

        $this->propertyName = $propertyName;
    }

    public function formatRecord(array &$records = NULL, $record) {
        $result = parent::formatRecord($records, $record);
        if ($result) {
            $this->errorUnsupportedChainOfResultFormatters();
        }

        $columnValue = NULL;
        if (isset($this->propertyName)) {
            $columnValue = isset($record[$this->propertyName]) ? $record[$this->propertyName] : NULL;
        }
        else {
            $count = count($record);
            switch ($count) {
                case 0:
                    // it is the same as NULL
                    break;
                case 1:
                    $columnValue = reset($record);
                    break;
                default:
                    throw new IllegalArgumentException(t('Only one property is supported by this result formatter'));
            }
        }

        if (isset($columnValue)) {
            $records[] = $columnValue;
        }

        return TRUE;
    }
}
