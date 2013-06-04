<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
