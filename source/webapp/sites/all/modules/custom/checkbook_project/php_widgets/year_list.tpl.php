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

//Pending Contracts do not have year filter applicable, so the Date Filter options are set to navigate to Active Expense Contracts landing page for the latest Fiscal Year
if(preg_match("/contracts_pending_exp_landing/",$q)){
  $q =preg_replace("/contracts_pending_exp_landing/","contracts_landing/status/A",$q );
}else if(preg_match("/contracts_pending_rev_landing/",$q)){
  $q =preg_replace("/contracts_pending_rev_landing/","contracts_landing/status/A",$q );
}

//$q is the new URL for the Date Filter options
$q = request_uri();
$url_parts = parse_url($q);
if(!isset($_REQUEST['expandBottomContURL']) && $url_parts['query']){
  $q = str_replace('?'.$url_parts['query'], "", $q);
}

//Set the default Domain to be 'Spending' for Trends
if(preg_match("/trends/",$q)){
  $q = "/spending_landing/yeartype/B/year/" ;
  $trends = true;
}

//Get Year list from DB
$filter_years = CheckbookDateUtil::getCurrentYears();

//Get $yearParamValue and $yearTypeParamValue from the current URL
if(RequestUtilities::get('year')){
    $yearParamValue = RequestUtilities::get('year');
}else if(RequestUtilities::get('calyear')){
    $yearParamValue = RequestUtilities::get('calyear');
}else if(preg_match("/contracts_pending_exp_landing/",$_GET['q']) || preg_match("/contracts_pending_rev_landing/",$_GET['q'])){
  //Set $year_id_value to current Fiscal Year ID for Pending Contracts
  $yearParamValue = CheckbookDateUtil::getCurrentFiscalYearId();
}
$yearTypeParamValue = (RequestUtilities::get('yeartype')) ? RequestUtilities::get('yeartype') : 'B';


$bottomURL = $_REQUEST['expandBottomContURL'];

$dataSource = Datasource::getCurrent();
$domain = CheckbookDomain::getCurrent();
$fiscalYears = CheckbookDateUtil::getFiscalYearOptionsRange($dataSource, $domain);
$calendarYears = CheckbookDateUtil::getCalendarYearOptionsRange($dataSource);
$fyDisplayData = array();
$cyDisplayData = array();
$yearListOptions = array();

//Fiscal Year options - Required for all domains except NYCHA Payroll
if(CheckbookDomain::getCurrent() != Domain::$PAYROLL) {
  foreach ($fiscalYears as $key => $value) {
    switch ($dataSource) {
      case Datasource::NYCHA:
        //For NYCHA Fiscal Year is same as calendar year
        $displayText = 'FY ' . $value['year_value'] . ' (Jan 1, ' . $value['year_value'] . ' - Dec 31, ' . $value['year_value'] . ')';
        //Handling Spending chart links issue date
        if (isset($bottomURL) && (preg_match("/wt_issue_date/", $q))) {
          $oldIssueDate = RequestUtil::getRequestKeyValueFromURL("issue_date", $bottomURL);
          $issueDate = explode("~", $oldIssueDate);
          $month = date("n", strtotime($issueDate[0]));
          $newIssueDate = $value['year_value'] . "-" . $month . "-01~" . $value['year_value'] . "-" . $month . "-31";
          $q = preg_replace("/issue_date\/" . $oldIssueDate . "/", "issue_date/" . $newIssueDate, $q);
        }
        break;
      default:
        $displayText = 'FY ' . $value['year_value'] . ' (Jul 1, ' . ($value['year_value'] - 1) . ' - Jun 30, ' . $value['year_value'] . ')';
        //For the charts with the months links, need to persist the month param for the newly selected year
        if (isset($bottomURL) && preg_match('/month/', $bottomURL)) {
          $oldMonthId = RequestUtil::getRequestKeyValueFromURL("month", $bottomURL);
          if (isset($oldMonthId) && isset($value['year_id'])) {
            $newMonthId = _translateMonthIdByYear($oldMonthId, $value['year_id']);
            $q = preg_replace('/\/month\/' . $oldMonthId . '/', '/month/' . $newMonthId, $q);
          }
        }
    }

    //Selected Fiscal Year
    $selectedFY = ($value['year_id'] == $yearParamValue && 'B' == $yearTypeParamValue) ? 'selected = yes' : "";

    //For TrendsNYCCHKBK-9474, set the default year value to current NYC fiscal year
    if ($trends) {
      if ($value['year_id'] == CheckbookDateUtil::getCurrentFiscalYearId()) {
        $selectedFY = 'selected = yes';
      }
    }

    //Update current URL with year and year type options. (For Trends, append the year value for 'Spending' link)
    $yearOptionUrl = "";
    if ($trends) {
      $yearOptionUrl = $q . $value['year_id'];
    } else {
      $yearOptionUrl = preg_replace("/year\/" . $yearParamValue . "/", "year/" . $value['year_id'], $q);
    }

    //Set year type 'B' for all Fiscal year options
    $yearOptionUrl = preg_replace("/yeartype\/./", "yeartype/B", $yearOptionUrl);

    $fyDisplayData[] = array('display_text' => $displayText,
      'link' => $yearOptionUrl,
      'value' => $value['year_id'] . '~B',
      'selected' => $selectedFY,);
  }
}

//Calendar Year options: Required only for Payroll domain
if(CheckbookDomain::getCurrent() == Domain::$PAYROLL) {
  foreach ($calendarYears as $key => $value) {
    //For the charts with the months links, need to persist the month param for the newly selected year
    if (isset($bottomURL) && preg_match('/month/', $bottomURL)) {
      $oldMonthId = RequestUtil::getRequestKeyValueFromURL("month", $bottomURL);
      if (isset($oldMonthId) && isset($value['year_id'])) {
        $newMonthId = _translateMonthIdByYear($oldMonthId, $value['year_id'], "C");
        $q = preg_replace('/\/month\/' . $oldMonthId . '/', '/month/' . $newMonthId, $q);
      }
    }

    //Selected Calendar
    $selectedCY = ($value['year_id'] == $yearParamValue && 'C' == $yearTypeParamValue) ? 'selected = yes' : "";

    //Update current URL with year and year type options. (For Trends, append the year value for 'Spending' link)
    $yearOptionUrl = "";
    $yearOptionUrl = preg_replace("/year\/" . $yearParamValue . "/", "year/" . $value['year_id'], $q);

    //Set year type 'C' for all calendar year options
    $yearOptionUrl = preg_replace("/yeartype\/./", "yeartype/C", $q);
    $displayText = 'CY '.$value['year_value'].' (Jan 1, '.$value['year_value'].' - Dec 31, '.$value['year_value'].')';
    $cyDisplayData[] = array('display_text' => $displayText,
      'value' => $value['year_id'] . '~C',
      'link' => $yearOptionUrl,
      'selected' => $selectedCY
    );
  }
}

//Combine Calendar Year and Fiscal Year options
$yearListOptions = array_merge($cyDisplayData, $fyDisplayData);

//HTML for Date Filter
$year_list = "<select id='year_list'>";
foreach($yearListOptions as $year){
    $year_list .= "<option ".$year['selected']." value=".$year['value']." link='" . $year['link'] . "'  >".$year['display_text']."</option>";
}
$year_list .= "</select>";

print "<span class=\"filter\" >Filter: </span>" . $year_list;
