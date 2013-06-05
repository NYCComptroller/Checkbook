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
