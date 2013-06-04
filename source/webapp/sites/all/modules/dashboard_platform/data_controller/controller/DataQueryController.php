<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




interface DataQueryController extends DataController {

    /**
     * @param string $datasetName
     * @return DatasetMetaData
     */
    function getDatasetMetaData($datasetName);
    /**
     * @param string $cubeName
     * @return CubeMetaData
     */
    function getCubeMetaData($cubeName);

    function getNextSequenceValues($datasourceName, $sequenceName, $quantity);

    /**
     * @param DataQueryControllerDatasetRequest|DataQueryControllerCubeRequest|DataQueryControllerRequestTree $request
     */
    function query($request);
    function queryDataset($datasetName, $columns = NULL, $parameters = NULL, $orderBy = NULL, $startWith = 0, $limit = NULL, ResultFormatter $resultFormatter = NULL);
    function queryCube($datasetName, $columns, $parameters = NULL, $orderBy = NULL, $startWith = 0, $limit = NULL, ResultFormatter $resultFormatter = NULL);

    /**
     * @param DataQueryControllerDatasetRequest|DataQueryControllerCubeRequest|DataQueryControllerRequestTree $request
     * @return integer
     */
    function countRecords($request);
    function countDatasetRecords($datasetName, $parameters = NULL, ResultFormatter $resultFormatter = NULL);
    function countCubeRecords($datasetName, $columns, $parameters = NULL, ResultFormatter $resultFormatter = NULL);
}
