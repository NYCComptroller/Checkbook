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

class CompositeColumnSection extends AbstractSelectColumnSection {

  private $function = NULL;

  public function __construct($function, $alias) {
    parent::__construct($alias);
    $this->function = $function;
  }

  public function parseColumns() {
    $callback = new ColumnStatementCompositeEntityParser__ColumnNameCollector();

    $parser = new ColumnStatementCompositeEntityParser();
    $parser->parse($this->function, array($callback, 'collectColumnNames'));

    return $callback->columnNames;
  }

  public function attachTo(AbstractTableSection $table) {
    $callback = new ColumnStatementCompositeEntityParser__ColumnNameUpdater($table, FALSE);

    $parser = new ColumnStatementCompositeEntityParser();
    $this->function = $parser->parse($this->function, array($callback, 'updateColumnNames'));

    $table->columns[] = $this;
  }

  public function event_updateTableAlias($oldTableAlias, $newTableAlias) {
    parent::event_updateTableAlias($oldTableAlias, $newTableAlias);

    $callback = new ColumnStatementCompositeEntityParser__TableAliasUpdater($oldTableAlias, $newTableAlias, FALSE);

    $parser = new ColumnStatementCompositeEntityParser();
    $this->function = $parser->parse($this->function, array($callback, 'updateTableAlias'));
  }

  public function assembleColumnName($tableAlias) {
    $callback = new ColumnStatementCompositeEntityParser__TableAliasUpdater(NULL, $tableAlias, TRUE);

    $parser = new ColumnStatementCompositeEntityParser();
    return $parser->parse($this->function, array($callback, 'updateTableAlias'));
  }
}
