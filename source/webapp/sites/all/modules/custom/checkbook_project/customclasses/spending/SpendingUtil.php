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

    /**
     * @var array
     */
    static $landingPageParams = array("category"=>"category","industry"=>"industry","mwbe"=>"mwbe","dashboard"=>"dashboard","agency"=>"agency","vendor"=>"vendor","subvendor"=>"subvendor");

    /**
     * @param $categoryId
     * @param array $columns
     * @return array|null
     */
    static function getSpendingCategoryDetails($categoryId, $columns=array('spending_category_id','display_name')){
        if(!isset($categoryId)){
            return NULL;
        }

        $categoryDetails = _checkbook_project_querydataset('checkbook:category',$columns, array('spending_category_id'=>$categoryId));
        return $categoryDetails;
    }

    /**
     * @return string
     */
    static  public function getSpendingTransactionsTitle(){
        $title = '';
        if(preg_match('/category\/1/',current_path())){
          $title = $title . ' Contract' ;
        }
        elseif(preg_match('/category\/2/',current_path())){
          $title = $title. ' Payroll' ;
        }
        elseif(preg_match('/category\/3/',current_path())){
          $title = $title . ' Capital' ;
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
      $title = $title . " Spending Transactions";
      return $title ;
    }

    /**
     * @return array
     */
    public static function getDepartmentIds(){
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

    /**
     * @return array
     */
    public static function getExpenseCatIds(){
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
        $override_params = array(
            "dtsmnid"=>$node->nid,
            "fvendor"=>self::getVendorFacetParameter($node)
        );
        return '/' . self::getSpendingTransactionPageUrl($override_params);
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
        . RequestUtilities::buildUrlFromParam('agency')
        . RequestUtilities::buildUrlFromParam('vendor')
        . RequestUtilities::buildUrlFromParam('category')
        . RequestUtilities::buildUrlFromParam('industry')
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
        $override_params = array(
            'agency'=>$row["agency_agency"],
            "fvendor"=>self::getVendorFacetParameter($node),
            "smnid"=>$node->nid
        );
        return '/' . self::getSpendingTransactionPageUrl($override_params);
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
        $year_id = RequestUtilities::get("year");
        $year_type = RequestUtilities::get("yeartype");
        $agency_id = RequestUtilities::get("agency");
        $dashboard = RequestUtilities::get("dashboard");

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
        $year_id = RequestUtilities::get("year");
        $year_type = RequestUtilities::get("yeartype");
        $agency_id = RequestUtilities::get("agency");
        $dashboard = RequestUtilities::get("dashboard");
        $datasource = RequestUtilities::get("datasource");

        return self::getPrimeVendorLink($vendor_id, $agency_id, $year_id, $year_type, $dashboard, '', $datasource);
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

        $year = RequestUtilities::get("year");
        $calyear = RequestUtilities::get("calyear");
        $year_type = isset($calyear) ? "C" : "B";
        $year_id = isset($calyear) ? $calyear : (isset($year) ? $year : _getCurrentYearID());
        $vendor_id = $row["vendor_id"];
        $agency_id = $row["agency"];
        $dashboard = RequestUtilities::get("dashboard");

        return $row["is_sub_vendor"] == "No"
            ? self::getPrimeVendorLink($vendor_id, $agency_id, $year_id, $year_type, $dashboard, true)
            : self::getSubVendorLink($vendor_id, $agency_id, $year_id, $year_type, $dashboard, true);

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
     * @param bool $payee_name
     * @param $datasource
     * @return string
     */
    static function getPrimeVendorLink($vendor_id, $agency_id, $year_id, $year_type, $current_dashboard, $payee_name = false, $datasource = null){

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
                "agency"=>$agency_id,
                "datasource"=>$datasource,
                "vendor"=>$vendor_id
            );
            //payee name will never have a drill down, this is to avoid ajax issues on drill down
            if($payee_name) {
                $override_params["mwbe"] = $is_mwbe_certified ? "2~3~4~5~9" : null;
            }
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
     * @param $payee_name
     * @return string
     */
    static function getSubVendorLink($vendor_id, $agency_id, $year_id, $year_type, $current_dashboard, $payee_name = false){

        $override_params = null;
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
            //payee name will never have a drill down, this is to avoid ajax issues on drill down
            if($payee_name) {
                $override_params["mwbe"] = $is_mwbe_certified ? "2~3~4~5~9" : null;
            }
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
     public static function getLatestMwbeCategoryByVendor($vendor_id, $agency_id = null, $year_id = null, $year_type = null, $is_prime_or_sub = "P"){
        STATIC $spending_vendor_latest_mwbe_category;

        if($agency_id == null){
        	$agency_id =  RequestUtilities::get('agency');
        }

        if($year_id == null){
        	$year_id =  RequestUtilities::get('year');
        }

        if($year_id == null){
            $year_id =  RequestUtilities::get('calyear');
        }

        if($year_type == null){
        	$year_type =  RequestUtilities::get('yeartype');
        }

        $latest_minority_type_id = null;
        if(!isset($spending_vendor_latest_mwbe_category)){
            $query = "SELECT vendor_id, agency_id, year_id, type_of_year, minority_type_id, is_prime_or_sub
                      FROM spending_vendor_latest_mwbe_category
                      WHERE minority_type_id IN (2,3,4,5,9) AND year_id = '" . $year_id . "' AND type_of_year = '" . $year_type . "'
                      GROUP BY vendor_id, agency_id, year_id, type_of_year, minority_type_id, is_prime_or_sub";

            $results = _checkbook_project_execute_sql_by_data_source($query, 'checkbook');
            foreach ($results as $row) {
                if (isset($row['agency_id'])) {
                    $spending_vendor_latest_mwbe_category[$row['vendor_id']][$row['agency_id']][$row['is_prime_or_sub']]['minority_type_id'] = $row['minority_type_id'];
                } else {
                    $spending_vendor_latest_mwbe_category[$row['vendor_id']][$row['is_prime_or_sub']]['minority_type_id'] = $row['minority_type_id'];
                }
            }
        }

        $latest_minority_type_id = isset($agency_id)
            ? $spending_vendor_latest_mwbe_category[$vendor_id][$agency_id][$is_prime_or_sub]['minority_type_id']
            : $spending_vendor_latest_mwbe_category[$vendor_id][$is_prime_or_sub]['minority_type_id'];
        return $latest_minority_type_id;
    }

    /**
     * Returns latest M/WBE category Name for the given vendor id in the given year and year type
     *
     * @param $vendor_id
     * @param $year_id
     * @param $year_type
     * @param string $is_prime_or_sub
     * @return null
     */
     public static function getLatestMwbeCategoryTitleByVendor($vendor_id, $year_id = NULL, $year_type = NULL, $is_prime_or_sub = "P"){
        if($year_id == null){
            $year_id =  RequestUtilities::get('year');
        }

        if($year_type == null){
            $year_type =  RequestUtilities::get('yeartype');
        }

         $query = "SELECT minority_type_id FROM(
            SELECT a.*, row_number() OVER (PARTITION BY a.vendor_id, a.year_id, a.type_of_year ORDER BY chk_date DESC) AS flag FROM(
                SELECT a.vendor_id, 
                    a.year_id, 
                    a.type_of_year,
                    CASE WHEN a.minority_type_id IS NULL OR a.minority_type_id = 11 THEN 7 ELSE a.minority_type_id END minority_type_id,
                    MAX(d.check_eft_issued_date ) AS chk_date
                FROM aggregateon_mwbe_spending_coa_entities a
                JOIN disbursement_line_item_details d ON a.vendor_id = d.vendor_id AND a.agency_id = d.agency_id 
                        AND a.minority_type_id = d.minority_type_id AND a.year_id = d.check_eft_issued_nyc_year_id
                WHERE a.vendor_id = ".$vendor_id." AND a.year_id = ".$year_id." AND a.type_of_year = '".$year_type."' 
                GROUP BY CASE WHEN a.minority_type_id IS NULL OR a.minority_type_id = 11 THEN 7 ELSE a.minority_type_id END, 
                a.vendor_id, a.year_id, a.type_of_year ) a ) a WHERE flag = 2 AND a.minority_type_id IN (2,3,4,5,9)
            UNION 
            SELECT DISTINCT minority_type_id 
            FROM spending_vendor_latest_mwbe_category 
            WHERE vendor_id = ".$vendor_id." AND is_prime_or_sub = '".$is_prime_or_sub."' AND type_of_year = '".$year_type."' 
                  AND year_id = ".$year_id." AND minority_type_id <> 7 ";
        $results = _checkbook_project_execute_sql_by_data_source($query,'checkbook');
        if($results[0]['minority_type_id'] != ''){
            return $results[0]['minority_type_id'];
        }
        else{
            return false;
        }
    }

    /**
     * @param $vendor_id
     * @param null $year_id
     * @param null $year_type
     * @return bool
     */
    public static function getLatestMwbeCategoryByVendorByTransactionYear($vendor_id, $year_id = null, $year_type = null){

        if($year_id == null){
            $year_id =  RequestUtilities::get('year');
        }

        if($year_type == null){
            $year_type =  RequestUtilities::get('yeartype');
        }

        $query = "SELECT vendor_id, agency_id, year_id, type_of_year, minority_type_id, is_prime_or_sub
                      FROM contract_vendor_latest_mwbe_category
                      WHERE minority_type_id IN (2,3,4,5,9)";
        $query .= isset($vendor_id) ? " AND vendor_id = ".$vendor_id : "";
        $query .= " AND year_id =".$year_id."
                    AND type_of_year ='".$year_type."'
                    GROUP BY vendor_id, agency_id, year_id, type_of_year, minority_type_id, is_prime_or_sub LIMIT 1";

        $results = _checkbook_project_execute_sql_by_data_source($query,'checkbook');

        if($results[0]['minority_type_id'] != ''){
            return $results[0]['minority_type_id'];
        }
        else{
            return false;
        }
    }


    /**
     * @param $vendor_id
     * @param null $year_id
     * @param null $year_type
     * @return bool
     */
    public static function getLatestMwbeCategoryBySpendingVendorByTransactionYear($vendor_id, $year_id = null, $year_type = null){

        if($year_id == null){
            $year_id =  RequestUtilities::get('year');
        }

        if($year_type == null){
            $year_type =  RequestUtilities::get('yeartype');
        }

        $query = "SELECT vendor_id, agency_id, year_id, type_of_year, minority_type_id, is_prime_or_sub
                      FROM spending_vendor_latest_mwbe_category
                      WHERE minority_type_id IN (2,3,4,5,9)";
        $query .= isset($vendor_id) ? " AND vendor_id = ".$vendor_id : "";
        $query .= " AND year_id =".$year_id."
                    AND type_of_year ='".$year_type."'
                    GROUP BY vendor_id, agency_id, year_id, type_of_year, minority_type_id, is_prime_or_sub LIMIT 1";

        $results = _checkbook_project_execute_sql_by_data_source($query,'checkbook');

        if($results[0]['minority_type_id'] != ''){
            return $results[0]['minority_type_id'];
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
        $nid = $node->nid;
        $vendor = isset($row["vendor_vendor"]) ? $row["vendor_vendor"] : $row["prime_vendor_prime_vendor"];
        $override_params = array(
            'vendor'=>$vendor,
            "fvendor"=>self::getVendorFacetParameter($node),
            'smnid'=>$node->nid
        );
        return '/' . self::getSpendingTransactionPageUrl($override_params);
    }

    /**
     * Returns Prime Vendor Amount Link Url based on values from current path & data row
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getPrimeVendorAmountLinkUrl($node, $row){
        $override_params = array(
            'vendor'=>$row["prime_vendor_prime_vendor"],
            "fvendor"=>self::getVendorFacetParameter($node),
            "smnid"=>$node->nid
        );
        return '/' . self::getSpendingTransactionPageUrl($override_params);
    }

//    /**
//     * Given the prime vendor, gets a ~ separated list of sub vendors
//     * @param $prime_vendor_id
//     * @return array|null|string
//     */
//    static function getSubVendorIdsByPrime($prime_vendor_id){
//
//        $year_id = RequestUtilities::getRequestParamValue('year');
//        $type_of_year = RequestUtilities::getRequestParamValue('yeartype');
//
//
//        $parameters = array('prime_vendor_id'=>$prime_vendor_id,'type_of_year'=>$type_of_year,'year_id'=>$year_id);
//        $results = _checkbook_project_querydataset( "checkbook:contracts_subven_vendor_spending",array('vendor_id'),$parameters);
//
//        foreach($results as $sub_vendor) {
//            $sub_vendors[] = $sub_vendor['vendor_id'];
//        }
//        $sub_vendors = array_unique($sub_vendors);
//        $sub_vendors = is_array($sub_vendors) ? implode("~", $sub_vendors) : null;
//        return $sub_vendors;
//
//    }

    /**
     * Returns Department Amount Link Url based on values from current path & data row
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getDepartmentAmountLinkUrl($node, $row){
        $override_params = array(
            'agency'=>$row["agency_agency"],
            'dept'=>$row["department_department"],
            "fvendor"=>self::getVendorFacetParameter($node),
            "smnid"=>$node->nid
        );
        return '/' . self::getSpendingTransactionPageUrl($override_params);
    }

    /**
     * Returns Check Amount Sum Link Url based on values from current path & data row
     *
     * Transaction page from M/WBE Dashboard landing page
     * @param $node
     * @param $row
     * @return string
     */
    static function getCheckAmountSumLinkUrl($node, $row){
        $override_params = array(
            'expcategory'=>$row["expenditure_object_expenditure_object"],
            "fvendor"=>self::getVendorFacetParameter($node),
            "smnid"=>$node->nid
        );
        return '/' . self::getSpendingTransactionPageUrl($override_params);
    }

    /**
     * Returns Contract Amount Link Url based on values from current path & data row
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getContractAmountLinkUrl($node, $row){
        $contract_url_part = _checkbook_project_get_contract_url($row["document_id_document_id"], $row["agreement_id_agreement_id"]);
        $override_params = array(
            "fvendor"=>self::getVendorFacetParameter($node),
            "smnid"=>$node->nid
        );
        return '/' . self::getSpendingTransactionPageUrl($override_params) . $contract_url_part;
    }

    /**
     * Returns Sub Contract Amount Link Url based on values from current path & data row
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getSubContractAmountLinkUrl($node, $row){
        $agreement_id = $row["agreement_id_agreement_id"];
        $document_id = isset($row["document_id_document_id"])
            ? $row["document_id_document_id"]
            : $row["reference_document_code"];
        $contract_url_part = _checkbook_project_get_contract_url($document_id, $agreement_id);
        $override_params = array(
            "fvendor"=>self::getVendorFacetParameter($node),
            "smnid"=>$node->nid
        );
        return '/' . self::getSpendingTransactionPageUrl($override_params) . $contract_url_part;
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
        $override_params = array(
            'industry'=>isset($row['industry_industry_industry_type_id']) ? $row['industry_industry_industry_type_id'] : $row['industry_type_industry_type'],
            "fvendor"=>self::getVendorFacetParameter($node),
            "smnid"=>$node->nid
        );
        return '/' . self::getSpendingTransactionPageUrl($override_params);
    }

    /**
     * Returns Sub Vendor YTD Spending Link Url based on values from current path & data row
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getSubVendorYtdSpendingUrl($node, $row){
        $override_params = array(
            'subvendor'=>$row['sub_vendor_sub_vendor'],
            'fvendor'=>$row['sub_vendor_sub_vendor'],
            'smnid'=>$node->nid
        );
        return '/' . self::getSpendingTransactionPageUrl($override_params);
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
        . RequestUtilities::buildUrlFromParam('vendor')
        . RequestUtilities::buildUrlFromParam('category')
        . RequestUtilities::buildUrlFromParam('industry')
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
     *
     * NYCCHKBK-4676:
     *   Do not hyperlink the M/WBE category within Top 5 Sub vendors widget if you are looking at prime data[M/WBE Featured Dashboard].
     *   Do not hyperlink the M/WBE category within Top 5 Prime vendors widget if you are looking at sub data[M/WBE(sub vendors) featured dashboard].
     *   The Details link from these widgets, also should follow same rule of not hyperlinking the M/WBE category.
     * NYCCHKBK-4798:
     *   From Top 5 Sub vendors widget, link should go to SP to maintain correct data
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function getMWBECategoryLinkUrl($node, $row){
        $dtsmnid = RequestUtilities::get("dtsmnid");
        $smnid = RequestUtilities::get("smnid");
        $dashboard = RequestUtilities::get("dashboard");

        if($dtsmnid != null) $nid = $dtsmnid;
        else if($smnid != null) $nid = $smnid;
        else $nid = $node->nid;

        if($dashboard == null){
        	$dashboard = ($row['is_sub_vendor'] == "Yes") ? "ms" : "mp";
        }
        $dashboard = (preg_match('/p/', $dashboard)) ? "mp" : "ms";
        $mwbe = isset($row["minority_type_id"]) ? $row["minority_type_id"] : $row["minority_type_minority_type"];
        //From sub vendors widget
        if($nid == 719) $dashboard = "sp";
        $custom_params = array(
            'dashboard'=> $dashboard,
            'mwbe' => $mwbe == 4 || $mwbe == 5 ? '4~5' : $mwbe
        );
        return '/' . self::getLandingPageWidgetUrl($custom_params) . '?expandBottomCont=true';
    }

    /**
     * Returns true/false if M/WBE Category should be a link
     *
     * @param $node
     * @param $row
     * @return string
     */
    static function showMWBECategoryLink($node,$row){
        $dtsmnid = RequestUtilities::get("dtsmnid");
        $smnid = RequestUtilities::get("smnid");

        $showLink = !RequestUtil::isNewWindow() &&
            MappingUtil::isMWBECertified(array($row['minority_type_id'])) &&
            $dtsmnid != 763 && $smnid != 763 && $dtsmnid != 747 && $smnid != 747 && $dtsmnid != 717 && $smnid != 717;
        return $showLink;
    }

    /**
     * Returns M/WBE Category Link Url for the advanced search page
     * @param $node
     * @param $row
     * @return string
     */
    static function getAdvancedSearchMWBECategoryLinkUrl($node, $row){
        $mwbe = isset($row["minority_type_id"]) ? $row["minority_type_id"] : $row["minority_type_minority_type"];
        $custom_params = array(
            'dashboard'=>$row["is_sub_vendor"] == "No" ? "mp" : "ms",
            'mwbe' => $mwbe == 4 || $mwbe == 5 ? '4~5' : $mwbe
        );
        return '/' . self::getLandingPageWidgetUrl($custom_params) . '?expandBottomCont=true';
    }

    /**
     * Returns M/WBE Category Link Url for the prime vendor
     * @param $node
     * @param $row
     * @return string
     */
    static function getPrimeMWBECategoryLinkUrl($node, $row){
        $dashboard = RequestUtilities::get("dashboard");
        $mwbe = isset($row["prime_minority_type_id"]) ? $row["prime_minority_type_id"] : $row["prime_minority_type_prime_minority_type"];
        $custom_params = array(
            'dashboard'=>(preg_match('/p/', $dashboard)) ? "mp" : "ms",
            'mwbe' => $mwbe == 4 || $mwbe == 5 ? '4~5' : $mwbe
        );
        return '/' . self::getLandingPageWidgetUrl($custom_params) . '?expandBottomCont=true';
    }

    /**
     * Returns the vendor or sub vendor id for the vendor facets
     * @param $node
     * @return null|request
     */
    static function getVendorFacetParameter($node){
        $dashboard = RequestUtilities::get('dashboard');
        $facet_vendor_param = null;

        if($dashboard == "mp") {
            $facet_vendor_param = RequestUtilities::get("vendor");
        }
        else if($dashboard == "ss" || $dashboard == "ms") {
            $facet_vendor_param = RequestUtilities::get("subvendor");
        }
        return $facet_vendor_param;
    }

    /**
     *  Returns a spending landing page Url with custom parameters appended but instead of persisted
     *
     * @param array $override_params
     * @return string
     */
   public static function getLandingPageWidgetUrl($override_params = array()) {
        $url = self::getSpendingUrl('spending_landing',$override_params);
        return str_replace("calyear","year",$url);
   }

    /**
     *  Returns a spending transaction page Url with custom parameters appended but instead of persisted
     *
     * @param array $override_params
     * @return string
     */
  public static function getSpendingTransactionPageUrl($override_params = array()) {
        return self::getSpendingUrl('panel_html/spending_transactions/spending/transactions',$override_params);
  }

    /**
     *  Returns a spending contract details page Url with custom parameters appended but instead of persisted
     *
     * @param array $override_params
     * @return string
     */
   public static function getSpendingContractDetailsPageUrl($override_params = array()) {
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
    public static function getSpendingUrl($path, $override_params = array()) {

        $url =  $path . _checkbook_project_get_year_url_param_string();

        $q = drupal_get_path_alias($_GET['q']);
        if (_checkbook_current_request_is_ajax()) {
          // remove query part
          $q = strtok($_SERVER['HTTP_REFERER'], '?');
        }

        $pathParams = explode('/', $q);

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

//        if (_checkbook_current_request_is_ajax()) {
//          LogHelper::log_warn('getSpendingUrl :: isAjax :: ' . $url);
//        }

        return $url;
    }

    /**
     * Transaction page from M/WBE Dashboard landing page:
     *
     * Top 10 agencies widget (759) - sub and prime data
     * Top 10 Sub Vendors widget (763) - sub data
     * All Others widgets - prime data
     *
     * @param $node
     * @return string
     */
    public static function getVendorTypeUrlParam($node){
        $dashboard = RequestUtilities::get('dashboard');
        $vendortype = null;
        $nid = $node->nid;
        /**
         * Transaction page from M/WBE Dashboard landing page
         * Top 10 agencies widget (759) - sub and prime data
         * Top 10 Sub Vendors widget (763) - sub data
         * All Others widgets - prime data
         */
        if($dashboard == 'mp') {
            switch($nid) {
                case 759:
                    $vendortype .= 'sv~pv~mv';
                    break;
                case 763:
                    $vendortype .= 'sv~mv';
                    break;
                default:
                    $vendortype .= 'pv~mv';
                    break;
            }
        }
        return $vendortype;
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
   public static function getSubVendorsPercentPaid($row){
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
   public static function calculatePercent($numerator, $denominator){
        $results = $numerator/$denominator*100;
        $results = $results < 0 ? 0.00 : $results;
        return custom_number_formatter_format($results,2,'','%');
   }

    /**
     * Checks to see if this is from the Advanced search page,
     * if so, need to append the data source but not the m/wbe parameter.
     */
    public static function getDataSourceParams(){
        if(self::isAdvancedSearchResults()) {
            $data_source = RequestUtilities::get("datasource");
            return isset($data_source) ? "/datasource/checkbook_oge" : "";
        }
        return _checkbook_append_url_params();
    }

    /**
     * Returns true if this is from spending advanced search for citywide
     OR if this is from the transaction page for M/WBE.
     */
    public static function showMwbeFields() {
        $is_mwbe = _checkbook_check_is_mwbe_page();
        $is_mwbe = $is_mwbe || (!_checkbook_check_isEDCPage() && self::isAdvancedSearchResults());
        return $is_mwbe;
    }

    /**
     * Returns true if this is from spending advanced search
     */
    public static function isAdvancedSearchResults() {
        return !self::isSpendingLanding();
    }

    /**
     * Returns true if this is the spending landing page
     */
    public static function isSpendingLanding() {
        $url_ref = $_SERVER['HTTP_REFERER'];
        $match_landing = '"/spending_landing/"';
        return preg_match($match_landing,$url_ref);
    }

    /**
     * Spending transaction page should be shown for citywide, oge
     * @return bool
     */
    public static function showSpendingTransactionPage(){
        $subvendor_exist = _checkbook_check_is_sub_vendor_page();
        $ma1_mma1_contracts_exist = _checkbook_project_ma1_mma1_exist();
        $edc_records_exist = _checkbook_check_isEDCPage() && _checkbook_project_recordsExists(6);
        $mwbe_records_exist = _checkbook_check_is_mwbe_page() && !$subvendor_exist && _checkbook_project_recordsExists(706);
        $citywide_exist =  !$subvendor_exist && !$mwbe_records_exist && !$edc_records_exist && _checkbook_project_recordsExists(6);

        if($ma1_mma1_contracts_exist || $subvendor_exist) {return false;}

        return ($edc_records_exist || $mwbe_records_exist || $citywide_exist);
    }

    /**
     * Spending transaction no results page should be shown for citywide, oge
     * @return bool
     */
    public static function showNoSpendingTransactionPage(){
        $subvendor_exist = _checkbook_check_is_sub_vendor_page();
        $ma1_mma1_contracts_exist = _checkbook_project_ma1_mma1_exist();
        $edc_records_exist = _checkbook_check_isEDCPage() && _checkbook_project_recordsExists(6);
        $mwbe_records_exist = _checkbook_check_is_mwbe_page() && !$subvendor_exist && _checkbook_project_recordsExists(706);
        $citywide_exist =  !$subvendor_exist && !$mwbe_records_exist && !$edc_records_exist && _checkbook_project_recordsExists(6);

        if($ma1_mma1_contracts_exist || $subvendor_exist){
            return false;
        }

        return $subvendor_exist || $ma1_mma1_contracts_exist || $edc_records_exist;
    }


    /**
     * @param $year
     * @param $yeartype
     * @param string $non_mwbe_spending_prime
     * @param string $mwbe_spending_prime
     * @return string
     */
    public static function getMWBENYCLegend($year, $yeartype, $non_mwbe_spending_prime = '', $mwbe_spending_prime = '')
    {

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
                case '3':
                case '4':
                case '5':
                case '9':
                    $mwbe_spending_prime += $row['total_spending'];
    				break;


    			case '7':
    				$non_mwbe_spending_prime += $row['total_spending'];
    				break;

    		}
    	}
    	$mwbe_share = custom_number_formatter_format(($mwbe_spending_prime )/($non_mwbe_spending_prime + $mwbe_spending_prime) *100,1,null,'%');
    	$mwbe_spending = custom_number_formatter_format($mwbe_spending_prime,2,'$');
    	$non_mwbe = custom_number_formatter_format($non_mwbe_spending_prime,2,'$');

    	return '<div class="chart-nyc-legend">
    			<div class="legend-title"><span>NYC Total M/WBE</span></div>
    			<div class="legend-item"><span>M/WBE Share: ' . $mwbe_share . ' </span></div>
    			<div class="legend-item"><span>M/WBE Spending: ' .$mwbe_spending . ' </span></div>
    			<div class="legend-item"><span>Non M/WBE: ' . $non_mwbe . '</span></div>    			
    			</div>
    			';

    }

    /**
     * Returns the legend displayed in the Sub Vendors (M/WBE) dashboard for the "Sub Spending by M/WBE Share" visualization
     * @param $year
     * @param $yeartype
     * @return string
     */
    public static function getSubMWBENYCLegend($year, $yeartype){

        $where_filter =  "where year_id = $year and type_of_year = '$yeartype' ";

        $sql = 'select rm.minority_type_id, rm.minority_type_name , sum(total_spending_amount) total_spending
	    from aggregateon_subven_spending_coa_entities a1
	    join ref_minority_type rm on rm.minority_type_id = a1.minority_type_id
	   ' . $where_filter . '
	    group by rm.minority_type_id, rm.minority_type_name  ';

        $spending_rows = _checkbook_project_execute_sql($sql);
        foreach($spending_rows as $row){
            switch($row['minority_type_id']){
                case '2':
                case '3':
                case '4':
                case '5':
                case '9':

                    $mwbe_spending_sub += $row['total_spending'];
                    break;

                case '7':
                    $non_mwbe_spending_sub += $row['total_spending'];
                    break;

            }
        }
        $mwbe_share = custom_number_formatter_format(($mwbe_spending_sub )/($non_mwbe_spending_sub + $mwbe_spending_sub) *100,1,null,'%') ;
        $mwbe_spending = custom_number_formatter_format($mwbe_spending_sub,2,'$');
        $non_mwbe = custom_number_formatter_format($non_mwbe_spending_sub,2,'$');

        return '<div class="chart-nyc-legend">
    			<div class="legend-title"><span>NYC Total M/WBE</span></div>
    			<div class="legend-item"><span>M/WBE Share: ' . $mwbe_share . ' </span></div>
    			<div class="legend-item"><span>M/WBE Spending: ' .$mwbe_spending . ' </span></div>
    			<div class="legend-item"><span>Non M/WBE: ' . $non_mwbe . '</span></div>
    			</div>
    			';


    }

    /**
     * @param string $widgetTitle
     * @return string
     */
    public static function getTransactionPageTitle($widgetTitle=''){
        $catName = self::getTransactionPageCategoryName();
        $dashboard_title = RequestUtil::getDashboardTitle();
        $dashboard = RequestUtilities::get('dashboard');
        $category = RequestUtilities::get('category');
        $smnid = RequestUtilities::get('smnid');

        //Sub Vendors Exception
        if(($widgetTitle == "Sub Vendors" || $widgetTitle == "Sub Vendor") && $dashboard == "ss") {
            $dashboard_title = MappingUtil::getCurrenEthnicityName();
        }
        //Contract Exception
        elseif(($widgetTitle == "Contracts" || $widgetTitle == "Contract") && $category == 1) {
            $catName = "Spending";
        }
        //Visualization - Sub Vendors (M/WBE) "Ethnicity" Exception
        elseif($smnid == "723" && $dashboard == "sp") {
            $dashboard_title = MappingUtil::getCurrenEthnicityName();
            return $widgetTitle . " " . $dashboard_title . " " . $catName . " Transactions";
        }
        //Visualization - Sub Vendors (M/WBE) "Ethnicity" Exception
        elseif($smnid == "723" && $dashboard == "ss") {
            $dashboard_title = MappingUtil::getCurrenEthnicityName();
        }


        return $dashboard_title . " " . $widgetTitle . " " . $catName . " Transactions";
    }

    /**
     * Returns Spending Category based on 'category' value from current path
     *
     * @param string $defaultName
     * @return string
     */
    public static function getTransactionPageCategoryName($defaultName = 'Total Spending'){
        $categoryId = RequestUtilities::get('category');
        $dtsmnid = RequestUtilities::get('dtsmnid');
        $smnid = RequestUtilities::get('smnid');

        $nid = isset($dtsmnid) ? $dtsmnid : $smnid;
        $category_name = $defaultName;

        if(isset($categoryId)){
            $categoryDetails = SpendingUtil::getSpendingCategoryDetails($categoryId,'display_name');
            $category_name = is_array($categoryDetails) ? $categoryDetails[0]['display_name'] : $defaultName;
        }

        return $category_name;
    }

    /**
     * @param $widgetTitle
     * @return string
     */
    public static function getSpentToDateTitle($widgetTitle){
        $dashboard = RequestUtil::getDashboardTitle();
        $contractTitle = self::getContractTitle();

        $dashboard_param = RequestUtilities::get('dashboard');
        $smnid = RequestUtilities::get('smnid');
        $status = RequestUtilities::get('contstatus');
        if($smnid == 720) {
            if ($dashboard_param == "ms") {
            $dashboard = "M/WBE";
            }
            elseif($dashboard_param == "ss") {
                $dashboard = "";
            }
        }
        //Visualization - M/WBE (Sub Vendors) Exception
        elseif($smnid == "subven_mwbe_contracts_visual_2" && $dashboard_param == "ms" || $smnid == "mwbe_contracts_visual_2" || $smnid == "subvendor_contracts_visual_1" && $dashboard_param == "ss" || $smnid == "subvendor_contracts_visual_1" && $dashboard_param == "sp") {
            $dashboard = MappingUtil::getCurrenEthnicityName();
        }
        //Visualization - "Ethnicity" Spending by Active Expense Contracts Transactions Exception
        $bottomNavigation = '';
        if($status == 'A') {
            $bottomNavigation = "Total Active Sub Vendor Contracts";
        }
        else {
            $bottomNavigation = "New Sub Vendor Contracts by Fiscal Year";
        }

        if($smnid == 721 || $smnid == 720 || $smnid == 781 || $smnid == 784){
            $widgetTitle = 'Spending';
        }
        if($smnid =='subvendor_contracts_visual_1' && $dashboard_param == 'sp'){
            return (MappingUtil::getCurrenEthnicityName() . " Sub Vendors Spending by <br />" .$bottomNavigation);
        }
        if($smnid =='subven_mwbe_contracts_visual_2' && $dashboard_param == 'ms'){

            return (MappingUtil::getCurrenEthnicityName() . " Sub Spending by <br />" .$bottomNavigation);
        }

        if($dashboard_param == 'ss' || $dashboard_param == 'ms' || $dashboard_param == 'sp') {
            //Title for Contract Spending Transactions page (Total Spent to Date link under 'Sub Vendor Information' section)
            if($smnid == 721 && preg_match("/^contract\/spending\/transactions\/contnum/",$_GET['q'])) {
                return "Total Sub Vendors Spending Transactions";
            }
            if($dashboard_param == 'ms' || $dashboard_param == 'sp'){
                if($status == 'A') {
                    $bottomNavigation = "Total Active M/WBE Sub Vendor Contracts";
                }
                else {
                    $bottomNavigation = "New M/WBE Sub Vendor Contracts by Fiscal Year";
                }
            }
           return ($widgetTitle . " by  " . $bottomNavigation . " " . "Transactions");
        }

        return ($dashboard . " " . $widgetTitle . " " . $contractTitle . " Contracts Transactions");
    }

    /**
     * @return string
     */
    public static function getContractTitle(){
        if(RequestUtilities::get('contstatus')){
            $contract_status = RequestUtilities::get('contstatus');
        }
        if(RequestUtilities::get('status')){
            $contract_status = RequestUtilities::get('status');
        }
        $contract_type = RequestUtilities::get('contcat');
        $title = 'by';

        switch($contract_status) {
            case 'A':
                $title .= ' Active';
                break;
            case 'R':
                $title .= ' Registered';
                break;
            default:
                $title .= ' Pending';
                break;
        }
        switch($contract_type) {
            case 'expense':
                $title .= ' Expense';
                break;
            case 'revenue':
                $title .= ' Revenue';
                break;
        }
        return $title;
    }

    /**
     * @return bool
     */
    public static function _show_mwbe_custom_legend(){
    	$mwbe_cats = RequestUtilities::get('mwbe');
    	if(	($mwbe_cats =='4~5' || $mwbe_cats =='4' || $mwbe_cats =='5' || $mwbe_cats =='2' || $mwbe_cats =='3' || $mwbe_cats =='9' ) && !(RequestUtilities::get('vendor') > 0 ) ){
    		return true;
    	}

    	if(	!(RequestUtilities::get('vendor') > 0 ) && ( RequestUtilities::get('agency') > 0 || RequestUtilities::get('industry') > 0 ) ){
    		return true;
    	}
    	return false;
    }


    /**
     * @return bool
     */
    function _mwbe_spending_use_subvendor(){
    	if(RequestUtilities::get('vendor') > 0 || RequestUtilities::get('mwbe') == '7' || RequestUtilities::get('mwbe') == '11')
    	{
    		return true;
    	}else{
    		return false;
    	}
    }
}





