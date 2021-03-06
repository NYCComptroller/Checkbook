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


abstract class AbstractRecordsHolder extends AbstractObject {

    // In most cases we will not use this property.
    // It is required only when provided record structure does not match to corresponding dataset
    /**
     * @var RecordMetaData
     */
    public $recordMetaData = NULL;

    /**
     * @var AbstractRecord[]
     */
    public $records = NULL;

    public $version = NULL;

    /**
     * @return AbstractRecord
     */
    abstract protected function initiateRecordInstance();

    /**
     * @return AbstractRecord
     */
    public function initiateRecord() {
        $recordInstance = $this->initiateRecordInstance();
        $this->registerRecordInstance($recordInstance);

        return $recordInstance;
    }

    public function registerRecordInstance(AbstractRecord $recordInstance) {
        $this->records[] = $recordInstance;
    }
}

class AssociativeRecordsHolder extends AbstractRecordsHolder {

    protected function initiateRecordInstance() {
        return new AssociativeRecord();
    }
}

class IndexedRecordsHolder extends AbstractRecordsHolder {

    protected function initiateRecordInstance() {
        return new IndexedRecord();
    }

    public function registerRecordColumnValues(&$indexedColumnValues) {
        $recordInstance = $this->initiateRecordInstance();
        $recordInstance->columnValues = $indexedColumnValues;

        $this->registerRecordInstance($recordInstance);
    }
}

abstract class AbstractRecord extends AbstractObject {

    abstract public function getColumnValue($identifier, $required = FALSE);
    abstract public function setColumnValue($identifier, $columnValue);
}

class AssociativeRecord extends AbstractRecord {

    public function getColumnValue($columnName, $required = FALSE) {
        $value = isset($this->$columnName) ? $this->$columnName : NULL;
        if (!isset($value) && $required) {
            throw new IllegalArgumentException(t("Value is not provided for '@columnName' column", array('@columnName' => $columnName)));
        }

        return $value;
    }

    public function setColumnValue($columnName, $columnValue) {
        if (isset($columnValue)) {
            $this->$columnName = $columnValue;
        }
        else {
            unset($this->$columnName);
        }
    }
}

class IndexedRecord extends AbstractRecord {

    public $columnValues = NULL;

    public function getColumnValue($columnIndex, $required = FALSE) {
        $value = isset($this->columnValues[$columnIndex]) ? $this->columnValues[$columnIndex] : NULL;
        if (!isset($value) && $required) {
            throw new IllegalArgumentException(t('Value is not provided for column with index @columnIndex', array('@columnIndex' => $columnIndex)));
        }

        return $value;
    }

    public function setColumnValue($columnIndex, $columnValue) {
        if (isset($columnValue)) {
            $this->columnValues[$columnIndex] = $columnValue;
        }
        else {
            unset($this->columnValues[$columnIndex]);
        }
    }
}
