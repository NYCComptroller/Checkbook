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
 * Class for data handler for CSV format
 */
class CSVDataHandler extends AbstractDataHandler {

  /**
   * @param $request_search_criteria
   */
  function __construct($request_search_criteria) {
    $this->requestSearchCriteria = $request_search_criteria;
  }

  /**
   * @return mixed|void
   */
  function addResponseMessages() {
    // Add status messages:
    $status = NULL;

    if ($this->requestSearchCriteria->hasErrors() || $this->requestSearchCriteria->hasMessages()) {
      // Errors:
      $errors = $this->requestSearchCriteria->getErrors();
      if (count($errors) > 0) {
        // Errors:
        $status .= "Records could not be retreived due to following errors:" . PHP_EOL;
        $status .= '"Code","Description"' . PHP_EOL;
        foreach ($errors as $error_code => $code_errors) {
          foreach ($code_errors as $error) {
            $status .= '"' . $error_code . '","' . $error . '"' . PHP_EOL;
          }
        }
      }

      $messages = $this->requestSearchCriteria->getMessages();
      if (count($messages) > 0) {
        // Messages:
        $status .= "No Results found:" . PHP_EOL;
        $status .= '"Code","Description"' . PHP_EOL;
        foreach ($messages as $msg_code => $code_messages) {
          foreach ($code_messages as $message) {
            $status .= '"' . $msg_code . '","' . $message . '"' . PHP_EOL;
          }
        }
      }
    }

    $this->response .= $status;
  }

  /**
   * @param $data_response
   * @return mixed|void
   */
  function prepareResponseResults($data_response) {
    $this->response .= $data_response;
  }

  /**
   * @param $data_set
   * @param $data_records
   * @return CSVFormatter|mixed
   */
  function getDataSetResultFormatter($data_set, $data_records) {
    $criteria = $this->requestSearchCriteria->getCriteria();
    $data_set_configured_columns = get_object_vars($data_set->displayConfiguration->csv->elementsColumn);
    $requested_response_columns = isset($criteria['responseColumns']) ? $criteria['responseColumns'] : array_keys($data_set_configured_columns);

    return new CSVFormatter($data_records, $requested_response_columns, $data_set_configured_columns);
  }
}
