<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class TableResultFormatter extends AbstractArrayResultFormatter {

    private $propertyNames = NULL;
    private $assembledColumnNames = NULL;

    public function __construct(array $propertyNames = NULL, ResultFormatter $parent = NULL) {
        parent::__construct($parent);

        $this->propertyNames = $propertyNames;
    }

    public function formatRecord(array &$records = NULL, $record) {
        $result = parent::formatRecord($records, $record);
        if ($result) {
            $this->errorUnsupportedChainOfResultFormatters();
        }

        $formattedRecord = NULL;
        if (isset($this->propertyNames)) {
            // using predefined column name
            foreach ($this->propertyNames as $index => $propertyName) {
                $formattedRecord[$index] = isset($record[$propertyName]) ? $record[$propertyName] : NULL;
            }
        }
        else {
            // checking if we need to add additional columns
            foreach ($record as $propertyName => $propertyValue) {
                if (!isset($this->assembledColumnNames[$propertyName])) {
                    $index = count($this->assembledColumnNames);

                    // registering new column
                    $this->assembledColumnNames[$propertyName] = $index;
                    // adding NULL values for the column for previous records
                    if (isset($records)) {
                        foreach ($records as &$record) {
                            $record[$index] = NULL;
                        }
                    }
                }
            }

            foreach ($this->assembledColumnNames as $propertyName => $index) {
                $formattedRecord[$index] = isset($record[$propertyName]) ? $record[$propertyName] : NULL;
            }
        }

        $records[] = $formattedRecord;

        return TRUE;
    }

    public function postFormatRecords(array &$records = NULL) {
        parent::postFormatRecords($records);

        if (isset($records)) {
            $propertyNames = $this->propertyNames;
            if (!isset($propertyNames)) {
                foreach ($this->assembledColumnNames as $propertyName => $index) {
                    $propertyNames[$index] = $propertyName;
                }
            }
            array_unshift($records, $propertyNames);
        }
    }
}
