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


class RecordMetaData extends AbstractMetaData
{

  /**
   * @var ColumnMetaData[]
   */
  public $columns = [];

  /**
   *
   */
  public function __clone()
  {
    parent::__clone();

    $this->columns = ArrayHelper::cloneArray($this->columns);
  }

  /**
   * @return int|string|null
   */
  protected function getEntityName()
  {
    return t('Record Set');
  }

  /**
   * @throws Exception
   */
  public function finalize()
  {
    parent::finalize();

    foreach ($this->columns as $column) {
      $column->finalize();
    }

    // soring columns by columnIndex
    sort_records($this->columns, new PropertyBasedComparator_DefaultSortingConfiguration('columnIndex'));
  }

  /**
   * @return bool|null
   */
  public function isComplete()
  {
    // this meta data have some calculated properties.
    // It has to be marked as complete once those properties are prepared
    return isset($this->complete) ? $this->complete : FALSE;
  }

  /**
   * @param $sourceRecordMetaData
   * @throws IllegalArgumentException
   */
  public function initializeFrom($sourceRecordMetaData)
  {
    parent::initializeFrom($sourceRecordMetaData);

    // preparing list of columns
    $sourceColumns = ObjectHelper::getPropertyValue($sourceRecordMetaData, 'columns');
    if (isset($sourceColumns)) {
      $this->initializeColumnsFrom($sourceColumns);
    }
  }

  /**
   * @param $sourceColumns
   * @throws IllegalArgumentException
   */
  public function initializeColumnsFrom($sourceColumns)
  {
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
  /**
   *
   */
  public function invalidateColumnIndexes()
  {
    foreach ($this->columns as $column) {
      $column->columnIndex = NULL;
    }
  }

  /**
   * @param $sourceColumn
   * @return ColumnMetaData|null
   * @throws IllegalArgumentException
   */
  public function initializeColumnFrom($sourceColumn)
  {
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

  /**
   * @return ColumnMetaData
   */
  public function initiateColumn()
  {
    return new ColumnMetaData();
  }

  /**
   * @param $columnName
   * @return ColumnMetaData
   * @throws IllegalArgumentException
   * @throws UnsupportedOperationException
   */
  public function registerColumn($columnName)
  {
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

  /**
   * @param ColumnMetaData $unregisteredColumn
   * @throws IllegalArgumentException
   */
  public function registerColumnInstance(ColumnMetaData $unregisteredColumn)
  {
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

  /**
   * @param $columnName
   * @throws IllegalArgumentException
   */
  public function unregisterColumn($columnName)
  {
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
   * @throws IllegalArgumentException
   */
  public function getColumns($usedOnly = TRUE)
  {
    $columns = [];

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

  /**
   * @param bool $usedOnly
   * @return |null
   * @throws IllegalArgumentException
   */
  public function findColumnNames($usedOnly = TRUE)
  {
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

  /**
   * @param bool $usedOnly
   * @return |null
   * @throws IllegalArgumentException
   */
  public function getColumnNames($usedOnly = TRUE)
  {
    $columns = $this->findColumnNames($usedOnly);
    if (!isset($columns)) {
      $this->errorColumnsAreNotDefined();
    }

    return $columns;
  }

  /**
   * @param $columnName
   * @return ColumnMetaData|null
   */
  public function findColumn($columnName)
  {
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
   * @throws IllegalArgumentException
   */
  public function getColumn($columnName)
  {
    $column = $this->findColumn($columnName);
    if (!isset($column)) {
      $this->errorColumnNotFound($columnName);
    }

    return $column;
  }

  /**
   * @param $columnAlias
   * @return ColumnMetaData|null
   */
  public function findColumnByAlias($columnAlias)
  {
    foreach ($this->columns as $column) {
      if ($column->alias === $columnAlias) {
        return $column;
      }
    }

    return NULL;
  }

  /**
   * @param $columnAlias
   * @return ColumnMetaData|null
   * @throws IllegalArgumentException
   */
  public function getColumnByAlias($columnAlias)
  {
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
  public function findColumnByIndex($columnIndex)
  {
    foreach ($this->columns as $column) {
      if ($column->columnIndex == $columnIndex) {
        return $column;
      }
    }

    return NULL;
  }

  /**
   * @param $columnIndex
   * @return ColumnMetaData|null
   * @throws IllegalArgumentException
   */
  public function getColumnByIndex($columnIndex)
  {
    $column = $this->findColumnByIndex($columnIndex);
    if (!isset($column)) {
      $this->errorColumnNotFound($columnIndex);
    }

    return $column;
  }

  /**
   * Prepares a list of used key columns
   *
   * @return ColumnMetaData[]|null
   * @throws IllegalArgumentException
   */
  public function findKeyColumns()
  {
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
   * @throws IllegalArgumentException
   */
  public function getKeyColumns()
  {
    $keyColumns = $this->findKeyColumns();
    if (!isset($keyColumns)) {
      $this->errorKeyColumnNotFound();
    }

    return $keyColumns;
  }

  /**
   * @return |null
   * @throws IllegalArgumentException
   */
  public function findKeyColumnNames()
  {
    $keyColumnNames = NULL;

    $keyColumns = $this->findKeyColumns();
    if (isset($keyColumns)) {
      foreach ($keyColumns as $index => $keyColumn) {
        $keyColumnNames[$index] = $keyColumn->name;
      }
    }

    return $keyColumnNames;
  }

  /**
   * @return |null
   * @throws IllegalArgumentException
   */
  public function getKeyColumnNames()
  {
    $keyColumnNames = $this->findKeyColumnNames();
    if (!isset($keyColumnNames)) {
      $this->errorKeyColumnNotFound();
    }

    return $keyColumnNames;
  }

  /**
   * @return ColumnMetaData|mixed|null
   * @throws IllegalArgumentException
   * @throws UnsupportedOperationException
   */
  public function findKeyColumn()
  {
    $keyColumns = $this->findKeyColumns();
    if (!isset($keyColumns)) {
      return NULL;
    } elseif (count($keyColumns) > 1) {
      throw new UnsupportedOperationException(t('Composite key is not supported for this request'));
    } else {
      return reset($keyColumns);
    }
  }

  /**
   * @return ColumnMetaData|mixed|null
   * @throws IllegalArgumentException
   * @throws UnsupportedOperationException
   */
  public function getKeyColumn()
  {
    $column = $this->findKeyColumn();
    if (!isset($column)) {
      $this->errorKeyColumnNotFound();
    }

    return $column;
  }

  /**
   * @return null |null
   * @throws IllegalArgumentException
   */
  public function findNonKeyColumns()
  {
    $columns = NULL;

    foreach ($this->getColumns() as $columnIndex => $column) {
      if ($column->isKey()) {
        continue;
      }

      $columns[$columnIndex] = $column;
    }

    return $columns;
  }

  /**
   * @return array
   * @throws IllegalArgumentException
   */
  public function findNonKeyColumnNames()
  {
    $columnNames = [];

    $columns = $this->findNonKeyColumns();
    if (isset($columns)) {
      foreach ($columns as $index => $column) {
        $columnNames[$index] = $column->name;
      }
    }

    return $columnNames;
  }

  /**
   * @param bool $usedOnly
   * @return int
   */
  public function getColumnCount($usedOnly = TRUE)
  {
    $count = 0;
    foreach ($this->columns as $column) {
      if ($usedOnly && !$column->isUsed()) {
        continue;
      }

      $count++;
    }

    return $count;
  }

  /**
   * @return mixed|null
   * @throws UnsupportedOperationException
   */
  public function findLastColumnIndex()
  {
    $lastColumnIndex = NULL;

    foreach ($this->columns as $column) {
      $lastColumnIndex = MathHelper::max($lastColumnIndex, $column->columnIndex);
    }

    return $lastColumnIndex;
  }

  /**
   * @throws IllegalArgumentException
   */
  protected function errorColumnsAreNotDefined()
  {
    throw new IllegalArgumentException(t(
      "Columns have not been defined for @publicName @entityName",
      array(
        '@publicName' => (isset($this->publicName) ? "'$this->publicName'" : 'the'),
        '@entityName' => strtolower($this->getEntityName()))));
  }

  /**
   * @throws IllegalArgumentException
   */
  protected function errorKeyColumnNotFound()
  {
    throw new IllegalArgumentException(t(
      "Key column has not been defined for @publicName @entityName",
      array(
        '@publicName' => (isset($this->publicName) ? "'$this->publicName'" : 'the'),
        '@entityName' => strtolower($this->getEntityName()))));
  }

  /**
   * @param $column
   * @throws IllegalArgumentException
   */
  protected function errorColumnFound($column)
  {
    throw new IllegalArgumentException(t(
      "Column '@columnName' has been already registered in @publicName @entityName",
      array(
        '@columnName' => $column->name,
        '@publicName' => (isset($this->publicName) ? "'$this->publicName'" : 'the'),
        '@entityName' => strtolower($this->getEntityName()))));
  }

  /**
   * @param $columnName
   * @throws IllegalArgumentException
   */
  protected function errorColumnNotFound($columnName)
  {
    throw new IllegalArgumentException(t(
      "Column '@columnName' is not registered in @publicName @entityName",
      array(
        '@columnName' => $columnName,
        '@publicName' => (isset($this->publicName) ? "'$this->publicName'" : 'the'),
        '@entityName' => strtolower($this->getEntityName()))));
  }

  /**
   * @param $columnIndex
   * @throws IllegalArgumentException
   */
  protected function errorColumnByIndexNotFound($columnIndex)
  {
    throw new IllegalArgumentException(t(
      "Column with index @columnIndex is not registered in @publicName @entityName",
      array(
        '@columnIndex' => $columnIndex,
        '@publicName' => (isset($this->publicName) ? "'$this->publicName'" : 'the'),
        '@entityName' => strtolower($this->getEntityName()))));
  }

  /**
   * @param ColumnMetaData $columnA
   * @param ColumnMetaData $columnB
   * @throws IllegalArgumentException
   */
  protected function errorTwoColumnsWithSameIndexFound(ColumnMetaData $columnA, ColumnMetaData $columnB)
  {
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
