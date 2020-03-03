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




interface JoinController {

    /**
     * @param JoinController_SourceConfiguration $sourceConfigurationA
     * @param JoinController_SourceConfiguration $sourceConfigurationB
     * @return JoinController_SourceConfiguration
     */
    public function join(JoinController_SourceConfiguration $sourceConfigurationA, JoinController_SourceConfiguration $sourceConfigurationB);
}

class JoinController_SourceConfiguration extends AbstractObject {

    public $data = NULL;
    public $keyColumnNames = NULL;
    public $columnPrefix = NULL;
    public $columnSuffix = NULL;

    public $adjustedColumnNames = NULL;

    public function __construct(array $data = NULL, $keyColumnNames = NULL, $columnPrefix = NULL, $columnSuffix = NULL) {
        parent::__construct();
        $this->keyColumnNames = ArrayHelper::toArray($keyColumnNames);

        $this->columnPrefix = $columnPrefix;
        $this->columnSuffix = $columnSuffix;

        $this->data = $data;
    }

    public function checkRequiredKeyColumnNames() {
        if (!isset($this->keyColumnNames)) {
            throw new IllegalArgumentException(t('Key column names have not been defined'));
        }
    }

    protected function adjustColumnName($columnName) {
        return (isset($this->columnPrefix) ? $this->columnPrefix : '')
            . $columnName
            . (isset($this->columnSuffix) ? $this->columnSuffix : '');
    }

    protected function recalculateRecordColumnNames(array &$record) {
        $adjustedRecord = NULL;

        foreach ($record as $columnName => $columnValue) {
            if (isset($this->adjustedColumnNames[$columnName])) {
                $adjustedColumnName = $this->adjustedColumnNames[$columnName];
            }
            else {
                $isKeyColumnName = isset($this->keyColumnNames) && (array_search($columnName, $this->keyColumnNames) !== FALSE);
                $adjustedColumnName = ($isKeyColumnName)
                    ? $columnName // do not adjust key column name. It is used to join with other sources
                    : $this->adjustColumnName($columnName);
                $this->adjustedColumnNames[$columnName] = $adjustedColumnName;
            }

            $adjustedRecord[$adjustedColumnName] = $columnValue;
        }

        return $adjustedRecord;
    }

    public function adjustRecordColumnNames(array &$record) {
        // [OPTIMIZATION] There is no need to reprocess data if nothing will be changed anyway
        if (!isset($this->columnPrefix) && !isset($this->columnSuffix)) {
            return $record;
        }

        return $this->recalculateRecordColumnNames($record);
    }

    public function adjustDataColumnNames() {
        // [OPTIMIZATION] There is no need to reprocess data if nothing will be changed anyway
        if (!isset($this->columnPrefix) && !isset($this->columnSuffix)) {
            return $this->data;
        }

        $adjustedData = NULL;

        if (isset($this->data)) {
            foreach ($this->data as $record) {
                $adjustedData[] = $this->recalculateRecordColumnNames($record);
            }
        }

        return $adjustedData;
    }

    public static function getAdjustedSourceConfiguration(JoinController_SourceConfiguration $sourceConfiguration) {
        return new JoinController_SourceConfiguration($sourceConfiguration->adjustDataColumnNames(), $sourceConfiguration->keyColumnNames);
    }
}
