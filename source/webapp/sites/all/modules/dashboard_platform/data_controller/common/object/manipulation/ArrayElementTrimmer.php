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
