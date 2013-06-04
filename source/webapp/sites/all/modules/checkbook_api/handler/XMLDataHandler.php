<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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
