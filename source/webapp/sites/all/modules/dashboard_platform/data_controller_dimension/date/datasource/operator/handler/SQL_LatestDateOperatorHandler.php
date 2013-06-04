<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class SQL_LatestDateOperatorHandler extends SQL_LatestOperatorHandler {}

class SQL_LatestMonthOperatorHandler extends SQL_AbstractLatestOperatorHandler {

    protected function adjustCalculatedValue($date) {
        return SQL_DateOperatorHandlerHelper::adjustToFirstDayOfMonth($date);
    }
}

class SQL_LatestQuarterOperatorHandler extends SQL_AbstractLatestOperatorHandler {

    protected function adjustCalculatedValue($date) {
        return SQL_DateOperatorHandlerHelper::adjustToFirstDayOfFiscalQuarter($date);
    }
}

class SQL_LatestYearOperatorHandler extends SQL_AbstractLatestOperatorHandler {

    protected function adjustCalculatedValue($date) {
        return SQL_DateOperatorHandlerHelper::adjustToFirstDayOfFiscalYear($date);
    }
}
