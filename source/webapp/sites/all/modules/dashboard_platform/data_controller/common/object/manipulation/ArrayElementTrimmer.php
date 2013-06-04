<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class ArrayElementTrimmer {

    public static function trimList($list) {
        $trimmedList = NULL;

        if (isset($list)) {
            if (is_array($list)) {
                foreach ($list as $key => $value) {
                    $trimmedValue = is_array($value) ? self::trimList($value) : StringHelper::trim($value);
                    if (isset($trimmedValue)) {
                        $trimmedList[$key] = $trimmedValue;
                    }
                }
            }
            else {
                // provided value is not an array. Converting result to an array with one element
                $value = StringHelper::trim($list);
                if (isset($value)) {
                    $trimmedList[] = $value;
                }
            }
        }

        return $trimmedList;
    }

    public static function trimMap($map) {
        $trimmedMap = NULL;

        if (isset($map)) {
            foreach ($map as $key => $value) {
                $key = StringHelper::trim($key);

                $value = is_array($value)
                    ? (ArrayHelper::isIndexedArray($value) ? self::trimList($value) : self::trimMap($value))
                    : StringHelper::trim($value);

                $trimmedMap[$key] = $value;
            }
        }

        return $trimmedMap;
    }
}
