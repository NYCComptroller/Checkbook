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

namespace Drupal\checkbook_domain\Sql;

use Drupal\checkbook_domain\Common\EntityFactory;


class SqlRecordCountFactory extends EntityFactory {

  /**
   * @param StatementInterface $data
   *
   * @return int
   */
  public function create(\Drupal\Core\Database\StatementInterface $data) {
    $results = $data->fetchAll();
    return $results[0]->record_count;
  }

}
