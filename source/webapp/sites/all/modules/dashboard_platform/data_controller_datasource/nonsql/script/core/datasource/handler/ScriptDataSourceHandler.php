<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class ScriptDataSourceHandler extends AbstractNonSQLDataSourceQueryHandler {

    const PARAMETER_NAME__OPERATION = 'exec';
    const PARAMETER_NAME__CALLBACK_SERVER_NAME = 'callback';

    private $converterJson2PHP = NULL;

    public function __construct($datasourceType, $extensions) {
        parent::__construct($datasourceType, $extensions);
        $this->converterJson2PHP = new Json2PHPArray();
    }

    protected function executeScriptFunction($functionName, DatasetMetaData $dataset, array $parameters = NULL) {
        $environment_metamodel = data_controller_get_environment_metamodel();

        $datasource = $environment_metamodel->getDataSource($dataset->datasourceName);

        // preparing query parameters
        $queryParameters = NULL;
        $queryParameters[self::PARAMETER_NAME__CALLBACK_SERVER_NAME] = $datasource->callbackServerName;
        $queryParameters[self::PARAMETER_NAME__OPERATION] = $functionName;
        $queryParameters[DataQueryControllerParameterNames::DATASET] = $dataset->name;
        // preparing version
        $scriptFileName = data_controller_datasource_script_get_script_file_name($dataset);
        $selectedVersion = data_controller_datasource_script_prepare_version($dataset, $scriptFileName);
        if (isset($selectedVersion)) {
            $queryParameters[DataQueryControllerParameterNames::DATASET_VERSION] = $selectedVersion;
        }
        ArrayHelper::mergeArrays($queryParameters, $parameters);
        // assembling query string
        $queryString = '';
        if (isset($queryParameters)) {
            foreach ($queryParameters as $name => $value) {
                if (strlen($queryString) > 0) {
                    $queryString .= '&';
                }
                $queryString .= $name . '=' . $value;
            }
        }
        // preparing final URI
        $uri = $datasource->host . $datasource->path . '/dp_datasource_integration.py';
        if (strlen($queryString) > 0) {
            $uri .= '?' . $queryString;
        }

        $ch = curl_init();
        try {
            curl_setopt($ch, CURLOPT_URL, $uri);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

            $output = curl_exec($ch);

            $error = curl_error($ch);
            if ($error != '') {
                throw new IllegalStateException(t(
                    "Could not execute a script for '@datasetName' dataset: @error",
                    array('@datasetName' => $dataset->publicName, '@error' => $error)));
            }

            if ($output === FALSE) {
                $output = NULL;
            }
            else {
                $output = StringHelper::trim($output);
            }

            $executionInfo = curl_getinfo($ch);

            // storing only some information about the execution into log
            $preparedExecutionInfo = NULL;
            ObjectHelper::copySelectedProperties(
                $preparedExecutionInfo, $executionInfo,
                array(
                    'http_code', 'namelookup_time', 'connect_time',
                    'pretransfer_time', 'starttransfer_time', 'size_upload', 'upload_content_length', 'speed_upload',
                    'speed_download', 'download_content_length', 'speed_download',
                    'total_time'));
            LogHelper::log_info($preparedExecutionInfo);
        }
        catch (Exception $e) {
            try {
                curl_close($ch);
            }
            catch (Exception $ne) {
                LogHelper::log_error($ne);
            }

            throw $e;
        }
        curl_close($ch);

        $records = NULL;
        if (isset($output)) {
            try {
                $this->validateResponse($functionName, $dataset, $output);

                $records = $this->converterJson2PHP->convert($output);
                if (isset($records)) {
                    if (count($records) == 0) {
                        $records = NULL;
                    }
                }
                else {
                    throw new IllegalStateException(t(
                        'Error occurred during execution of a script for the dataset: @datasetName',
                        array('@datasetName' => $dataset->publicName)));
                }
            }
            catch (Exception $e) {
                LogHelper::log_error(new PreservedTextMessage($output));
                throw $e;
            }
        }

        LogHelper::log_info(t('Received @count record(s)', array('@count' => count($records))));
        LogHelper::log_debug($records);

        return $records;
    }

    protected function validateResponse($functionName, DatasetMetaData $dataset, $responseBody) {}

    public function loadDatasetMetaData(DataControllerCallContext $callcontext, DatasetMetaData $dataset) {
        parent::loadDatasetMetaData($callcontext, $dataset);

        $loadedColumns = $this->executeScriptFunction('defineDatasetColumns', $dataset);

        $dataset->initializeColumnsFrom($loadedColumns);
    }

    public function queryDataset(DataControllerCallContext $callcontext, DatasetQueryRequest $request, ResultFormatter $resultFormatter) {
        $datasetName = $request->getDatasetName();
        LogHelper::log_notice(t('Querying script-based dataset: @datasetName', array('@datasetName' => $datasetName)));

        $metamodel = data_controller_get_metamodel();

        $dataset = $metamodel->getDataset($datasetName);

        // preparing parameters
        $parameters = NULL;
        // preparing column names
        ArrayHelper::mergeArrays(
            $parameters,
            DataQueryControllerUIRequestPreparer::serializeValue(
                DataQueryControllerParameterNames::COLUMNS,
                DataQueryControllerUIRequestPreparer::prepareColumns($request->columns)));
        // preparing query parameters
        if (isset($request->queries)) {
            if (count($request->queries) > 1) {
                throw new UnsupportedOperationException(t('Composite request is not supported yet for script-based dataset'));
            }
            ArrayHelper::mergeArrays(
                $parameters,
                DataQueryControllerUIRequestPreparer::serializeValue(
                    DataQueryControllerParameterNames::PARAMETERS,
                    DataQueryControllerUIRequestPreparer::prepareParameters($request->queries[0])));
        }
        // preparing columns names to sort result
        if ($request->sortingConfigurations) {
            $sortColumns = NULL;
            foreach ($request->sortingConfigurations as $sortingConfiguration) {
                $sortColumns[] = __PropertyBasedComparator_AbstractSortingConfiguration::assembleDirectionalPropertyName(
                    $sortingConfiguration->propertyName, $sortingConfiguration->isSortAscending);
            }
            ArrayHelper::mergeArrays(
                $parameters,
                DataQueryControllerUIRequestPreparer::serializeValue(
                    DataQueryControllerParameterNames::SORT,
                    DataQueryControllerUIRequestPreparer::prepareSortColumns($sortColumns)));
        }
        // preparing record offset
        if (isset($request->startWith) && ($request->startWith > 0)) {
            $parameters[DataQueryControllerParameterNames::OFFSET] = $request->startWith;
        }
        // preparing record limit
        if (isset($request->limit)) {
            $parameters[DataQueryControllerParameterNames::LIMIT] = $request->limit;
        }

        $records = $this->executeScriptFunction('queryDataset', $dataset, $parameters);
        if (isset($resultFormatter)) {
            $resultFormatter->reformatRecords($records);
        }

        return $records;
    }

    public function countDatasetRecords(DataControllerCallContext $callcontext, DatasetCountRequest $request, ResultFormatter $resultFormatter) {
        $datasetName = $request->getDatasetName();
        LogHelper::log_notice(t('Counting script-based dataset records: @datasetName', array('@datasetName' => $datasetName)));

        $metamodel = data_controller_get_metamodel();

        $dataset = $metamodel->getDataset($datasetName);

        // preparing parameters
        $parameters = NULL;
        // preparing query parameters
        if (isset($request->queries)) {
            if (count($request->queries) > 1) {
                throw new UnsupportedOperationException(t('Composite request is not supported yet for script-based dataset'));
            }
            ArrayHelper::mergeArrays(
                $parameters,
                DataQueryControllerUIRequestPreparer::serializeValue(
                    DataQueryControllerParameterNames::PARAMETERS,
                    DataQueryControllerUIRequestPreparer::prepareParameters($request->queries[0])));
        }

        $count = $this->executeScriptFunction('countDatasetRecords', $dataset, $parameters);

        return $count;
    }

    public function queryCube(DataControllerCallContext $callcontext, CubeQueryRequest $request, ResultFormatter $resultFormatter) {
        throw new UnsupportedOperationException();
    }

    public function countCubeRecords(DataControllerCallContext $callcontext, CubeQueryRequest $request, ResultFormatter $resultFormatter) {
        throw new UnsupportedOperationException();
    }
}
