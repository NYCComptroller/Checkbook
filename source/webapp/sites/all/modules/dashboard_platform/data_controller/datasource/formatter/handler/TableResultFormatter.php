<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
* 
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
