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
