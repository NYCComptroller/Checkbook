<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
