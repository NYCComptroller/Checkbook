<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
* 
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
