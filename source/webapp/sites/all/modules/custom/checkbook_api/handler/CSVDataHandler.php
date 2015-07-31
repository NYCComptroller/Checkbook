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

    /**
     * Given the query, creates a command to connect to the db and generate the output file, returns the filename
     * @param $query
     * @return string
     */
    function getJobCommand($query) {
        global $conf;

        //map csv headers
        $columnMappings = $this->requestDataSet->displayConfiguration->csv->elementsColumn;
        $columnMappings =  (array)$columnMappings;
        $columnMappings = array_flip($columnMappings);
        $end = strpos($query, 'FROM');
        $select_part = substr($query,0,$end);
        $select_part = str_replace("SELECT", "", $select_part);

        $sql_parts = explode(",", $select_part);
        $new_select_part = "SELECT ";
        foreach($sql_parts as $sql_part) {

            $sql_part = trim($sql_part);
            $column = $sql_part;
            $alias = "";

            //get only column
            if (strpos($sql_part,".") !== false) {
                $alias = substr($sql_part, 0, 3);
                $column = substr($sql_part, 3);
            }

            //Handle derived columns
            switch($column) {
                case "prime_vendor_name":
                    $new_column = "CASE WHEN " . $alias . $column . " IS NULL THEN 'N/A' ELSE " . $alias . $column . " END";
                    $new_select_part .= $new_column . ' AS \\"' . $columnMappings[$column] . '\\",' .  "\n";
                    break;
                case "minority_type_name":
                    $new_column = "CASE \n";
                    $new_column .= "WHEN " . $alias . $column . " = 2 THEN 'Black American' \n";
                    $new_column .= "WHEN " . $alias . $column . " = 3 THEN 'Hispanic American' \n";
                    $new_column .= "WHEN " . $alias . $column . " = 7 THEN 'Non-M/WBE' \n";
                    $new_column .= "WHEN " . $alias . $column . " = 9 THEN 'Women' \n";
                    $new_column .= "WHEN " . $alias . $column . " = 11 THEN 'Individuals and Others' \n";
                    $new_column .= "ELSE 'Asian American' END";
                    $new_select_part .= $new_column . ' AS \\"' . $columnMappings[$column] . '\\",' .  "\n";
                    break;
                case "vendor_type":
                    $new_column = "CASE WHEN " . $alias . $column . " ~* 's' THEN 'Yes' ELSE 'No' END";
                    $new_select_part .= $new_column . ' AS \\"' . $columnMappings[$column] . '\\",' .  "\n";
                    break;
                default:
                    $new_select_part .= $alias . $column . ' AS \\"' . $columnMappings[$column] . '\\",' .  "\n";
                    break;
            }
        }
        $new_select_part = rtrim($new_select_part,",\n");
        $query = substr_replace($query, $new_select_part, 0, $end);

        $tmpDir = $conf['check_book']['tmpdir'];
        $outputFileDir = $conf['check_book']['data_feeds']['output_file_dir'];
        $command = $conf['check_book']['data_feeds']['command'];

        $filename = 'tmp_' . date('mdY_His') . '.csv';
        $fileOutputDir = variable_get('file_public_path', 'sites/default/files') . '/' . $outputFileDir;
        $tmpDir =  (isset($tmpDir) && is_dir($tmpDir)) ? rtrim($tmpDir,'/') : '/tmp';
        $tempOutputFile = $tmpDir .'/'. $filename;
        $outputFile = DRUPAL_ROOT . '/' . $fileOutputDir .'/'. $filename;


        $cmd = $command
            . " -c \"\\\\COPY (" . $query . ") TO '"
            . $tempOutputFile
            . "'  WITH DELIMITER ',' CSV HEADER \" ";

        log_error($cmd);
        shell_exec($cmd);

        $move_cmd = "mv $tempOutputFile $outputFile";
        shell_exec($move_cmd);

        return $filename;
    }

    /**
     * Generates the API file based on the format specified
     * @param $fileName
     * @return mixed
     */
    function outputFile($fileName){

        // validateRequest:
        if (!$this->validateRequest()) {
            return $this->response;
        }

        $file = variable_get('file_public_path', 'sites/default/files') . '/datafeeds/dev/' . $fileName;

        drupal_add_http_header("Content-Type", "text/csv; utf-8");
        drupal_add_http_header("Content-Disposition", "attachment; filename=nyc-data-feed.csv");
        drupal_add_http_header("Pragma", "cache");
        drupal_add_http_header("Expires", "-1");

        if(is_file($file)) {
            $data = file_get_contents($file);
            drupal_add_http_header("Content-Length",strlen($data));
            echo $data;
        }
        else {
            echo "Data is not generated. Please contact support team.";
        }
    }
}
