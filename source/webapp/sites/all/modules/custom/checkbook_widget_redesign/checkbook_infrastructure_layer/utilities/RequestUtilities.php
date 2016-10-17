<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class RequestUtilities {
        
    /**
     * Checks if the page is Checkbook or Checkbook OGE (EDC)
     * @return True if the page is EDC
     */
    static public function isEDCPage(){
        $database = _getRequestParamValue('datasource');
        if(isset($database)){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * returns request parameter value from URL($_REQUEST['q'])
     * @param string $paramName
     * @return request parameter value
     */
    static public function getRequestParamValue($paramName, $fromRequestPath = TRUE){
        if(empty($paramName)){
            return NULL;
        }
        $value = NULL;
        if($fromRequestPath){
          $urlPath = drupal_get_path_alias($_GET['q']);
          $pathParams = explode('/', $urlPath);
          $index = array_search($paramName,$pathParams);
          if($index !== FALSE){
              $value =  filter_xss($pathParams[($index+1)]);
          }
          if(trim($value) == ""){
            return NULL;
          }
          if(isset($value) || $fromRequestPath){
              return htmlspecialchars_decode($value,ENT_QUOTES);
          }
        }else{
          return filter_xss(htmlspecialchars_decode($_GET[$paramName],ENT_QUOTES));
        }
    }
}