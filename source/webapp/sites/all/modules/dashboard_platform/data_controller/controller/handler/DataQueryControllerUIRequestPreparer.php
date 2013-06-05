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


class DataQueryControllerUIRequestPreparer extends AbstractObject {

    public static $QUERY_PARAMETER__COLUMN_NAME = 'n';
    public static $QUERY_PARAMETER__OPERATOR_NAME = 'o';
    public static $QUERY_PARAMETER__OPERATOR_VALUE = 'v';

    // -----------------------------------------------------------------------------------------------------------------------------
    //   Parsing parameters
    // -----------------------------------------------------------------------------------------------------------------------------
    public static function parseColumns(array $columns = NULL) {
        return $columns;
    }

    public static function parseParameters(array $parameters = NULL) {
        if (!isset($parameters)) {
            return NULL;
        }

        $adjustedParameters = NULL;

        foreach ($parameters as $parameterIndex => $parameterProperties) {
            if (!is_array($parameterProperties)) {
                $parameterProperties = array($parameterProperties);
            }

            $isParameterPropertyDetected = FALSE;

            $parameterName = NULL;
            if (is_int($parameterIndex)) {
                if (!isset($parameterProperties[self::$QUERY_PARAMETER__COLUMN_NAME])) {
                    throw new IllegalArgumentException(t(
                        'Could not find corresponding column name for the parameter: @parameterIndex',
                        array('@parameterIndex' => $parameterIndex)));
                }
                $parameterName = $parameterProperties[self::$QUERY_PARAMETER__COLUMN_NAME];

                unset($parameterProperties[self::$QUERY_PARAMETER__COLUMN_NAME]);
                $isParameterPropertyDetected = TRUE;
            }
            else {
                $parameterName = $parameterIndex;
            }

            $operatorName = NULL;
            if (isset($parameterProperties[self::$QUERY_PARAMETER__OPERATOR_NAME])) {
                $operatorName = $parameterProperties[self::$QUERY_PARAMETER__OPERATOR_NAME];

                unset($parameterProperties[self::$QUERY_PARAMETER__OPERATOR_NAME]);
                $isParameterPropertyDetected = TRUE;
            }
            else {
                $operatorName = EqualOperatorHandler::$OPERATOR__NAME;
            }

            $parameterValues = NULL;
            if (isset($parameterProperties[self::$QUERY_PARAMETER__OPERATOR_VALUE])) {
                $parameterValues = $parameterProperties[self::$QUERY_PARAMETER__OPERATOR_VALUE];

                unset($parameterProperties[self::$QUERY_PARAMETER__OPERATOR_VALUE]);
                $isParameterPropertyDetected = TRUE;
            }

            if (!$isParameterPropertyDetected) {
                $parameterValues = $parameterProperties;
                // marking that all properties are processed in the variable
                $parameterProperties = NULL;
            }

            // some properties are left and we do not know what to do with them
            if (count($parameterProperties) > 0) {
                throw new IllegalArgumentException(t(
                    'Unsupported keys for parameter definition: @unsupportedKeys',
                    array('@unsupportedKeys' => implode(', ', array_keys($parameterProperties)))));
            }

            $operatorValues = NULL;
            if (isset($parameterValues)) {
                if (ArrayHelper::isIndexedArray($parameterValues)) {
                    $operatorValues = $parameterValues;
                }
                else {
                    // named operator value parameters are provided
                    // we need to order the values in the same order as the parameters defined for the operator
                    $operatorMetaData = OperatorFactory::getInstance()->getOperatorMetaData($operatorName);
                    $operatorParameterMetaDatas = $operatorMetaData->getParameters();
                    if (isset($operatorParameterMetaDatas)) {
                        foreach ($operatorParameterMetaDatas as $operatorParameterMetaData) {
                            $name = $operatorParameterMetaData->name;

                            $value = NULL;
                            if (isset($parameterValues[$name])) {
                                $value = $parameterValues[$name];

                                unset($parameterValues[$name]);
                            }
                            $operatorValues[] = $value;
                        }
                    }

                    // some named parameters are not recognized
                    if (count($parameterValues) > 0) {
                        throw new IllegalArgumentException(t(
                            'Unsupported keys for parameter value definition: @unsupportedKeys',
                            array('@unsupportedKeys' => implode(', ', array_keys($parameterValues)))));
                    }
                }
            }

            $operator = OperatorFactory::getInstance()->initiateHandler($operatorName, $operatorValues);

            if (isset($adjustedParameters[$parameterName])) {
                $previousOperator = $adjustedParameters[$parameterName];
                if (is_array($previousOperator)) {
                    $adjustedParameters[$parameterName][] = $operator;
                }
                else {
                    $adjustedParameters[$parameterName] = array($previousOperator, $operator);
                }
            }
            else {
                $adjustedParameters[$parameterName] = $operator;
            }
        }

        return $adjustedParameters;
    }

    public static function parseSortColumns(array $sortColumns = NULL) {
        return $sortColumns;
    }

    public static function parseOffset($offset) {
        return $offset;
    }

    public static function parseLimit($limit) {
        return $limit;
    }

    // -----------------------------------------------------------------------------------------------------------------------------
    //   Preparing parameters
    // -----------------------------------------------------------------------------------------------------------------------------
    public static function prepareColumns(array $columns = NULL) {
        return $columns;
    }

    protected static function prepareParameterValue($parameterName, $value) {
        $preparedValue = NULL;

        $preparedValue[self::$QUERY_PARAMETER__COLUMN_NAME] = $parameterName;

        if ($value::$OPERATOR__NAME != EqualOperatorHandler::$OPERATOR__NAME) {
            $preparedValue[self::$QUERY_PARAMETER__OPERATOR_NAME] = $value::$OPERATOR__NAME;
        }

        if (isset($value->metadata)) {
            foreach ($value->metadata->getParameters() as $operatorParameter) {
                $parameterValue = $value->{$operatorParameter->name};
                if (!$operatorParameter->required && ($parameterValue == $operatorParameter->defaultValue)) {
                    continue;
                }

                $preparedValue[self::$QUERY_PARAMETER__OPERATOR_VALUE][$operatorParameter->name] = $parameterValue;
            }
        }

        return $preparedValue;
    }

    public static function prepareParameters(array $parameters = NULL) {
        if (!isset($parameters)) {
            return NULL;
        }

        $preparedParameters = NULL;

        $index = 0;
        foreach ($parameters as $parameterName => $values) {
            if (is_array($values)) {
                foreach ($values as $value) {
                    $preparedParameters[$index] = self::prepareParameterValue($parameterName, $value);
                    $index++;
                }
            }
            else {
                $preparedParameters[$index] = self::prepareParameterValue($parameterName, $values);
                $index++;
            }
        }

        return $preparedParameters;
    }

    public static function prepareSortColumns(array $sortColumns = NULL) {
        return $sortColumns;
    }

    public static function prepareOffset($offset) {
        return $offset;
    }

    public static function prepareLimit($limit) {
        return $limit;
    }

    // -----------------------------------------------------------------------------------------------------------------------------
    //   Serializing parameters
    // -----------------------------------------------------------------------------------------------------------------------------
    public static function serializeValue($name, $value) {
        if (!isset($value)) {
            return NULL;
        }

        $serializedValues = NULL;

        $serializedName = isset($name) ? $name : '';

        if (is_array($value)) {
            foreach ($value as $itemKey => $itemValue) {
                $serializedItemValue = self::serializeValue(NULL, $itemValue);
                foreach ($serializedItemValue as $k => $v) {
                    $key = $serializedName . '[' . $itemKey . ']' . $k;
                    $serializedValues[$key] =  $v;
                }
            }
        }
        else {
            $key = $serializedName;

            $serializedValue = $value;
            if (is_bool($serializedValue)) {
                $serializedValue = $serializedValue ? 'true' : 'false';
            }

            $serializedValues[$key] = $serializedValue;
        }

        return $serializedValues;
    }
}
