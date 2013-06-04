<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class StarSchemaNamingConvention {

    public static $COLUMN_NAME__ATTRIBUTE_LOOKUP_VALUE = 'value';

    protected static $MEASURE_NAME_DELIMITER = '__';
    public static $MEASURE_NAME__RECORD_COUNT = 'record_count';
    public static $MEASURE_NAME_SUFFIX__DISTINCT_COUNT = 'distinct_count';

    public static $SUFFIX__FACTS = '_facts';

    public static function preparePossibleOwners4Measure($measureName) {
        $owners = NULL;

        $parts = explode(self::$MEASURE_NAME_DELIMITER, $measureName);
        for ($i = count($parts) - 1; $i > 0; $i--) {
            $owners[] = implode(self::$MEASURE_NAME_DELIMITER, array_slice($parts, 0, $i));
        }

        return $owners;
    }

    public static function getAttributeRelatedName($name, $columnName) {
        return $name . '_' . $columnName;
    }

    public static function getAttributeRelatedMeasureName($attributeName, $functionName) {
        $adjustedAttributeName = str_replace(ParameterHelper::$COLUMN_NAME_DELIMITER__CODE, self::$MEASURE_NAME_DELIMITER, $attributeName);

        return $adjustedAttributeName . self::$MEASURE_NAME_DELIMITER . strtolower($functionName);
    }

    public static function getFactsRelatedName($name) {
        return $name . self::$SUFFIX__FACTS;
    }

    public static function getFactRelatedMeasureName($columnName, $functionName) {
        return $columnName . self::$MEASURE_NAME_DELIMITER . strtolower($functionName);
    }
}
