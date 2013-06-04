<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class ArgumentHelper {

    public static function getNamedArguments() {
        global $argv;

        $datatypeFactory = DataTypeFactory::getInstance();

        $namedArguments = NULL;
        foreach ($argv as $argument) {
            $index = strpos($argument, '=');
            if ($index === FALSE) {
                continue;
            }

            // preparing name of the argument
            $name = trim(substr($argument, 0, $index));

            // preparing value of the argument
            $value = substr($argument, $index + 1);
            if ($value === FALSE) {
                continue;
            }
            else {
                $value = $datatypeFactory->getHandler($datatypeFactory->autoDetectDataType($value))->castValue($value);
            }

            if (isset($namedArguments[$name])) {
                $oldValue = $namedArguments[$name];
                if (is_array($oldValue)) {
                    $oldValue[] = $value;
                    $namedArguments[$name] = $oldValue;
                }
                else {
                    $namedArguments[$name] = array($oldValue, $value);
                }
            }
            else {
                $namedArguments[$name] = $value;
            }
        }

        return $namedArguments;
    }

    public static function findNamedArgument($name) {
        $namedArguments = self::getNamedArguments();

        return isset($namedArguments[$name]) ? $namedArguments[$name] : NULL;
    }

    public static function getNamedArgument($name, $defaultValue = NULL) {
        $value = self::findNamedArgument($name);
        if (!isset($value)) {
            $value = $defaultValue;
        }

        if (!isset($value)) {
            throw new IllegalArgumentException(t("Command line argument '@name' was not defined", array('@name' => $name)));
        }

        return $value;
    }
}
