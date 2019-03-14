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
    $requested_response_columns = isset($criteria['responseColumns']) && !empty($criteria['responseColumns']) ? $criteria['responseColumns'] : array_keys($data_set_configured_columns);

    return new CSVFormatter($data_records, $requested_response_columns, $data_set_configured_columns);
  }

    /**
     * Given the query, creates a command to connect to the db and generate the output file, returns the filename
     * @param $query
     * @return string
     */
    function getJobCommand($query) {
        global $conf, $databases;

        LogHelper::log_notice("DataFeeds :: csv::getJobCommand()");

        //map csv headers
        $columnMappings = $this->requestDataSet->displayConfiguration->csv->elementsColumn;
        $columnMappings =  (array)$columnMappings;
        //Handle referenced columns
        foreach($columnMappings as $key=>$value) {
            if (strpos($value,"@") !== false) {
                $column_parts = explode("@", $value);
                $columnMappings[$key] = $column_parts[0];
            }
        }
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

            //Remove "AS"
            if (strpos($sql_part,"AS") !== false) {
                $pos = strpos($column, " AS");
                $sql_part = substr($sql_part,0,$pos);
            }
            //get only column
            if (strpos($sql_part,".") !== false) {
                $select_column_parts = explode('.', trim($sql_part));
                $alias = $select_column_parts[0] . '.';
                $column = $select_column_parts[1];
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
                    $new_column .= "WHEN " . $alias . $column . " = 4 THEN 'Asian American' \n";
                    $new_column .= "WHEN " . $alias . $column . " = 5 THEN 'Asian American' \n";
                    $new_column .= "WHEN " . $alias . $column . " = 7 THEN 'Non-M/WBE' \n";
                    $new_column .= "WHEN " . $alias . $column . " = 9 THEN 'Women' \n";
                    $new_column .= "WHEN " . $alias . $column . " = 11 THEN 'Individuals and Others' \n";
                    $new_column .= "WHEN " . $alias . $column . " = 'African American' THEN 'Black American' \n";
                    $new_column .= "WHEN " . $alias . $column . " = 'Hispanic American' THEN 'Hispanic American' \n";
                    $new_column .= "WHEN " . $alias . $column . " = 'Asian-Pacific' THEN 'Asian American' \n";
                    $new_column .= "WHEN " . $alias . $column . " = 'Asian-Indian' THEN 'Asian American' \n";
                    $new_column .= "WHEN " . $alias . $column . " = 'Non-Minority' THEN 'Non-M/WBE' \n";
                    $new_column .= "WHEN " . $alias . $column . " = 'Caucasian Woman' THEN 'Women' \n";
                    $new_column .= "WHEN " . $alias . $column . " = 'Individuals & Others' THEN 'Individuals and Others' END \n";
                    $new_select_part .= $new_column . ' AS \\"' . $columnMappings[$column] . '\\",' .  "\n";
                    break;
                case "vendor_type":
                    $new_column = "CASE WHEN " . $alias . $column . " ~* 's' THEN 'Yes' ELSE 'No' END";
                    $new_select_part .= $new_column . ' AS \\"' . $columnMappings[$column] . '\\",' .  "\n";
                    break;
                case "amount_basis_id":
                    $new_column = "CASE WHEN " . $alias . $column . " = 1 THEN 'Salaried' ELSE 'Non-Salaried' END";
                    $new_select_part .= $new_column . ' AS \\"' . $columnMappings[$column] . '\\",' .  "\n";
                    break;
                case "hourly_rate":
                    if($this->requestDataSet->data_source == Datasource::NYCHA) {
                        $new_column = "''";
                        $new_select_part .= $new_column . ' AS \\"' . $columnMappings[$column] . '\\",' . "\n";
                    }
                    break;
                default:
                    $new_select_part .= $alias . $column . ' AS \\"' . $columnMappings[$column] . '\\",' .  "\n";
                    break;
            }
        }
        $new_select_part = rtrim($new_select_part,",\n");
        $query = substr_replace($query, $new_select_part.' ', 0, $end);

        try{
            $fileDir = _checkbook_project_prepare_data_feeds_file_output_dir();
            $filename = _checkbook_project_generate_uuid(). '.csv';
            $tmpDir =  (isset($conf['check_book']['tmpdir']) && is_dir($conf['check_book']['tmpdir'])) ? rtrim($conf['check_book']['tmpdir'],'/') : '/tmp';
            LogHelper::log_notice("DataFeeds :: csv::getJobCommand() tmp dir: ".$tmpDir);


            $command = _checkbook_psql_command($this->requestDataSet->data_source);

            if(!is_writable($tmpDir)){
                LogHelper::log_error("$tmpDir is not writable. Please make sure this is writable to generate export file.");
                return $filename;
            }

            $tempOutputFile = $tmpDir .'/'. $filename;
            $outputFile = DRUPAL_ROOT . '/' . $fileDir . '/' . $filename;

            $cmd = $command
                . " -c \"\\\\COPY (" . $query . ") TO '"
                . $tempOutputFile
                . "'  WITH DELIMITER ',' CSV HEADER \" 2>&1";
            LogHelper::log_notice("DataFeeds :: csv::getJobCommand() cmd: ".$cmd);
            $out = shell_exec($cmd);
            LogHelper::log_notice("DataFeeds :: csv::getJobCommand() cmd out: ".var_export($out, true));

//            sleep(30);

            $move_cmd = "mv $tempOutputFile $outputFile 2>&1";
            LogHelper::log_notice("DataFeeds :: csv::getJobCommand() mv_cmd: ".$move_cmd);
            $out = shell_exec($move_cmd);
            LogHelper::log_notice("DataFeeds :: csv::getJobCommand() mv_cmd out: ".var_export($out, true));

        }
        catch (Exception $e){
            $value = TextLogMessageTrimmer::$LOGGED_TEXT_LENGTH__MAXIMUM;
            TextLogMessageTrimmer::$LOGGED_TEXT_LENGTH__MAXIMUM = NULL;

            LogHelper::log_error($e);
            $msg = "Command used to generate the file: " . $command ;
            $msg .= ("Error generating DB command: " . $e->getMessage());
            LogHelper::log_error($msg);

            TextLogMessageTrimmer::$LOGGED_TEXT_LENGTH__MAXIMUM = $value;
        }

        return $filename;
    }

    /**
     * Generates the API file based on the format specified
     * @param $fileName
     * @return mixed
     */
    function outputFile($fileName){
        global $conf;

        // validateRequest:
        if (!$this->validateRequest()) {
            return $this->response;
        }

        $fileDir = variable_get('file_public_path','sites/default/files') . '/' . $conf['check_book']['data_feeds']['output_file_dir'];
        $fileDir .= '/' . $conf['check_book']['export_data_dir'];
        $file = DRUPAL_ROOT . '/' . $fileDir . '/' . $fileName;

        drupal_add_http_header("Content-Type", "text/csv; utf-8");
        drupal_add_http_header("Content-Disposition", "attachment; filename=nyc-data-feed.csv");
        drupal_add_http_header("Pragma", "cache");
        drupal_add_http_header("Expires", "-1");

        if(is_file($file)) {
          $data = file_get_contents($file);
          drupal_add_http_header("Content-Length",strlen($data));
          echo $data;
        } else {
            echo "Data is not generated... Please contact support team.";
        }
        return;
    }
}
