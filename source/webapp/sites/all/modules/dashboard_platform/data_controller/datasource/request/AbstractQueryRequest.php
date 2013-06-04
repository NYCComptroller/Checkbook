<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




abstract class AbstractQueryRequest extends AbstractRequest {

    protected $sourceName = NULL;
    public $queries = NULL;
    public $sortingConfigurations = NULL;
    public $limit = NULL;
    public $startWith = 0;

    public function __construct($sourceName) {
        parent::__construct();
        $this->sourceName = $sourceName;
    }

    public function __clone() {
        parent::__clone();
        $this->queries = ArrayHelper::cloneArray($this->queries);
        $this->sortingConfigurations = ArrayHelper::cloneArray($this->sortingConfigurations);
    }

    public function isSortingColumnPresent($columnName) {
        if (isset($this->sortingConfigurations)) {
            foreach ($this->sortingConfigurations as $sortingConfiguration) {
                if ($sortingConfiguration->propertyName == $columnName) {
                    return TRUE;
                }
            }
        }

        return FALSE;
    }

    protected function initiateSortingConfiguration($columnName, $isSortAscending) {
        return new PropertyBasedComparator_DefaultSortingConfiguration($columnName, $isSortAscending);
    }

    public function addSortingConfiguration(__PropertyBasedComparator_AbstractSortingConfiguration $sortingConfiguration) {
        $this->sortingConfigurations[] = $sortingConfiguration;
    }

    public function addSortingConfigurations(array $sortingConfigurations = NULL) {
        if (isset($sortingConfigurations)) {
            foreach ($sortingConfigurations as $sortingConfiguration) {
                $this->addSortingConfiguration($sortingConfiguration);
            }
        }
    }

    public function addOrderByColumn($directionalColumnName) {
        list($columnName, $isSortAscending) = PropertyBasedComparator_DefaultSortingConfiguration::parseDirectionalPropertyName($directionalColumnName);
        $this->addSortingConfiguration($this->initiateSortingConfiguration($columnName, $isSortAscending));
    }

    public function addOrderByColumns($directionalColumnNames) {
        if (isset($directionalColumnNames)) {
            if (is_array($directionalColumnNames)) {
                foreach ($directionalColumnNames as $directionalColumnName) {
                    $this->addOrderByColumn($directionalColumnName);
                }
            }
            else {
                $this->addOrderByColumn($directionalColumnNames);
            }
        }
    }

    public function setPagination($limit, $startWith = 0) {
        IntegerDataTypeHandler::checkNonNegativeInteger($limit);
        IntegerDataTypeHandler::checkNonNegativeInteger($startWith);

        $this->limit = $limit;
        $this->startWith = $startWith;
    }
}
