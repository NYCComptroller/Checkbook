<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
 
$title = 'New York City';

$agency = _getRequestParamValue('agency');
$expcategory = _getRequestParamValue('expcategory');

if(!empty($expcategory)){
    $expName = _checkbook_project_get_name_for_argument('object_class_id', $expcategory);

        $title =  $expName;
}else if(!empty($agency)){
    $title = _checkbook_project_get_name_for_argument('agency_id', $agency) ;
}
