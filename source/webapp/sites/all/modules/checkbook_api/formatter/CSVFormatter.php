<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


/**
 * CSV formatter class
 */
class CSVFormatter extends AbstractFormatter {
  // protected $addHeaders;
  /**
   * @param $data_records
   * @param $response_columns
   * @param $configuration
   */
  function __construct($data_records, $response_columns, $configuration) {
    parent::__construct($data_records, $response_columns, $configuration);
  }

  /**
   * @return mixed|null
   */
  function formatData() {
    $formatted_data = NULL;
    $this->addHeaderColumns($formatted_data, $this->responseColumns);
    $this->addDataRecords($formatted_data, $this->responseColumns);

    return $formatted_data;
  }

  /**
   * @param $formatted_data
   * @param $headers
   */
  private function addHeaderColumns(&$formatted_data, $headers) {
    $formatted_data .= '"' . implode('","', $headers) . '"';
  }

  /**
   * @param $formatted_data
   * @param $headers
   */
  private function addDataRecords(&$formatted_data, $headers) {
    foreach ($this->dataRecords as $data_record) {
      $record_str = NULL;
      $first_column = 0;
      foreach ($headers as $header) {
        $data = $data_record[$this->configuration[$header]];
        $data = str_replace('"', chr(34) . '"', $data);
        if ($first_column == 0) {
          $record_str .= '"' . $data . '"';
          $first_column++;
        }
        else {
          $record_str .= ',"' . $data . '"';
        }
      }

      $record_str = str_replace(array("\r\n", "\n", "\r"), '', $record_str);
      $formatted_data .= PHP_EOL . $record_str;
    }
  }
}
