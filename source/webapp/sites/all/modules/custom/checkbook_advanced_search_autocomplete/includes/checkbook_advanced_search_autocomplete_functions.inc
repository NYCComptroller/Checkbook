<?php

// Spending autocomplete menu callback functions
function _get_spending_advanced_search_autocomplete_results($facet, $query, $year = null, $agency = null, $expcategory = null, $dept = null, $exptype = null){
        if ($year) {
            $query .= _checkbook_autocomplete_get_start_end_dates($year, 'spending');
        }
        if ($dept) {
            $query .= '&fq=department_name:'._checkbook_autocomplete_escapeSolrValue(str_replace('__','/',$string));
        }
        if ($agency) {
            $query .= '&fq=agency_id:' . $agency;
        }
        if ($expcategory) {
            $query .= '&fq=expenditure_object_name:' . '"'._checkbook_autocomplete_escapeSolrValue(trim($expcategory)).'"';
        }
        if($exptype){
            $query .= '&fq=spending_category_id:'.$exptype;
        }
        $matches = _checkbook_autocomplete_solr_results($query, $facet);
        return $matches;
}

function _get_spending_expense_cat_options($agency, $dept, $spending_cat, $year) {
  
   if (strpos($year, '~')) {
        if ($year == 'fy~all') {
          $year = "";
        }
        $year_type = substr($year, 0, 2);
        $year_id = substr($year, 3, strlen($year));
        $year_value = _getYearValueFromID($year_id);
  }
  if ($year && $year_type == 'fy') {
    $yearstring = " AND fiscal_year = " . $year_value;
  }
  elseif ($year && $year_type == 'cy') {
    $yearstring = " AND calendar_fiscal_year = " . $year_value;
  }
  if ($agency) {
    $agencystring = " AND agency_id = " . $agency;
  }

  $scstring = ($spending_cat)?" spending_category_id = " . $spending_cat:" spending_category_id IN (1,2,3,4,5)";

  if ($dept){
    $deptstring = " AND department_name ILIKE '".trim($dept)."' ";
  }
  $query = "SELECT DISTINCT expenditure_object_name FROM disbursement_line_item_details
            WHERE ".$scstring.$yearstring.$agencystring.$deptstring."ORDER BY expenditure_object_name ASC";

  $results = _checkbook_project_execute_sql($query);
  if(count($results) == 0){
        $output = array('label' => 'No Matches Found','value' => '');
  }else{
      foreach ($results as $result) {
         $output[$result['expenditure_object_name']] = $result['expenditure_object_name'];
      }
  }
  return $output;
}

function _checkbook_advanced_search_autocomplete_spending_payee($year, $agency, $expcategory, $dept, $exptype = 0) {
    $searchTerm = _get_autocomplete_search_term();
    if (!empty($searchTerm)) {
        $query = 'select/?q=vendor_name_autocomplete:' . $searchTerm . '&facet=true&facet.field=vendor_name&fq=domain:spending&facet.limit=10&wt=php';
        $matches = _get_spending_advanced_search_autocomplete_results('vendor_name', $query, $year, $agency, $expcategory, $dept, $exptype);
        drupal_json_output($matches);
    }
}

function _checkbook_advanced_search_autocomplete_spending_contractno($year, $agency, $expcategory, $dept, $exptype = 0) {
    $searchTerm = _get_autocomplete_search_term();
    if (!empty($searchTerm)) {
        $doc_type1 = substr(trim(htmlspecialchars_decode($_REQUEST['term'], ENT_QUOTES)), 0, 3);
        $doc_type2 = substr(trim(htmlspecialchars_decode($_REQUEST['term'], ENT_QUOTES)), 0, 4);

        if(strtolower($doc_type1) == 'ma1' || strtolower($doc_type2) == 'mma1'){
            $matches = array(array('label'=>'No Matches Found','value'=>''));
        }else{
            $query = 'select/?q=contract_number:' . $searchTerm . '&facet=true&facet.field=contract_number&fq=domain:spending&facet.limit=10&wt=php';
            $matches = _get_spending_advanced_search_autocomplete_results('contract_number', $query, $year, $agency, $expcategory, $dept, $exptype);
        }
        drupal_json_output($matches);
    }
}

function _checkbook_advanced_search_autocomplete_spending_capitalproject($year, $agency, $expcategory, $dept, $exptype = 0) {
	$searchTerm = _get_autocomplete_search_term();
	if (!empty($searchTerm)) {
		$query = 'select/?q=reporting_code:' . $searchTerm . '&facet=true&facet.field=reporting_code&fq=domain:spending&facet.limit=10&wt=php';
		$matches = _get_spending_advanced_search_autocomplete_results('reporting_code', $query, $year, $agency, $expcategory, $dept, $exptype);
        drupal_json_output($matches);
	}
}

function _checkbook_advanced_search_autocomplete_spending_expenseid($year, $agency, $expcategory, $dept, $exptype = 0) {
	$searchTerm = _get_autocomplete_search_term();
	if (!empty($searchTerm)) {
		$query = 'select/?q=expense_id:' . $searchTerm . '&facet=true&facet.field=expense_id&fq=domain:spending&facet.limit=10&wt=php';
        $matches = _get_spending_advanced_search_autocomplete_results('expense_id', $query, $year, $agency, $expcategory, $dept, $exptype);
        drupal_json_output($matches);
	}
}

function _checkbook_advanced_search_autocomplete_spending_department($year, $agency, $exptype) {
    $query = 'select/?q=department_name:*&facet=true&facet.field=department_name&fq=domain:spending&wt=php';
    $options = _get_spending_advanced_search_autocomplete_results('department_name', $query, $year, $agency, null, null, $exptype);
    $matches = array();
    foreach($options as $key => $value)
        $matches[] = htmlentities($value);
    drupal_json_output($matches);
}

function _checkbook_advanced_search_autocomplete_spending_expcategory($year, $agency, $dept, $exptype) {
    $options = _get_spending_expense_cat_options($agency, str_replace('__','/',$dept), $exptype, $year);
    $matches = array();
    foreach($options as $key => $value)
        $matches[] = htmlentities(strtolower($value));

    drupal_json_output($matches);
}

//Payroll autocomplete menu callback functions

function _checkbook_advanced_search_autocomplete_payroll_employee_name($pay_frequency, $agency, $year){
    $searchTerm = _get_autocomplete_search_term();
    $query = 'select/?q=civil_service_title_autocomplete:'.$searchTerm.'&facet=true&facet.field=civil_service_title&fq=domain:payroll&facet.limit=10&wt=php';
    if(!empty($searchTerm)){
        if($pay_frequency){
            $query .= '&fq=pay_frequency:'.$pay_frequency;
        }
        if($agency){
            $query .= '&fq=agency_id:'.$agency;
        }
        if($year){
            $year_type = substr($year, 0, 2);
            $year_value = substr($year, 3,strlen($year));
            if($year_type == 'fy'){
                $query .= '&fq=fiscal_year_id:'.$year_value;
            }elseif($year_type == 'cy'){
                $query .= '&fq=calendar_fiscal_year_id:'.$year_value;
            }
        }
        $matches = _checkbook_autocomplete_solr_results($query, 'civil_service_title');
        drupal_json_output($matches);
    }
}

//Contracts autocomplete menu callback functions

function _get_contracts_advanced_search_autocomplete_results($facet, $query, $status = null, $category = null, $contract_type = null, $agency = null, $award_method = null, $year = null){

        if($category){
            $query .= '&fq=contract_category_name:'.$category;
        }
        if($agency){
            $query .= '&fq=agency_id:'.$agency;
        }
        if($award_method){
            $code_id_array = _checkbook_advanced_search_autocomplete_get_code_id($award_method);
            $query .= '&fq=award_method_id:'.$code_id_array['id'];
        }
        if(!in_array($contract_type, array('No Contract Type Selected', '0'))){
            $code_id_array = _checkbook_advanced_search_autocomplete_get_code_id($contract_type);
            if($status == 'P'){
                $query .= '&fq=contract_type_code:'.$code_id_array['code'];
            }else{
                $query .= '&fq=contract_type_id:'.$code_id_array['id'];
            }
        }
        if($status == 'P'){
            $query .= '&fq=contract_status:pending';
        }
        if($status == 'A'){
            if(isset($year))
                $query .= _checkbook_autocomplete_get_start_end_dates($year, 'contracts', 'A');
                $query .= '&fq=contract_status:registered';
        }
        if($status == 'R'){
            $query .= '&fq=contract_status:registered';
            if(isset($year)){
               $query .= _checkbook_autocomplete_get_start_end_dates($year, 'contracts', 'R');
            }
        }
        $matches = _checkbook_autocomplete_solr_results($query, $facet);
        return $matches;
}

function _checkbook_advanced_search_autocomplete_contracts_vendor_name($status, $category, $contract_type, $agency, $award_method, $year){
    $searchTerm = _get_autocomplete_search_term();
    if(!empty($searchTerm)){
        $query = 'select/?q=vendor_name_autocomplete:'.$searchTerm.'&facet=true&facet.field=vendor_name&fq=domain:contracts&facet.limit=10&wt=php';
        $matches = _get_contracts_advanced_search_autocomplete_results('vendor_name', $query, $status, $category, $contract_type, $agency, $award_method, $year);
        drupal_json_output($matches);
    }
}

function _checkbook_advanced_search_autocomplete_contracts_contract_number($status, $category, $contract_type, $agency, $award_method, $year){
    $searchTerm = _get_autocomplete_search_term();
    if(!empty($searchTerm)){
        $query = 'select/?q=contract_number:'.$searchTerm.'&facet=true&facet.field=contract_number&fq=domain:contracts&facet.limit=10&wt=php';
        $matches = _get_contracts_advanced_search_autocomplete_results('contract_number', $query, $status, $category, $contract_type, $agency, $award_method, $year);
        drupal_json_output($matches);
    }
}

function _checkbook_advanced_search_autocomplete_contracts_apt_pin($status, $category, $contract_type, $agency, $award_method, $year){
   $searchTerm = _get_autocomplete_search_term();
    if(strlen($searchTerm) > 0){
        $query = 'select/?q=apt_pin:'. $searchTerm .'&facet=true&facet.field=apt_pin&fq=domain:contracts&facet.limit=10&wt=php';
        $matches = _get_contracts_advanced_search_autocomplete_results('apt_pin', $query, $status, $category, $contract_type, $agency, $award_method, $year);
        drupal_json_output($matches);
    }
}

function _checkbook_advanced_search_autocomplete_contracts_pin($status, $category, $contract_type, $agency, $award_method, $year){
    $searchTerm = _get_autocomplete_search_term();
    if(strlen($searchTerm) > 0){
        $query = 'select/?q=pin:'. $searchTerm .'&facet=true&facet.field=pin&fq=domain:contracts&facet.limit=10&wt=php';
        $matches = _get_contracts_advanced_search_autocomplete_results('pin', $query, $status, $category, $contract_type, $agency, $award_method, $year);
        drupal_json_output($matches);
    }
}

//Budget autocomplete menu callback functions
function _checkbook_advanced_search_autocomplete_budget_department($fiscal_year, $agency) {
    $params =  array("agency_id"=>$agency,"budget_fiscal_year_id"=>$fiscal_year);
    $results = get_db_results(false, 'checkbook:budget', array("department_name.department_name"), $params,"department_name.department_name");
    if (count($results ) > 0) {
      $matches = array();
      foreach ($results as $key=>$value) {
          $matches[] = $value['department_name.department_name'];
      }
      drupal_json_output($matches);
    }
    else {
      drupal_json_output(array(array('label'=>'No Matches Found','value'=>'')));
    }
}

function _checkbook_advanced_search_autocomplete_budget_expcategory($fiscal_year, $agency, $dept) {
    $params =  array("agency_id"=>$agency,"budget_fiscal_year_id"=>$fiscal_year);
    if($dept != '0' ) {
      $params["department_name"] = str_replace('__','/',$dept);
    }
    $results = get_db_results(false, 'checkbook:budget', array("object_class_name.object_class_name"), $params,"object_class_name.object_class_name");
    if (count($results ) > 0) {
      $matches = array();
      foreach ($results as $key=>$value) {
          $matches[] = $value['object_class_name.object_class_name'];
      }
      drupal_json_output($matches);
    }
    else {
      drupal_json_output(array(array('label'=>'No Matches Found','value'=>'')));
    }
}

function _checkbook_advanced_search_autocomplete_budget_budgetcode($fiscal_year, $agency, $dept, $expcategory) {
   $searchTerm = _get_autocomplete_search_term();
    if (!empty($searchTerm)) {
        $query = 'select?q=budget_code_name_code_autocomplete:' . $searchTerm . '&facet=true&facet.field=budget_code_name_code&fq=domain:budget&facet.limit=10&wt=php';
        if ($fiscal_year) {
            $query .= '&fq=fiscal_year_id:' . $fiscal_year;
        }
        if ($agency) {
            $query .= '&fq=agency_id:' . $agency;
        }
        if ($dept) {
            $query .= '&fq=department_name:' . _checkbook_autocomplete_escapeSolrValue($dept);
        }
        if ($expcategory) {
            $query .= '&fq=expenditure_object_name:' . _checkbook_autocomplete_escapeSolrValue($expcategory);
        }
        $matches = _checkbook_autocomplete_solr_results($query, 'budget_code_name_code');
        drupal_json_output($matches);
    }
}

//Revenue autocomplete menu callback functions
function _checkbook_advanced_search_autocomplete_revenue_revenueclass($year, $fundclass, $agency, $budgetyear, $revcat, $revsrc, $fundingsrc) {
   $searchTerm = _get_autocomplete_search_term();
    if (!empty($searchTerm)) {
        $query = 'select/?q=revenue_class_name_autocomplete:' . $searchTerm . '&facet=true&facet.field=revenue_class_name&fq=domain:revenue&facet.limit=10&wt=php';
        if($year) {
            $query .= '&fq=display_fiscal_year_id:' . $year;
        }
        if($fundclass) {
            $query .= '&fq=fund_class_id:' . $fundclass;
        }
        if($agency) {
          $query .= '&fq=agency_id:' . $agency;
        }
        if($budgetyear) {
            $query .= '&fq=fiscal_year_id:' . $budgetyear;
        }
        if($revcat) {
            $query .= '&fq=revenue_category_id:' . $revcat;
        }
        if ($fundingsrc) {
            $query .= '&fq=funding_class_code:' . $fundingsrc;
        }
        $matches = _checkbook_autocomplete_solr_results($query, 'revenue_class_name');
        drupal_json_output($matches);
    }
}

function _checkbook_advanced_search_autocomplete_revenue_revenuesource($year, $fundclass, $agency, $budgetyear, $revcat, $revclass, $fundingsrc) {
    $searchTerm = _get_autocomplete_search_term();
    if (!empty($searchTerm)) {
        $query = 'select/?q=revenue_source_name_autocomplete:' . $searchTerm . '&facet=true&facet.field=revenue_source_name&fq=domain:revenue&facet.limit=10&wt=php';
        if ($year) {
            $query .= '&fq=display_fiscal_year_id:' . $year;
        }
        if ($fundclass) {
            $query .= '&fq=fund_class_id:' . $fundclass;
        }
        if ($agency) {
            $query .= '&fq=agency_id:' . $agency;
        }
        if ($budgetyear) {
            $query .= '&fq=fiscal_year_id:' . $budgetyear;
        }
        if ($revcat) {
            $query .= '&fq=revenue_category_id:' . $revcat;
        }
        if ($fundingsrc) {
          $query .= '&fq=funding_class_code:' . $fundingsrc;
        }
        $matches = _checkbook_autocomplete_solr_results($query, 'revenue_source_name');
        drupal_json_output($matches);
    }
}

/**
 * Used for Contracts Advanced Search, for some elements like "Contract Type" which is a drop down on Advanced Search, we need contract_type_code
 * if status is "Pending" and contract_type_id if status is "Active" / "Registered". So $code_id_string is always in the format "id=>13~code=>41".
 * From this we have to separate out id and code
 *
 * @param $code_id_string
 * @return array('code' => 41, 'id' => 13)
 *
 */

function _checkbook_advanced_search_autocomplete_get_code_id($code_id_string){
    $code_id_array = explode('~',$code_id_string);
    $id = substr($code_id_array[0],4);
    $code = substr($code_id_array[1],6);
    return array('code' => $code, 'id' => $id);
}
