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


abstract class AbstractLookupIdentifierLoader extends AbstractObject {

    abstract public function selectLookupableColumns(RecordMetaData $recordMetaData);

    protected function prepareLookupValues(IndexedRecordsHolder $recordsHolder, array $lookupableColumns) {
        $columnsLookupValues = NULL;
        if (isset($lookupableColumns)) {
            foreach ($recordsHolder->records as $record) {
                foreach ($lookupableColumns as $columnIndex => $column) {
                    $columnValue = $record->columnValues[$columnIndex];
                    if (!isset($columnValue)) {
                        continue;
                    }

                    $dimensionLookupHandler = DimensionLookupFactory::getInstance()->getHandler($column->type->applicationType);

                    // storing unique set of values
                    $lookupKey = AbstractDimensionLookupHandler::prepareLookupKey($columnValue);
                    $columnsLookupValues[$columnIndex][$lookupKey] = $dimensionLookupHandler->prepareLookupValue($columnValue);
                }
            }
        }

        return $columnsLookupValues;
    }

    protected function checkMissingIdentifiers(ColumnMetaData $column, array $columnLookupValues) {
        $KEY__COMPOSITE_NAME = '<composite>';

        // calculating number of missing identifiers based on number of properties in lookup key
        $propertyUsageStatistics = NULL;
        foreach ($columnLookupValues as $lookupValue) {
            if (isset($lookupValue->identifier)) {
                continue;
            }

            $propertyNames = NULL;
            foreach ($lookupValue as $name => $value) {
                if (isset($value)) {
                    $propertyNames[] = $name;
                }
            }

            $propertyCount = count($propertyNames);
            if ($propertyCount > 0) {
                $key = ($propertyCount > 1) ? $KEY__COMPOSITE_NAME : $propertyNames[0];
                if (!isset($propertyUsageStatistics[$propertyCount][$key])) {
                    $propertyUsageStatistics[$propertyCount][$key] = 0;
                }
                $propertyUsageStatistics[$propertyCount][$key]++;
            }
        }

        // we do have uncompleted data
        if (isset($propertyUsageStatistics)) {
            $showSingleColumnName = FALSE;

            $useInnerArray = FALSE;
            $variationsOfPropertyCount = count($propertyUsageStatistics);
            if ($variationsOfPropertyCount === 1) {
                $propertyNames = array_keys($propertyUsageStatistics[key($propertyUsageStatistics)]);
                $variationsOfPropertyName = count($propertyNames);
                if ($variationsOfPropertyName == 1) {
                    $propertyName = $propertyNames[0];
                    if ($propertyName == $KEY__COMPOSITE_NAME) {
                        $useInnerArray = TRUE;
                    }
                    else {
                        $showSingleColumnName = TRUE;
                    }
                }
                else {
                    $useInnerArray = TRUE;
                }
            }
            else {
                // we have different number of key properties
                $useInnerArray = TRUE;
            }

            $message = '';
            foreach ($columnLookupValues as $lookupValue) {
                if (isset($lookupValue->identifier)) {
                    continue;
                }

                $s = '';
                foreach ($lookupValue as $name => $value) {
                    if (isset($value)) {
                        if (strlen($s) > 0) {
                            $s .= '; ';
                        }
                        if ($useInnerArray) {
                            $s .= $name . '=';
                        }
                        $s .= $value;
                    }
                }
                if ($useInnerArray) {
                    $s = '[' . $s . ']';
                }

                if (strlen($message) > 0) {
                    $message .= ', ';
                }
                $message .= $s;
            }
            $message = ($showSingleColumnName ? ($column->publicName . ' = ') : '') . '[' . $message . ']';

            throw new IllegalArgumentException(t('Could not find identifiers for the following values: @values', array('@values' => $message)));
        }
    }

    public function load($datasetName, RecordMetaData $recordMetaData, IndexedRecordsHolder $recordsHolder) {
        // preparing columns for which we can lookup values
        $lookupableColumns = $this->selectLookupableColumns($recordMetaData);
        if (!isset($lookupableColumns)) {
            return;
        }

        // preparing required values for each lookup
        $columnsLookupValues = $this->prepareLookupValues($recordsHolder, $lookupableColumns);
        if (!isset($columnsLookupValues)) {
            return;
        }

        // loading identifier for each values
        foreach ($columnsLookupValues as $columnIndex => &$columnLookupValues) {
            $column = $lookupableColumns[$columnIndex];

            $dimensionLookupHandler = DimensionLookupFactory::getInstance()->getHandler($column->type->applicationType);
            $dimensionLookupHandler->prepareDatasetColumnLookupIds($datasetName, $column, $columnLookupValues);

            // checking if we loaded all values
            $this->checkMissingIdentifiers($column, $columnLookupValues);
        }
        unset($columnLookupValues);

        // replacing column values with corresponding ids
        foreach ($recordsHolder->records as $record) {
            foreach ($lookupableColumns as $columnIndex => $column) {
                $columnValue = $record->columnValues[$columnIndex];
                if (!isset($columnValue)) {
                    continue;
                }

                $lookupKey = AbstractDimensionLookupHandler::prepareLookupKey($columnValue);
                $record->columnValues[$columnIndex] = $columnsLookupValues[$columnIndex][$lookupKey]->identifier;
            }
        }
    }
}
