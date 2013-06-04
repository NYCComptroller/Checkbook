<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
