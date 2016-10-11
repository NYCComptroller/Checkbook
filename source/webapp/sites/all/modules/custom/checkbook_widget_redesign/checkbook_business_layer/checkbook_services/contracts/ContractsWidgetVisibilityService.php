<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ContractsWidgetVisibilityService {
    /**
     * returns the view to be displayed
     * @param string $widget
     * @return view name to be displayed
     */
    static function getWidgetVisibility($widget) {
        $dashboard = self::getRequestParamValue('dashboard');
        $datasouce = self::getRequestParamValue('datasouce');
        $category = self::contractCategory();

        switch($widget){
            case 'departments':
                if(self::getRequestParamValue('agency')){
                    if($category['category'] === 'expense' && $category['status'] != 'pending'){
                        if($datasouce === 'checkbook_oge'){
                            if(self::getRequestParamValue('vendor'))
                                $view = 'contracts_departments_view';
                            else
                               $view = 'oge_contracts_departments_view'; 
                        }else{
                           if(!$dashboard || $dashboard == 'mp'){
                                $view = 'contracts_departments_view';
                           }
                        }
                    }
                }
                break;
            default : 
                //handle the exception when there is no match
                $view = ''; 
        }
        return $view;
    }
    
    /**
     * returns request parameter value from URL($_REQUEST['q'])
     * @param string $paramName
     * @return request parameter value
     */
    function getRequestParamValue($paramName, $fromRequestPath = TRUE){
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
    
    function contractCategory(){
        $urlPath = drupal_get_path_alias($_GET['q']);
        $pathParams = explode('/', $urlPath);
        $status = getRequestParamValue('status');
        $category = array();
        
        switch($pathParams[0]){
            case 'contracts_landing':
                $category['category'] = 'expense';
                if($status == 'A'){
                    $category['status'] = 'active';
                }else{
                    $category['status'] = 'registered';
                }
                break;
            case 'cpntracts_revenue_landing':
               $category['category'] = 'revenue';
                if($status == 'A'){
                    $category['status'] = 'active';
                }else{
                    $category['status'] = 'registered';
                }
               break; 
            case 'cpntracts_pending_exp_landing':
                $category['category'] = 'expense';
                $category['status'] = 'pending';
               break;  
            case 'cpntracts_pending_rev_landing':
               $category['category'] = 'revenue';
                $category['status'] = 'pending';
               break; 
        }
        
        return $category;
    }
    
}