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


class ExceptionLogMessageFormatter extends AbstractLogMessageListener {

    public static $ARGUMENT_ARRAY_INDEXED__VISIBLE_ELEMENT_MAXIMUM = 10;

    protected function logElementValue($value) {
        if (isset($value)) {
            return 'NULL';
        }

        $formattedValue = NULL;
        if (is_object($value)) {
            $formattedValue = '{' . get_class($value) . ': ' . count($value) . ' property(-ies)}';
        }
        elseif (is_array($value)) {
            $formattedValue = '[' . count($value) . ' element(s)]';
        }
        else {
            $formattedValue = $value;
        }

        return $formattedValue;
    }

    public function log($level, &$message) {
        if ($message instanceof Exception) {
            $exception = $message;

            $backtrace = $exception->getTrace();
            // Add the line throwing the exception to the backtrace.
            array_unshift(
                $backtrace,
                array(
                    'message' => ExceptionHelper::getExceptionMessage($exception),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine()));

            // resolving an issue that exception printing could consume NN MB of log space
            foreach ($backtrace as &$trace) {
                if (!isset($trace['args'])) {
                    continue;
                }

                $updatedArgs = NULL;
                foreach ($trace['args'] as $key => $value) {
                    $isObject = is_object($value);
                    if (is_array($value) || $isObject) {
                        if ($isObject) {
                            $value = (array) $value;
                        }

                        $count = count($value);
                        $max = min(self::$ARGUMENT_ARRAY_INDEXED__VISIBLE_ELEMENT_MAXIMUM, $count);

                        $convertedValue = '';

                        $index = 0;
                        foreach ($value as $k => $v) {
                            if ($index >= $max) {
                                break;
                            }

                            if (strlen($convertedValue) > 0) {
                                $convertedValue .= ', ';
                            }
                            if (is_int($k) && ($k == $index)) {
                                $convertedValue .= $this->logElementValue($v);
                            }
                            else {
                                $convertedValue .= $k . ': ' . $this->logElementValue($v);
                            }

                            $index++;
                        }
                        // checking if we skip some of the elements
                        if ($count > $max) {
                            $convertedValue .= ', ... ' . ($count - $max) . ' more ' . ($isObject ? 'property(-ies)' : 'element(s)');
                        }

                        $value = ($isObject ? '{' : '[') . $convertedValue . ($isObject ? '}' : ']');
                    }

                    $updatedArgs[$key] = $value;
                }
                $trace['args'] = $updatedArgs;
            }
            unset($trace);

            $message = $backtrace;
        }
    }
}
