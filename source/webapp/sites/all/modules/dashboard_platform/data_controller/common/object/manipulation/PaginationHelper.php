<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




function paginate_records(array &$records = NULL, $start_with = 0, $limit = NULL) {
    if (!isset($records)) {
        return $records;
    }

    if ((!isset($start_with) || ($start_with == 0)) && !isset($limit)) {
        return $records;
    }

    return array_slice($records, (isset($start_with) ? $start_with : 0), $limit);
}
