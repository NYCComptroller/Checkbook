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
