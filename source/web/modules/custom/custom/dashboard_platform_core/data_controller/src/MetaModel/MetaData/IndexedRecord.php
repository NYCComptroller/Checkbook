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

class IndexedRecord extends AbstractRecord {

    public $columnValues = NULL;

    public function getColumnValue($columnIndex, $required = FALSE) {
        $value = isset($this->columnValues[$columnIndex]) ? $this->columnValues[$columnIndex] : NULL;
        if (!isset($value) && $required) {
            throw new IllegalArgumentException(t('Value is not provided for column with index @columnIndex', array('@columnIndex' => $columnIndex)));
        }

        return $value;
    }

    public function setColumnValue($columnIndex, $columnValue) {
        if (isset($columnValue)) {
            $this->columnValues[$columnIndex] = $columnValue;
        }
        else {
            unset($this->columnValues[$columnIndex]);
        }
    }
}
