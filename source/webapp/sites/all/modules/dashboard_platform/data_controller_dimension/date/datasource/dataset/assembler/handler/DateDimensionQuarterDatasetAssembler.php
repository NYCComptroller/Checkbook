<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DateDimensionQuarterDatasetAssembler extends AbstractDateDimensionDatasetAssembler {

    public static $DATASET_SOURCE_ASSEMBLER__TYPE = 'dimension/date/quarter';

    public static $TABLE_ALIAS_SUFFIX__QUARTERS = 'q';
    public static $TABLE_ALIAS_SUFFIX__QUARTER_DEF = 'qd';

    public static function prepareTableSection(AbstractSQLDataSourceQueryHandler $datasourceHandler, DataControllerCallContext $callcontext, array &$requestedColumnNames = NULL) {
        $metamodel = data_controller_get_metamodel();

        $tableQuarters = new DatasetSection(
            $metamodel->getDataset(DateDimensionDatasetNames::QUARTERS),
            self::$TABLE_ALIAS_SUFFIX__QUARTERS);
        self::selectColumn($tableQuarters, $requestedColumnNames, FALSE, 'quarter_def_id', FALSE);
        self::selectColumn($tableQuarters, $requestedColumnNames, FALSE, 'year_id', FALSE);

        // calculated field: first day of quarter
        $expressionFirstDayOfQuarter = $datasourceHandler->concatenateValues(array(
            '(' . ColumnStatementCompositeEntityParser::assembleColumnName(self::$TABLE_ALIAS_SUFFIX__QUARTER_DEF . '.series') . ' - 1) * 3 + 1',
            "'/1/'", // Note: this function requires all values has to be formatted
            ColumnStatementCompositeEntityParser::assembleColumnName(DateDimensionYearDatasetAssembler::$TABLE_ALIAS_SUFFIX__YEARS . '.entry_year')));
        $expressionFirstDayOfQuarter = $datasourceHandler->formatDateValue($expressionFirstDayOfQuarter, 'm/d/Y');
        $columnFirstDayOfQuarter = self::selectColumn($tableQuarters, $requestedColumnNames, FALSE, $expressionFirstDayOfQuarter, TRUE, 'quarter_first_date');
        if (isset($columnFirstDayOfQuarter)) {
            // registering required column for this functionality to work. This will indirectly force joining with required tables
            self::registerDependentColumnName($requestedColumnNames, 'quarter_series');
            self::registerDependentColumnName($requestedColumnNames, 'entry_year');
        }

        $columnQuarterId = self::selectColumn($tableQuarters, $requestedColumnNames, isset($tableQuarters->columns), 'quarter_id', FALSE);
        if (isset($columnQuarterId)) {
            $columnQuarterId->key = TRUE;
        }

        return $tableQuarters;
    }

    public static function prepareDefTableSection(AbstractSQLDataSourceQueryHandler $datasourceHandler, DataControllerCallContext $callcontext, array &$requestedColumnNames = NULL) {
        $metamodel = data_controller_get_metamodel();

        $tableQuarterDef = new DatasetSection(
            $metamodel->getDataset(DateDimensionDatasetNames::QUARTER_DEF),
            self::$TABLE_ALIAS_SUFFIX__QUARTER_DEF);
        self::selectColumn($tableQuarterDef, $requestedColumnNames, FALSE, 'series', FALSE, 'quarter_series');
        self::selectColumn($tableQuarterDef, $requestedColumnNames, FALSE, 'code', FALSE, 'quarter_code');
        self::selectColumn($tableQuarterDef, $requestedColumnNames, FALSE, 'name', FALSE, 'quarter_name');

        $columnQuarterDefId = self::selectColumn($tableQuarterDef, $requestedColumnNames, isset($tableQuarterDef->columns), 'quarter_def_id', FALSE);
        if (isset($columnQuarterDefId)) {
            $columnQuarterDefId->key = TRUE;
        }

        return $tableQuarterDef;
    }

    public static function prepareTableSections4Statement(AbstractSQLDataSourceQueryHandler $datasourceHandler, DataControllerCallContext $callcontext, array &$requestedColumnNames = NULL, $alwaysJoin = FALSE) {
        $tableSections = NULL;

        $tableQuarters = self::prepareTableSection($datasourceHandler, $callcontext, $requestedColumnNames);
        $isQuarterRequired = isset($tableQuarters->columns);

        $tableDef = self::prepareDefTableSection($datasourceHandler, $callcontext, $requestedColumnNames);
        $isQuarterDefRequired = isset($tableDef->columns);

        $yearTableSections = DateDimensionYearDatasetAssembler::prepareTableSections4Statement($datasourceHandler, $callcontext, $requestedColumnNames, FALSE);
        $isYearRequired = isset($yearTableSections);

        if ($alwaysJoin || $isQuarterRequired || $isQuarterDefRequired || $isYearRequired) {
            $tableSections[] = $tableQuarters;

            if ($isQuarterDefRequired) {
                $tableDef->conditions[] = new JoinConditionSection(
                	'quarter_def_id', new TableColumnConditionSectionValue(self::$TABLE_ALIAS_SUFFIX__QUARTERS, 'quarter_def_id'));
                $tableSections[] = $tableDef;
            }

            if ($isYearRequired) {
                $tableYears = $yearTableSections[0];
                $tableYears->conditions[] = new JoinConditionSection('year_id', new TableColumnConditionSectionValue(self::$TABLE_ALIAS_SUFFIX__QUARTERS, 'year_id'));

                ArrayHelper::mergeArrays($tableSections, $yearTableSections);
            }
        }

        return $tableSections;
    }

    public function assemble(AbstractSQLDataSourceQueryHandler $datasourceHandler, DataControllerCallContext $callcontext, DatasetMetaData $dataset, array $columnNames = NULL) {
        $statement = new Statement();

        $requestedColumnNames = self::prepareRequestedColumnNames($columnNames);

        ArrayHelper::mergeArrays($statement->tables, self::prepareTableSections4Statement($datasourceHandler, $callcontext, $requestedColumnNames, TRUE));

        return $statement;
    }
}
