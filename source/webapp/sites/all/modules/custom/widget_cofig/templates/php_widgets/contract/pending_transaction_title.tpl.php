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

$contactCategory = RequestUtilities::get('contcat');
$contactCategoryLabel = 'Expense';
if ($contactCategory == 'revenue') {
  $contactCategoryLabel = 'Revenue';
}
if ($contactCategory == 'all') {
    $contactCategoryLabel = '';
}

$current_url = explode('/',request_uri());
if($current_url[1] == 'contract' && ($current_url[2] == 'search' || $current_url[2] == 'all')&& $current_url[3] == 'transactions'){
    $summaryTitle = "";
}else if(_checkbook_check_is_mwbe_page()){
    $summaryTitle = MappingUtil::getCurrenEthnicityName()." ";
}
$summaryTitle .= NodeSummaryUtil::getInitNodeSummaryTitle();
print "<h2 class='contract-title' class='title'>{$summaryTitle} Pending {$contactCategoryLabel} Contracts Transactions</h2>";
global $checkbook_breadcrumb_title;
$checkbook_breadcrumb_title =  "$summaryTitle Pending $contactCategoryLabel Contracts Transactions";
