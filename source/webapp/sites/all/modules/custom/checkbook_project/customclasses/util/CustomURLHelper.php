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


class CustomURLHelper
{

    static function get_url_param($pathParams,$key,$key_alias =  null){

        if (!is_array($pathParams)) {
          return NULL;
        }
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

    static function prepareUrl($path, $params=array(), $requestParams=array(), $customPathParams=array(), $applyPreviousYear=false, $applySpendingYear=false){
        $pathParams = explode('/',drupal_get_path_alias($_GET['q']));

        $url =  $path . _checkbook_append_url_params() . _checkbook_project_get_year_url_param_string($applySpendingYear, $applyPreviousYear);

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
