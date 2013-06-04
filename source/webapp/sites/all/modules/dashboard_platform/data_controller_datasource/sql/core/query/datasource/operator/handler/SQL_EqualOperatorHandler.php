<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




class SQL_EqualOperatorHandler extends SQL_AbstractOperatorHandler {

    protected function prepareExpression(DataControllerCallContext $callcontext, AbstractRequest $request, $datasetName, $columnName, $columnDataType) {
        $value = $this->getParameterValue('value');

        if (!isset($value)) {
            return ' IS NULL';
        }

        if (is_array($value)) {
            $values = NULL;
            foreach ($value as $v) {
                $values[] = $this->datasourceHandler->formatValue($columnDataType, $v);
            }

            $formattedValue = ' IN (' . implode(', ', $values) . ')';
        }
        else {
            $formattedValue = ' = ' . $this->datasourceHandler->formatValue($columnDataType, $value);
        }

        return $formattedValue;
    }
}
