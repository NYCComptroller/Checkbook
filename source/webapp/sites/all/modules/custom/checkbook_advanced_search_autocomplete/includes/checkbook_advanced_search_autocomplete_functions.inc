<?php

//Generic Spending/Contracts Auto-complete

/**
 * Construct query string and get results from Solr.
 *
 * @param string $facet
 *   Facet results needed
 * @param string $query
 *   Partially constructed query
 * @param string $domain
 *   spending, used to merge later
 * @param string $parameters
 *   Solr filter parameters
 *
 * @return array
 *   Results from Solr
 */
function _get_advanced_search_autocomplete_results($facet, $query, $domain, $parameters) {

    switch($domain)
    {
        case 'spending':
            $year = $parameters['year'];
            $department = $parameters['department'];
            $agency = $parameters['agency'];
            $expense_category = $parameters['expense_category'];
            $expense_type = $parameters['expense_type'];
            $entity_contract_number = $parameters['entity_contract_number'];
            $commodity_line = $parameters['commodity_line'];
            $budget_name = $parameters['budget_name'];

            if ($year) $query .= _checkbook_autocomplete_get_start_end_dates($year, 'spending');
            if ($department) $query .= '&fq=department_name:' . $department;
            if ($agency) $query .= '&fq=agency_id:' . $agency;
            if ($expense_category) $query .= '&fq=expenditure_object_name:' . $expense_category;
            if ($expense_type) $query .= '&fq=spending_category_id:' . $expense_type;
            if ($entity_contract_number) $query .= '&fq=spending_entity_contract_number:' . $entity_contract_number;
            if ($commodity_line) $query .= '&fq=spending_commodity_line:' . $commodity_line;
            if ($budget_name) $query .= '&fq=spending_budget_name:' . $budget_name;
            break;

        case 'contracts':
            $status = $parameters['status'];
            $category = $parameters['category'];
            $contract_type = $parameters['contract_type'];
            $agency = $parameters['agency'];
            $award_method = $parameters['award_method'];
            $year = $parameters['year'];
            $entity_contract_number = $parameters['entity_contract_number'];
            $commodity_line = $parameters['commodity_line'];
            $budget_name = $parameters['budget_name'];

            switch($status)
            {
                case 'P':
                    $query .= '&fq=contract_status:pending';
                    break;

                case 'A':
                    if(isset($year)) $query .= _checkbook_autocomplete_get_start_end_dates($year, 'contracts', 'A');
                    $query .= '&fq=contract_status:registered';
                    break;

                case 'R':
                    $query .= '&fq=contract_status:registered';
                    if (isset($year)) $query .= _checkbook_autocomplete_get_start_end_dates($year, 'contracts', 'R');
                    break;
            }
            if ($category) $query .= '&fq=contract_category_name:' . $category;
            if(!in_array($contract_type, array('No Contract Type Selected', '0')))
            {
                $code_id_array = _checkbook_advanced_search_autocomplete_get_code_id($contract_type);
                if($status == 'P')
                    $query .= '&fq=contract_type_code:'.$code_id_array['code'];
                else
                    $query .= '&fq=contract_type_id:'.$code_id_array['id'];
            }
            if ($agency) $query .= '&fq=agency_id:' . $agency;
            if($award_method){
                $code_id_array = _checkbook_advanced_search_autocomplete_get_code_id($award_method);
                $query .= '&fq=award_method_id:'.$code_id_array['id'];
            }
            if ($entity_contract_number) $query .= '&fq=contract_entity_contract_number:' . $entity_contract_number;
            if ($commodity_line) $query .= '&fq=contract_commodity_line:' . $commodity_line;
            if ($budget_name) $query .= '&fq=contract_budget_name:' . $budget_name;
            break;
    }

    $matches = _checkbook_autocomplete_solr_results($query, $facet);

    return $matches;
}
/**
 * Construct query string and get results from Solr.
 *
 * @param string $field
 *   Field results needed
 * @param string $domain
 *   Domain to search on
 * @param string $data_source
 *   data source (i.e. checkbook, checkbook_oge)
 * @param string $args
 *   Solr filter parameters
 *
 * @return array
 *   Results from Solr
 */
function _checkbook_advanced_search_autocomplete($field, $domain, $data_source = 'checkbook', $args, $string) {

    $search_term = _get_autocomplete_search_term();
    $query_field = $field;
    $facet_field = $field;
    $prefix = $domain == 'contracts' ? 'contract' : $domain;

    switch($field)
    {
        case 'payee_name':
            $query_field = 'vendor_name_autocomplete';
            $facet_field = 'vendor_name';
            break;
        case 'document_id':
            $query_field = 'expense_id';
            $facet_field = 'expense_id';
            break;
        case 'capital_project':
            $query_field = 'reporting_code';
            $facet_field = 'reporting_code';
            break;
        case 'apt_pin':
            $query_field = 'apt_pin';
            $facet_field = 'apt_pin';
            break;
        case 'pin':
            $query_field = 'pin';
            $facet_field = 'pin';
            break;
        case 'contract_id':
            $query_field = 'contract_number';
            $facet_field = 'contract_number';
            break;
        case 'entity_contract_number':
            $query_field = $prefix . '_entity_contract_number';
            $facet_field = $prefix . '_entity_contract_number';
            break;
        case 'commodity_line':
            $query_field = $prefix . '_commodity_line';
            $facet_field = $prefix . '_commodity_line';
            break;
        case 'budget_name':
            $query_field = $prefix . '_budget_name';
            $facet_field = $prefix . '_budget_name';
            break;
        case 'vendor':
            $query_field = 'vendor_name_autocomplete';
            $facet_field = 'vendor_name';
            break;
        case 'department':
            $search_term = 'department_name';
            $query_field = 'department_name';
            $facet_field = 'department_name';
            break;
        case 'expense_category':
            $search_term = 'department_name';
            $query_field = 'department_name';
            $facet_field = 'department_name';
            break;
    }

    foreach(explode(',', $args) as $arg)
    {
        $param_parts = explode('=>', $arg);
        if($param_parts[0] != $field)
            $parameters[$param_parts[0]] = $param_parts[1];
    }

    if (!empty($search_term) & !empty($parameters))
    {
        $query = 'select/?q=' . $query_field . ':' . $search_term . '&facet=true&facet.field=' . $facet_field . '&fq=domain:' . $domain . '&wt=php&facet.limit=10';
        if($data_source == 'checkbook_oge') $query .= '&fq=agency_type:oge';
        $options = _get_advanced_search_autocomplete_results($facet_field, $query, $domain, $parameters);
        $matches = array();
        if(isset($options[0]['label']) && $options[0]['label'] == "No Matches Found")
            $matches = $options;
        else
            foreach($options as $key => $value)
                $matches[] = htmlentities($value);

        drupal_json_output($matches);
    }
}

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
 * @param $data_source
 *   Data source to get data (i.e checkbook, checkbook_oge)
 ***/
function _get_spending_advanced_search_autocomplete_results($facet, $query, $year = null, $agency = null, $expcategory = null, $dept = null, $exptype = null, $data_source = 'checkbook'){
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
        $matches = _checkbook_autocomplete_solr_results($query, $facet, $data_source);
        return $matches;
}

/**
 * Returns the options for Expense Category Dropdown based on the selected agency/department/year/spending category values.
 * @param string $year
 *   Year value
 * @param string $agency
 *   Selected agency id
 * @param string $dept
 *   Selected Department Name
 * @param string $spending_cat
 *   Selected Spending Category Id
 * @param $data_source
 *   Data source to get data (i.e checkbook, checkbook_oge)
 ***/
function _get_spending_expense_cat_options($agency, $dept, $spending_cat, $year, $data_source = 'checkbook') {
  
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

    $db_name = "main";
  $results = _checkbook_project_execute_sql($query, $db_name,  $data_source);
  if(count($results) == 0){
        $output = array('label' => 'No Matches Found','value' => '');
  }else{
      foreach ($results as $result) {
         $output[$result['expenditure_object_name']] = $result['expenditure_object_name'];
      }
  }
  return $output;
}

/**
 * Returns the auto suggestions for vendor name based on the selected agency/department/year/spending category/expense category values.
 * @param string $year
 *   Year value
 * @param string $agency
 *   Selected agency id
 * @param string $dept
 *   Selected Department Name
 * @param string $exptype
 *   Selected Spending Category Id
 * @param $expcategory
 *   Selected Expense Category
 * @param $data_source
 *   Data source to get data (i.e checkbook, checkbook_oge)
 ***/
function _checkbook_advanced_search_autocomplete_spending_payee($year, $agency, $expcategory, $dept, $exptype = 0, $data_source = 'checkbook') {
    $searchTerm = _get_autocomplete_search_term();
    if (!empty($searchTerm)) {
        $query = 'select/?q=vendor_name_autocomplete:' . $searchTerm . '&facet=true&facet.field=vendor_name&fq=domain:spending&facet.limit=10&wt=php';
        $matches = _get_spending_advanced_search_autocomplete_results('vendor_name', $query, $year, $agency, $expcategory, $dept, $exptype, $data_source);
        drupal_json_output($matches);
    }
}

/**
 * Returns the auto suggestions Contract ID based on the selected agency/department/year/spending category/expense category values.
 * @param string $year
 *   Year value
 * @param string $agency
 *   Selected agency id
 * @param string $dept
 *   Selected Department Name
 * @param string $exptype
 *   Selected Spending Category Id
 * @param $expcategory
 *   Selected Expense Category
 * @param $data_source
 *   Data source to get data (i.e checkbook, checkbook_oge)
 ***/
function _checkbook_advanced_search_autocomplete_spending_contractno($year, $agency, $expcategory, $dept, $exptype = 0, $data_source = 'checkbook') {
    $searchTerm = _get_autocomplete_search_term();
    if (!empty($searchTerm)) {
        $doc_type1 = substr(trim(htmlspecialchars_decode($_REQUEST['term'], ENT_QUOTES)), 0, 3);
        $doc_type2 = substr(trim(htmlspecialchars_decode($_REQUEST['term'], ENT_QUOTES)), 0, 4);

        if(strtolower($doc_type1) == 'ma1' || strtolower($doc_type2) == 'mma1'){
            $matches = array(array('label'=>'No Matches Found','value'=>''));
        }else{
            $query = 'select/?q=contract_number:' . $searchTerm . '&facet=true&facet.field=contract_number&fq=domain:spending&facet.limit=10&wt=php';
            $matches = _get_spending_advanced_search_autocomplete_results('contract_number', $query, $year, $agency, $expcategory, $dept, $exptype, $data_source);
        }
        drupal_json_output($matches);
    }
}

/**
 * Returns the auto suggestions for capital project based on the selected agency/department/year/spending category/expense category values.
 * @param string $year
 *   Year value
 * @param string $agency
 *   Selected agency id
 * @param string $dept
 *   Selected Department Name
 * @param string $exptype
 *   Selected Spending Category Id
 * @param $expcategory
 *   Selected Expense Category
 * @param $data_source
 *   Data source to get data (i.e checkbook, checkbook_oge)
 ***/
function _checkbook_advanced_search_autocomplete_spending_capitalproject($year, $agency, $expcategory, $dept, $exptype = 0, $data_source = 'checkbook') {
	$searchTerm = _get_autocomplete_search_term();
	if (!empty($searchTerm)) {
		$query = 'select/?q=reporting_code:' . $searchTerm . '&facet=true&facet.field=reporting_code&fq=domain:spending&facet.limit=10&wt=php';
		$matches = _get_spending_advanced_search_autocomplete_results('reporting_code', $query, $year, $agency, $expcategory, $dept, $exptype, $data_source);
        drupal_json_output($matches);
	}
}

/**
 * Returns the auto suggestions for expense id based on the selected agency/department/year/spending category/expense category values.
 * @param string $year
 *   Year value
 * @param string $agency
 *   Selected agency id
 * @param string $dept
 *   Selected Department Name
 * @param string $exptype
 *   Selected Spending Category Id
 * @param $expcategory
 *   Selected Expense Category
 * @param $data_source
 *   Data source to get data (i.e checkbook, checkbook_oge)
 ***/
function _checkbook_advanced_search_autocomplete_spending_expenseid($year, $agency, $expcategory, $dept, $exptype = 0, $data_source = 'checkbook') {
	$searchTerm = _get_autocomplete_search_term();
	if (!empty($searchTerm)) {
		$query = 'select/?q=expense_id:' . $searchTerm . '&facet=true&facet.field=expense_id&fq=domain:spending&facet.limit=10&wt=php';
        $matches = _get_spending_advanced_search_autocomplete_results('expense_id', $query, $year, $agency, $expcategory, $dept, $exptype, $data_source);
        drupal_json_output($matches);
	}
}

/**
 * Returns the options for department dropdown based on the selected agency/year/spending category values.
 * @param string $year
 *   Year value
 * @param string $agency
 *   Selected agency id
 * @param string $dept
 *   Selected Department Name
 * @param string $exptype
 *   Selected Spending Category Id
 * @param $expcategory
 *   Selected Expense Category
 * @param $data_source
 *   Data source to get data (i.e checkbook, checkbook_oge)
 ***/
function _checkbook_advanced_search_autocomplete_spending_department($year, $agency, $exptype, $data_source = 'checkbook') {
    $query = 'select/?q=department_name:*&facet=true&facet.field=department_name&fq=domain:spending&wt=php';
    if($data_source == 'checkbook_oge') $query .= '&fq=agency_type:oge';
    $options = _get_spending_advanced_search_autocomplete_results('department_name', $query, $year, $agency, null, null, $exptype, $data_source);
    $matches = array();
    foreach($options as $key => $value)
        $matches[] = htmlentities($value);
    drupal_json_output($matches);
}

/**
 * Returns the options for Expense Category dropdown based on the selected agency/department/year/spending category values.
 * @param string $year
 *   Year value
 * @param string $agency
 *   Selected agency id
 * @param string $dept
 *   Selected Department Name
 * @param string $exptype
 *   Selected Spending Category Id
 * @param $dept
 *   Selected Department
 * @param $data_source
 *   Data source to get data (i.e checkbook, checkbook_oge)
 ***/
function _checkbook_advanced_search_autocomplete_spending_expcategory($year, $agency, $dept, $exptype, $data_source = 'checkbook') {
    $options = _get_spending_expense_cat_options($agency, str_replace('__','/',$dept), $exptype, $year, $data_source);
    $matches = array();
    foreach($options as $key => $value)
        $matches[] = htmlentities(strtolower($value));

    drupal_json_output($matches);
}

//Payroll autocomplete menu callback functions
/**
 * Returns auto suggestions for employee title based on the selected agency/pay frequency/year values.
 * @param string $year
 *   Year value
 * @param string $agency
 *   Selected agency id
 * @param string $pay_frequency
 *   Selected Pay Frequency
 ***/
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
 *
 * @return array
 *   Results from Solr
 */
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

/**
 * Get autocomplete results for vendor from Solr.
 *
 * @param string $status
 *   Status
 * @param string $category
 *   Contract category
 * @param string $contract_type
 *   Contract Type
 * @param string $agency
 *   Agency code
 * @param string $award_method
 *   Award method id
 * @param string $year
 *   Year
 */
function _checkbook_advanced_search_autocomplete_contracts_vendor_name($status, $category, $contract_type, $agency, $award_method, $year){
    $searchTerm = _get_autocomplete_search_term();
    if(!empty($searchTerm)){
        $query = 'select/?q=vendor_name_autocomplete:'.$searchTerm.'&facet=true&facet.field=vendor_name&fq=domain:contracts&facet.limit=10&wt=php';
        $matches = _get_contracts_advanced_search_autocomplete_results('vendor_name', $query, $status, $category, $contract_type, $agency, $award_method, $year);
        drupal_json_output($matches);
    }
}

/**
 * Get autocomplete results for Contract Status from Solr.
 *
 * @param string $status
 *   Status
 * @param string $category
 *   Contract category
 * @param string $contract_type
 *   Contract Type
 * @param string $agency
 *   Agency code
 * @param string $award_method
 *   Award method id
 * @param string $year
 *   Year
 */
function _checkbook_advanced_search_autocomplete_contracts_contract_number($status, $category, $contract_type, $agency, $award_method, $year){
    $searchTerm = _get_autocomplete_search_term();
    if(!empty($searchTerm)){
        $query = 'select/?q=contract_number:'.$searchTerm.'&facet=true&facet.field=contract_number&fq=domain:contracts&facet.limit=10&wt=php';
        $matches = _get_contracts_advanced_search_autocomplete_results('contract_number', $query, $status, $category, $contract_type, $agency, $award_method, $year);
        drupal_json_output($matches);
    }
}

/**
 * Get autocomplete results for APT PIN from Solr.
 *
 * @param string $status
 *   Status
 * @param string $category
 *   Contract category
 * @param string $contract_type
 *   Contract Type
 * @param string $agency
 *   Agency code
 * @param string $award_method
 *   Award method id
 * @param string $year
 *   Year
 */
function _checkbook_advanced_search_autocomplete_contracts_apt_pin($status, $category, $contract_type, $agency, $award_method, $year){
   $searchTerm = _get_autocomplete_search_term();
    if(strlen($searchTerm) > 0){
        $query = 'select/?q=apt_pin:'. $searchTerm .'&facet=true&facet.field=apt_pin&fq=domain:contracts&facet.limit=10&wt=php';
        $matches = _get_contracts_advanced_search_autocomplete_results('apt_pin', $query, $status, $category, $contract_type, $agency, $award_method, $year);
        drupal_json_output($matches);
    }
}

/**
 * Get autocomplete results for PIN from Solr.
 *
 * @param string $status
 *   Status
 * @param string $category
 *   Contract category
 * @param string $contract_type
 *   Contract Type
 * @param string $agency
 *   Agency code
 * @param string $award_method
 *   Award method id
 * @param string $year
 *   Year
 */
function _checkbook_advanced_search_autocomplete_contracts_pin($status, $category, $contract_type, $agency, $award_method, $year){
    $searchTerm = _get_autocomplete_search_term();
    if(strlen($searchTerm) > 0){
        $query = 'select/?q=pin:'. $searchTerm .'&facet=true&facet.field=pin&fq=domain:contracts&facet.limit=10&wt=php';
        $matches = _get_contracts_advanced_search_autocomplete_results('pin', $query, $status, $category, $contract_type, $agency, $award_method, $year);
        drupal_json_output($matches);
    }
}

//Budget autocomplete menu callback functions

/** @return options for department based on selected agency and year */
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

/** @return options for Expense Category based on selected agency, Department and year */
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

/** @return autosuggestions for budget code based on selected agency, department, expense category and year */
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
            $query .= '&fq=department_name:"' . _checkbook_autocomplete_escapeSolrValue(trim($dept)) .'"';
        }
        if ($expcategory) {
            $query .= '&fq=expenditure_object_name:"' . _checkbook_autocomplete_escapeSolrValue(trim($expcategory)) .'"';
        }
        $matches = _checkbook_autocomplete_solr_results($query, 'budget_code_name_code');
        drupal_json_output($matches);
    }
}

//Revenue autocomplete menu callback functions

/** @return auto suggestions for revenue class name based on selected year, fund class, agency, revenue category and budget year */
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

/** @return auto suggestions for revenue source name based on selected year, fund class, agency, revenue category and budget year */
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
