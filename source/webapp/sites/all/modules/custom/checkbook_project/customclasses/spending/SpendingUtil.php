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


class SpendingUtil{

    static $landingPageParams = array("agency"=>"agency","vendor"=>"vendor","category"=>"category");

    static function getSpendingCategoryDetails($categoryId, $columns=array('spending_category_id','display_name')){
        if(!isset($categoryId)){
            return NULL;
        }

        $categoryDetails = _checkbook_project_querydataset('checkbook:category',$columns, array('spending_category_id'=>$categoryId));
        return $categoryDetails;
    }

    static  public function getSpendingTransactionsTitle(){
      $agency_id = _getRequestParamValue("agency");
      $title = "";                
      //if(isset($agency_id)){
        $title = '';// _checkbook_project_get_name_for_argument("agency_id",RequestUtil::getRequestKeyValueFromURL("agency",current_path()));
        if(preg_match('/category\/1/',current_path())){
          $title = $title . ' Contract' ;
        }
        elseif(preg_match('/category\/2/',current_path())){
          $title = $title. ' Payroll' ;
        }
        elseif(preg_match('/category\/3/',current_path())){
          $title = $title . ' Capital Contracts' ;
        }
        elseif(preg_match('/category\/4/',current_path())){
          $title = $title. ' Others' ;
        }
        elseif(preg_match('/category\/5/',current_path())){
          $title = $title . ' Trust & Agency' ;
        }
        else{
          $title = $title . ' Total' ;
        }        
      //}
      $title = $title . " Spending Transactions";
      return $title ;
    }

    static public function getDepartmentIds(){
        $bottomURL = $_REQUEST['expandBottomContURL'];
        $deptId = NULL;$deptIds = array();

        if(isset($bottomURL) && preg_match("/dept/",$bottomURL)){
             $pathParams = explode('/', $bottomURL);
             $index = array_search('dept',$pathParams);
                $deptId =  filter_xss($pathParams[($index+1)]);
        }

        if($deptId){
            $query1 = "SELECT agency_id, fund_class_id, department_code FROM ref_department WHERE department_id = " .$deptId;
            $deptInfo = _checkbook_project_execute_sql($query1);

            $query2 = "SELECT department_id, fiscal_year, year_id FROM ref_department d
                       LEFT JOIN ref_year y ON d.fiscal_year = y.year_value
                       WHERE agency_id = ".$deptInfo[0]['agency_id']
                      ." AND fund_class_id = ".$deptInfo[0]['fund_class_id']
                      ." AND department_code = ".$deptInfo[0]['department_code'];

            $result = _checkbook_project_execute_sql($query2);

            foreach($result as $key => $value){
                $deptIds[$value['year_id']] = $value['department_id'];
            }

        }

        return $deptIds;

    }


    static public function getExpenseCatIds(){
        $bottomURL = $_REQUEST['expandBottomContURL'];
        $expCatId = NULL;$expCatIds = array();

        if(isset($bottomURL) && preg_match("/expcategory/",$bottomURL)){
             $pathParams = explode('/', $bottomURL);
             $index = array_search('expcategory',$pathParams);
                $expCatId =  filter_xss($pathParams[($index+1)]);
        }

        if($expCatId){
            $query1 = "SELECT expenditure_object_code FROM ref_expenditure_object WHERE expenditure_object_id = " .$expCatId;
            $expCatInfo = _checkbook_project_execute_sql($query1);

            $query2 = "SELECT expenditure_object_id, fiscal_year, year_id FROM ref_expenditure_object e
                       LEFT JOIN ref_year y ON e.fiscal_year = y.year_value
                       WHERE expenditure_object_code = '".$expCatInfo[0]['expenditure_object_code'] . "'";

            $result = _checkbook_project_execute_sql($query2);

            foreach($result as $key => $value){
                $expCatIds[$value['year_id']] = $value['expenditure_object_id'];
            }

        }

        return $expCatIds;

    }

    /**
     * Returns Spending Footer Url based on values from current path
     *
     * @param $node
     * @return string
     */
    static function getSpendingFooterUrl($node){
        return '/panel_html/spending_transactions/spending/transactions'
        . _checkbook_project_get_url_param_string("vendor")
        . _checkbook_project_get_url_param_string("agency")
        . _checkbook_project_get_url_param_string("category")
        . _checkbook_project_get_url_param_string("industry")
        . '/dtsmnid/' . $node->nid
        . _checkbook_project_get_year_url_param_string(false,false,true)
        . _checkbook_append_url_params();
    }

    /**
     * Returns Spending Footer Url based on values from current path
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getSpendingLinkUrl($node, $row){
        return '/panel_html/spending_transactions/spending/transactions'
        . _checkbook_project_get_url_param_string("agency")
        . _checkbook_project_get_url_param_string("vendor")
        . _checkbook_project_get_url_param_string("category")
        . _checkbook_project_get_url_param_string("industry")
        . '/dtsmnid/' . $node->nid
        . _checkbook_project_get_year_url_param_string(false,false,true)
        . _checkbook_append_url_params();

    }

    /** Returns Spending Footer Url based on values from current path,
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getAgencyNameLinkUrl($node, $row){
        //agency_name_link
        $url = '/spending_landing'
            . _checkbook_project_get_url_param_string("vendor")
            . _checkbook_project_get_url_param_string("category")
            . _checkbook_project_get_url_param_string("industry")
            . _checkbook_project_get_year_url_param_string()
            . _checkbook_append_url_params()
            . '/agency/' . (isset($row["agency_id"]) ? $row["agency_id"] : $row["agency_agency"]);

        return $url;
    }

    /**
     * Returns Agency Amount Link Url based on values from current path & data row
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getAgencyAmountLinkUrl($node, $row){
        //agency_amount_link
        return '/panel_html/spending_transactions/spending/transactions'
        . '/smnid/' . $node->nid
        . '/agency/'. $row["agency_agency"]
        . _checkbook_project_get_url_param_string("vendor")
        . _checkbook_project_get_url_param_string("category")
        . _checkbook_project_get_url_param_string("industry")
        .  _checkbook_project_get_year_url_param_string(false,false,true)
        . _checkbook_append_url_params();
    }

    /**
     * Returns Vendor Name Link Url based on values from current path & data row,
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getVendorNameLinkUrl($node, $row){
        //vendor_name_link
        return '/spending_landing'
        . _checkbook_project_get_url_param_string("agency")
        . _checkbook_project_get_url_param_string("category")
        . _checkbook_project_get_url_param_string("industry")
        . _checkbook_project_get_year_url_param_string()
        . _checkbook_append_url_params()
        . '/vendor/' . (isset($row["vendor_id"]) ? $row["vendor_id"] : $row["vendor_vendor"]);
    }

    /**
     * Returns Sub Vendor Name Link Url based on values from current path & data row,
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getSubVendorNameLinkUrl($node, $row){
        //vendor_name_link
        return '/spending_landing'
        . _checkbook_project_get_url_param_string("agency")
        . _checkbook_project_get_url_param_string("category")
        . _checkbook_project_get_url_param_string("industry")
        . _checkbook_project_get_year_url_param_string()
        . _checkbook_project_get_url_param_string("mwbe")
        . '/subvendor/' . $row["sub_vendor_sub_vendor"];
    }

    /**
     * Returns Prime Vendor Name Link Url based on values from current path & data row,
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getPrimeVendorNameLinkUrl($node, $row){
        //vendor_name_link
        return '/spending_landing'
        . _checkbook_project_get_url_param_string("agency")
        . _checkbook_project_get_url_param_string("category")
        . _checkbook_project_get_url_param_string("industry")
        . _checkbook_project_get_year_url_param_string()
        . _checkbook_append_url_params()
        . '/vendor/' . $row["prime_vendor_prime_vendor"];
    }

    /**
     * Returns Vendor Amount Link Url based on values from current path & data row
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getVendorAmountLinkUrl($node, $row){
        //vendor_amount_link
        return '/panel_html/spending_transactions/spending/transactions'
        . _checkbook_project_get_url_param_string("agency")
        . '/vendor/'. $row["vendor_vendor"]
        . _checkbook_project_get_url_param_string("category")
        . _checkbook_project_get_url_param_string("industry")
        . _checkbook_project_get_year_url_param_string(false,false,true)
        . '/smnid/' . $node->nid
        . _checkbook_append_url_params();
    }

    /**
     * Returns Department Amount Link Url based on values from current path & data row
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getDepartmentAmountLinkUrl($node, $row){
        //department_amount_link
        return '/panel_html/spending_transactions/spending/transactions'
        . _checkbook_project_get_year_url_param_string(false,false,true)
        . '/smnid/' . $node->nid
        . '/agency/' . $row["agency_agency"]
        . '/dept/' . $row["department_department"]
        . _checkbook_project_get_url_param_string("vendor")
        . _checkbook_project_get_url_param_string("category")
        . _checkbook_project_get_url_param_string("industry")
        . _checkbook_append_url_params();
    }

    /** Returns Check Amount Sum Link Url based on values from current path & data row */
    static function getCheckAmountSumLinkUrl($node, $row){
        //formatted_check_amount_sum_link
        return '/panel_html/spending_transactions/spending/transactions'
        .  _checkbook_project_get_year_url_param_string(false,false,true)
        . '/smnid/' . $node->nid
        . _checkbook_project_get_url_param_string("agency")
        . _checkbook_project_get_url_param_string("vendor")
        . _checkbook_project_get_url_param_string("category")
        . _checkbook_project_get_url_param_string("industry")
        . '/expcategory/' .  $row["expenditure_object_expenditure_object"]
        . _checkbook_append_url_params();
    }

    /**
     * Returns Contract Amount Link Url based on values from current path & data row
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getContractAmountLinkUrl($node, $row){
        //contract_amount_link
        return '/panel_html/spending_transactions/spending/transactions'
        . '/smnid/' . $node->nid
        . _checkbook_project_get_year_url_param_string(false,false,true)
        .  _checkbook_project_get_contract_url($row["document_id_document_id"], $row["agreement_id_agreement_id"])
        . _checkbook_append_url_params();
    }

    /**
     * Returns Contract Number Link Url based on values from current path & data row
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getContractNumberLinkUrl($node, $row){
        //contract_number_link
        return '/contract_details'
        . _checkbook_append_url_params()
        . _checkbook_project_get_contract_url($row["document_id_document_id"], $row["agreement_id_agreement_id"])
        .'/newwindow';
    }

    /**
     * Returns Industry Name Link Url based on values from current path & data row
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getIndustryNameLinkUrl($node, $row){
        //industry_name_link
        return '/spending_landing'
        . _checkbook_project_get_url_param_string("agency")
        . _checkbook_project_get_url_param_string("vendor")
        . _checkbook_project_get_url_param_string("category")
        . _checkbook_project_get_year_url_param_string()
        . _checkbook_append_url_params()
        . '/industry/'. $row['industry_industry_industry_type_id'];
    }

    /**
     * Returns Industry Ytd Spending Link Url based on values from current path & data row
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getIndustryYtdSpendingLinkUrl($node, $row){
        //ytd_spending_link
        return '/panel_html/spending_transactions/spending/transactions'
        .  _checkbook_project_get_year_url_param_string(false,false,true)
        . '/smnid/' . $node->nid
        . _checkbook_project_get_url_param_string("vendor")
        . _checkbook_project_get_url_param_string("category")
        . _checkbook_project_get_url_param_string("agency")
        . '/industry/'. $row['industry_industry_industry_type_id']
        . _checkbook_append_url_params();
    }

    /**
     * Returns Sub Vendor YTD Spending Link Url based on values from current path & data row
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getSubVendorYtdSpendingUrl($node, $row){
        //ytd_spending_sub_vendors_link
        return '/panel_html/spending_transactions/spending/transactions'
        . _checkbook_project_get_url_param_string("agency")
        . _checkbook_project_get_url_param_string("category")
        . _checkbook_project_get_url_param_string("industry")
        . _checkbook_project_get_year_url_param_string(false,false,true)
        . '/smnid/' . $node->nid
        . _checkbook_append_url_params();
    }
    /**
     * Returns Agency YTD Spending Link Url based on values from current path & data row.
     * This is for sub vendors Top 5 Agencies widget
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getAgencyYtdSpendingUrl($node, $row){
        //ytd_spending_sub_vendors_link
        return '/spending/transactions'
        . '/agency/'. $row["agency_agency"]
        . _checkbook_project_get_url_param_string("vendor")
        . _checkbook_project_get_url_param_string("category")
        . _checkbook_project_get_url_param_string("industry")
        . _checkbook_project_get_year_url_param_string(false,false,true)
        . '/smnid/' . $node->nid . '/dtsmnid/' . $node->nid .'/newwindow';
    }

    /** Returns Ytd Spending percent */
    static function getPercentYtdSpending($node, $row){
        $ytd_spending = $row['check_amount_sum']/$node->totalAggregateColumns['check_amount_sum']*100;
        $ytd_spending = $ytd_spending < 0 ? 0.00 : $ytd_spending;
        return  custom_number_formatter_format($ytd_spending,2,'','%');
    }

    /**
     * Returns percent paid to sub vendors defined as:
     * The sum of all checks issued to all sub vendors associated to each agency
     * within the selected fiscal year or calendar year divided by the sum of all checks
     * issued to all vendors associated to the same agency and within the same fiscal (without payroll)
     * year or calendar multiplied by 100% and display the results as '% Paid Sub Vendors'
     *
     * @param $row
     * @return string
     */
    static function getSubVendorsPercentPaid($row){
        return self::calculatePercent($row['ytd_spending_sub_vendors'], $row['check_amount_sum@checkbook:spending_data_no_payroll']);
    }

    /**
     * Given a numerator and denominator, calculates the percent.
     * Returns value with up to 2 decimal places.
     * If the value is negative, 0 is returned.
     *
     * @param $numerator
     * @param $denominator
     * @return string
     */
    static function calculatePercent($numerator, $denominator){
        $results = $numerator/$denominator*100;
        $results = $results < 0 ? 0.00 : $results;
        return custom_number_formatter_format($results,2,'','%');
    }

    /**
     * Checks to see if this is from the Advanced search page,
     * if so, need to append the data source but not the m/wbe parameter.
     */
    static function getDataSourceParams(){
        if(self::isAdvancedSearchResults()) {
            $data_source = _getRequestParamValue("datasource");
            return isset($data_source) ? "/datasource/checkbook_oge" : "";
        }
        return _checkbook_append_url_params();
    }

    /**
     * Returns true if this is from spending advanced search for citywide
     OR if this is from the transaction page for M/WBE.
     */
    static function showMwbeFields() {
        $is_mwbe = _checkbook_check_is_mwbe_page();
        $is_mwbe = $is_mwbe || (!_checkbook_check_isEDCPage() && self::isAdvancedSearchResults());
        return $is_mwbe;
    }

    /**
     * Returns true if this is from spending advanced search
     */
    static function isAdvancedSearchResults() {
        return !self::isSpendingLanding();
    }

    /**
     * Returns true if this is the spending landing page
     */
    static function isSpendingLanding() {
        $url_ref = $_SERVER['HTTP_REFERER'];
        $match_landing = '"/spending_landing/"';
        return preg_match($match_landing,$url_ref);
    }


    /**
     * Returns the vendor type
     */
    static function getVendorType($node) {
        $vendor_type = ($node->is_sub_vendor) ? ($node->is_mwbe ? 'SM' : 'S') : ($node->is_mwbe ? 'PM' : 'P');
        return $vendor_type;
    }

    /**
     * Spending transaction page should be shown for citywide, oge, m/wbe
     * @return bool
     */
    static function showSpendingTransactionPage(){
        $subvendor_exist = _checkbook_check_is_sub_vendor_page();
        $ma1_mma1_contracts_exist = _checkbook_project_ma1_mma1_exist();
        $edc_records_exist = _checkbook_check_isEDCPage() && _checkbook_project_recordsExists(6);
        $mwbe_records_exist = _checkbook_check_is_mwbe_page() && !$subvendor_exist && _checkbook_project_recordsExists(706);
        $citywide_exist =  !$subvendor_exist && !$mwbe_records_exist && !$edc_records_exist && _checkbook_project_recordsExists(6);

        if($ma1_mma1_contracts_exist || $subvendor_exist) return false;

        return ($edc_records_exist || $mwbe_records_exist || $citywide_exist);
    }

    /**
     * Spending transaction no results page should be shown for citywide, oge, m/wbe
     * @return bool
     */
    static function showNoSpendingTransactionPage(){
        $subvendor_exist = _checkbook_check_is_sub_vendor_page();
        $ma1_mma1_contracts_exist = _checkbook_project_ma1_mma1_exist();
        $edc_records_exist = _checkbook_check_isEDCPage() && _checkbook_project_recordsExists(6);
        $mwbe_records_exist = _checkbook_check_is_mwbe_page() && !$subvendor_exist && _checkbook_project_recordsExists(706);
        $citywide_exist =  !$subvendor_exist && !$mwbe_records_exist && !$edc_records_exist && _checkbook_project_recordsExists(6);

        if($ma1_mma1_contracts_exist || $subvendor_exist) return false;

        return $subvendor_exist || $ma1_mma1_contracts_exist || $edc_records_exist;
    }
}
