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


class ColumnCategoryPreparer extends AbstractDataSubmitter {

    private $executionTimeEventName = NULL;

    public function __construct($executionTimeEventName) {
        parent::__construct();
        $this->executionTimeEventName = $executionTimeEventName;
    }

    public function beforeProcessingRecords(RecordMetaData $recordMetaData, AbstractDataProvider $dataProvider) {
        $result = parent::beforeProcessingRecords($recordMetaData, $dataProvider);

        if ($result && ($this->executionTimeEventName === self::$EVENT_NAME__BEFORE_PROCESSING)) {
            ColumnCategoryHelper::setupColumnsCategory($recordMetaData);
        }

        return $result;
    }

    public function afterProcessingRecords(RecordMetaData $recordMetaData, $fileProcessedCompletely) {
        parent::afterProcessingRecords($recordMetaData, $fileProcessedCompletely);

        if ($this->executionTimeEventName === self::$EVENT_NAME__AFTER_PROCESSING) {
            ColumnCategoryHelper::setupColumnsCategory($recordMetaData);
        }
    }
}

class ColumnCategoryHelper {

    public static function setupColumnsCategory(RecordMetaData $recordMetaData) {
        foreach ($recordMetaData->columns as $column) {
            // We will not be able to detect column type anyway
            if (!isset($column->type->applicationType)) {
                continue;
            }

            if (isset($column->columnCategory)) {
                if (($column->columnCategory === DatasetColumnCategories::FACT)
                        && (self::detectColumnCategory($column) != DatasetColumnCategories::FACT)) {
                    throw new IllegalArgumentException(t(
                    	"Column of type '@dataType' cannot be used as a fact: @columnName",
                        array('@columnName' => $column->publicName, '@dataType' => $column->type->applicationType)));
                }
            }
            else {
                $column->columnCategory = self::detectColumnCategory($column);
            }
        }
    }

    public static function detectColumnCategory(ColumnMetaData $column) {
        $columnCategory = NULL;
        switch ($column->type->applicationType) {
            case NumberDataTypeHandler::$DATA_TYPE:
            case CurrencyDataTypeHandler::$DATA_TYPE:
            case PercentDataTypeHandler::$DATA_TYPE:
                $columnCategory = DatasetColumnCategories::FACT;
                break;
            case IntegerDataTypeHandler::$DATA_TYPE:
                $columnCategory = ($column->isKey() || $column->isUnique())
                    ? DatasetColumnCategories::ATTRIBUTE
                    : DatasetColumnCategories::FACT;
                break;
            default:
                $columnCategory = DatasetColumnCategories::ATTRIBUTE;
        }

        return $columnCategory;
    }
}
