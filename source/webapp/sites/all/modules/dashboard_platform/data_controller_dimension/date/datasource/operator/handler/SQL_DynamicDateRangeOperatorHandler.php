<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
