<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


class ContractURLHelper{

    static $landingPageParams = array("status"=>"status","vendor"=>"vendor","agency"=>"agency","awdmethod"=>"awdmethod","cindustry"=>"cindustry","csize"=>"csize");
    static $transactionPageParams = array("status"=>"status","vendor"=>"cvendor","agency"=>"cagency","awdmethod"=>"awdmethod","cindustry"=>"cindustry","csize"=>"csize");

    static function prepareExpenseContractLink($row, $node) {

        $link = NULL;
        $docType = $row['document_code@checkbook:ref_document_code'];
        $agrParamName = in_array($docType, array('MMA1','MA1')) ? 'magid' : 'agid';

        if( RequestUtil::isExpandBottomContainer() ){
          $link = '<a href=/panel_html/contract_transactions/contract_details/' .$agrParamName . '/' . $row['original_agreement_id'] .  '/doctype/' . $docType .  ' class=bottomContainerReload>'. $row['contract_number'] . '</a>';
        } else {
          $link = '<a href=/contracts_landing'
              . _checkbook_project_get_url_param_string('contstatus','status')
              .  (
                    isset($row['type_of_year@checkbook:contracts_coa_aggregates']) ?
                            ( $row['type_of_year@checkbook:contracts_coa_aggregates'] == 'B' ? ('/yeartype/B/year/'. $row['fiscal_year_id@checkbook:contracts_coa_aggregates']) : ('/yeartype/C/calyear/'.$row['fiscal_year_id@checkbook:contracts_coa_aggregates']) )
                            : (_checkbook_project_get_year_url_param_string())
                 )
              . '?expandBottomContURL=/panel_html/contract_transactions/contract_details/' .$agrParamName . '/' . $row['original_agreement_id'] .  '/doctype/' . $docType
              .  ' >'. $row['contract_number'] . '</a>';
        }

        return $link;
    }

    static function prepareRevenueContractLink($row, $node) {

        $link = NULL;
        $docType = $row['document_code@checkbook:ref_document_code'];
        $agrParamName = 'magid';//in_array($docType, array('MMA1','MA1')) ? 'magid' : 'agid';

        if( RequestUtil::isExpandBottomContainer() ){
          $link = '<a href=/panel_html/contract_transactions/' .$agrParamName . '/' . $row['original_agreement_id'] .  '/doctype/' . $docType .  ' class=bottomContainerReload>'. $row['contract_number'] . '</a>';
        } else {
          $link = '<a href=/contracts_revenue_landing'
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
            $link = '<a href=/panel_html/contract_transactions/contract_details/agid/' . $row['agreement_id'] .  '/doctype/' . $docType .  ' class=bottomContainerReload>'. $row['reference_document_number'] . '</a>';
        }else if( RequestUtil::isNewWindow() ){
            $link = '<span href=/contracts_landing/status/A'
                . _checkbook_project_get_year_url_param_string()
                . '?expandBottomContURL=/panel_html/contract_transactions/contract_details/agid/' . $row['agreement_id'] .  '/doctype/' . $docType
                .  ' class=loadParentWindow>'. $row['reference_document_number'] . '</span>';
        }else {
            $link = "<a class='new_window' href='/contract_details" . _checkbook_project_get_contract_url($row[reference_document_number], $row[agreement_id])  ."/newwindow'>"  . $row[reference_document_number] . "</a>";
        }

        return $link;
    }

    static function prepareSpendingContractTransactionsLink($row, $node) {

        $link = NULL;
        $docType = $row['document_code@checkbook:ref_document_code'];

        if( RequestUtil::isExpandBottomContainer() ){
            $link = '<a href=/panel_html/contract_transactions/contract_details/agid/' . $row['disb_agreement_id'] .  '/doctype/' . $docType .  ' class=bottomContainerReload>'. $row['disb_contract_number'] . '</a>';
        }else if( RequestUtil::isNewWindow() ){
            $link = '<span href=/contracts_landing/status/A'
                . _checkbook_project_get_year_url_param_string()
                . '?expandBottomContURL=/panel_html/contract_transactions/contract_details/agid/' . $row['disb_agreement_id'] .  '/doctype/' . $docType
                .  ' class=loadParentWindow>'. $row['disb_contract_number'] . '</span>';
        }else {
            $link = '<a href=/contracts_landing/status/A'
                . _checkbook_project_get_year_url_param_string()
                . '?expandBottomContURL=/panel_html/contract_transactions/contract_details/agid/' . $row['disb_agreement_id'] .  '/doctype/' . $docType
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
        
        if( preg_match("/^contracts_pending/", drupal_get_path_alias($_GET['q'])) ){
          $allowedFilters =  array("agency","vendor","awrdmthd","csize","cindustry","agid");
          $url .= "/yeartype/B/year/"._getCurrentYearID();
        }
        else{
          $allowedFilters =  array("year","calyear","agency","yeartype","awdmethod","vendor","csize","cindustry","agid");
        }
        for($i=1;$i < count($pathParams);$i++){

            if(in_array($pathParams[$i] ,$allowedFilters) ){
                 $url .= '/'.$pathParams[$i].'/'.$pathParams[($i+1)];
            }
            $i++;
        }
        return $url;

    }

    public function preparePendingContractsSliderFilter($page){

        $pathParams = explode('/',drupal_get_path_alias($_GET['q']));
        $url = $page;
        if( preg_match("/^contracts_pending/", drupal_get_path_alias($_GET['q'])) ){
          $allowedFilters =  array("year","calyear","agency","yeartype","awrdmthd","vendor","csize","cindustry");
               
        }
        else{
          $allowedFilters =  array("year","calyear","agency","yeartype","awdmethod","vendor","csize","cindustry");
        }  
        for($i=1;$i < count($pathParams);$i++){
        
          if(in_array($pathParams[$i] ,$allowedFilters) ){
            $url .= '/'.$pathParams[$i].'/'.$pathParams[($i+1)];
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
                return "/yeartype/B/year/" . $pathParams[($yearIndex+1)] . "/syear/" . $pathParams[($yearIndex+1)];
            }else{
                $year = ($calyearIndex)?$pathParams[($calyearIndex+1)]:$pathParams[($yearIndex+1)];
                return "/yeartype/C/calyear/" . $year . "/scalyear/" . $year;
            }

        }
    }
}
