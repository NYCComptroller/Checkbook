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
