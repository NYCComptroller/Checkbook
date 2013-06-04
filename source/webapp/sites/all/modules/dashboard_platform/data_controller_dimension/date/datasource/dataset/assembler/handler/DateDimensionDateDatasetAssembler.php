<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DateDimensionDateDatasetAssembler extends AbstractDateDimensionDatasetAssembler {

    public static $DATASET_SOURCE_ASSEMBLER__TYPE = 'dimension/date/date';

    public static $TABLE_ALIAS_SUFFIX__DATES = 'd';
    public static $TABLE_ALIAS_SUFFIX__DAY_OF_WEEK = 'dw';

    public static function prepareTableSection(AbstractSQLDataSourceQueryHandler $datasourceHandler, DataControllerCallContext $callcontext, array &$requestedColumnNames = NULL) {
        $metamodel = data_controller_get_metamodel();

        $tableDates = new DatasetSection(
            $metamodel->getDataset(DateDimensionDatasetNames::DATES),
            self::$TABLE_ALIAS_SUFFIX__DATES);
        self::selectColumn($tableDates, $requestedColumnNames, FALSE, 'entry_date', FALSE);
        self::selectColumn($tableDates, $requestedColumnNames, FALSE, 'day_of_week_def_id', FALSE);
        self::selectColumn($tableDates, $requestedColumnNames, FALSE, 'month_id', FALSE);

        $columnDateId = self::selectColumn($tableDates, $requestedColumnNames, isset($tableDates->columns), 'date_id', FALSE);
        if (isset($columnDateId)) {
            $columnDateId->key = TRUE;
        }

        return $tableDates;
    }

    public static function prepareDefTableSection(AbstractSQLDataSourceQueryHandler $datasourceHandler, DataControllerCallContext $callcontext, array &$requestedColumnNames = NULL) {
        $metamodel = data_controller_get_metamodel();

        $tableDayOfWeek = new DatasetSection(
            $metamodel->getDataset(DateDimensionDatasetNames::DAY_OF_WEEK_DEF),
            self::$TABLE_ALIAS_SUFFIX__DAY_OF_WEEK);
        self::selectColumn($tableDayOfWeek, $requestedColumnNames, FALSE, 'code', FALSE, 'day_of_week_code');
        self::selectColumn($tableDayOfWeek, $requestedColumnNames, FALSE, 'name', FALSE, 'day_of_week_name');

        $columnDateOfWeekDefId = self::selectColumn($tableDayOfWeek, $requestedColumnNames, isset($tableDayOfWeek->columns), 'day_of_week_def_id', FALSE);
        if (isset($columnDateOfWeekDefId)) {
            $columnDateOfWeekDefId->key = TRUE;
        }

        return $tableDayOfWeek;
    }

    public function assemble(AbstractSQLDataSourceQueryHandler $datasourceHandler, DataControllerCallContext $callcontext, DatasetMetaData $dataset, array $columnNames = NULL) {
        $statement = new Statement();

        $requestedColumnNames = self::prepareRequestedColumnNames($columnNames);

        $tableDates = self::prepareTableSection($datasourceHandler, $callcontext, $requestedColumnNames);
        $statement->tables[] = $tableDates;

        $tableDayOfWeek = self::prepareDefTableSection($datasourceHandler, $callcontext, $requestedColumnNames);
        $isDayOfWeekRequired = isset($tableDayOfWeek->columns);

        $monthTableSections = DateDimensionMonthDatasetAssembler::prepareTableSections4Statement($datasourceHandler, $callcontext, $requestedColumnNames, FALSE);
        $isMonthRequired = isset($monthTableSections);

        if ($isDayOfWeekRequired) {
            $tableDayOfWeek->conditions[] = new JoinConditionSection(
            	'day_of_week_def_id', new TableColumnConditionSectionValue(self::$TABLE_ALIAS_SUFFIX__DATES, 'day_of_week_def_id'));
            $statement->tables[] = $tableDayOfWeek;
        }

        if ($isMonthRequired) {
            $tableMonths = $monthTableSections[0];
            $tableMonths->conditions[] = new JoinConditionSection('month_id', new TableColumnConditionSectionValue(self::$TABLE_ALIAS_SUFFIX__DATES, 'month_id'));

            ArrayHelper::mergeArrays($statement->tables, $monthTableSections);
        }

        return $statement;
    }
}
