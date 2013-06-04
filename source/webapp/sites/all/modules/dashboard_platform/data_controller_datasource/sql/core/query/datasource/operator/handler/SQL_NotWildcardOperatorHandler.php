<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class SQL_NotWildcardOperatorHandler extends SQL_WildcardOperatorHandler {

    protected function prepareExpression(DataControllerCallContext $callcontext, AbstractRequest $request, $datasetName, $columnName, $columnDataType) {
        return ' NOT ' . parent::prepareExpression($callcontext, $request, $datasetName, $columnName, $columnDataType);
    }
}
