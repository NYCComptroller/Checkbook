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

/**
 * Class AbstractConditionSection
 */
abstract class AbstractConditionSection extends AbstractSection {

  /**
   * @var null
   */
  public $subjectColumnName = NULL;
  /**
   * @var null
   */
  public $joinValue = NULL;

  public $subjectColumnType = null;

  /**
   * AbstractConditionSection constructor.
   * @param $subjectColumnName
   * @param $joinValue
   */
  public function __construct($subjectColumnName, $joinValue) {
        parent::__construct();
        $this->subjectColumnName = $subjectColumnName;
        $this->joinValue = $joinValue;
    }

  /**
   * @param $oldTableAlias
   * @param $newTableAlias
   */
  public function event_updateTableAlias($oldTableAlias, $newTableAlias) {
        parent::event_updateTableAlias($oldTableAlias, $newTableAlias);

        $this->joinValue->event_updateTableAlias($oldTableAlias, $newTableAlias);
    }

  /**
   * @param Statement $statement
   * @param AbstractTableSection $table
   * @param $useTableNameAsAlias
   * @return string
   */
  public function assemble(Statement $statement, AbstractTableSection $table, $useTableNameAsAlias) {
        list($referencedDatasetName, $referencedColumnName) = ReferencePathHelper::splitReference($this->subjectColumnName);

        $columnByAlias = $table->findColumnByAlias($referencedColumnName);
        $column = isset($columnByAlias) ? $table->findColumn($referencedColumnName) : NULL;

        $selectedColumn = isset($columnByAlias) && isset($column)
            ? $column
            : $columnByAlias;

        $tableAlias = $table->prepareColumnTableAlias($useTableNameAsAlias);

        // We need to cast integer DB fields for autocomplete
        // CAST(s0_b.release_approved_year AS text)  ~* '(.* 201.*)|(^201.*)'
        $sql_column = isset($selectedColumn) ? $selectedColumn->assembleColumnName($tableAlias) : ColumnNameHelper::combineColumnName($tableAlias, $referencedColumnName);
        if (('integer' === $this->subjectColumnType??false) && ($this->joinValue->stringOperation??false)) {
          $sql_column = " CAST({$sql_column} AS text) ";
        }

        return
          $sql_column
                . $this->joinValue->assemble($statement, $useTableNameAsAlias);
    }
}
