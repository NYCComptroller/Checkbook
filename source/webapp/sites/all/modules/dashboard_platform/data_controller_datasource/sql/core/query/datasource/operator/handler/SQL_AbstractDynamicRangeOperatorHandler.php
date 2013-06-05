<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
* 
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
