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

use Drupal\data_controller\Common\Object\Exception\IllegalArgumentException;

class __CubeQueryRequest_Dimension extends __CubeQueryRequest_AbstractDimension {

    public $properties = NULL;

    public function getPropertyNames() {
        $propertyNames = NULL;

        if (isset($this->properties)) {
            foreach ($this->properties as $property) {
                $propertyNames[] = $property->name;
            }
        }

        return $propertyNames;
    }

    protected function findProperty($name) {
        if (isset($this->properties)) {
            foreach ($this->properties as $property) {
                if ($property->name === $name) {
                    return $property;
                }
            }
        }

        return NULL;
    }

    public function registerProperty($requestColumnIndex, $name) {
        if ($this->findProperty($name) != NULL) {
            throw new IllegalArgumentException(t('The property has been registered already: @propertyName', array('@propertyName' => $name)));
        }

        $property = new __CubeQueryRequest_Property($name);
        $property->requestColumnIndex = $requestColumnIndex;

        $this->properties[] = $property;
    }
}
