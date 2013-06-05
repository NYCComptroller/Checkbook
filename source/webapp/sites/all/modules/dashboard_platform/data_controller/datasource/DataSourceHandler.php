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
