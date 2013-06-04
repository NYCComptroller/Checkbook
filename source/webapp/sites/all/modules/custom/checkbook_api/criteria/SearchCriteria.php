<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


/**
 * Class to construct search criteria
 */
class SearchCriteria extends AbstractAPISearchCriteria {

  /**
   * @param $criteria
   * @param $response_type
   */
  function __construct($criteria, $response_type) {
    $this->criteria = $criteria;
    $this->criteria['global']['response_format'] = $response_type;
  }

  /**
   * @return mixed
   */
  function getRequest() {
    return $this->criteria;
  }

  /**
   * @return int|mixed
   */
  function getMaxAllowedTransactionResults() {
    return 50000;
  }
}
