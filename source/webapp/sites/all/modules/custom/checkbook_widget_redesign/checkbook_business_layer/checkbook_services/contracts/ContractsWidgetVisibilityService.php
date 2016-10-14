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
        $category = self::getContractCategory();
        $view = NULL;
        
        switch($widget){
            case 'departments':
                if($category === 'expense'){
                    if(self::isEDCPage()){
                        if(self::getRequestParamValue('vendor'))
                           $view = 'contracts_departments_view';
                        else
                           $view = 'oge_contracts_departments_view'; 
                    }else{
                       if(($dashboard == NULL || $dashboard == 'mp') && self::getRequestParamValue('agency')){
                           $view = 'contracts_departments_view';
                       }
                    }
                }
                break;
            case 'industries':
                if(!self::getRequestParamValue('cindustry')){
                    switch($category) {
                        case "expense":
                            switch($dashboard) {
                                case "ss":
                                case "sp":
                                    $view = 'sub_contracts_by_industries_view';
                                    break;
                                case "ms":
                                    $view = 'mwbe_sub_contracts_by_industries_view';
                                    break;
                                default:
                                    $view = self::isEDCPage() ? 'oge_contracts_by_industries_view' : 'contracts_by_industries_view';
                                    break;
                            }
                            break;

                        case "revenue":
                            $view = 'revenue_contracts_by_industries_view';
                            break;

                        case "pending expense":
                        case "pending revenue":
                            $view = 'pending_contracts_by_industries_view';
                            break;
                    }
                }
                break;
            case 'award_methods':
                if(!self::getRequestParamValue('awdmethod')){
                    switch($category) {
                        case "expense":
                            switch($dashboard) {
                                case "ss":
                                case "sp":
                                case "ms":
                                    $view = 'subvendor_award_methods_view';
                                    break;
                                default:
                                    $view = self::isEDCPage() ? 'oge_award_methods_view' : 'expense_award_methods_view';
                                    break;
                            }
                            break;

                        case "revenue":
                            $view = 'revenue_award_methods_view';
                            break;

                        case "pending expense":
                        case "pending revenue":
                            $view = 'pending_award_methods_view';
                            break;
                    }
                }
                break;
            case 'master_agreements':
                if(self::isEDCPage()){
                    $view = 'oge_master_agreements_view';
                }else{
                    switch($category) {
                        case 'expense':
                            $view = 'master_agreements_view';
                            break;
                        case "pending expense":
                            $view = 'pending_master_agreements_view';
                            break;
                        case "revenue":
                            $view = '';
                            break;
                        case "pending revenue":
                            $view = '';
                            break;
                    }
                }
                break;
            case 'vendors':
                if(!self::getRequestParamValue('vendor')){
                    switch($category) {
                        case "expense":
                            switch($dashboard) {
                                case "ss":
                                case "sp":
                                    $view = 'subcontracts_by_prime_vendors_view';
                                    break;
                                case "ms":
                                    $view = 'mwbe_expense_contracts_by_prime_vendors_view';
                                    break;
                                default:
                                    $view = self::isEDCPage() ? 'oge_contracts_by_prime_vendors_view' : 'expense_contracts_by_prime_vendors_view';
                                    break;
                            }
                            break;

                        case "revenue":
                            switch($dashboard) {
                                case "mp":
                                    $view = 'mwbe_revenue_contracts_by_prime_vendors_view';
                                    break;
                                default:
                                    $view = 'revenue_contracts_by_prime_vendors_view';
                                    break;
                            }
                            break;

                        case "pending expense":
                        case "pending revenue":
                            switch($dashboard) {
                                case "mp":
                                    $view = 'mwbe_pending_contracts_by_prime_vendors_view';
                                    break;
                                default:
                                    $view = 'pending_contracts_by_prime_vendors_view';
                                    break;
                            }
                        break;
                    }
                }
                break;
            default : 
                //handle the exception when there is no match
                $view = NULL;
                break;
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
    
    /**
     * returns the contract category based on the page URL
     * @return Contracts Category
     */
    function getContractCategory(){
        $urlPath = drupal_get_path_alias($_GET['q']);
        $pathParams = explode('/', $urlPath);
        $category = NULL;
        
        switch($pathParams[2]){
            case 'contracts_landing':
                $category = 'expense';
                break;
            case 'contracts_revenue_landing':
               $category = 'revenue';
               break; 
            case 'contracts_pending_exp_landing':
                $category = 'pending expense';
               break;  
            case 'contracts_pending_rev_landing':
                $category = 'pending revenue';
               break; 
        }
        
        return $category;
    }
    
    /**
     * Checks if the page is Checkbook or Checkbook OGE (EDC)
     * @return True if the page is EDC
     */
    function isEDCPage(){
        $database = _getRequestParamValue('datasource');
        if(isset($database)){
            return true;
        }else{
            return false;
        }
    }
}