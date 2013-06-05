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




class ParameterHelper {

    public static $COLUMN_NAME_DELIMITER__CODE = '.';
    public static $COLUMN_NAME_DELIMITER__DATABASE = '_';

    public static function splitName($parameterName) {
        $MIN_SECTION_COUNT = 1; // measure | cube source property
        $MAX_SECTION_COUNT = 3; // dimension + level + property

        if (strpos($parameterName, self::$COLUMN_NAME_DELIMITER__CODE) === FALSE) {
            return array($parameterName, NULL, NULL);
        }
        else {
            $parts = explode(self::$COLUMN_NAME_DELIMITER__CODE, $parameterName);
            $count = count($parts);
            if ($count > $MAX_SECTION_COUNT) {
                throw new IllegalArgumentException(t(
                	"Parameter name cannot contain more than $MAX_SECTION_COUNT sections (dimension, level, property): @name",
                array('@name' => $parameterName)));
            }

            for ($i = ($count - 1); $i < $MAX_SECTION_COUNT; $i++) {
                $parts[] = NULL;
            }

            return $parts;
        }
    }

    public static function assembleParameterName($dimensionName, $levelName, $propertyName = NULL) {
        $name = $dimensionName;

        if (isset($levelName)) {
            $name .= self::$COLUMN_NAME_DELIMITER__CODE . $levelName;
        }

        // property should be added only
        if (isset($propertyName)) {
            if (isset($levelName)) {
                $name .= self::$COLUMN_NAME_DELIMITER__CODE . $propertyName;
            }
            else {
                throw new IllegalArgumentException(t('Level name has not been defined'));
            }
        }

        return $name;
    }

    public static function assembleDatabaseColumnName($maximumLength, $elementName, $subElementName = NULL, $elementPropertyName = NULL) {
        $databaseColumnName = ReferencePathHelper::generateDatabaseColumnName($elementName);

        if (isset($subElementName)) {
            $databaseColumnName .= self::$COLUMN_NAME_DELIMITER__DATABASE . strtolower($subElementName);
        }

        if (isset($elementPropertyName)) {
            $databaseColumnName .= self::$COLUMN_NAME_DELIMITER__DATABASE . strtolower($elementPropertyName);
        }

        return ParameterNameTruncater::truncateParameterName($databaseColumnName, $maximumLength);
    }
}
