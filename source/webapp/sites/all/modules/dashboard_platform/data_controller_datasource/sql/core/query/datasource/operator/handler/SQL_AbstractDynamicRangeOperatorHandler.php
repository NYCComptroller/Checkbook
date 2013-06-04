<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class SQL_AbstractDynamicRangeOperatorHandler extends SQL_AbstractRangeBasedOperatorHandler {

    protected static $OPERATOR_VARIABLE_NAME__LATEST = 'range.dynamic:latest';

    abstract protected function getLatestOperatorName();

    protected function getLatestValue(DataControllerCallContext $callcontext, AbstractRequest $request, $datasetName, $columnName) {
        $latestValue = NULL;
        if ($this->operatorHandler->wasValueCalculated(self::$OPERATOR_VARIABLE_NAME__LATEST)) {
            $latestValue = $this->operatorHandler->getCalculatedValue(self::$OPERATOR_VARIABLE_NAME__LATEST);
        }
        else {
            $operator = OperatorFactory::getInstance()->initiateHandler($this->getLatestOperatorName());
            $sqlOperatorHandler = SQLOperatorFactory::getInstance()->getHandler($this->datasourceHandler, $operator);

            $latestValue = $sqlOperatorHandler->prepareBoundaryValue($callcontext, $request, $datasetName, $columnName);

            // storing into internal cache for further usage
            $this->operatorHandler->setCalculatedValue(self::$OPERATOR_VARIABLE_NAME__LATEST, $latestValue);
        }

        return $latestValue;
    }

    abstract protected function offsetLatestValue($latestValue, $offset);

    protected function prepareFromValue(DataControllerCallContext $callcontext, AbstractRequest $request, $datasetName, $columnName) {
        $offest = parent::prepareFromValue($callcontext, $request, $datasetName, $columnName);
        if (!isset($offest)) {
            return NULL;
        }

        $latestValue = $this->getLatestValue($callcontext, $request, $datasetName, $columnName);

        return isset($latestValue)
            ? (($offest == 0) ? $latestValue : $this->offsetLatestValue($latestValue, $offest))
            : NULL;
    }

    protected function prepareToValue(DataControllerCallContext $callcontext, AbstractRequest $request, $datasetName, $columnName) {
        $offest = parent::prepareToValue($callcontext, $request, $datasetName, $columnName);
        if (!isset($offest)) {
            return NULL;
        }

        $latestValue = $this->getLatestValue($callcontext, $request, $datasetName, $columnName);

        return isset($latestValue)
            ? (($offest == 0) ? $latestValue : $this->offsetLatestValue($latestValue, $offest))
            : NULL;
    }
}
