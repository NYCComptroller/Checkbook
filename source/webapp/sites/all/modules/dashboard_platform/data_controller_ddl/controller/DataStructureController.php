<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


interface DataStructureController extends DataController {

    function createDatabase($datasourceName, array $options = NULL);
    function dropDatabase($datasourceName);

    function createDatasetStorage($datasetName);
    function truncateDatasetStorage($datasetName);
    function dropDatasetStorage($datasetName);

    function createCubeStorage($cubeName);
    function truncateCubeStorage($cubeName);
    function dropCubeStorage($cubeName);
}
