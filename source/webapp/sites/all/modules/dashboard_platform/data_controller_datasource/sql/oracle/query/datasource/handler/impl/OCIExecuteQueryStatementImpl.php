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


class OCIExecuteQueryStatementImpl extends AbstractExecuteQueryStatementImpl {

    public function execute(
            DataSourceHandler $handler,
            DataControllerCallContext $callcontext,
            $connection, $sql,
            __SQLDataSourceHandler__AbstractQueryCallbackProxy $callbackInstance) {

        $statement = OCIImplHelper::oci_parse($connection, $sql);
        try {
            OCIImplHelper::oci_execute($connection, $statement, OCI_NO_AUTO_COMMIT);
            $result = $callbackInstance->callback($callcontext, $connection, $statement);
        }
        catch (Exception $e) {
            OCIImplHelper::oci_free_statement($connection, $statement);

            throw $e;
        }
        OCIImplHelper::oci_free_statement($connection, $statement);

        return $result;
    }
}

class OracleQueryStatementExecutionCallback extends AbstractQueryStatementExecutionCallback {

    public function fetchNextRecord($connection, $statement) {
        // if we do not use OCI_RETURN_NULLS and first record contains all NULL values oci_fetch_array() will return FALSE and we will stop processing of other records
        return OCIImplHelper::oci_fetch_array($connection, $statement, OCI_NUM + OCI_RETURN_NULLS);
    }

    public function getColumnCount($connection, $statement) {
        return OCIImplHelper::oci_num_fields($connection, $statement);
    }

    public function getColumnMetaData($connection, $statement, $columnIndex) {
        $columnNumber = $columnIndex + 1;

        $column = new ColumnMetaData();
        $column->name = strtolower(OCIImplHelper::oci_field_name($connection, $statement, $columnNumber));
        // preparing the column type
        $column->type->databaseType = OCIImplHelper::oci_field_type($connection, $statement, $columnNumber);
        switch ($column->type->databaseType) {
            case 'VARCHAR2':
                $column->type->length = OCIImplHelper::oci_field_size($connection, $statement, $columnNumber);
                break;
            case 'NUMBER':
                $column->type->precision = OCIImplHelper::oci_field_precision($connection, $statement, $columnNumber);
                $column->type->scale = OCIImplHelper::oci_field_scale($connection, $statement, $columnNumber);
                break;
        }

        return $column;
    }

    public function calculateApplicationDataType(ColumnMetaData $column) {
        $columnDataType = NULL;

        switch ($column->type->databaseType) {
            case 'VARCHAR2':
                $columnDataType = StringDataTypeHandler::$DATA_TYPE;
                break;
            case 'NUMBER':
                $columnDataType = NumberDataTypeHandler::$DATA_TYPE;

                if ($column->type->precision == 0) {
                    // it is calculated (not physical) column of type 'NUMBER'
                }
                else {
                    if ($column->type->scale == 0) {
                        if ($column->type->precision <= 10) {
                            $columnDataType = IntegerDataTypeHandler::$DATA_TYPE;
                        }
                    }
                }

                break;
            default:
                throw new UnsupportedOperationException(t(
                    "Unsupported data type for '@columnName' column: @datatype",
                    array('@columnName' => $column->name, '@datatype' => $column->type->databaseType)));
        }

        return $columnDataType;
    }
}
