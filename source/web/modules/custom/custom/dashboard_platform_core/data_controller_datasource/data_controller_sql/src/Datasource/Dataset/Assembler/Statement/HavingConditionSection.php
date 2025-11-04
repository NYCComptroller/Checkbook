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

class HavingConditionSection extends AbstractSection {

  /**
   * @var CompositeColumnSection|null
   */
  public $subject = NULL;
  /**
   * @var null
   */
  public $value = NULL;

  /**
   * HavingConditionSection constructor.
   * @param CompositeColumnSection $subject
   * @param $value
   */
  public function __construct(CompositeColumnSection $subject, $value) {
    parent::__construct();
    $this->subject = $subject;
    $this->value = $value;
  }

  /**
   * @param $oldTableAlias
   * @param $newTableAlias
   */
  public function event_updateTableAlias($oldTableAlias, $newTableAlias) {
    parent::event_updateTableAlias($oldTableAlias, $newTableAlias);

    $this->value->event_updateTableAlias($oldTableAlias, $newTableAlias);
  }

  /**
   * @param Statement $statement
   * @param $useTableNameAsAlias
   * @return string
   */
  public function assemble(Statement $statement, $useTableNameAsAlias) {
    // FIXME statement first table is 'facts'. Try to find better way to get the table instead of using [0]
    return $this->subject->assembleColumnName($statement->tables[0]->prepareColumnTableAlias($useTableNameAsAlias))
      . $this->value->assemble($statement, $useTableNameAsAlias);
  }
}

