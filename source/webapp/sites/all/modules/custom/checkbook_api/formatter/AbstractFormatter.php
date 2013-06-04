<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


/**
 * Base data formatter class
 */
abstract class AbstractFormatter {
  protected $dataRecords;
  protected $responseColumns;
  protected $configuration;

  /**
   * @param $data_records
   * @param $response_columns
   * @param $configuration
   */
  function __construct($data_records, $response_columns, $configuration) {
    $this->dataRecords = $data_records;
    $this->responseColumns = $response_columns;
    $this->configuration = $configuration;
  }

  /**
   * @abstract
   * @return mixed
   */
  abstract function formatData();
}
