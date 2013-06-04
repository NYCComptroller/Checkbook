<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




interface ResultFormatterConfiguration {

    function printFormattingPath();

    function adjustDatasetQueryRequest(DataControllerCallContext $callcontext, DatasetQueryRequest $request);
    function adjustDatasetCountRequest(DataControllerCallContext $callcontext, DatasetCountRequest $request);
    function adjustCubeQueryRequest(DataControllerCallContext $callcontext, CubeQueryRequest $request);
    function adjustCubeCountRequest(DataControllerCallContext $callcontext, CubeQueryRequest $request);

    function isClientSortingRequired();
    function isClientPaginationRequired();
}

interface ResultFormatter extends ResultFormatterConfiguration {

    function formatPropertyName($propertyName);
    function formatPropertyValue($propertyName, $propertyValue);
    function setRecordPropertyValue(array &$record = NULL, $propertyName, $propertyValue);

    function formatRecord(array &$records = NULL, $record);
    function postFormatRecords(array &$records = NULL);

    function reformatRecords(array &$records = NULL);
}
