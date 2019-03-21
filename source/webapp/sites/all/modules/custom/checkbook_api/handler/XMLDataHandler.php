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
        global $conf;
        $criteria = $this->requestSearchCriteria->getCriteria();

        //map tags and build sql
        $rootElement = $this->requestDataSet->displayConfiguration->xml->rootElement;
        $rowParentElement = $this->requestDataSet->displayConfiguration->xml->rowParentElement;
        $columnMappings = $this->requestDataSet->displayConfiguration->xml->elementsColumn;
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
        $where_part = substr($query,$end,strlen($query)-1);
        $select_part = str_replace("SELECT", "", $select_part);
        $sql_parts = explode(",", $select_part);

        $new_select_part = "'<".$rowParentElement.">'";
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
            $tag = $columnMappings[$column];
            $new_select_part .= "\n||'<".$tag.">' || ";
            switch($column) {
                case "prime_vendor_name":
                    $new_select_part .=  "CASE WHEN " . "COALESCE(CAST(" . $alias . $column . " AS VARCHAR),'')" . " IS NULL THEN 'N/A' ELSE " . $alias . $column . " END";
                    break;
                case "minority_type_name":
                    $new_select_part .=  "CASE \n";
                    $new_select_part .= "WHEN " . "COALESCE(CAST(" . $alias . $column . " AS VARCHAR),'')" . " = 2 THEN 'Black American' \n";
                    $new_select_part .= "WHEN " . "COALESCE(CAST(" . $alias . $column . " AS VARCHAR),'')" . " = 3 THEN 'Hispanic American' \n";
                    $new_select_part .= "WHEN " . "COALESCE(CAST(" . $alias . $column . " AS VARCHAR),'')" . " = 7 THEN 'Non-M/WBE' \n";
                    $new_select_part .= "WHEN " . "COALESCE(CAST(" . $alias . $column . " AS VARCHAR),'')" . " = 9 THEN 'Women' \n";
                    $new_select_part .= "WHEN " . "COALESCE(CAST(" . $alias . $column . " AS VARCHAR),'')" . " = 11 THEN 'Individuals and Others' \n";
                    $new_select_part .= "ELSE 'Asian American' END";
                    break;
                case "vendor_type":
                    $new_select_part .= "CASE WHEN " . "COALESCE(CAST(" . $alias . $column . " AS VARCHAR),'')" . " ~* 's' THEN 'Yes' ELSE 'No' END";
                    break;
                case "amount_basis_id":
                    $new_select_part .=  "CASE WHEN amount_basis_id = 1 THEN 'SALARIED' ELSE 'NON-SALARIED' END";
                    break;
                case "release_approved_year":
                    if($criteria['global']['type_of_data'] == 'Contracts_NYCHA'){
                      $new_select_part .= $criteria['value']['fiscal_year'];
                    }
                    break;
                case "hourly_rate":
                    if($this->requestDataSet->data_source == Datasource::NYCHA) {
                        $new_select_part .=  "''";
                    }
                    break;
                default:
                    $new_select_part .= "COALESCE(CAST(" . $alias . $column . " AS VARCHAR),'')";
                    break;
            }
            $new_select_part .= " || '</".$tag.">'";
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
            $fileDir = _checkbook_project_prepare_data_feeds_file_output_dir();
            $filename = _checkbook_project_generate_uuid(). '.xml';
            $tmpDir =  (isset($conf['check_book']['tmpdir']) && is_dir($conf['check_book']['tmpdir'])) ? rtrim($conf['check_book']['tmpdir'],'/') : '/tmp';

            $command = _checkbook_psql_command($this->requestDataSet->data_source);

            if(!is_writable($tmpDir)){
                LogHelper::log_error("$tmpDir is not writable. Please make sure this is writable to generate export file.");
                return $filename;
            }

            $tempOutputFile = $tmpDir .'/'. $filename;
            $formattedOutputFile = $tmpDir . '/formatted_' . $filename;
            $outputFile = DRUPAL_ROOT . '/' . $fileDir . '/' . $filename;
            $commands = array();

            //sql command
            $command = $command
                . " -c \"\\\\COPY (" . $query . ") TO '"
                . $tempOutputFile
                . "' \" ";
            $commands[] = $command;

            //prepend open tags command
            $command = "sed -i '1i " . $open_tags . "' " . $tempOutputFile;
            $commands[] = $command;

            //append close tags command
            $command = "sed -i '$"."a" . $close_tags . "' " . $tempOutputFile;
            $commands[] = $command;

            //escape '&' for xml compatibility
            $command = "sed -i 's/&/&amp;/g' " . $tempOutputFile;
            $commands[] = $command;

            //escape '<' for xml compatibility
            $command = "sed -i 's/</\&lt;/g' " . $tempOutputFile;
            $commands[] = $command;

            //escape '>' for xml compatibility
            $command = "sed -i 's/>/\&gt;/g' " . $tempOutputFile;
            $commands[] = $command;

            //put back the '<' tags
            $command = "sed -i 's/|LT|/</g' " . $tempOutputFile;
            $commands[] = $command;

            //put back the '>' tags
            $command = "sed -i 's/|GT|/>/g' " . $tempOutputFile;
            $commands[] = $command;

            //xmllint command to format the xml
            $maxmem = 1024 * 1024 * 500;  // 500 MB
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
        global $conf;

        // validateRequest:
        if (!$this->validateRequest()) {
            return $this->response;
        }

        $fileDir = variable_get('file_public_path','sites/default/files') . '/' . $conf['check_book']['data_feeds']['output_file_dir'];
        $fileDir .= '/' . $conf['check_book']['export_data_dir'];
        $file = DRUPAL_ROOT . '/' . $fileDir . '/' . $fileName;

        drupal_add_http_header("Content-Type", "text/xml; utf-8");
        drupal_add_http_header("Content-Disposition", "attachment; filename=nyc-data-feed.xml");
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
        return;
    }
}
