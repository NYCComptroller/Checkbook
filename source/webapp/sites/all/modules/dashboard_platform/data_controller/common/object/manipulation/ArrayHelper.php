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




class ArrayHelper {

    public static $DELIMITER__COMPOSITE_KEY = '|';

    public static function isIndexedArray(array $array = NULL) {
        if (!isset($array)) {
            return NULL;
        }

        foreach ($array as $key => $value) {
            if (!is_int($key)) {
                return FALSE;
            }
        }

        return TRUE;
    }

    public static function addUniqueValue(array &$array = NULL, $value) {
        if (isset($value)) {
            if (is_array($value)) {
                LogHelper::log_error(t("[@value] should not be an array", array('@value' => implode(', ', $value))));
                throw new IllegalArgumentException(t('Value should not be an array'));
            }

            if (isset($array)) {
                if (array_search($value, $array) === FALSE) {
                    $array[] = $value;
                    return TRUE;
                }
            }
            else {
                $array[] = $value;
                return TRUE;
            }
        }

        return FALSE;
    }

    public static function addUniqueValues(array &$array = NULL, array $values = NULL) {
        if (isset($values)) {
            foreach ($values as $v) {
                self::addUniqueValue($array, $v);
            }
        }
    }

    public static function prepareCompositeKey(array $values) {
        return self::printArray($values, self::$DELIMITER__COMPOSITE_KEY, FALSE, FALSE);
    }

    public static function insertValue(array &$array = NULL, $position, $value) {
        if (isset($array)) {
            array_splice($array, $position, 0, $value);
        }
        else {
            $array[$position] = $value;
        }
    }

    public static function appendValue(array &$array = NULL, $value) {
        if (isset($value)) {
            if (is_array($value)) {
                self::mergeArrays($array, $value);
            }
            else {
                $array[] = $value;
            }
        }
    }

    public static function mergeArrays(array &$destinationArray = NULL, array $sourceArray = NULL) {
        if (!isset($sourceArray)) {
            return;
        }

        if (isset($destinationArray)) {
            $destinationArray = array_merge($destinationArray, $sourceArray);
        }
        else {
            $destinationArray = $sourceArray;
        }
    }

    public static function cloneArray(array &$array = NULL) {
        $clonnedArray = NULL;

        if (isset($array)) {
            // it is possible that the array was empty. I expect it was a reason for that. We do not want to convert it to NULL
            $clonnedArray = [];

            foreach ($array as $key => $value) {
                $clonnedArray[$key] = is_array($value)
                    ? self::cloneArray($value)
                    : (is_object($value) ? clone $value : $value);
            }
        }

        return $clonnedArray;
    }

    public static function toArray($value) {
        $values = NULL;

        if (isset($value)) {
            if (is_array($value)) {
                // to support associative arrays and index array with random indexes
                foreach ($value as $v) {
                    $values[] = $v;
                }
            }
            else {
                $values[] = $value;
            }
        }

        return $values;
    }

    public static function printArray(array $values = NULL, $delimiter, $addArrayBrackets, $isStringQuoted) {
        if (!isset($values)) {
            return NULL;
        }

        $isIndexArray = self::isIndexedArray($values);

        $s = '';
        if ($isIndexArray && !$isStringQuoted) {
            $s .= implode($delimiter, $values);
        }
        else {
            foreach ($values as $key => $value) {
                if (strlen($s) > 0) {
                    $s .= $delimiter;
                }

                if (!$isIndexArray) {
                    $s .= isset($key)
                        ? ((is_numeric($key) || !$isStringQuoted) ? $key : "'$key'")
                        : 'null';
                    $s .= ' = ';
                }

                $s .= isset($value)
                    ? (is_array($value) ? self::printArray($value, $delimiter, TRUE, $isStringQuoted) : ((is_numeric($value) || !$isStringQuoted) ? $value : "'$value'"))
                    : 'null';

            }
        }

        return $addArrayBrackets ? "[$s]" : $s;
    }
}
