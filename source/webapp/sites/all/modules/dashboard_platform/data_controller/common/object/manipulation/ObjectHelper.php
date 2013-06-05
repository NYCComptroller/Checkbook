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


class ObjectHelper {

    public static function mergeWith(&$instance, $source, $mergeCompositeProperty = FALSE) {
        if (isset($source)) {
            if (is_object($source) || is_array($source)) {
                foreach ($source as $name => $value) {
                    // source does not have value for the property
                    if (!isset($value)) {
                        continue;
                    }

                    // we do not support composite properties
                    if (is_object($value) || is_array($value)) {
                        if ($mergeCompositeProperty) {
                            if (!isset($instance->$name)) {
                                $instance->$name = NULL;
                            }

                            if (is_object($value)) {
                                // support for an object
                                self::mergeWith($instance->$name, $value, TRUE);
                            }
                            else {
                                if (ArrayHelper::isIndexedArray($value)) {
                                    // support for an indexed array
                                    $a = NULL;
                                    foreach ($value as $index => $v) {
                                        $o = NULL;
                                        self::mergeWith($o, $v, TRUE);

                                        $a[$index] = $o;
                                    }
                                    $instance->$name = $a;
                                }
                                else {
                                    // support for an associative array
                                    self::mergeWith($instance->$name, $value, TRUE);
                                }
                            }
                        }

                        continue;
                    }

                    // overriding is not allowed
                    if (isset($instance->$name) && ($instance->$name != $value)) {
                        LogHelper::log_error(t(
                            "'@propertyName' property already contains value: @existingPropertyValue. Merge cannot be performed with new value: @newPropertyValue",
                            array('@propertyName' => $name, '@existingPropertyValue' => $instance->$name, '@newPropertyValue' => $value)));
                        throw new UnsupportedOperationException(t(
                            "'@propertyName' property already contains value. Merge cannot be performed",
                            array('@propertyName' => $name)));
                    }

                    $instance->$name = $value;
                }
            }
            else {
                $instance = $source;
            }
        }

        return $instance;
    }

    public static function copySelectedProperties(&$instance, $source, $sourcePropertyNames) {
        if (is_object($source) || is_array($source)) {
            foreach ($sourcePropertyNames as $sourcePropertyName) {
                $sourceValue = self::getPropertyValue($source, $sourcePropertyName);
                if (isset($sourceValue)) {
                    $instance->$sourcePropertyName = $sourceValue;
                }
            }
        }

        return $instance;
    }

    public static function getPropertyValue($source, $propertyName) {
        $value = NULL;

        if (is_object($source)) {
            if (isset($source->$propertyName)) {
                $value = $source->$propertyName;
            }
        }
        elseif (is_array($source)) {
            if (isset($source[$propertyName]))  {
                $value = $source[$propertyName];
            }
        }

        return $value;
    }
}
