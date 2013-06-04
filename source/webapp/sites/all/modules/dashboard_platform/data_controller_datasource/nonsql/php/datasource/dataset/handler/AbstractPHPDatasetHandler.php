<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/




abstract class AbstractPHPDatasetHandler extends AbstractObject implements PHPDatasetHandler {

    protected function isColumnRequested($request, $columnName) {
        return !isset($request->columns) || array_search($columnName, $request->columns);
    }

    protected function checkRequestQuery($request) {
        if (isset($request->queries)) {
            if (count($request->queries) !== 1) {
                throw new UnsupportedOperationException(t('Only one request is supported at this time'));
            }
        }
    }

    protected function findQueryOperatorValue($request, $requestParameterName) {
        $this->checkRequestQuery($request);

        if (isset($request->queries)) {
            $query = $request->queries[0];

            if (isset($query[$requestParameterName])) {
                return $query[$requestParameterName];
            }
        }

        return NULL;
    }

    protected function getQueryOperatorValue($request, $requestParameterName) {
        $value = $this->findQueryOperatorValue($request, $requestParameterName);
        if (!isset($value)) {
            throw new IllegalStateException(t("Undefined '@parameterName' parameter in the request", array('@parameterName' => $requestParameterName)));
        }

        return $value;
    }

    protected function mergeQueryValue(&$parameters, $parameterName, $request, $requestParameterName) {
        $this->checkRequestQuery($request);

        if (isset($request->queries)) {
            $query = $request->queries[0];

            if (isset($query[$requestParameterName])) {
                $parameters[$parameterName] = $query[$requestParameterName];
            }
        }
    }

    protected function mergeQueryValues(&$parameters, $request) {
        $this->checkRequestQuery($request);

        if (isset($request->queries)) {
            $query = $request->queries[0];

            ArrayHelper::mergeArrays($parameters, $query);
        }
    }

    protected function prepareQueryDatasetRequest2CountRecords($callcontext, $countRequest) {
        $datasetName = $countRequest->getDatasetName();

        $dataRequest = new DatasetQueryRequest($datasetName);
        $dataRequest->addCompositeQueryValues($countRequest->queries);

        return $dataRequest;
    }

    public function countDatasetRecords($callcontext, $request, ResultFormatter $resultFormatter) {
        $dataRequest = $this->prepareQueryDatasetRequest2CountRecords($callcontext, $request);

        $data = DataSourceController::getInstance()->queryDataset($callcontext, $dataRequest, $resultFormatter);

        return isset($data) ? count($data) : 0;
    }
}
