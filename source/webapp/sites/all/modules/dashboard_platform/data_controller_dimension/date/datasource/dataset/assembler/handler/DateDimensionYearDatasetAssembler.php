<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DateDimensionYearDatasetAssembler extends AbstractDateDimensionDatasetAssembler {

    public static $DATASET_SOURCE_ASSEMBLER__TYPE = 'dimension/date/year';

    public static $TABLE_ALIAS_PREFIX__CALENDAR = 'c';
    public static $TABLE_ALIAS_PREFIX__FISCAL = 'f';

    public static $TABLE_ALIAS_SUFFIX__YEARS = 'y';

    public static function prepareTableSection(AbstractSQLDataSourceQueryHandler $datasourceHandler, DataControllerCallContext $callcontext, array &$requestedColumnNames = NULL) {
        $metamodel = data_controller_get_metamodel();

        $tableYears = new DatasetSection(
            $metamodel->getDataset(DateDimensionDatasetNames::YEARS),
            self::$TABLE_ALIAS_SUFFIX__YEARS);
        self::selectColumn($tableYears, $requestedColumnNames, FALSE, 'entry_year', FALSE);

        $columnYearId = self::selectColumn($tableYears, $requestedColumnNames, isset($tableYears->columns), 'year_id', FALSE);
        if (isset($columnYearId)) {
            $columnYearId->key = TRUE;
        }

        return $tableYears;
    }

    public static function prepareTableSections4Statement(AbstractSQLDataSourceQueryHandler $datasourceHandler, DataControllerCallContext $callcontext, array &$requestedColumnNames = NULL, $alwaysJoin = FALSE) {
        $tableSections = NULL;

        $tableYears = self::prepareTableSection($datasourceHandler, $callcontext, $requestedColumnNames);
        $isYearRequired = isset($tableYears->columns);

        if ($alwaysJoin || $isYearRequired) {
            $tableSections[] = $tableYears;
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
