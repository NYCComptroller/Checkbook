<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


interface DataSourceStructureHandler extends DataSourceHandler {

    function createDatabase(DataControllerCallContext $callcontext, CreateDatabaseRequest $request);
    function dropDatabase(DataControllerCallContext $callcontext, DropDatabaseRequest $request);

    function createDatasetStorage(DataControllerCallContext $callcontext, CreateDatasetStorageRequest $request);
    function truncateDatasetStorage(DataControllerCallContext $callcontext, TruncateDatasetStorageRequest $request);
    function dropDatasetStorage(DataControllerCallContext $callcontext, DropDatasetStorageRequest $request);
}
