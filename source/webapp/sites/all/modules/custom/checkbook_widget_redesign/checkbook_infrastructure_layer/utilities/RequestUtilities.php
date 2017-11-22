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

    //Returns the path of the current page
    static public function _getCurrentPage() {
        $currentUrl = explode('/',$_SERVER['HTTP_REFERER']);
        return '/'.$currentUrl[3];
    }

    /**
     * Returns key value pair string is present in URL
     * @param $key
     * @param null $key_alias
     * @return string
     */
    static function _getUrlParamString($key,$key_alias =  null){
        $urlPath = drupal_get_path_alias($_GET['q']);
        $pathParams = explode('/', $urlPath);
        $keyIndex = NULL;
        foreach($pathParams as $index => $value){
            if($key == $value){
                $keyIndex = $index;
            }else if($key_alias != null && $key_alias == $value && $value != null){
                $keyIndex = $index;
            }
        }

        if($keyIndex){
            if($key_alias == null){
                return "/$key/" . urlencode($pathParams[($keyIndex+1)]);
            }
            else{
                return "/$key_alias/" . urlencode($pathParams[($keyIndex+1)]);
            }
        }
        return '';
    }

    /**
     * Adds mwbe, subvendor and datasource parameters to url.  Precedence ,$source > $overidden_params > requestparam
     * @return string
     */
    static function _appendMWBESubVendorDatasourceUrlParams($source = null,$overidden_params = array(),$top_nav = false){
        $datasource = (isset($overidden_params['datasource'])) ? $overidden_params['datasource'] :_getRequestParamValue('datasource');
        $mwbe = (isset($overidden_params['mwbe'])) ? $overidden_params['mwbe'] : _getRequestParamValue('mwbe');
        $dashboard = (isset($overidden_params['dashboard'])) ? $overidden_params['dashboard'] : _getRequestParamValue('dashboard');

        $url = "";
        if(isset($datasource)) {
            $url = "/datasource/checkbook_oge";
        }
        else {
            $current_url = explode('/',$_SERVER['HTTP_REFERER']);
            if(($current_url[3] == 'contract' && ($current_url[4] == 'search' || $current_url[4] == 'all') && $current_url[5] == 'transactions')){
                $advanced_search = true;
            }
            if(!$advanced_search){
                if($source) {
                    $source = explode("/",$source);
                    if(!in_array("mwbe",$source)){
                        $url = isset($mwbe) ? "/mwbe/".$mwbe : "";
                    }
                    if(!in_array("dashboard",$source)){
                        $url = isset($dashboard) ? "/dashboard/".$dashboard : "";
                    }
                }
                else {
                    if(!$top_nav ||  ( isset($mwbe) && _getRequestParamValue('vendor') > 0 && _getRequestParamValue('dashboard') != "ms" )){
                        $url = isset($mwbe) ? "/mwbe/".$mwbe : "";
                        $url .= isset($dashboard) ? "/dashboard/".$dashboard : "";
                    }
                }
            }
        }
        return $url;
    }

    /** Checks if the current URL is opened in a new window */
    static function isNewWindow(){
        $referer = $_SERVER['HTTP_REFERER'];

        return preg_match('/newwindow/i',$referer);
    }

    function _checkbook_check_isEDCPage(){
        $database = self::getRequestParamValue('datasource');
        if(isset($database)){
            return true;
        }else{
            return false;
        }
    }

    static function get_url_param($pathParams,$key,$key_alias =  null){

        $keyIndex = array_search($key,$pathParams);
        if($keyIndex){
            if($key_alias == null){
                return "/$key/" . $pathParams[($keyIndex+1)];
            }
            else{
                return "/$key_alias/" . $pathParams[($keyIndex+1)];
            }
        }
        return NULL;

    }

    /**
     * This function returns the current NYC year  ...
     * @return year_id
     */
    static function getCurrentYearID(){
        STATIC $currentNYCYear;
        if(!isset($currentNYCYear)){
            if(variable_get('current_fiscal_year_id')){
                $currentNYCYear = variable_get('current_fiscal_year_id');
            }else{
                $currentNYCYear=date("Y");
                $currentMonth=date("m");
                if($currentMonth > 6 )
                    $currentNYCYear +=1;
                $currentNYCYear = _getYearIDFromValue($currentNYCYear);
            }
        }
        return $currentNYCYear;
    }
}