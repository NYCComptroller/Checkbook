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




abstract class SQL_AbstractOperatorHandler extends AbstractObject {

    /**
     * @var DataSourceHandler
     */
    protected $datasourceHandler = NULL;

    protected $operatorHandler = NULL;

    public function __construct(DataSourceHandler $datasourceHandler, AbstractOperatorHandler $operatorHandler) {
        parent::__construct();
        $this->datasourceHandler = $datasourceHandler;
        $this->operatorHandler = $operatorHandler;
    }

    protected function getParameterValue($parameterName, $isValueRequired = FALSE) {
        $this->operatorHandler->metadata->checkParameterName($parameterName);

        $value = isset($this->operatorHandler->$parameterName) ? $this->operatorHandler->$parameterName : NULL;
        if ($isValueRequired && !isset($value)) {
            throw new IllegalStateException(t(
            	"Value has not been provided for '@parameterName' parameter",
                array('@parameterName' => $parameterName)));
        }

        return $value;
    }

    public function format(DataControllerCallContext $callcontext, AbstractRequest $request, $datasetName, $columnName, $columnDataType = NULL) {
        if (!isset($columnDataType)) {
            $metamodel = data_controller_get_metamodel();

            // trying to detect data type for the value
            list($referencedDatasetName, $referencedColumnName) = ReferencePathHelper::splitReference($columnName);
            $selectedDatasetName = isset($referencedDatasetName) ? $referencedDatasetName : $datasetName;
            $selectedColumnName = isset($referencedColumnName) ? $referencedColumnName : $columnName;
            $dataset = $metamodel->getDataset($selectedDatasetName);
            $column = $dataset->findColumn($selectedColumnName);
            $columnDataType = isset($column) ? $column->type->applicationType : NULL;

            // preparing column data type based on operator parameter data type
            if (!isset($columnDataType) && ($this->operatorHandler instanceof ParameterBasedOperatorHandler)) {
                $columnDataType = $this->operatorHandler->getParameterDataType();
            }
        }

        return $this->prepareExpression($callcontext, $request, $datasetName, $columnName, $columnDataType);
    }

    abstract protected function prepareExpression(DataControllerCallContext $callcontext, AbstractRequest $request, $datasetName, $columnName, $columnDataType);
}
