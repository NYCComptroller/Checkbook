<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class CheckbookDateUtil
{
    /** Returns month details for the given Month Id */
    static function getMonthDetails($monthId){
        if(!isset($monthId)){
            return NULL;
        }

        $monthDetails = _checkbook_project_querydataset('checkbook:month',array('month_id','month_value','month_name','month_short_name'), array('month_id'=>$monthId));
        return $monthDetails;
    }

}
