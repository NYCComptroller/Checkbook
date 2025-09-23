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

use Drupal\data_controller\Common\Object\Exception\IllegalStateException;

class SubquerySection extends AbstractTableSection {

  public $body = NULL;

  public function __construct($body, $alias = NULL) {
    parent::__construct($alias);
    $this->body = $body;
  }

  public function prepareColumnTableAlias($useTableNameAsAlias) {
    $columnTableAlias = $this->alias;
    if (!isset($columnTableAlias) && $useTableNameAsAlias) {
      throw new IllegalStateException(t('Sub query is required to have an alias'));
    }

    return $columnTableAlias;
  }

  public function assemble() {
    return '(' . $this->body . ')' . (isset($this->alias) ? (' ' . $this->alias)  : '');
  }
}

