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


/**
 * Base class for data handler.
 *
 * Validates requests, prepares data as response.
 */
abstract class AbstractDataHandler {
  protected $requestSearchCriteria;
  protected $requestDataSet;
  //protected $configuration;
  protected $response;
  protected $validRequest;

  /**
   * @return mixed
   */
  function execute() {

    // validateRequest:
    if (!$this->validateRequest()) {
      return $this->response;
    }

    if (!isset($this->requestDataSet)) {
      // Prepare dataSet:
      $this->setRequestDataSet();
    }

    // Load data:
    $data_records = $this->getDataRecords();

      if (isset($this->requestDataSet->adjustDataSetResults)) {
          eval($this->requestDataSet->adjustDataSetResults);
      }

    // Format Data:
    $data_response = NULL;
    if (is_array($data_records) && count($data_records) > 0) {
      $data_set_result_formatter = $this->getDataSetResultFormatter($this->requestDataSet, $data_records);
      $data_response = $data_set_result_formatter->formatData();
    }

    if (!isset($data_records)) {
      $this->requestSearchCriteria->addMessage(1, array());
    }

    // addResponseMessages:
    $this->addResponseMessages();

    // Prepare response:
    $this->prepareResponseResults($data_response);

    // Close response:
    $this->closeResponse();

    return $this->response;
  }

  /**
   * Logs the request in the custom_queue_job/custom_queue_request tables.
   * If the request is the same for a given date, only add entry to custom_queue_request.
   * @param $email
   * @return string
   * @throws Exception
   */
  function queueRequest($email) {
    try {
      // validateRequest:
      if (!$this->validateRequest()) {
        return $this->response;
      }

      if (!isset($this->requestDataSet)) {
        // Prepare dataSet:
        $this->setRequestDataSet();
      }

      $queue_request_token = NULL;

    // Get queue request:
    $queue_criteria = $this->getQueueCriteria($this->requestSearchCriteria->getCriteria());
    $queue_search_results = QueueUtil::searchQueue($email, $queue_criteria);

    // Different user, same request, add entry in custom_queue_request only
    if (isset($queue_search_results['job_id'])) {
        // Generate Token:
        $token = $this->generateToken();
        // Create queue request:
        QueueUtil::createQueueRequest($token, $email, $queue_search_results['job_id']);
        //Update the last_update_date of the existing job
        QueueUtil::updateJobTimestamp($queue_search_results);
        return $token;
    }

      $sql_query = get_db_query(TRUE, $this->requestDataSet->name, $this->requestDataSet->columns,
        $this->requestDataSet->parameters, $this->requestDataSet->sortColumn, $this->requestDataSet->startWith, $this->requestDataSet->limit, NULL);

        if (isset($this->requestDataSet->adjustSql)) {
            eval($this->requestDataSet->adjustSql);
        }

      $token = $this->generateToken();

      $criteria = $this->requestSearchCriteria->getCriteria();
      // Prepare new queue request:
      $queue_request['token'] = $token;
      $queue_request['email'] = $email;
      $queue_request['name'] = strtolower($criteria['global']['type_of_data']);
      $queue_request['request'] = $queue_criteria;
      $queue_request['request_criteria'] = json_encode($criteria);
      if ($this->requestSearchCriteria->getUserCriteria()) {
        $queue_request['user_criteria'] = json_encode($this->requestSearchCriteria->getUserCriteria());
      }
      $queue_request['data_command'] = $sql_query;

      QueueUtil::createNewQueueRequest($queue_request);

      return $token;
    }
    catch (Exception $e) {
      LogHelper::log_error('Error Processing Queue Request: ' . $e);
      throw new Exception('Error Processing Queue Request.');
    }
  }


    /**
     * For records under 200,000, user will download the file immediately.
     * An entry will be made in both the custom_queue_job & custom_queue_request.
     *
     * @return string
     * @throws Exception
     */
    function queueImmediateRequest() {
        try {
            // validateRequest:
            if (!$this->validateRequest()) {
                return $this->response;
            }

            if (!isset($this->requestDataSet)) {
                // Prepare dataSet:
                $this->setRequestDataSet();
            }

            $queue_request_token = NULL;

            // Get queue request:
            $queue_criteria = $this->getQueueCriteria($this->requestSearchCriteria->getCriteria());

            $sql_query = get_db_query(TRUE, $this->requestDataSet->name, $this->requestDataSet->columns,
                $this->requestDataSet->parameters, $this->requestDataSet->sortColumn, $this->requestDataSet->startWith, $this->requestDataSet->limit, NULL);

            if (isset($this->requestDataSet->adjustSql)) {
                eval($this->requestDataSet->adjustSql);
            }

            $token = $this->generateToken();

            $criteria = $this->requestSearchCriteria->getCriteria();
            // Prepare new queue request:
            $queue_request['token'] = $token;
            $queue_request['name'] = strtolower($criteria['global']['type_of_data']);
            $queue_request['request'] = $queue_criteria;
            $queue_request['request_criteria'] = json_encode($criteria);
            $queue_request['status'] = 4; // N/A - no file to generate
            if ($this->requestSearchCriteria->getUserCriteria()) {
                $queue_request['user_criteria'] = json_encode($this->requestSearchCriteria->getUserCriteria());
            }
            $queue_request['data_command'] = $sql_query;

            QueueUtil::createImmediateNewQueueRequest($queue_request);

            return $token;
        }
        catch (Exception $e) {
            LogHelper::log_error('Error Processing Queue Request: ' . $e);
            throw new Exception('Error Processing Queue Request.');
        }
    }

  /**
   * @param array $criteria
   * @return null|string
   */
  private function getQueueCriteria(array $criteria = array()) {
    $output = NULL;
    if (empty($criteria)) {
      return $output;
    }
    else {
      ksort($criteria);
      foreach ($criteria as $key => $value) {
        $output .= "/$key";
        if (is_array($value)) {
          $output .= $this->getQueueCriteria($value);
        }
        else {
          $output .= "/$value";
        }
      }
    }
    return $output;
  }

  /**
   * @return string
   */
  private function generateToken() {
    $token = '';

    // This variable contains the list of allowable characters for the
    // password. Note that the number 0 and the letter 'O' have been
    // removed to avoid confusion between the two. The same is true
    // of 'I', 1, and 'l'.
    $allowable_characters = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    // Zero-based count of characters in the allowable list:
    $len = strlen($allowable_characters) - 1;

    for ($i = 0; $i < 10; $i++) {
      // Each iteration, pick a random character from the
      // allowable string and append it to the password:
      $token .= $allowable_characters[mt_rand(0, $len)];
    }

    return $token;
  }

  /**
   * @return int
   */
  function getRecordCount() {
    // validateRequest:
    if (!$this->validateRequest()) {
      return $this->response;
    }

    if (!isset($this->requestDataSet)) {
      // Prepare dataSet:
      $this->setRequestDataSet();
    }

    $record_count = get_db_result_count(TRUE, $this->requestDataSet->name,
      $this->requestDataSet->columns, $this->requestDataSet->parameters, NULL);

    return $record_count;
  }

  /**
   * @return mixed
   */
  function validateRequest() {
    if (isset($this->validRequest)) {
      return $this->validRequest;
    }

    // Initiate response:
    $this->initiateResponse();

    // Validate criteria:
    $this->requestSearchCriteria->validateCriteria();

    if ($this->requestSearchCriteria->hasErrors()) {
      $this->validRequest = FALSE;
      $this->addResponseMessages();
      // Close response:
      $this->closeResponse();
    }
    else {
      $this->validRequest = TRUE;
    }

    return $this->validRequest;
  }

  /**
   * @return mixed
   */
  function getErrorResponse() {
    return $this->response;
  }

  /**
   * @return DB
   */
  private function getDataRecords() {
    DateDataTypeHandler::$MASK_CUSTOM = 'Y-m-d';
    ini_set('max_execution_time',0);
    $sql_query = get_db_query(TRUE, $this->requestDataSet->name, $this->requestDataSet->columns,
      $this->requestDataSet->parameters, $this->requestDataSet->sortColumn, $this->requestDataSet->startWith, $this->requestDataSet->limit, NULL);

    //Get Data Source from Data set name
    $dataset_name = explode(':', $this->requestDataSet->name);
    $data_source = $dataset_name[0];
    if (isset($this->requestDataSet->adjustSql)) {
      eval($this->requestDataSet->adjustSql);
    }
    $records = _checkbook_project_execute_sql_by_data_source($sql_query, $data_source);

    return $records;
  }

  /**
   *
   */
  private function setRequestDataSet() {
    // Load request configuration:
    $configuration = $this->loadConfiguration();
    $data_set_handler = new DataSetHandler($this->requestSearchCriteria, $configuration);
    $this->requestDataSet = $data_set_handler->prepareDataSet();
  }

  /**
   * @return mixed
   */
  private function loadConfiguration() {
    // TODO - Avoid loading twice. Already loaded in search criteria.
    $criteria = $this->requestSearchCriteria->getCriteria();
    $configuration = ConfigUtil::getConfiguration($criteria['global']['type_of_data'], $this->requestSearchCriteria->getConfigKey());
    return $configuration;
  }

    /**
     * Calls db query and generates the file
     * @return string
     * @throws Exception
     */
    function generateFile() {
        $isList = TRUE;
        $dataSetName = $this->requestDataSet->name;
        $columns = $this->requestDataSet->columns;
        $parameters = $this->requestDataSet->parameters;
        $orderBy = $this->requestDataSet->sortColumn;
        $startWith = $this->requestDataSet->startWith;
        $limit = $this->requestDataSet->limit;
        $resultFormatter = null;

        $query = widget_get_db_query($isList, $dataSetName, $columns, $parameters, $orderBy, $startWith, $limit, $resultFormatter);
log_error($query);
        LogHelper::log_notice("DataFeeds :: generateFile() query: ".$query);

        $filename = $this->getJobCommand($query);

        return $filename;
    }

  /**
   *
   */
  function initiateResponse() {
  }

  /**
   *
   */
  function closeResponse() {
  }

  /**
   * @abstract
   * @return mixed
   */
  abstract function addResponseMessages();

  /**
   * @abstract
   * @param $data_set
   * @param $data_records
   * @return mixed
   */
  abstract function getDataSetResultFormatter($data_set, $data_records);

  /**
   * @abstract
   * @param $data_response
   * @return mixed
   */
  abstract function prepareResponseResults($data_response);

    /**
     * Generates the API file based on the format specified
     * @param $fileName
     * @return mixed
     */
    abstract function outputFile($fileName);

    /**
     * Given the query, creates a command to connect to the db and generate the output file, returns the filename
     * @param $query
     * @return string
     */
    abstract function getJobCommand($query);
}
