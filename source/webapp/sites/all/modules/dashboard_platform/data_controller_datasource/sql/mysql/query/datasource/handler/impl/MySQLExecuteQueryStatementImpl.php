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
