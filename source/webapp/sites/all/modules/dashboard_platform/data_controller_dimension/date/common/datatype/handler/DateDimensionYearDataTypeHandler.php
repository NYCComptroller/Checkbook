<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DateDimensionYearDataTypeHandler extends AbstractIntegerDataTypeHandler {

    public static $DATA_TYPE = 'date:year';

    public static $MINIMUM_YEAR = 1900;
    public static $MAXIMUM_YEAR = 2100;

    public function getStorageDataType() {
        return IntegerDataTypeHandler::$DATA_TYPE;
    }

    protected function isValueInRange($year) {
        return ($year >= self::$MINIMUM_YEAR) && ($year <= self::$MAXIMUM_YEAR);
    }

    protected function isValueOfImpl(&$value) {
        $isValueOf = parent::isValueOfImpl($value);

        if ($isValueOf) {
            $isValueOf = $this->isValueInRange($value);
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
        $isParsable = parent::isParsableImpl($value);
        if ($isParsable) {
            $year = $this->castValue($value);
            $isParsable = $this->isValueInRange($year);
        }

        return $isParsable;
    }
}
