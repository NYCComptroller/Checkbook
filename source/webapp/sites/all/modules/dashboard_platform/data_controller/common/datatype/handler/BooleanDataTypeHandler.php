<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class BooleanDataTypeHandler extends AbstractDataTypeHandler {

    public static $DATA_TYPE = 'boolean';

    public static $SUPPORTED_VALUE_SET__FALSE_TRUE = TRUE;
    public static $SUPPORTED_VALUE_SET__0_1 = FALSE;
    public static $SUPPORTED_VALUE_SET__N_Y = FALSE;
    public static $SUPPORTED_VALUE_SET__NO_YES = FALSE;
    public static $SUPPORTED_VALUE_SET__OFF_ON = FALSE;

    public static $STORAGE_VALUE__FALSE = '0';
    public static $STORAGE_VALUE__TRUE = '1';

    public function getStorageDataType() {
        return self::$DATA_TYPE;
    }

    protected function isValueOfImpl(&$value) {
        $isValueOf = parent::isValueOfImpl($value);
        if ($isValueOf) {
            $result = $this->testValue($value);
            $isValueOf = isset($result);
        }

        return $isValueOf;
    }

    public function selectCompatible($datatype) {
        if (($datatype === IntegerDataTypeHandler::$DATA_TYPE)
                || ($datatype === NumberDataTypeHandler::$DATA_TYPE)) {
            return $datatype;
        }

        return parent::selectCompatible($datatype);
    }

    protected function isParsableImpl(&$value) {
        if (!parent::isParsableImpl($value)) {
            return FALSE;
        }

        $result = $this->testValue($value);

        return isset($result);
    }

    protected function testValue($value) {
        $result = NULL;

        if (is_bool($value)) {
            $result = $value;
        }
        else {
            $adjustedValue = strtoupper($value);

            $result = $this->testFalseTrueValue($adjustedValue);
            if (!isset($result)) {
                $result = $this->test01Value($adjustedValue);
            }
            if (!isset($result)) {
                $result = $this->testNYValue($adjustedValue);
            }
            if (!isset($result)) {
                $result = $this->testNoYesValue($adjustedValue);
            }
            if (!isset($result)) {
                $result = $this->testOffOnValue($adjustedValue);
            }
        }

        return $result;
    }

    protected function testFalseTrueValue($value) {
        $result = NULL;

        if (self::$SUPPORTED_VALUE_SET__FALSE_TRUE) {
            if ($value === 'FALSE') {
                $result = FALSE;
            }
            elseif ($value === 'TRUE') {
                $result = TRUE;
            }
        }

        return $result;
    }

    protected function test01Value($value) {
        $result = NULL;

        if (self::$SUPPORTED_VALUE_SET__0_1) {
            if ($value == '0') {
                $result = FALSE;
            }
            elseif ($value == '1') {
                $result = TRUE;
            }
        }

        return $result;
    }

    protected function testNYValue($value) {
        $result = NULL;

        if (self::$SUPPORTED_VALUE_SET__N_Y) {
            if ($value === 'N') {
                $result = FALSE;
            }
            elseif ($value === 'Y') {
                $result = TRUE;
            }
        }

        return $result;
    }

    protected function testNoYesValue($value) {
        $result = NULL;

        if (self::$SUPPORTED_VALUE_SET__NO_YES) {
            if ($value === 'NO') {
                $result = FALSE;
            }
            elseif ($value === 'YES') {
                $result = TRUE;
            }
        }

        return $result;
    }

    protected function testOffOnValue($value) {
        $result = NULL;

        if (self::$SUPPORTED_VALUE_SET__OFF_ON) {
            if ($value === 'OFF') {
                $result = FALSE;
            }
            elseif ($value === 'ON') {
                $result = TRUE;
            }
        }

        return $result;
    }

    public function castValue($value) {
        $adjustedValue = parent::castValue($value);
        if (!isset($adjustedValue)) {
            return NULL;
        }

        if (is_bool($adjustedValue)) {
            // do nothing. It is already correct type
        }
        else {
            $result = $this->testValue($adjustedValue);
            if (isset($result)) {
                $adjustedValue = $result;
            }
            else {
                throw new IllegalArgumentException(t('Incorrect value of type BOOLEAN: @value', array('@value' => $adjustedValue)));
            }
        }

        return $adjustedValue;
    }
}
