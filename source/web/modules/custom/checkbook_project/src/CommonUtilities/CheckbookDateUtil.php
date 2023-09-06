<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\checkbook_project\CommonUtilities;
use Drupal\checkbook_infrastructure_layer\Constants\Common\CheckbookDomain;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_log\LogHelper;

class CheckbookDateUtil{
  /**
   * @var
   */
  private static $currentFiscalYear;
  /**
   * @var
   */
  private static $currentFiscalYearId;
  /**
   * @var
   */
  private static $currentCalendarYear;
  /**
   * @var
   */
  private static $currentCalendarYearId;
  /**
   * @var
   */
  private static $startingFiscalYear;
  /**
   * @var
   */
  private static $startingFiscalYearId;
  /**
   * @var
   */
  private static $startingCalendarYear;
  /**
   * @var
   */
  private static $startingCalendarYearId;

  const MONTH_DATASET = 'checkbook:month';

  /**
   * Sets up current and beginning year values if Drupal variables are not set
   */
  private static function setCurrentYears(){
    if (self::$currentCalendarYear) {
      return;
    }
    self::$currentFiscalYear = self::$currentCalendarYear = date('Y');

    // Calendar year is used for Payroll Domain (Citywide and NYCHA)
    self::$currentCalendarYearId = self::year2yearId(self::$currentCalendarYear);

    // Fiscal year starts from July for NYC non-federal agencies
    if (6 < date('m')) {
      self::$currentFiscalYear++;
    }
    self::$currentFiscalYearId = self::year2yearId(self::$currentFiscalYear);

    self::$startingFiscalYear = self::$currentFiscalYear-11;
    self::$startingFiscalYearId = self::year2yearId(self::$startingFiscalYear);

    self::$startingCalendarYear = self::$currentCalendarYear-11;
    self::$startingCalendarYearId = self::year2yearId(self::$startingCalendarYear);
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
   * @param $data_source
   *
   * SET THESE VARS ON SERVER (AT DATA-SOURCE LEVEL):
   * drush sset default_checkbook_fy 2021
   * drush sset default_checkbook_oge_fy 2021
   * drush sset default_checkbook_nycha_fy 2021
   *
   * @return string
   */
  public static function getMaxDatasourceFiscalYear(string $data_source){
    self::setCurrentYears();
    $key = 'current_' . $data_source . '_fy';

    if ($year = _checkbook_dmemcache_get($key)) {
      LogHelper::log_info("Get cached year in CheckbookDateUtil::getMaxDatasourceFiscalYear with CacheKey: $key ");
      return $year;
    }
    $year = \Drupal::state()->get($key, self::$currentFiscalYear);
    LogHelper::log_info("Set cached year in CheckbookDateUtil::getMaxDatasourceFiscalYear with CacheKey: $key value: $year");
    _checkbook_dmemcache_set($key, $year);
    return $year ?? null;
  }

  /**
   * @param $data_source
   *
   * SET THESE VARS ON SERVER (AT DATA-SOURCE LEVEL):
   * drush sset current_checkbook_fy 2022
   * drush sset current_checkbook_oge_fy 2022
   * drush sset current_checkbook_nycha_fy 2021
   *
   * @return string
   */
  public static function getCurrentDatasourceFiscalYear(string $data_source){
    self::setCurrentYears();
    $key = 'default_' . $data_source . '_fy';

    if ($year = _checkbook_dmemcache_get($key)) {
      LogHelper::log_info("Get cached year in CheckbookDateUtil::getCurrentDatasourceFiscalYear with CacheKey: $key ");
      return $year;
    }
    $year = \Drupal::state()->get($key, self::$currentFiscalYear);

    LogHelper::log_info("Set cached year in CheckbookDateUtil::getCurrentDatasourceFiscalYear with CacheKey: $key  value: $year");
    _checkbook_dmemcache_set($key, $year);
    return $year ?? null;
  }

  /**
   * @param $data_source
   * @param $domain
   * SET THESE VARS ON SERVER (AT DATA-SOURCE LEVEL):
   * drush sset min_checkbook_fy 2011
   * drush sset min_checkbook_oge_fy 2011
   * drush sset min_checkbook_nycha_fy 2010
   * drush sset min_checkbook_nycha_budget_fy 2018
   *
   * @return string
   */
  public static function getCurrentDatasourceStartingYear(string $data_source, $domain = NULL){
    self::setCurrentYears();
    if($domain == CheckbookDomain::$NYCHA_BUDGET || $domain == CheckbookDomain::$NYCHA_REVENUE ||
      ($data_source == Datasource::NYCHA && ($domain == CheckbookDomain::$REVENUE || $domain == CheckbookDomain::$BUDGET))){
      $key = 'min_' . $data_source . '_budget_fy';
    }else{
      $key = 'min_' . $data_source . '_fy';
    }

    if ($year = _checkbook_dmemcache_get($key)) {
      LogHelper::log_info("Get cached year in CheckbookDateUtil::getCurrentDatasourceStartingYear with CacheKey: $key ");
      return $year;
    }
    $year = \Drupal::state()->get($key, self::$startingFiscalYear);

    LogHelper::log_info("Set cached year in CheckbookDateUtil::getCurrentDatasourceStartingYear with CacheKey: $key  value: $year");
    _checkbook_dmemcache_set($key, $year);
    return $year;
  }

  /**
   * @param $data_source
   *
   * SET THESE VARS ON SERVER (AT DATA-SOURCE LEVEL):
   * drush sset min_cy 2010
   * @return string
   */
  public static function getStartingCalendarYear(){
    self::setCurrentYears();
    $key = 'min_cy';

    if ($year = _checkbook_dmemcache_get($key)) {
      LogHelper::log_info("Get cached year in CheckbookDateUtil::getStartingCalendarYear with CacheKey: $key ");
      return $year;
    }
    $year = \Drupal::state()->get($key, self::$startingCalendarYear);

    LogHelper::log_info("Set cached year in CheckbookDateUtil::getStartingCalendarYear with CacheKey: $key  value: $year");
    _checkbook_dmemcache_set($key, $year);
    return $year;
  }

  /**
   * @param string $data_source
   * @return mixed
   */
  public static function getCurrentFiscalYear($data_source = Datasource::CITYWIDE){
    self::setCurrentYears();
    $data_source = ($data_source == Datasource::NYCHA || Datasource::isNYCHA()) ? Datasource::NYCHA : $data_source;
    $year_value = self::getCurrentDatasourceFiscalYear($data_source);
    return $year_value ?? null;
  }

  /**
   * @param string $data_source
   * @return string
   */
  public static function getCurrentFiscalYearId($data_source = Datasource::CITYWIDE){
    $year_value = self::year2yearId(self::getCurrentFiscalYear($data_source));
    return $year_value ?? null;
  }


  /**
   * @param string $data_source
   * @param string $domain
   * @return mixed
   */
  public static function getStartingFiscalYear($data_source = Datasource::CITYWIDE, $domain = NULL){
    self::setCurrentYears();
    $data_source = ($data_source == Datasource::NYCHA || Datasource::isNYCHA()) ? Datasource::NYCHA : $data_source;
    return self::getCurrentDatasourceStartingYear($data_source, $domain);
  }

  /**
   * @param string $data_source
   * @return mixed
   */
  public static function getStartingFiscalYearId($data_source = Datasource::CITYWIDE){
    return self::year2yearId(self::getStartingFiscalYear($data_source));
  }

  /**
   * @return mixed
   * drush sset current_calendar_year_cy 2020
   */
  public static function getCurrentCalendarYear(){
    self::setCurrentYears();
    $key = 'current_calendar_year_cy';

    if ($year = _checkbook_dmemcache_get($key)) {
      LogHelper::log_info("Get cached year in CheckbookDateUtil::getCurrentCalendarYear with CacheKey: $key ");
      return $year;
    }
    $year = \Drupal::state()->get($key, self::$currentCalendarYear);

    LogHelper::log_info("Set cached year in CheckbookDateUtil::getCurrentCalendarYear with CacheKey: $key  value: $year");
    _checkbook_dmemcache_set($key, $year);
    return $year;
  }

  /**
   * @return mixed
   */
  public static function getCurrentCalendarYearId(){
    self::setCurrentYears();
    return self::year2yearId(self::getCurrentCalendarYear());
  }


  /**
   * @return mixed
   */
  public static function getStartingCalendarYearId(){
    self::setCurrentYears();
    return self::year2yearId(self::getStartingCalendarYear());
  }

  /**
   * @return array
   * @param $data_source
   */
  public static function getCurrentYears($data_source = Datasource::CITYWIDE){
    $maxYear = self::getMaxDatasourceFiscalYear($data_source);
    return [
      'year_value' => $maxYear,
      'year_id' => self::year2yearId($maxYear),
      'cal_year_value' => self::getCurrentCalendarYear(),
      'cal_year_id' => self::getCurrentCalendarYearId()
    ];
  }

  /**
   * @param $data_source
   * @param $domain
   * @return array
   */
  public static function getFiscalYearsRange($data_source = Datasource::CITYWIDE, $domain = null){
    $last = self::getMaxDatasourceFiscalYear($data_source);
    $first = self::getStartingFiscalYear($data_source, $domain);
    $results = [];
    for ($i = $last; $i >= $first; $i--) {
      $results[$i] = $i;
    }
    return $results;
  }

  /**
   * @param string $data_source
   * @param string $domain
   * @return array
   */
  public static function getFiscalYearOptionsRange($data_source, $domain = NULL){
    $last = self::getMaxDatasourceFiscalYear($data_source);
    $first = self::getStartingFiscalYear($data_source, $domain);

    $results = [];
    for ($year = $last; $year >= $first; $year--) {
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
  public static function getCalendarYearOptionsRange($data_source){
    $last = self::getCurrentCalendarYear();
    $first = self::getStartingCalendarYear();

    $results = [];
    for ($year = $last; $year >= $first; $year--) {
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
  public static function getMonthDetails($monthId){
    if (!isset($monthId)) {
      return NULL;
    }
    $monthDetails = _checkbook_project_querydataset(self::MONTH_DATASET, array('month_id', 'month_value', 'month_name', 'month_short_name'), array('month_id' => $monthId));
    return $monthDetails;
  }

  /**
   * return full year text value for a give year id
   * @param $yearId
   * @param $yearType
   * @param null $datasource
   * @return string
   */
  public static function getFullYearString($yearId = null, $yearType=null, $datasource = null){
    $yearId = $yearId ?? RequestUtilities::get('year');
    $yearId = empty(((empty($yearId))) ? RequestUtilities::get('calyear') : $yearId) ? self::getCurrentFiscalYearId() :  $yearId;
    $yearType = $yearType ?? RequestUtilities::get('yeartype');
    $yearType = (empty($yearType)) ? 'B' : $yearType;
    $yearValue = self::_getYearValueFromID($yearId);
    $yearString = ($yearType == 'B') ? "FY $yearValue" : "CY $yearValue";
    if (RequestUtilities::get('datasource') == Datasource::NYCHA || $datasource == Datasource::NYCHA) {
      $yearString .= "(January 1, " . ($yearValue) . " - Decemeber 31, $yearValue)";
    } else {
      $yearString .= ($yearType == 'B') ? " (July 1, " . ($yearValue - 1) . " - June 30, $yearValue)" : " (January 1, $yearValue - December 31, $yearValue)";
    }
    return $yearString;
  }

  /** CITYWIDE Top Navigation
   * Returns Year ID for Spending, Contracts, Budget and Revenue domains navigation URLs from Top Navigation
   * @return integer $fiscalYearId
   */
  public static function getFiscalYearIdForTopNavigation()
  {
    $year = RequestUtilities::get("year");
    if (!$year) {
      $year = self::getCurrentFiscalYearId();
    }

    //For CY 2010 Payroll selection, other domains should be navigated to FY 2011
    $fiscalYearId = ($year == self::getStartingCalendarYearId() && strtoupper(RequestUtilities::get("yeartype")) == 'C') ? self::getStartingFiscalYearId() : $year;
    return $fiscalYearId;
  }

  /** CITYWIDE Top Navigation
   * Returns Year ID for Payroll domain navigation URLs from Top Navigation
   * @return integer $calYearId
   */
  public static function getCalYearIdForTopNavigation()
  {
    $year = null;
    if (RequestUtilities::get("year") != NULL) {
      $year = RequestUtilities::get("year");
    }
    $currentCalYear = self::getCurrentCalendarYearId();
    if (is_null($year) || $year > $currentCalYear) {
      $year = $currentCalYear;
    }
    return $year;
  }

  /**
   * This function returns current NYC fiscal year ID
   * @return string
   */
  public static function _getFiscalYearID(){
    return self::getCurrentFiscalYearId();
  }

  /**
   * returns NYC year id for a giver year ...
   * @param string $year_value
   * @return string
   */
  public static function _getYearIDFromValue($year_value){
    return self::year2yearId($year_value);
  }

  /**
   * return year value for a give year id ...
   * @param string $year_id
   * @return string
   */
  public static function _getYearValueFromID($year_id){
    return self::yearId2Year($year_id);
  }

  /**
   * return full year text value for a give year id ...
   * @return string
   */
  public static function _getFullYearString(){
    return self::getFullYearString();
  }

  /**
   * returns NYC month id for a different year id
   * @param $month_id
   * @param $year_id
   * @param null $year_type
   * @return mixed
   */
  public static function _translateMonthIdByYear($month_id, $year_id, $year_type = null){
    $month_value = self::_getMonthValueFromId($month_id);
    $month_id = self::_getMonthIDFromValue($month_value, $year_id, $year_type);
    return $month_id;
  }

  /**
   * returns NYC month id for a given month value and year id
   * @param $month_value
   * @param $year_id
   * @param $year_type
   * @return mixed
   */
  public static function _getMonthIDFromValue($month_value, $year_id, $year_type = null){
    $month_id = null;
    if ($year_type == "C") {
      $monthIDs = _checkbook_project_querydataset(self::MONTH_DATASET, array('month_id'), array('month_value' => $month_value, 'year_id' => $year_id));
      $month_id = $monthIDs[0]['month_id'];
    } else {
      $query =
        "SELECT DISTINCT calendar_month_id FROM ref_date date
            JOIN ref_year year ON year.year_id = date.nyc_year_id
            JOIN ref_month month ON month.month_id = date.calendar_month_id
            WHERE year.year_id = " . $year_id . " AND month.month_value = " . $month_value;
      $results = _checkbook_project_execute_sql($query, "main");
      $month_id = $results[0]['calendar_month_id'];

    }
    return $month_id;
  }

  /**
   * returns NYC month value for a given month id
   * @param $month_id
   * @return mixed
   */
  public static function _getMonthValueFromId($month_id){
    $monthValues = _checkbook_project_querydataset(self::MONTH_DATASET, array('month_value'), array('month_id' => $month_id));
    $month_value = $monthValues[0]['month_value'];
    return $month_value;
  }

  /**
   * @param $haystack
   * @param $needle
   * @return bool
   */
  public static function startsWith($haystack, $needle) {
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
  }
}
