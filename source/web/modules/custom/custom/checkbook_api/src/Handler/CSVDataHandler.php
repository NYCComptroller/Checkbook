<?php
namespace Drupal\checkbook_api\Handler;

use Drupal\checkbook_datafeeds\Utilities\FeedUtil;
use Symfony\Component\HttpFoundation\Response;
use Drupal\checkbook_api\Formatter\CSVFormatter;
use Drupal\checkbook_log\LogHelper;
use Drupal\checkbook_api\Utilities\APIUtil;
use Drupal\data_controller_log\TextLogMessageTrimmer;
use Exception;

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
        LogHelper::log_notice("DataFeeds :: csv::getJobCommand()");

        //Adjust query if applicable (adjustSQL functions have $sql_query as parameter)
        if (isset($this->requestDataSet->adjustSql)) {
          $sql_query = $query;
          eval($this->requestDataSet->adjustSql);
          $query = $sql_query;
        }

        //map csv headers
        $columnMappings = $this->requestDataSet->displayConfiguration->csv->elementsColumn;
        $columnMappings =  (array)$columnMappings;
        //Handle referenced columns
        foreach($columnMappings as $key=>$value) {
          if (strpos($value,"@") !== false) {
            $data_set_column = str_replace('@', '_', $value);
            $data_set_column = str_replace(':', '_', $data_set_column);
            $columnMappings[$key] = $data_set_column;
          }
        }

        $columnMappings = array_flip($columnMappings);

        $end = strpos($query, 'FROM');
        $select_part = substr($query, 0, $end);
        $select_part = str_replace("SELECT", "", $select_part);

        // $sql_parts = explode(",", $select_part);
        $sql_parts  = $this->specialExplodeIgnoreParentheses($select_part);

        $new_select_part = "SELECT ";

        foreach($sql_parts as $sql_part) {
            $sql_part = trim($sql_part);
            $column = $sql_part;

            // Remove "AS".
            $selectColumn = NULL;
            if (strripos($sql_part," AS") !== FALSE) {
                $pos = strripos($column, " AS");
                //Get Column name from derived columns
                $selectColumn = trim(substr($sql_part, $pos + strlen(" AS")));
                $sql_part = substr($sql_part, 0, $pos);
            }

            // Get only column.
            if (strpos($sql_part,".") !== FALSE) {
              $select_column_parts = explode('.', trim($sql_part), 2);
              $column = $select_column_parts[1];
            }
            else {
              $column = $sql_part;
            }

          $data_set_column = isset($selectColumn) ? $selectColumn : $column;
          if (isset($columnMappings[$data_set_column])) {
            $new_select_part .= $sql_part . ' AS \\"' . $columnMappings[$data_set_column] . '\\",' .  "\n";
          }
        }
        $new_select_part = rtrim($new_select_part,",\n");
        $query = substr_replace($query, $new_select_part.' ', 0, $end);

        try {
            $fileDir = FeedUtil::_checkbook_project_prepare_data_feeds_file_output_dir();
            $filename = APIUtil::_checkbook_project_generate_uuid(). '.csv';

            $checkbook_tempdir = \Drupal::config('check_book')->get('tmpdir');
            $tmpDir =  (isset($checkbook_tempdir) && is_dir($checkbook_tempdir)) ? rtrim($checkbook_tempdir,'/') : '/tmp';

            LogHelper::log_notice("DataFeeds :: csv::getJobCommand() tmp dir: ".$tmpDir);

            $command = _checkbook_psql_command($this->requestDataSet->data_source);

            if(!is_writable($tmpDir)){
                LogHelper::log_error("$tmpDir is not writable. Please make sure this is writable to generate export file.");
                return $filename;
            }

            $tempOutputFile = $tmpDir .'/'. $filename;
            $outputFile = \Drupal::root() . '/' . $fileDir . '/' . $filename;

            $cmd = $command
                . " -c \"\\\\COPY (" . $query . ") TO '"
                . $tempOutputFile
                . "'  WITH DELIMITER ',' CSV HEADER \" 2>&1";
            LogHelper::log_notice("DataFeeds :: csv::getJobCommand() cmd: ".$cmd);

            $out = shell_exec($cmd);
            LogHelper::log_notice("DataFeeds :: csv::getJobCommand() cmd out: ".var_export($out, true));

            $move_cmd = "mv $tempOutputFile $outputFile 2>&1";
            LogHelper::log_notice("DataFeeds :: csv::getJobCommand() mv_cmd: ".$move_cmd);
            $out = shell_exec($move_cmd);
            LogHelper::log_notice("DataFeeds :: csv::getJobCommand() mv_cmd out: ".var_export($out, true));
        }
        catch (Exception $e) {
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
   * This function separates string by comma and ignore parentheses and its content.
   *
   * added for ticket NYCCHKBK-12747 - the SQL query's select was using functions that had commas and
   * separating by `,` was not giving correct explode
   *
   * @param $string - string to explode
   *
   * @return string[]
   */
  function specialExplodeIgnoreParentheses($string) {
    $level = 0;       // number of nested sets of brackets
    $ret = array(''); // array to return
    $cur = 0;         // current index in the array to return, for convenience

    for ($i = 0; $i < strlen($string); $i++) {
      switch ($string[$i]) {
        case '(':
          $level++;
          $ret[$cur] .= '(';
          break;
        case ')':
          $level--;
          $ret[$cur] .= ')';
          break;
        case ',':
          if ($level == 0) {
            $cur++;
            $ret[$cur] = '';
            break;
          }
        // else fallthrough
        default:
          $ret[$cur] .= $string[$i];
      }
    }

    return $ret;
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

        $fileDir = \Drupal::state()->get('file_public_path','sites/default/files') . '/' . \Drupal::config('check_book')->get('data_feeds')['output_file_dir'];

        $fileDir .= '/' . \Drupal::config('check_book')->get('export_data_dir');
        $file = \Drupal::root() . '/' . $fileDir . '/' . $fileName;

        $response = new Response();
        $response->headers->set("Content-Type", "text/csv; utf-8");
        $response->headers->set("Content-Disposition", "attachment; filename=nyc-data-feed.csv");
        $response->headers->set("Pragma", "cache");
        $response->headers->set("Expires", "-1");
        // drupal_add_http_header("Content-Type", "text/csv; utf-8");
        //drupal_add_http_header("Content-Disposition", "attachment; filename=nyc-data-feed.csv");
        //drupal_add_http_header("Pragma", "cache");
        //drupal_add_http_header("Expires", "-1");

        if(is_file($file)) {
          $data = file_get_contents($file);
          //drupal_add_http_header("Content-Length",strlen($data));
          $response->headers->set("Content-Length",strlen($data));
          $response->setContent($data);
          //echo $data;
          $response->send();
        } else {
            //echo "Data is not generated... Please contact support team.";
            $data = "Data is not generated... Please contact support team.";
            $response->headers->set("Content-Length",strlen($data));
            $response->setContent($data);
            $response->send();
        }
        return null;
    }
}
