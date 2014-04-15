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
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 *
 * @return array
 *   Results from Solr
 */
function _get_contracts_datafeeds_autocomplete_results($facet, $query, $status = NULL, $category = NULL, $contract_type = NULL, $agency = NULL, $award_method = NULL, $year = NULL, $data_source = 'checkbook') {
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
        $query .= '&fq=contract_category_name:' . ucfirst($category);
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
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_contracts_vendor($status, $category, $contract_type, $agency, $award_method, $year, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        switch($data_source)
        {
            case 'checkbook':
                $query = 'select/?q=vendor_name_code_autocomplete:' . $search_term . '&facet=true&facet.field=vendor_name_code&fq=domain:contracts&wt=php&facet.limit=10';
                $matches = _get_contracts_datafeeds_autocomplete_results('vendor_name_code', $query, $status, $category, $contract_type, $agency, $award_method, $year, $data_source);
                break;
            case 'checkbook_oge':
                $query = 'select/?q=vendor_name_autocomplete:' . $search_term . '&facet=true&facet.field=vendor_name&fq=domain:contracts&wt=php&facet.limit=10';
                $query .= '&fq=agency_type:oge';
                $matches = _get_contracts_datafeeds_autocomplete_results('vendor_name', $query, $status, $category, $contract_type, $agency, $award_method, $year, $data_source);
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
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_contracts_contract_number($status, $category, $contract_type, $agency, $award_method, $year, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        $query = 'select/?q=contract_number:' . $search_term . '&facet=true&facet.field=contract_number&fq=domain:contracts&wt=php&facet.limit=10';
        if($data_source == 'checkbook_oge') $query .= '&fq=agency_type:oge';
        $matches = _get_contracts_datafeeds_autocomplete_results('contract_number', $query, $status, $category, $contract_type, $agency, $award_method, $year, $data_source);
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
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_contracts_apt_pin($status, $category, $contract_type, $agency, $award_method, $year, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        $query = 'select/?q=apt_pin:' . $search_term . '&facet=true&facet.field=apt_pin&fq=domain:contracts&wt=php&facet.limit=10';
        if($data_source == 'checkbook_oge') $query .= '&fq=agency_type:oge';
        $matches = _get_contracts_datafeeds_autocomplete_results('apt_pin', $query, $status, $category, $contract_type, $agency, $award_method, $year, $data_source);
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
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_contracts_pin($status, $category, $contract_type, $agency, $award_method, $year, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        $query = 'select/?q=pin:' . $search_term . '&facet=true&facet.field=pin&fq=domain:contracts&wt=php&facet.limit=10';
        if($data_source == 'checkbook_oge') $query .= '&fq=agency_type:oge';
        $matches = _get_contracts_datafeeds_autocomplete_results('pin', $query, $status, $category, $contract_type, $agency, $award_method, $year, $data_source);
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
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_contracts_entitycontractnum($status, $category, $contract_type, $agency, $award_method, $year, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        $query = 'select/?q=contract_entity_contract_number:' . $search_term . '&facet=true&facet.field=contract_entity_contract_number&fq=domain:contracts&facet.limit=10&wt=php';
        if($data_source == 'checkbook_oge') $query .= '&fq=agency_type:oge';
        $matches = _get_contracts_datafeeds_autocomplete_results('contract_entity_contract_number', $query, $status, $category, $contract_type, $agency, $award_method, $year, $data_source);
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
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_contracts_commodityline($status, $category, $contract_type, $agency, $award_method, $year, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        $query = 'select/?q=contract_commodity_line:' . $search_term . '&facet=true&facet.field=contract_commodity_line&fq=domain:contracts&facet.limit=10&wt=php';
        if($data_source == 'checkbook_oge') $query .= '&fq=agency_type:oge';
        $matches = _get_contracts_datafeeds_autocomplete_results('contract_commodity_line', $query, $status, $category, $contract_type, $agency, $award_method, $year, $data_source);
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
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_contracts_budgetname($status, $category, $contract_type, $agency, $award_method, $year, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        $query = 'select/?q=contract_budget_name_autocomplete:' . $search_term . '&facet=true&facet.field=contract_budget_name&fq=domain:contracts&facet.limit=10&wt=php';
        if($data_source == 'checkbook_oge') $query .= '&fq=agency_type:oge';
        $matches = _get_contracts_datafeeds_autocomplete_results('contract_budget_name', $query, $status, $category, $contract_type, $agency, $award_method, $year, $data_source);
        drupal_json_output($matches);
    }
}

// Spending autocomplete menu callback functions:
/**
 * Construct query string and get results from Solr.
 *
 * @param string $facet
 *   Facet results needed.
 * @param string $query
 *   Partially constructed query string
 * @param string|null $year
 *   Year
 * @param string|null $agency
 *   Agency code
 * @param string|null $expcategory
 *   Expenditure object code
 * @param string|null $dept
 *   Department code
 * @param string|null $exptype
 *   Spending category code
 *
 * @return array
 *   Results from Solr
 */
function _get_spending_datafeeds_autocomplete_results($facet, $query, $year = NULL, $agency = NULL, $expcategory = NULL, $dept = NULL, $exptype = NULL, $data_source = 'checkbook') {
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
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_spending_payee($year, $agency, $expcategory, $dept, $exptype = 0, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        switch($data_source)
        {
            case 'checkbook':
                $query = 'select/?q=vendor_name_code_autocomplete:' . $search_term . '&facet=true&facet.field=vendor_name_code&fq=domain:spending&wt=php&facet.limit=10';
                $matches = _get_spending_datafeeds_autocomplete_results('vendor_name_code', $query, $year, $agency, $expcategory, $dept, $exptype);
                break;
            case 'checkbook_oge':
                $query = 'select/?q=vendor_name_autocomplete:' . $search_term . '&facet=true&facet.field=vendor_name&fq=domain:spending&wt=php&facet.limit=10';
                $query .= '&fq=agency_type:oge';
                $matches = _get_spending_datafeeds_autocomplete_results('vendor_name', $query, $year, $agency, $expcategory, $dept, $exptype);
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
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_spending_contractno($year, $agency, $expcategory, $dept, $exptype = 0, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        $query = 'select/?q=contract_number:' . $search_term . '&facet=true&facet.field=contract_number&fq=domain:spending&facet.limit=10&wt=php';
        if($data_source == 'checkbook_oge') $query .= '&fq=agency_type:oge';
        $matches = _get_spending_datafeeds_autocomplete_results('contract_number', $query, $year, $agency, $expcategory, $dept, $exptype);
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
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_spending_capitalproject($year, $agency, $expcategory, $dept, $exptype = 0, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        $query = 'select/?q=reporting_code:' . $search_term . '&facet=true&facet.field=reporting_code&fq=domain:spending&facet.limit=10&wt=php';
        if($data_source == 'checkbook_oge') $query .= '&fq=agency_type:oge';
        $matches = _get_spending_datafeeds_autocomplete_results('reporting_code', $query, $year, $agency, $expcategory, $dept, $exptype);
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
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_spending_documentid($year, $agency, $expcategory, $dept, $exptype = 0, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        $query = 'select/?q=expense_id:' . $search_term . '&facet=true&facet.field=expense_id&fq=domain:spending&facet.limit=10&wt=php';
        if($data_source == 'checkbook_oge') $query .= '&fq=agency_type:oge';
        $matches = _get_spending_datafeeds_autocomplete_results('expense_id', $query, $year, $agency, $expcategory, $dept, $exptype);
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
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_spending_entitycontractnum($year, $agency, $expcategory, $dept, $exptype = 0, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        $query = 'select/?q=spending_entity_contract_number:' . $search_term . '&facet=true&facet.field=spending_entity_contract_number&fq=domain:spending&facet.limit=10&wt=php';
        if($data_source == 'checkbook_oge') $query .= '&fq=agency_type:oge';
        $matches = _get_spending_datafeeds_autocomplete_results('spending_entity_contract_number', $query, $year, $agency, $expcategory, $dept, $exptype, $data_source);
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
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_spending_commodityline($year, $agency, $expcategory, $dept, $exptype = 0, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        $query = 'select/?q=spending_commodity_line:' . $search_term . '&facet=true&facet.field=spending_commodity_line&fq=domain:spending&facet.limit=10&wt=php';
        if($data_source == 'checkbook_oge') $query .= '&fq=agency_type:oge';
        $matches = _get_spending_datafeeds_autocomplete_results('spending_commodity_line', $query, $year, $agency, $expcategory, $dept, $exptype, $data_source);
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
 * @param string $data_source
 *   Data source (checkbook, checkbook_oge)
 */
function _checkbook_autocomplete_spending_budgetname($year, $agency, $expcategory, $dept, $exptype = 0, $data_source = 'checkbook') {
    $search_term = _get_autocomplete_search_term();
    if (!empty($search_term)) {
        $query = 'select/?q=spending_budget_name_autocomplete:' . $search_term . '&facet=true&facet.field=spending_budget_name&fq=domain:spending&facet.limit=10&wt=php';
        if($data_source == 'checkbook_oge') $query .= '&fq=agency_type:oge';
        $matches = _get_spending_datafeeds_autocomplete_results('spending_budget_name', $query, $year, $agency, $expcategory, $dept, $exptype, $data_source);
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
            $query .= '&fq=department_code:' . $dept;
        }
        if ($expcategory) {
            $query .= '&fq=expenditure_object_code:' . $expcategory;
        }
        if ($year) {
            $query .= '&fq=fiscal_year:' . $year;
        }
    }
    $matches = _checkbook_autocomplete_solr_results($query, 'budget_code_name_code');
    drupal_json_output($matches);
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
 * @return array
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
    }

    $query = "";
    if (strtolower($year_type) == 'fy') {
        $start_date = ($year_value - 1) . "-07-01T00:00:00.999Z";
        $end_date = $year_value . "-06-30T23:59:59.999Z";
    }
    elseif (strtolower($year_type) == 'cy') {
        $start_date = ($year_value) . "-01-01T00:00:00.999Z";
        $end_date = $year_value . "-12-31T23:59:59.999Z";
    }

    if ($domain == 'spending') {
        $query .= '(check_eft_issued_date:[' . $start_date . '%20TO%20' . $end_date . '])';
    }
    else {
        if ($status == 'A') {
            $query .= '(start_date:[*%20TO%20' . $start_date . ']%20AND%20end_date:[' . $end_date . '%20TO%20*])OR';
            $query .= '(start_date:[*%20TO%20' . $end_date . ']%20AND%20end_date[:' . $end_date . '%20TO%20*])OR';
            $query .= '(start_date:[' . $end_date . '%20TO%20*]%20AND%20start_date:[*%20TO%20' . $end_date . '])';
        }
        else {
            if ($status == 'R') {
                $query .= '(registration_date:[' . $start_date . '%20TO%20' . $end_date . '])';
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

    $string = str_replace("%", "%25", $string);
    $string = str_replace("\\", "%5C%5C", $string);
    $string = str_replace(" ", "%20", $string);
    $string = str_replace("#", "%23", $string);
    $string = str_replace("/", "\%2F", $string);
    $string = str_replace("&", "%26", $string);
    $string = str_replace("[", "\[", $string);
    $string = str_replace("]", "\]", $string);
    $string = str_replace(":", "\:", $string);
    $string = str_replace("'", "%27", $string);
    $string = str_replace("+", "\%2B", $string);
    $string = str_replace("?", "\%3F", $string);
    $string = str_replace("$", "\%24", $string);
    $string = str_replace("!", "\%21", $string);
    $string = str_replace("^", "\%5E", $string);
    $string = str_replace("|", "\%7C", $string);
    $string = str_replace("(", "\%28", $string);
    $string = str_replace(")", "\%29", $string);
    $string = str_replace("*", "\%2A", $string);
    $string = str_replace(".", "\%2E", $string);
    $string = str_replace("-", "\-", $string);

    return $string;
}

/**
 * Get search term from $_REQUEST.
 *
 * @return string
 *   Search term
 */
function _get_autocomplete_search_term() {
    if (strlen($_REQUEST['term']) > 0) {
        $autocomplete_text = trim(htmlspecialchars_decode($_REQUEST['term'], ENT_QUOTES));
        $autocomplete_terms = explode(' ', $autocomplete_text);
        $autocomplete_text = _checkbook_autocomplete_escapeSolrValue(trim(htmlspecialchars_decode($_REQUEST['term']), ENT_QUOTES));

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
        foreach ($facetcounts as $key => $value) {
            if ($value > 0) {
                $key = (string) $key;
                $matches[] = $key;
            }
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
 * @return array
 *   Agreement type codes and agreement type names
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
 * @return array
 *   Award method codes and award method names
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