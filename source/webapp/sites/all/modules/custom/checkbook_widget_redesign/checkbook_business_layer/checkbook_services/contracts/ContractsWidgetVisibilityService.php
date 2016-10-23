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
        $dashboard = RequestUtilities::getRequestParamValue('dashboard');
        $category = ContractsParameters::getContractCategory();
        $view = NULL;
        
        switch($widget){
            case 'departments':
                if($category === 'expense'){
                    if(RequestUtilities::isEDCPage()){
                        if(RequestUtilities::getRequestParamValue('vendor'))
                           $view = 'contracts_departments_view';
                        else
                           $view = 'oge_contracts_departments_view'; 
                    }else{
                       if(($dashboard == NULL || $dashboard == 'mp') && RequestUtilities::getRequestParamValue('agency')){
                           $view = 'contracts_departments_view';
                       }
                    }
                }
                break;
            case 'contracts_modifications':
                switch($category) {
                        case "expense":
                            switch($dashboard) {
                                case "ss":
                                case "sp":
                                    $view = 'sub_contracts_modifications_view';
                                    break;
                                case "ms":
                                    $view = 'mwbe_sub_contracts_modifications_view';
                                    break;
                                default:
                                    $view = RequestUtilities::isEDCPage() ? 'oge_contracts_modifications_view' : 'contracts_modifications_view';
                                    break;
                            }
                            break;
                        case "revenue":
                            $view = 'revenue_contracts_modifications_view';
                            break;
                        case "pending expense":
                            $view = 'expense_pending_contracts_modifications_view';
                            break;
                        case "pending revenue":
                            $view = 'revenue_pending_contracts_modifications_view';
                            break;
                }
                break;
            case 'industries':
                if(!RequestUtilities::getRequestParamValue('cindustry')){
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
                                    $view = RequestUtilities::isEDCPage() ? 'oge_contracts_by_industries_view' : 'contracts_by_industries_view';
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
            case 'size':
                if(!RequestUtilities::getRequestParamValue('csize')){
                    switch($category) {
                        case "expense":
                            switch($dashboard) {
                                case "ss":
                                case "sp":
                                    $view = 'sub_contracts_by_size_view';
                                    break;
                                case "ms":
                                    $view = 'mwbe_sub_contracts_by_size_view';
                                    break;
                                default:
                                    $view = RequestUtilities::isEDCPage() ? 'oge_contracts_by_size_view' : 'contracts_by_size_view';
                                    break;
                            }
                            break;

                        case "revenue":
                            $view = 'revenue_contracts_by_size_view';
                            break;

                        case "pending expense":
                        case "pending revenue":
                            $view = 'pending_contracts_by_size_view';
                            break;
                    }
                }
                break;
            case 'award_methods':
                if(!RequestUtilities::getRequestParamValue('awdmethod')){
                    switch($category) {
                        case "expense":
                            switch($dashboard) {
                                case "ss":
                                case "sp":
                                case "ms":
                                    $view = 'subvendor_award_methods_view';
                                    break;
                                default:
                                    $view = RequestUtilities::isEDCPage() ? 'oge_award_methods_view' : 'expense_award_methods_view';
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
                if(RequestUtilities::isEDCPage()){
                    $view = 'oge_master_agreements_view';
                }else{
                    switch($category) {
                        case 'expense':
                            switch($dashboard) {
                                case "ss":
                                case "sp":
                                case "ms":
                                    $view = '';
                                    break;
                                default :
                                    $view = 'master_agreements_view';
                                    break;
                            }
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
            case 'master_agreement_modifications':
                if(RequestUtilities::isEDCPage()){
                    $view = 'oge_master_agreement_modifications_view';
                }else{
                    switch($category) {
                        case 'expense':
                            switch($dashboard) {
                                case "ss":
                                case "sp":
                                case "ms":
                                    $view = '';
                                    break;
                                default :
                                    $view = 'master_agreement_modifications_view';
                                    break;
                            }
                            break;
                        case "pending expense":
                            $view = 'pending_master_agreement_modifications_view';
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
                if(!RequestUtilities::getRequestParamValue('vendor')){
                    switch($category) {
                        case "expense":
                            switch($dashboard) {
                                case "ss":
                                case "sp":
                                    $view = 'subcontracts_by_prime_vendors_view';
                                    break;
                                case "ms":
                                    $view = 'mwbe_subcontracts_by_prime_vendors_view';
                                    break;
                                case "mp":
                                    $view = 'mwbe_expense_contracts_by_prime_vendors_view';
                                    break;
                                default:
                                    $view = RequestUtilities::isEDCPage() ? 'oge_contracts_by_prime_vendors_view' : 'expense_contracts_by_prime_vendors_view';
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
            case 'agencies':
                if(!RequestUtilities::getRequestParamValue('agency')){
                    switch($category) {
                        case "expense":
                            switch($dashboard) {
                                case "ss":
                                case "sp":
                                    $view = 'subcontracts_by_agencies_view';
                                    break;
                                case "ms":
                                    $view = 'mwbe_subcontracts_by_agencies_view';
                                    break;
                                default:
                                    $view = 'expense_contracts_by_agencies_view';
                                    break;
                            }
                            break;
                        case "revenue":
                            $view = 'revenue_contracts_by_agencies_view';        
                            break;
                        case "pending expense":
                        case "pending revenue":
                            $view = 'pending_contracts_by_agencies_view';
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
}