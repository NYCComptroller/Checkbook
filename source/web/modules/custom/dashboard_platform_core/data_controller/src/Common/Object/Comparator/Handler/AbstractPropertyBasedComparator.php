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

namespace Drupal\data_controller\Common\Object\Comparator\Handler;

abstract class AbstractPropertyBasedComparator extends AbstractValueComparator {

    private $sortingConfigurations = NULL;

    public function registerDirectionalPropertyName($directionalPropertyName) {
        list($propertyName, $isSortAscending) = PropertyBasedComparator_DefaultSortingConfiguration::parseDirectionalPropertyName($directionalPropertyName);

        $this->registerSortingConfiguration(new PropertyBasedComparator_DefaultSortingConfiguration($propertyName, $isSortAscending));
    }

    public function registerDirectionalPropertyNames($directionalPropertyNames) {
        if (isset($directionalPropertyNames)) {
            foreach ((is_array($directionalPropertyNames) ? $directionalPropertyNames : array($directionalPropertyNames)) as $directionalPropertyName) {
                $this->registerDirectionalPropertyName($directionalPropertyName);
            }
        }
    }

    public function registerSortingConfiguration(__PropertyBasedComparator_AbstractSortingConfiguration $sortingConfiguration) {
        $this->sortingConfigurations[] = $sortingConfiguration;
    }

    public function registerSortingConfigurations($sortingConfigurations) {
        if (isset($sortingConfigurations)) {
            foreach ((is_array($sortingConfigurations) ? $sortingConfigurations : array($sortingConfigurations)) as $sortingConfiguration) {
                $this->registerSortingConfiguration($sortingConfiguration);
            }
        }
    }

    abstract protected function getProperty($record, $propertyName);

    public function compare($recordA, $recordB) {
        foreach ($this->sortingConfigurations as $sortingConfiguration) {
            $a = $this->getProperty($recordA, $sortingConfiguration->propertyName);
            $b = $this->getProperty($recordB, $sortingConfiguration->propertyName);

            $result = $this->compareSingleValue($a, $b, $sortingConfiguration->isSortAscending);
            if ($result != 0) {
                return $result;
            }
        }

        return 0;
    }
}
