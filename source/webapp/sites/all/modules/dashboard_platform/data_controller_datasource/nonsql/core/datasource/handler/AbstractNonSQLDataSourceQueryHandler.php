<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


abstract class AbstractNonSQLDataSourceQueryHandler extends AbstractNonSQLDataSourceHandler implements DataSourceQueryHandler {

    public function isJoinSupported($datasourceNameA, $datasourceNameB) {
        return FALSE;
    }

    public function loadDatasetMetaData(DataControllerCallContext $callcontext, DatasetMetaData $dataset) {}

    public function prepareCubeMetaData(DataControllerCallContext $callcontext, CubeMetaData $cube) {}

    public function getNextSequenceValues(DataControllerCallContext $callcontext, SequenceRequest $request) {
        throw new UnsupportedOperationException();
    }
}
