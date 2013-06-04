<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DateDimensionLookupHandler extends AbstractDateDimensionLookupHandler {

    protected static $COLUMN_NAME__ATTRIBUTE_LOOKUP_VALUE = 'entry_date';

    public function prepareLookupValue($value) {
        $lookupValue = new DimensionLookupHandler__LookupValue();
        $lookupValue->setProperty(self::$COLUMN_NAME__ATTRIBUTE_LOOKUP_VALUE, $value);

        return $lookupValue;
    }

    // *****************************************************************************************************************************
    //
    // Supporting functions to implement prepareDatasetColumnLookupIds()
    //
    // *****************************************************************************************************************************
    private function registerYear4Loading(&$possibleMissingYears, $year) {
        // checking if we already registered this year
        if (isset($possibleMissingYears)) {
            foreach ($possibleMissingYears as $value) {
                if ($value->getProperty('entry_year') == $year) {
                    return;
                }
            }
        }

        $yearLookupValue = new DimensionLookupHandler__LookupValue();
        $yearLookupValue->setProperty('entry_year', $year);

        $yearLookupKey = self::prepareLookupKey($year);
        $possibleMissingYears[$yearLookupKey] = $yearLookupValue;
    }

    protected function prepareYearIds(array &$missingDates) {
        $entryYearColumn = new ColumnMetaData();
        $entryYearColumn->name = 'entry_year';
        $entryYearColumn->type->applicationType = IntegerDataTypeHandler::$DATA_TYPE;

        $possibleMissingYears = NULL;
        foreach ($missingDates as $datetime) {
            $year = $datetime->getYear();
            $this->registerYear4Loading($possibleMissingYears, $year);

            // to support fiscal year we need to add also a record for next year
            // Example: end of 2010 calendar year could be begining of 2011 fiscal year
            $fiscalYear = $year + 1;
            $this->registerYear4Loading($possibleMissingYears, $fiscalYear);
        }

        // processing years
        $this->prepareIdentifiers(DateDimensionDatasetNames::YEARS, array($entryYearColumn), NULL, self::$SEQUENCE_NAME__TIME, $possibleMissingYears);

        return $possibleMissingYears;
    }

    protected function guaranteeQuarterDefinitionInCache() {
        $lookupCacheKey = DateDimensionDatasetNames::QUARTER_DEF;

        if (!isset($this->cachedLookupValues[$lookupCacheKey])) {
            $dataQueryController = data_controller_get_instance();

            $definitionProperties = $dataQueryController->queryDataset(
                DateDimensionDatasetNames::QUARTER_DEF,
                array('quarter_def_id', 'series'));

            foreach ($definitionProperties as $properties) {
                $key = $properties['series'];
                $value = $properties['quarter_def_id'];

                $this->cachedLookupValues[$lookupCacheKey][$key] = $value;
            }
        }
    }

    private function registerQuarter4Loading(&$possibleMissingQuarters, $yearId, $quarterDefId) {
        // checking if we already registered this year
        if (isset($possibleMissingQuarters)) {
            foreach ($possibleMissingQuarters as $value) {
                if (($value->getProperty('year_id') == $yearId)
                        && ($value->getProperty('quarter_def_id') == $quarterDefId)) {
                    return;
                }
            }
        }

        $quarterLookupValue = new DimensionLookupHandler__LookupValue();
        $quarterLookupValue->setProperty('year_id', $yearId);
        $quarterLookupValue->setProperty('quarter_def_id', $quarterDefId);

        $quarterLookupKey = self::prepareLookupKey(array($quarterDefId, $yearId));
        $possibleMissingQuarters[$quarterLookupKey] = $quarterLookupValue;
    }

    protected function prepareQuarterIds(array &$processedYearValues, array &$missingDates) {
        $this->guaranteeQuarterDefinitionInCache();

        $yearColumn = new ColumnMetaData();
        $yearColumn->name = 'year_id';
        $yearColumn->initializeTypeFrom(Sequence::getSequenceColumnType());

        $quarterDefinitionColumn = new ColumnMetaData();
        $quarterDefinitionColumn->name = 'quarter_def_id';
        $quarterDefinitionColumn->initializeTypeFrom(Sequence::getSequenceColumnType());

        // preparing quarter records
        $possibleMissingQuarters = NULL;
        foreach ($missingDates as $datetime) {
            $year = $datetime->getYear();

            $yearLookupKey = self::prepareLookupKey($year);
            $yearId = $processedYearValues[$yearLookupKey]->identifier;

            $quarter = $datetime->getQuarter();

            $quarterDefId = $this->cachedLookupValues[DateDimensionDatasetNames::QUARTER_DEF][$quarter];
            $this->registerQuarter4Loading($possibleMissingQuarters, $yearId, $quarterDefId);

            // to support all possible combinations of fiscal quarter we need to support next 3 quarters
            $fiscalYear = $year;
            $fiscalYearId = $yearId;
            $fiscalQuarter = $quarter;
            for ($i = 0; $i < 3; $i++) {
                $fiscalQuarter++;
                if ($fiscalQuarter > 4) {
                    $fiscalQuarter = 1;
                    // moving to next year
                    $fiscalYear++;
                    $fiscalYearLookupKey = self::prepareLookupKey($fiscalYear);
                    $fiscalYearId = $processedYearValues[$fiscalYearLookupKey]->identifier;
                }

                $fiscalQuarterDefId = $this->cachedLookupValues[DateDimensionDatasetNames::QUARTER_DEF][$fiscalQuarter];
                $this->registerQuarter4Loading($possibleMissingQuarters, $fiscalYearId, $fiscalQuarterDefId);
            }
        }

        // processing quarters
        $this->prepareIdentifiers(DateDimensionDatasetNames::QUARTERS, array($quarterDefinitionColumn, $yearColumn), NULL, self::$SEQUENCE_NAME__TIME, $possibleMissingQuarters);

        // preparing processed quarters
        $processedQuarterValues = NULL;
        foreach ($missingDates as $datetime) {
            $year = $datetime->getYear();
            $yearLookupKey = self::prepareLookupKey($year);
            $yearId = $processedYearValues[$yearLookupKey]->identifier;

            $quarter = $datetime->getQuarter();
            $quarterDefId = $this->cachedLookupValues[DateDimensionDatasetNames::QUARTER_DEF][$quarter];

            $quarterLookupKey = self::prepareLookupKey(array($quarterDefId, $yearId));

            $processedQuarterLookupKey = self::prepareLookupKey(array($quarter, $year));
            $processedQuarterValues[$processedQuarterLookupKey] = $possibleMissingQuarters[$quarterLookupKey];
        }

        return $processedQuarterValues;
    }

    protected function guaranteeMonthDefinitionInCache() {
        $lookupCacheKey = DateDimensionDatasetNames::MONTH_DEF;

        if (!isset($this->cachedLookupValues[$lookupCacheKey])) {
            $dataQueryController = data_controller_get_instance();

            $definitionProperties = $dataQueryController->queryDataset(
                DateDimensionDatasetNames::MONTH_DEF,
                array('month_def_id', 'series'));

            foreach ($definitionProperties as $properties) {
                $key = $properties['series'];
                $value = $properties['month_def_id'];

                $this->cachedLookupValues[$lookupCacheKey][$key] = $value;
            }
        }
    }

    protected function prepareMonthIds(array &$processedYearValues, array &$missingDates) {
        $this->guaranteeMonthDefinitionInCache();

        $yearColumn = new ColumnMetaData();
        $yearColumn->name = 'year_id';
        $yearColumn->initializeTypeFrom(Sequence::getSequenceColumnType());

        $monthDefinitionColumn = new ColumnMetaData();
        $monthDefinitionColumn->name = 'month_def_id';
        $monthDefinitionColumn->initializeTypeFrom(Sequence::getSequenceColumnType());

        // preparing month records
        $possibleMissingMonths = NULL;
        foreach ($missingDates as $datetime) {
            $year = $datetime->getYear();

            $yearLookupKey = self::prepareLookupKey($year);
            $yearId = $processedYearValues[$yearLookupKey]->identifier;

            $month = $datetime->getMonth();

            $monthDefId = $this->cachedLookupValues[DateDimensionDatasetNames::MONTH_DEF][$month];

            $monthLookupValue = new DimensionLookupHandler__LookupValue();
            $monthLookupValue->setProperty($monthDefinitionColumn->name, $monthDefId);
            $monthLookupValue->setProperty($yearColumn->name, $yearId);

            $monthLookupKey = self::prepareLookupKey(array($monthDefId, $yearId));
            $possibleMissingMonths[$monthLookupKey] = $monthLookupValue;
        }

        // processing months
        $this->prepareIdentifiers(DateDimensionDatasetNames::MONTHS, array($monthDefinitionColumn, $yearColumn), NULL, self::$SEQUENCE_NAME__TIME, $possibleMissingMonths);

        // preparing processed quarters
        $processedMonthValues = NULL;
        foreach ($missingDates as $datetime) {
            $year = $datetime->getYear();
            $yearLookupKey = self::prepareLookupKey($year);
            $yearId = $processedYearValues[$yearLookupKey]->identifier;

            $month = $datetime->getMonth();
            $monthDefId = $this->cachedLookupValues[DateDimensionDatasetNames::MONTH_DEF][$month];

            $monthLookupKey = self::prepareLookupKey(array($monthDefId, $yearId));

            $processedMonthLookupKey = self::prepareLookupKey(array($month, $year));
            $processedMonthValues[$processedMonthLookupKey] = $possibleMissingMonths[$monthLookupKey];
        }

        return $processedMonthValues;
    }

    protected function guaranteeDayOfWeekDefinitionInCache() {
        $lookupCacheKey = DateDimensionDatasetNames::DAY_OF_WEEK_DEF;

        if (!isset($this->cachedLookupValues[$lookupCacheKey])) {
            $dataQueryController = data_controller_get_instance();

            $definitionProperties = $dataQueryController->queryDataset(
                DateDimensionDatasetNames::DAY_OF_WEEK_DEF,
                array('day_of_week_def_id', 'code'));

            foreach ($definitionProperties as $properties) {
                $key = $properties['code'];
                $value = $properties['day_of_week_def_id'];

                $this->cachedLookupValues[$lookupCacheKey][$key] = $value;
            }
        }
    }

    protected function prepareDateIds(ColumnMetaData $column, array &$dateLookupValues, array &$processedMonthValues, array &$missingDates) {
        $this->guaranteeDayOfWeekDefinitionInCache();

        $entryDateColumn = new ColumnMetaData();
        $entryDateColumn->name = self::$COLUMN_NAME__ATTRIBUTE_LOOKUP_VALUE;
        $entryDateColumn->initializeTypeFrom($column->type);

        $monthColumn = new ColumnMetaData();
        $monthColumn->name = 'month_id';
        $monthColumn->initializeTypeFrom(Sequence::getSequenceColumnType());

        $dayOfWeekDefinitionColumn = new ColumnMetaData();
        $dayOfWeekDefinitionColumn->name = 'day_of_week_def_id';
        $dayOfWeekDefinitionColumn->initializeTypeFrom(Sequence::getSequenceColumnType());

        // adding missing properties in case we need to insert new records
        $missingLookupValues = NULL;
        foreach ($missingDates as $lookupKey => $datetime) {
            $lookupValue = $dateLookupValues[$lookupKey];
            if (isset($lookupValue->identifier)) {
                continue;
            }

            $year = $datetime->getYear();

            $month = $datetime->getMonth();
            $monthLookupKey = self::prepareLookupKey(array($month, $year));
            $monthId = $processedMonthValues[$monthLookupKey]->identifier;
            $lookupValue->setProperty($monthColumn->name, $monthId);

            $dayOfWeekCode = $datetime->getDayOfWeek();
            $dayOfWeekDefId = $this->cachedLookupValues[DateDimensionDatasetNames::DAY_OF_WEEK_DEF][$dayOfWeekCode];
            $lookupValue->setProperty($dayOfWeekDefinitionColumn->name, $dayOfWeekDefId);

            $missingLookupValues[$lookupKey] = $lookupValue;
        }

        // storing dates
        if (isset($missingLookupValues)) {
            $this->storeLookupValues(
                DateDimensionDatasetNames::DATES,
                array($entryDateColumn), array($monthColumn, $dayOfWeekDefinitionColumn),
                self::$SEQUENCE_NAME__TIME,
                $missingLookupValues);

            if (self::$SHARED_LOOKUP) {
                $this->loadIdentifiers(DateDimensionDatasetNames::DATES, array($entryDateColumn), $missingLookupValues, TRUE);
            }
        }
    }

    public function prepareDatasetColumnLookupIds($datasetName, ColumnMetaData $column, array &$lookupValues) {
        $entryDateColumn = new ColumnMetaData();
        $entryDateColumn->name = self::$COLUMN_NAME__ATTRIBUTE_LOOKUP_VALUE;
        $entryDateColumn->initializeTypeFrom($column->type);

        $this->loadIdentifiers(DateDimensionDatasetNames::DATES, array($entryDateColumn), $lookupValues);

        // even if we find a record for a particular date we still need to check if there are corresponding records for related fiscal year and quarters
        $dates = NULL;
        foreach ($lookupValues as $lookupKey => $lookupPproperties) {
            $entryDate = $lookupPproperties->{self::$COLUMN_NAME__ATTRIBUTE_LOOKUP_VALUE};

            $datetime = new DateTimeProxy(new DateTime($entryDate));
            $dates[$lookupKey] = $datetime;
        }

        $processedYearValues = $this->prepareYearIds($dates);
        $this->prepareQuarterIds($processedYearValues, $dates);
        $processedMonthValues = $this->prepareMonthIds($processedYearValues, $dates);
        $this->prepareDateIds($column, $lookupValues, $processedMonthValues, $dates);
    }


    // *****************************************************************************************************************************
    //
    // Supporting functions to implement prepareDimension()
    //
    // *****************************************************************************************************************************
    protected function prepareDateLevel(DimensionMetaData $dimension, ColumnMetaData $column) {
        $level = $dimension->registerLevel(DateDimensionLevelNames::DATES);
        $level->publicName = t('Date');
        $level->sourceColumnName = $column->name;
        $level->datasetName = DateDimensionDatasetNames::LEVEL_DATES;

        return $level;
    }

    protected function prepareMonthLevel(DimensionMetaData $dimension, ColumnMetaData $column) {
        $level = $dimension->registerLevel(DateDimensionLevelNames::MONTHS);
        $level->publicName = t('Month');
        $level->datasetName = DateDimensionDatasetNames::LEVEL_MONTHS;

        return $level;
    }

    protected function prepareQuarterLevel(DimensionMetaData $dimension, ColumnMetaData $column) {
        $level = $dimension->registerLevel(DateDimensionLevelNames::QUARTERS);
        $level->publicName = t('Quarter');
        $level->datasetName = DateDimensionDatasetNames::LEVEL_QUARTERS;

        return $level;
    }

    protected function prepareYearLevel(DimensionMetaData $dimension, ColumnMetaData $column) {
        $level = $dimension->registerLevel(DateDimensionLevelNames::YEARS);
        $level->publicName = t('Year');
        $level->datasetName = DateDimensionDatasetNames::LEVEL_YEARS;

        return $level;
    }

    public function prepareDimension(MetaModel $metamodel, DatasetMetaData $dataset, $columnName, CubeMetaData $cube) {
        $column = $dataset->getColumn($columnName);
        $sourceDatasetColumn = $cube->sourceDataset->getColumn($columnName);
        $dimension = $cube->getDimension($columnName);

        // preparing levels
        $this->prepareDateLevel($dimension, $column);
        $this->prepareMonthLevel($dimension, $column);
        $this->prepareQuarterLevel($dimension, $column);
        $this->prepareYearLevel($dimension, $column);

        // cube source dataset column contains a reference to date identifier
        $sourceDatasetColumn->initializeTypeFrom(Sequence::getSequenceColumnType());

        // adding a reference to date dataset
        $referenceName = DateDimensionDatasetNames::LEVEL_DATES;
        $metamodel->registerSimpleReferencePoint($referenceName, DateDimensionDatasetNames::LEVEL_DATES, 'date_id');
        $metamodel->registerSimpleReferencePoint($referenceName, $cube->sourceDatasetName, $columnName);
    }


    // *****************************************************************************************************************************
    //
    // Supporting functions to implement adjustReferencePointColumn()
    //
    // *****************************************************************************************************************************
    public function adjustReferencePointColumn(AbstractMetaModel $metamodel, $datasetName, $columnName) {
        $shared = TRUE;

        return array(DateDimensionDatasetNames::LEVEL_DATES, self::$COLUMN_NAME__ATTRIBUTE_LOOKUP_VALUE, $shared);
    }
}
