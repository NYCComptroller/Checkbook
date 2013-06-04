<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


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
