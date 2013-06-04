<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


interface DataSourceHandler {

    function getDataSourceType();
    function getExtension($functionalityName);

    function getMaximumEntityNameLength();

    function concatenateValues(array $formattedValues);

    // casting value to storage format. Similar method in DataTypeHandler casts value to in-memory format
    function castValue($datatype, $value);

    function formatStringValue($value);
    function formatDateValue($formattedValue, $mask);
    function formatValue($datatype, $value);
    function formatOperatorValue(DataControllerCallContext $callcontext, AbstractRequest $request, $datasetName, $columnName, OperatorHandler $value);

    function startTransaction($datasourceName);
    function commitTransaction($datasourceName);
    function rollbackTransaction($datasourceName);
}
