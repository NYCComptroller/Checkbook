<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


$refURL =$_GET['refURL'];

$title = "New York City";

$agencyId = _getRequestParamValue('agency');
if(isset($agencyId)){
    $title = _checkbook_project_get_name_for_argument("agency_id",$agencyId);
}

$domain = 'Payroll';
