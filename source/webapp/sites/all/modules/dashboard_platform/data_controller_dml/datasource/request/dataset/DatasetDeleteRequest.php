<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class DatasetDeleteRequest extends AbstractDatasetManipulationRequest {

    public static $OPERATION__DELETE = 'delete';

    public function getOperationName() {
        return self::$OPERATION__DELETE;
    }
}
