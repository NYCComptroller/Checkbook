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




abstract class AbstractResultFormatter extends AbstractObject implements ResultFormatter {

    /**
     * @var ResultFormatter|null
     */
    protected $parent = NULL;

    protected $stateChanged = FALSE;
    private $cachedPropertyNameMappings = NULL;

    public function __construct(ResultFormatter $parent = NULL) {
        parent::__construct();
        $this->setParent($parent);
    }

    public function __clone() {
        parent::__clone();

        $this->cachedPropertyNameMappings = ArrayHelper::cloneArray($this->cachedPropertyNameMappings);

        if (isset($this->parent)) {
            $this->parent = clone $this->parent;
        }
    }

    public function printFormattingPath() {
        $path = get_class($this);
        if (isset($this->parent)) {
            $path .= '(' . $this->parent->printFormattingPath() . ')';
        }

        return $path;

    }

    public function getParent($lastInChain = FALSE) {
        $parent = isset($this->parent) ? $this->parent : NULL;
        if (isset($parent) && $lastInChain) {
            $possibleParent = $parent->getParent($lastInChain);
            if (isset($possibleParent)) {
                $parent = $possibleParent;
            }
        }

        return $parent;
    }

    public function setParent(ResultFormatter $parent = NULL) {
        $this->parent = $parent;

        $this->stateChanged = TRUE;
    }

    public function addParent(ResultFormatter $parent = NULL) {
        $root = $this->getParent(TRUE);
        if (!isset($root)) {
            $root = $this;
        }

        $root->setParent($parent);
    }

    protected function isStateChanged() {
        $changed = $this->stateChanged;
        if (!$changed && isset($this->parent)) {
            $changed = $this->parent->isStateChanged();
        }

        return $changed;
    }

    protected function cleanState() {
        $this->cachedPropertyNameMappings = NULL;
        $this->stateChanged = FALSE;

        if (isset($this->parent)) {
            $this->parent->cleanState();
        }
    }

    protected function adjustPropertyName($propertyName) {
        $adjustedPropertyName = $propertyName;

        if (isset($this->parent) && isset($adjustedPropertyName)) {
            $adjustedPropertyName = $this->parent->adjustPropertyName($adjustedPropertyName);
        }

        return $adjustedPropertyName;
    }

    public function formatPropertyName($propertyName) {
        if ($this->isStateChanged()) {
            $this->cleanState();
        }

        if (isset($this->cachedPropertyNameMappings[$propertyName])) {
            $adjustedPropertyName = $this->cachedPropertyNameMappings[$propertyName];

            return ($adjustedPropertyName === FALSE) ? NULL : $adjustedPropertyName;
        }

        $adjustedPropertyName = $this->adjustPropertyName($propertyName);
        // checking if the same name is not mapped to two different properties
        if (isset($adjustedPropertyName) && isset($this->cachedPropertyNameMappings)) {
            $otherPropertyName = array_search($adjustedPropertyName, $this->cachedPropertyNameMappings);
            if ($otherPropertyName !== FALSE) {
                throw new IllegalStateException(t(
                    "Name mapping is ambiguous for '@propertyName' property: [@mappedPropertyNameA, @mappedPropertyNameB]",
                    array(
                        '@propertyName' => $adjustedPropertyName,
                        '@mappedPropertyNameA' => $otherPropertyName,
                        '@mappedPropertyNameB' => $propertyName)));
            }
        }
        $this->cachedPropertyNameMappings[$propertyName] = isset($adjustedPropertyName) ? $adjustedPropertyName : FALSE;

        return $adjustedPropertyName;
    }

    public function formatPropertyNames(array $propertyNames = NULL, $matchRequired = FALSE) {
        $updatedPropertyNames = NULL;

        if (isset($propertyNames)) {
            foreach ($propertyNames as $propertyName) {
                $formattedPropertyName = $this->formatPropertyName($propertyName);
                if (isset($formattedPropertyName)) {
                    $updatedPropertyNames[] = $formattedPropertyName;
                }
                elseif ($matchRequired) {
                    $this->errorRequiredFormattedPropertyName($propertyName);
                }
            }
        }

        return $updatedPropertyNames;
    }

    public function formatPropertyValue($propertyName, $propertyValue) {
        $formattedPropertyValue = $propertyValue;

        if (isset($this->parent)) {
            $formattedPropertyValue = $this->parent->formatPropertyValue($propertyName, $formattedPropertyValue);
        }

        return $formattedPropertyValue;
    }

    public function setRecordPropertyValue(array &$record = NULL, $propertyName, $propertyValue) {
        // Note: the following code does not work with $this->parent because
        //       formatPropertyName() and formatPropertyValue() are responsible for that

        $formattedPropertyName = $this->formatPropertyName($propertyName);
        // if formatted property name does not exist we do not need to store the property value
        if (isset($formattedPropertyName)) {
            $formattedPropertyValue = $this->formatPropertyValue($formattedPropertyName, $propertyValue);

            $record[$formattedPropertyName] = $formattedPropertyValue;
        }
    }

    public function formatRecord(array &$records = NULL, $record) {
        return isset($this->parent) ? $this->parent->formatRecord($records, $record) : FALSE;
    }

    public function postFormatRecords(array &$records = NULL) {
        if (isset($this->parent)) {
            $this->parent->postFormatRecords($records);
        }
    }

    public function reformatRecords(array &$records = NULL) {
        if (!isset($records)) {
            return;
        }

        LogHelper::log_debug(t("Using '!formatterClassName' to reformat result", array('!formatterClassName' => get_class($this))));

        $reformattedRecords = NULL;
        foreach ($records as $record) {
            $adjustedRecord = NULL;
            foreach ($record as $propertyName => $propertyValue) {
                $this->setRecordPropertyValue($adjustedRecord, $propertyName, $propertyValue);
            }

            if (!$this->formatRecord($reformattedRecords, $adjustedRecord)) {
                $reformattedRecords[] = $adjustedRecord;
            }
        }
        $this->postFormatRecords($reformattedRecords);

        $records = $reformattedRecords;
    }

    public function adjustDatasetQueryRequest(DataControllerCallContext $callcontext, DatasetQueryRequest $request) {
        if (isset($this->parent)) {
            $this->parent->adjustDatasetQueryRequest($callcontext, $request);
        }
    }

    public function adjustDatasetCountRequest(DataControllerCallContext $callcontext, DatasetCountRequest $request) {
        if (isset($this->parent)) {
            $this->parent->adjustDatasetCountRequest($callcontext, $request);
        }
    }

    public function adjustCubeQueryRequest(DataControllerCallContext $callcontext, CubeQueryRequest $request) {
        if (isset($this->parent)) {
            $this->parent->adjustCubeQueryRequest($callcontext, $request);
        }
    }

    public function adjustCubeCountRequest(DataControllerCallContext $callcontext, CubeQueryRequest $request) {
        if (isset($this->parent)) {
            $this->parent->adjustCubeCountRequest($callcontext, $request);
        }
    }

    protected function isClientSortingRequiredImpl() {
        return FALSE;
    }

    // TODO integrate with Data Controller
    public final function isClientSortingRequired() {
        return $this->isClientSortingRequiredImpl()
                    || (isset($this->parent) && $this->parent->isClientSortingRequired());
    }

    protected function isClientPaginationRequiredImpl() {
        return FALSE;
    }

    // TODO integrate with Data Controller
    public final function isClientPaginationRequired() {
        return $this->isClientSortingRequired()
                   || $this->isClientPaginationRequiredImpl()
                   || (isset($this->parent) && $this->parent->isClientPaginationRequired());
    }

    protected function errorRequiredFormattedPropertyName($propertyName) {
        throw new IllegalArgumentException(t('Could not format property name: @propertyName', array('@propertyName' => $propertyName)));
    }

    protected function errorUnsupportedChainOfResultFormatters() {
        throw new IllegalStateException(t('Unsupported chain of result formatters'));
    }
}
