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
$smnid = RequestUtilities::get('smnid');
$dashboard= RequestUtilities::get('dashboard');
$contactStatus = RequestUtilities::get('contstatus');
$bottomNavigation = "";
if ($contactStatus == 'A') {
    $contactStatusLabel = 'Active';
}
if ($contactStatus == 'R') {
  $contactStatusLabel = 'Registered';
}
if($dashboard == 'ss'){
    if($contactStatus == 'A')
        $bottomNavigation = "Total Active Sub Vendor Contracts";
    else
        $bottomNavigation = "New Sub Vendor Contracts by Fiscal Year";
}
if($dashboard == 'ms' || $dashboard == 'sp'){
    if($contactStatus == 'A')
     $bottomNavigation = "Total Active M/WBE Sub Vendor Contracts";
    else
     $bottomNavigation = "New M/WBE Sub Vendor Contracts by Fiscal Year";
}
$contactCategory = RequestUtilities::get('contcat');
$contactCategoryLabel = 'Expense';
if ($contactCategory == 'revenue') {
  $contactCategoryLabel = 'Revenue';
}
if ($contactCategory == 'all') {
    $contactCategoryLabel = '';
}
$current_url = explode('/', request_uri());
if($current_url[1] == 'contract' && ($current_url[2] == 'search' || $current_url[2] == 'all')&& $current_url[3] == 'transactions'){
    $summaryTitle = "";
}else if(_checkbook_check_is_mwbe_page() || $dashboard){
    $summaryTitle = RequestUtil::getDashboardTitle()." ";
}
//Handle Sub Vendor widget to not repeat 'Sub Vendor' in title in certain dashboards
$suppress_widget_title = ($dashboard == "ss" && $smnid == 720) || //Sub Vendors
                         ($dashboard == "sp" && $smnid == 720); //Sub Vendors (M/WBE)
if(!$suppress_widget_title) {
    $summaryTitle .= NodeSummaryUtil::getInitNodeSummaryTitle();
}

$summaryTitle = $summaryTitle != '' ? $summaryTitle : '';
global $checkbook_breadcrumb_title;

if($dashboard == 'ss' || $dashboard == 'sp'){
    switch($smnid){
        case '720':
        case '721':
            print "<h2 class='contract-title' class='title'>{$bottomNavigation} Transactions</h2>";
            $checkbook_breadcrumb_title =  "$bottomNavigation Transactions";
            break;
        case '722':
            print "<h2 class='contract-title' class='title'>Amount Modifications by {$bottomNavigation} Transactions</h2>";
            $checkbook_breadcrumb_title =  "Amount Modifications by $bottomNavigation Transactions";
            break;
        case '725':
            print "<h2 class='contract-title' class='title'>Prime Vendors with {$bottomNavigation} Transactions</h2>";
            $checkbook_breadcrumb_title =  "Prime Vendors with $bottomNavigation Transactions";
            break;
        case '726':
            print "<h2 class='contract-title' class='title'>Award Methods by {$bottomNavigation} Transactions</h2>";
            $checkbook_breadcrumb_title =  "Award Methods by $bottomNavigation Transactions";
            break;
        case '727':
            print "<h2 class='contract-title' class='title'>Agencies by {$bottomNavigation} Transactions</h2>";
            $checkbook_breadcrumb_title =  "Agencies by $bottomNavigation Transactions";
            break;
        case '728':
            print "<h2 class='contract-title' class='title'>Contracts by Industries by {$bottomNavigation} Transactions</h2>";
            $checkbook_breadcrumb_title =  "$bottomNavigation Transactions";
            break;
        case '729':
            print "<h2 class='contract-title' class='title'>Contracts by Size by {$bottomNavigation} Transactions</h2>";
            $checkbook_breadcrumb_title =  "Contracts by Size by $bottomNavigation Transactions";
            break;
    }
}elseif($dashboard == 'ms'){
    switch($smnid){
        case '781':
        case '784':
            print "<h2 class='contract-title' class='title'>{$bottomNavigation} Transactions</h2>";
            $checkbook_breadcrumb_title =  "$bottomNavigation Transactions";
            break;
        case '782':
            print "<h2 class='contract-title' class='title'>Amount Modifications by {$bottomNavigation} Transactions</h2>";
            $checkbook_breadcrumb_title =  "Amount Modifications by $bottomNavigation Transactions";
            break;
        case '783':
            print "<h2 class='contract-title' class='title'>Prime Vendors with {$bottomNavigation} Transactions</h2>";
            $checkbook_breadcrumb_title =  "Prime Vendors with $bottomNavigation Transactions";
            break;
        case '785':
            print "<h2 class='contract-title' class='title'>Award Methods by {$bottomNavigation} Transactions</h2>";
            $checkbook_breadcrumb_title =  "Award Methods by $bottomNavigation Transactions";
            break;
        case '786':
            print "<h2 class='contract-title' class='title'>Agencies by {$bottomNavigation} Transactions</h2>";
            $checkbook_breadcrumb_title =  "Agencies by $bottomNavigation Transactions";
            break;
        case '787':
            print "<h2 class='contract-title' class='title'>Contracts by Industries by {$bottomNavigation} Transactions</h2>";
            $checkbook_breadcrumb_title =  "$bottomNavigation Transactions";
            break;
        case '788':
            print "<h2 class='contract-title' class='title'>Contracts by Size by {$bottomNavigation} Transactions</h2>";
            $checkbook_breadcrumb_title =  "Contracts by Size by $bottomNavigation Transactions";
            break;
    }
}else{
    print "<h2 class='contract-title' class='title'>{$summaryTitle} {$contactStatusLabel} {$contactCategoryLabel} Contracts Transactions</h2>";
    $checkbook_breadcrumb_title =  "$summaryTitle $contactStatusLabel $contactCategoryLabel Contracts Transactions";
}





