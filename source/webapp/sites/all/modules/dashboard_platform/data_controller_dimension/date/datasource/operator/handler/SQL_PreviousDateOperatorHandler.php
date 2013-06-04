<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class SQL_PreviousDateOperatorHandler extends SQL_PreviousOperatorHandler {}

class SQL_PreviousMonthOperatorHandler extends SQL_AbstractPreviousOperatorHandler {

    protected function adjustCalculatedValue($date) {
        return SQL_DateOperatorHandlerHelper::adjustToFirstDayOfMonth($date);
    }
}

class SQL_PreviousQuarterOperatorHandler extends SQL_AbstractPreviousOperatorHandler {

    protected function adjustCalculatedValue($date) {
        return SQL_DateOperatorHandlerHelper::adjustToFirstDayOfFiscalQuarter($date);
    }
}

class SQL_PreviousYearOperatorHandler extends SQL_AbstractPreviousOperatorHandler {

    protected function adjustCalculatedValue($date) {
        return SQL_DateOperatorHandlerHelper::adjustToFirstDayOfFiscalYear($date);
    }
}
