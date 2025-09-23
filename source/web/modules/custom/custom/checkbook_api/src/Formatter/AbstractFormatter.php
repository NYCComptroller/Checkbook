<?php
namespace Drupal\checkbook_api\Formatter;

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
