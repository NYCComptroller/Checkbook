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

namespace { //global
    class ContractUtil{

        static $landingPageParams = array("status"=>"status","awdmethod"=>"awdmethod","cindustry"=>"cindustry","csize"=>"csize","mwbe"=>"mwbe","dashboard"=>"dashboard","agency"=>"agency","vendor"=>"vendor","subvendor"=>"subvendor");

        /**
         * NYCCHKBK-3573 - Contract ID page notepad icon should be displayed only if there is a difference between OGE
         * and Citywide values for any the following fields:
         *
         * 1. Current Amount
         * 2. Original Amount
         * 3. Spent to date
         *
         */
        static public function childAgreementAmountsDiffer($agreement_id){

            $checkbook_ca = new checkbook_project_custom_classes_contract\ChildAgreement('checkbook', $agreement_id);
            $checkbook_oge_ca = new checkbook_project_custom_classes_contract\ChildAgreement('checkbook_oge', $agreement_id);


            $data_source_amounts_differ =
                ($checkbook_ca->getCurrentAmount() - $checkbook_oge_ca->getCurrentAmount()) != 0 ||
                ($checkbook_ca->getOriginalAmount() - $checkbook_oge_ca->getOriginalAmount()) != 0 ||
                ($checkbook_ca->getSpentAmount() - $checkbook_oge_ca->getSpentAmount()) != 0;

            return $data_source_amounts_differ;
        }

        static public function masterAgreementAmountsDiffer($agreement_id){

            $checkbook_ca = new checkbook_project_custom_classes_contract\MasterAgreement('checkbook', $agreement_id);
            $checkbook_oge_ca = new checkbook_project_custom_classes_contract\MasterAgreement('checkbook_oge', $agreement_id);

            $data_source_amounts_differ =
                ($checkbook_ca->getCurrentAmount() - $checkbook_oge_ca->getCurrentAmount()) != 0 ||
                ($checkbook_ca->getOriginalAmount() - $checkbook_oge_ca->getOriginalAmount()) != 0 ||
                ($checkbook_ca->getSpentAmount() - $checkbook_oge_ca->getSpentAmount()) != 0;

            return $data_source_amounts_differ;
        }
        
        static public function getCurrentPageDocumentIdsArray(){
        	if(preg_match('/revenue/',$_GET['q'])){
        		$document_codes = array('RCT1') ;
        	}else if(preg_match('/pending_exp/',$_GET['q'])){
        		$document_codes = array('MA1','CT1','CTA1');
        	}else if(preg_match('/pending_rev/',$_GET['q'])){
        		$document_codes = array('RCT1') ;
        	}else{
        		$document_codes = array('MA1','CT1','CTA1');
        	}
        	return $document_codes;
        }

        static public function getCurrentPageDocumentIds(){
        	if(preg_match('/revenue/',$_GET['q'])){
        		$document_codes = "'RCT1'" ;
        	}else if(preg_match('/pending_exp/',$_GET['q'])){
        		$document_codes ="'MA1','CT1','CTA1'";
        	}else if(preg_match('/pending_rev/',$_GET['q'])){
        		$document_codes = "'RCT1'" ;
        	}else{
        		$document_codes = "'MA1','CT1','CTA1'";
        	}
        	return $document_codes;
        }
        
        static public function getSubvendorDashboard(){
            $dashboard = _getRequestParamValue('dashboard');
            if($dashboard == 'mp')
                return '/dashboard/sp';
            else if($dashboard == 'ms')
                return '/dashboard/ss';
            else
                return '/dashboard/'.$dashboard;
        }
        
        static public function getCurrentContractStatusandType(){
        	if(_getRequestParamValue('status') == 'A'){
				$status = 'Active';
			}else if(_getRequestParamValue('status') == 'R'){
				$status = 'Registered';
			}
			
			$contract_type = 'Expense';
			if(preg_match('/revenue/',$_GET['q'])){
				$contract_type = 'Revenue';
			}
			if(preg_match('/pending_exp/',$_GET['q'])){
				$contract_type = 'Pending Expense';
			}
			if(preg_match('/pending_rev/',$_GET['q'])){
				$contract_type = 'Pending Revenue';
			}
        	return $status . ' ' . $contract_type ;
        }

        /**
         * Based on the dashboard and minority type,
         * this will return either a link or just the M/WBE category name.
         *
         * NYCCHKBK-4676:
         *   Do not hyperlink the M/WBE category within Top 5 Sub vendors widget if you are looking at prime data[M/WBE Featured Dashboard].
         *   Do not hyperlink the M/WBE category within Top 5 Prime vendors widget if you are looking at sub data[M/WBE(sub vendors) featured dashboard].
         *   The Details link from these widgets, also should follow same rule of not hyperlinking the M/WBE category.
         * NYCCHKBK-4798:
         *   From Top 5 Sub vendors widget, link should go to SP to maintain correct data
         * @param $node
         * @param $row
         * @return string
         */
        static public function getMWBECategory($node,$row){

            $minority_type_id = isset($row["prime_minority_type_prime_minority_type"])
                ? $row["prime_minority_type_prime_minority_type"]
                : $row["minority_type_minority_type"];
            $minority_category = MappingUtil::getMinorityCategoryById($minority_type_id);
            $is_mwbe_certified = MappingUtil::isMWBECertified(array($minority_type_id));
            $dtsmnid = _getRequestParamValue("dtsmnid");
            $smnid = _getRequestParamValue("smnid");
            $dashboard = _getRequestParamValue("dashboard");

            if($dtsmnid != null) $nid = $dtsmnid;
            else if($smnid != null) $nid = $smnid;
            else $nid = $node->nid;

            $no_link = $dashboard == "mp" && $nid == 720;
            $no_link = $no_link || (preg_match('/s/', $dashboard) && ($nid == 725 || $nid == 783));

            $dashboard = (preg_match('/p/', $dashboard)) ? "mp" : "ms";
            //From sub vendors widget
            if($nid == 720) $dashboard = "sp";

            $showLink = !RequestUtil::isNewWindow()
                && $is_mwbe_certified
                && !$no_link;

            if(!$showLink) {
                $return_value = $minority_category;
            }
            else {
                $return_value = '<a href="/contracts_landing'
                    . _checkbook_project_get_year_url_param_string()
                    . _checkbook_project_get_url_param_string("agency")
                    . _checkbook_project_get_url_param_string("cindustry")
                    . _checkbook_project_get_url_param_string("csize")
                    . _checkbook_project_get_url_param_string("awdmethod")
                    . _checkbook_project_get_url_param_string("contstatus","status")
                    . _checkbook_project_get_url_param_string("vendor")
                    . _checkbook_project_get_url_param_string("subvendor")
                    . '/dashboard/' . $dashboard
                    . '/mwbe/'. $minority_type_id .  '?expandBottomCont=true">' . $minority_category . '</a>';
            }

            return $return_value;
        }


        /* Returns M/WBE category for the given vendor id in the given year and year type for city-wide Active/Registered Contracts Transactions Pages*/

        static public function get_contract_vendor_minority_category($vendor_id, $year_id = null, $year_type = null,$agency_id = null, $is_prime_or_sub = 'P'){

            $latest_minority_id = self::getLatestMwbeCategoryByVendor($vendor_id, $agency_id = null, $year_id, $year_type, $is_prime_or_sub);
            if($is_prime_or_sub == 'P'){
                if(in_array($latest_minority_id, array(2,3,4,5,9))){
                    return _checkbook_project_get_year_url_param_string()._checkbook_project_get_url_param_string("contstatus","status")."/dashboard/mp/mwbe/2~3~4~5~9/vendor/".$vendor_id;
                }else{
                    return  _checkbook_project_get_year_url_param_string()._checkbook_project_get_url_param_string("contstatus","status")."/vendor/".$vendor_id;
                }
            }else if($is_prime_or_sub == 'S'){
               return self::get_contracts_vendor_link_sub($vendor_id, $year_id = null, $year_type = null,$agency_id = null);
            }

            return '';
        }

        /**
         * Returns M/WBE category for the given vendor id in the given year and year type for
         * Active/Registered Contracts Landing Pages
         * @param $row
         * @return string
         */
        static public function get_contracts_vendor_link_by_mwbe_category($row){

            $vendor_id = $row["vendor_vendor"] != null ? $row["vendor_vendor"] : $row["vendor_id"];
            $year_id = _getRequestParamValue("year");
            $year_type = $row["yeartype_yeartype"];
            $is_prime_or_sub = $row["is_prime_or_sub"] != null ? $row["is_prime_or_sub"] : "P";
            $agency_id = null;
            if($row["current_prime_minority_type_id"])
                $minority_type_id = $row["current_prime_minority_type_id"];
            if($row["minority_type_id"])
                $minority_type_id = $row["minority_type_id"];

            $smnid = _getRequestParamValue("smnid");
            if($smnid == 720 || $smnid == 784) return self::get_contracts_vendor_link_sub($vendor_id, $year_id, $year_type,$agency_id);

            $latest_minority_id = self::getLatestMwbeCategoryByVendor($vendor_id, $agency_id = null, $year_id, $year_type, $is_prime_or_sub);
            $latest_minority_id = isset($latest_minority_id) ? $latest_minority_id : $minority_type_id;
            $is_mwbe_certified = MappingUtil::isMWBECertified(array($latest_minority_id));

            $url = _checkbook_project_get_url_param_string("agency") . _checkbook_project_get_url_param_string("contstatus","status") . _checkbook_project_get_year_url_param_string();

            if($is_mwbe_certified && _getRequestParamValue('dashboard') == 'mp') {
                $url .= _checkbook_project_get_url_param_string("cindustry")
                    . _checkbook_project_get_url_param_string("csize")
                    . _checkbook_project_get_url_param_string("awdmethod")
                    . "/dashboard/mp/mwbe/2~3~4~5~9/vendor/".$vendor_id;
            }
            else if($is_mwbe_certified && _getRequestParamValue('dashboard') != 'mp') {
                $url .= "/dashboard/mp/mwbe/2~3~4~5~9/vendor/".$vendor_id;
            }
            else {
                $url .= _checkbook_project_get_url_param_string("datasource")."/vendor/".$vendor_id;
            }
            return $url;
        }

        static public function get_contracts_vendor_link($vendor_id, $year_id = null, $year_type = null,$agency_id = null, $mwbe_cat = null, $is_prime_or_sub = 'P'){

            //For the 3rd menu option on contracts sub vendor, contract status should be set to active for links
            $contract_status = _checkbook_project_get_url_param_string("contstatus","status");
            $contract_status = $contract_status == "" ? "/status/A" : $contract_status;

            $latest_minority_id = isset($mwbe_cat) ? $mwbe_cat : self::getLatestMwbeCategoryByVendor($vendor_id, $agency_id = null, $year_id, $year_type, $is_prime_or_sub);
            $url = _checkbook_project_get_url_param_string("agency") . $contract_status . _checkbook_project_get_year_url_param_string();

            if(in_array($latest_minority_id, array(2,3,4,5,9)) && _getRequestParamValue('dashboard') == 'mp'){
                $url .= _checkbook_project_get_url_param_string("cindustry"). _checkbook_project_get_url_param_string("csize")
                      . _checkbook_project_get_url_param_string("awdmethod") ."/dashboard/mp/mwbe/2~3~4~5~9/vendor/".$vendor_id;
                return $url;
            }else if(in_array($latest_minority_id, array(2,3,4,5,9)) && _getRequestParamValue('dashboard') != 'mp'){
                return $url ."/dashboard/mp/mwbe/2~3~4~5~9/vendor/".$vendor_id;
            }else{
                return $url. "/vendor/".$vendor_id;
            }

            return '';
        }

        
        
        static public function get_contracts_vendor_link_sub($vendor_id, $year_id = null, $year_type = null,$agency_id = null, $mwbe_cat = null){

            $latest_minority_id = isset($mwbe_cat) ? $mwbe_cat : self::getLatestMwbeCategoryByVendor($vendor_id, $agency_id = null, $year_id, $year_type, "S");
            $url = _checkbook_project_get_url_param_string("agency") .  _checkbook_project_get_url_param_string("contstatus","status") . _checkbook_project_get_year_url_param_string();

            $current_dashboard = _getRequestParamValue("dashboard");
            $is_mwbe_certified = in_array($latest_minority_id, array(2, 3, 4, 5, 9));

            //if M/WBE certified, go to M/WBE (Sub Vendor) else if NOT M/WBE certified, go to Sub Vendor dashboard
            $new_dashboard = $is_mwbe_certified ? "ms" : "ss";
            $status = strlen(_checkbook_project_get_url_param_string("contstatus","status"))== 0 ? "/status/A" : "";
            
            if($current_dashboard != $new_dashboard ){
                    return $url . $status . "/dashboard/" . $new_dashboard . ($is_mwbe_certified ? "/mwbe/2~3~4~5~9" : "" ) . "/subvendor/".$vendor_id;
            }else{
                    $url .= $status._checkbook_project_get_url_param_string("cindustry"). _checkbook_project_get_url_param_string("csize")
                    . _checkbook_project_get_url_param_string("awdmethod") ."/dashboard/" . $new_dashboard .
                    ($is_mwbe_certified ? "/mwbe/2~3~4~5~9" : "" ) . "/subvendor/".$vendor_id;
                    return $url;
            }

            return '';
        }
        
        

        static public function getLatestMwbeCategoryByVendor($vendor_id, $agency_id = null, $year_id = null, $year_type = null, $is_prime_or_sub = "P"){
        	STATIC $contract_vendor_latest_mwbe_category;
        	if($agency_id == null){
        		$agency_id =  _getRequestParamValue('agency');
        	}
            if($year_type == null){
                $year_type =  _getRequestParamValue('yeartype');
            }
        	if($year_id == null){
        		$year_id =  _getRequestParamValue('year');
        	}
            if($year_id == null){
                $year_id =  _getRequestParamValue('calyear');
            }
            if($year_id == null){
                $year_type = "B";
                $year_id = _getCurrentYearID();
            }



        	$latest_minority_type_id = null;
            $agency_query = isset($agency_id) ? "agency_id = " . $agency_id : "agency_id IS NULL";

        	if(!isset($contract_vendor_latest_mwbe_category)){
        		$query = "SELECT vendor_id, agency_id, year_id, type_of_year, minority_type_id, is_prime_or_sub
                      FROM contract_vendor_latest_mwbe_category
                      WHERE minority_type_id IN (2,3,4,5,9) AND year_id = ".$year_id
                            ." AND type_of_year = '".$year_type . "'"
                            ."  AND " . $agency_query
                            ." AND is_prime_or_sub = '" . $is_prime_or_sub . "'"
                      ." GROUP BY vendor_id, agency_id, year_id, type_of_year, minority_type_id, is_prime_or_sub";

        		$results = _checkbook_project_execute_sql_by_data_source($query,'checkbook');
        		foreach($results as $row){
        			if(isset($row['agency_id'])) {
        				$contract_vendor_latest_mwbe_category[$row['vendor_id']][$row['agency_id']][$row['is_prime_or_sub']]['minority_type_id'] = $row['minority_type_id'];
        			}
        			else{
        				$contract_vendor_latest_mwbe_category[$row['vendor_id']][$row['is_prime_or_sub']]['minority_type_id'] = $row['minority_type_id'];
        			}
        
        		}
        	}

            $latest_minority_type_id = isset($agency_id)
        	? $contract_vendor_latest_mwbe_category[$vendor_id][$agency_id][$is_prime_or_sub]['minority_type_id']
        	: $contract_vendor_latest_mwbe_category[$vendor_id][$is_prime_or_sub]['minority_type_id'];
        	return $latest_minority_type_id;
        }


        static public function getLatestMwbeCategoryByVendorByTransactionYear($vendor_id, $year_id = null, $year_type = null){

            if($year_id == null){
                $year_id =  _getRequestParamValue('year');
            }

            if($year_type == null){
                $year_type =  _getRequestParamValue('yeartype');
            }

                $query = "SELECT vendor_id, agency_id, year_id, type_of_year, minority_type_id, is_prime_or_sub
                      FROM contract_vendor_latest_mwbe_category
                      WHERE minority_type_id IN (2,3,4,5,9)
                      AND vendor_id =".$vendor_id."
                      AND year_id =".$year_id."
                      AND type_of_year ='".$year_type."'
                      GROUP BY vendor_id, agency_id, year_id, type_of_year, minority_type_id, is_prime_or_sub LIMIT 1";

                $results = _checkbook_project_execute_sql_by_data_source($query,'checkbook');
                if(!$results){
                    $query = "SELECT year_id, minority_type_id
                          FROM contract_vendor_latest_mwbe_category
                          WHERE  vendor_id = ".$vendor_id
                        ."AND type_of_year ='".$year_type."'"
                        ." ORDER BY year_id DESC LIMIT 1 ";
                    $results = _checkbook_project_execute_sql_by_data_source($query,'checkbook');
                }
                if($results[0]['minority_type_id'] != ''){
                   return $results[0]['minority_type_id'];
                }
                else{
                    return false;
                }
        }
        /* Returns M/WBE category for the given vendor id in the given year and year type for contracts Advanced Serach results*/

        static public function get_contract_vendor_link($vendor_id, $is_prime_or_sub, $minority_type_id){

               if($is_prime_or_sub == "P" && in_array($minority_type_id, array(2,3,4,5,9))){
                   return "/dashboard/mp/mwbe/2~3~4~5~9/vendor/".$vendor_id;
               }else if($is_prime_or_sub == "P" && in_array($minority_type_id, array(2,3,4,5,9))){
                   return "/vendor/".$vendor_id;
               }
        }

        /* Returns M/WBE category of a vendor id in citywide pending contracts*/

        static public function get_pending_contract_vendor_minority_category($vendor_id){
            STATIC $mwbe_vendors;
            $agency_id =  _getRequestParamValue('agency');
            $agency_query = isset($agency_id) ? " AND awarding_agency_id = " . $agency_id : " ";

            if(!isset($mwbe_vendors)){
                $query = "SELECT vendor_id FROM pending_contracts WHERE is_prime_or_sub='P' AND minority_type_id IN (2,3,4,5,9)"
                         . $agency_query
                         ." GROUP BY vendor_id";
                $results = _checkbook_project_execute_sql_by_data_source($query,'checkbook');
                foreach($results as $row){
                    $mwbe_vendors[$row['vendor_id']] = $row['vendor_id'];
                }
            }
            if($mwbe_vendors[$vendor_id] == $vendor_id){
                    return '/dashboard/mp/mwbe/2~3~4~5~9';
            }
            return '';
        }

       /* Returns minority type category URL for contracts transaction and advanced search results pages */
        static public function get_mwbe_category_url($minority_type_category_id, $is_prime_or_sub = null, $doctype = null){
            $lower_doctype = strtolower($doctype);

            /* Begin update for NYCCHKBK-4676 */
            $minority_type_category_name = MappingUtil::getMinorityCategoryById($minority_type_category_id);
            $dtsmnid = _getRequestParamValue("dtsmnid");
            $smnid = _getRequestParamValue("smnid");
            $dashboard = _getRequestParamValue("dashboard");

            if($dtsmnid != null) $nid = $dtsmnid;
            else if($smnid != null) $nid = $smnid;

            $no_link = $dashboard == "mp" && $nid == 720;
            $no_link = $no_link || (preg_match('/s/', $dashboard) && ($nid == 725 || $nid == 783));

            if($no_link) return $minority_type_category_name;
            /* End update for NYCCHKBK-4676 */

            $mwbe_cats = MappingUtil::getMinorityCategoryMappings();
            $minority_type_category_string = implode('~', $mwbe_cats[$minority_type_category_name]);

            $current_url = explode('/',$_SERVER['HTTP_REFERER']);
            $status_index = array_search('contstatus',$current_url);
            $category_index = array_search('contcat',$current_url);

            $status = filter_xss($current_url[($status_index+1)]);
            $category = filter_xss($current_url[($category_index+1)]);

            $dashboard = ($is_prime_or_sub == 'S') ? 'ms' :'mp';
            //From sub vendors widget
            if($nid == 720) $dashboard = "sp";

            if($category == 'expense' && $status != 'P'){
                $url = '/contracts_landing/status/'.$status.'/yeartype/B/year/'._getFiscalYearID().'/dashboard/'. $dashboard .'/mwbe/'.$minority_type_category_string;
            }else if($category == 'revenue' && $status != 'P'){
                $url = '/contracts_revenue_landing/status/'.$status.'/yeartype/B/year/'._getFiscalYearID().'/dashboard/'. $dashboard .'/mwbe/'.$minority_type_category_string;
            }else if($category == 'expense' && $status == 'P'){
                $url = '/contracts_pending_exp_landing/yeartype/B/year/'._getFiscalYearID().'/dashboard/'. $dashboard .'/mwbe/'.$minority_type_category_string;
            }else if($category == 'revenue' && $status == 'P'){
                $url = '/contracts_pending_rev_landing/yeartype/B/year/'._getFiscalYearID().'/dashboard/'. $dashboard .'/mwbe/'.$minority_type_category_string;
            }else if($category == 'all' && $status != 'P'){
                if(_get_contract_cat($lower_doctype) == 'revenue'){
                    $url = '/contracts_revenue_landing/status/'.$status.'/yeartype/B/year/'._getFiscalYearID().'/dashboard/'. $dashboard .'/mwbe/'.$minority_type_category_string;
                }
                else{
                    $url = '/contracts_landing/status/'.$status.'/yeartype/B/year/'._getFiscalYearID().'/dashboard/'. $dashboard .'/mwbe/'.$minority_type_category_string;
                }
            }else if($category == 'all' && $status == 'P'){
                if(_get_contract_cat($lower_doctype) == 'revenue'){
                    $url = '/contracts_pending_rev_landing/yeartype/B/year/'._getFiscalYearID().'/dashboard/'. $dashboard .'/mwbe/'.$minority_type_category_string;
                }else{
                    $url = '/contracts_pending_exp_landing/yeartype/B/year/'._getFiscalYearID().'/dashboard/'. $dashboard .'/mwbe/'.$minority_type_category_string;
                }
            }
            // pending_changes

            $url .= _checkbook_project_get_url_param_string("agency")
            . _checkbook_project_get_url_param_string("cindustry")
            . _checkbook_project_get_url_param_string("csize")
            . _checkbook_project_get_url_param_string("awdmethod")
            . _checkbook_project_get_url_param_string("contstatus","status")
            . _checkbook_project_get_url_param_string("vendor")
            . _checkbook_project_get_url_param_string("subvendor");

            $result = (!in_array($minority_type_category_id,array(7,11)))?"<a href='".$url."'>".$minority_type_category_name."</a>" : $minority_type_category_name;

            return $result;
        }

        static public function getMWBECategoryLinkUrl($minority_type_id){
            $current_url = explode('/',$_SERVER['HTTP_REFERER']);
            $minority_type_id = ($minority_type_id == 4 || $minority_type_id == 5) ? '4~5': $minority_type_id;
            $nid =
            $dashboard = "mp";
            $url =  '/'. $current_url[3]._checkbook_project_get_year_url_param_string()
                    . _checkbook_project_get_url_param_string("agency")
                    . _checkbook_project_get_url_param_string("cindustry")
                    . _checkbook_project_get_url_param_string("csize")
                    . _checkbook_project_get_url_param_string("awdmethod")
                    . _checkbook_project_get_url_param_string("contstatus","status")
                    . _checkbook_project_get_url_param_string("vendor")
                    . _checkbook_project_get_url_param_string("subvendor")
                    . '/dashboard/mp'
                    . '/mwbe/'. $minority_type_id .  '?expandBottomCont=true';
            return $url;
        }

        /**
         * Gets the Spent to date link Url for the Sub Vendors widget
         * @param $node
         * @param $row
         * @return string
         */
        static public function getSubVendorSpentToDateLinkUrl($node,$row){
            $dashboard = _getRequestParamValue("dashboard");
            $url = "/contract/spending/transactions/csubvendor/" . $row["subvendor_subvendor"]
                . _checkbook_append_url_params()
                . _checkbook_project_get_url_param_string("status")
                . _checkbook_project_get_url_param_string("agency","cagency")
                . _checkbook_project_get_url_param_string("awdmethod")
                .  _checkbook_project_get_url_param_string("cindustry")
                .  _checkbook_project_get_url_param_string("csize");
            if($node->nid == 720){
                $url .= '/doctype/CT1~CTA1'.ContractURLHelper::_checkbook_project_spending_get_year_url_param_string();
            }
            else if($dashboard == "ss" || $dashboard == "ms" || $dashboard == "sp"){
                $url .= '/doctype/CT1~CTA1'.ContractURLHelper::_checkbook_project_spending_get_year_url_param_string();
            }else{
                $url .= '/doctype/CT1~CTA1~MA1'.ContractURLHelper::_checkbook_project_spending_get_year_url_param_string();
            }
                $url .= '/smnid/' . $node->nid . self::getSpentToDateParams() . '/newwindow';

            if($dashboard == "mp" && $node->nid == 720)
                $url = str_replace("dashboard/mp","dashboard/ms",$url);
            return $url;
        }

        /**
         *  Returns a contract landing page Url with custom parameters appended but instead of persisted
         *
         * @param array $override_params
         * @return string
         */
        static function getLandingPageWidgetUrl($override_params = array()) {
            return self::getContractUrl('contracts_landing',$override_params);
        }

        /**
         * Function build the url using the path and the current Contract URL parameters.
         * The Url parameters can be overridden by the override parameter array.
         *
         * @param $path
         * @param array $override_params
         * @return string
         */
        static function getContractUrl($path, $override_params = array()) {

            $url =  $path . _checkbook_project_get_year_url_param_string();

            $pathParams = explode('/',drupal_get_path_alias($_GET['q']));
            $url_params = self::$landingPageParams;
            $exclude_params = array_keys($override_params);
            if(is_array($url_params)){
                foreach($url_params as $key => $value){
                    if(!in_array($key,$exclude_params)){
                        $url .=  CustomURLHelper::get_url_param($pathParams,$key,$value);
                    }
                }
            }

            if(is_array($override_params)){
                foreach($override_params as $key => $value){
                    if(isset($value)){
                        if($key == 'yeartype' && $value == 'C'){
                            $value = 'B';
                        }
                        $url .= "/$key";
                        $url .= "/$value";
                    }
                }
            }

            return $url;
        }

        static public function getSpentToDateParams(){
            $url = $_GET['q'];
            $parameters = '';
            $contract_status = _getRequestParamValue('status');
            $contract_type = 'expense';

            if(preg_match('/revenue/',$url)){
                $contract_type = 'revenue';
            }
            else if(preg_match('/pending_exp/',$url)){
                $contract_type = 'expense';
            }
            else if(preg_match('/pending_rev/',$url)){
                $contract_type = 'revenue';
            }
            if(isset($contract_status)) {
                $parameters = '/contstatus/'.$contract_status;
            }
            $parameters .= '/contcat/'.$contract_type;
            return $parameters;
        }

        static public function adjustContractParameterFilters(&$node, &$parameters) {

            //Handle year parameter
            $reqYear = _getRequestParamValue('year');
            $data_controller_instance = data_controller_get_operator_factory_instance();
            $geCondition = $data_controller_instance->initiateHandler(GreaterOrEqualOperatorHandler::$OPERATOR__NAME, array($reqYear));
            $leCondition = $data_controller_instance->initiateHandler(LessOrEqualOperatorHandler::$OPERATOR__NAME, array($reqYear));
            $parameters['starting_year_id']= $leCondition;
            $parameters['ending_year_id']= $geCondition;
            $parameters['effective_begin_year_id']= $leCondition;
            $parameters['effective_end_year_id']= $geCondition;

            //Vendor Facet -- prime_sub_vendor_code ~* '(^155$)|(.*,155$)|(^155,.*)'
            $vendor_codes = explode('~', _getRequestParamValue('vendornm'));
            $has_vendors = isset($vendor_codes[0]) && $vendor_codes[0] != "";
            if($has_vendors) {
                $pattern = null;
                foreach($vendor_codes as $vendor_code) {
                    $localValue = _checkbook_regex_replace_pattern($vendor_code);
                    $localValue = "(^{$localValue}$)|(.*,{$localValue}$)|(^{$localValue},.*)";
                    $pattern .= isset($pattern) ? '|'.$localValue : $localValue;
                }
                $pattern = '('.$pattern.')';
                $condition = $data_controller_instance->initiateHandler(RegularExpressionOperatorHandler::$OPERATOR__NAME, $pattern);
                if(isset($condition)) {
                    $parameters['prime_sub_vendor_code'] = $condition;
                }
            }

            //Vendor Type Facet
            $vendor_types = explode('~', _getRequestParamValue('vendortype'));
            $has_vendor_types = isset($vendor_types[0]) && $vendor_types[0] != "";
            if($has_vendor_types) {

                $condition = null;

                if($has_vendors) {
                    $pattern = null;
                    foreach($vendor_codes as $vendor_code) {
                        $local_pattern = self::getVendorTypeRegEx($vendor_types, $vendor_code);
                        $pattern .= isset($pattern) ? '|'.$local_pattern : $local_pattern;
                    }
                }
                else {
                    $pattern = self::getVendorTypeRegEx($vendor_types);
                }
                if($pattern != null) {
                    $condition = $data_controller_instance->initiateHandler(RegularExpressionOperatorHandler::$OPERATOR__NAME, $pattern);
                    if(isset($condition)) {
                        $parameters['prime_sub_vendor_code_by_type'] = $condition;
                    }
                }
            }

            //M/WBE Category Facet
            $mwbe_categories = explode('~', _getRequestParamValue('mwbe'));
            $has_mwbe_categories = isset($mwbe_categories[0]) && $mwbe_categories[0] != "";
            if($has_mwbe_categories) {

                $condition = null;

                if($has_vendor_types) {
                    $pattern = null;
                    foreach($mwbe_categories as $mwbe_category) {
                        $local_pattern = self::getMWBECategoryRegEx($mwbe_category, $vendor_types);
                        $pattern .= isset($pattern) ? '|'.$local_pattern : $local_pattern;
                    }
                }
                else {
                    $pattern = null;
                    foreach($mwbe_categories as $mwbe_category) {
                        $local_pattern = self::getMWBECategoryRegEx($mwbe_category);
                        $pattern .= isset($pattern) ? '|'.$local_pattern : $local_pattern;
                    }
                }
                if($pattern != null) {
                    $condition = $data_controller_instance->initiateHandler(RegularExpressionOperatorHandler::$OPERATOR__NAME, $pattern);
                    if(isset($condition)) {
                        $parameters['prime_sub_minority_type_id'] = $condition;
                    }
                }
            }

            unset($parameters['year']);
            unset($parameters['vendor_name']);
            unset($parameters['minority_type_id']);

            return $parameters;
        }

        static public function getMWBECategoryRegEx($mwbe_category, $vendor_types = null) {

            if(isset($vendor_types)) {
                $P = in_array('P', $vendor_types);
                $S = in_array('S', $vendor_types);
                $M = in_array('M', $vendor_types);;
                $pattern = null;

                if($P && $M) {
                    $localValue = "PM:{$mwbe_category}";
                    $localValue = "(^{$localValue}$)|(.*,{$localValue}$)|(^{$localValue},.*)";
                    $pattern .= isset($pattern) ? '|'.$localValue : $localValue;
                }
                if($P && !$M) {
                    $localValue = "PM:{$mwbe_category}";
                    $localValue = "(^{$localValue}$)|(.*,{$localValue}$)|(^{$localValue},.*)";
                    $pattern .= isset($pattern) ? '|'.$localValue : $localValue;

                    $localValue = "P:{$mwbe_category}";
                    $localValue = "(^{$localValue}$)|(.*,{$localValue}$)|(^{$localValue},.*)";
                    $pattern .= isset($pattern) ? '|'.$localValue : $localValue;
                }
                if($S && $M) {
                    $localValue = "SM:{$mwbe_category}";
                    $localValue = "(^{$localValue}$)|(.*,{$localValue}$)|(^{$localValue},.*)";
                    $pattern .= isset($pattern) ? '|'.$localValue : $localValue;
                }
                if($S && !$M) {
                    $localValue = "S:{$mwbe_category}";
                    $localValue = "(^{$localValue}$)|(.*,{$localValue}$)|(^{$localValue},.*)";
                    $pattern .= isset($pattern) ? '|'.$localValue : $localValue;

                    $localValue = "SM:{$mwbe_category}";
                    $localValue = "(^{$localValue}$)|(.*,{$localValue}$)|(^{$localValue},.*)";
                    $pattern .= isset($pattern) ? '|'.$localValue : $localValue;
                }
                if($M && !$P && !$S) {
                    $localValue = "PM:{$mwbe_category}";
                    $localValue = "(^{$localValue}$)|(.*,{$localValue}$)|(^{$localValue},.*)";
                    $pattern .= isset($pattern) ? '|'.$localValue : $localValue;

                    $localValue = "SM:{$mwbe_category}";
                    $localValue = "(^{$localValue}$)|(.*,{$localValue}$)|(^{$localValue},.*)";
                    $pattern .= isset($pattern) ? '|'.$localValue : $localValue;
                }
            }
            else {
                $localValue = ":{$mwbe_category}";
                $pattern = "(.*{$localValue}$)|(.*,.*{$localValue}$)|(.*{$localValue},.*)";
            }

            return $pattern;
        }

        static public function getVendorTypeRegEx($vendor_types, $vendor_code = null) {

            $P = in_array('P', $vendor_types);
            $S = in_array('S', $vendor_types);
            $M = in_array('M', $vendor_types);;
            $vendor_code = isset($vendor_code) ? $vendor_code : ".*";
            $pattern = null;

            if($P && $M) {
                $localValue = "PM:{$vendor_code}";
                $localValue = "(^{$localValue}$)|(.*,{$localValue}$)|(^{$localValue},.*)";
                $pattern .= isset($pattern) ? '|'.$localValue : $localValue;
            }
            if($P && !$M) {
                $localValue = "PM:{$vendor_code}";
                $localValue = "(^{$localValue}$)|(.*,{$localValue}$)|(^{$localValue},.*)";
                $pattern .= isset($pattern) ? '|'.$localValue : $localValue;

                $localValue = "P:{$vendor_code}";
                $localValue = "(^{$localValue}$)|(.*,{$localValue}$)|(^{$localValue},.*)";
                $pattern .= isset($pattern) ? '|'.$localValue : $localValue;
            }
            if($S && $M) {
                $localValue = "SM:{$vendor_code}";
                $localValue = "(^{$localValue}$)|(.*,{$localValue}$)|(^{$localValue},.*)";
                $pattern .= isset($pattern) ? '|'.$localValue : $localValue;
            }
            if($S && !$M) {
                $localValue = "S:{$vendor_code}";
                $localValue = "(^{$localValue}$)|(.*,{$localValue}$)|(^{$localValue},.*)";
                $pattern .= isset($pattern) ? '|'.$localValue : $localValue;

                $localValue = "SM:{$vendor_code}";
                $localValue = "(^{$localValue}$)|(.*,{$localValue}$)|(^{$localValue},.*)";
                $pattern .= isset($pattern) ? '|'.$localValue : $localValue;
            }
            if($M && !$P && !$S) {
                $localValue = "PM:{$vendor_code}";
                $localValue = "(^{$localValue}$)|(.*,{$localValue}$)|(^{$localValue},.*)";
                $pattern .= isset($pattern) ? '|'.$localValue : $localValue;

                $localValue = "SM:{$vendor_code}";
                $localValue = "(^{$localValue}$)|(.*,{$localValue}$)|(^{$localValue},.*)";
                $pattern .= isset($pattern) ? '|'.$localValue : $localValue;
            }

            return $pattern;
        }

        static public function adjustActiveContractParameterFilters(&$node, &$parameters) {

            //Handle status and year parameter
            $contractStatus = _getRequestParamValue('contstatus');
            $reqYear = _getRequestParamValue('year');

            if(isset($reqYear)){
                $data_controller_instance = data_controller_get_operator_factory_instance();
                $geCondition = $data_controller_instance->initiateHandler(GreaterOrEqualOperatorHandler::$OPERATOR__NAME, array($reqYear));
                $leCondition = $data_controller_instance->initiateHandler(LessOrEqualOperatorHandler::$OPERATOR__NAME, array($reqYear));
                $parameters['starting_year_id']= $leCondition;
                $parameters['ending_year_id']= $geCondition;
                if($contractStatus=='R'){
                    $parameters['registered_year_id']= array($reqYear);
                }
                else if($contractStatus=='A'){
                    $parameters['effective_begin_year_id']= $leCondition;
                    $parameters['effective_end_year_id']= $geCondition;
                }
            }
            unset($parameters['status_flag']);

            return $parameters;
        }
    }
}

namespace checkbook_project_custom_classes_contract {

    interface Contract  {

        function initializeAmounts();
    }

    Abstract class AbstractContract implements Contract{

        private $dataSource;
        private $agreementId;
        private $originalAmount;
        private $currentAmount;
        private $spentAmount;

        public function __construct($data_source, $agreement_id) {
            $this->dataSource = $data_source;
            $this->agreementId = $agreement_id;

            $this->initializeAmounts();
        }

        public function getOriginalAmount() { return $this->originalAmount; }
        public function setOriginalAmount($originalAmount) { $this->originalAmount = $originalAmount; }

        public function getCurrentAmount() { return $this->currentAmount; }
        public function setCurrentAmount($currentAmount) { $this->currentAmount = $currentAmount; }

        public function getSpentAmount() { return $this->spentAmount; }
        public function setSpentAmount($spentAmount) { $this->spentAmount = $spentAmount; }

        public function getDataSource() { return $this->dataSource; }
        public function setDataSource($dataSource) { $this->dataSource = $dataSource; }

        public function getAgreementId() { return $this->agreementId; }
        public function setAgreementId($agreementId) { $this->agreementId = $agreementId; }
    }

    class ChildAgreement extends AbstractContract
    {
        public function initializeAmounts() {
            $agreement_id = $this->getAgreementId();
            $query = "SELECT";

            switch($this->getDataSource()) {
                case "checkbook_oge":
                    $query .=
                        " sum(original_amount) original_amount
                        , sum(current_amount) current_amount
                        , sum(check_amount) as spent_amount
                        FROM {oge_contract_vendor_level} a
                        JOIN
                        (
                            SELECT DISTINCT contract_number
                            FROM {history_agreement}
                            WHERE agreement_id = " . $agreement_id . "
                        ) b ON a.fms_contract_number = b.contract_number
                        LEFT JOIN
                        (
                        SELECT sum(check_amount) as check_amount
                        , contract_number
                        , vendor_id
                        FROM {disbursement_line_item_details}
                        GROUP BY 2,3
                        ) c
                        ON b.contract_number = c.contract_number AND a.vendor_id = c.vendor_id limit 1";
                    break;

                default:
                    $query .=
                        " l1.contract_number
                        , l1.maximum_contract_amount as current_amount
                        , l1.original_contract_amount as original_amount
                        , l1.rfed_amount as spent_amount
                        FROM history_agreement AS l1
                        WHERE l1.original_agreement_id = " . $agreement_id . " AND l1.latest_flag = 'Y'";
                    break;
            }

            $results = _checkbook_project_execute_sql_by_data_source($query,$this->getDataSource());

            foreach ($results as $row) {
                $this->setOriginalAmount($row['original_amount']);
                $this->setCurrentAmount($row['current_amount']);
                $this->setSpentAmount($row['spent_amount']);
            }
        }

    }

    class MasterAgreement extends AbstractContract
    {
        public function initializeAmounts() {
            $master_agreement_id = $this->getAgreementId();
            $query = "SELECT";

            switch($this->getDataSource()) {
                case "checkbook_oge":
                    $query .=
                        " sum(original_amount) original_amount
                        , sum(current_amount) current_amount
                        , sum(check_amount) as spent_amount
                        FROM {oge_contract_vendor_level} a
                        JOIN
                        (
                        SELECT distinct contract_number
                        FROM {history_agreement}
                        WHERE master_agreement_id = " . $master_agreement_id . "
                        ) b ON a.fms_contract_number = b.contract_number
				        LEFT JOIN
				        (
				        SELECT sum(check_amount) as check_amount
				        , contract_number
				        , vendor_id
				        FROM {disbursement_line_item_details} GROUP BY 2,3
				        ) c ON b.contract_number = c.contract_number AND a.vendor_id = c.vendor_id limit 1";
                    break;

                default:
                    $query .=
                        " l1.contract_number,
                        l1.original_contract_amount as original_amount,
                        l1.maximum_spending_limit as current_amount,
                        l2.spent_amount as spent_amount
                        FROM history_master_agreement AS l1
                        JOIN
                        (
                            SELECT rfed_amount as spent_amount, original_agreement_id
                            FROM agreement_snapshot_expanded
                            WHERE original_agreement_id = " . $master_agreement_id . "
                            AND master_agreement_yn = 'Y'
                            AND status_flag = 'A'
                            ORDER BY fiscal_year DESC LIMIT 1
                        ) l2 ON l1.original_master_agreement_id = l2.original_agreement_id
                        WHERE l1.original_master_agreement_id = " . $master_agreement_id . "
                        AND l1.latest_flag = 'Y'";
                    break;
            }

            $results = _checkbook_project_execute_sql_by_data_source($query,$this->getDataSource());

            foreach ($results as $row) {
                $this->setOriginalAmount($row['original_amount']);
                $this->setCurrentAmount($row['current_amount']);
                $this->setSpentAmount($row['spent_amount']);
            }
        }

    }
}