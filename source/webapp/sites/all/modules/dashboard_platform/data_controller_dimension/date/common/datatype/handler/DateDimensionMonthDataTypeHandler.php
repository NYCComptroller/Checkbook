<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DateDimensionMonthDataTypeHandler extends DateDataTypeHandler {

    public static $DATA_TYPE = 'date:month';

    public function selectCompatible($datatype) {
        return ($datatype == DateDataTypeHandler::$DATA_TYPE)
            ? DateDataTypeHandler::$DATA_TYPE
            : parent::selectCompatible($datatype);
    }
}
