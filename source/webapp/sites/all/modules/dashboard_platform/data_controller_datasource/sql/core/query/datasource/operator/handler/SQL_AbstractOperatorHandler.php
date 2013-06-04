<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
