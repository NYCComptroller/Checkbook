<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
*
* Copyright (C) 2012, 2013 New York City
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
*
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/




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

abstract class __PropertyBasedComparator_AbstractSortingConfiguration extends AbstractObject {

    public static $SORT_DIRECTION_DELIMITER__DESCENDING = '-';

    public $propertyName = NULL;
    public $isSortAscending = NULL;
    public $sortSourceByNull = NULL;
    public $sql=NULL;

    public function __construct($propertyName, $isSortAscending = TRUE,$sortSourceByNull=NULL) {
        parent::__construct();
        $this->propertyName = $propertyName;
        $this->isSortAscending = $isSortAscending;
        $this->sql = $sortSourceByNull;

        $this->checkPropertyName();
    }

    abstract protected function checkPropertyName();

    abstract public function formatPropertyNameAsDatabaseColumnName($maximumLength);

    public static function parseDirectionalPropertyName($directionalPropertyName,$sortSourceByNull=NULL) {
        $isSortAscending = TRUE;
        $propertyName = $directionalPropertyName;




        if ($directionalPropertyName{0} == self::$SORT_DIRECTION_DELIMITER__DESCENDING) {
            $isSortAscending = FALSE;
            $propertyName = substr($propertyName, 1);
            if (isset($sortSourceByNull)) {
                foreach ($sortSourceByNull as $value) {
                    if ($value == $propertyName) {
                        $sql = $propertyName . " " . "IS  NULL";
                    }
                }
            }
        }
        else if($directionalPropertyName{0} !== self::$SORT_DIRECTION_DELIMITER__DESCENDING) {
            if (isset($sortSourceByNull)) {
                foreach ($sortSourceByNull as $value) {
                    if ($value == $propertyName) {
                        $sql = $propertyName . " " . "IS NOT NULL";
                    }
                }
            }

        }

        return array($propertyName, $isSortAscending,$sql);
    }

    public static function assembleDirectionalPropertyName($propertyName, $isSortAscending) {
        return ($isSortAscending ? '' : self::$SORT_DIRECTION_DELIMITER__DESCENDING) . $propertyName;
    }
}

class PropertyBasedComparator_DefaultSortingConfiguration extends __PropertyBasedComparator_AbstractSortingConfiguration {

    protected function checkPropertyName() {
        ReferencePathHelper::checkReference($this->propertyName);
    }

    public function formatPropertyNameAsDatabaseColumnName($maximumLength) {
        return ReferencePathHelper::assembleDatabaseColumnName($maximumLength, $this->propertyName);
    }
}
