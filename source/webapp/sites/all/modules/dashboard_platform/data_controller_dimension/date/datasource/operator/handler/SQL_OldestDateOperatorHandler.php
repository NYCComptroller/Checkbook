<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class SQL_OldestDateOperatorHandler extends SQL_OldestOperatorHandler {}

class SQL_OldestMonthOperatorHandler extends SQL_AbstractLowestBoundaryOperatorHandler {

    protected function adjustCalculatedValue($date) {
        return SQL_DateOperatorHandlerHelper::adjustToFirstDayOfMonth($date);
    }
}

class SQL_OldestQuarterOperatorHandler extends SQL_AbstractLowestBoundaryOperatorHandler {

    protected function adjustCalculatedValue($date) {
        return SQL_DateOperatorHandlerHelper::adjustToFirstDayOfFiscalQuarter($date);
    }
}

class SQL_OldestYearOperatorHandler extends SQL_AbstractLowestBoundaryOperatorHandler {

    protected function adjustCalculatedValue($date) {
        return SQL_DateOperatorHandlerHelper::adjustToFirstDayOfFiscalYear($date);
    }
}
