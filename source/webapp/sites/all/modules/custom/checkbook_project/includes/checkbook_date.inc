<?php

/**
 * This function returns current NYC fiscal year ID
 * @return string
 */
function _getFiscalYearID(){
  return CheckbookDateUtil::getCurrentFiscalYearId();
}

/**
 * returns NYC year id for a giver year ...
 * @param string $year_value
 * @return string
 */
function _getYearIDFromValue($year_value){
  return CheckbookDateUtil::year2yearId($year_value);
}

/**
 * return year value for a give year id ...
 * @param string $year_id
 * @return string
 */
function _getYearValueFromID($year_id){
  return CheckbookDateUtil::yearId2Year($year_id);
}

/**
 * return full year text value for a give year id ...
 * @return string
 */
function _getFullYearString(){
  return CheckbookDateUtil::getFullYearString();
}

/**
 * returns NYC month id for a different year id
 * @param $month_id
 * @param $year_id
 * @param null $year_type
 * @return mixed
 */
function _translateMonthIdByYear($month_id, $year_id, $year_type = null){
  $month_value = _getMonthValueFromId($month_id);
  $month_id = _getMonthIDFromValue($month_value, $year_id, $year_type);
  return $month_id;
}

/**
 * returns NYC month id for a given month value and year id
 * @param $month_value
 * @param $year_id
 * @param $year_type
 * @return mixed
 */
function _getMonthIDFromValue($month_value, $year_id, $year_type = null){
  $month_id = null;
  if ($year_type == "C") {
    $monthIDs = _checkbook_project_querydataset('checkbook:month', array('month_id'), array('month_value' => $month_value, 'year_id' => $year_id));
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
function _getMonthValueFromId($month_id){
  $monthValues = _checkbook_project_querydataset('checkbook:month', array('month_value'), array('month_id' => $month_id));
  $month_value = $monthValues[0]['month_value'];
  return $month_value;
}

/**
 * @param $haystack
 * @param $needle
 * @return bool
 */
function startsWith($haystack, $needle) {
  $length = strlen($needle);
  return (substr($haystack, 0, $length) === $needle);
}
