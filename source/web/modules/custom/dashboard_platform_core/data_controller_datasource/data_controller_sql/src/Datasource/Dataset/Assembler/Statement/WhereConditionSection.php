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

/**
 * Class WhereConditionSection
 */
class WhereConditionSection extends AbstractConditionSection {

  /**
   * @var null
   */
  public $subjectTableAlias = NULL;
  /**
   * @var null
   */
  public $subjectColumnType = null;

  /**
   * WhereConditionSection constructor.
   * @param $subjectTableAlias
   * @param $subjectColumnName
   * @param $joinValue
   * @param null $subjectColumnType
   */
  public function __construct($subjectTableAlias, $subjectColumnName, $joinValue, $subjectColumnType = null) {
        parent::__construct($subjectColumnName, $joinValue);
        $this->subjectTableAlias = $subjectTableAlias;
        $this->subjectColumnType = $subjectColumnType;
    }

  /**
   * @param $oldTableAlias
   * @param $newTableAlias
   */
  public function event_updateTableAlias($oldTableAlias, $newTableAlias) {
        if ($oldTableAlias === $this->subjectTableAlias) {
            $this->subjectTableAlias = $newTableAlias;
        }

        parent::event_updateTableAlias($oldTableAlias, $newTableAlias);
    }
}


