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


//Hide the Date Filter
//on Spending Advanced Search page when 'Check Date' parameter is present in the URL &
//on Pending Contracts Advanced Search page
if((preg_match('/^spending\/search\/transactions/',$_GET['q']) && (RequestUtilities::get('chkdate') || !RequestUtilities::get('year')))
    || RequestUtilities::get('contstatus') == 'P' || preg_match('/^contract\/all\/transactions/',$_GET['q']) || preg_match('/^nycha_contracts\/all\/transactions/',$_GET['q'])
    || (preg_match('/^nycha_spending\/search\/transactions/',$_GET['q']) && (RequestUtilities::get('issue_date') || !RequestUtilities::get('year')))){
    return;
}

$url = request_uri();
$url_parts = parse_url($url);
$urlPath = $url_parts['path'];
$urlQuery = $url_parts['query'];

//Set $yearParamValue and $yearTypeParamValue from the current URL
if(RequestUtilities::get('year')){
    $yearParamValue = RequestUtilities::get('year');
}else if(RequestUtilities::get('calyear')){
    $yearParamValue = RequestUtilities::get('calyear');
}else if(preg_match("/contracts_pending_exp_landing/", $url) || preg_match("/contracts_pending_rev_landing/", $url)){
  //Set $year_id_value to current Fiscal Year ID for Pending Contracts
  $yearParamValue = CheckbookDateUtil::getCurrentFiscalYearId();
}
$yearTypeParamValue = (RequestUtilities::get('yeartype')) ? RequestUtilities::get('yeartype') : 'B';
//Pending Contracts do not have year filter applicable, so the Date Filter options are set to navigate to Active Expense Contracts landing page for the latest Fiscal Year
if(preg_match("/contracts_pending_exp_landing/", $url)){
  $urlPath = preg_replace("/contracts_pending_exp_landing/","contracts_landing/status/A", $urlPath);
}elseif(preg_match("/contracts_pending_rev_landing/", $url)){
  $urlPath = preg_replace("/contracts_pending_rev_landing/","contracts_landing/status/A", $urlPath);
}elseif(PageType::getCurrent() == PageType::TRENDS_PAGE){
  $trends = true;
  $urlPath = "/spending_landing/yeartype/B/year/";
}

$dataSource = Datasource::getCurrent();
$domain = CheckbookDomain::getCurrent();
$fiscalYears = CheckbookDateUtil::getFiscalYearOptionsRange($dataSource, $domain);
$calendarYears = CheckbookDateUtil::getCalendarYearOptionsRange($dataSource);
$fyDisplayData = array();
$cyDisplayData = array();
$yearListOptions = array();

//Fiscal Year options (We do not need to calculate Fiscal Years for NYCHA Payroll)
if(!($domain == Domain::$PAYROLL && $dataSource == Datasource::NYCHA)) {
  foreach ($fiscalYears as $key => $value) {
    $selectedFY = ($value['year_id'] == $yearParamValue && 'B' == $yearTypeParamValue) ? 'selected = yes' : "";

    //For TrendsNYCCHKBK-9474, set the default year value to current NYC fiscal year
    if ($trends) {
      //For Trends, append the year value for 'Spending' link
      $yearOptionUrl = $urlPath . $value['year_id'];
      if ($value['year_id'] == CheckbookDateUtil::getCurrentFiscalYearId()) {
        $selectedFY = 'selected = yes';
      }
    } else {
      $yearOptionUrl = preg_replace("/\/year\/[^\/]*/", "/year/" . $value['year_id'], $urlPath);
      //Year Option changes for bottom container
      if (isset($urlQuery)) {
        $bottomURLOption = preg_replace("/\/year\/[^\/]*/", "/year/" . $value['year_id'], $urlQuery);
        //For charts with months links, we need to persist the month param for the newly selected year
        if (preg_match('/month/', $urlQuery)) {
          $oldMonthId = RequestUtil::getRequestKeyValueFromURL("month", $urlQuery);
          if (isset($oldMonthId) && isset($value['year_id'])) {
            $newMonthId = _translateMonthIdByYear($oldMonthId, $value['year_id']);
            $bottomURLOption = preg_replace("/\/month\/[^\/]*/", "/month/" . $newMonthId, $bottomURLOption);
          }
        }
        //For NYCHA Spending Transaction pages handle 'issue date' parameter
        if ($dataSource == Datasource::NYCHA && (preg_match("/wt_issue_date/", $urlQuery))) {
          $oldIssueDate = RequestUtil::getRequestKeyValueFromURL("issue_date", $urlQuery);
          $oldIssueDateParts = explode("~", $oldIssueDate);
          $month = date("n", strtotime($oldIssueDateParts[0]));
          $newIssueDate = $value['year_value'] . "-" . $month . "-01~" . $value['year_value'] . "-" . $month . "-31";
          $bottomURLOption = preg_replace("/\/issue_date\/[^\/]*/", "issue_date/" . $newIssueDate, $bottomURLOption);
        }
        $yearOptionUrl = $yearOptionUrl . '?' . $bottomURLOption;
      }
    }

    //Set year type 'B' for all Fiscal year options
    $yearOptionUrl = preg_replace("/yeartype\/./", "yeartype/B", $yearOptionUrl);
    $displayText = ($dataSource == Datasource::NYCHA) ? 'FY ' . $value['year_value'] . ' (Jan 1, ' . $value['year_value'] . ' - Dec 31, ' . $value['year_value'] . ')' :
      'FY ' . $value['year_value'] . ' (Jul 1, ' . ($value['year_value'] - 1) . ' - Jun 30, ' . $value['year_value'] . ')';
    $fyDisplayData[] = array('display_text' => $displayText,
      'link' => $yearOptionUrl,
      'value' => $value['year_id'] . '~B',
      'selected' => $selectedFY
    );
  }
}

//Calendar Year options: Required only for Payroll domain (Citywide and NYCHA)
if($domain == Domain::$PAYROLL) {
  foreach ($calendarYears as $key => $value) {
    $selectedCY = ($value['year_id'] == $yearParamValue && 'C' == $yearTypeParamValue) ? 'selected = yes' : "";
    $yearOptionUrl = preg_replace("/\/year\/[^\/]*/","/year/" .  $value['year_id'], $urlPath);

    if (isset($urlQuery)) {
      $bottomURLOption = preg_replace("/\/year\/[^\/]*/","/year/" .  $value['year_id'], $urlQuery);
      //For charts with months links, we need to persist the month param for the newly selected year
      if (preg_match('/month/', $urlQuery)) {
        $oldMonthId = RequestUtil::getRequestKeyValueFromURL("month", $urlQuery);
        if (isset($oldMonthId) && isset($value['year_id'])) {
          $newMonthId = _translateMonthIdByYear($oldMonthId, $value['year_id']);
          $bottomURLOption = preg_replace("/\/month\/[^\/]*/","/month/" .  $newMonthId, $bottomURLOption);
        }
      }
      $yearOptionUrl = $yearOptionUrl . '?' . $bottomURLOption;
    }

    //Set year type 'C' for all calendar year options
    $yearOptionUrl = preg_replace("/yeartype\/./", "yeartype/C", $yearOptionUrl);
    $displayText = 'CY '.$value['year_value'].' (Jan 1, '.$value['year_value'].' - Dec 31, '.$value['year_value'].')';
    $cyDisplayData[] = array('display_text' => $displayText,
      'value' => $value['year_id'] . '~C',
      'link' => $yearOptionUrl,
      'selected' => $selectedCY
    );
  }
}

//Merge Fiscal Year and Calendar Year Options
$yearListOptions = array_merge($cyDisplayData, $fyDisplayData);


//HTML for Date Filter
$year_list = "<select id='year_list'>";
foreach($yearListOptions as $year){
    $year_list .= "<option ".$year['selected']." value=".$year['value']." link='" . $year['link'] . "'  >".$year['display_text']."</option>";
}
$year_list .= "</select>";

print "<span class=\"filter\" >Filter: </span>" . $year_list;
