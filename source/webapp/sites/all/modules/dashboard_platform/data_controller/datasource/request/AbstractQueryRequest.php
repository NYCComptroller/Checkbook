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




abstract class AbstractQueryRequest extends AbstractRequest {

    protected $sourceName = NULL;
    public $queries = NULL;
    public $sortingConfigurations = NULL;
    public $limit = NULL;
    public $startWith = 0;
    public $logicalOrColumns = NULL;

    public function __construct($sourceName) {
        parent::__construct();
        $this->sourceName = $sourceName;
    }

    public function __clone() {
        parent::__clone();
        $this->queries = ArrayHelper::cloneArray($this->queries);
        $this->sortingConfigurations = ArrayHelper::cloneArray($this->sortingConfigurations);
        $this->logicalOrColumns = ArrayHelper::cloneArray($this->logicalOrColumns);
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

    protected function initiateSortingConfiguration($columnName, $isSortAscending,$sql=NULL) {
        return new PropertyBasedComparator_DefaultSortingConfiguration($columnName, $isSortAscending,$sql);
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

    public function addOrderByColumn($directionalColumnName,$customColumn) {
        list($columnName, $isSortAscending,$sql) = PropertyBasedComparator_DefaultSortingConfiguration::parseDirectionalPropertyName($directionalColumnName,$customColumn);
        $this->addSortingConfiguration($this->initiateSortingConfiguration($columnName, $isSortAscending,$sql));
    }

    public function addOrderByColumns($directionalColumnNames,$customColumn=Null) {
        if (isset($directionalColumnNames)) {
            if (is_array($directionalColumnNames)) {
                foreach ($directionalColumnNames as $directionalColumnName) {
                    $this->addOrderByColumn($directionalColumnName,$customColumn);
                }
            }
            else {
                $this->addOrderByColumn($directionalColumnNames,$customColumn);
            }
        }
    }

    public function setPagination($limit, $startWith = 0) {
        IntegerDataTypeHandler::checkNonNegativeInteger($limit);
        IntegerDataTypeHandler::checkNonNegativeInteger($startWith);

        $this->limit = $limit;
        $this->startWith = $startWith;
    }

    public function addLogicalOrColumns($logicalOrColumns) {
        if (isset($logicalOrColumns)) {
            $this->logicalOrColumns = $logicalOrColumns;
        }
    }
}
