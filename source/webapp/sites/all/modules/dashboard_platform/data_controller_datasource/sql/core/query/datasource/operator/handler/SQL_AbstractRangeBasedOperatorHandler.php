<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class SQL_AbstractRangeBasedOperatorHandler extends SQL_AbstractOperatorHandler {

    protected function prepareFromValue(DataControllerCallContext $callcontext, AbstractRequest $request, $datasetName, $columnName) {
        return $this->getParameterValue('from');
    }

    protected function prepareToValue(DataControllerCallContext $callcontext, AbstractRequest $request, $datasetName, $columnName) {
        return $this->getParameterValue('to');
    }

    protected function prepareExpression(DataControllerCallContext $callcontext, AbstractRequest $request, $datasetName, $columnName, $columnDataType) {
        $fromValue = $this->prepareFromValue($callcontext, $request, $datasetName, $columnName);
        $formattedFromValue = isset($fromValue) ? $this->datasourceHandler->formatValue($columnDataType, $fromValue) : NULL;

        $toValue = $this->prepareToValue($callcontext, $request, $datasetName, $columnName);
        $formattedToValue = isset($toValue) ? $this->datasourceHandler->formatValue($columnDataType, $toValue) : NULL;

        $formattedValue = NULL;
        if (isset($formattedFromValue) && isset($formattedToValue)) {
            $formattedValue = ' BETWEEN ' . $formattedFromValue . ' AND ' . $formattedToValue;
        }
        elseif (isset($formattedFromValue)) {
            $formattedValue = ' >= ' . $formattedFromValue;
        }
        elseif (isset($formattedToValue)) {
            $formattedValue = ' <= ' . $formattedToValue;
        }
        else {
            $formattedValue = ' IS NULL';
        }

        return $formattedValue;
    }
}
