<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


interface DataManipulationController extends DataController {

    // ----- individual operations
    function insertDatasetRecords($datasetName, AssociativeRecordsHolder $recordsHolder);
    function updateDatasetRecords($datasetName, AssociativeRecordsHolder $recordsHolder);
    function insertOrUpdateOrDeleteDatasetRecords($datasetName, AssociativeRecordsHolder $recordsHolder);
    function deleteDatasetRecords($datasetName, AssociativeRecordsHolder $recordsHolder);

    // ----- batch of operations
    function insertDatasetRecordBatch($datasetName, IndexedRecordsHolder $recordsHolder);
    function updateDatasetRecordBatch($datasetName, IndexedRecordsHolder $recordsHolder);
    function insertOrUpdateOrDeleteDatasetRecordBatch($datasetName, IndexedRecordsHolder $recordsHolder);
    function deleteDatasetRecordBatch($datasetName, IndexedRecordsHolder $recordsHolder);
}
