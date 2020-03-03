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




/*
 * Data Controller has the following responsibilities:
 *   - prepares context for the call
 *   - cleans/adjusts (trims, converts) input parameters
 *   - wraps input parameters into a request object
 */

/**
 * Class DefaultDataQueryController
 */
class DefaultDataQueryController extends AbstractDataQueryController {

  /**
   * @param string $datasetName
   * @return DatasetMetaData
   * @throws IllegalStateException
   */
  public function getDatasetMetaData($datasetName) {
        $datasetName = StringHelper::trim($datasetName);

        $metamodel = data_controller_get_metamodel();

        $dataset = $metamodel->getDataset($datasetName);
        if (!$dataset->isComplete()) {
            $callcontext = $this->prepareCallContext();

            MetaModelFactory::getInstance()->startGlobalModification();
            try {
                $this->getDataSourceQueryHandlerByDataset($dataset)->loadDatasetMetaData($callcontext, $dataset);

                $dataset->markAsComplete();
            }
            catch (Exception $e) {
                MetaModelFactory::getInstance()->finishGlobalModification(FALSE);
                throw $e;
            }
            MetaModelFactory::getInstance()->finishGlobalModification(TRUE);
        }

        return $dataset;
    }

  /**
   * @param string $cubeName
   * @return CubeMetaData
   * @throws IllegalStateException
   */
  public function getCubeMetaData($cubeName) {
        $cubeName = StringHelper::trim($cubeName);

        $metamodel = data_controller_get_metamodel();

        $cube = $metamodel->getCube($cubeName);
        if (!$cube->isComplete()) {
            $callcontext = $this->prepareCallContext();

            // preparing meta data for the cube source dataset
            if (!isset($cube->sourceDataset)) {
                $cube->sourceDataset = $this->getDatasetMetaData($cube->sourceDatasetName);
            }

            // preparing meta data for dimension level datasets
            if (isset($cube->dimensions)) {
                foreach ($cube->dimensions as $dimension) {
                    foreach ($dimension->levels as $level) {
                        if (isset($level->datasetName) && !isset($level->dataset)) {
                            $level->dataset = $this->getDatasetMetaData($level->datasetName);
                        }
                    }
                }
            }

            // preparing metadata for the rest of the cube
            $this->getDataSourceQueryHandlerByDatasetName($cube->sourceDatasetName)->prepareCubeMetaData($callcontext, $cube);
        }

        return $cube;
    }

  /**
   * @param $datasourceName
   * @param $sequenceName
   * @param $quantity
   * @return mixed
   */
  public function getNextSequenceValues($datasourceName, $sequenceName, $quantity) {
        $datasourceName = StringHelper::trim($datasourceName);
        $sequenceName = StringHelper::trim($sequenceName);

        $callcontext = $this->prepareCallContext();

        $request = new SequenceRequest($datasourceName, $sequenceName, $quantity);

        LogHelper::log_debug($request);

        return $this->getDataSourceQueryHandler($datasourceName)->getNextSequenceValues($callcontext, $request);
    }

  /**
   * @param DataQueryControllerCubeRequest|DataQueryControllerDatasetRequest|DataQueryControllerRequestTree $request
   * @return mixed|null |null
   * @throws IllegalArgumentException
   * @throws IllegalStateException
   * @throws UnsupportedOperationException
   */
  public function query($request) {
        $requestCleaner = new DataQueryControllerRequestCleaner();
        $adjustedRequest = $requestCleaner->adjustRequest($request);

        $result = NULL;
        if ($adjustedRequest instanceof DataQueryControllerRequestTree) {
            // it is possible that whole tree or some branches of the tree need to be joined manually
            throw new UnsupportedOperationException();
        }
        elseif ($adjustedRequest instanceof DataQueryControllerDatasetRequest) {
            $result = $this->executeDatasetQueryRequest($adjustedRequest);
        }
        elseif ($adjustedRequest instanceof DataQueryControllerCubeRequest) {
            $result = $this->executeCubeQueryRequest($adjustedRequest);
        }

        return $result;
    }

  /**
   * @param DataQueryControllerCubeRequest|DataQueryControllerDatasetRequest|DataQueryControllerRequestTree $request
   * @return int|null
   * @throws IllegalArgumentException
   * @throws IllegalStateException
   * @throws UnsupportedOperationException
   */
  public function countRecords($request) {
        $requestCleaner = new DataQueryControllerRequestCleaner();
        $adjustedRequest = $requestCleaner->adjustRequest($request);

        $result = NULL;
        if ($adjustedRequest instanceof DataQueryControllerRequestTree) {
            // it is possible that whole tree or some branches of the tree need to be joined manually
            throw new UnsupportedOperationException();
        }
        elseif ($adjustedRequest instanceof DataQueryControllerDatasetRequest) {
            $result = $this->executeDatasetCountRequest($adjustedRequest);
        }
        elseif ($adjustedRequest instanceof DataQueryControllerCubeRequest) {
            $result = $this->executeCubeCountRequest($adjustedRequest);
        }

        return $result;
    }

  /**
   * @param DataQueryControllerDatasetRequest $request
   * @return mixed
   * @throws IllegalStateException
   */
  protected function executeDatasetQueryRequest(DataQueryControllerDatasetRequest $request) {
        $callcontext = $this->prepareCallContext();

        $requestPreparer = new DataSourceDatasetQueryRequestPreparer();
        $datasetQueryRequest = $requestPreparer->prepareDatasetQueryRequest($request);

        $this->prepareDatasetRequestMetaData($datasetQueryRequest);

        $datasetResultFormatter = isset($request->resultFormatter) ? $request->resultFormatter : $this->getDefaultResultFormatter();
        $datasetResultFormatter->adjustDatasetQueryRequest($callcontext, $datasetQueryRequest);

        LogHelper::log_notice($datasetQueryRequest);

        $datasetName = $datasetQueryRequest->getDatasetName();
        LogHelper::log_notice(t(
        	"Using '!formattingPath' to format result of the dataset: @datasetName",
            array('!formattingPath' => $datasetResultFormatter->printFormattingPath(), '@datasetName' => $datasetName)));

        $result = $this->getDataSourceQueryHandlerByDatasetName($datasetName)->queryDataset($callcontext, $datasetQueryRequest, $datasetResultFormatter);
        return $result;
    }

  /**
   * @param DataQueryControllerDatasetRequest $request
   * @return mixed
   * @throws IllegalStateException
   */
  protected function executeDatasetCountRequest(DataQueryControllerDatasetRequest $request) {
        $callcontext = $this->prepareCallContext();

        $requestPreparer = new DataSourceDatasetQueryRequestPreparer();
        $datasetCountRequest = $requestPreparer->prepareDatasetCountRequest($request);

        $this->prepareDatasetRequestMetaData($datasetCountRequest);

        $datasetResultFormatter = isset($request->resultFormatter) ? $request->resultFormatter : $this->getDefaultResultFormatter();
        $datasetResultFormatter->adjustDatasetCountRequest($callcontext, $datasetCountRequest);

        LogHelper::log_debug($request);

        $datasetName = $datasetCountRequest->getDatasetName();

        return $this->getDataSourceQueryHandlerByDatasetName($datasetName)->countDatasetRecords($callcontext, $datasetCountRequest, $datasetResultFormatter);
    }

  /**
   * @param AbstractDatasetQueryRequest $request
   * @throws IllegalStateException
   */
  protected function prepareDatasetRequestMetaData(AbstractDatasetQueryRequest $request) {
        $this->getDatasetMetaData($request->getDatasetName());
    }

  /**
   * @param DataQueryControllerCubeRequest $request
   * @return mixed
   * @throws IllegalArgumentException
   * @throws IllegalStateException
   * @throws UnsupportedOperationException
   */
  protected function executeCubeQueryRequest(DataQueryControllerCubeRequest $request) {
        $callcontext = $this->prepareCallContext();

        $requestPreparer = new DataSourceCubeQueryRequestPreparer();
        $cubeQueryRequest = $requestPreparer->prepareCubeQueryRequest($request);

        $this->prepareCubeRequestMetaData($cubeQueryRequest);

        $cubeResultFormatter = isset($request->resultFormatter) ? $request->resultFormatter : $this->getDefaultResultFormatter();
        $cubeResultFormatter->adjustCubeQueryRequest($callcontext, $cubeQueryRequest);

        $cubeName = $cubeQueryRequest->getCubeName();
        LogHelper::log_info('cubeQuery');
        LogHelper::log_debug(t(
        	"Using '!formattingPath' to format result of the cube: @cubeName",
            array('!formattingPath' => $cubeResultFormatter->printFormattingPath(), '@cubeName' =>  $cubeName)));
        LogHelper::log_debug($request);

        return $this->getDataSourceQueryHandlerByCubeName($cubeName)->queryCube($callcontext, $cubeQueryRequest, $cubeResultFormatter);
    }

  /**
   * @param DataQueryControllerCubeRequest $request
   * @return mixed
   * @throws IllegalArgumentException
   * @throws IllegalStateException
   * @throws UnsupportedOperationException
   */
  protected function executeCubeCountRequest(DataQueryControllerCubeRequest $request) {
        $callcontext = $this->prepareCallContext();

        $requestPreparer = new DataSourceCubeQueryRequestPreparer();
        $cubeCountRequest = $requestPreparer->prepareCubeCountRequest($request);

        $this->prepareCubeRequestMetaData($cubeCountRequest);

        $cubeResultFormatter = isset($request->resultFormatter) ? $request->resultFormatter : $this->getDefaultResultFormatter();
        $cubeResultFormatter->adjustCubeCountRequest($callcontext, $cubeCountRequest);

        LogHelper::log_info('cubeCount');
        LogHelper::log_debug($request);

        $cubeName = $cubeCountRequest->getCubeName();

        return $this->getDataSourceQueryHandlerByCubeName($cubeName)->countCubeRecords($callcontext, $cubeCountRequest, $cubeResultFormatter);
    }

  /**
   * @param CubeQueryRequest $request
   * @throws IllegalStateException
   */
  protected function prepareCubeRequestMetaData(CubeQueryRequest $request) {
        $metamodel = data_controller_get_metamodel();

        $cube = $metamodel->getCube($request->getCubeName());
        $this->getDatasetMetaData($cube->sourceDatasetName);

        if (isset($request->referencedRequests)) {
            foreach ($request->referencedRequests as $referencedRequest) {
                $referencedCube = $metamodel->getCube($referencedRequest->getCubeName());
                $this->getDatasetMetaData($referencedCube->sourceDatasetName);
            }
        }
    }

  /**
   * @return PassthroughResultFormatter
   */
  protected function getDefaultResultFormatter() {
        return new PassthroughResultFormatter();
    }
}
