<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/

$title = 'New York City';

$year = _getYearValueFromID(_getRequestParamValue('year'));
$revcat = _getRequestParamValue('revcat');
$fundsrccode = _getRequestParamValue('fundsrccode');
$agency = _getRequestParamValue('agency');

if(!empty($revcat)){
  $title =  _checkbook_project_get_name_for_argument('revenue_category_id', $revcat);
}else if(!empty($fundsrccode)){
  $title =  _checkbook_project_get_name_for_argument('funding_class_code', $fundsrccode);
}else if(!empty($agency)){
  $title =  _checkbook_project_get_name_for_argument('agency_id', $agency);
}
