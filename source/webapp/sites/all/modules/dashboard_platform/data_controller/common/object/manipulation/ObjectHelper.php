<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
