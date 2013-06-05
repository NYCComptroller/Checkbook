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


// Theoretically this class has to be located in data_controller_ddl module
// This class is placed here because we need it when we query database and data_controller_ddl module could be turned off

class Sequence {

    public static $DATA_SOURCE_NAME = NULL;
    public static $COLUMN_TYPE = NULL;

    public static function getSequenceColumnType() {
        if (isset(self::$COLUMN_TYPE)) {
            return clone self::$COLUMN_TYPE;
        }

        $columnType = new ColumnType();
        $columnType->applicationType = IntegerDataTypeHandler::$DATA_TYPE;

        return $columnType;
    }

    protected static function checkDataSourceName() {
        if (!isset(self::$DATA_SOURCE_NAME)) {
            throw new IllegalStateException(t('Data Source to support sequence functionality has not been initialized'));
        }
    }

    public static function getNextSequenceValue($sequenceName) {
        $values = self::getNextSequenceValues($sequenceName, 1);

        return $values[0];
    }

    /**
     * @static
     * @param $sequenceName
     * @param $quantity
     * @return array
     */
    public static function getNextSequenceValues($sequenceName, $quantity) {
        self::checkDataSourceName();

        $dataQueryController = data_controller_get_instance();

        return $dataQueryController->getNextSequenceValues(self::$DATA_SOURCE_NAME, $sequenceName, $quantity);
    }
}
