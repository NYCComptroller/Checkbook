<?php
namespace Drupal\checkbook_api\Handler;

use Drupal\checkbook_datafeeds\Utilities\FeedUtil;
use Symfony\Component\HttpFoundation\Response;
use DOMDocument;
use Drupal\checkbook_api\Formatter\XMLFormatter;
use Drupal\checkbook_log\LogHelper;
use Drupal\checkbook_api\Utilities\APIUtil;
use Drupal\data_controller_log\TextLogMessageTrimmer;
use Exception;

/**
 * Class for data handler for XML format
 */
class XMLDataHandler extends AbstractDataHandler
{

    function __construct($requestSearchCriteria){
        $this->requestSearchCriteria = $requestSearchCriteria;
    }

    function addResponseMessages(){
        //Add status messages
        $status = '<status>';
        $status .= '<result>'.($this->requestSearchCriteria->hasErrors() ? "failure" : "success").'</result>';


        if($this->requestSearchCriteria->hasErrors() || $this->requestSearchCriteria->hasMessages()){
            $errors = $this->requestSearchCriteria->getErrors();
            $status .= '<messages>';
            if(count($errors) > 0){//errors
                foreach($errors as $errorCode => $codeErrors){
                    foreach($codeErrors as $error){
                        $status .= '<message>';
                        $status .= '<code>'.$errorCode.'</code><description>'.$error.'</description>';
                        $status .= '</message>';
                    }
                }
            }

            $messages = $this->requestSearchCriteria->getMessages();
            if(count($messages) > 0){//messages
                foreach($messages as $msgCode => $codeMessages){
                    foreach($codeMessages as $message){
                        $status .= '<message>';
                        $status .= '<code>'.$msgCode.'</code><description>'.$message.'</description>';
                        $status .= '</message>';
                    }
                }
            }
            $status .= '</messages>';
        }

        $status .= '</status>';

        $this->response .= $status;

        //Add Search Criteria
        $requestCriteria = $this->requestSearchCriteria->getRequest();
        if($requestCriteria instanceof DOMDocument){
            $requestCriteria = '<request_criteria>';
            $requestCriteria .= $this->getDocumentString($this->requestSearchCriteria->getRequest());
            $requestCriteria .= '</request_criteria>';

            $this->response .= $requestCriteria;
        }
    }

    function prepareResponseResults($data_response){
        $responseResults = '<result_records>';
        $recordCount = $this->getRecordCount();
        $responseResults .= '<record_count>'.$recordCount.'</record_count>';

        //Add results
        if(isset($data_response)){
            $responseResults .= $data_response;
        }

        $responseResults .= '</result_records>';

        $this->response .= $responseResults;
    }

    private function getDocumentString(DOMDocument $document){
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;
        $documentStr = ($document != NULL && $document->hasChildNodes()) ? $document->saveXML(NULL, LIBXML_NOBLANKS) : NULL;

        // TODO - current 'LIBXML_NOXMLDECL' is not working.
        if(isset($documentStr)){
            $documentStr = str_replace('<?xml version="1.0"?>','',$documentStr);
        }

        return $documentStr;
    }

    function getDataSetResultFormatter($data_set, $data_records){
        $criteria = $this->requestSearchCriteria->getCriteria();
        $dataSetConfiguredColumns = get_object_vars($data_set->displayConfiguration->xml->elementsColumn);
        $requestedResponseColumns = isset($criteria['responseColumns']) && !empty($criteria['responseColumns'])  ? $criteria['responseColumns'] : array_keys($dataSetConfiguredColumns);

        return new XMLFormatter($data_records,$requestedResponseColumns,$data_set->displayConfiguration->xml,TRUE);
    }

    function initiateResponse(){
        $this->response = '<?xml version="1.0"?>';
        $this->response .= '<response>';
    }

    function closeResponse(){
        $this->response .= '</response>';
        $this->response = self::formatXmlString($this->response);
        parent::closeResponse();
    }


    /**
     * Function will reformat the xml document to have correct indention.
     *
     * @param $strXml
     * @return string
     */
    private function formatXmlString($strXml) {

        try {
            $doc = new DOMDocument();
            $doc->loadXML($strXml, LIBXML_NOBLANKS);
            $doc->preserveWhiteSpace = false;
            $doc->formatOutput = true;
        }
        catch (Exception $e) {
            LogHelper::log_error('Error formatting xml: ' . $e);
        }
        return ($doc != NULL && $doc->hasChildNodes()) ? $doc->saveXML() : NULL;
    }

    /**
     * Given the query, creates a command to connect to the db and generate the output file, returns the filename
     * @param $query
     * @return string
     */
    function getJobCommand($query) {
        LogHelper::log_notice("DataFeeds :: xml::getJobCommand()");

        //Adjust query if applicable (adjustSQL functions have $sql_query as parameter)
        if (isset($this->requestDataSet->adjustSql)) {
          $sql_query = $query;
          eval($this->requestDataSet->adjustSql);
          $query = $sql_query;
        }

        //map tags and build sql
        $rootElement = $this->requestDataSet->displayConfiguration->xml->rootElement;
        $rowParentElement = $this->requestDataSet->displayConfiguration->xml->rowParentElement;
        $columnMappings = $this->requestDataSet->displayConfiguration->xml->elementsColumn;
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
        $where_part = substr($query,$end,strlen($query)-1);
        $select_part = str_replace("SELECT", "", $select_part);

        //explode line below had separator as ",\n" but don't see newline in d9 $select_part, so added below
        //str_replace to remove "\n" in case there somehow \n in string, and changes explode separator
        $select_part=str_replace("\n","",$select_part);
        $sql_parts = explode(",", $select_part);

        $new_select_part = "'<".$rowParentElement.">'";
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
          $tag = $columnMappings[$data_set_column] ?? NULL;
          if ($tag) {
            $modified_select_part = "\n||'<".$tag.">' || ";
            $modified_select_part .= "regexp_replace(COALESCE(CAST(" . $sql_part . " AS VARCHAR),''), '[\u0080-\u00ff]', '', 'g')";
            $modified_select_part .= " || '</".$tag.">'";

            $new_select_part .= $modified_select_part;
          }
        }

        $new_select_part .= "||'</".$rowParentElement.">'";
        $new_select_part = "SELECT ".ltrim($new_select_part,"\n||")."\n";
        $query = $new_select_part;

        //open/close tags
        $open_tags = "<?xml version=\"1.0\"?><response><status><result>success</result></status>";
        $open_tags .= "<result_records><record_count>".$this->getRecordCount()."</record_count>";
        $open_tags .= "<".$rootElement.">";
        $close_tags = "</".$rootElement."></result_records></response>";

        //replace '<' and '>' to allow escaping of db columns with these tags
        $query = str_replace("<","|LT|",$query);
        $query = str_replace(">","|GT|",$query);
        $open_tags = str_replace("<","|LT|",$open_tags);
        $open_tags = str_replace(">","|GT|",$open_tags);
        $close_tags = str_replace("<","|LT|",$close_tags);
        $close_tags = str_replace(">","|GT|",$close_tags);
        $query .= $where_part;

        try{
            $fileDir = FeedUtil::_checkbook_project_prepare_data_feeds_file_output_dir();
            $filename = APIUtil::_checkbook_project_generate_uuid(). '.xml';

            $checkbook_tempdir = \Drupal::config('check_book')->get('tmpdir');
            $tmpDir =  (isset($checkbook_tempdir) && is_dir($checkbook_tempdir)) ? rtrim($checkbook_tempdir,'/') : '/tmp';

            $command = _checkbook_psql_command($this->requestDataSet->data_source);

            if(!is_writable($tmpDir)){
                LogHelper::log_error("$tmpDir is not writable. Please make sure this is writable to generate export file.");
                return $filename;
            }

            $tempOutputFile = $tmpDir .'/'. $filename;
            $formattedOutputFile = $tmpDir . '/formatted_' . $filename;
            $outputFile = \Drupal::root() . '/' . $fileDir . '/' . $filename;
            $commands = array();
            //LogHelper::log_notice("DataFeeds :: QueueJob::getXMLJobCommands() cmd: ".$outputFile);

            //sql command
            $command = $command
                . " -c \"\\\\COPY (" . $query . ") TO '"
                . $tempOutputFile
                . "' \" ";
            $commands[] = $command;

            LogHelper::log_notice("DataFeeds :: XML QUERY FOR > 10000 records: ".$command);

            $this->processCommands($commands);

            $commands = [];

            //prepend open tags command - (replaces the following command:- "sed -i '1i " . $open_tags . "' " . $tempOutputFile;)
            APIUtil::prependToFile($tempOutputFile,$open_tags);

            //append close tags command - (replaces the following command:- "sed -i '$"."a" . $close_tags . "' " . $tempOutputFile;)
            APIUtil::appendToFile($tempOutputFile,$close_tags);

            //escape '&' for xml compatibility - (replaces the following command:- "sed -i 's/&/&amp;/g' " . $tempOutputFile;)
            APIUtil::replaceInFile($tempOutputFile, '&', '&amp;');

            //escape '<' for xml compatibility - (replaces the following command:- "sed -i 's/</\&lt;/g' " . $tempOutputFile;)
            APIUtil::replaceInFile($tempOutputFile, '<', '&lt;');

            //escape '>' for xml compatibility - (replaces the following command:- "sed -i 's/>/\&gt;/g' " . $tempOutputFile;)
            APIUtil::replaceInFile($tempOutputFile, '>', '&gt;');

            //put back the '<' tags - (replaces the following command:- "sed -i 's/|LT|/</g' " . $tempOutputFile;)
            APIUtil::replaceInFile($tempOutputFile, '|LT|', '<');

            //put back the '>' tags - (replaces the following command:- "sed -i 's/|GT|/>/g' " . $tempOutputFile;)
            APIUtil::replaceInFile($tempOutputFile, '|GT|', '>');

            //xmllint command to format the xml
            $maxmem = 1024 * 1024 * 750;  // 750 MB
            $command = "xmllint --format $tempOutputFile --output $formattedOutputFile --maxmem $maxmem";
            $commands[] = $command;

            //Move file from tmp to data feeds dir
            $command = "mv $formattedOutputFile $outputFile";
            $commands[] = $command;

            //Remove tmp file file from tmp to data feeds dir
            $command = "rm $tempOutputFile";
            $commands[] = $command;
            $this->processCommands($commands);
        }
        catch (Exception $e){
            $value = TextLogMessageTrimmer::$LOGGED_TEXT_LENGTH__MAXIMUM;
            TextLogMessageTrimmer::$LOGGED_TEXT_LENGTH__MAXIMUM = NULL;
            LogHelper::log_error($e);
            TextLogMessageTrimmer::$LOGGED_TEXT_LENGTH__MAXIMUM = $value;
        }
        return $filename;
    }

    /**
     * Executes the shell commands with error logging
     * @param $commands
     */
    private function processCommands($commands) {
        $current_command = "";
        try {
            foreach($commands as $command) {
                $current_command = $command;
                shell_exec($command);
            }
        }
        catch (Exception $e){
            $value = TextLogMessageTrimmer::$LOGGED_TEXT_LENGTH__MAXIMUM;
            TextLogMessageTrimmer::$LOGGED_TEXT_LENGTH__MAXIMUM = NULL;

            LogHelper::log_error($e);
            $msg = "Command used to generate the file: " . $current_command ;
            $msg .= ("Error generating DB command: " . $e->getMessage());
            LogHelper::log_error($msg);

            TextLogMessageTrimmer::$LOGGED_TEXT_LENGTH__MAXIMUM = $value;
        }
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
        $response->headers->set("Content-Type", "text/xml; utf-8");
        $response->headers->set("Content-Disposition", "attachment; filename=nyc-data-feed.xml");
        $response->headers->set("Pragma", "cache");
        $response->headers->set("Expires", "-1");


        if(is_file($file)) {
          $data = file_get_contents($file);
          $response->headers->set("Content-Length",strlen($data));
          $response->setContent($data);
          $response->send();
        }
        else {
          $data = "Data is not generated... Please contact support team.";
          $response->headers->set("Content-Length",strlen($data));
          $response->setContent($data);
          $response->send();
        }
        return FALSE;
    }
}
