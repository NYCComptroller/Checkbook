<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractDateDimensionDatasetAssembler extends AbstractDatasetSourceAssembler {

    protected static function selectColumn(TableSection $table, array &$requestedColumnNames = NULL, $alwaysInclude, $columnName, $compositeColumnName, $columnAlias = NULL) {
        $includeAllColumns = !isset($requestedColumnNames);

        $columnIdentity = isset($columnAlias) ? $columnAlias : $columnName;
        $isColumnPresent = isset($requestedColumnNames[$columnIdentity]);

        if ($alwaysInclude || $includeAllColumns || $isColumnPresent) {
            $column = $compositeColumnName
                ? new CompositeColumnSection($columnName, $columnAlias)
                : new ColumnSection($columnName, $columnAlias);
            // we included the column only because it is mandatory ($alwaysInclude == TRUE)
            // but because the column is not in the list and not all columns are requested we hide it
            $column->visible = $includeAllColumns || ($isColumnPresent && $requestedColumnNames[$columnIdentity]);

            $table->columns[] = $column;

            if ($isColumnPresent) {
                unset($requestedColumnNames[$columnIdentity]);
            }

            return $column;
        }

        return NULL;
    }

    protected static function prepareRequestedColumnNames($columnNames) {
        $requestedColumnNames = NULL;

        if (isset($columnNames)) {
            foreach ($columnNames as $columnName) {
                $requestedColumnNames[$columnName] = TRUE;
            }
        }

        return $requestedColumnNames;
    }

    protected static function registerDependentColumnName(array &$requestedColumnNames = NULL, $columnName) {
        if (!isset($requestedColumnNames[$columnName])) {
            // if the column is not present we add one but it should be invisible
            $requestedColumnNames[$columnName] = FALSE;
        }
    }
}
