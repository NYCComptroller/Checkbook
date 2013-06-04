<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
