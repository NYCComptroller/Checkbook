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




class RowFlattenerResultFormatter extends AbstractArrayResultFormatter {

    protected $groupByPropertyNames = NULL;
    protected $enumerationPropertyName = NULL;
    protected $subjectPropertyNames = NULL;

    protected $adjustedGroupByPropertyNames = NULL;
    protected $adjustedEnumerationPropertyName = NULL;
    protected $adjustedSubjectPropertyNames = NULL;

    public function __construct($groupByPropertyNames, $enumerationPropertyName, $subjectPropertyNames, ResultFormatter $parent = NULL) {
        parent::__construct($parent);

        $this->groupByPropertyNames = is_array($groupByPropertyNames) ? $groupByPropertyNames : array($groupByPropertyNames);
        $this->enumerationPropertyName = $enumerationPropertyName;
        $this->subjectPropertyNames = is_array($subjectPropertyNames) ? $subjectPropertyNames : array($subjectPropertyNames);

        $this->adjustedGroupByPropertyNames = $this->formatPropertyNames($this->groupByPropertyNames, TRUE);
        $this->adjustedEnumerationPropertyName = $this->formatPropertyName($this->enumerationPropertyName);
        $this->adjustedSubjectPropertyNames = $this->formatPropertyNames($this->subjectPropertyNames, TRUE);
    }

    public function __clone() {
        parent::__clone();

        $this->groupByPropertyNames = ArrayHelper::cloneArray($this->groupByPropertyNames);
        $this->subjectPropertyNames = ArrayHelper::cloneArray($this->subjectPropertyNames);
        $this->adjustedGroupByPropertyNames = ArrayHelper::cloneArray($this->adjustedGroupByPropertyNames);
        $this->adjustedSubjectPropertyNames = ArrayHelper::cloneArray($this->adjustedSubjectPropertyNames);
    }

    protected function formatSubjectProperty(array &$existingRecord, array &$record, $subjectPropertyName) {
        $existingRecord[$subjectPropertyName . '_' . $record[$this->adjustedEnumerationPropertyName]] = $record[$subjectPropertyName];
    }

    protected function formatSubjectProperties(array &$existingRecord, array &$record) {
        if (isset($this->adjustedSubjectPropertyNames)) {
            foreach ($this->adjustedSubjectPropertyNames as $subjectPropertyName) {
                $this->formatSubjectProperty($existingRecord, $record, $subjectPropertyName);
            }
        }
        else {
            foreach ($record as $name => $value) {
                if ((array_search($name, $this->adjustedGroupByPropertyNames) !== FALSE) || ($name === $this->adjustedEnumerationPropertyName)) {
                    continue;
                }

                $this->formatSubjectProperty($existingRecord, $record, $name);
            }
        }
    }

    public function formatRecord(array &$records = NULL, $record) {
        $result = parent::formatRecord($records, $record);
        if ($result) {
            $this->errorUnsupportedChainOfResultFormatters();
        }

        if (isset($records)) {
            // trying to find a record which could be reused
            foreach ($records as &$existingRecord) {
                $isRecordMatched = TRUE;
                foreach ($this->adjustedGroupByPropertyNames as $groupByPropertyName) {
                    if (isset($existingRecord[$groupByPropertyName])) {
                        if (isset($record[$groupByPropertyName])) {
                            if ($existingRecord[$groupByPropertyName] !== $record[$groupByPropertyName]) {
                                $isRecordMatched = FALSE;
                            }
                        }
                        else {
                            $isRecordMatched = FALSE;
                        }

                    }
                    elseif (isset($record[$groupByPropertyName])) {
                        $isRecordMatched = FALSE;
                    }

                    if (!$isRecordMatched) {
                        break;
                    }
                }

                if ($isRecordMatched) {
                    $this->formatSubjectProperties($existingRecord, $record);
                    return TRUE;
                }
            }
            unset($existingRecord);
        }

        // preparing new record
        $newRecord = NULL;
        foreach ($record as $name => $value) {
            if ($name === $this->adjustedEnumerationPropertyName) {
                continue;
            }

            if (isset($this->adjustedSubjectPropertyNames)) {
                if (array_search($name, $this->adjustedSubjectPropertyNames) !== FALSE) {
                    continue;
                }
            }
            elseif (array_search($name, $this->adjustedGroupByPropertyNames) === FALSE) {
                continue;
            }

            // We still add some columns which are not really useful.
            // Example:
            //    * data is prepared by cube. Returned properties: 'agency', 'agency.name', ...
            //    * 'agency.name' is subject property
            //    * new record will contain 'agency' property. There is no good generic logic to eliminate such property
            $newRecord[$name] = $value;
        }
        $this->formatSubjectProperties($newRecord, $record);
        $records[] = $newRecord;

        return TRUE;
    }

    public function adjustCubeCountRequest(DataControllerCallContext $callcontext, CubeQueryRequest $request) {
        list($dimensionName, $levelName, $propertyName) = ParameterHelper::splitName($this->enumerationPropertyName);

        // we have to find corresponding level in this request
        $isLevelFound = FALSE;
        if (isset($request->dimensions)) {
            foreach ($request->dimensions as $key => $dimension) {
                if (($dimension->dimensionName == $dimensionName) && ($dimension->levelName == $levelName)) {
                    $isLevelFound = TRUE;
                    unset($request->dimensions[$key]);

                    break;
                }
            }
        }
        if (!$isLevelFound) {
            throw new IllegalStateException(t(
            	"Could not find configuration for '@levelName' level of '@dimensionName' dimension in the request",
                array('@dimensionName' => $dimensionName, '@levelName' => $levelName)));
        }
    }

    protected function isClientSortingRequiredImpl() {
        return TRUE;
    }

    protected function isClientPaginationRequiredImpl() {
        return TRUE;
    }
}
