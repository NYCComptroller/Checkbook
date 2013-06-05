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
