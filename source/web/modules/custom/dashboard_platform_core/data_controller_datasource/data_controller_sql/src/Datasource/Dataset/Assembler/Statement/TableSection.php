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

class TableSection extends AbstractTableSection {

    public $name = NULL;

    public function __construct($name, $alias = NULL) {
        parent::__construct($alias);
        $this->name = $name;
    }

    public function prepareColumnTableAlias($useTableNameAsAlias) {
        return isset($this->alias) ? $this->alias : ($useTableNameAsAlias ? $this->name : NULL);
    }

    protected function assembleTableName() {
        return $this->name;
    }

    public function assemble() {
        return $this->assembleTableName() . (isset($this->alias) ? (' ' . $this->alias)  : '');
    }
}
