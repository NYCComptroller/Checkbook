<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class SQL_NotInRangeOperatorHandler extends SQL_AbstractOperatorHandler {

    protected function prepareExpression(DataControllerCallContext $callcontext, AbstractRequest $request, $datasetName, $columnName, $columnDataType) {
        $fromValue = $this->getParameterValue('from');
        $toValue = $this->getParameterValue('to');

        $formattedFromValue = isset($fromValue) ? $this->datasourceHandler->formatValue($columnDataType, $fromValue) : NULL;
        $formattedToValue = isset($toValue) ? $this->datasourceHandler->formatValue($columnDataType, $toValue) : NULL;

        $formattedValue = NULL;
        if (isset($formattedFromValue) && isset($formattedToValue)) {
            $formattedValue = ' NOT BETWEEN ' . $formattedFromValue . ' AND ' . $formattedToValue;
        }
        elseif (isset($formattedFromValue)) {
            $formattedValue = ' < ' . $formattedFromValue;
        }
        elseif (isset($formattedToValue)) {
            $formattedValue = ' > ' . $formattedToValue;
        }
        else {
            $formattedValue = ' IS NOT NULL';
        }

        return $formattedValue;
    }
}
