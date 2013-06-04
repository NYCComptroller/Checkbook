<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class MySQLQueryStatementExecutionCallback extends AbstractPDOQueryStatementExecutionCallback {

    public function calculateApplicationDataType(ColumnMetaData $column) {
        $columnDataType = NULL;

        switch ($column->type->databaseType) {
            case 'BLOB':       // 252 - blob (text)
            case 'VAR_STRING': // 253 - varchar
            case 'STRING':     // 254 - char (binary)
                $columnDataType = StringDataTypeHandler::$DATA_TYPE;
                break;
//             case 'TINY':       // 1 - tiny
            case 'LONG':       // 3 - int
            case 'LONGLONG':   // 8 - bigint
                $columnDataType = IntegerDataTypeHandler::$DATA_TYPE;
                break;
            case 'DOUBLE':     // 5 - double
            case 'NEWDECIMAL': // 246 - DECIMAL(15, 2) ???
                $columnDataType = NumberDataTypeHandler::$DATA_TYPE;
                break;
            case 'DATE':       // 10 - date
                $columnDataType = DateDataTypeHandler::$DATA_TYPE;
                break;
//            case 'TIME':       // 11 - time
//                $columnDataType = TimeDataTypeHandler::$DATA_TYPE;
//                break;
            case 'TIMESTAMP':  // 7 - timestamp
//            case 'DATETIME':   // 12 - datetime
                $columnDataType = DateTimeDataTypeHandler::$DATA_TYPE;
                break;
            default:
                throw new UnsupportedOperationException(t(
                    "Unsupported data type for '@columnName' column: @datatype",
                    array('@columnName' => $column->name, '@datatype' => $column->type->databaseType)));
        }

        return $columnDataType;
    }
}
