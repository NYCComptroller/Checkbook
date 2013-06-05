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
 * Class to construct to queue requests
 */
class QueueCriteria extends SearchCriteria {
  private $userCriteria;

  function __construct($criteria, $response_type) {
    parent::__construct($criteria, $response_type);
  }

  /**
   * @param int $records_from
   * @param int $max_records
   */
  protected function validateResultRecordRange($records_from, $max_records) {
    $limit_param_errors = array();
    if (isset($records_from) && (!is_numeric($records_from) || !is_int($records_from + 0) || $records_from < 1)) {
      $limit_param_errors[1002][] = t(Messages::$message[1002], array(
        '@value' => $records_from,
        '@paramName' => 'records_from'
      ));
    }
    else {
      // Reducing since DB row starts at 0:
      $this->criteria['global']['records_from'] = isset($records_from) ? $records_from - 1 : 0;
    }

    if (isset($max_records) && (!is_numeric($max_records) || !is_int($max_records + 0) || $max_records < 1)) {
      $limit_param_errors[1002][] = t(Messages::$message[1002], array(
        '@value' => $max_records,
        '@paramName' => 'max_records',
      ));
    }

    if (!empty($limit_param_errors)) {
      $this->addErrors($limit_param_errors);
    }
  }

  /**
   * @return int|null
   */
  function getMaxAllowedTransactionResults() {
    return NULL;
  }

  /**
   * @param array $userCriteria
   */
  function setUserCriteria(array $userCriteria) {
    $this->userCriteria = $userCriteria;
  }

  /**
   * @return mixed
   */
  function getUserCriteria() {
    return $this->userCriteria;
  }
}
