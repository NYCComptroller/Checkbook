<?php

// Contracts autocomplete menu callback functions:
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
 * @param null $mwbe_category
 *   Minority type id
 * @param null $industry
 *   Industry type id
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
function _get_contracts_datafeeds_autocomplete_results($facet, $query, $status = NULL, $category = NULL, $contract_type = NULL, $agency = NULL, $award_method = NULL, $year = NULL, $mwbe_category = NULL, $industry = NULL, $includes_sub_vendors = null, $sub_vendor_status = null, $data_source = 'checkbook') {
    if ($status == 'pending') {
        $query .= '&fq=contract_status:pending';
    }
    if ($status == 'active') {
        if (strtolower($year) != 'all') {
            $query .= _checkbook_autocomplete_get_start_end_dates($year, 'contracts', 'A');
        }
        else {
            $query .= '&fq=-contract_status:pending';
        }
    }
    if ($status == 'registered') {
        $query .= '&fq=contract_status:registered';
        if (isset($year)) {
            $query .= _checkbook_autocomplete_get_start_end_dates($year, 'contracts', 'R');
        }
    }
    if ($category) {
        if($category == 'all'){
            $query .= '&fq=contract_category_name:expense~revenue';
        }
        else{
            $query .= '&fq=contract_category_name:' . ucfirst($category);
        }

    }
    if ($contract_type) {
        $contract_id= _get_contracts_datafeeds_autocomplete_contract_type_options($contract_type);
        if ($contract_id) $query .= '&fq=contract_type_id:' . $contract_id;
    }
    if ($agency) {
        switch($data_source) {
            case 'checkbook_oge':
                $agency_id = _get_contracts_datafeeds_autocomplete_agency_id($agency, 'checkbook_oge');
                $query .= '&fq=oge_agency_id:' . $agency_id;
                break;

              default:
                $query .= '&fq=agency_code:' . $agency;
                break;
        }
    }

    if ($award_method) {
        $award_method_id = _get_contracts_datafeeds_autocomplete_award_method_options($award_method);
        if ($award_method_id) $query .= '&fq=award_method_id:' . $award_method_id;
    }
    if($mwbe_category){
        $query .= '&fq=minority_type_id:'._checkbook_autocomplete_get_mwbe_category($mwbe_category);
    }
    if($industry){
        $query .= '&fq=industry_type_id:'.$industry;
    }

    //if($status != 'P' && $category != 'revenue') {

    //}
    if ($includes_sub_vendors) {
        $query .= '&fq=scntrc_status:'.$includes_sub_vendors;
    }
    if ($sub_vendor_status) {
        $query .= '&fq=aprv_sta:'.$sub_vendor_status;
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
 *   Award method code
 * @param string $agency
 *   Agency code
 * @param string $award_method
 *   Award method code
 * @param string $year
 *   Year
 * @param string $mwbe_category
 *   Minority type id
 * @param string $industry
 *   Industry type id
 * @param $includes_sub_vendors
 *   Selected Contract Includes Sub Vendors
 * @param $sub_vendor_status
 *   Selected Sub Vendor Status in PIP
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_contracts_vendor($status, $category, $contract_type, $agency, $award_method, $year, $mwbe_category, $industry, $includes_sub_vendors, $sub_vendor_status, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        switch($data_source)
        {
            case 'checkbook':
                $query = 'select/?q=vendor_name_code_autocomplete:' . $search_term . '&facet=true&facet.field=vendor_name_code&fq=domain:contracts&wt=php&facet.limit=10';
                $query .= '&fq=-agency_type:oge';
                $matches = _get_contracts_datafeeds_autocomplete_results('vendor_name_code', $query, $status, $category, $contract_type, $agency, $award_method, $year, $mwbe_category, $industry, $includes_sub_vendors, $sub_vendor_status, $data_source);
                break;
            case 'checkbook_oge':
                $query = 'select/?q=vendor_name_autocomplete:' . $search_term . '&facet=true&facet.field=vendor_name&fq=domain:contracts&wt=php&facet.limit=10';
                $query .= '&fq=agency_type:oge';
                $matches = _get_contracts_datafeeds_autocomplete_results('vendor_name', $query, $status, $category, $contract_type, $agency, $award_method, $year, $mwbe_category, $industry, null, null, $data_source);
                break;
        }
        drupal_json_output($matches);
    }
}

/**
 * Get autocomplete results for contract number from Solr.
 *
 * @param string $status
 *   Contract status
 * @param string $category
 *   Contract category
 * @param string $contract_type
 *   Agreement type code
 * @param string $agency
 *   Agency code
 * @param string $award_method
 *   Award method code
 * @param string $year
 *   Year
 * @param string $mwbe_category
 *   Minority type id
 * @param string $industry
 *   Industry type id
 * @param $includes_sub_vendors
 *   Selected Contract Includes Sub Vendors
 * @param $sub_vendor_status
 *   Selected Sub Vendor Status in PIP
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_contracts_contract_number($status, $category, $contract_type, $agency, $award_method, $year, $mwbe_category, $industry, $includes_sub_vendors, $sub_vendor_status, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        $query = 'select/?q=contract_number:' . $search_term . '&facet=true&facet.field=contract_number&fq=domain:contracts&wt=php&facet.limit=10';
        $query .= $data_source == 'checkbook_oge' ? '&fq=agency_type:oge' : '&fq=-agency_type:oge';
        $matches = _get_contracts_datafeeds_autocomplete_results('contract_number', $query, $status, $category, $contract_type, $agency, $award_method, $year, $mwbe_category, $industry, $includes_sub_vendors, $sub_vendor_status, $data_source);
        drupal_json_output($matches);
    }
}

/**
 * Get autocomplete results for APT PIN from Solr.
 *
 * @param string $status
 *   Contract status
 * @param string $category
 *   Contract cateogry
 * @param string $contract_type
 *   Agreement type code
 * @param string $agency
 *   Agency code
 * @param string $award_method
 *   Award method code
 * @param string $year
 *   Year
 * @param null $mwbe_category
 *   Minority type id
 * @param string $industry
 *   Industry type id
 * @param $includes_sub_vendors
 *   Selected Contract Includes Sub Vendors
 * @param $sub_vendor_status
 *   Selected Sub Vendor Status in PIP
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_contracts_apt_pin($status, $category, $contract_type, $agency, $award_method, $year, $mwbe_category, $industry, $includes_sub_vendors, $sub_vendor_status, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        $apt_pin = $data_source == 'checkbook_oge' ? 'apt_pin' : 'prime_apt_pin';
        $query = 'select/?q='.$apt_pin.':' . $search_term . '&facet=true&facet.field='.$apt_pin.'&fq=domain:contracts&wt=php&facet.limit=10';
        $query .= $data_source == 'checkbook_oge' ? '&fq=agency_type:oge' : '&fq=-agency_type:oge';
        $matches = _get_contracts_datafeeds_autocomplete_results($apt_pin, $query, $status, $category, $contract_type, $agency, $award_method, $year, $mwbe_category, $industry, $includes_sub_vendors, $sub_vendor_status, $data_source);
        drupal_json_output($matches);
    }
}

/**
 * Get autocomplete results for PIN from Solr.
 *
 * @param string $status
 *   Contract status
 * @param string $category
 *   Contract category
 * @param string $contract_type
 *   Agreement type code
 * @param string $agency
 *   Agency code
 * @param string $award_method
 *   Award method code
 * @param string $year
 *   Year
 * @param null $mwbe_category
 *   Minority type id
 * @param string $industry
 *   Industry type id
 * @param $includes_sub_vendors
 *   Selected Contract Includes Sub Vendors
 * @param $sub_vendor_status
 *   Selected Sub Vendor Status in PIP
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_contracts_pin($status, $category, $contract_type, $agency, $award_method, $year, $mwbe_category, $industry, $includes_sub_vendors, $sub_vendor_status, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        $pin = $data_source == 'checkbook_oge' ? 'pin' : 'prime_pin';
        $query = 'select/?q='.$pin.':' . $search_term . '&facet=true&facet.field='.$pin.'&fq=domain:contracts&wt=php&facet.limit=10';
        $query .= $data_source == 'checkbook_oge' ? '&fq=agency_type:oge' : '&fq=-agency_type:oge';
        $matches = _get_contracts_datafeeds_autocomplete_results($pin, $query, $status, $category, $contract_type, $agency, $award_method, $year, $mwbe_category, $industry, $includes_sub_vendors, $sub_vendor_status, $data_source);
        drupal_json_output($matches);
    }
}

/**
 * Get autocomplete results for Entity Contract Number from Solr.
 *
 * @param string $status
 *   Contract status
 * @param string $category
 *   Contract category
 * @param string $contract_type
 *   Agreement type code
 * @param string $agency
 *   Agency code
 * @param string $award_method
 *   Award method code
 * @param string $year
 *   Year
 * @param null $mwbe_category
 *   Minority type id
 * @param string $industry
 *   Industry type id
 * @param $includes_sub_vendors
 *   Selected Contract Includes Sub Vendors
 * @param $sub_vendor_status
 *   Selected Sub Vendor Status in PIP
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_contracts_entitycontractnum($status, $category, $contract_type, $agency, $award_method, $year, $mwbe_category, $industry, $includes_sub_vendors, $sub_vendor_status, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    $facet_prefix = _get_autocomplete_search_term_facet_prefix();
    if (!empty($search_term)) {
        $query = 'select/?q=contract_entity_contract_number:' . $search_term . '&facet=true&facet.field=contract_entity_contract_number&facet.prefix='. $facet_prefix .'&fq=domain:contracts&facet.limit=10&wt=php';
        $query .= $data_source == 'checkbook_oge' ? '&fq=agency_type:oge' : '&fq=-agency_type:oge';
        $matches = _get_contracts_datafeeds_autocomplete_results('contract_entity_contract_number', $query, $status, $category, $contract_type, $agency, $award_method, $year, $mwbe_category, $industry, $includes_sub_vendors, $sub_vendor_status, $data_source);
        drupal_json_output($matches);
    }
}

/**
 * Get autocomplete results for Commodity Line from Solr.
 *
 * @param string $status
 *   Contract status
 * @param string $category
 *   Contract category
 * @param string $contract_type
 *   Agreement type code
 * @param string $agency
 *   Agency code
 * @param string $award_method
 *   Award method code
 * @param string $year
 *   Year
 * @param null $mwbe_category
 *   Minority type id
 * @param string $industry
 *   Industry type id
 * @param $includes_sub_vendors
 *   Selected Contract Includes Sub Vendors
 * @param $sub_vendor_status
 *   Selected Sub Vendor Status in PIP
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_contracts_commodityline($status, $category, $contract_type, $agency, $award_method, $year, $mwbe_category, $industry, $includes_sub_vendors, $sub_vendor_status, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    $facet_prefix = _get_autocomplete_search_term_facet_prefix();
    if (!empty($search_term)) {
        $query = 'select/?q=contract_commodity_line:' . $search_term . '&facet=true&facet.field=contract_commodity_line&facet.prefix='. $facet_prefix .'&fq=domain:contracts&facet.limit=10&wt=php';
        $query .= $data_source == 'checkbook_oge' ? '&fq=agency_type:oge' : '&fq=-agency_type:oge';
        $matches = _get_contracts_datafeeds_autocomplete_results('contract_commodity_line', $query, $status, $category, $contract_type, $agency, $award_method, $year, $mwbe_category, $industry, $includes_sub_vendors, $sub_vendor_status, $data_source);
        drupal_json_output($matches);
    }
}

/**
 * Get autocomplete results for Budget Name from Solr.
 *
 * @param string $status
 *   Contract status
 * @param string $category
 *   Contract category
 * @param string $contract_type
 *   Agreement type code
 * @param string $agency
 *   Agency code
 * @param string $award_method
 *   Award method code
 * @param string $year
 *   Year
 * @param string $mwbe_category
 *   Minority type id
 * @param string $industry
 *   Industry type id
 * @param $includes_sub_vendors
 *   Selected Contract Includes Sub Vendors
 * @param $sub_vendor_status
 *   Selected Sub Vendor Status in PIP
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_contracts_budgetname($status, $category, $contract_type, $agency, $award_method, $year, $mwbe_category, $industry, $includes_sub_vendors, $sub_vendor_status, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        $query = 'select/?q=contract_budget_name_autocomplete:' . $search_term . '&q.op=AND&rows=0&facet=true&facet.field=contract_budget_name&facet.mincount=1&facet.limit=10&wt=php';
        $query .= $data_source == 'checkbook_oge' ? '&fq=agency_type:oge' : '&fq=-agency_type:oge';
        $matches = _get_contracts_datafeeds_autocomplete_results('contract_budget_name', $query, $status, $category, $contract_type, $agency, $award_method, $year, $mwbe_category, $industry, $includes_sub_vendors, $sub_vendor_status, $data_source);
        drupal_json_output($matches);
    }
}

function _checkbook_autocomplete_datafeeds_nycha_contracts($solr_field, $purchase_order_type, $responsibility_center, $contract_type, $award_method, $industry, $agency, $year, $data_source){
    $search_term = _get_autocomplete_search_term();
    $facetquery = "";
    if (!empty($search_term)) {
        if($purchase_order_type && $purchase_order_type != 'All'){
            $facetquery .= '&fq=agreement_type_code:' . $purchase_order_type;
        }
        if($responsibility_center){
            $facetquery .= '&fq=responsibility_center_code:' . $responsibility_center;
        }
        if($contract_type){
            $facetquery .= '&fq=contract_type_code:' . $contract_type;
        }
        if($award_method){
            $facetquery .= '&fq=award_method_code:' . $award_method;
        }
        if ($industry){
            $facetquery .= '&fq=industry_type_code:' . $industry;
        }
        if($agency){
            $facetquery .= '&fq=agency_code:' . $agency;
        }
        if($year && $year != 'ALL'){
            $year = ltrim($year, 'FY');
            $facetquery .= '&('.$year.':[*%20TO%20agreement_start_year]%20AND%20'.$year.':[agreement_end_year%20TO%20*])';
        }
        $query = "select/?q=".$solr_field.":" . $search_term
            ."&facet.field=" . $solr_field .$facetquery
            ."&q.op=AND&rows=0&facet=true&facet.mincount=1&facet.limit=10&wt=php";
        $matches = _checkbook_autocomplete_solr_results($query, $solr_field);
        drupal_json_output($matches);
    }
}
// Spending autocomplete menu callback functions:
/**
 * Construct query string and get results from Solr.
 *
 * @param $facet
 * @param $query
 * @param null $year
 *   Year
 * @param null $agency
 *   Agency code
 * @param null $expcategory
 *   Expenditure object code
 * @param null $dept
 *   Department code
 * @param null $exptype
 *   Spending category code
 * @param null $mwbe_category
 *   Minority type id
 * @param int $industry
 *   Industry type id
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 * @return array
 */
function _get_spending_datafeeds_autocomplete_results($facet, $query, $year = NULL, $agency = NULL, $expcategory = NULL, $dept = NULL, $exptype = NULL, $mwbe_category = NULL, $industry = NULL, $data_source = 'checkbook') {
    if ($year) {
        $query .= _checkbook_autocomplete_get_start_end_dates($year, 'spending');
    }
    if ($dept) {
        $query .= '&fq=department_code:' . $dept;
    }
    if ($agency) {
        $query .= '&fq=agency_code:' . $agency;
    }
    if ($expcategory) {
        $query .= '&fq=expenditure_object_code:' . $expcategory;
    }
    if ($exptype) {
        $exptypeid = _get_spending_datafeeds_autocomplete_expense_type_options($exptype, $data_source);
        if ($exptypeid) $query .= '&fq=spending_category_id:' . $exptypeid;
    }
    if($mwbe_category){
        $query .= '&fq=minority_type_id:'._checkbook_autocomplete_get_mwbe_category($mwbe_category);
    }
    if($industry){
        $query .= '&fq=industry_type_id:'.$industry;
    }
    $matches = _checkbook_autocomplete_solr_results($query, $facet);
    return $matches;
}

/**
 * Get autocomplete results for Payee from Solr.
 *
 * @param string $year
 *   Year
 * @param string $agency
 *   Agency code
 * @param string $expcategory
 *   Expenditure object code
 * @param string $dept
 *   Department code
 * @param int $exptype
 *   Spending category code
 * @param int $mwbe_category
 *   Minority type id
 * @param int $industry
 *   Industry type id
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_spending_payee($year, $agency, $expcategory, $dept, $exptype = 0, $mwbe_category = 0, $industry, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    $dept = _emptyToZero(urldecode($dept));
    $expcategory = _emptyToZero(urldecode($expcategory));

    if (!empty($search_term)) {
        switch($data_source)
        {
            case 'checkbook':
                $query = 'select/?q=vendor_name_code_autocomplete:' . $search_term . '&facet=true&facet.field=vendor_name_code&fq=domain:spending&wt=php&facet.limit=10';
                $query .= '&fq=-agency_type:oge';
                $matches = _get_spending_datafeeds_autocomplete_results('vendor_name_code', $query, $year, $agency, $expcategory, $dept, $exptype, $mwbe_category, $industry, $data_source);
                break;
            case 'checkbook_oge':
                $query = 'select/?q=vendor_name_autocomplete:' . $search_term . '&facet=true&facet.field=vendor_name&fq=domain:spending&wt=php&facet.limit=10';
                $query .= '&fq=agency_type:oge';
                $matches = _get_spending_datafeeds_autocomplete_results('vendor_name', $query, $year, $agency, $expcategory, $dept, $exptype, $mwbe_category, $industry, $data_source);
                break;
        }
        drupal_json_output($matches);
    }
}

/**
 * Get autocomplete results for Contract Number from Solr.
 *
 * @param string $year
 *   Year
 * @param string $agency
 *   Agency code
 * @param string $expcategory
 *   Expenditure object code
 * @param string $dept
 *   Department code
 * @param int $exptype
 *   Spending category code
 * @param int $mwbe_category
 *   Minority type id
 * @param int $industry
 *   Industry type id
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_spending_contractno($year, $agency, $expcategory, $dept, $exptype = 0, $mwbe_category = 0, $industry, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    $dept = _emptyToZero(urldecode($dept));
    $expcategory = _emptyToZero(urldecode($expcategory));
    if (!empty($search_term)) {
        $query = 'select/?q=contract_number:' . $search_term . '&facet=true&facet.field=contract_number&fq=domain:spending&facet.limit=10&wt=php';
        $query .= $data_source == 'checkbook_oge' ? '&fq=agency_type:oge' : '&fq=-agency_type:oge';
        $matches = _get_spending_datafeeds_autocomplete_results('contract_number', $query, $year, $agency, $expcategory, $dept, $exptype, $mwbe_category, $industry, $data_source);
        drupal_json_output($matches);
    }
}

/**
 * Get autocomplete results for Capital Project from Solr.
 *
 * @param string $year
 *   Year
 * @param string $agency
 *   Agency code
 * @param string $expcategory
 *   Expense category code
 * @param string $dept
 *   Department code
 * @param int $exptype
 *   Spending category code
 * @param int $mwbe_category
 *   Minority type id
 * @param int $industry
 *   Industry type id
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_spending_capitalproject($year, $agency, $expcategory, $dept, $exptype = 0, $mwbe_category = 0, $industry, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    $dept = _emptyToZero(urldecode($dept));
    $expcategory = _emptyToZero(urldecode($expcategory));
    if (!empty($search_term)) {
        $query = 'select/?q=reporting_code_autocomplete:' . $search_term . '&facet=true&facet.field=reporting_code&fq=domain:spending&facet.limit=10&wt=php';
        if($data_source == 'checkbook_oge') $query .= '&fq=agency_type:oge';
        $matches = _get_spending_datafeeds_autocomplete_results('reporting_code', $query, $year, $agency, $expcategory, $dept, $exptype, $mwbe_category, $industry, $data_source);
        drupal_json_output($matches);
    }
}

/**
 * Get autocomplete results for Document ID from Solr.
 *
 * @param string $year
 *   Year
 * @param string $agency
 *   Agency code
 * @param string $expcategory
 *   Expense category code
 * @param string $dept
 *   Department code
 * @param int $exptype
 *   Spending category code
 * @param int $mwbe_category
 *   Minority type id
 * @param int $industry
 *   Industry type id
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_spending_documentid($year, $agency, $expcategory, $dept, $exptype = 0, $mwbe_category = 0, $industry, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    $dept = _emptyToZero(urldecode($dept));
    $expcategory = _emptyToZero(urldecode($expcategory));
    if (!empty($search_term)) {
        $query = 'select/?q=expense_id:' . $search_term . '&facet=true&facet.field=expense_id&fq=domain:spending&facet.limit=10&wt=php';
        if($data_source == 'checkbook_oge') $query .= '&fq=agency_type:oge';
        $matches = _get_spending_datafeeds_autocomplete_results('expense_id', $query, $year, $agency, $expcategory, $dept, $exptype, $mwbe_category, $industry, $data_source);
        drupal_json_output($matches);
    }
}

/**
 * Get autocomplete results for Entity Contract Number from Solr.
 *
 * @param string $year
 *   Year
 * @param string $agency
 *   Agency code
 * @param string $expcategory
 *   Expense category code
 * @param string $dept
 *   Department code
 * @param int $exptype
 *   Spending category code
 * @param int $mwbe_category
 *   Minority type id
 * @param int $industry
 *   Industry type id
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_spending_entitycontractnum($year, $agency, $expcategory, $dept, $exptype = 0, $mwbe_category = 0, $industry, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    $dept = _emptyToZero(urldecode($dept));
    $expcategory = _emptyToZero(urldecode($expcategory));
    if (!empty($search_term)) {
        $query = 'select/?q=spending_entity_contract_number:' . $search_term . '&facet=true&facet.field=spending_entity_contract_number&fq=domain:spending&facet.limit=10&wt=php';
        if($data_source == 'checkbook_oge') $query .= '&fq=agency_type:oge';
        $matches = _get_spending_datafeeds_autocomplete_results('spending_entity_contract_number', $query, $year, $agency, $expcategory, $dept, $exptype, $mwbe_category, $industry, $data_source);
        drupal_json_output($matches);
    }
}

/**
 * Get autocomplete results for Commodity Line from Solr.
 *
 * @param string $year
 *   Year
 * @param string $agency
 *   Agency code
 * @param string $expcategory
 *   Expense category code
 * @param string $dept
 *   Department code
 * @param int $exptype
 *   Spending category code
 * @param int $mwbe_category
 *   Minority type id
 * @param int $industry
 *   Industry type id
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_spending_commodityline($year, $agency, $expcategory, $dept, $exptype = 0, $mwbe_category = 0, $industry, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    $dept = _emptyToZero(urldecode($dept));
    $expcategory = _emptyToZero(urldecode($expcategory));
    if (!empty($search_term)) {
        $query = 'select/?q=spending_commodity_line:' . $search_term . '&facet=true&facet.field=spending_commodity_line&fq=domain:spending&facet.limit=10&wt=php';
        if($data_source == 'checkbook_oge') $query .= '&fq=agency_type:oge';
        $matches = _get_spending_datafeeds_autocomplete_results('spending_commodity_line', $query, $year, $agency, $expcategory, $dept, $exptype, $mwbe_category, $industry, $data_source);
        drupal_json_output($matches);
    }
}

/**
 * Get autocomplete results for Budget Name from Solr.
 *
 * @param string $year
 *   Year
 * @param string $agency
 *   Agency code
 * @param string $expcategory
 *   Expense category code
 * @param string $dept
 *   Department code
 * @param int $exptype
 *   Spending category code
 * @param int $mwbe_category
 *   Minority type id
 * @param int $industry
 *   Industry type id
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_spending_budgetname($year, $agency, $expcategory, $dept, $exptype = 0, $mwbe_category = 0, $industry, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    $dept = _emptyToZero(urldecode($dept));
    $expcategory = _emptyToZero(urldecode($expcategory));
    if (!empty($search_term)) {
        $query = 'select/?q=spending_budget_name_autocomplete:' . $search_term . '&facet=true&facet.field=spending_budget_name&fq=domain:spending&facet.limit=10&wt=php';
        if($data_source == 'checkbook_oge') $query .= '&fq=agency_type:oge';
        $matches = _get_spending_datafeeds_autocomplete_results('spending_budget_name', $query, $year, $agency, $expcategory, $dept, $exptype, $mwbe_category, $industry, $data_source);
        drupal_json_output($matches);
    }
}

function _checkbook_autocomplete_spending_dept($year, $agency, $spending_cat, $data_source = 'checkbook') {
    $year_string = null;

    if ($year && startsWith($year, 'F')) {
        $year = ltrim($year, 'FY');
        $year_string = " AND disbursement_line_item_details.fiscal_year = '" . $year . "' ";
    }
    elseif ($year && startsWith($year, 'C')) {
        $year = ltrim($year, 'CY');
        $year_string = " AND disbursement_line_item_details.calendar_fiscal_year = '" . $year . "' ";
    }
    $agency_string = $agency ? " AND disbursement_line_item_details.agency_code = '" . $agency . "' " : "";
    $spending_cat_string = $spending_cat ? " AND ref_spending_category.spending_category_code = '" . $spending_cat . "' " : "";
    $query = "SELECT DISTINCT department_name|| '[' || department_code || ']' department_name
            FROM disbursement_line_item_details, ref_spending_category
            WHERE disbursement_line_item_details.spending_category_id = ref_spending_category.spending_category_id " .
        $agency_string . $year_string . $spending_cat_string . "ORDER BY department_name ASC";

    $db_name = "main";
    $results = _checkbook_project_execute_sql($query, $db_name,  $data_source);

    if (count($results) > 0) {
        $matches = array();
        foreach ($results as $key=>$value) {
            $dept_name_value = htmlentities($value['department_name']);
            $dept_name_text = (strlen($dept_name_value) > 18) ? substr($dept_name_value, 0, 18) . '...' : $dept_name_value;
            $matches[] = array('label'=> $dept_name_text,'value' => $dept_name_value);
        }
        drupal_json_output($matches);
    }
}

function _checkbook_autocomplete_spending_expcategory($agency, $dept, $spending_cat, $year, $data_source = 'checkbook') {
    $options = $year_type = $year_value = $year_string = $agency_string = $dept_string = null;
    $dept = _emptyToZero(urldecode($dept));
    if ($year && startsWith($year, 'F')) {
        $year = ltrim($year, 'FY');
        $year_string = " AND disbursement_line_item_details.fiscal_year = " . $year . " ";
    }
    elseif ($year && startsWith($year, 'C')) {
        $year = ltrim($year, 'CY');
        $year_string = " AND disbursement_line_item_details.calendar_fiscal_year = " . $year . " ";
    }
    if ($agency) {
        $agency_string = " AND disbursement_line_item_details.agency_code = '" . $agency . "' ";
    }

    $spending_cat_string = ($spending_cat)
        ? " ref_spending_category.spending_category_code = '" . $spending_cat . "' "
        : " ref_spending_category.spending_category_id IN (1,2,3,4,5)";

    if ($dept){
        $dept_string = " AND disbursement_line_item_details.department_code = '" . $dept . "' ";
    }
    $query = "SELECT DISTINCT expenditure_object_name|| '[' || expenditure_object_code || ']' expenditure_object_code
              FROM disbursement_line_item_details, ref_spending_category
              WHERE disbursement_line_item_details.spending_category_id = ref_spending_category.spending_category_id AND " .$spending_cat_string.$year_string.$agency_string.$dept_string. "
              ORDER BY expenditure_object_code ASC";

    $db_name = "main";
    $results = _checkbook_project_execute_sql($query, $db_name,  $data_source);

    if (count($results) > 0) {
        $matches = array();
        foreach ($results as $key=>$value) {
            $exp_obj_value = htmlentities($value['expenditure_object_code']);
            $exp_obj_text = (strlen($exp_obj_value) > 18) ? substr($exp_obj_value, 0, 18) . '...' : $exp_obj_value;
            $matches[] = array('label'=> $exp_obj_text,'value' => $exp_obj_value);
        }
        drupal_json_output($matches);
    }
}

// Payroll autocomplete menu callback functions:
/**
 * Get autocomplete results for civil service title from Solr.
 *
 * @param string $agency
 *   Agency code
 * @param string $payfreq
 *   Pay frequency
 * @param string $year
 *   Year
 */
function _checkbook_autocomplete_payroll_employee_name($agency, $payfreq, $year) {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        $query = 'select/?q=civil_service_title_autocomplete:' . $search_term . '&facet=true&facet.field=civil_service_title&fq=domain:payroll&facet.limit=10&wt=php';
        if ($agency) {
            $agency_code = _getCodeFromName($agency);
            $query .= '&fq=agency_code:' . $agency_code;
        }
        if ($payfreq) {
            $query .= '&fq=pay_frequency:' . $payfreq;
        }
        if ($year) {
            if (startsWith($year, 'F')) {
                $query .= '&fq=fiscal_year:' . ltrim($year, 'FY ');
            }
            elseif (startsWith($year, 'C')) {
                $query .= '&fq=calendar_fiscal_year:' . ltrim($year, 'CY ');
            }
        }
        $matches = _checkbook_autocomplete_solr_results($query, 'civil_service_title');
        drupal_json_output($matches);
    }
}

// Budget autocomplete menu callback functions:
function _checkbook_autocomplete_budget_department($agency, $budgetcode, $expcategory, $fiscal_year, $fundingsrc, $revcat, $revsrc, $type) {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        $query = 'select/?q=department_name_code:' . $search_term . '&facet=true&facet.field=department_name_code&wt=php';
        if ($agency) {
            $query .= '&fq=agency_code:' . $agency;
        }
        if ($budgetcode) {
            $query .= '&fq=budget_code:' . $budgetcode;
        }
        if ($expcategory) {
            $query .= '&fq=object_class_code:' . $expcategory;
        }
        if ($fiscal_year) {
            $query .= '&fq=fiscal_year:' . $fiscal_year;
        }
        if ($fundingsrc) {
            $query .= '&fq=funding_class_code:' . $fundingsrc;
        }
        if ($revcat) {
            $query .= '&fq=revenue_category_code:' . $revcat;
        }
        if ($revsrc) {
            $query .= '&fq=revenue_source_code:' . $revsrc;
        }
        if ($type) {
            if ($type == 'expense') {
                $query .= '&fq=domain:expense\%20budget';
            }
            elseif ($type == 'revenue') {
                $query .= '&fq=domain:revenue\%20budget';
            }
        }
        $results = _checkbook_autocomplete_solr_results($query);
        if ($results['response']['numFound'] != 0) {
            $facetcounts = $results['facet_counts']['facet_fields']['department_name_code'];
            $matches = array();
            foreach ($facetcounts as $key => $value) {
                if ($value > 0) {
                    $matches[] = $key;
                }
            }
            drupal_json_output($matches);
        }
        else {
            drupal_json_output(array(
                array(
                    'label' => 'No Matches Found',
                    'value' => '',
                ),
            ));
        }
    }
}

function _checkbook_autocomplete_budget_expcategory($agency, $budgetcode, $dept, $fiscal_year, $fundingsrc, $revcat, $revsrc, $type) {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        $query = 'select/?q=object_class_name_code:' . $search_term . '&facet=true&facet.field=object_class_name_code&facet.limit=10&wt=php';
        if ($agency) {
            $query .= '&fq=agency_code:' . $agency;
        }
        if ($budgetcode) {
            $query .= '&fq=budget_code:' . $budgetcode;
        }
        if ($dept) {
            $query .= '&fq=department_code:' . $dept;
        }
        if ($fiscal_year) {
            $query .= '&fq=fiscal_year:' . $fiscal_year;
        }
        if ($fundingsrc) {
            $query .= '&fq=funding_class_code:' . $fundingsrc;
        }
        if ($revcat) {
            $query .= '&fq=revenue_category_code:' . $revcat;
        }
        if ($revsrc) {
            $query .= '&fq=revenue_source_code:' . $revsrc;
        }
        if ($type) {
            if ($type == 'expense') {
                $query .= '&fq=domain:expense\%20budget';
            }
            elseif ($type == 'revenue') {
                $query .= '&fq=domain:revenue\%20budget';
            }
        }
        $results = _checkbook_autocomplete_solr_results($query);
        if ($results['response']['numFound'] != 0) {
            $facetcounts = $results['facet_counts']['facet_fields']['object_class_name_code'];
            $matches = array();
            foreach ($facetcounts as $key => $value) {
                if ($value > 0) {
                    $matches[] = $key;
                }
            }
            drupal_json_output($matches);
        }
        else {
            drupal_json_output(array(
                array(
                    'label' => 'No Matches Found',
                    'value' => '',
                ),
            ));
        }
    }
}

function _checkbook_autocomplete_budget_budgetcode($agency , $dept , $expcategory, $budgetcode , $year) {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        $query = 'select?q=budget_code_name_code_autocomplete:' . $search_term . '&facet=true&facet.field=budget_code_name_code&fq=domain:budget&facet.limit=10&wt=php';
        if ($agency) {
            $query .= '&fq=agency_code:' . $agency;
        }
        if ($dept) {
            $query .= '&fq=department_code:' . _emptyToZero($dept);
        }
        if ($expcategory) {
            $query .= '&fq=expenditure_object_code:' . _emptyToZero($expcategory);
        }
        if ($year) {
            $query .= '&fq=fiscal_year:' . $year;
        }
    }
    $matches = _checkbook_autocomplete_solr_results($query, 'budget_code_name_code');
    drupal_json_output($matches);
}

function _emptyToZero($input) {
    $p = "/.*?(\\[.*?\\])/is";
    $matches = array();
    if ($input) {
        $inputval = preg_match($p, $input, $matches);
        $output = trim($matches[1], '[ ]');
    }
    else {
        $output = 0;
    }
    return $output;
}

function _checkbook_autocomplete_budget_fundingsrc($agency, $budgetcode, $dept, $expcategory, $fiscal_year, $revcat, $revsrc, $type) {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        $query = 'select/?q=funding_class_name_code:' . $search_term . '&facet=true&facet.field=funding_class_name_code&facet.limit=10&wt=php';
        if ($agency) {
            $query .= '&fq=agency_code:' . $agency;
        }
        if ($budgetcode) {
            $query .= '&fq=budget_code:' . $budgetcode;
        }
        if ($dept) {
            $query .= '&fq=department_code:' . $dept;
        }
        if ($expcategory) {
            $query .= '&fq=object_class_code:' . $expcategory;
        }
        if ($fiscal_year) {
            $query .= '&fq=fiscal_year:' . $fiscal_year;
        }
        if ($revcat) {
            $query .= '&fq=revenue_category_code:' . $revcat;
        }
        if ($revsrc) {
            $query .= '&fq=revenue_source_code:' . $revsrc;
        }
        if ($type) {
            if ($type == 'expense') {
                $query .= '&fq=domain:expense\%20budget';
            }
            elseif ($type == 'revenue') {
                $query .= '&fq=domain:revenue\%20budget';
            }
        }
        $results = _checkbook_autocomplete_solr_results($query);
        if ($results['response']['numFound'] != 0) {
            $facetcounts = $results['facet_counts']['facet_fields']['funding_class_name_code'];
            $matches = array();
            foreach ($facetcounts as $key => $value) {
                if ($value > 0) {
                    $matches[] = $key;
                }
            }
            drupal_json_output($matches);
        }
        else {
            drupal_json_output(array(
                array(
                    'label' => 'No Matches Found',
                    'value' => ''
                )
            ));
        }
    }
}

function _checkbook_autocomplete_budget_revcat($agency, $budgetcode, $dept, $expcategory, $fiscal_year, $fundingsrc, $revsrc, $type) {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        $query = 'select/?q=revenue_category_name_code:' . $search_term . '&facet=true&facet.field=revenue_category_name_code&facet.limit=10&wt=php';
        if ($agency) {
            $query .= '&fq=agency_code:' . $agency;
        }
        if ($budgetcode) {
            $query .= '&fq=budget_code:' . $budgetcode;
        }
        if ($dept) {
            $query .= '&fq=department_code:' . $dept;
        }
        if ($expcategory) {
            $query .= '&fq=object_class_code:' . $expcategory;
        }
        if ($fiscal_year) {
            $query .= '&fq=fiscal_year:' . $fiscal_year;
        }
        if ($fundingsrc) {
            $query .= '&fq=funding_class_code:' . $fundingsrc;
        }
        if ($revsrc) {
            $query .= '&fq=revenue_source_code:' . $revsrc;
        }
        if ($type) {
            if ($type == 'expense') {
                $query .= '&fq=domain:expense\%20budget';
            }
            elseif ($type == 'revenue') {
                $query .= '&fq=domain:revenue\%20budget';
            }
        }
        $results = _checkbook_autocomplete_solr_results($query);
        if ($results['response']['numFound'] != 0) {
            $facetcounts = $results['facet_counts']['facet_fields']['revenue_category_name_code'];
            $matches = array();
            foreach ($facetcounts as $key => $value) {
                if ($value > 0) {
                    $matches[] = $key;
                }
            }
            drupal_json_output($matches);
        }
        else {
            drupal_json_output(array(
                array(
                    'label' => 'No Matches Found',
                    'value' => ''
                )
            ));
        }
    }
}

function _checkbook_autocomplete_budget_revsrc($agency, $budgetcode, $dept, $expcategory, $fiscal_year, $fundingsrc, $revcat, $type) {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        $query = 'select/?q=revenue_source_name_code:' . $search_term . '&facet=true&facet.field=revenue_source_name_code&facet.limit=10&wt=php';
        if ($agency) {
            $query .= '&fq=agency_code:' . $agency;
        }
        if ($budgetcode) {
            $query .= '&fq=budget_code:' . $budgetcode;
        }
        if ($dept) {
            $query .= '&fq=department_code:' . $dept;
        }
        if ($expcategory) {
            $query .= '&fq=object_class_code:' . $expcategory;
        }
        if ($fiscal_year) {
            $query .= '&fq=fiscal_year:' . $fiscal_year;
        }
        if ($fundingsrc) {
            $query .= '&fq=funding_class_code:' . $fundingsrc;
        }
        if ($revcat) {
            $query .= '&fq=revenue_category_code:' . $revcat;
        }
        if ($type) {
            if ($type == 'expense') {
                $query .= '&fq=domain:expense\%20budget';
            }
            elseif ($type == 'revenue') {
                $query .= '&fq=domain:revenue\%20budget';
            }
        }
        $results = _checkbook_autocomplete_solr_results($query);
        if ($results['response']['numFound'] != 0) {
            $facetcounts = $results['facet_counts']['facet_fields']['revenue_source_name_code'];
            $matches = array();
            foreach ($facetcounts as $key => $value) {
                if ($value > 0) {
                    $matches[] = (string) $key;
                }
            }
            drupal_json_output($matches);
        }
        else {
            drupal_json_output(array(
                array(
                    'label' => 'No Matches Found',
                    'value' => ''
                )
            ));
        }
    }
}

//Revenue autocomplete menu callback functions
function _checkbook_autocomplete_revenue_revenueclass($year, $fundclass, $agency, $budgetyear, $revcat, $revsrc, $fundingsrc) {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        $query = 'select/?q=revenue_class_name_code_autocomplete:' . $search_term . '&facet=true&facet.field=revenue_class_name_code&fq=domain:revenue&facet.limit=10&wt=php';
        if ($year) {
            $query .= '&fq=display_fiscal_year:' . $year;
        }
        if ($fundclass) {
            $query .= '&fq=fund_class_code:' . $fundclass;
        }
        if ($agency) {
            $query .= '&fq=agency_code:' . $agency;
        }
        if ($budgetyear) {
            $query .= '&fq=fiscal_year:' . $budgetyear;
        }
        if ($revcat) {
            $query .= '&fq=revenue_category_code:' . $revcat;
        }
        if ($fundingsrc) {
            $query .= '&fq=funding_class_code:' . $fundingsrc;
        }
        $matches = _checkbook_autocomplete_solr_results($query, 'revenue_class_name_code');
        drupal_json_output($matches);
    }
}

function _checkbook_autocomplete_revenue_revenuesource($year, $fundclass, $agency, $budgetyear, $revcat, $revclass, $fundingsrc) {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        $query = 'select/?q=revenue_source_name_code_autocomplete:' . $search_term . '&facet=true&facet.field=revenue_source_name_code&fq=domain:revenue&facet.limit=10&wt=php';
        if ($year) {
            $query .= '&fq=display_fiscal_year:' . $year;
        }
        if ($fundclass) {
            $query .= '&fq=fund_class_code:' . $fundclass;
        }
        if ($agency) {
            $query .= '&fq=agency_code:' . $agency;
        }
        if ($budgetyear) {
            $query .= '&fq=fiscal_year:' . $budgetyear;
        }
        if ($revcat) {
            $query .= '&fq=revenue_category_code:' . $revcat;
        }
        if ($fundingsrc) {
            $query .= '&fq=funding_class_code:' . $fundingsrc;
        }
        $matches = _checkbook_autocomplete_solr_results($query, 'revenue_source_name_code');
        drupal_json_output($matches);
    }
}

/**
 * Returns date query based on the year value and type of year (fiscal or calendar) chosen on contracts checkbook advanced search form.
 *
 * @param string $year
 *   Format: type_of_year~year_id, eg: "FY~112" or "FY2012"
 * @param string|null $domain
 *   Domain
 * @param string|null $status
 *   Status
 *
 * @return string
 *   array('start_date' => $start_date, 'end_date' => $end_date)
 */
function _checkbook_autocomplete_get_start_end_dates($year, $domain = NULL, $status = NULL) {

    if (strpos($year, '~')) {
        if ($year == 'fy~all') {
            return '';
        }
        $year_type = substr($year, 0, 2);
        $year_id = substr($year, 3, strlen($year));
        $year_value = _getYearValueFromID($year_id);
    }
    else {
        if ($year == 'ALL') {
            return '';
        }
        $year_type = substr($year, 0, 2);
        $year_value = str_replace($year_type, '', $year);
        $year_id = _getYearIDFromValue($year_value);
    }
    $query = "";
    if ($domain == 'spending') {
        if (strtolower($year_type) == 'fy') {
            $query .= 'fiscal_year_id:'. $year_id;
        }
        elseif (strtolower($year_type) == 'cy') {
            $query .= 'calendar_fiscal_year_id:'. $year_id;
        }
    }
    else {
        if ($status == 'A') {
            if (strtolower($year_type) == 'fy') {
                $query .= 'fiscal_year_id:'. $year_id;
            }
            elseif (strtolower($year_type) == 'cy') {
                $start_date = ($year_value) . "-12-31T23:59:59.999Z";
                $end_date = ($year_value) . "-01-01T00:00:00.999Z";
                $query .= '(start_date:[*%20TO%20' . $start_date . ']%20AND%20end_date:[' . $end_date . '%20TO%20*])';
            }
        }
        else {
            if ($status == 'R') {
                if (strtolower($year_type) == 'fy') {
                    $query .= 'registered_fiscal_year_id:'. $year_id;
                }
                elseif (strtolower($year_type) == 'cy') {
                    $start_date = ($year_value) . "-01-01T00:00:00.999Z";
                    $end_date = ($year_value) . "-12-31T23:59:59.999Z";
                    $query .= '(registration_date:[' . $start_date . '%20TO%20' . $end_date . '])';
                }
            }
        }
    }
    $query = '&fq=(' . $query . ')';
    return $query;
}

/**
 * Replaces and encodes the special characters in the given string.
 *
 * @param string $string
 *   String that needs escaping
 *
 * @return string
 *   Escaped string
 */
function _checkbook_autocomplete_escapeSolrValue($string) {
    $string_start = $string;

    $string = str_replace("%", "%25", $string);
    $string = str_replace('\\', "%5C", $string);
    $string = str_replace(" ", "%20", $string);
    $string = str_replace("#", "%23", $string);
    $string = str_replace("/", "%2F", $string);
    $string = str_replace("&", "%26", $string);
    $string = str_replace("[", "\\[", $string);
    $string = str_replace("]", "\\]", $string);
    $string = str_replace(":", "\\:", $string);
    $string = str_replace("'", "%27", $string);
    $string = str_replace("+", "%2B", $string);
    $string = str_replace("?", "%3F", $string);
    $string = str_replace("$", "%24", $string);
    $string = str_replace("!", "%21", $string);
    $string = str_replace("^", "%5E", $string);
    $string = str_replace("|", "%7C", $string);
    $string = str_replace("(", "%28", $string);
    $string = str_replace(")", "%29", $string);
    $string = str_replace("*", "%2A", $string);
    $string = str_replace(".", "%2E", $string);
    $string = str_replace("-", "-", $string);

    LogHelper::log_notice("Solr: '$string_start' => '$string' ");

    return $string;
}

/**
 * Get search term from $_REQUEST.
 *
 * @return string
 *   Search term
 */
function _get_autocomplete_search_term() {
    $term = trim($_REQUEST['term']);
    if (strlen($term) > 0) {
        $autocomplete_text = trim(htmlspecialchars_decode($term, ENT_QUOTES));
        $autocomplete_terms = explode(' ', $autocomplete_text);
        $autocomplete_text = _checkbook_autocomplete_escapeSolrValue(trim(htmlspecialchars_decode($term), ENT_QUOTES));

        if (count($autocomplete_terms) > 0) {
            $autocomplete_text = "";
            foreach ($autocomplete_terms as $key => $value) {
                if (strlen($value) > 0) {
                    $autocomplete_text .= _checkbook_autocomplete_escapeSolrValue($value) . "*%20AND%20";
                }
            }
            $search_term = "(" . substr($autocomplete_text, 0, -9) . ")";
        }
        else {
            $search_term = $autocomplete_text . "*";
        }

        return $search_term;
    }
    else {
        return '';
    }
}

/**
 * Get search term from $_REQUEST and prepares it as facet prefix
 *
 * @return string
 *   Search term
 */
function _get_autocomplete_search_term_facet_prefix() {
    $term = trim($_REQUEST['term']);
    if (strlen($term) > 0) {
        $autocomplete_text = trim(htmlspecialchars_decode($term, ENT_QUOTES));
        $autocomplete_terms = explode(' ', $autocomplete_text);
        $autocomplete_text = _checkbook_autocomplete_escapeSolrValue(trim(htmlspecialchars_decode($term), ENT_QUOTES));

        if (count($autocomplete_terms) > 0) {
            $autocomplete_text = "";
            foreach ($autocomplete_terms as $key => $value) {
                if (strlen($value) > 0) {
                    $autocomplete_text .= _checkbook_autocomplete_escapeSolrValue($value) . "%20AND%20";
                }
            }
            $search_term =substr($autocomplete_text, 0, -9);
        }
        else {
            $search_term = $autocomplete_text;
        }

        return $search_term;
    }
    else {
        return '';
    }
}

/**
 * Outputs the solr query result for the input query.
 *
 * @param string $query
 *   Partially constructed query.
 * @param string|null $facet
 *   Facet name to get from results.
 *
 * @return array
 *   Facet results from Solr.
 */
function _checkbook_autocomplete_solr_results($query, $facet = NULL) {
    global $conf;
    $results = array();
    if ($conf['check_book']['solr']['url']) {
        $solr_server_url = $conf['check_book']['solr']['url'];
    }
    $url = $solr_server_url . $query;
    $contents = file_get_contents($url);
    $contents = utf8_encode("$contents");
    if ($contents) {
        eval("\$results = " . $contents . ";");
    }
    if ($results['response']['numFound'] != 0) {
        $facetcounts = $results['facet_counts']['facet_fields'][$facet];
        $matches = array();
        if($facetcounts)
            foreach ($facetcounts as $key => $value) {
                if ($value > 0) {
                    $key = (string) $key;
                    $matches[] = $key;
                }
            }
        else {
            $matches = array(array('label' => 'No Matches Found', 'value' => ''));
        }
    }
    else {
        $matches = array(array('label' => 'No Matches Found', 'value' => ''));
    }

    return $matches;
}

/**
 * Get a string enclosed in brackets from another string.
 *
 * @param string $string
 *   String with another string in brackets
 *
 * @return string
 *   Text in brackets
 */
function _getCodeFromName($string) {
    $index1 = strrpos($string, '[');
    $code = substr($string, $index1 + 1, strlen($string) - ($index1 + 2));
    return $code;
}

/**
 * Get Expense Type id from Data Controller and format into a FAPI select input options array.
 *
 * @param $exptype
 * @param $data_source
 * @return array
 *   Spending category id from name
 */
function _get_spending_datafeeds_autocomplete_expense_type_options($exptype, $data_source = 'checkbook') {
    try {
        $dataController = data_controller_get_instance();
        switch($data_source)
        {
            case 'checkbook_oge':
                $results = $dataController->queryDataset('checkbook_oge:spending_category', array('spending_category_id'), array('spending_category_code' => $exptype));
                break;

            default:
                $results = $dataController->queryDataset('checkbook:spending_category', array('spending_category_id'), array('spending_category_code' => $exptype));
                break;
        }
        if($results && $results[0]['spending_category_id']) return $results[0]['spending_category_id'];
    }
    catch (Exception $e) {
        log_error("Error getting data from controller: \n" . $e->getMessage());
    }
    return null;
};


/**
 * Get Contract Type from Data Controller and format into a FAPI select input #options array.
 *
 * @param $agency_code
 * @param $data_source
 * @return array
 *   Agency Ids
 */
function _get_contracts_datafeeds_autocomplete_agency_id($agency_code, $data_source = 'checkbook') {
    try {

        $parameters = $data_source == 'checkbook_oge' ? array('agency_code' => $agency_code,"is_oge_agency" => "Y") : array('agency_code' => $agency_code);
        $dataController = data_controller_get_instance();
        $results = $dataController->queryDataset($data_source . ':agency', array('agency_id'), $parameters);

        if($results && $results[0]['agency_id']) return $results[0]['agency_id'];
    }
    catch (Exception $e) {
        log_error("Error getting data from controller: \n" . $e->getMessage());
    }
    return null;
}

/**
 * Get Contract Type from Data Controller and format into a FAPI select input #options array.
 *
 * @param $contract_type
 * Agreement type codes and agreement type names
 * @return array
 */
function _get_contracts_datafeeds_autocomplete_contract_type_options($contract_type) {
    try {
        $dataController = data_controller_get_instance();
        $results = $dataController->queryDataset('checkbook:agreement_type', array('agreement_type_id'), array('agreement_type_code' => $contract_type));
        if($results && $results[0]['agreement_type_id']) return $results[0]['agreement_type_id'];
    }
    catch (Exception $e) {
        log_error("Error getting data from controller: \n" . $e->getMessage());
    }
    return null;
}

/**
 * Get Award Method from Data Controller and format into a FAPI select input #options array.
 *
 * @param $award_method_code
 * Award method codes and award method names
 * @return array
 */
function _get_contracts_datafeeds_autocomplete_award_method_options($award_method_code) {
    try {
        $dataController = data_controller_get_instance();
        $results = $dataController->queryDataset('checkbook:award_method', array('award_method_id'), array('award_method_code' => $award_method_code));
        if($results && $results[0]['award_method_id']) return $results[0]['award_method_id'];
    }
    catch (Exception $e) {
        log_error("Error getting data from controller: \n" . $e->getMessage());
    }
    return null;
}

/**
 * Used for the M/WBE Category drop down in the advanced search this may have a list
 *
 * For example $minority_types = 1~2~3, return (1 OR 2 OR 3)
 * @param $minority_types
 * @return string
 *
 */
function _checkbook_autocomplete_get_mwbe_category($minority_types){
    return '('.implode('%20OR%20',explode('~',$minority_types)).')';
}
