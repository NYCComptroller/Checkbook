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

namespace Drupal\data_controller\Datasource\Formatter\Handler;

use Drupal\data_controller\Common\Object\Manipulation\ArrayHelper;
use Drupal\data_controller\Datasource\Formatter\ResultFormatter;


class ColumnMappingResultFormatter extends AbstractResultFormatter {

    private $columnMappings;

    public function __construct(array $columnMappings, ResultFormatter $parent = NULL) {
        parent::__construct($parent);

        $this->columnMappings = $columnMappings;
    }

    public function __clone() {
        parent::__clone();

        $this->columnMappings = ArrayHelper::cloneArray($this->columnMappings);
    }

    public function getColumnMappings() {
        return $this->columnMappings;
    }

    protected function adjustPropertyName($propertyName) {
        $adjustedPropertyName = parent::adjustPropertyName($propertyName);

        return isset($this->columnMappings[$adjustedPropertyName]) ? $this->columnMappings[$adjustedPropertyName] : NULL;
    }
}
