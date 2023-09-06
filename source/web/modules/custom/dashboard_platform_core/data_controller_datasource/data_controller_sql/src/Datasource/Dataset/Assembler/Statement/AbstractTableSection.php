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

use Drupal\data_controller\Common\Object\Exception\IllegalArgumentException;

abstract class AbstractTableSection extends AbstractSection {

    public $alias = NULL;
    public $columns = NULL;
    public $conditions = NULL;

    public function __construct($alias) {
        parent::__construct();
        $this->alias = $alias;
    }

    public function event_updateTableAlias($oldTableAlias, $newTableAlias) {
        parent::event_updateTableAlias($oldTableAlias, $newTableAlias);

        if ($oldTableAlias === $this->alias) {
            $this->alias = $newTableAlias;
        }

        if (isset($this->columns)) {
            foreach ($this->columns as $column) {
                $column->event_updateTableAlias($oldTableAlias, $newTableAlias);
            }
        }

        if (isset($this->conditions)) {
            foreach ($this->conditions as $condition) {
                $condition->event_updateTableAlias($oldTableAlias, $newTableAlias);
            }
        }
    }

    public function findColumns($columnName) {
        $columns = NULL;

        if (isset($this->columns)) {
            foreach ($this->columns as $column) {
                if ($column instanceof ColumnSection) {
                    if ($column->name === $columnName) {
                        $columns[] = $column;
                    }
                }
            }
        }

        return $columns;
    }

    public function findColumn($columnName) {
        if (isset($this->columns)) {
            foreach ($this->columns as $column) {
                if ($column instanceof ColumnSection) {
                    if ($column->name === $columnName) {
                        return $column;
                    }
                }
                elseif ($column->alias === $columnName) {
                    return $column;
                }
            }
        }

        // looking for a column by alias
        if (isset($this->columns)) {
            foreach ($this->columns as $column) {
                if ($column instanceof ColumnSection) {
                    if ($column->alias === $columnName) {
                        return $column;
                    }
                }
            }
        }

        return NULL;
    }

    public function getColumn($columnName) {
        $column = $this->findColumn($columnName);
        if (!isset($column)) {
            throw new IllegalArgumentException(t(
            	"Column '@columnName' has not been registered for the table: @tableAlias",
                array('@columnName' => $columnName, '@tableAlias' => $this->alias)));
        }

        return $column;
    }

    /**
     * @param $columnAlias
     * @return AbstractColumnSection
     */
    public function findColumnByAlias($columnAlias) {
        if (isset($this->columns)) {
            foreach ($this->columns as $column) {
                if ($column->alias === $columnAlias) {
                    return $column;
                }
            }
        }

        return NULL;
    }

    abstract public function prepareColumnTableAlias($useTableNameAsAlias);

    abstract public function assemble();
}
