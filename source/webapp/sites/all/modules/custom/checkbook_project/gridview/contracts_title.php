<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


$refURL =$_GET['refURL'];

if(RequestUtil::isExpenseContractPath($refURL) || RequestUtil::isRevenueContractPath($refURL)){
   $title = _get_contracts_breadcrumb_title_drilldown();
}
if(RequestUtil::isPendingExpenseContractPath($refURL) || RequestUtil::isPendingRevenueContractPath($refURL)){
   $title =  _get_pending_contracts_breadcrumb_title_drilldown();
}

$domain = 'Contracts';
