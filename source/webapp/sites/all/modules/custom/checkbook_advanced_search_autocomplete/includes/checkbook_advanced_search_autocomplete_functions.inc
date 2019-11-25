<?php

// Spending autocomplete menu callback functions
/**
 * Construct query string and get results from Solr for spending domain.
 * @param string $facet
 *   Facet name for which auto suggestions needed
 * @param string $query
 *   Partially constructed query
 * @param string/null $year
 *   Year value
 * @param string/null $agency
 *   Selected agency id
 * @param string/null $expcategory
 *   Selected Expense Category Name
 * @param string/null $dept
 *   Selected Department Name
 * @param string/null $exptype
 *   Selected Spending Category Id
 * @param $mwbe_category
 *   Selected M/WBE Category
 * @param $industry
 *   Selected Industry
 * @param string $solr_datasource
 *   Data source to get data (i.e checkbook, checkbook_oge)
 *
 * @return array
 *   Results from Solr
 ***/
function _get_spending_advanced_search_autocomplete_results($facet, $query, $year = null, $agency = null, $expcategory = null, $dept = null, $exptype = null, $mwbe_category = null, $industry = null, $solr_datasource = 'checkbook'){
    if ($year) {
        $query .= _checkbook_autocomplete_get_start_end_dates($year, 'spending');
    }
    if ($dept) {
        $query .= '&fq=department_name:"'.CheckbookSolrQuery::escape(trim(str_replace('__','/',$dept))).'"';
    }
    if ($agency) {
        $query .= '&fq=agency_id:' . $agency;
    }
    if ($expcategory) {
        $query .= '&fq=expenditure_object_code:' . '"'.CheckbookSolrQuery::escape(trim($expcategory)).'"';
    }
    if($exptype){
        $query .= '&fq=spending_category_id:'.$exptype;
    }
    if($mwbe_category){
        $query .= '&fq=minority_type_id:'._checkbook_advanced_search_autocomplete_get_mwbe_category($mwbe_category);
    }
    if($industry){
        $query .= '&fq=industry_type_id:'.$industry;
    }
    $matches = _checkbook_autocomplete_solr_results($solr_datasource, $query, $facet);
    return $matches;
}

/**
 * Returns the options for Expense Category Dropdown based on the selected agency/department/year/spending category/mwbe category values.
 *
 * @param string $agency
 *   Selected agency id
 * @param string $dept
 *   Selected Department Name
 * @param string $spending_cat
 *   Selected Spending Category Id
 * @param string $year
 *   Year value
 * @param $data_source
 *   Data source to get data (i.e checkbook, checkbook_oge)
 *
 * @return array
 *   Results from Query
 ***/
function _get_spending_expense_cat_options($agency, $dept, $spendingCat, $year, $dataSource = 'checkbook') {
  $yearId = (isset($year)) ? substr($year, 3, strlen($year)) : "all";
  $categoryParam = $spendingCat ? "AND spending_category_id = '" . $spendingCat . "' " : "";
  $deptParam = $dept ? " AND department_name ILIKE '".trim($dept)."' ":"";

  if($dataSource == Datasource::NYCHA){
    $yearParam = ($yearId != 'all') ? " AND issue_date_year_id = '" . $yearId . "' " : "";
    $query = "SELECT DISTINCT expenditure_type_description AS expenditure_object_name, 
                              expenditure_type_code AS expenditure_object_code
              FROM all_disbursement_transactions
              WHERE 1=1 {$yearParam} {$categoryParam} {$deptParam} 
              ORDER BY expenditure_object_name ASC";
  }else{
    $yearParam = ($yearId != 'all') ? " AND check_eft_issued_nyc_year_id = '" . $yearId . "' " : "";
    $agencyParam = $agency ? " AND agency_id = '" . $agency . "' " : "";
    $query = "SELECT DISTINCT expenditure_object_code, expenditure_object_name 
              FROM disbursement_line_item_details
              WHERE 1=1 {$categoryParam} {$yearParam} {$agencyParam} {$deptParam} 
              ORDER BY expenditure_object_name ASC";
  }

  $results = _checkbook_project_execute_sql($query, "main",  $dataSource);
  if(count($results) == 0){
    $output = array('label' => 'No Matches Found','value' => '');
  }else{
    foreach ($results as $result) {
        $output[$result['expenditure_object_code']] = $result['expenditure_object_name'];
    }
  }
  return $output;
}

/**
 * Returns the options for department drop down based on the selected agency/year/spending category values.
 *
 * @param string $year
 *   Year value
 * @param string $agency
 *   Selected agency id
 * @param string $exp_type
 *   Selected Spending Category Id
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 *
 * @return mixed Results from Solr
 *   Results from Solr
 *
 */
function _checkbook_advanced_search_autocomplete_spending_department($year, $agency, $spendingCat, $dataSource = 'checkbook') {
  $yearId = (isset($year)) ? substr($year, 3, strlen($year)) : "all";
  $spendingCatParam = $spendingCat ? " AND spending_category_id = '" . $spendingCat . "' " : "";

  if($dataSource == Datasource::NYCHA){
      $yearParam = ($yearId != 'all') ? " AND issue_date_year_id = '" . $yearId . "' " : "";
      $query = "SELECT DISTINCT department_name 
                FROM all_disbursement_transactions 
                WHERE 1=1 {$yearParam} {$spendingCatParam}
                ORDER BY department_name ASC";
  }else {
      $yearParam = ($yearId != 'all') ? " AND check_eft_issued_nyc_year_id = '" . $yearId . "' " : "";
      $agencyParam = $agency ? " AND agency_id = '" . $agency . "' " : "";
      $query = "SELECT DISTINCT department_name
                FROM disbursement_line_item_details
                WHERE 1 =1 {$agencyParam} {$yearParam} {$spendingCatParam} 
                ORDER BY department_name ASC";
  }

  $results = _checkbook_project_execute_sql($query, "main",  $dataSource);
  if(count($results) == 0){
      $options = array('label' => 'No Matches Found','value' => '');
  }else{
      foreach ($results as $result) {
          $options[$result['department_name']] = $result['department_name'];
      }
  }
  $matches = array();
  if(isset($options['label'])) {
      $matches[] = $options['label'];
  }
  else {
      foreach($options as $key => $value)
          $matches[] = htmlentities($value);
  }
  drupal_json_output($matches);
}

/**
 * Returns the options for Expense Category dropdown based on the selected agency/department/year/spending category values.
 * @param string $year
 *   Year value
 * @param string $agency
 *   Selected agency id
 * @param $dept
 *   Selected Department
 * @param string $exptype
 *   Selected Spending Category Id
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 *
 */
function _checkbook_advanced_search_autocomplete_spending_expcategory($year, $agency, $dept, $exptype, $data_source = 'checkbook') {
    $options = _get_spending_expense_cat_options($agency, str_replace('__','/',$dept), $exptype, $year, $data_source);
    $matches = array();
    if(isset($options['label'])) {
        $matches[] = $options['label'];
    }
    else {
        foreach($options as $key => $value){
            $value = htmlentities($value);
            $name = (strlen($value) > 18) ? substr($value, 0, 18) . '...' : $value;
            $matches[] = array("code" => $key, "name" => $name, "title" => $value);
        }
    }
    drupal_json_output($matches);
}


//Contracts autocomplete menu callback functions
/**
 * Construct query string and get results from Solr.
 *
 * @param string $facet
 *   Facet results needed
 * @param string $query
 *   Partially constructed query
 * @param null $status
 *   Contract status
 * @param string|null $category
 *   Contract category
 * @param string|null $contract_type
 *   Agreement type code
 * @param string|null $agency
 *   Agency code
 * @param string|null $award_method
 *   Award method code
 * @param string|null $year
 *   Year
 * @param $mwbe_category
 *   Selected M/WBE Category
 * @param $industry
 *   Selected Industry
 * @param $includes_sub_vendors
 *   Selected Contract Includes Sub Vendors
 * @param $sub_vendor_status
 *   Selected Sub Vendor Status in PIP
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 *
 * @return array
 *   Results from Solr
 */
function _get_contracts_advanced_search_autocomplete_results($facet, $query, $status = null, $category = null, $contract_type = null, $agency = null, $award_method = null, $year = null, $mwbe_category = null, $industry = null, $includes_sub_vendors = null, $sub_vendor_status = null, $data_source = 'checkbook') {

    if($category){
        if($category == 'all'){
            $query .= '&fq=contract_category_name:expense~revenue';
        }
        else{
            $query .= '&fq=contract_category_name:'.$category;
        }
    }
    if ($agency) {
        $field = $data_source == 'checkbook_oge' ? 'oge_agency_id' : 'agency_id';
        $query .= '&fq='.$field.':'.$agency;
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
    if ($mwbe_category) {
        $query .= '&fq=minority_type_id:'._checkbook_advanced_search_autocomplete_get_mwbe_category($mwbe_category);
    }
    if ($industry) {
        $query .= '&fq=industry_type_id:'.$industry;
    }
   /* if($status != 'P' && $category != 'revenue') {

    }*/
    if ($includes_sub_vendors) {
        $query .= '&fq=scntrc_status:'.$includes_sub_vendors;
    }
    if ($sub_vendor_status) {
        $query .= '&fq=aprv_sta:'.$sub_vendor_status;
    }
    $matches = _checkbook_autocomplete_solr_results('citywide', $query, $facet);
    return $matches;
}

//Budget autocomplete menu callback functions

/**
 * @param $fiscal_year
 * @param $agency
 * @return mixed for department based on selected agency and year
 */
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

/**
 * @param $fiscal_year
 * @param $agency
 * @param $dept
 * @return mixed for Expense Category based on selected agency, Department and year
 */
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

/**
 * @param $fiscal_year
 * @param $agency
 * @param $dept
 * @param $expcategory
 * @param $budget_name
 * @return mixed for budget code based on selected agency, department, expense category, year and budget name
 */
function _checkbook_advanced_search_autocomplete_budget_budgetcode($fiscal_year, $agency, $dept, $expcategory, $budget_name) {
   
    $params =  array("budget_fiscal_year_id"=>$fiscal_year);
    if($agency != '0' ) {
        $params["agency_id"] = $agency;
    }
    if($dept != '0' ) {
        $params["department_name"] = str_replace('__','/',$dept);
    }
    if($expcategory != '0' ) {
        $params["object_class_name"] = str_replace('__','/',$expcategory);
    }
    if($budget_name != '0' ) {
        $params["budget_code_name"] = str_replace('__','/',$budget_name);
    }
    $results = get_db_results(false, 'checkbook:budget', array("budget_code_code.budget_code_code"), $params,"budget_code_code.budget_code_code");
    if(count($results) == 0 && $agency != '0'){
        unset($params["budget_code_name"]);
        $results = get_db_results(false, 'checkbook:budget', array("budget_code_code.budget_code_code"), $params,"budget_code_code.budget_code_code");
    }
    if (count($results ) > 0) {
        $matches = array();
        foreach ($results as $key=>$value) {
            $matches[] = $value['budget_code_code.budget_code_code'];
        }
        drupal_json_output($matches);
    }
    else {
        drupal_json_output(array(array('label'=>'No Matches Found','value'=>'')));
    }
}

/**
 * @param $fiscal_year
 * @param $agency
 * @param $dept
 * @param $expcategory
 * @param $budget_code
 * @return mixed for budget name based on selected agency, department, expense category, year and budget code
 */
function _checkbook_advanced_search_autocomplete_budget_budgetname($fiscal_year, $agency, $dept, $expcategory, $budget_code) {
    $params =  array("budget_fiscal_year_id"=>$fiscal_year);
    if($agency != '0' ) {
        $params["agency_id"] = $agency;
    }
    if($dept != '0' ) {
        $params["department_name"] = str_replace('__','/',$dept);
    }
    if($expcategory != '0' ) {
        $params["object_class_name"] = str_replace('__','/',$expcategory);
    }
    if($budget_code != '0' ) {
        $params["budget_code"] = $budget_code;
    }
    $results = get_db_results(false, 'checkbook:budget', array("budget_code_name.budget_code_name", "budget_code_code.budget_code_code"), $params,"budget_code_name.budget_code_name");
    if(count($results) == 0 && $agency != '0'){
        unset($params["budget_code"]);
        $results = get_db_results(false, 'checkbook:budget', array("budget_code_name.budget_code_name", "budget_code_code.budget_code_code"), $params,"budget_code_name.budget_code_name");
    }
    if (count($results) > 0) {
        $matches = array();
        foreach ($results as $key=>$value) {
            $budget_name_value = $value['budget_code_name.budget_code_name'];
            $budget_name_text = (strlen($budget_name_value) > 18) ? substr($budget_name_value, 0, 18) . '...' : $budget_name_value;
            $matches[] = array('label'=> $budget_name_text,'value' => $budget_name_value, 'code' => $value["budget_code_code.budget_code_code"]);
        }
        drupal_json_output($matches);
    }
    else {
        drupal_json_output(array(array('label'=>'No Matches Found','value'=>'')));
    }
}

function _checkbook_advanced_search_get_payroll_years($dataSource){
  $yearList = _checkbook_advanced_search_get_year(Domain::$PAYROLL, NULL, $dataSource);
  if(count($yearList) > 0){
    $matches = array();
    foreach($yearList as $key => $value){
      $matches[] = array('label'=> $value,'value' => $key);
    }
    drupal_json_output($matches);
  }else{
    drupal_json_output(array(array('label'=>'No Matches Found','value'=>'')));
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

/**
 * Used for the M/WBE Category dropdown in the advanced search this may have a list
 *
 * For example $minority_types = 1~2~3, return (1 OR 2 OR 3)
 * @param $minority_types
 * @return string
 *
 */

function _checkbook_advanced_search_autocomplete_get_mwbe_category($minority_types){
    return '('.implode('%20OR%20',explode('~',$minority_types)).')';
}
