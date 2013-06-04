<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class PostgreSQLQueryStatementExecutionCallback extends AbstractPDOQueryStatementExecutionCallback {

    public function calculateApplicationDataType(ColumnMetaData $column) {
        $columnDataType = NULL;

        switch ($column->type->databaseType) {
            // support for standard types
            case 'varchar':
            case 'text':
            case 'bpchar':
                $columnDataType = StringDataTypeHandler::$DATA_TYPE;
                break;
            case 'bit':
            case 'int2':
            case 'int4':
            case 'int8':
                $columnDataType = IntegerDataTypeHandler::$DATA_TYPE;
                break;
            case 'float4':
            case 'float8':
            case 'numeric':
            case 'money':
                $columnDataType = NumberDataTypeHandler::$DATA_TYPE;
                break;
            case 'bool':
                $columnDataType = BooleanDataTypeHandler::$DATA_TYPE;
                break;
            case 'date':
                $columnDataType = DateDataTypeHandler::$DATA_TYPE;
                break;
            case 'time':
                $columnDataType = TimeDataTypeHandler::$DATA_TYPE;
                break;
            case 'timestamp':
                $columnDataType = DateTimeDataTypeHandler::$DATA_TYPE;
                break;

            // support for entity names
            case 'name':
                $columnDataType = StringDataTypeHandler::$DATA_TYPE;
                break;

            default:
                throw new UnsupportedOperationException(t(
                    "Unsupported data type for '@columnName' column: @dataType",
                    array('@columnName' => $column->name, '@dataType' => $column->type->databaseType)));
        }

        return $columnDataType;
    }
}
