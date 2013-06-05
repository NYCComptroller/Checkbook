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


class RecordMetaData extends AbstractMetaData {

    /**
     * @var ColumnMetaData[]
     */
    public $columns = array();

    public function __clone() {
        parent::__clone();

        $this->columns = ArrayHelper::cloneArray($this->columns);
    }

    protected function getEntityName() {
        return t('Record Set');
    }

    public function finalize() {
        parent::finalize();

        foreach ($this->columns as $column) {
            $column->finalize();
        }

        // soring columns by columnIndex
        sort_records($this->columns, new PropertyBasedComparator_DefaultSortingConfiguration('columnIndex'));
    }

    public function isComplete() {
        // this meta data have some calculated properties.
        // It has to be marked as complete once those properties are prepared
        return isset($this->complete) ? $this->complete : FALSE;
    }

    public function initializeFrom($sourceRecordMetaData) {
        parent::initializeFrom($sourceRecordMetaData);

        // preparing list of columns
        $sourceColumns = ObjectHelper::getPropertyValue($sourceRecordMetaData, 'columns');
        if (isset($sourceColumns)) {
            $this->initializeColumnsFrom($sourceColumns);
        }
    }

    public function initializeColumnsFrom($sourceColumns) {
        if (isset($sourceColumns)) {
            // source columns can have different column index
            $columnIndexFound = FALSE;
            foreach ($sourceColumns as $sourceColumn) {
                $sourceColumnIndex = ObjectHelper::getPropertyValue($sourceColumn, 'columnIndex');
                if (isset($sourceColumnIndex)) {
                    $columnIndexFound = TRUE;
                    break;
                }
            }

            // we should invalidate column index for existing columns before adding/updating columns
            if ($columnIndexFound) {
                $this->invalidateColumnIndexes();
            }

            foreach ($sourceColumns as $sourceColumn) {
                $this->initializeColumnFrom($sourceColumn);
            }
        }
    }

    // TODO try to eliminate this function.
    // For now it is needed because we could define just a few columns in .json configuration and than call database to get rest of the columns
    public function invalidateColumnIndexes() {
        foreach ($this->columns as $column) {
            $column->columnIndex = NULL;
        }
    }

    public function initializeColumnFrom($sourceColumn) {
        $sourceColumnName = ObjectHelper::getPropertyValue($sourceColumn, 'name');

        $column = $this->findColumn($sourceColumnName);
        $isColumnNew = !isset($column);

        if ($isColumnNew) {
            $column = $this->initiateColumn();

            $sourceColumnIndex = ObjectHelper::getPropertyValue($sourceColumn, 'columnIndex');
            if (!isset($sourceColumnIndex)) {
                // we do not check for last column index here and that is correct
                // we just assign index based on column count
                $column->columnIndex = count($this->columns);
            }
        }

        $column->initializeFrom($sourceColumn);

        if ($isColumnNew) {
            $this->registerColumnInstance($column);
        }

        return $column;
    }

    public function initiateColumn() {
        return new ColumnMetaData();
    }

    public function registerColumn($columnName) {
        $column = $this->initiateColumn();
        $column->name = $columnName;

        // preparing column index
        $lastColumnIndex = $this->findLastColumnIndex();
        $column->columnIndex = isset($lastColumnIndex)
            ? (($lastColumnIndex >= 0) ? ($lastColumnIndex + 1) : 0)
            : 0;

        $this->registerColumnInstance($column);

        return $column;
    }

    public function registerColumnInstance(ColumnMetaData $unregisteredColumn) {
        $existingColumn = $this->findColumn($unregisteredColumn->name);
        if (isset($existingColumn)) {
            $this->errorColumnFound($existingColumn);
        }

        foreach ($this->columns as $column) {
            if (isset($column->columnIndex) && ($unregisteredColumn->columnIndex === $column->columnIndex)) {
                $this->errorTwoColumnsWithSameIndexFound($column, $unregisteredColumn);
            }
        }

        $this->columns[] = $unregisteredColumn;
    }

    public function unregisterColumn($columnName) {
        for ($i = 0, $count = $this->getColumnCount(FALSE); $i < $count; $i++) {
            $column = $this->columns[$i];
            if ($column->name === $columnName) {
                unset($this->columns[$i]);
                return;
            }
        }

        $this->errorColumnNotFound($columnName);
    }

    /**
     * @param boolean $usedOnly
     * @return ColumnMetaData[]
     */
    public function getColumns($usedOnly = TRUE) {
        $columns = array();

        foreach ($this->columns as $column) {
            if (!$usedOnly || $column->isUsed()) {
                if (isset($columns[$column->columnIndex])) {
                    $this->errorTwoColumnsWithSameIndexFound($columns[$column->columnIndex], $column);
                }

                $columns[$column->columnIndex] = $column;
            }
        }

        return $columns;
    }

    public function findColumnNames($usedOnly = TRUE) {
        $columnNames = NULL;

        foreach ($this->columns as $column) {
            if (!$usedOnly || $column->isUsed()) {
                if (isset($columnNames[$column->columnIndex])) {
                    $this->errorTwoColumnsWithSameIndexFound($columnNames[$column->columnIndex], $column);
                }

                $columnNames[$column->columnIndex] = $column->name;
            }
        }

        return $columnNames;
    }

    public function getColumnNames($usedOnly = TRUE) {
        $columns = $this->findColumnNames($usedOnly);
        if (!isset($columns)) {
            $this->errorColumnsAreNotDefined();
        }

        return $columns;
    }

    public function findColumn($columnName) {
        foreach ($this->columns as $column) {
            if ($column->name === $columnName) {
                return $column;
            }
        }

        return NULL;
    }

    /**
     * @param $columnName
     * @return ColumnMetaData
     */
    public function getColumn($columnName) {
        $column = $this->findColumn($columnName);
        if (!isset($column)) {
            $this->errorColumnNotFound($columnName);
        }

        return $column;
    }

    public function findColumnByAlias($columnAlias) {
        foreach ($this->columns as $column) {
            if ($column->alias === $columnAlias) {
                return $column;
            }
        }

        return NULL;
    }

    public function getColumnByAlias($columnAlias) {
        $column = $this->findColumnByAlias($columnAlias);
        if (!isset($column)) {
            $this->errorColumnNotFound($columnAlias);
        }

        return $column;
    }

    /**
     * @param $columnIndex
     * @return ColumnMetaData|null
     */
    public function findColumnByIndex($columnIndex) {
        foreach ($this->columns as $column) {
            if ($column->columnIndex == $columnIndex) {
                return $column;
            }
        }

        return NULL;
    }

    public function getColumnByIndex($columnIndex) {
        $column = $this->findColumnByIndex($columnIndex);
        if (!isset($column)) {
            $this->errorColumnNotFound($columnIndex);
        }

        return $column;
    }

    /**
     * Prepares a list of used key columns
     *
     * @param boolean $indexResultByColumnIndex
     * @return ColumnMetaData[]|null
     */
    public function findKeyColumns() {
        $keyColumns = NULL;

        foreach ($this->columns as $column) {
            if ($column->isUsed() && $column->isKey()) {
                if (isset($keyColumns[$column->columnIndex])) {
                    $this->errorTwoColumnsWithSameIndexFound($keyColumns[$column->columnIndex], $column);
                }

                $keyColumns[$column->columnIndex] = $column;
            }
        }

        return $keyColumns;
    }

    /**
     * @return ColumnMetaData[]
     */
    public function getKeyColumns() {
        $keyColumns = $this->findKeyColumns();
        if (!isset($keyColumns)) {
            $this->errorKeyColumnNotFound();
        }

        return $keyColumns;
    }

    public function findKeyColumnNames() {
        $keyColumnNames = NULL;

        $keyColumns = $this->findKeyColumns();
        if (isset($keyColumns)) {
            foreach ($keyColumns as $index => $keyColumn) {
                $keyColumnNames[$index] = $keyColumn->name;
            }
        }

        return $keyColumnNames;
    }

    public function getKeyColumnNames() {
        $keyColumnNames = $this->findKeyColumnNames();
        if (!isset($keyColumnNames)) {
            $this->errorKeyColumnNotFound();
        }

        return $keyColumnNames;
    }

    public function findKeyColumn() {
        $keyColumns = $this->findKeyColumns();
        if (!isset($keyColumns)) {
            return NULL;
        }
        elseif (count($keyColumns) > 1) {
            throw new UnsupportedOperationException(t('Composite key is not supported for this request'));
        }
        else {
            return reset($keyColumns);
        }
    }

    public function getKeyColumn() {
        $column = $this->findKeyColumn();
        if (!isset($column)) {
            $this->errorKeyColumnNotFound();
        }

        return $column;
    }

    public function findNonKeyColumns() {
        $columns = NULL;

        foreach ($this->getColumns() as $columnIndex => $column) {
            if ($column->isKey()) {
                continue;
            }

            $columns[$columnIndex] = $column;
        }

        return $columns;
    }

    public function findNonKeyColumnNames() {
        $columnNames = array();

        $columns = $this->findNonKeyColumns();
        if (isset($columns)) {
            foreach ($columns as $index => $column) {
                $columnNames[$index] = $column->name;
            }
        }

        return $columnNames;
    }

    public function getColumnCount($usedOnly = TRUE) {
        $count = 0;
        foreach ($this->columns as $column) {
            if ($usedOnly && !$column->isUsed()) {
                continue;
            }

            $count++;
        }

        return $count;
    }

    public function findLastColumnIndex() {
        $lastColumnIndex = NULL;

        foreach ($this->columns as $column) {
            $lastColumnIndex = MathHelper::max($lastColumnIndex, $column->columnIndex);
        }

        return $lastColumnIndex;
    }

    protected function errorColumnsAreNotDefined() {
        throw new IllegalArgumentException(t(
        	"Columns have not been defined for @publicName @entityName",
            array(
            	'@publicName' => (isset($this->publicName) ? "'$this->publicName'" : 'the'),
            	'@entityName' => strtolower($this->getEntityName()))));
    }

    protected function errorKeyColumnNotFound() {
        throw new IllegalArgumentException(t(
        	"Key column has not been defined for @publicName @entityName",
            array(
            	'@publicName' => (isset($this->publicName) ? "'$this->publicName'" : 'the'),
            	'@entityName' => strtolower($this->getEntityName()))));
    }

    protected function errorColumnFound($column) {
        throw new IllegalArgumentException(t(
        	"Column '@columnName' has been already registered in @publicName @entityName",
            array(
            	'@columnName' => $column->name,
            	'@publicName' => (isset($this->publicName) ? "'$this->publicName'" : 'the'),
            	'@entityName' => strtolower($this->getEntityName()))));
    }

    protected function errorColumnNotFound($columnName) {
        throw new IllegalArgumentException(t(
        	"Column '@columnName' is not registered in @publicName @entityName",
            array(
            	'@columnName' => $columnName,
            	'@publicName' => (isset($this->publicName) ? "'$this->publicName'" : 'the'),
            	'@entityName' => strtolower($this->getEntityName()))));
    }

    protected function errorColumnByIndexNotFound($columnIndex) {
        throw new IllegalArgumentException(t(
        	"Column with index @columnIndex is not registered in @publicName @entityName",
            array(
            	'@columnIndex' => $columnIndex,
            	'@publicName' => (isset($this->publicName) ? "'$this->publicName'" : 'the'),
            	'@entityName' => strtolower($this->getEntityName()))));
    }

    protected function errorTwoColumnsWithSameIndexFound(ColumnMetaData $columnA, ColumnMetaData $columnB) {
        throw new IllegalArgumentException(t(
        	"Several columns with index @columnIndex has been registered in @publicName @entityName: [@columnNameA, @columnNameB]",
            array(
            	'@columnIndex' => $columnA->columnIndex,
            	'@publicName' => (isset($this->publicName) ? "'$this->publicName'" : 'the'),
                '@entityName' => strtolower($this->getEntityName()),
                '@columnNameA' => $columnA->publicName,
                '@columnNameB' => $columnB->publicName)));
    }
}
