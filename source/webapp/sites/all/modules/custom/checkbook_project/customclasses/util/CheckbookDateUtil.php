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


class CheckbookDateUtil
{
  /**
   * @var
   */
  private static $currentFiscalYearId;
  /**
   * @var
   */
  private static $currentCalendarYearId;
  /**
   * @var
   */
  private static $currentCalendarYear;
  /**
   * @var
   */
  private static $currentFiscalYear;

  /**
   *
   */
  private static function setCurrentYears()
  {
    if (self::$currentCalendarYear) {
      return;
    }
    self::$currentFiscalYear = self::$currentCalendarYear = date('Y');
    if (6 < date('m')) {
      // Fiscal year starts from July for NYC non-federal agencies
      self::$currentFiscalYear++;
    }
    // because of unknown reasons
    self::$currentCalendarYearId = self::year2yearId(self::$currentCalendarYear);
    self::$currentFiscalYearId = self::year2yearId(self::$currentFiscalYear);
  }

  /**
   * @param $year
   * @return int
   */
  public static function year2yearId($year)
  {
    return $year < 1900 ? $year : $year - 1899;
  }

  /**
   * @param $id
   * @return int
   */
  public static function yearId2Year($id)
  {
    return $id > 1900 ? $id : $id + 1899;
  }

  /**
   * @return mixed
   */
  public static function getCurrentFiscalYear($data_source = Datasource::CITYWIDE)
  {
    self::setCurrentYears();
    //For NYCHA, Fiscal Year is Calender Year
    $isNYCHA = ($data_source == Datasource::NYCHA || Datasource::isNYCHA()) ? true : false;
    if($isNYCHA) {
      return self::$currentCalendarYear;
    }else{
      return self::$currentFiscalYear;
    }
  }

  /**
   * @return mixed
   */
  public static function getCurrentFiscalYearId($data_source = Datasource::CITYWIDE)
  {
    self::setCurrentYears();
    //For NYCHA, Fiscal Year is Calender Year
    $isNYCHA = ($data_source == Datasource::NYCHA || Datasource::isNYCHA()) ? true : false;
    if($isNYCHA) {
      return self::$currentCalendarYearId;
    }else{
      return self::$currentFiscalYearId;
    }
  }

  /**
   * @return mixed
   */
  public static function getCurrentCalendarYear()
  {
    self::setCurrentYears();
    return self::$currentCalendarYear;
  }

  /**
   * @return mixed
   */
  public static function getCurrentCalendarYearId()
  {
    self::setCurrentYears();
    return self::$currentCalendarYearId;
  }

  /**
   * @return array
   */
  public static function getLast10fiscalYears()
  {
    $last = self::getCurrentFiscalYear();
    $results = [];
    for ($i = $last; $i > $last - 10; $i--) {
      $results[$i] = $i;
    }
    return $results;
  }

  /**
   * @return array
   */
  public static function getLast10FiscalYearOptions()
  {
    $last = self::getCurrentFiscalYear();
    $results = [];
    for ($year = $last; $year > $last - 10; $year--) {
      $results[] = [
        'year_id' => self::year2yearId($year),
        'year_value' => $year
      ];
    }
    return $results;
  }

  /**
   * @return array
   */
  public static function getLast10CalendarYearOptions()
  {
    $last = self::getCurrentCalendarYear();
    $results = [];
    for ($year = $last; $year > $last - 10; $year--) {
      $results[] = [
        'year_id' => self::year2yearId($year),
        'year_value' => $year
      ];
    }
    return $results;
  }

  /**
   * @param $monthId
   * @return mixed|null
   */
  static function getMonthDetails($monthId)
  {
    if (!isset($monthId)) {
      return NULL;
    }

    $monthDetails = _checkbook_project_querydataset('checkbook:month', array('month_id', 'month_value', 'month_name', 'month_short_name'), array('month_id' => $monthId));
    return $monthDetails;
  }

}
