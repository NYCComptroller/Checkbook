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




class QueryKeyResultFormatter extends AbstractArrayResultFormatter {

    private $keyColumnNames = NULL;
    private $isColumnValueUnique = TRUE;

    public function __construct($keyColumnNames, $isColumnValueUnique = TRUE, ResultFormatter $parent = NULL) {
        parent::__construct($parent);

        $this->keyColumnNames = is_array($keyColumnNames) ? $keyColumnNames : array($keyColumnNames);
        $this->isColumnValueUnique = $isColumnValueUnique;
    }

    public function __clone() {
        parent::__clone();

        $this->keyColumnNames = ArrayHelper::cloneArray($this->keyColumnNames);
    }

    public function formatRecord(array &$records = NULL, $record) {
        $result = parent::formatRecord($records, $record);
        if ($result) {
            $this->errorUnsupportedChainOfResultFormatters();
        }

        $recordKey = NULL;
        foreach ($this->keyColumnNames as $columnName) {
            $recordKey[] = isset($record[$columnName]) ? $record[$columnName] : NULL;
        }

        $key = ArrayHelper::prepareCompositeKey($recordKey);
        if (isset($records[$key])) {
            if ($this->isColumnValueUnique) {
                throw new IllegalArgumentException(t(
                	'Found several records for the key: @key',
                    array('@key' => ArrayHelper::printArray($recordKey, ', ', TRUE, TRUE))));
            }

            $records[$key][] = $record;
        }
        else {
            if ($this->isColumnValueUnique) {
                $records[$key] = $record;
            }
            else {
                $records[$key][] = $record;
            }
        }

        return TRUE;
    }
}
