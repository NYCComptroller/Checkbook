<?php

namespace Drupal\data_controller\Datasource\Request\Cube;

use Drupal\data_controller\Common\Datatype\Handler\StringDataTypeHandler;
use Drupal\data_controller\Common\Object\Comparator\Handler\__PropertyBasedComparator_AbstractSortingConfiguration;
use Drupal\data_controller\Common\Parameter\ParameterHelper;

class __CubeQueryRequest_SortingConfiguration extends __PropertyBasedComparator_AbstractSortingConfiguration {

    private $elementName = NULL; // dimension or measure name
    private $subElementName = NULL; // level name
    private $elementPropertyName = NULL;

    public function __construct($columnName, $isSortAscending) {
        list($this->elementName, $this->subElementName, $this->elementPropertyName) = ParameterHelper::splitName($columnName);
        parent::__construct($columnName, $isSortAscending);
    }

    protected function checkPropertyName() {
        // checking dimension or measure
        StringDataTypeHandler::checkValueAsWord($this->elementName);
        // checking level
        StringDataTypeHandler::checkValueAsWord($this->subElementName);
        // checking property
        StringDataTypeHandler::checkValueAsWord($this->elementPropertyName);
    }

    public function formatPropertyNameAsDatabaseColumnName($maximumLength) {
        return ParameterHelper::assembleDatabaseColumnName($maximumLength, $this->elementName, $this->subElementName, $this->elementPropertyName);
    }

    public function getElementName() {
        return $this->elementName;
    }

    public function getSubElementName() {
        return $this->subElementName;
    }

    public function getElementPropertyName() {
        return $this->elementPropertyName;
    }
}
