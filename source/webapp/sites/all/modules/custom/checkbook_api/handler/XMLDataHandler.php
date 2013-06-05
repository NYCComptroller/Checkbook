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

    private function getDocumentString($document){
        //TODO - current 'LIBXML_NOXMLDECL' is not working
        $documentStr = ($document != NULL && $document->hasChildNodes()) ? $document->saveXML(NULL,LIBXML_NOXMLDECL) : NULL;

        if(isset($documentStr)){
            $documentStr = str_replace('<?xml version="1.0"?>','',$documentStr);
        }

        return $documentStr;
    }

    function getDataSetResultFormatter($data_set, $data_records){
        $criteria = $this->requestSearchCriteria->getCriteria();
        $dataSetConfiguredColumns = get_object_vars($data_set->displayConfiguration->xml->elementsColumn);
        $requestedResponseColumns = isset($criteria['responseColumns']) ? $criteria['responseColumns'] : array_keys($dataSetConfiguredColumns);

        return new XMLFormatter($data_records,$requestedResponseColumns,$data_set->displayConfiguration->xml,TRUE);
    }

    function initiateResponse(){
        $this->response = '<?xml version="1.0"?>';
        $this->response .= '<response>';
    }

    function closeResponse(){
        $this->response .= '</response>';
        parent::closeResponse();
    }
}
