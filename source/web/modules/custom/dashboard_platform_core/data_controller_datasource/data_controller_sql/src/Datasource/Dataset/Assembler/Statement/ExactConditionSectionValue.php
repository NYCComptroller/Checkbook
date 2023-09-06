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

use Drupal\data_controller\Datasource\Operator\Handler\RegularExpressionOperatorHandler;

class ExactConditionSectionValue extends ConditionSectionValue {

  /**
   * @var null
   */
  public $value = NULL;
  /**
   * @var bool
   */
  public $stringOperation = false;

  /**
   * ExactConditionSectionValue constructor.
   * @param $value
   * @param bool $propertyValue
   */
  public function __construct($value, $propertyValue = false) {
    parent::__construct();
    $this->value = $value;
    if ($propertyValue && $propertyValue instanceof RegularExpressionOperatorHandler){
      $this->stringOperation = true;
    }
  }

  /**
   * @param $oldTableAlias
   * @param $newTableAlias
   * @throws UnsupportedOperationException
   */
  public function event_updateTableAlias($oldTableAlias, $newTableAlias) {
    parent::event_updateTableAlias($oldTableAlias, $newTableAlias);

    $callback = new ColumnStatementCompositeEntityParser__TableAliasUpdater($oldTableAlias, $newTableAlias, FALSE);

    $parser = new ColumnStatementCompositeEntityParser();
    $this->value = $parser->parse($this->value, array($callback, 'updateTableAlias'));
  }

  /**
   * @param Statement $statement
   * @param $useTableNameAsAlias
   * @return mixed
   * @throws UnsupportedOperationException
   */
  public function assemble(Statement $statement, $useTableNameAsAlias) {
    $callback = new ColumnStatementCompositeEntityParser__ColumnNameAdjuster(TRUE);

    $parser = new ColumnStatementCompositeEntityParser();
    return $parser->parse($this->value, array($callback, 'adjustCallbackObject'));
  }
}
