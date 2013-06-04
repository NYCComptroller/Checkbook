<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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

    public function __construct($propertyName, $isSortAscending = TRUE) {
        parent::__construct();
        $this->propertyName = $propertyName;
        $this->isSortAscending = $isSortAscending;

        $this->checkPropertyName();
    }

    abstract protected function checkPropertyName();

    abstract public function formatPropertyNameAsDatabaseColumnName($maximumLength);

    public static function parseDirectionalPropertyName($directionalPropertyName) {
        $isSortAscending = TRUE;

        $propertyName = $directionalPropertyName;
        if ($directionalPropertyName{0} == self::$SORT_DIRECTION_DELIMITER__DESCENDING) {
            $isSortAscending = FALSE;
            $propertyName = substr($propertyName, 1);
        }

        return array($propertyName, $isSortAscending);
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
