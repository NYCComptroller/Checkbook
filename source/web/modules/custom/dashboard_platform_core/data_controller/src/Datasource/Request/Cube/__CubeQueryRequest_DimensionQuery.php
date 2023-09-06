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

namespace Drupal\data_controller\Datasource\Request\Cube;

class __CubeQueryRequest_DimensionQuery extends __CubeQueryRequest_AbstractDimension {

    public $values = NULL;

    public function getPropertyNames() {
        $propertyNames = NULL;

        if (isset($this->values)) {
            foreach ($this->values as $element) {
                if (isset($element->name)) {
                    $propertyNames[] = $element->name;
                }
            }
        }

        return $propertyNames;
    }

    protected function findPropertyValueElement($name) {
        if (isset($this->values)) {
            foreach ($this->values as $element) {
                if ($element->name == $name) {
                    return $element;
                }
            }
        }

        return NULL;
    }

    public function addPropertyValue($name, $value) {
        $element = $this->findPropertyValueElement($name);
        if (!isset($element)) {
            $element = new __CubeQueryRequest_PropertyValue($name);
            $this->values[] = $element;
        }
        $element->addPropertyValue($value);
    }

    public function addPropertyValues($name, $values) {
        foreach ($values as $value) {
            $this->addPropertyValue($name, $value);
        }
    }
}
