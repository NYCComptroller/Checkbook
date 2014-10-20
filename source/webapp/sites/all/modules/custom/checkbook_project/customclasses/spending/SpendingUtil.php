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

    static $landingPageParams = array("agency"=>"agency","vendor"=>"vendor","subvendor"=>"subvendor","category"=>"category","industry"=>"industry","mwbe"=>"mwbe","dashboard"=>"dashboard");

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
        $url = '/panel_html/spending_transactions/spending/transactions'
        . _checkbook_project_get_url_param_string("vendor")
        . _checkbook_project_get_url_param_string("agency")
        . _checkbook_project_get_url_param_string("category")
        . _checkbook_project_get_url_param_string("industry")
        . '/dtsmnid/' . $node->nid
        . _checkbook_project_get_year_url_param_string(false,false,true)
        . _checkbook_append_url_params();

        return $url;
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
        $custom_params = array('agency'=>(isset($row["agency_id"]) ? $row["agency_id"] : $row["agency_agency"]));
        return '/' . self::getLandingPageWidgetUrl($custom_params);
    }

    /**
     * Returns Agency Amount Link Url based on values from current path & data row.
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getAgencyAmountLinkUrl($node, $row){
        //agency_amount_link
        $custom_params = array('agency'=>$row["agency_agency"]);
        return '/' . self::getSpendingTransactionPageUrl($custom_params). '/smnid/' . $node->nid;
    }

    /**
     * Returns Sub Vendor Name Link Url based on values from current path & data row,
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getSubVendorNameLinkUrl($node, $row){
        $override_params = null;
        $vendor_id = isset($row["sub_vendor_sub_vendor"]) ? $row["sub_vendor_sub_vendor"] : $row["vendor_id"];
        $year_id = _getRequestParamValue("year");
        $year_type = _getRequestParamValue("yeartype");
        $agency_id = _getRequestParamValue("agency_id");
        $dashboard = _getRequestParamValue("dashboard");

        return self::getSubVendorLink($vendor_id, $agency_id, $year_id, $year_type, $dashboard);
    }

    /**
     * Returns Prime Vendor Name Link Url based on values from current path & data row
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getPrimeVendorNameLinkUrl($node, $row){

        $vendor_id = isset($row["prime_vendor_id"]) ? $row["prime_vendor_id"] : $row["prime_vendor_prime_vendor"];
        if(!isset($vendor_id)) {
            $vendor_id = isset($row["vendor_id"]) ? $row["vendor_id"] : $row["vendor_vendor"];
        }
        $year_id = _getRequestParamValue("year");
        $year_type = _getRequestParamValue("yeartype");
        $agency_id = _getRequestParamValue("agency_id");
        $dashboard = _getRequestParamValue("dashboard");

        return self::getPrimeVendorLink($vendor_id, $agency_id, $year_id, $year_type, $dashboard);
    }

    /**
     * Returns Prime Vendor Name Link Url based on values from current path & data row.
     * This is for the advanced search page, if no year is provided, we use the current year
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getPayeeNameLinkUrl($node, $row){

        $year = _getRequestParamValue("year");
        $calyear = _getRequestParamValue("calyear");
        $year_type = isset($calyear) ? "C" : "B";
        $year_id = isset($calyear) ? $calyear : (isset($year) ? $year : _getCurrentYearID());
        $vendor_id = $row["vendor_id"];
        $agency_id = _getRequestParamValue("agency_id");
        $dashboard = _getRequestParamValue("dashboard");

        return $row["is_sub_vendor"] == "No"
            ? self::getPrimeVendorLink($vendor_id, $agency_id, $year_id, $year_type, $dashboard)
            : self::getSubVendorLink($vendor_id, $agency_id, $year_id, $year_type, $dashboard);

    }

    /**
     * Returns Prime Vendor Name Link Url based on values from current path & data row
     *
     * if vendor is M/WBE certified - go to M/WBE dashboard
     * if vendor is NOT M/WBE certified - go to citywide (default) dashboard
     *
     * if switching from citywide->M/WBE OR M/WBE->citywide,
     * then persist only agency filter (mwbe & vendor if applicable)
     *
     * if remaining in the same dashboard persist all filters (drill-down) except sub vendor
     *
     * @param $vendor_id
     * @param $agency_id
     * @param $year_id
     * @param $year_type
     * @param $current_dashboard
     * @return string
     */
    static function getPrimeVendorLink($vendor_id, $agency_id, $year_id, $year_type, $current_dashboard){

        $override_params = null;
        $latest_certified_minority_type_id = self::getLatestMwbeCategoryByVendor($vendor_id, $agency_id, $year_id, $year_type, "P");
        $is_mwbe_certified = isset($latest_certified_minority_type_id);

        //if M/WBE certified, go to M/WBE dashboard else if NOT M/WBE certified, go to citywide
        $new_dashboard = $is_mwbe_certified ? "mp" : null;

        //if switching between dashboard, persist only agency filter (mwbe & vendor if applicable)
        if($current_dashboard != $new_dashboard) {
            $override_params = array(
                "dashboard"=>$new_dashboard,
                "mwbe"=>$is_mwbe_certified ? "2~3~4~5~9" : null,
                "agency"=>$agency_id,
                "vendor"=>$vendor_id,
                "subvendor"=>null,
                "category"=>null,
                "industry"=>null
            );
        }
        //if remaining in the same dashboard persist all filters (drill-down) except sub vendor
        else {
            $override_params = array(
                "dashboard"=>$new_dashboard,
                "subvendor"=>null,
                "vendor"=>$vendor_id
            );
        }
        return '/' . self::getLandingPageWidgetUrl($override_params);
    }

    /**
     * Returns Sub Vendor Name Link Url based on values from current path & data row
     *
     * if sub vendor is M/WBE certified - go to M/WBE (Sub Vendor) dashboard
     * if sub vendor is NOT M/WBE certified - go to Sub Vendor dashboard
     *
     * if switching from citywide->M/WBE OR M/WBE->citywide,
     * then persist only agency filter (mwbe & vendor if applicable)
     *
     * if remaining in the same dashboard persist all filters (drill-down) except sub vendor
     *
     * @param $vendor_id
     * @param $agency_id
     * @param $year_id
     * @param $year_type
     * @param $current_dashboard
     * @return string
     */
    static function getSubVendorLink($vendor_id, $agency_id, $year_id, $year_type, $current_dashboard){

        $override_params = null;
        $new_dashboard = null;
        $latest_certified_minority_type_id = self::getLatestMwbeCategoryByVendor($vendor_id, $agency_id, $year_id, $year_type, "S");
        $is_mwbe_certified = isset($latest_certified_minority_type_id);

        //if M/WBE certified, go to M/WBE (Sub Vendor) else if NOT M/WBE certified, go to Sub Vendor dashboard
        $new_dashboard = $is_mwbe_certified ? "ms" : "ss";

        //if switching between dashboard, persist only agency filter (mwbe & subvendor if applicable)
        if($current_dashboard != $new_dashboard) {
            $override_params = array(
                "dashboard"=>$new_dashboard,
                "mwbe"=>$is_mwbe_certified ? "2~3~4~5~9" : null,
                "agency"=>$agency_id,
                "subvendor"=>$vendor_id,
                "vendor"=>null,
                "category"=>null,
                "industry"=>null
            );
        }
        //if remaining in the same dashboard persist all filters (drill-down) except vendor
        else {
            $override_params = array(
                "dashboard"=>$new_dashboard,
                "subvendor"=>$vendor_id,
                "vendor"=>null
            );
        }
        return '/' . self::getLandingPageWidgetUrl($override_params);
    }

    /**
     * Returns M/WBE category for the given vendor id in the given year and year type
     *
     * @param $vendor_id
     * @param $agency_id
     * @param $year_id
     * @param $year_type
     * @param string $is_prime_or_sub
     * @return null
     */
    static public function getLatestMwbeCategoryByVendor($vendor_id, $agency_id = null, $year_id = null, $year_type = null, $is_prime_or_sub = "P"){
        STATIC $spending_vendor_latest_mwbe_category;

        if($agency_id == null){
        	$agency_id =  _getRequestParamValue('agency');
        }
        
        if($year_id == null){
        	$year_id =  _getRequestParamValue('year');
        }

        if($year_type == null){
        	$year_type =  _getRequestParamValue('yeartype');
        }
        

        
        $latest_minority_type_id = null;
        if(!isset($spending_vendor_latest_mwbe_category)){
            $query = "SELECT vendor_id, agency_id, year_id, type_of_year, minority_type_id, is_prime_or_sub
                      FROM spending_vendor_latest_mwbe_category
                      WHERE minority_type_id IN (2,3,4,5,9) AND year_id = '".$year_id."' AND type_of_year = '".$year_type."'
                      GROUP BY vendor_id, agency_id, year_id, type_of_year, minority_type_id, is_prime_or_sub";

            $results = _checkbook_project_execute_sql_by_data_source($query,'checkbook');
            foreach($results as $row){
                if(isset($row['agency_id'])) {
                    $spending_vendor_latest_mwbe_category[$row['vendor_id']][$row['agency_id']][$row['is_prime_or_sub']]['minority_type_id'] = $row['minority_type_id'];
                }
                else {
                    $spending_vendor_latest_mwbe_category[$row['vendor_id']][$row['is_prime_or_sub']]['minority_type_id'] = $row['minority_type_id'];
                }

            }
        }

        $latest_minority_type_id = isset($agency_id)
            ? $spending_vendor_latest_mwbe_category[$vendor_id][$agency_id][$is_prime_or_sub]['minority_type_id']
            : $spending_vendor_latest_mwbe_category[$vendor_id][$is_prime_or_sub]['minority_type_id'];
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

        if($results[0]['minority_type_id'] != ''){
            return true;
        }
        else{
            return false;
        }
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
        $custom_params = array('vendor'=>isset($row["vendor_vendor"]) ? $row["vendor_vendor"] : $row["prime_vendor_prime_vendor"]);
        return '/' . self::getSpendingTransactionPageUrl($custom_params). '/smnid/' . $node->nid;
    }

    /**
     * Returns Prime Vendor Amount Link Url based on values from current path & data row
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getPrimeVendorAmountLinkUrl($node, $row){
        //vendor_amount_link
        return '/panel_html/spending_transactions/spending/transactions'
        . _checkbook_project_get_url_param_string("agency")
        . '/vendor/'. $row["prime_vendor_prime_vendor"]
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
     * @param $row
     * @param $node
     * @return null|string
     */
//    static function prepareContractNumberLink($row, $node) {
//        if($row['spending_category_name'] == 'Payroll' ||  $row['spending_category_name'] == 'Others') {
//            return 'N/A';
//        }
//
//        if(empty($row[agreement_id])){
//            return $row[reference_document_number];
//        }
//self::getSpendingContractDetailsPageUrl();
//        $link = NULL;
//        $docType = $row['reference_document_code'];
//
//        $link = "<a class='new_window' href='/contract_details" . _checkbook_append_url_params() . _checkbook_project_get_contract_url($row[reference_document_number], $row[agreement_id])  ."/newwindow'>"  . $row[reference_document_number] . "</a>";
//
//        return $link;
//    }
//
//    /**
//     * @param $row
//     * @param $node
//     * @return null|string
//     */
//    static function prepareSubContractNumberLink($row, $node) {
//        //'<a class=\"new_window\" href=\"' . SpendingUtil::getSubContractNumberLinkUrl($node,$row) . '\">'  . $row[document_id_document_id] . '</a>'
//        if(isset($row['spending_category_name'])) {
//            if($row['spending_category_name'] == 'Payroll' ||  $row['spending_category_name'] == 'Others') {
//                return 'N/A';
//            }
//        }
//
//        $agreement_id = isset($row["sub_contract_number_sub_contract_number_original_agreement_id"])
//            ? $row["sub_contract_number_sub_contract_number_original_agreement_id"]
//            : $row["original_agreement_id@checkbook:sub_vendor_agid"];
//        $document_id = isset($row["document_id_document_id"])
//            ? $row["document_id_document_id"]
//            : $row["reference_document_code"];
//
//        if(empty($row[$agreement_id])){
//            return $row[$document_id];
//        }
//
//        $contract_type = _get_contract_type($document_id);
//        if(strtolower($contract_type) == 'mma1' || strtolower($contract_type) == 'ma1'){
//            return '/magid/'.$agreement_id.'/doctype/'.$contract_type;
//        }else{
//            return '/agid/'.$agreement_id.'/doctype/'.$contract_type;
//        }
//        $custom_params = array()
//
//        $custom_params = array('industry'=>isset($row['industry_industry_industry_type_id']) ? $row['industry_industry_industry_type_id'] : $row['industry_type_industry_type']);
//
//        $link = NULL;
//        $docType = $row['reference_document_code'];
//
//        $contract_type = _get_contract_type($contnum);
//        if(strtolower($contract_type) == 'mma1' || strtolower($contract_type) == 'ma1'){
//            return '/magid/'.$agreement_id.'/doctype/'.$contract_type;
//        }else{
//            return '/agid/'.$agreement_id.'/doctype/'.$contract_type;
//        }
//        $url = self::getSpendingContractDetailsPageUrl();
//
//            //_checkbook_project_get_contract_url($row[reference_document_number], $row[agreement_id])  ."/newwindow'
//
//            $custom_params = array('industry'=>isset($row['industry_industry_industry_type_id']) ? $row['industry_industry_industry_type_id'] : $row['industry_type_industry_type']);
//        return '/' . self::getLandingPageWidgetUrl($custom_params);
//
//        if( RequestUtil::isExpandBottomContainer() ){
//            $link = '<a href=/panel_html/contract_transactions/contract_details/agid/' . $row['agreement_id'] .  '/doctype/' . $docType .  _checkbook_append_url_params() . ' class=bottomContainerReload>'. $row['reference_document_number'] . '</a>';
//        }else if( RequestUtil::isNewWindow() ){
//            $link = '<span href=/contracts_landing/status/A'
//                . _checkbook_project_get_year_url_param_string()
//                . _checkbook_append_url_params()
//                . '?expandBottomContURL=/panel_html/contract_transactions/contract_details/agid/' . $row['agreement_id'] .  '/doctype/' . $docType . _checkbook_append_url_params()
//                .  ' class=loadParentWindow>'. $row['reference_document_number'] . '</span>';
//        }else {
//            $link = "<a class='new_window' href='/contract_details" . _checkbook_append_url_params() . _checkbook_project_get_contract_url($row[reference_document_number], $row[agreement_id])  ."/newwindow'>"  . $row[reference_document_number] . "</a>";
//        }
//
//        return $link;
//    }

    /**
     * Returns Contract Number Link Url based on values from current path & data row
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getContractNumberLinkUrl($node, $row){
        //contract_number_link
        $agreement_id = isset($row["agreement_id_agreement_id"])
            ? $row["agreement_id_agreement_id"]
            : $row["agreement_id"];
        $document_id = isset($row["document_id_document_id"])
            ? $row["document_id_document_id"]
            : $row["reference_document_code"];
        return '/contract_details'
        . _checkbook_append_url_params()
        . _checkbook_project_get_contract_url($document_id,$agreement_id)
        .'/newwindow';
    }

    /**
     * Returns Sub Contract Number Link Url based on values from current path & data row
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getSubContractNumberLinkUrl($node, $row){
        //contract_number_link
        $agreement_id = isset($row["sub_contract_number_sub_contract_number_original_agreement_id"])
            ? $row["sub_contract_number_sub_contract_number_original_agreement_id"]
            : $row["original_agreement_id@checkbook:sub_vendor_agid"];
        $document_id = isset($row["document_id_document_id"])
            ? $row["document_id_document_id"]
            : $row["reference_document_code"];
        return '/contract_details'
        . _checkbook_append_url_params()
        . _checkbook_project_get_contract_url($document_id, $agreement_id)
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
        $custom_params = array('industry'=>isset($row['industry_industry_industry_type_id']) ? $row['industry_industry_industry_type_id'] : $row['industry_type_industry_type']);
        return '/' . self::getLandingPageWidgetUrl($custom_params);
    }

    /**
     * Returns Industry Ytd Spending Link Url based on values from current path & data row
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getIndustryYtdSpendingLinkUrl($node, $row){
        $custom_params = array('industry'=>isset($row['industry_industry_industry_type_id']) ? $row['industry_industry_industry_type_id'] : $row['industry_type_industry_type']);
        return '/' . self::getSpendingTransactionPageUrl($custom_params). '/smnid/' . $node->nid;
    }

    /**
     * Returns Sub Vendor YTD Spending Link Url based on values from current path & data row
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getSubVendorYtdSpendingUrl($node, $row){
        $custom_params = array('subvendor'=>$row['sub_vendor_sub_vendor']);
        return '/' . self::getSpendingTransactionPageUrl($custom_params). '/smnid/' . $node->nid;
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
     * Returns Ytd Spending percent for both vendor and sub vendor spending
     *  NYCCHKBK-4263
     *
     * @param $node
     * @param $row
     * @param $data_set
     * @return string
     */
    static function getPercentYtdSpendingVendorSubVendor($node, $row, $data_set){
        $sum_vendor_sub_vendor = $row['check_amount_sum'] + $row['check_amount_sum@checkbook:'.$data_set];
        $sum_vendor_sub_vendor_total = $node->totalAggregateColumns['check_amount_sum'] + $node->totalAggregateColumns['check_amount_sum@checkbook:'.$data_set];

        $ytd_spending = $sum_vendor_sub_vendor/$sum_vendor_sub_vendor_total*100;
        $ytd_spending = $ytd_spending < 0 ? 0.00 : $ytd_spending;
        return  custom_number_formatter_format($ytd_spending,2,'','%');
    }

    /**
     * Returns M/WBE Category Link Url
     * @param $node
     * @param $row
     * @return string
     */
    static function getMWBECategoryLinkUrl($node, $row){
        $dashboard = _getRequestParamValue("dashboard");
        $custom_params = array(
            'dashboard'=>(preg_match('/p/', $dashboard)) ? "mp" : "ms",
            'mwbe'=>(isset($row["minority_type_id"]) ? $row["minority_type_id"] : $row["minority_type_minority_type"])
        );
        return '/' . self::getLandingPageWidgetUrl($custom_params) . '?expandBottomCont=true';
    }

    /**
     *  Returns a spending landing page Url with custom parameters appended but instead of persisted
     *
     * @param array $override_params
     * @return string
     */
    static function getLandingPageWidgetUrl($override_params = array()) {
        return self::getSpendingUrl('spending_landing',$override_params);
    }

    /**
     *  Returns a spending transaction page Url with custom parameters appended but instead of persisted
     *
     * @param array $override_params
     * @return string
     */
    static function getSpendingTransactionPageUrl($override_params = array()) {
        return self::getSpendingUrl('panel_html/spending_transactions/spending/transactions',$override_params);
    }

    /**
     *  Returns a spending contract details page Url with custom parameters appended but instead of persisted
     *
     * @param array $override_params
     * @return string
     */
    static function getSpendingContractDetailsPageUrl($override_params = array()) {
        return self::getSpendingUrl('contract_details',$override_params);
    }

    /**
     * Function build the url using the path and the current Spending URL parameters.
     * The Url parameters can be overridden by the override parameter array.
     *
     * @param $path
     * @param array $override_params
     * @return string
     */
    static function getSpendingUrl($path, $override_params = array()) {

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
                    $url .= "/$key";
                    $url .= "/$value";
                }
            }
        }

        return $url;
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
        return self::calculatePercent($row['ytd_spending_sub_vendors'], $row['check_amount_sum_no_payroll@checkbook:spending_data']);
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
     * Spending transaction page should be shown for citywide, oge
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
     * Spending transaction no results page should be shown for citywide, oge
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
    
    
    static function getMWBENYCLegend($year, $yeartype){
    	
    	$where_filter =  "where year_id = $year and type_of_year = '$yeartype' ";
    	
    	$prime_sql = 'select rm.minority_type_id, rm.minority_type_name , sum(total_spending_amount) total_spending
	    from aggregateon_mwbe_spending_coa_entities a1
	    join ref_minority_type rm on rm.minority_type_id = a1.minority_type_id
	   ' . $where_filter . '
	    group by rm.minority_type_id, rm.minority_type_name  ';
    	
    	$prime_spending_rows = _checkbook_project_execute_sql($prime_sql);
    	foreach($prime_spending_rows as $row){
    		switch($row['minority_type_id']){
    			case '2':
    				$mwbe_spending_prime += $row['total_spending'];
    				break;
    			case '3':
    				$mwbe_spending_prime += $row['total_spending'];
    				break;
    			case '4':
    				$mwbe_spending_prime += $row['total_spending'];
    				break;
    			case '5':
    				$mwbe_spending_prime += $row['total_spending'];
    				break;
    			case '7':
    				$non_mwbe_spending_prime += $row['total_spending'];
    				break;
    			case '9':
    				$mwbe_spending_prime += $row['total_spending'];
    				break;
    		}
    	}
    	
    	$sub_sql = 'select rm.minority_type_id, rm.minority_type_name , sum(total_spending_amount) total_spending
	    from aggregateon_subven_spending_coa_entities a1
	    join ref_minority_type rm on rm.minority_type_id = a1.minority_type_id
	   ' . $where_filter . '
	    group by rm.minority_type_id, rm.minority_type_name  ';
    	 
    	$sub_spending_rows = _checkbook_project_execute_sql($sub_sql);
    	foreach($sub_spending_rows as $row){
    		switch($row['minority_type_id']){
    			case '2':
    				$mwbe_spending_sub += $row['total_spending'];
    				break;
    			case '3':
    				$mwbe_spending_sub += $row['total_spending'];
    				break;
    			case '4':
    				$mwbe_spending_sub += $row['total_spending'];
    				break;
    			case '5':
    				$mwbe_spending_sub += $row['total_spending'];
    				break;
    			case '7':
    				$non_mwbe_spending_sub += $row['total_spending'];
    				break;
    			case '9':
    				$mwbe_spending_sub += $row['total_spending'];
    				break;
    		}
    	}
    	$mwbe_share = custom_number_formatter_format(($mwbe_spending_prime + $mwbe_spending_sub )/($non_mwbe_spending_prime + $mwbe_spending_prime) *100,1,null,'%') ;
    	$mwbe_spending = custom_number_formatter_format($mwbe_spending_prime + $mwbe_spending_sub,2,'$');
    	$non_mwbe = custom_number_formatter_format($non_mwbe_spending_prime,2,'$');
    	
    	return '<div class="chart-nyc-legend">
    			<div class="legend-title"><span>NYC Total M/WBE</span></div>
    			<div class="legend-item"><span>M/WBE Share: ' . $mwbe_share . ' </span></div>
    			<div class="legend-item"><span>M/WBE Spending: ' .$mwbe_spending . ' </span></div>
    			<div class="legend-item"><span>Non M/WBE: ' . $non_mwbe . '</span></div>    			
    			</div>
    			';
    	
    	
    }

    static function getTransactionPageTitle($widgetTitle){
        $catName = RequestUtil::getSpendingCategoryName();
        $title = RequestUtil::getDashboardTitle();
        return ($title . " " . $widgetTitle . " " . $catName . " Transactions");
    }

    
    function _show_mwbe_custom_legend(){
    	$mwbe_cats = _getRequestParamValue('mwbe');
    	if(	($mwbe_cats =='4~5' || $mwbe_cats =='2' || $mwbe_cats =='3' || $mwbe_cats =='9' ) && !(_getRequestParamValue('vendor') > 0 ) ){
    		return true;
    	}
    	
    	if(	!(_getRequestParamValue('vendor') > 0 ) && ( _getRequestParamValue('agency') > 0 || _getRequestParamValue('industry') > 0 ) ){
    		return true;
    	}
    	
    	return false;
    }
    
    
    function _mwbe_spending_use_subvendor(){
    	if(_getRequestParamValue('vendor') > 0 || _getRequestParamValue('mwbe') == '7' || _getRequestParamValue('mwbe') == '11')
    	{
    		return true;
    	}else{
    		return false;
    	}
    }
}





