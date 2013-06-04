<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class CustomURLHelper
{

    /** Generate partial URL string with given parameter name and value */
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

    /** Prepares URL with given request parameters and custom path parameters */
    static function prepareUrl($path, $params=array(), $requestParams=array(), $customPathParams=array(), $applyPreviousYear=false, $applySpendingYear=false){
        $pathParams = explode('/',drupal_get_path_alias($_GET['q']));

        $url =  $path . _checkbook_project_get_year_url_param_string($applySpendingYear, $applyPreviousYear);

        if(is_array($params)){
            foreach($params as $key => $value){
                $url .=  self::get_url_param($pathParams,$key,$value);
            }
        }

        if(is_array($customPathParams)){
            foreach($customPathParams as $key => $value){
                $url .= "/$key";
                if(isset($value)){
                    $url .= "/$value";
                }
            }
        }

        if(is_array($requestParams) && !empty($requestParams)){
            $cnt = 0;
            foreach($requestParams as $key => $value){
                if($cnt == 0){
                    $url .= "?$key=$value";
                }else{
                    $url .= "&$key=$value";
                }

                $cnt++;
            }
        }

        return $url;
    }
}
