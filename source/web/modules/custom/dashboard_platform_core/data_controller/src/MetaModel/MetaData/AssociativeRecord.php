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

namespace Drupal\data_controller\MetaModel\MetaData;

use Drupal\data_controller\Common\Object\Exception\IllegalArgumentException;

class AssociativeRecord extends AbstractRecord {

    public function getColumnValue($columnName, $required = FALSE) {
        $value = isset($this->$columnName) ? $this->$columnName : NULL;
        if (!isset($value) && $required) {
            throw new IllegalArgumentException(t("Value is not provided for '@columnName' column", array('@columnName' => $columnName)));
        }

        return $value;
    }

    public function setColumnValue($columnName, $columnValue) {
        if (isset($columnValue)) {
            $this->$columnName = $columnValue;
        }
        else {
            unset($this->$columnName);
        }
    }
}
