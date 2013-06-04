<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DateDimensionConfiguration {

    // 1 - January, ..., 12 - December
    public static $FISCAL_YEAR_FIRST_MONTH = 1;

    public static function getFiscalMonth($calendarYear, $calendarMonth) {
        $fiscalYear = $calendarYear;

        $fiscalMonth = $calendarMonth + self::$FISCAL_YEAR_FIRST_MONTH - 1;
        if ($fiscalMonth > 12) {
            $fiscalMonth -= 12;
            $fiscalYear++;
        }

        return array($fiscalYear, $fiscalMonth);
    }
}
