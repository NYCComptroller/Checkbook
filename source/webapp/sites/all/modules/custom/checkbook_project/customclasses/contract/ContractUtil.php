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

        /* Returns M/WBE category for the given vendor id in the given year and year type for city-wide Active/Registered Contracts*/

        static public function get_contract_vendor_minority_category($vendor_id, $year_id, $year_type,$agency_id = null){
        	
        	$latest_minority_id = self::getLatestMwbeCategoryByVendor($vendor_id, $agency_id = null, $year_id = null, $year_type = null, $is_prime_or_sub = null);
        	
            if($latest_minority_id != null){            	
                if(!_getRequestParamValue('mwbe'))
                    return '/dashboard/mp/mwbe/2~3~4~5~9';
            }
            return '';
        }
        
        
        
        static public function getLatestMwbeCategoryByVendor($vendor_id, $agency_id = null, $year_id = null, $year_type = null, $is_prime_or_sub = null){
        	STATIC $contract_vendor_latest_mwbe_category;
        
        	if($agency_id == null){
        		$agency_id =  _getRequestParamValue('agency');
        	}
        
        	if($year_id == null){
        		$year_id =  _getRequestParamValue('year');
        	}
        
        	if($year_type == null){
        		$year_type =  _getRequestParamValue('yeartype');
        	}
        
        	if($is_prime_or_sub == null){
        		$is_prime_or_sub =  $is_prime_or_sub = (RequestUtil::isDashboardFlowSubvendor()) ? "S":"P";
        	}
        
        	$latest_minority_type_id = null;
        	if(!isset($contract_vendor_latest_mwbe_category)){
        		$query = "SELECT vendor_id, agency_id, year_id, type_of_year, minority_type_id, is_prime_or_sub
                      FROM contract_vendor_latest_mwbe_category
                      WHERE minority_type_id IN (2,3,4,5,9) AND year_id = '".$year_id."' AND type_of_year = '".$year_type."'
                      GROUP BY vendor_id, agency_id, year_id, type_of_year, minority_type_id, is_prime_or_sub";
        
        		$results = _checkbook_project_execute_sql_by_data_source($query,'checkbook');
        		foreach($results as $row){
        			if(isset($row['agency_id'])) {
        				$contract_vendor_latest_mwbe_category[$row['vendor_id']][$row['agency_id']][$row['is_prime_or_sub']]['minority_type_id'] = $row['minority_type_id'];
        			}
        			else {
        				$contract_vendor_latest_mwbe_category[$row['vendor_id']][$row['is_prime_or_sub']]['minority_type_id'] = $row['minority_type_id'];
        			}
        
        		}
        	}
        
        	$latest_minority_type_id = isset($agency_id)
        	? $contract_vendor_latest_mwbe_category[$vendor_id][$agency_id][$is_prime_or_sub]['minority_type_id']
        	: $contract_vendor_latest_mwbe_category[$vendor_id][$is_prime_or_sub]['minority_type_id'];
        	return $latest_minority_type_id;
        }

        /* Returns M/WBE category for the given vendor id in the given year and year type for contracts Advanced Serach results*/

        static public function get_contract_vendor_link($vendor_id, $is_prime_or_sub, $minority_type_id){
               if($is_prime_or_sub == "S" && in_array($minority_type_id, array(2,3,4,5,9))){
                    return "/dashboard/ms/subvendor/".$vendor_id;
               }elseif($is_prime_or_sub == "S" && !in_array($minority_type_id, array(2,3,4,5,9))){
                    return "/dashboard/ss/subvendor/".$vendor_id;
               }else if($is_prime_or_sub == "P" && in_array($minority_type_id, array(2,3,4,5,9))){
                    return "/dashboard/mp/mwbe/2~3~4~5~9/vendor/".$vendor_id;
               }else{
                   return "/vendor/".$vendor_id;
               }

        }

        /* Returns M/WBE category of a vendor id in citywide pending contracts*/

        static public function get_pending_contract_vendor_minority_category($vendor_id){
            STATIC $mwbe_vendors;
            if(!isset($mwbe_vendors)){
                $query = "SELECT vendor_id FROM pending_contracts WHERE is_prime_or_sub='P' AND minority_type_id IN (2,3,4,5,9)
                            GROUP BY vendor_id";
                $results = _checkbook_project_execute_sql_by_data_source($query,'checkbook');
                foreach($results as $row){
                    $mwbe_vendors[$row['vendor_id']] = $row['vendor_id'];
                }
            }
            if($mwbe_vendors[$vendor_id] == $vendor_id){
                if(!_getRequestParamValue('mwbe'))
                    return '/dashboard/mp/mwbe/2~3~4~5~9';
            }
            return '';
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