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
use Drupal\data_controller\Common\Object\Exception\UnsupportedOperationException;

class ColumnSection extends AbstractSelectColumnSection {

  public $name = NULL;
  public $key = NULL;

  // TODO in the future it should be a reference to a table which contains this column
  // This functionality is used to support table alias in GROUP BY section which has a reference to this column
  // This functionality will be deleted when we implement more comprehensive object model for SQL generation
  protected $tableAlias = NULL;

  public function __construct($name, $alias = NULL) {
    parent::__construct($alias ?? $name);
    $this->name = $name;
  }

  public function event_updateTableAlias($oldTableAlias, $newTableAlias) {
    parent::event_updateTableAlias($oldTableAlias, $newTableAlias);

    if ($this->tableAlias == $oldTableAlias) {
      $this->tableAlias = $newTableAlias;
    }
  }

  public function attachTo(AbstractTableSection $table) {
    $attachedColumn = $table->findColumnByAlias($this->name);
    if (isset($attachedColumn)) {
      if (isset($this->alias)) {
        $attachedColumn->alias = $this->alias;
      }
      $attachedColumn->visible = $this->visible;
    }
    else {
      $attachedColumn = $this;

      $table->columns[] = $attachedColumn;
    }

    return $attachedColumn;
  }

  /**
   * @throws UnsupportedOperationException
   */
  public function assemble($tableAlias): string
  {
    return $this->assembleColumnName($tableAlias) . (($this->name === $this->alias) ? '' : (' AS ' . $this->alias));
  }

  public function assembleColumnName($tableAlias) {
    $selectedTableAlias = $this->tableAlias;

    if (isset($selectedTableAlias)) {
      if (isset($tableAlias) && ($selectedTableAlias != $tableAlias)) {
        throw new UnsupportedOperationException();
      }
    }
    else {
      $this->tableAlias = $tableAlias;
      $selectedTableAlias = $tableAlias;
    }

    return ColumnNameHelper::combineColumnName($selectedTableAlias, $this->name);
  }
}

