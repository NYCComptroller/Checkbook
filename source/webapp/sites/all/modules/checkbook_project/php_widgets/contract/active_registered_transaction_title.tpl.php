<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
$contactStatus = _getRequestParamValue('contstatus');
$contactStatusLabel = 'Active';
if ($contactStatus == 'R') {
  $contactStatusLabel = 'Registered';
}
$contactCategory = _getRequestParamValue('contcat');
$contactCategoryLabel = 'Expense';
if ($contactCategory == 'revenue') {
  $contactCategoryLabel = 'Revenue';
}
$summaryTitle = NodeSummaryUtil::getInitNodeSummaryTitle();
print "<h2 class='contract-title' class='title'>{$summaryTitle}<br/>{$contactStatusLabel} {$contactCategoryLabel} Contracts Transactions</h2>";

global $checkbook_breadcrumb_title;
$checkbook_breadcrumb_title =  "$summaryTitle $contactStatusLabel $contactCategoryLabel Contracts Transactions";  
