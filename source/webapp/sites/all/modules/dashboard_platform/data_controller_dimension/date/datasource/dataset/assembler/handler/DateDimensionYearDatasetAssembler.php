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
