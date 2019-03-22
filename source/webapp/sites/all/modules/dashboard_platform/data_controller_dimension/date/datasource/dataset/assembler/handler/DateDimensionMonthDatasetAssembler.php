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


class DateDimensionMonthDatasetAssembler extends AbstractDateDimensionDatasetAssembler {

    public static $DATASET_SOURCE_ASSEMBLER__TYPE = 'dimension/date/month';

    public static $INDENT_FISCAL_SUBQUERY = 24;

    public static $TABLE_ALIAS_SUFFIX__MONTHS = 'm';
    public static $TABLE_ALIAS_SUFFIX__MONTH_DEF = 'md';

    public static function prepareTableSection(AbstractSQLDataSourceQueryHandler $datasourceHandler, DataControllerCallContext $callcontext, array &$requestedColumnNames = NULL) {
        $metamodel = data_controller_get_metamodel();

        $tableMonths = new DatasetSection(
            $metamodel->getDataset(DateDimensionDatasetNames::MONTHS),
            self::$TABLE_ALIAS_SUFFIX__MONTHS);
        self::selectColumn($tableMonths, $requestedColumnNames, FALSE, 'month_def_id', FALSE);
        self::selectColumn($tableMonths, $requestedColumnNames, FALSE, 'year_id', FALSE);

        // calculated field: first day of month
        $expressionFirstDayOfMonth = $datasourceHandler->concatenateValues(array(
            ColumnStatementCompositeEntityParser::assembleColumnName(self::$TABLE_ALIAS_SUFFIX__MONTH_DEF . '.series'),
            "'/1/'", // Note: this function requires all values has to be formatted
            ColumnStatementCompositeEntityParser::assembleColumnName(DateDimensionYearDatasetAssembler::$TABLE_ALIAS_SUFFIX__YEARS . '.entry_year')));
        $expressionFirstDayOfMonth = $datasourceHandler->formatDateValue($expressionFirstDayOfMonth, 'm/d/Y');
        $columnFirstDayOfMonth = self::selectColumn($tableMonths, $requestedColumnNames, FALSE, $expressionFirstDayOfMonth, TRUE, 'month_first_date');
        if (isset($columnFirstDayOfMonth)) {
            // registering required column for this functionality to work. This will indirectly force joining with required tables
            self::registerDependentColumnName($requestedColumnNames, 'month_series');
            self::registerDependentColumnName($requestedColumnNames, 'entry_year');
        }

        $columnMonthId = self::selectColumn($tableMonths, $requestedColumnNames, (isset($tableMonths->columns) || (count($requestedColumnNames) > 0)), 'month_id', FALSE);
        if (isset($columnMonthId)) {
            $columnMonthId->visible = $columnMonthId->visible || (count($requestedColumnNames) > 0);
            $columnMonthId->key = TRUE;
        }

        return $tableMonths;
    }

    public static function prepareDefTableSection(AbstractSQLDataSourceQueryHandler $datasourceHandler, DataControllerCallContext $callcontext, array &$requestedColumnNames = NULL) {
        $metamodel = data_controller_get_metamodel();

        $tableMonthDef = new DatasetSection(
            $metamodel->getDataset(DateDimensionDatasetNames::MONTH_DEF),
            self::$TABLE_ALIAS_SUFFIX__MONTH_DEF);
        self::selectColumn($tableMonthDef, $requestedColumnNames, FALSE, 'series', FALSE, 'month_series');
        self::selectColumn($tableMonthDef, $requestedColumnNames, FALSE, 'code', FALSE, 'month_code');
        self::selectColumn($tableMonthDef, $requestedColumnNames, FALSE, 'name', FALSE, 'month_name');

        $columnMonthDefId = self::selectColumn($tableMonthDef, $requestedColumnNames, isset($tableMonthDef->columns), 'month_def_id', FALSE);
        if (isset($columnMonthDefId)) {
            $columnMonthDefId->key = TRUE;
        }

        return $tableMonthDef;
    }

    protected static function prepareStatement_Jan2Nnn(AbstractSQLDataSourceQueryHandler $datasourceHandler, DataControllerCallContext $callcontext, $toMonthNumber, array &$requestedColumnNames = NULL, $alwaysJoin = FALSE) {
        $statement = new Statement();

        $tableMonth = self::prepareTableSection($datasourceHandler, $callcontext, $requestedColumnNames);
        $isMonthRequired = isset($tableMonth->columns);

        $tableMonthDef = self::prepareDefTableSection($datasourceHandler, $callcontext, $requestedColumnNames);
        $isMonthDefRequired = isset($tableMonthDef->columns);

        $tableQuarters = DateDimensionQuarterDatasetAssembler::prepareTableSection($datasourceHandler, $callcontext, $requestedColumnNames);
        $isQuarterRequired = isset($tableQuarters->columns);

        $tableQuarterDef = DateDimensionQuarterDatasetAssembler::prepareDefTableSection($datasourceHandler, $callcontext, $requestedColumnNames);
        $isQuarterDefRequired = isset($tableQuarterDef->columns);

        $yearTableSections = DateDimensionYearDatasetAssembler::prepareTableSections4Statement($datasourceHandler, $callcontext, $requestedColumnNames, FALSE);
        $isYearRequired = isset($yearTableSections);

        // we cannot link with quarters without querter def
        $isQuarterDefRequired = $isQuarterDefRequired || $isQuarterRequired;
        // linking with quarter def is done through month def
        $isMonthDefRequired = $isMonthDefRequired || $isQuarterDefRequired;

        if ($alwaysJoin || $isMonthRequired || $isMonthDefRequired || $isQuarterRequired || $isQuarterDefRequired || $isYearRequired) {
            $statement->tables[] = $tableMonth;

            $isFiscalYearSupportRequired = ($toMonthNumber != 12) && ($isQuarterRequired || $isQuarterDefRequired || $isYearRequired);
            $isMonthDefRequired = $isMonthDefRequired || $isFiscalYearSupportRequired;

            if ($isMonthDefRequired) {
                $tableMonthDef->conditions[] = new JoinConditionSection(
                	'month_def_id', new TableColumnConditionSectionValue(self::$TABLE_ALIAS_SUFFIX__MONTHS, 'month_def_id'));
                $statement->tables[] = $tableMonthDef;
            }

            if ($isQuarterDefRequired) {
                $tableQuarterDef->conditions[] = new JoinConditionSection(
                	'series',
                    new ExactConditionSectionValue(
                    	' = ((' . ColumnStatementCompositeEntityParser::assembleColumnName(self::$TABLE_ALIAS_SUFFIX__MONTH_DEF . '.series') . ($isFiscalYearSupportRequired ? " + 12 - $toMonthNumber" : '') . ' - 1) DIV 3) + 1'));
                $statement->tables[] = $tableQuarterDef;
            }

            if ($isQuarterRequired) {
                $tableQuarters->conditions[] = new JoinConditionSection(
                    'quarter_def_id', new TableColumnConditionSectionValue(DateDimensionQuarterDatasetAssembler::$TABLE_ALIAS_SUFFIX__QUARTER_DEF, 'quarter_def_id'));
                $tableQuarters->conditions[] = new JoinConditionSection(
                    'year_id', new TableColumnConditionSectionValue(self::$TABLE_ALIAS_SUFFIX__MONTHS, 'year_id'));
                $statement->tables[] = $tableQuarters;
            }

            if ($isYearRequired) {
                $tableYears = $yearTableSections[0];
                $tableYears->conditions[] = new JoinConditionSection('year_id', new TableColumnConditionSectionValue(self::$TABLE_ALIAS_SUFFIX__MONTHS, 'year_id'));

                ArrayHelper::mergeArrays($statement->tables, $yearTableSections);
            }

            if ($isFiscalYearSupportRequired) {
                $statement->conditions[] = new WhereConditionSection(
                    self::$TABLE_ALIAS_SUFFIX__MONTH_DEF, 'series', new ExactConditionSectionValue(" BETWEEN 1 AND $toMonthNumber"));
            }
        }

        return $statement;
    }

    protected static function prepareStatement_Nnn2Dec(AbstractSQLDataSourceQueryHandler $datasourceHandler, DataControllerCallContext $callcontext, $fromMonthNumber, array &$requestedColumnNames = NULL, $alwaysJoin = FALSE) {
        $statement = new Statement();

        $tableMonth = self::prepareTableSection($datasourceHandler, $callcontext, $requestedColumnNames);
        $isMonthRequired = isset($tableMonth->columns);

        $tableMonthDef = self::prepareDefTableSection($datasourceHandler, $callcontext, $requestedColumnNames);
        $isMonthDefRequired = isset($tableMonthDef->columns);

        $tableQuarters = DateDimensionQuarterDatasetAssembler::prepareTableSection($datasourceHandler, $callcontext, $requestedColumnNames);
        $isQuarterRequired = isset($tableQuarters->columns);

        $tableQuarterDef = DateDimensionQuarterDatasetAssembler::prepareDefTableSection($datasourceHandler, $callcontext, $requestedColumnNames);
        $isQuarterDefRequired = isset($tableQuarterDef->columns);

        // fiscal year
        $fiscalYearTableSections = DateDimensionYearDatasetAssembler::prepareTableSections4Statement($datasourceHandler, $callcontext, $requestedColumnNames, $isQuarterRequired);
        $isFiscalYearRequired = isset($fiscalYearTableSections);
        // calendar year
        $calendarYearTableSections = DateDimensionYearDatasetAssembler::prepareTableSections4Statement($datasourceHandler, $callcontext, $requestedColumnNames, $isQuarterRequired || $isFiscalYearRequired);
        $isCalendarYearRequired = isset($calendarYearTableSections);

        // we cannot link with quarters without querter def
        $isQuarterDefRequired = $isQuarterDefRequired || $isQuarterRequired;

        // we do not check for ($alwaysJoin || $isMonthRequired || $isMonthDefRequired) because if only those flags are true
        // all the work was done in prepareStatement_Jan2Nnn() and this function does not have to do anything in addition
        if ($isQuarterRequired || $isQuarterDefRequired || $isCalendarYearRequired || $isFiscalYearRequired) {
            // linking with quarter def is done through month def
            // Also we need month def for fiscal quarter
            // Also we need month def to select required months for BETWEEN
            $isMonthDefRequired = TRUE;

            if ($isCalendarYearRequired) {
                $tableMonth->event_updateTableAlias(
                    DateDimensionYearDatasetAssembler::$TABLE_ALIAS_SUFFIX__YEARS,
                    DateDimensionYearDatasetAssembler::$TABLE_ALIAS_PREFIX__CALENDAR . DateDimensionYearDatasetAssembler::$TABLE_ALIAS_SUFFIX__YEARS);
            }

            $statement->tables[] = $tableMonth;

            if ($isMonthDefRequired) {
                $tableMonthDef->conditions[] = new JoinConditionSection(
                	'month_def_id', new TableColumnConditionSectionValue(self::$TABLE_ALIAS_SUFFIX__MONTHS, 'month_def_id'));
                $statement->tables[] = $tableMonthDef;
            }

            if ($isQuarterDefRequired) {
                $tableQuarterDef->conditions[] = new JoinConditionSection(
                	'series',
                    new ExactConditionSectionValue(
                    	' = ((' . ColumnStatementCompositeEntityParser::assembleColumnName(self::$TABLE_ALIAS_SUFFIX__MONTH_DEF . '.series') . " - $fromMonthNumber) DIV 3) + 1"));
                $statement->tables[] = $tableQuarterDef;
            }

            if ($isCalendarYearRequired) {
                $tableYears = $calendarYearTableSections[0];
                $tableYears->conditions[] = new JoinConditionSection('year_id', new TableColumnConditionSectionValue(self::$TABLE_ALIAS_SUFFIX__MONTHS, 'year_id'));

                foreach ($calendarYearTableSections as $table) {
                    $table->event_updateTableAlias($table->alias, DateDimensionYearDatasetAssembler::$TABLE_ALIAS_PREFIX__CALENDAR . $table->alias);
                }

                ArrayHelper::mergeArrays($statement->tables, $calendarYearTableSections);
            }

            if ($isFiscalYearRequired) {
                // adjusting alias for all year table sections
                foreach ($fiscalYearTableSections as $table) {
                    $table->event_updateTableAlias($table->alias, DateDimensionYearDatasetAssembler::$TABLE_ALIAS_PREFIX__FISCAL . $table->alias);
                }

                $tableYears = $fiscalYearTableSections[0];
                $tableYears->conditions[] = new JoinConditionSection(
                    'entry_year',
                    new ExactConditionSectionValue(
                        ' = '
                        . ColumnStatementCompositeEntityParser::assembleColumnName(
                            DateDimensionYearDatasetAssembler::$TABLE_ALIAS_PREFIX__CALENDAR . DateDimensionYearDatasetAssembler::$TABLE_ALIAS_SUFFIX__YEARS . '.entry_year')
                        . ' + 1'));
                ArrayHelper::mergeArrays($statement->tables, $fiscalYearTableSections);
            }

            if ($isQuarterRequired) {
                $tableQuarters->conditions[] = new JoinConditionSection(
                    'quarter_def_id', new TableColumnConditionSectionValue(DateDimensionQuarterDatasetAssembler::$TABLE_ALIAS_SUFFIX__QUARTER_DEF, 'quarter_def_id'));
                $tableQuarters->conditions[] = new JoinConditionSection(
                    'year_id',
                    new TableColumnConditionSectionValue(DateDimensionYearDatasetAssembler::$TABLE_ALIAS_SUFFIX__YEARS, 'year_id'));

                $tableQuarters->event_updateTableAlias(
                    DateDimensionYearDatasetAssembler::$TABLE_ALIAS_SUFFIX__YEARS,
                    DateDimensionYearDatasetAssembler::$TABLE_ALIAS_PREFIX__FISCAL . DateDimensionYearDatasetAssembler::$TABLE_ALIAS_SUFFIX__YEARS);

                $statement->tables[] = $tableQuarters;
            }

            $statement->conditions[] = new WhereConditionSection(
                self::$TABLE_ALIAS_SUFFIX__MONTH_DEF, 'series', new ExactConditionSectionValue(" BETWEEN $fromMonthNumber AND 12"));
        }

        return $statement;
    }

    public static function prepareTableSections4Statement(AbstractSQLDataSourceQueryHandler $datasourceHandler, DataControllerCallContext $callcontext, array &$requestedColumnNames = NULL, $alwaysJoin = FALSE) {
        $tableSections = NULL;

        if (DateDimensionConfiguration::$FISCAL_YEAR_FIRST_MONTH == 1) {
            // we do not need any specific support for fiscal year
            $statement = self::prepareStatement_Jan2Nnn($datasourceHandler, $callcontext, 12, $requestedColumnNames, $alwaysJoin);

            // it is expected that only 'tables' section is populated in the statement
            ArrayHelper::mergeArrays($tableSections, $statement->tables);
        }
        else {
            // we need another copy of requested column names for second request
            $copyOfRequestedColumnNames = $requestedColumnNames;
            // preparing configurations ..
            $statements = array(
                // ... for when current year equals fiscal year
                self::prepareStatement_Jan2Nnn($datasourceHandler, $callcontext, DateDimensionConfiguration::$FISCAL_YEAR_FIRST_MONTH - 1, $requestedColumnNames, $alwaysJoin),
                // ... for portion of current year which belongs to next fiscal year
                self::prepareStatement_Nnn2Dec($datasourceHandler, $callcontext, DateDimensionConfiguration::$FISCAL_YEAR_FIRST_MONTH, $copyOfRequestedColumnNames, $alwaysJoin));

            // we need to prepare list of column names to assemble SQL
            $assemblableColumnNames = NULL;

            // combining configurations
            $sql = '';
            for ($i = 0, $count = count($statements); $i < $count; $i++) {
                $statement = $statements[$i];
                // checking if any tables were selected to provide support for this request's columns
                if (!isset($statement->tables)) {
                    continue;
                }

                // we need to add columns only from first eligible statement
                $addAssemblableColumnNames = !isset($assemblableColumnNames);

                foreach ($statement->tables as $table) {
                    if (isset($table->columns)) {
                        if ($addAssemblableColumnNames) {
                            foreach ($table->columns as $column) {
                                if ($column->visible) {
                                    $assemblableColumnNames[] = $column->alias;
                                }
                            }
                        }
                    }
                    else {
                        $table->columns = []; // We do not need any columns
                    }
                }

                list($isSubqueryRequired, $assembledSections) = $statement->prepareSections($assemblableColumnNames);

                if (strlen($sql) > 0) {
                    $sql .= "\n" . str_pad('', self::$INDENT_FISCAL_SUBQUERY) . " UNION\n";
                }

                $sql .= Statement::assemble($isSubqueryRequired, $assemblableColumnNames, $assembledSections, self::$INDENT_FISCAL_SUBQUERY, ($i > 0));
            }

            if (strlen($sql) > 0) {
                $subquerySection = new SubquerySection($sql, DateDimensionYearDatasetAssembler::$TABLE_ALIAS_PREFIX__FISCAL . self::$TABLE_ALIAS_SUFFIX__MONTHS);

                // adding visible columns from first statement as invisible column in this subquery
                foreach ($assemblableColumnNames as $assemblableColumnName) {
                    $subqueryColumn = new ColumnSection($assemblableColumnName, $assemblableColumnName);
                    // FIXME do not use subquery. Use a list of statements as one section. Assemble when it is time
                    // $subqueryColumn->visible = FALSE;
                    $subquerySection->columns[] = $subqueryColumn;
                }

                $tableSections[] = $subquerySection;
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
