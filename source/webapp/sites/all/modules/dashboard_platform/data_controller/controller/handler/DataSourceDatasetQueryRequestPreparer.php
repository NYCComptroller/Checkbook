<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class DataSourceDatasetQueryRequestPreparer extends AbstractObject {

    public function prepareDatasetQueryRequest(DataQueryControllerDatasetRequest $request) {
        $datasourceRequest = new DatasetQueryRequest($request->datasetName);

        $datasourceRequest->addCompositeQueryValues($request->parameters);
        $datasourceRequest->addColumns($request->columns);
        $datasourceRequest->addOrderByColumns($request->orderBy);
        $datasourceRequest->setPagination($request->limit, $request->startWith);

        return $datasourceRequest;
    }

    public function prepareDatasetCountRequest(DataQueryControllerDatasetRequest $request) {
        $datasourceRequest = new DatasetCountRequest($request->datasetName);

        $datasourceRequest->addCompositeQueryValues($request->parameters);

        return $datasourceRequest;
    }
}
