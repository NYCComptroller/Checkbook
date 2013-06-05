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
