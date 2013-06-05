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
