<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


interface DataSourceManipulationHandler extends DataSourceHandler {

    function insertDatasetRecords(DataControllerCallContext $callcontext, AbstractDatasetManipulationRequest $request);
    function updateDatasetRecords(DataControllerCallContext $callcontext, AbstractDatasetManipulationRequest $request);
    function deleteDatasetRecords(DataControllerCallContext $callcontext, AbstractDatasetManipulationRequest $request);

    function insertOrUpdateOrDeleteDatasetRecords(DataControllerCallContext $callcontext, AbstractDatasetManipulationRequest $request);
}
