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
