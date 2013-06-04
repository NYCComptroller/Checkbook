<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class SQL_DynamicRangeOperatorHandler extends SQL_AbstractDynamicRangeOperatorHandler {

    protected function getLatestOperatorName() {
        return LatestOperatorHandler::$OPERATOR__NAME;
    }

    protected function offsetLatestValue($latestValue, $offset) {
        return $latestValue - $offset;
    }
}
