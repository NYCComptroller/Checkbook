<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractDataTypeHandler extends AbstractObject implements DataTypeHandler {

    public function getHandlerType() {
        return DATA_TYPE__PRIMITIVE;
    }

    public function getMask() {
        return NULL;
    }

    public function getStorageMask() {
        return NULL;
    }

    public final function isValueOf($value) {
        $adjustedValue = $this->adjustValue($value);

        return $this->isValueOfImpl($adjustedValue);
    }

    protected function isValueOfImpl(&$value) {
        return isset($value);
    }

    public function selectCompatible($datatype) {
        return NULL;
    }

    public final function isParsable($value) {
        $adjustedValue = $this->adjustValue($value);

        return $this->isParsableImpl($adjustedValue);
    }

    protected function isParsableImpl(&$value) {
        return isset($value);
    }

    public function castValue($value) {
        return $this->adjustValue($value);
    }

    protected function adjustValue($value) {
        if (!isset($value)) {
            return NULL;
        }

        $adjustedValue = $value;
        if (is_string($adjustedValue)) {
            $adjustedValue = trim($adjustedValue);
            if (strlen($adjustedValue) === 0) {
                return NULL;
            }

            $v = strtoupper($adjustedValue);
            if (($v === 'NULL') || ($v === 'N/A')) {
                return NULL;
            }
        }

        return $adjustedValue;
    }
}
