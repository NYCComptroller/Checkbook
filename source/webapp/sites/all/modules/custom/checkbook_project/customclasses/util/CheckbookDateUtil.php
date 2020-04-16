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


class CheckbookDateUtil{
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
  private static function setCurrentYears(){
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
  public static function year2yearId($year){
    return $year < 1900 ? $year : $year - 1899;
  }

  /**
   * @param $id
   * @return int
   */
  public static function yearId2Year($id){
    return $id > 1900 ? $id : $id + 1899;
  }

  /**
   * @param string $data_source
   * @return mixed
   */
  public static function getCurrentFiscalYear($data_source = Datasource::CITYWIDE){
    self::setCurrentYears();
    $isNYCHA = (bool)($data_source == Datasource::NYCHA || Datasource::isNYCHA());
    //For NYCHA, Fiscal Year is Calender Year
    if ($isNYCHA) {
      return self::getCurrentDatasourceFiscalYear(Datasource::NYCHA);
    } else {
      return self::$currentFiscalYear;
    }
  }

  /**
   * @param string $data_source
   * @return string
   */
  public static function getCurrentFiscalYearId($data_source = Datasource::CITYWIDE){
    return self::year2yearId(self::getCurrentFiscalYear($data_source));
  }


  /**
   * @param $data_source
   *
   * SET THESE VARS ON SERVER:
   * drush vset current_checkbook_fy 2020
   * drush vset current_checkbook_oge_fy 2020
   * drush vset current_checkbook_nycha_fy 2019
   *
   * @return string
   */
  public static function getCurrentDatasourceFiscalYear(string $data_source){
    self::setCurrentYears();
    $key = 'current_' . $data_source . '_fy';
    if ($year = _checkbook_dmemcache_get($key)) {
      return $year;
    }
    $year = variable_get($key, FALSE);
    if (FALSE === $year) {
      LogHelper::log_warn('Drush variable ' . $key . ' not found!');
      $year = self::$currentFiscalYear;
    }
    _checkbook_dmemcache_set($key, $year);
    return $year;
  }

  /**
   * @param string $data_source
   * @return string
   */
  public static function getCurrentDatasourceFiscalYearId(string $data_source){
    return self::year2yearId(self::getCurrentDatasourceFiscalYear($data_source));
  }

  /**
   * @return mixed
   */
  public static function getCurrentCalendarYear(){
    self::setCurrentYears();
    return self::$currentCalendarYear;
  }

  /**
   * @return mixed
   */
  public static function getCurrentCalendarYearId(){
    self::setCurrentYears();
    return self::$currentCalendarYearId;
  }

  /**
   * @return array
   */
  public static function getLast10fiscalYears(){
    $last = self::getCurrentFiscalYear();
    $results = [];
    for ($i = $last; $i > $last - 10; $i--) {
      $results[$i] = $i;
    }
    return $results;
  }

  /**
   * Year List for NYCHA Budget
   * Returns past 10 years list from current year which are greater than 2017
   * @return array
   */
  public static function getNychaBudgetFiscalYears(){
    $last = self::getCurrentFiscalYear(Datasource::NYCHA);
    $results = [];
    for ($i = $last; $i > $last - 10; $i--) {
      if($i > 2017) {
        $results[$i] = $i;
      }
    }
    return $results;
  }

  /**
   * @param string $data_source
   * @return array
   */
  public static function getLast10FiscalYearOptions($data_source){
    // For NYCHA Fiscal Year is Calendar Year
    $last = self::getCurrentDatasourceFiscalYear($data_source);
    $yearCount = 10;
    $isNYCHA = (bool)($data_source == Datasource::NYCHA || Datasource::isNYCHA());
    if ($isNYCHA){ $yearCount =11;}
    $results = [];
    for ($year = $last; $year > $last - $yearCount; $year--) {
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
  public static function getLast10CalendarYearOptions($data_source){
    $last = self::getCurrentCalendarYear();
    $yearCount = 10;
    $isNYCHA = (bool)($data_source == Datasource::NYCHA || Datasource::isNYCHA());
    if ($isNYCHA){ $yearCount =11;}
    $results = [];
    for ($year = $last; $year > $last - $yearCount; $year--) {
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
  static function getMonthDetails($monthId){
    if (!isset($monthId)) {
      return NULL;
    }
    $monthDetails = _checkbook_project_querydataset('checkbook:month', array('month_id', 'month_value', 'month_name', 'month_short_name'), array('month_id' => $monthId));
    return $monthDetails;
  }

  /**
   * return full year text value for a give year id ...
   * @return string
   */
  public static function getFullYearString(){
    $yearId = RequestUtilities::get('year');
    $yearId = empty(((empty($yearId))) ? RequestUtilities::get('calyear') : $yearId) ? self::getCurrentFiscalYearId() :  $yearId;
    $yearType = RequestUtilities::get('yeartype');
    $yearType = (empty($yearType)) ? 'B' : $yearType;
    $yearValue = _getYearValueFromID($yearId);
    $yearString = ($yearType == 'B') ? "FY $yearValue" : "CY $yearValue";
    if (RequestUtilities::get('datasource') == Datasource::NYCHA) {
      $yearString .= "(January 1, " . ($yearValue) . " - Decemeber 31, $yearValue)";
    } else {
      $yearString .= ($yearType == 'B') ? " (July 1, " . ($yearValue - 1) . " - June 30, $yearValue)" : " (January 1, {$yearValue} - December 31, $yearValue)";
    }
    return $yearString;
  }

}
