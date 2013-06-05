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


abstract class SQL_AbstractPreviousOperatorHandler extends SQL_AbstractHighestBoundaryOperatorHandler {

    protected function processDatasetExpressionRequest(DataControllerCallContext $callcontext, DatasetQueryRequest $expressionRequest, $columnName) {
        $occurenceIndex = $this->getParameterValue('occurenceIndex');
        if (!isset($occurenceIndex)) {
            $occurenceIndex = 1;
        }

        $loader = new __PreviousBoundaryOperatorHandler__Loader($this->datasourceHandler, $expressionRequest, $columnName);

        // we need to run the loop for +1 times to calculate and eliminate latest and then calculate 1st occurence
        $selectedOccurenceValue = NULL;
        for ($index = 0; $index <= $occurenceIndex; $index++) {
            while (TRUE) {
                $occurenceValue = $loader->getNextOccurenceValue($callcontext);
                if (isset($occurenceValue)) {
                    $occurenceValue = $this->adjustCalculatedValue($occurenceValue);
                    if (isset($selectedOccurenceValue) && ($selectedOccurenceValue == $occurenceValue)) {
                        continue;
                    }

                    $selectedOccurenceValue = $occurenceValue;
                    break;
                }
                else {
                    // there is no values in database for this occurence
                    $selectedOccurenceValue = NULL;
                    break 2;
                }
            }
        }

        return $selectedOccurenceValue;
    }

    protected function processCubeExpressionRequest(DataControllerCallContext $callcontext, CubeQueryRequest $expressionRequest, $resultColumnName) {
        // we need to return 2 records: first - 'the latest' and second is 'previous'
        $expressionRequest->setPagination(2, 0);
        return parent::processCubeExpressionRequest($callcontext, $expressionRequest, $resultColumnName);
    }

    protected function processCubeExpressionRequestResult($resultColumnName, array $result = NULL) {
        // returning data from second record. That will correspond to 'previous' value
        return isset($result[1]) ? $result[1][$resultColumnName] : NULL;
    }
}


class __PreviousBoundaryOperatorHandler__Loader extends AbstractObject {

    public static $PRELOADED_RECORD_COUNT = 20;

    /**
     * @var DataSourceHandler
     */
    protected $datasourceHandler = NULL;
    /**
     * @var AbstractQueryRequest
     */
    protected $request = NULL;
    protected $columnName = NULL;

    protected $loadedRecords = NULL;
    protected $requestedRecordCount = NULL;
    protected $processedRecordIndex = NULL;

    protected $lastOccurenceValue = NULL;

    public function __construct(DataSourceHandler $datasourceHandler, AbstractQueryRequest $request, $columnName) {
        parent::__construct();
        $this->datasourceHandler = $datasourceHandler;
        $this->request = $request;
        $this->columnName = $columnName;
    }

    protected function loadNextRecord(DataControllerCallContext $callcontext) {
        // checking if the record could be retrieved from cache
        if (isset($this->requestedRecordCount) && ($this->requestedRecordCount > ($this->processedRecordIndex + 1))) {
            // the value is possibly in cache. That depends on number of records we were able to load
            $this->processedRecordIndex++;
        }
        else {
            $this->processedRecordIndex = 0;

            // loading data from database
            $this->requestedRecordCount = self::$PRELOADED_RECORD_COUNT;

            $request = clone $this->request;
            $request->setPagination($this->requestedRecordCount, 0);
            // adding condition for the value column
            if (isset($this->lastOccurenceValue)) {
                $request->addQueryValue(
                    0,
                    $this->columnName, data_controller_get_operator_factory_instance()->initiateHandler(LessThanOperatorHandler::$OPERATOR__NAME, $this->lastOccurenceValue));
            }

            $this->loadedRecords = $this->datasourceHandler->queryDataset($callcontext, $request, new PassthroughResultFormatter());
        }

        return isset($this->loadedRecords[$this->processedRecordIndex]) ? $this->loadedRecords[$this->processedRecordIndex] : NULL;
    }

    public function getNextOccurenceValue(DataControllerCallContext $callcontext) {
        while (($record = $this->loadNextRecord($callcontext)) != NULL) {
            $occurenceValue = $record[$this->columnName];
            if (isset($this->lastOccurenceValue) && ($this->lastOccurenceValue == $occurenceValue)) {
                continue;
            }

            $this->lastOccurenceValue = $occurenceValue;
            return $this->lastOccurenceValue;
        }

        return NULL;
    }
}
