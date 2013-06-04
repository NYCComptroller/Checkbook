<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class SQL_DateOperatorHandlerHelper {

    public static function adjustToFirstDayOfMonth($date) {
        $proxy = new DateTimeProxy(new DateTime($date));

        // converting the date to the first day of corresponding month
        $adjustedDateTime = new DateTime();
        $adjustedDateTime->setDate($proxy->getYear(), $proxy->getMonth(), 1);

        return $adjustedDateTime->format(DateDataTypeHandler::getDateMask());
    }

    public static function adjustToFirstDayOfFiscalQuarter($date) {
        $proxy = new DateTimeProxy(new DateTime($date));
        list($fiscalYear, $fiscalMonth) = DateDimensionConfiguration::getFiscalMonth($proxy->getYear(), $proxy->getMonth());
        $fiscalQuarter = DateTimeProxy::getQuarterByMonth($fiscalMonth);
        $firstMonthOfFiscalQuarter = DateTimeProxy::getFirstMonthOfQuarter($fiscalQuarter);

        // converting the date to the first day of corresponding quarter
        $adjustedDateTime = new DateTime();
        $adjustedDateTime->setDate($fiscalYear, $firstMonthOfFiscalQuarter, 1);

        return $adjustedDateTime->format(DateDataTypeHandler::getDateMask());
    }

    public static function adjustToFirstDayOfFiscalYear($date) {
        $proxy = new DateTimeProxy(new DateTime($date));
        list($fiscalYear) = DateDimensionConfiguration::getFiscalMonth($proxy->getYear(), $proxy->getMonth());

        // converting the date to the first day of corresponding year
        $adjustedDateTime = new DateTime();
        $adjustedDateTime->setDate($fiscalYear, 1, 1);

        return $adjustedDateTime->format(DateDataTypeHandler::getDateMask());
    }
}
