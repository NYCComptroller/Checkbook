<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\data_controller_sql\Datasource\Dataset\Assembler\Statement;

use Drupal\data_controller\Common\Parameter\ReferencePathHelper;

class TableColumnConditionSectionValue extends ConditionSectionValue {

  /**
   * @var null
   */
  public $tableAlias = NULL;
  /**
   * @var null
   */
  public $columnName = NULL;

  /**
   * TableColumnConditionSectionValue constructor.
   * @param $tableAlias
   * @param $columnName
   */
  public function __construct($tableAlias, $columnName) {
    parent::__construct();
    $this->tableAlias = $tableAlias;
    $this->columnName = $columnName;
  }

  /**
   * @param $oldTableAlias
   * @param $newTableAlias
   */
  public function event_updateTableAlias($oldTableAlias, $newTableAlias) {
    parent::event_updateTableAlias($oldTableAlias, $newTableAlias);

    if ($oldTableAlias === $this->tableAlias) {
      $this->tableAlias = $newTableAlias;
    }
  }

  /**
   * @param Statement $statement
   * @param $useTableNameAsAlias
   * @return mixed|string
   * @throws IllegalArgumentException
   */
  public function assemble(Statement $statement, $useTableNameAsAlias) {
    $table = $statement->getTable($this->tableAlias);

    list($referencedDatasetName, $referencedColumnName) = ReferencePathHelper::splitReference($this->columnName);
    $column = $table->findColumnByAlias($referencedColumnName);

    return ' = ' . ColumnNameHelper::combineColumnName(
        $table->prepareColumnTableAlias($useTableNameAsAlias),
        (isset($column) && ($column instanceof ColumnSection)) ? $column->name : $referencedColumnName);
  }
}
