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


abstract class AbstractDataQueryController extends AbstractDataController implements DataQueryController {

    /**
     * @param string $cubeName
     * @return DataSourceHandler
     */
    protected function getDataSourceQueryHandlerByCubeName($cubeName) {
        $metamodel = data_controller_get_metamodel();

        $cube = $metamodel->getCube($cubeName);

        return $this->getDataSourceQueryHandlerByCube($cube);
    }

    /**
     * @param CubeMetaData $cube
     * @return DataSourceHandler
     */
    protected function getDataSourceQueryHandlerByCube(CubeMetaData $cube) {
        return $this->getDataSourceQueryHandlerByDatasetName($cube->sourceDatasetName);
    }

    /**
     * @param string $datasetName
     * @return DataSourceHandler
     */
    protected function getDataSourceQueryHandlerByDatasetName($datasetName) {
        return $this->getDataSourceHandlerByDatasetName($datasetName);
    }

    /**
     * @param DatasetMetaData $dataset
     * @return DataSourceQueryHandler
     */
    protected function getDataSourceQueryHandlerByDataset(DatasetMetaData $dataset) {
        return $this->getDataSourceHandlerByDataset($dataset);
    }

    /**
     * @param string $datasourceName
     * @return DataSourceQueryHandler
     */
    protected function getDataSourceQueryHandler($datasourceName) {
        return $this->getDataSourceHandler($datasourceName);
    }

  /**
   * @param $type
   * @return DataSourceHandler|DataSourceQueryHandler
   */
  protected function lookupDataSourceHandler($type) {
        return DataSourceQueryFactory::getInstance()->getHandler($type);
    }

  /**
   * @param $datasetName
   * @param null $columns
   * @param null $parameters
   * @param null $orderBy
   * @param int $startWith
   * @param null $limit
   * @param ResultFormatter|NULL $resultFormatter
   * @return mixed
   */
  public function queryDataset($datasetName, $columns = NULL, $parameters = NULL, $orderBy = NULL, $startWith = 0, $limit = NULL, ResultFormatter $resultFormatter = NULL) {
        $request = new DataQueryControllerDatasetRequest();
        $request->initializeFrom($datasetName, $columns, $parameters, $orderBy, $startWith, $limit, $resultFormatter);

        return $this->query($request);
    }

  /**
   * @param $datasetName
   * @param $columns
   * @param null $parameters
   * @param null $orderBy
   * @param int $startWith
   * @param null $limit
   * @param ResultFormatter|NULL $resultFormatter
   * @return mixed
   */
  public function queryCube($datasetName, $columns, $parameters = NULL, $orderBy = NULL, $startWith = 0, $limit = NULL, ResultFormatter $resultFormatter = NULL) {
        $request = new DataQueryControllerCubeRequest();
        $request->initializeFrom($datasetName, $columns, $parameters, $orderBy, $startWith, $limit, $resultFormatter);

        return $this->query($request);
    }

  /**
   * @param $datasetName
   * @param null $parameters
   * @param ResultFormatter|NULL $resultFormatter
   * @return int
   */
  public function countDatasetRecords($datasetName, $parameters = NULL, ResultFormatter $resultFormatter = NULL) {
        $request = new DataQueryControllerDatasetRequest();
        $request->initializeFrom($datasetName, NULL, $parameters, NULL, 0, NULL, $resultFormatter);

        return $this->countRecords($request);
    }

  /**
   * @param $datasetName
   * @param $columns
   * @param null $parameters
   * @param ResultFormatter|NULL $resultFormatter
   * @return int
   */
  public function countCubeRecords($datasetName, $columns, $parameters = NULL, ResultFormatter $resultFormatter = NULL) {
        $request = new DataQueryControllerCubeRequest();
        // Note: even if we want we cannot remove $columns parameter for cube record number calculation.
        // That is because $columns are used to identify columns for aggregation
        $request->initializeFrom($datasetName, $columns, $parameters, NULL, 0, NULL, $resultFormatter);

        return $this->countRecords($request);
    }
}
