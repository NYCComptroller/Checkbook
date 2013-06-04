<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




interface DataSourceQueryHandler extends DataSourceHandler {

    function isJoinSupported($datasourceNameA, $datasourceNameB);

    function loadDatasetMetaData(DataControllerCallContext $callcontext, DatasetMetaData $dataset);
    function prepareCubeMetaData(DataControllerCallContext $callcontext, CubeMetaData $cube);

    function getNextSequenceValues(DataControllerCallContext $callcontext, SequenceRequest $request);

    function queryDataset(DataControllerCallContext $callcontext, DatasetQueryRequest $request, ResultFormatter $resultFormatter);
    function countDatasetRecords(DataControllerCallContext $callcontext, DatasetCountRequest $request, ResultFormatter $resultFormatter);

    function queryCube(DataControllerCallContext $callcontext, CubeQueryRequest $request, ResultFormatter $resultFormatter);
    function countCubeRecords(DataControllerCallContext $callcontext, CubeQueryRequest $request, ResultFormatter $resultFormatter);
}
