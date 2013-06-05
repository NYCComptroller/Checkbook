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


class SQL_DynamicDateRangeOperatorHandler extends SQL_AbstractDynamicRangeOperatorHandler {

    protected function getLatestOperatorName() {
        return LatestDateOperatorHandler::$OPERATOR__NAME;
    }

    protected function offsetLatestValue($latestDateValue, $offset) {
        $datetime = new DateTime($latestDateValue);
        $datetime->sub(new DateInterval("P{$offset}D"));

        return $datetime->format(DateDataTypeHandler::getDateMask());
    }
}

class SQL_DynamicMonthRangeOperatorHandler extends SQL_AbstractDynamicRangeOperatorHandler {

    protected function getLatestOperatorName() {
        return LatestMonthOperatorHandler::$OPERATOR__NAME;
    }

    protected function offsetLatestValue($latestMonthValue, $offset) {
        $datetime = new DateTime($latestMonthValue);
        $datetime->sub(new DateInterval("P{$offset}M"));

        return $datetime->format(DateDataTypeHandler::getDateMask());
    }
}

class SQL_DynamicQuarterRangeOperatorHandler extends SQL_AbstractDynamicRangeOperatorHandler {

    protected function getLatestOperatorName() {
        return LatestQuarterOperatorHandler::$OPERATOR__NAME;
    }

    protected function offsetLatestValue($latestQuarterValue, $offset) {
        // convering number of quarters to number of months
        $monthOffset = $offset * 3;

        $datetime = new DateTime($latestQuarterValue);
        $datetime->sub(new DateInterval("P{$monthOffset}M"));

        return $datetime->format(DateDataTypeHandler::getDateMask());
    }
}

class SQL_DynamicYearRangeOperatorHandler extends SQL_AbstractDynamicRangeOperatorHandler {

    protected function getLatestOperatorName() {
        return LatestYearOperatorHandler::$OPERATOR__NAME;
    }

    protected function offsetLatestValue($latestYearValue, $offset) {
        return $latestYearValue - $offset;
    }
}
