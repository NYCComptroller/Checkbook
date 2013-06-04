<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class SQL_NotRegularExpressionOperatorHandler extends SQL_AbstractOperatorHandler {

    protected function prepareExpression(DataControllerCallContext $callcontext, AbstractRequest $request, $datasetName, $columnName, $columnDataType) {
        $pattern = $this->getParameterValue('pattern', TRUE);

        $formattedPattern = $this->datasourceHandler->formatValue(StringDataTypeHandler::$DATA_TYPE, $pattern);

        return $this->datasourceHandler->getExtension('formatNotRegularExpression')->format($this->datasourceHandler, $formattedPattern);
    }
}
