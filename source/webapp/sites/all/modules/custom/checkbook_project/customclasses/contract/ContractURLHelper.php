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


class ContractURLHelper{

    static $landingPageParams = array("status"=>"status","vendor"=>"vendor","agency"=>"agency","awdmethod"=>"awdmethod","cindustry"=>"cindustry","csize"=>"csize");
    static $transactionPageParams = array("status"=>"status","vendor"=>"cvendor","agency"=>"cagency","awdmethod"=>"awdmethod","cindustry"=>"cindustry","csize"=>"csize");

    static function prepareExpenseContractLink($row, $node, $parent = false, $original_agreement_id = null) {

        $link = NULL;
        if(isset($row['contract_original_agreement_id'])) $row['original_agreement_id'] = $row['contract_original_agreement_id'];
        $row['original_agreement_id'] = ($original_agreement_id)? $original_agreement_id : $row['original_agreement_id'];

        if($parent && strlen($row['master_contract_number']) > 0){
            $agrParamName = 'magid';
            $docTypeStr = substr($row['master_contract_number'],0,3);
            $docType = ($docTypeStr == 'MA1') ? 'MA1' : 'MMA1';
            $row['original_agreement_id'] = $row['master_agreement_id'];
            $row['contract_number'] = $row['master_contract_number'];
        }else if($parent && strlen($row['master_contract_number']) == 0){
            return "N/A";
        }else{
            $docType = isset($row['document_code@checkbook:ref_document_code']) ? $row['document_code@checkbook:ref_document_code'] : _get_contract_type($row['contract_number']);
            $agrParamName = in_array($docType, array('MMA1','MA1')) ? 'magid' : 'agid';
        }

        if( RequestUtil::isExpandBottomContainer() ){
          $link = '<a href=/panel_html/contract_transactions/contract_details/' .$agrParamName . '/' . $row['original_agreement_id'] .  '/doctype/' . $docType .  _checkbook_append_url_params() . ' class=bottomContainerReload>'. $row['contract_number'] . '</a>';
        } else {
          $link = '<a href=/contracts_landing'
              . _checkbook_project_get_url_param_string('contstatus','status')
              . _checkbook_append_url_params()
              .  (
                    isset($row['type_of_year@checkbook:contracts_coa_aggregates']) ?
                            ( $row['type_of_year@checkbook:contracts_coa_aggregates'] == 'B' ? ('/yeartype/B/year/'. $row['fiscal_year_id@checkbook:contracts_coa_aggregates']) : ('/yeartype/C/calyear/'.$row['fiscal_year_id@checkbook:contracts_coa_aggregates']) )
                            : (_checkbook_project_get_year_url_param_string())
                 )
              . ((_checkbook_check_isEDCPage()) ? '/agency/' . $row['agency_id'] :'')
              . '?expandBottomContURL=/panel_html/contract_transactions/contract_details/' .$agrParamName . '/' . $row['original_agreement_id'] .  '/doctype/' . $docType . _checkbook_append_url_params()
              .  ' >'. $row['contract_number'] . '</a>';
        }


        return $link;
    }

    static function prepareRevenueContractLink($row, $node) {

        $link = NULL;
        $docType = $row['document_code@checkbook:ref_document_code'];
        $agrParamName = 'magid';//in_array($docType, array('MMA1','MA1')) ? 'magid' : 'agid';
        $agid = isset($row['original_agreement_id']) ? $row['original_agreement_id'] : $row['contract_original_agreement_id'];

        if( RequestUtil::isExpandBottomContainer() ){
          $link = '<a href=/panel_html/contract_transactions/' .$agrParamName . '/' . $agid .  '/doctype/' . $docType .  ' class=bottomContainerReload>'. $row['contract_number'] . '</a>';
        } else {
          $link = '<a href=/contracts_revenue_landing'
              . _checkbook_project_get_url_param_string('contstatus','status')
              .  (
                  isset($row['type_of_year@checkbook:contracts_coa_aggregates']) ?
                      ( $row['type_of_year@checkbook:contracts_coa_aggregates'] == 'B' ? ('/yeartype/B/year/'. $row['fiscal_year_id@checkbook:contracts_coa_aggregates']) : ('/yeartype/C/calyear/'.$row['fiscal_year_id@checkbook:contracts_coa_aggregates']) )
                      : (_checkbook_project_get_year_url_param_string())
                  )
              . '?expandBottomContURL=/panel_html/contract_transactions/' .$agrParamName . '/' . $agid .  '/doctype/' . $docType
              .  ' >'. $row['contract_number'] . '</a>';
        }

        return $link;
    }

    static function preparePendingContractLink($row, $node) {

        $agreementId = $row['original_agreement_id'];
        if(!isset($agreementId)){//No link if mag is not present
          return '<a class="bottomContainerReload" href = "/minipanels/pending_contract_transactions/contract/'.$row['fms_contract_number'].'/version/'.$row['document_version'].'">'.$row['contract_number'].'</a>';
        }

        $link = NULL;
        $docType = $row['document_code@checkbook:ref_document_code'];
        $agrParamName = in_array($docType, array('MMA1','MA1','RCT1')) ? 'magid' : 'agid';

        if( RequestUtil::isExpandBottomContainer() ){
          $link = '<a href=/panel_html/contract_transactions/' .$agrParamName . '/' . $row['original_agreement_id'] .  '/doctype/' . $docType .  ' class=bottomContainerReload>'. $row['contract_number'] . '</a>';
        } else {
          $link = '<a href=/' . ($docType == 'RCT1' ? 'contracts_pending_rev_landing': 'contracts_pending_exp_landing') .'/'
              . _checkbook_project_get_url_param_string('contstatus','status')
              .  (
                  isset($row['type_of_year@checkbook:contracts_coa_aggregates']) ?
                      ( $row['type_of_year@checkbook:contracts_coa_aggregates'] == 'B' ? ('/yeartype/B/year/'. $row['fiscal_year_id@checkbook:contracts_coa_aggregates']) : ('/yeartype/C/calyear/'.$row['fiscal_year_id@checkbook:contracts_coa_aggregates']) )
                      : (_checkbook_project_get_year_url_param_string())
                  )
              . '?expandBottomContURL=/panel_html/contract_transactions/' .$agrParamName . '/' . $row['original_agreement_id'] .  '/doctype/' . $docType
              .  ' >'. $row['contract_number'] . '</a>';
        }

        return $link;
    }

    static function prepareSpendingContractLink($row, $node) {
        if($row['spending_category_name'] == 'Payroll' ||  $row['spending_category_name'] == 'Others') {
          return 'N/A';
        }

        if(empty($row[agreement_id])){
            return $row[reference_document_number];
        }

        $link = NULL;
        $docType = $row['reference_document_code'];
    
        if( RequestUtil::isExpandBottomContainer() ){
            $link = '<a href=/panel_html/contract_transactions/contract_details/agid/' . $row['agreement_id'] .  '/doctype/' . $docType .  _checkbook_append_url_params() . ' class=bottomContainerReload>'. $row['reference_document_number'] . '</a>';
        }else if( RequestUtil::isNewWindow() ){
            $link = '<span href=/contracts_landing/status/A'
                . _checkbook_project_get_year_url_param_string()
                . _checkbook_append_url_params()
                . '?expandBottomContURL=/panel_html/contract_transactions/contract_details/agid/' . $row['agreement_id'] .  '/doctype/' . $docType . _checkbook_append_url_params()
                .  ' class=loadParentWindow>'. $row['reference_document_number'] . '</span>';
        }else {
            $link = "<a class='new_window' href='/contract_details" . _checkbook_append_url_params() . _checkbook_project_get_contract_url($row[reference_document_number], $row[agreement_id])  ."/newwindow'>"  . $row[reference_document_number] . "</a>";
        }

        return $link;
    }

    static function prepareSpendingContractTransactionsLink($row, $node) {

        $link = NULL;
        $docType = $row['document_code@checkbook:ref_document_code'];

        if( RequestUtil::isExpandBottomContainer() ){
            $link = '<a href=/panel_html/contract_transactions/contract_details/agid/' . $row['disb_agreement_id'] .  '/doctype/' . $docType . _checkbook_append_url_params() . ' class=bottomContainerReload>'. $row['disb_contract_number'] . '</a>';
        }else if( RequestUtil::isNewWindow() ){
            $link = '<span href=/contracts_landing/status/A'
                . _checkbook_project_get_year_url_param_string()
                . '?expandBottomContURL=/panel_html/contract_transactions/contract_details/agid/' . $row['disb_agreement_id'] .  '/doctype/' . $docType . _checkbook_append_url_params()
                .  ' class=loadParentWindow>'. $row['disb_contract_number'] . '</span>';
        }else {
            $link = '<a href=/contracts_landing/status/A'
                . _checkbook_project_get_year_url_param_string()
                . '?expandBottomContURL=/panel_html/contract_transactions/contract_details/agid/' . $row['disb_agreement_id'] .  '/doctype/' . $docType . _checkbook_append_url_params()
                .  ' >'. $row['disb_contract_number'] . '</a>';
        }

        return $link;
    }
    
    public function prepareActRegContractsSliderFilter($page, $status){
        
        $pathParams = explode('/',drupal_get_path_alias($_GET['q']));
        $url = $page;
        if(strlen($status) > 0){
            $url .= "/status/".$status;
        }
        $url .= _checkbook_append_url_params();
        if( preg_match("/^contracts_pending/", drupal_get_path_alias($_GET['q'])) ){
          $allowedFilters =  array("agency","vendor","awrdmthd","csize","cindustry","agid","dashboard","subvendor","mwbe");
          $url .= "/yeartype/B/year/"._getCurrentYearID();
        }
        else{
          $allowedFilters =  array("year","calyear","agency","yeartype","awdmethod","vendor","csize","cindustry","agid","dashboard","subvendor","mwbe");
        }
        for($i=1;$i < count($pathParams);$i++){

            if(in_array($pathParams[$i] ,$allowedFilters)){
                $newPathParams = explode('/', $url);
                $url .= (!in_array($pathParams[$i] ,$newPathParams)) ? '/'.$pathParams[$i].'/'.$pathParams[($i+1)] : '';
            }
            $i++;
        }
        return $url;

    }

    public function prepareSubvendorContractsSliderFilter($page, $dashboard=NULL){

        $pathParams = explode('/',drupal_get_path_alias($_GET['q']));
        $url = $page;
        $url .= _checkbook_append_url_params();
        if( preg_match("/^contracts_pending/", drupal_get_path_alias($_GET['q'])) ){
            $allowedFilters =  array("agency","vendor","awrdmthd","csize","cindustry","agid","dashboard","subvendor","mwbe");
            $url .= "/yeartype/B/year/"._getCurrentYearID();
        }
        else{
            $allowedFilters =  array("year","calyear","agency","yeartype","awdmethod","vendor","csize","cindustry","agid","subvendor","mwbe","status");
            //Add new parameter for bottom slider
            $dashboard = isset($dashboard)? $dashboard : _getRequestParamValue("dashboard");
            
            //Remove dashboard parameter before appending the new value
            $url = preg_replace("/dashboard\/../","",$url);
            $url .= "/bottom_slider/sub_vendor/status/A"        
                 .(isset($dashboard)? '/dashboard/'.$dashboard :"");
        }
        for($i=1;$i < count($pathParams);$i++){

            if(in_array($pathParams[$i] ,$allowedFilters)){
                $newPathParams = explode('/', $url);
                $url .= (!in_array($pathParams[$i] ,$newPathParams)) ? '/'.$pathParams[$i].'/'.$pathParams[($i+1)] : '';
            }
            $i++;
        }
        
        //Persist the last parameter in the current page URL as the last param only to fix the title issues
        $lastReqParam = _getLastRequestParamValue();
        if($lastReqParam != _getLastRequestParamValue($url)){
            foreach($lastReqParam as $key=>$value){
                $url = preg_replace("/\/".$key."\/".$value."/","",$url);
                $url .= "/".$key."/".$value;
            }
        }
        
        return $url;

    }

    public function preparePendingContractsSliderFilter($page){

        $pathParams = explode('/',drupal_get_path_alias($_GET['q']));
        $url = $page;
        if( preg_match("/^contracts_pending/", drupal_get_path_alias($_GET['q'])) ){
          $allowedFilters =  array("year","calyear","agency","yeartype","awrdmthd","vendor","csize","cindustry","mwbe","dashboard");
               
        }
        else{
          $allowedFilters =  array("year","calyear","agency","yeartype","awdmethod","vendor","csize","cindustry","mwbe","dashboard");
        }  
        for($i=1;$i < count($pathParams);$i++){
        
          if(in_array($pathParams[$i] ,$allowedFilters) ){
              $newPathParams = explode('/', $url);
              $url .= (!in_array($pathParams[$i] ,$newPathParams)) ? '/'.$pathParams[$i].'/'.$pathParams[($i+1)] : '';
          }
          $i++;
        }     
        return $url;

    }


    /**
     * returns the year type and year values string to be appended to the URL for spending trans link.
     * @return string
     */
    function _checkbook_project_spending_get_year_url_param_string(){
        $urlPath = drupal_get_path_alias($_GET['q']);
        $pathParams = explode('/', $urlPath);

        $yeartypeIndex = array_search("yeartype",$pathParams);
        $yearIndex = array_search("year",$pathParams);
        $calyearIndex = array_search("calyear",$pathParams);

        if($yeartypeIndex){
            $yeartypeValue = $pathParams[($yeartypeIndex+1)];
            if($yeartypeValue == 'B'){
                return _checkbook_append_url_params()."/yeartype/B/year/" . $pathParams[($yearIndex+1)] . "/syear/" . $pathParams[($yearIndex+1)];
            }else{
                $year = ($calyearIndex)?$pathParams[($calyearIndex+1)]:$pathParams[($yearIndex+1)];
                return _checkbook_append_url_params()."/yeartype/C/calyear/" . $year . "/scalyear/" . $year;
            }

        }
    }

    function _prepare_oge_contracts_spending_url($row, $node){
       $agencies = _checkbook_project_querydataset('checkbook_oge:agency',array('agency_id','agency_name'),array('agency_id'=>$row['agency_id'], 'is_oge_agency'=>'Y'));
       $oge_agency_name = $agencies[0]['agency_name'];

       $vendors = _checkbook_project_querydataset('checkbook_oge:vendor',array('vendor_id','legal_name'),array('vendor_id'=>$row['vendor_id']));
       $oge_vendor_name = $vendors[0]['legal_name'];

       $vendor_url = '';
       if(strtolower($oge_agency_name) != strtolower($oge_vendor_name)){
           $vendor_url = '/svendor/' . $row['vendor_id'];
       }

       $year_url = '';
       if((!(_getRequestParamValue('year') || _getRequestParamValue('calyear')))){
           $year_url = '/yeartype/B/year/' . _getFiscalYearID() . '/syear/'. _getFiscalYearID();
       }else{
           $year_url = $row['type_of_year'] == 'B' ? ('/year/'. $row['fiscal_year_id'].'/syear/'. $row['fiscal_year_id']) : ('/calyear/'.$row['fiscal_year_id']. '/scalyear/'.$row['fiscal_year_id']);
       }

       $url = "<a href='/spending/transactions"
             .  ($row['master_agreement_yn'] == 'Y' ? '/magid/' : '/agid/') . $row['original_agreement_id']
             .  ($row['master_agreement_yn'] == 'Y' ? $vendor_url : '/svendor/' . $row['vendor_id'])
             .  ($row['master_agreement_yn'] == 'Y' ? '' : ('/scomline/'.$row['fms_commodity_line']))
             .  $year_url
//             . _checkbook_project_get_url_param_string('agency')
             . _checkbook_project_get_url_param_string('vendor')
             . _checkbook_append_url_params()
             .  "/newwindow' class='new_window'>" . custom_number_formatter_basic_format($row['spending_amount_disb']) . '</a>';
        return $url;
    }

    function _prepare_oge_spent_to_date_url($row, $node){
        $oge_agency_name = $row['agency_name_checkbook_oge_agency'];
        $oge_vendor_name = $row['legal_name_checkbook_oge_vendor'];

        $vendor_url = $year_url = '';
        if(strtolower($oge_agency_name) != strtolower($oge_vendor_name)){
            $vendor_url = '/svendor/' . $row['vendor_id'];
        }
        if((!(_getRequestParamValue('year') || _getRequestParamValue('calyear')))){
            $year_url = '/yeartype/B/year/' . _getFiscalYearID() . '/syear/'. _getFiscalYearID();
        }
        else{
            $year_url = $row['type_of_year'] == 'B' ? ('/year/'. $row['fiscal_year_id'].'/syear/'. $row['fiscal_year_id']) : ('/calyear/'.$row['fiscal_year_id']. '/scalyear/'.$row['fiscal_year_id']);
        }

        $url = "<a href='/spending/transactions"
            .  ($row['master_agreement_yn'] == 'Y' ? '/magid/' : '/agid/') . $row['original_agreement_id']
            .  ($row['master_agreement_yn'] == 'Y' ? $vendor_url : '/svendor/' . $row['vendor_id'])
            .  ($row['master_agreement_yn'] == 'Y' ? '' : ('/scomline/'.$row['fms_commodity_line']))
            .  $year_url
            . _checkbook_project_get_url_param_string('vendor')
            . _checkbook_append_url_params()
            .  "/newwindow' class='new_window'>" . custom_number_formatter_basic_format($row['spending_amount_disb']) . '</a>';

        return $url;
    }

    static function prepareExpandLink($row, $node ) {
    	$flag = ( preg_match("/^mwbe/", $_GET['q']) ) ? "has_mwbe_children" :"has_children";
		$show_expander = ($row[$flag] == 'Y') ? true : false;

        $year = $row['fiscal_year_id@checkbook:all_contracts_coa_aggregates'];
        $year_type = $row['type_of_year@checkbook:all_contracts_coa_aggregates'];

        $year = !$year ? _getCurrentYearID() : $year;
        $year_type = !$year_type ? 'B' : $year_type;

        $link = ($show_expander) ? '<span id=dtl_expand class="toggler collapsed"  magid="' . ((isset($row['contract_original_agreement_id']))?$row['contract_original_agreement_id'] : $row['original_agreement_id']) . '" '
            . ( _getRequestParamValue('dashboard') != '' ?  ('dashboard="' . _getRequestParamValue('dashboard') . '" ' ) : '')
            . ( _getRequestParamValue('mwbe') != '' ?  ('mwbe="' . _getRequestParamValue('mwbe') . '" ' ) : '')
            . ( _getRequestParamValue('smnid') != '' ?  ('smnid="' . _getRequestParamValue('smnid') . '" ' ) : '')
            . ( _getRequestParamValue('contstatus') != '' ?  ('contstatus="' . _getRequestParamValue('contstatus') . '" ' ) : '')
            . 'year="' . $year . '" '
            . 'yeartype="' . $year_type . '" '
            . ('mastercode="' . $row['document_code@checkbook:ref_document_code'] . '"' )
            . '></span>' : '';

        return $link;
    }

    /*Start Expense Contracts Transaction Page*/

    static function expenseContractsExpandLink($row, $node ) {
        $flag = ( preg_match("/^mwbe/", $_GET['q']) ) ? "has_mwbe_children" :"has_children";
        $show_expander = ($row[$flag] == 'Y') ? true : false;

        $link = ($show_expander) ? '<span id=dtl_expand class="toggler collapsed"  magid="' . ((isset($row['contract_original_agreement_id']))?$row['contract_original_agreement_id'] : $row['original_agreement_id']) . '" '
            . ( _getRequestParamValue('dashboard') != '' ?  ('dashboard="' . _getRequestParamValue('dashboard') . '" ' ) : '')
            . ( _getRequestParamValue('mwbe') != '' ?  ('mwbe="' . _getRequestParamValue('mwbe') . '" ' ) : '')
            . ( _getRequestParamValue('smnid') != '' ?  ('smnid="' . _getRequestParamValue('smnid') . '" ' ) : '')
            . ( _getRequestParamValue('contstatus') != '' ?  ('contstatus="' . _getRequestParamValue('contstatus') . '" ' ) : '')
            . _checkbook_project_get_year_url_param_string()
            . ('mastercode="' . $row['document_code'] . '"' )
            . '></span>' : '';

        return $link;
    }

    static function expenseContractsLink($row, $node, $parent = false, $original_agreement_id = null) {

        $link = NULL;
        if(isset($row['contract_original_agreement_id'])) $row['original_agreement_id'] = $row['contract_original_agreement_id'];
        $row['original_agreement_id'] = ($original_agreement_id)? $original_agreement_id : $row['original_agreement_id'];

        if($parent && strlen($row['master_contract_number']) > 0){
            $agrParamName = 'magid';
            $docTypeStr = substr($row['master_contract_number'],0,3);
            $docType = ($docTypeStr == 'MA1') ? 'MA1' : 'MMA1';
            $row['original_agreement_id'] = $row['master_agreement_id'];
            $row['contract_number'] = $row['master_contract_number'];
        }else if($parent && strlen($row['master_contract_number']) == 0){
            return "N/A";
        }else{
            $docType = $row['document_code'];
            $agrParamName = in_array($docType, array('MMA1','MA1')) ? 'magid' : 'agid';
        }

        if( RequestUtil::isExpandBottomContainer() ){
            $link = '<a href=/panel_html/contract_transactions/contract_details/' .$agrParamName . '/' . $row['original_agreement_id'] .  '/doctype/' . $docType .  _checkbook_append_url_params() . ' class=bottomContainerReload>'. $row['contract_number'] . '</a>';
        } else {
            $link = '<a href=/contracts_landing'
                . _checkbook_project_get_url_param_string('contstatus','status')
                . _checkbook_append_url_params()
                . _checkbook_project_get_year_url_param_string()
                . ((_checkbook_check_isEDCPage()) ? '/agency/' . $row['agency_id'] :'')
                . '?expandBottomContURL=/panel_html/contract_transactions/contract_details/' .$agrParamName . '/' . $row['original_agreement_id'] .  '/doctype/' . $docType . _checkbook_append_url_params()
                .  ' >'. $row['contract_number'] . '</a>';
        }


        return $link;
    }
}
