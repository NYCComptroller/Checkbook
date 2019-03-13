<?php
/*
 * Advanced search submit form handler, used to figure which submit button
 * has been clicked and invoke the respective functions that generate the
 * redirect URL with the necessary parameters
 */
function checkbook_advanced_search_form_submit($form, &$form_state) {

    switch ($form_state['clicked_button']['#name']) {
        case "budget_submit":
            $form_state['redirect'] = _checkbook_advanced_search_budget_submit($form, $form_state);
            break;
        case "revenue_submit":
            $form_state['redirect'] = _checkbook_advanced_search_revenue_submit($form, $form_state);
            break;
        case "spending_submit":
            $data_source = $form_state['values']['spending_advanced_search_domain_filter'];
            $form_state['redirect'] = _checkbook_advanced_search_spending_submit($form, $form_state, $data_source);
            break;
        case "contracts_submit":
            $data_source = $form_state['values']['contracts_advanced_search_domain_filter'];
            $form_state['redirect'] = _checkbook_advanced_search_contracts_submit($form, $form_state, $data_source);
            break;
        case "payroll_submit":
            $form_state['redirect'] = _checkbook_advanced_search_payroll_submit($form, $form_state);
            break;
        case "budget_next":
            return _checkbook_advanced_search_budget_submit($form, $form_state,'next');
            break;
        case "revenue_next":
            return _checkbook_advanced_search_revenue_submit($form, $form_state, 'next');
            break;
        case "spending_next":
            $data_source = $form_state['values']['spending_advanced_search_domain_filter'];
            return _checkbook_advanced_search_spending_submit($form, $form_state, $data_source, 'next');
            break;
        case "contracts_next":
            $data_source = $form_state['values']['contracts_advanced_search_domain_filter'];
            return _checkbook_advanced_search_contracts_submit($form, $form_state, $data_source, 'next');
            break;
        case "payroll_next":
            return _checkbook_advanced_search_payroll_submit($form, $form_state, 'next');
            break;
    }
}


/*
 * Get year_value and id using the data controller
 * To populate Year Filter drop downs on Advanced Search form
 * @return array
 */
function _checkbook_advanced_search_get_year($domain = null, $form = null, $data_source = 'checkbook')
{
    try {
        $dataController = data_controller_get_instance();
        $data = $dataController->queryDataset($data_source.":year", array('year_value', 'year_id'), array('year_value' => data_controller_get_operator_factory_instance()->initiateHandler(GreaterOrEqualOperatorHandler::$OPERATOR__NAME, 2010)), 'year_value', 0);

        $current_fy_id = _getFiscalYearID();
        $max_years = _checkbook_max_data_year();
        $fiscal_year_array = [];
        foreach ($data as $row) {
            if ($domain != 'revenue' && $domain != 'budget') {
                if ($row['year_value'] <= $max_years['year_value'] && $row['year_value'] != 2010) {
                    $fiscal_year_array['fy~' . $row['year_id']] = 'FY ' . $row['year_value'];
                }
                if ($row['year_value'] <= $max_years['cal_year_value']) {
                    $calendar_year_array['cy~' . $row['year_id']] = 'CY ' . $row['year_value'];
                }
            } else {
                if ($row['year_value'] <= $max_years['year_value'] && $row['year_value'] != 2010) {
                    if ($form == 'datafeeds')
                        $fiscal_year_array[$row['year_value']] = $row['year_value'];
                    else
                        $fiscal_year_array[$row['year_id']] = $row['year_value'];
                }
            }
        }

        $fiscal_year_array && arsort($fiscal_year_array);
        $calendar_year_array && arsort($calendar_year_array);

        if ($domain == 'payroll' && $calendar_year_array) {
            $fiscal_year_array = array_merge($calendar_year_array, $fiscal_year_array);
        }

        if ($domain == 'contracts' || $domain == 'spending') {
            $fiscal_year_array = array_reverse($fiscal_year_array, true);
            $fiscal_year_array['fy~all'] = 'All Years';
            $fiscal_year_array = array_reverse($fiscal_year_array, true);
        }

        return $fiscal_year_array;

    } catch (Exception $e) {
        log_error("Error getting data from controller: \n" . $e->getMessage());
    }
}

/*
 * Get agency name and id using the data controller
 * @return array
 */
function _checkbook_advanced_search_get_agencyname_and_id($data_source = 'checkbook')
{
    try {
        switch ($data_source) {
            case 'checkbook_oge':
                $parameters = array("is_oge_agency" => "Y", "is_display" => "Y");
                break;
            case 'checkbook_nycha':
                $parameters = array("is_display" => "Y");
                break;
            default:
                $title = 'Citywide(All Agencies)';
                $parameters = array("is_display" => "Y");
                break;
        }
        $dataController = data_controller_get_instance();
        $nyc_agencies = $dataController->queryDataset($data_source.':agency', array('agency_name', 'agency_id'), $parameters, 'agency_name', 0, NULL, NULL);
        if(isset($title)) {
            $nyc_agencies_key_val = array($title);
        }
        foreach ($nyc_agencies as $value) {
            if (strlen($value['agency_name']) > 20)
                $nyc_agencies_key_val[$value['agency_id']] = substr($value['agency_name'], 0, 20) . '...';
            else
                $nyc_agencies_key_val[$value['agency_id']] = $value['agency_name'];
        }
        return $nyc_agencies_key_val;
    } catch (Exception $e) {
        log_error("Error getting data from controller: \n" . $e->getMessage());
        return;
    }
}

function _checkbook_advanced_search_get_agency_attributes($data_source = 'checkbook')
{
    try {
        switch ($data_source) {
            case 'checkbook_oge':
                $parameters = array("is_oge_agency" => "Y");
                break;
            case 'checkbook_nycha':
                $parameters = array("is_display" => "Y");
                break;
            default:
                $title = 'Citywide(All Agencies)';
                $parameters = null;
                break;
        }
        $dataController = data_controller_get_instance();
        $nyc_agencies = $dataController->queryDataset($data_source . ':agency', array('agency_name', 'agency_id'), $parameters, 'agency_name', 0, NULL, NULL);
        if(isset($title)) {
            $nyc_agencies_key_val = array($title => array('title' => $title));
        }
        foreach ($nyc_agencies as $value) {
            $nyc_agencies_key_val[$value['agency_id']] = array('title' => $value['agency_name']);
        }
        return $nyc_agencies_key_val;
    } catch (Exception $e) {
        log_error("Error getting data from controller: \n" . $e->getMessage());
        return;
    }
}

/*
 * Get mwbe category name and id using mapping
 *
 * Total M/WBE 2,3,4,5,7,9,11
 * Asian American 4,5
 * Black American 2
 * Women 9
 * Hispanic American 3
 * Emerging ?
 * Non-M/WBE 7
 * Individuals and Others 11
 *
 * @return array
 */
function _checkbook_advanced_search_get_mwbe_category_and_id()
{
    $minority_cat_map = MappingUtil::getMinorityCategoryMappings();
    $results = array('' => 'Select Category');
    foreach($minority_cat_map as $category => $minority_types) {
        $results[implode('~',$minority_types)] = $category;
    }
    return $results;
}

/**
 * Gets the industry types and id using the data controller
 * @return void
 */
function _checkbook_advanced_search_get_industry_type_and_id()
{
    try {
        $dataController = data_controller_get_instance();
        $industry_types = $dataController->queryDataset('checkbook:industry_type', array('industry_type_id', 'industry_type_name'), NULL, 'industry_type_name', 0, 10, NULL);
        $industry_types_val = array('Select Industry');
        foreach ($industry_types as $industry_type) {
            $industry_types_val[$industry_type['industry_type_id']] = $industry_type['industry_type_name'];
        }
        return $industry_types_val;
    } catch (Exception $e) {
        log_error("Error getting data from controller: \n" . $e->getMessage());
        return;
    }
}

/*
 * Get expense name and id using the data controller
 * @return array
 */
function _checkbook_advanced_search_get_expensename_and_id()
{
    try {
        $dataController = data_controller_get_instance();
        $nyc_expense_obj = $dataController->queryDataset('checkbook:expenditure_object', array('expenditure_object_id', 'expenditure_object_name'), NULL, 'expenditure_object_name', 0, 10, NULL);
        $nyc_expense_obj_key_val = array('Select Expense category');
        foreach ($nyc_expense_obj as $value) {
            $nyc_expense_obj_key_val[$value['expenditure_object_id']] = $value['expenditure_object_name'];
        }
        return $nyc_expense_obj_key_val;
    } catch (Exception $e) {
        log_error("Error getting data from controller: \n" . $e->getMessage());
        return;
    }
}

/**
 * Gets the subcontract_approval_status and id using the data controller
 * @param null $attributes
 * @return void
 */
function _checkbook_advanced_search_get_subcontract_approval_status_and_id($attributes = NULL)
{
    try {
        $dataController = data_controller_get_instance();
        $values = $dataController->queryDataset('checkbook:subcontract_approval_status', array('aprv_sta_id', 'aprv_sta_value'), NULL, 'sort_order', 0, 10, NULL);

        if($attributes){
            $statuses = array('title' => 'Select Status');
        }else{
            $statuses = array('Select Status');
        }
        foreach ($values as $value) {
            if ($value['aprv_sta_id'] != "7") {
                if ($attributes) {
                    $statuses[$value['aprv_sta_id']] = array('title' => $value['aprv_sta_value']);
                } else {
                    if (strlen($value['aprv_sta_value']) > 20)
                        $statuses[$value['aprv_sta_id']] = substr($value['aprv_sta_value'], 0, 20) . '...';
                    else
                        $statuses[$value['aprv_sta_id']] = $value['aprv_sta_value'];
                }
            }
        }
        return $statuses;
    } catch (Exception $e) {
        log_error("Error getting data from controller: \n" . $e->getMessage());
        return;
    }
}


/**
 * Gets the scntrc_status_name, scntrc_status(id), and display_flag using the data controller
 * @param null $attributes
 * @return void
 */
function _checkbook_advanced_search_get_contract_includes_subvendors_status($attributes = NULL)
{
    try {
        $dataController = data_controller_get_instance();
        $values = $dataController->queryDataset('checkbook:ref_subcontract_status',
            array('scntrc_status', 'scntrc_status_name', 'display_flag'),
            NULL, 'sort_order', 0, 10, NULL);

        if($attributes){
            $statuses = array('title' => 'Select Status');
        }else{
            $statuses = array('Select Status');
        }
        foreach ($values as $value) {
            if($attributes){
                $statuses[$value['scntrc_status']] = array('title' => $value['scntrc_status_name']);
            }else{
                if($value['display_flag'] == 1) {
                    if(strlen($value['scntrc_status_name']) > 20)
                        $statuses[$value['scntrc_status']] = substr($value['scntrc_status_name'], 0, 20) . '...';
                    else
                        $statuses[$value['scntrc_status']] = $value['scntrc_status_name'];
                }
            }
        }
        return $statuses;
    } catch (Exception $e) {
        log_error("Error getting data from controller: \n" . $e->getMessage());
        return;
    }
}


/**
 * Generates the Url param mappings for current amount id by prime and sub
 * @param $value
 * @return string
 */
function _checkbook_advanced_search_add_current_amount_id($value)
{
    $value = _checkbook_advanced_search_replaceSlash($value);
    $redirect_url = '';
    $from_length = strlen($value['from']);
    $to_length = strlen($value['to']);

    $from_value = $from_length > 0 ? $value['from'] : null;
    $to_value = $to_length > 0 ? $value['to'] : null;

//    1. 'Greater than $100M' ==> X > 100000000
//    2. '$51M - $100M' ==>  X > 50000000 AND X <= 100000000
//    3. '$26M - $50M' ==> X > 25000000 AND X <= 50000000
//    4. '$11M - $25M' ==> X > 10000000 AND X <= 25000000
//    5. '$1M - $10M' ==> x >= 1000000 AND X <= 10000000
//    6. 'Less than $1M' ==> X < 1000000

    $amount_ids = array();
    if(isset($from_value) && isset($to_value)) {

        // 1. 'Greater than $100M' ==> X > 100000000
        if($from_value > 100000000 && $to_value > 100000000) {
            $amount_ids[] = 1;
        }
        // 2. '$51M - $100M' ==> X > 50000000 AND X <= 100000000
        if(($from_value > 100000000 && $from_value <= 100000000) || ($to_value > 100000000 && $to_value <= 100000000)) {
            $amount_ids[] = 2;
        }
        // 3. '$26M - $50M' ==> X > 25000000 AND X <= 50000000
        if(($from_value > 25000000 && $from_value <= 50000000) || ($to_value > 25000000 && $to_value <= 50000000)) {
            $amount_ids[] = 3;
        }
        // 4. '$11M - $25M' ==> X > 10000000 AND X <= 25000000
        if(($from_value > 10000000 && $from_value <= 25000000) || ($to_value > 10000000 && $to_value <= 25000000)) {
            $amount_ids[] = 4;
        }
        // 5. '$1M - $10M' ==> x >= 1000000 AND X <= 10000000
        if(($from_value >= 1000000 && $from_value <= 10000000) || ($to_value >= 1000000 && $to_value <= 10000000)) {
            $amount_ids[] = 5;
        }
        // 6. 'Less than $1M' ==> X < 1000000
        if($from_value < 1000000 && $to_value < 1000000) {
            $amount_ids[] = 6;
        }
    }

    if(count($amount_ids) > 0) {
        $amount_param = implode("~",$amount_ids);
        $redirect_url = '/' . "curamtid" . "/" . $amount_param;
    }
    return $redirect_url;
}

/*
 * this functiocheckbook_advanced_searchn is used to generate the URL based on what the users have selected on advanced search form
 * @param $value
 * @param string $url_param
 * @param boolean $need_to_get_id
 * @param boolean $is_range
 * @return string "$url_param/$value"
 */
function _checkbook_advanced_search_generate_redirect_url($value, $url_param = '', $is_range = FALSE)
{
    /*
     *
     * some inputs, like "spending check amount from" and "spending check amount to" form a range of values that we need to search
     * against in the DB. For these type of inputs, set $is_range to TRUE and $value should be an array in the
     * format: array('from' => $check_amount_from, 'to' => $check_amount_to, 'type' => $type)
     * $type is a string with possible values 'amount' or 'date'
     *
     * We need to append these type of inputs to the URL in the following format
     *  if $check_amount_from = 100 and $check_amount_to is 200, then URL would be base_url/redirect_url/checkamt/100~200
     *  if $check_amount_from = 100 and $check_amount_to is not specified, then URL would be base_url/redirect_url/checkamt/100
     *  if $check_amount_from is not specified and $check_amount_to = 200, then URL would be base_url/redirect_url/checkamt/~200
     *
     */
    $value = _checkbook_advanced_search_replaceSlash($value);
    $redirect_url = '';
    if ($is_range) {
        if ($value['type'] == 'amount') {
            /* need to make special exception for amount ranges, because 0 is also an acceptable range value
            and empty(0) always returns true, so we have to use strlen to figure out whether the user has entered
            any valid text in the input box
            */
            $from_length = strlen($value['from']);
            $to_length = strlen($value['to']);

            if ($from_length > 0) {
                $redirect_url = '/' . $url_param . '/' . $value['from'];
            }
            if ($to_length > 0) {
                if ($from_length > 0) {
                    $redirect_url .= '~' . $value['to'];
                } else {
                    $redirect_url = '/' . $url_param . '/~' . $value['to'];
                }
            }
        } else {
            if (!empty($value['from'])) {
                $redirect_url = '/' . $url_param . '/' . $value['from'];
            }
            if (!empty($value['to'])) {
                if (!empty($value['from'])) {
                    $redirect_url .= '~' . $value['to'];
                } else {
                    $redirect_url = '/' . $url_param . '/~' . $value['to'];
                }
            }
        }
    } else {
        $redirect_url = '/' . $url_param . '/' . $value;
    }

    return $redirect_url;
}

/*
 * Get revenue category and id using the data controller
 * @return array
 */
function _checkbook_advanced_search_get_revenue_category_and_id()
{
    try {
        $dataController = data_controller_get_instance();
        $data = $dataController->queryDataset('checkbook:revenue_category', array(
            'revenue_category_id',
            'revenue_category_name'
        ), NULL, 'revenue_category_name');
        $results = array('' => 'All Revenue Categories');
        foreach ($data as $row) {
            $results[$row['revenue_category_id']] = $row['revenue_category_name'];
        }
        return array_unique($results);
    } catch (Exception $e) {
        log_error("Error getting data from controller: \n" . $e->getMessage());
        return;
    }
}

/*
 * Get fund class name and id using the data controller
 * @return array
 */
function _checkbook_advanced_search_get_fund_class_and_id()
{
    try {
        $dataController = data_controller_get_instance();
        $data = $dataController->queryDataset('checkbook:fund_class', array(
            'fund_class_id',
            'fund_class_name'
        ), NULL, 'fund_class_name');
        //$results = array('' => 'All Fund Class');
        foreach ($data as $row) {
            if (strtolower($row['fund_class_name']) == 'general fund')
                $results[$row['fund_class_id']] = $row['fund_class_name'];
        }
        return array_unique($results);
    } catch (Exception $e) {
        log_error("Error getting data from controller: \n" . $e->getMessage());
        return;
    }
}

/*
 * Get funding class name and code using the data controller
 * @return array
 */
function _checkbook_advanced_search_get_funding_source_and_id()
{
    try {
        $dataController = data_controller_get_instance();
        $data = $dataController->queryDataset('checkbook:ref_funding_class', array(
            'funding_class_code',
            'funding_class_name'
        ), NULL, 'funding_class_name');
        $results = array('' => 'All Funding Classes');
        foreach ($data as $row) {
            $results[$row['funding_class_code']] = $row['funding_class_name'];
        }
        return array_unique($results);
    } catch (Exception $e) {
        log_error("Error getting data from controller: \n" . $e->getMessage());
        return;
    }
}

/*
 * Get document id using the data controller
 * @return array
 */
function _checkbook_advanced_search_get_document_id()
{
    try {
        $dataController = data_controller_get_instance();
        $data = $dataController->queryDataset('checkbook:fact_disbursement_line_item', array('document_id'), NULL, 'document_id');
        $results = array('' => 'All Document ID');
        foreach ($data as $row) {
            $results[$row['document_id']] = $row['document_id'];
        }
        return array_unique($results);
    } catch (Exception $e) {
        log_error("Error getting data from controller: \n" . $e->getMessage());
        return;
    }
}

/*
 * Get payroll frequency using the data controller
 * @return array
 */
function _checkbook_advanced_search_get_payroll_frequency()
{
    try {
        $dataController = data_controller_get_instance();
        $data = $dataController->queryDataset('checkbook:ref_pay_frequency', array(
            'pay_frequency'
        ), NULL, 'pay_frequency');

        $results = array('' => 'Select Pay Frequency');
        foreach ($data as $row) {
            $results[$row['pay_frequency']] = $row['pay_frequency'];
        }
        return array_unique($results);
    } catch (Exception $e) {
        log_error("Error getting data from controller: \n" . $e->getMessage());
        return;
    }
}

/*
 * Get spending category name and id using data controller
 * @return array
 */
function _checkbook_advanced_search_get_expensetype_and_id($data_source = 'checkbook')
{
    try {
        $dataController = data_controller_get_instance();
        $data = $dataController->queryDataset($data_source.':spending_category', array(
            'spending_category_id', 'spending_category_name'
        ), NULL, 'display_order');
        $results = array('' => "Total Spending");
        foreach ($data as $row) {
            if ($row['spending_category_id'] != 6)
                $results[$row['spending_category_id']] = $row['spending_category_name'];
        }
        return array_unique($results);
    } catch (Exception $e) {
        log_error("Error getting data from controller: \n" . $e->getMessage());
        return;
    }
}

/*
 * Get agency name and id using the data controller
 * @return array
 */
function _checkbook_advanced_search_get_contract_award_method_and_id($data_source = 'checkbook')
{
    try {
        $dataController = data_controller_get_instance();
        $nyc_agencies = $dataController->queryDataset($data_source.':award_method', array('award_method_name', 'award_method_id', 'award_method_code'), NULL, 'award_method_name', 0, NULL, NULL);
        $nyc_agencies_key_val = array('Select Award Method');
        foreach ($nyc_agencies as $value) {
            $keys = 'id=>' . $value['award_method_id'] . '~code=>' . $value['award_method_code'];
            if (strlen($value['award_method_name']) > 20)
                $nyc_agencies_key_val[$keys] = substr($value['award_method_name'], 0, 20) . '...';
            else
                $nyc_agencies_key_val[$keys] = $value['award_method_name'];
        }
        return $nyc_agencies_key_val;
    } catch (Exception $e) {
        log_error("Error getting data from controller: \n" . $e->getMessage());
        return;
    }
}

function _checkbook_advanced_search_get_contract_award_method_attributes($data_source = 'checkbook')
{
    try {
        $dataController = data_controller_get_instance();
        $nyc_agencies = $dataController->queryDataset($data_source.':award_method', array('award_method_name', 'award_method_id', 'award_method_code'), NULL, 'award_method_name', 0, NULL, NULL);
        $nyc_agencies_key_val = array('title' => 'Select Award Method)');
        foreach ($nyc_agencies as $value) {
            $keys = 'id=>' . $value['award_method_id'] . '~code=>' . $value['award_method_code'];
            $nyc_agencies_key_val[$keys] = array('title' => $value['award_method_name']);
        }
        return $nyc_agencies_key_val;
    } catch (Exception $e) {
        log_error("Error getting data from controller: \n" . $e->getMessage());
        return;
    }
}

/*
 * Get contract type name and id using the data controller
 * @return array
 */
function _checkbook_advanced_search_get_contract_type_and_id($data_source = 'checkbook')
{
    try {
        $dataController = data_controller_get_instance();
        $nyc_agencies = $dataController->queryDataset($data_source.':agreement_type', array('agreement_type_name', 'agreement_type_id', 'agreement_type_code'), NULL, 'agreement_type_name', 0, NULL, NULL);
        $nyc_agencies_key_val = array('Select Contract Type');
        foreach ($nyc_agencies as $value) {
            $keys = 'id=>' . $value['agreement_type_id'] . '~code=>' . $value['agreement_type_code'];
            $nyc_agencies_key_val[$keys] = $value['agreement_type_name'];
        }
        return $nyc_agencies_key_val;
    } catch (Exception $e) {
        log_error("Error getting data from controller: \n" . $e->getMessage());
        return;
    }
}

/***
 * Get Responsibility center options for NYCHA Contracts
 * @param string $data_source
 * @param bool $feeds
 * @return array|void
 */
function _checkbook_advanced_search_get_responsibility_centers($data_source = 'checkbook_nycha', $feeds = false){
    try {
        $query = "SELECT DISTINCT responsibility_center_id, responsibility_center_code, responsibility_center_description FROM ref_responsibility_center ORDER BY description";
        $results = _checkbook_project_execute_sql_by_data_source($query, $data_source);

        $res_center_key_val_option_attributes = array('title' => 'Select Responsibility Center');
        $res_center_key_val_options = array('Select Responsibility Center');

        foreach ($results as $value) {
            if($feeds){
                $text = $value['description'].' ['.$value['responsibility_center_code'].']';
                $res_center_key_val_option_attributes[$text] = array('title' => $text);
                if (strlen($text) > 20)
                    $res_center_key_val_options[$text] = substr($text, 0, 20) . '...';
                else
                    $res_center_key_val_options[$text] = $text;
            } else {
                $keys = 'id=>' . $value['responsibility_center_id'] . '~code=>' . $value['responsibility_center_code'];
                $res_center_key_val_option_attributes[$keys] = array('title' => $value['description']);
                if (strlen($value['description']) > 20)
                    $res_center_key_val_options[$keys] = substr($value['description'], 0, 20) . '...';
                else
                    $res_center_key_val_options[$keys] = $value['description'];
            }
        }

        return array('options' => $res_center_key_val_options, 'option_attributes' => $res_center_key_val_option_attributes);
        
    } catch (Exception $e) {
        log_error("Error getting data from controller: \n" . $e->getMessage());
        return;
    }
}

/***
 * Get Contract Type options for NYCHA
 * @param string $data_source
 * @param bool $feeds
 * @return array|void
 */
function _checkbook_advanced_search_get_nycha_contract_types($data_source = 'checkbook_nycha', $feeds = false){
    try {
        $query = "SELECT DISTINCT contract_type_id, contract_type_code, contract_type_name, contract_type_description FROM ref_contract_type ORDER BY contract_type_name";
        $results = _checkbook_project_execute_sql_by_data_source($query, $data_source);

        $contract_type_key_val_option_attributes = array('title' => 'Select Contract Type');
        $contract_type_key_val_options = array('Select Contract Type');
        
        foreach ($results as $value) {
            if($feeds){
                $text = $value['contract_type_name'].' ['.$value['contract_type_code'].']';
                $contract_type_key_val_option_attributes[$text] = array('title' => $text);
                if (strlen($text) > 20)
                    $contract_type_key_val_options[$text] = substr($text, 0, 20) . '...';
                else
                    $contract_type_key_val_options[$text] = $text;
            }else {
                $keys = 'id=>' . $value['contract_type_id'] . '~code=>' . $value['contract_type_code'];
                $contract_type_key_val_option_attributes[$keys] = array('title' => $value['contract_type_name']);
                if (strlen($value['contract_type_name']) > 20)
                    $contract_type_key_val_options[$keys] = substr($value['contract_type_name'], 0, 20) . '...';
                else
                    $contract_type_key_val_options[$keys] = $value['contract_type_name'];
            }
        }

        return array('options' => $contract_type_key_val_options, 'option_attributes' => $contract_type_key_val_option_attributes);
    } catch (Exception $e) {
        log_error("Error getting data from controller: \n" . $e->getMessage());
        return;
    }
}

/***
 * Get NYCHA Award Method options for NYCHA
 * @param string $data_source
 * @param bool $feeds
 * @return array|void
 */
function checkbook_advanced_search_get_nycha_award_methods($data_source = 'checkbook_nycha', $feeds = false){
    try {
        $query = "SELECT DISTINCT award_method_id, award_method_code, award_method_name FROM ref_award_method ORDER BY award_method_name";
        $results = _checkbook_project_execute_sql_by_data_source($query, $data_source);
        
        $award_method_key_val_option_attributes = array('title' => 'Select Award Method');
        $award_method_key_val_options = array('Select Award Method');

        foreach ($results as $value) {
            if($feeds){
                $text = $value['award_method_name'].' ['.$value['award_method_code'].']';
                $award_method_key_val_option_attributes[$text] = array('title' => $text);
                if (strlen($text) > 20)
                    $award_method_key_val_options[$text] = substr($text, 0, 20) . '...';
                else
                    $award_method_key_val_options[$text] = $text;
            }else {
                $keys = 'id=>' . $value['award_method_id'] . '~code=>' . $value['award_methode_code'];
                $award_method_key_val_option_attributes[$keys] = array('title' => $value['award_method_name']);
                if (strlen($value['award_method_name']) > 20)
                    $award_method_key_val_options[$keys] = substr($value['award_method_name'], 0, 20) . '...';
                else
                    $award_method_key_val_options[$keys] = $value['award_method_name'];
            }
        }

        return array('options' => $award_method_key_val_options, 'option_attributes' => $award_method_key_val_option_attributes);

    } catch (Exception $e) {
        log_error("Error getting data from controller: \n" . $e->getMessage());
        return;
    }
}

/***
 * Get Nycha Industry options for NYCHA
 * @param string $data_source
 * @param bool $feeds
 * @return array|void
 */
function checkbook_advanced_search_get_nycha_industries($data_source = 'checkbook_nycha', $feeds = false){
    try {
        $query = "SELECT DISTINCT industry_type_name, industry_type_id, industry_type_code, display_name FROM ref_industry_type ORDER BY industry_type_name";
        $results = _checkbook_project_execute_sql_by_data_source($query, $data_source);

        $industry_key_val_option_attributes = array('title' => 'Select Industry');
        $industry_key_val_options = array('Select Industry');

        foreach ($results as $value) {
            if($feeds){
                $text = $value['display_name'].' ['.$value['industry_type_code'].']';
                $industry_key_val_option_attributes[$text] = array('title' => $text);
                if (strlen($text) > 20)
                    $industry_key_val_options[$text] = substr($text, 0, 20) . '...';
                else
                    $industry_key_val_options[$text] = $text;
            }else {
                $keys = 'id=>' . $value['industry_type_id'] . '~code=>' . $value['industry_type_code'];
                $industry_key_val_option_attributes[$keys] = array('title' => $value['display_name']);
                if (strlen($value['display_name']) > 20)
                    $industry_key_val_options[$keys] = substr($value['display_name'], 0, 20) . '...';
                else
                    $industry_key_val_options[$keys] = $value['display_name'];
            }
        }
        return array('options' => $industry_key_val_options, 'option_attributes' => $industry_key_val_option_attributes);

    } catch (Exception $e) {
        log_error("Error getting data from controller: \n" . $e->getMessage());
        return;
    }
}



/*
 * Constructs the URL for Payroll based on the input by the users
 */
function _checkbook_advanced_search_payroll_submit($form, &$form_state)
{
    $data_source = $form_state['values']['payroll_advanced_search_domain_filter'];
    $payroll_employee_name = trim($form['payroll']['payroll_employee_name']['#value']);
    $payroll_employee_name_exact = trim($form['payroll']['payroll_employee_name_exact']['#value']);
    $payroll_agency = trim($form['payroll'][$data_source.'_payroll_agencies']['#value']);
    $payroll_department = trim($form['payroll']['payroll_department']['#value']);
    $payroll_pay_frequency = trim($form['payroll']['payroll_pay_frequency']['#value']);
    $payroll_pay_date_from = trim($form['payroll']['payroll_pay_date_from']['#value']['date']);
    $payroll_pay_date_to = trim($form['payroll']['payroll_pay_date_to']['#value']['date']);
    $payroll_amount_from = trim($form['payroll']['payroll_amount_from']['#value']);
    $payroll_amount_to = trim($form['payroll']['payroll_amount_to']['#value']);
    $payroll_amount_type = trim($form['payroll']['payroll_amount_type']['#value']);
    $payroll_base_salary_from = trim($form['payroll']['payroll_base_salary_from']['#value']);
    $payroll_base_salary_to = trim($form['payroll']['payroll_base_salary_to']['#value']);
    $payroll_overtime_amount_from = trim($form['payroll']['payroll_overtime_amount_from']['#value']);
    $payroll_overtime_amount_to = trim($form['payroll']['payroll_overtime_amount_to']['#value']);
    $payroll_other_payments_from = trim($form['payroll']['payroll_other_payments_from']['#value']);
    $payroll_other_payments_to = trim($form['payroll']['payroll_other_payments_to']['#value']);
    $payroll_gross_pay_amount_from = trim($form['payroll']['payroll_gross_pay_amount_from']['#value']);
    $payroll_gross_pay_amount_to = trim($form['payroll']['payroll_gross_pay_amount_to']['#value']);
    $payroll_total_gross_pay_from = trim($form['payroll']['payroll_total_gross_pay_from']['#value']);
    $payroll_total_gross_pay_to = trim($form['payroll']['payroll_total_gross_pay_to']['#value']);
    $payroll_year = trim($form['payroll']['payroll_year']['#value']);

    $redirect_url = 'payroll/search/transactions';

    if (!empty($payroll_employee_name)) {
        if ($payroll_employee_name_exact == $payroll_employee_name) {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($payroll_employee_name_exact, 'empnm_exact');
        } else {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($payroll_employee_name, 'empnm');
        }
    }
    if (!empty($payroll_agency)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($payroll_agency, 'agency');
    }
    if (!empty($payroll_department)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($payroll_department, 'deptnm');
    }
    if (!empty($payroll_pay_frequency)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($payroll_pay_frequency, 'payfq');
    }

    $payroll_pay_date_range_array = array('from' => $payroll_pay_date_from, 'to' => $payroll_pay_date_to, 'type' => 'date');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($payroll_pay_date_range_array, 'paydate', TRUE);

    $payroll_amount_range_array = array('from' => $payroll_amount_from, 'to' => $payroll_amount_to, 'type' => 'amount');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($payroll_amount_range_array, 'salamt', TRUE);

    $payroll_base_salary_range_array = array('from' => $payroll_base_salary_from, 'to' => $payroll_base_salary_to, 'type' => 'amount');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($payroll_base_salary_range_array, 'baseamt', TRUE);

    $payroll_overtime_amount_range_array = array('from' => $payroll_overtime_amount_from, 'to' => $payroll_overtime_amount_to, 'type' => 'amount');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($payroll_overtime_amount_range_array, 'overamt', TRUE);

    $payroll_other_payments_range_array = array('from' => $payroll_other_payments_from, 'to' => $payroll_other_payments_to, 'type' => 'amount');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($payroll_other_payments_range_array, 'otheramt', TRUE);

    $payroll_gross_pay_amount_range_array = array('from' => $payroll_gross_pay_amount_from, 'to' => $payroll_gross_pay_amount_to, 'type' => 'amount');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($payroll_gross_pay_amount_range_array, 'grossamt', TRUE);

    $payroll_total_gross_pay_range_array = array('from' => $payroll_total_gross_pay_from, 'to' => $payroll_total_gross_pay_to, 'type' => 'amount');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($payroll_total_gross_pay_range_array, 'totalgrosspay', TRUE);

    if (!empty($payroll_year)) {
        $redirect_url .= _checkbook_advanced_search_year_arg($payroll_year);
    }

    if($data_source == 'checkbook_oge'){
        $redirect_url .= '/datasource/checkbook_nycha'.Datasource::getNYCHAUrl();
    }

    if ($payroll_amount_type == 0) {
        $redirect_url .= '/salamttype/1~2~3';
    } elseif ($payroll_amount_type == 1) {
        $redirect_url .= '/salamttype/1';
    } elseif ($payroll_amount_type == 2) {
        $redirect_url .= '/salamttype/2~3';
    }

    return $redirect_url;
}

function _checkbook_advanced_search_add_payroll_range_filter_param($from,$to,$url_param){
    $redirect_url = "";
    if($from == "" && $to == "") return $redirect_url;
    $parameters = array();
    if($from < 20000 || $to < 20000)
        $parameters[] = 1;
    if(($from >= 21000 && $from <= 50000 ) || ($to >= 21000 && $to <= 50000 ))
        $parameters[] = 2;
    if(($from >= 51000 && $from <= 100000 ) || ($to >= 51000 && $to <= 100000 ))
        $parameters[] = 3;
    if(($from >= 101000 && $from <= 250000 ) || ($to >= 101000 && $to <= 250000 ))
        $parameters[] = 4;
    if($to > 250000 || $from > 250000)
        $parameters[] = 5;

    if(!empty($parameters)) {
        $redirect_url = "/".$url_param."/";
        $redirect_url .= implode('~',$parameters);
    }

    return $redirect_url;
}
/*
 * Constructs the URL for Nycha Contracts based on the input by the users
 */
function _checkbook_advanced_search_nycha_contracts_submit($form, &$form_state, $data_source = 'checkbook_oge')
{
    $filter_dimension = $data_source . '_contracts';
    $contracts_nycha_agency_id = Datasource::getNYCHAId();
    $contracts_contract_type=trim($form[$filter_dimension][$data_source . '_contracts_nycha_contract_type']['#value']);
    $contracts_contract_vendor_name_exact = trim($form[$filter_dimension][$data_source . '_contracts_vendor_name_exact']['#value']);
    $contracts_responsibility_center=trim($form[$filter_dimension][$data_source . '_contracts_responsibility_center']['#value']);
    $contracts_purchase_order_type = trim($form[$filter_dimension][$data_source . '_contracts_purchase_order_type']['#value']);
    $contracts_contract_vendor_name = trim($form[$filter_dimension][$data_source . '_contracts_vendor_name']['#value']);
    $contracts_contract_purpose = trim($form[$filter_dimension][$data_source . '_contracts_purpose']['#value']);
    $contracts_contract_contract_num = trim($form[$filter_dimension][$data_source . '_contracts_purchase_order_number']['#value']);
    $contracts_contract_contract_num_exact = trim($form[$filter_dimension][$data_source . '_contracts_purchase_order_number_exact']['#value']);
    $contracts_contract_pin = trim($form[$filter_dimension][$data_source . '_contracts_pin']['#value']);
    $contracts_contract_award_method = trim($form[$filter_dimension][$data_source . '_contracts_nycha_award_method']['#value']);
    $contracts_contract_current_contract_amount_from = trim($form[$filter_dimension][$data_source . '_contracts_current_contract_amount_from']['#value']);
    $contracts_contract_current_contract_amount_to = trim($form[$filter_dimension][$data_source . '_contracts_current_contract_amount_to']['#value']);
    $contracts_contract_start_date_from = trim($form[$filter_dimension][$data_source . '_contracts_start_date_from']['#value']['date']);
    $contracts_contract_start_date_to = trim($form[$filter_dimension][$data_source . '_contracts_start_date_to']['#value']['date']);
    $contracts_contract_end_date_from = trim($form[$filter_dimension][$data_source . '_contracts_end_date_from']['#value']['date']);
    $contracts_contract_end_date_to = trim($form[$filter_dimension][$data_source . '_contracts_end_date_to']['#value']['date']);
    $contracts_contract_approved_date_from = trim($form_state['input'][$data_source . '_contracts_approved_date_from']['date']);
    $contracts_contract_approved_date_to = trim($form_state['input'][$data_source . '_contracts_approved_date_to']['date']);
    $contracts_year = trim($form[$filter_dimension][$data_source . '_contracts_year']['#value']);
    $contracts_industry_type_id = trim($form[$filter_dimension][$data_source . '_contracts_nycha_industry']['#value']);
    $contracts_industry_type_id = $contracts_industry_type_id == "Select Industry" ? 0 : $contracts_industry_type_id;

    if ($contracts_year == 'fy~all') {
        $redirect_url = 'nycha_contracts/all/transactions';
        $contracts_year = null;
    }
    else {
        $redirect_url = 'nycha_contracts/search/transactions';
    }
    if(!empty($contracts_nycha_agency_id)){
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_nycha_agency_id, 'agency');
    }

    if(!empty($contracts_purchase_order_type)){
        if($contracts_purchase_order_type!=="All"){

            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_purchase_order_type, 'agreement_type');
        }
    }
    if(!empty( $contracts_contract_type))
    {
        $code_id_array = _checkbook_advanced_search_autocomplete_get_code_id($contracts_contract_type);
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($code_id_array['code'], 'contract_type');
    }

    //Pending Contracts should always be in the current FY.
    if (!empty($contracts_industry_type_id)) {
        $code_id_array = _checkbook_advanced_search_autocomplete_get_code_id($contracts_industry_type_id);
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($code_id_array['id'], 'industrynm_exact');
    }

    if (!empty($contracts_contract_vendor_name)) {
        if ($contracts_contract_vendor_name_exact == $contracts_contract_vendor_name) {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_vendor_name_exact, 'vendornm_exact');
        } else {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_vendor_name, 'vendornm');
        }
    }
    if (!empty($contracts_contract_purpose)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_purpose, 'pcontdesc');
    }

    if (!empty($contracts_contract_contract_num)) {
        if ($contracts_contract_contract_num_exact == $contracts_contract_contract_num) {

            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_contract_num, 'po_num_exact');
        }
        else{
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_contract_num, 'po_num');
        }

    }


    if (!empty($contracts_responsibility_center)) {
        $code_id_array = _checkbook_advanced_search_autocomplete_get_code_id($contracts_responsibility_center);
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($code_id_array['id'], 'responsibilitynm_exact');
    }

    if (!empty($contracts_contract_pin)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_pin, 'pin');
    }
    if (!empty($contracts_contract_award_method)) {
        $code_id_array = _checkbook_advanced_search_autocomplete_get_code_id($contracts_contract_award_method);
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($code_id_array['id'], 'awdmethod');
    }

    if (!empty($contracts_year)) {
        $redirect_url .= _checkbook_advanced_search_year_arg($contracts_year);
    }

    $contracts_contract_current_contract_amount_range_array = array('from' => $contracts_contract_current_contract_amount_from, 'to' => $contracts_contract_current_contract_amount_to, 'type' => 'amount');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_current_contract_amount_range_array, 'curamtr', TRUE);

    $contracts_contract_start_date_range_array = array('from' => $contracts_contract_start_date_from, 'to' => $contracts_contract_start_date_to, 'type' => 'date');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_start_date_range_array, 'startdate', TRUE);

    $contracts_contract_end_date_range_array = array('from' => $contracts_contract_end_date_from, 'to' => $contracts_contract_end_date_to, 'type' => 'date');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_end_date_range_array, 'enddate', TRUE);

    $contracts_contract_approved_date_range_array = array('from' => $contracts_contract_approved_date_from, 'to' => $contracts_contract_approved_date_to, 'type' => 'date');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_approved_date_range_array, 'appdate', TRUE);


    $redirect_url .= '/datasource/checkbook_nycha';

    return $redirect_url;
}

/*
 * Constructs the URL for Contracts based on the input by the users
 */
function _checkbook_advanced_search_contracts_submit($form, &$form_state, $data_source = 'checkbook')
{
    $filter_dimension = $data_source . '_contracts';
    $contracts_agency = trim($form[$filter_dimension][$data_source . '_contracts_agency']['#value']);
    if( $contracts_agency == Datasource::getNYCHAId()){
     $redirect_url =   _checkbook_advanced_search_nycha_contracts_submit($form, $form_state,  'checkbook_oge');
     return $redirect_url;
    }

    $contracts_contract_status = trim($form[$filter_dimension][$data_source . '_contracts_status']['#value']);
    $contracts_contract_category = trim($form[$filter_dimension][$data_source . '_contracts_category']['#value']);
    $contracts_contract_vendor_name = trim($form[$filter_dimension][$data_source . '_contracts_vendor_name']['#value']);
    $contracts_contract_vendor_name_exact = trim($form[$filter_dimension][$data_source . '_contracts_vendor_name_exact']['#value']);
    $contracts_contract_purpose = trim($form[$filter_dimension][$data_source . '_contracts_purpose']['#value']);
    $contracts_contract_type = trim($form[$filter_dimension][$data_source . '_contracts_type']['#value']);
    $contracts_contract_agency = trim($form[$filter_dimension][$data_source . '_contracts_agency']['#value']);
    $contracts_contract_contract_num = trim($form[$filter_dimension][$data_source . '_contracts_contract_num']['#value']);
    $contracts_contract_contract_num_exact = trim($form[$filter_dimension][$data_source . '_contracts_contract_num_exact']['#value']);
    $contracts_contract_apt_pin = trim($form[$filter_dimension][$data_source . '_contracts_apt_pin']['#value']);
    $contracts_contract_pin = trim($form[$filter_dimension][$data_source . '_contracts_pin']['#value']);
    $contracts_contract_award_method = trim($form[$filter_dimension][$data_source . '_contracts_award_method']['#value']);
    $contracts_contract_current_contract_amount_from = trim($form[$filter_dimension][$data_source . '_contracts_current_contract_amount_from']['#value']);
    $contracts_contract_current_contract_amount_to = trim($form[$filter_dimension][$data_source . '_contracts_current_contract_amount_to']['#value']);
    $contracts_contract_start_date_from = trim($form[$filter_dimension][$data_source . '_contracts_start_date_from']['#value']['date']);
    $contracts_contract_start_date_to = trim($form[$filter_dimension][$data_source . '_contracts_start_date_to']['#value']['date']);
    $contracts_contract_end_date_from = trim($form[$filter_dimension][$data_source . '_contracts_end_date_from']['#value']['date']);
    $contracts_contract_end_date_to = trim($form[$filter_dimension][$data_source . '_contracts_end_date_to']['#value']['date']);
    $contracts_contract_received_date_from = trim($form_state['input'][$data_source . '_contracts_received_date_from']['date']);
    $contracts_contract_received_date_to = trim($form_state['input'][$data_source . '_contracts_received_date_to']['date']);
    $contracts_contract_registration_date_from = trim($form[$filter_dimension][$data_source . '_contracts_registration_date_from']['#value']['date']);
    $contracts_contract_registration_date_to = trim($form[$filter_dimension][$data_source . '_contracts_registration_date_to']['#value']['date']);
    $contracts_entity_contract_number = trim($form[$filter_dimension][$data_source . '_contracts_entity_contract_number']['#value']);
    $contracts_entity_contract_number_exact = trim($form[$filter_dimension][$data_source . '_contracts_entity_contract_number_exact']['#value']);
    $contracts_commodity_line = trim($form[$filter_dimension][$data_source . '_contracts_commodity_line']['#value']);
    $contracts_commodity_line_exact = trim($form[$filter_dimension][$data_source . '_contracts_commodity_line_exact']['#value']);
    $contracts_budget_name = trim($form[$filter_dimension][$data_source . '_contracts_budget_name']['#value']);
    $contracts_budget_name_exact = trim($form[$filter_dimension][$data_source . '_contracts_budget_name_exact']['#value']);
    $contracts_year = trim($form[$filter_dimension][$data_source . '_contracts_year']['#value']);
    $contracts_mwbe_category = trim($form[$filter_dimension][$data_source . '_contracts_mwbe_category']['#value']);
    $contracts_mwbe_category = $contracts_mwbe_category == "Select Category" ? 0 : $contracts_mwbe_category;
    $contracts_industry_type_id = trim($form[$filter_dimension][$data_source . '_contracts_industry']['#value']);
    $contracts_industry_type_id = $contracts_industry_type_id == "Select Industry" ? 0 : $contracts_industry_type_id;
    $contracts_includes_sub_vendors = trim($form[$filter_dimension][$data_source . '_contracts_includes_sub_vendors']['#value']);
    $contracts_includes_sub_vendors = $contracts_includes_sub_vendors == "Select Status" ||  $contracts_includes_sub_vendors == ""
        ? 0 : $contracts_includes_sub_vendors;
    $contracts_sub_vendor_status = trim($form[$filter_dimension][$data_source . '_contracts_sub_vendor_status']['#value']);
    $contracts_sub_vendor_status = $contracts_sub_vendor_status == "Select Status" ||  $contracts_sub_vendor_status == ""
        ? 0 : $contracts_sub_vendor_status;


    if ($contracts_year == 'fy~all') {
        $redirect_url = 'contract/all/transactions';
        $contracts_year = null;
    } else {
        $redirect_url = 'contract/search/transactions';
    }

    //Pending Contracts should always be in the current FY.
    if($contracts_contract_status == "P") {
        $contracts_year = "fy~" . _getCurrentYearID();
    }

    if (!empty($contracts_mwbe_category)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_mwbe_category, 'mwbe');
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_mwbe_category, 'psmwbe');
    }
    if (!empty($contracts_industry_type_id)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_industry_type_id, 'cindustry');
    }
    if (!empty($contracts_contract_status)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_status, 'contstatus');
    }
    if($contracts_contract_status != "P" && $contracts_contract_category != 'revenue') {
        if (!empty($contracts_includes_sub_vendors)) {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_includes_sub_vendors, 'subcontstatus');
        }
        if (!empty($contracts_sub_vendor_status)) {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_sub_vendor_status, 'subvendorstatus');
        }
    }
    if (!empty($contracts_contract_category)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_category, 'contcat');
        if ($contracts_contract_status != 'P'){
            if($contracts_contract_category == 'revenue'){
                $redirect_url .= '/doctype/RCT1';
            }else if($contracts_contract_category == 'expense'){
                $redirect_url .= '/doctype/MMA1~MA1~CTA1~CT1~DO1';
            }
            else {
                $redirect_url .= '/doctype/MMA1~MA1~CTA1~CT1~DO1~RCT1';
            }
        }
        else {
            if($contracts_contract_category == 'revenue'){
                $redirect_url .= '/doctype/RCT1';
            }else if($contracts_contract_category == 'expense'){
                $redirect_url .= '/doctype/MMA1~MA1~MAR~CTA1~CT1~CTR~DO1';
            }
            else {
                $redirect_url .= '/doctype/MMA1~MA1~MAR~CTA1~CT1~CTR~DO1~RCT1';
            }
        }
    }
    if (!empty($contracts_contract_vendor_name)) {
        if ($contracts_contract_vendor_name_exact == $contracts_contract_vendor_name) {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_vendor_name_exact, 'vendornm_exact');
        } else {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_vendor_name, 'vendornm');
        }
    }
    if (!empty($contracts_contract_purpose)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_purpose, 'pcontdesc');
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_purpose, 'scontdesc');
    }
    if (!empty($contracts_contract_agency)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_agency, 'agency');
    }
    if (!empty($contracts_contract_contract_num)) {
        if ($contracts_contract_contract_num_exact == $contracts_contract_contract_num) {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url(strtoupper($contracts_contract_contract_num_exact), 'contnum_exact');
        }
        else {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_contract_num, 'contnum');
        }
    }
    if (!empty($contracts_contract_apt_pin)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_apt_pin, 'aptpin');
    }
    if (!empty($contracts_contract_pin)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_pin, 'pin');
    }
    if (!empty($contracts_contract_award_method)) {
        $code_id_array = _checkbook_advanced_search_autocomplete_get_code_id($contracts_contract_award_method);
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($code_id_array['id'], 'awdmethod');
    }
    if (!empty($contracts_contract_type)) {
        switch ($data_source) {
            case "checkbook_oge":
                $code_id_array = _checkbook_advanced_search_autocomplete_get_code_id($contracts_contract_type);
                $redirect_url .= _checkbook_advanced_search_generate_redirect_url($code_id_array['code'], 'agrmnttypecode');
                break;
            default:
                $code_id_array = _checkbook_advanced_search_autocomplete_get_code_id($contracts_contract_type);
                if ($contracts_contract_status == 'P') {
                    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($code_id_array['code'], 'agrmnttypecode');
                }
                else {
                    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($code_id_array['id'], 'agrmnttype');
                }
                break;
        }
    }
    if (!empty($contracts_year)) {
        $redirect_url .= _checkbook_advanced_search_year_arg($contracts_year);
    }
    if (!empty($contracts_entity_contract_number)) {
        if ($contracts_entity_contract_number_exact == $contracts_entity_contract_number) {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_entity_contract_number, 'entcontnum_exact');
        } else {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_entity_contract_number, 'entcontnum');
        }
    }
    if (!empty($contracts_commodity_line)) {
        if ($contracts_commodity_line_exact == $contracts_commodity_line) {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_commodity_line, 'comline_exact');
        } else {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_commodity_line, 'comline');
        }
    }
    if (!empty($contracts_budget_name)) {
        if ($contracts_budget_name_exact == $contracts_budget_name) {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_budget_name, 'budname_exact');
        } else {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_budget_name, 'budname');
        }
    }

    $contracts_contract_current_contract_amount_range_array = array('from' => $contracts_contract_current_contract_amount_from, 'to' => $contracts_contract_current_contract_amount_to, 'type' => 'amount');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_current_contract_amount_range_array, 'curamt', TRUE);

    $contracts_contract_start_date_range_array = array('from' => $contracts_contract_start_date_from, 'to' => $contracts_contract_start_date_to, 'type' => 'date');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_start_date_range_array, 'startdate', TRUE);

    $contracts_contract_end_date_range_array = array('from' => $contracts_contract_end_date_from, 'to' => $contracts_contract_end_date_to, 'type' => 'date');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_end_date_range_array, 'enddate', TRUE);

    $contracts_contract_received_date_range_array = array('from' => $contracts_contract_received_date_from, 'to' => $contracts_contract_received_date_to, 'type' => 'date');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_received_date_range_array, 'recdate', TRUE);

    $contracts_contract_registration_date_range_array = array('from' => $contracts_contract_registration_date_from, 'to' => $contracts_contract_registration_date_to, 'type' => 'date');

    //append data source if not default (checkbook)
    if($data_source != 'checkbook') $redirect_url .= '/datasource/' . $data_source;

    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($contracts_contract_registration_date_range_array, 'regdate', TRUE);

    return $redirect_url;
}

/*
 * Constructs the URL for Spending transactions based on the input by the users
 */
function _checkbook_advanced_search_spending_submit($form, &$form_state, $data_source = 'checkbook')
{
    $filter_dimension = $data_source . '_spending';

    $spending_payee_name = trim($form[$filter_dimension][$data_source . '_spending_payee_name']['#value']);
    $spending_payee_name_exact = trim($form[$filter_dimension][$data_source . '_spending_payee_name_exact']['#value']);
    $spending_contract_num = trim($form[$filter_dimension][$data_source . '_spending_contract_num']['#value']);
    $spending_contract_num_exact = trim($form[$filter_dimension][$data_source . '_spending_contract_num_exact']['#value']);
    $spending_document_id = trim($form[$filter_dimension][$data_source . '_spending_document_id']['#value']);
    $spending_document_id_exact = trim($form[$filter_dimension][$data_source . '_spending_document_id_exact']['#value']);
    $spending_agencies = trim($form[$filter_dimension][$data_source . '_spending_agency']['#value']); //drop down

    $spending_expense_category = trim($form[$filter_dimension][$data_source . '_spending_expense_category']['#value']);
    $spending_expense_category = $spending_expense_category == "Select Expense Category" ? 0 : $spending_expense_category;

    $spending_check_amount_from = trim($form[$filter_dimension][$data_source . '_spending_check_amount_from']['#value']);
    $spending_check_amount_to = trim($form[$filter_dimension][$data_source . '_spending_check_amount_to']['#value']);
    $spending_issue_date_from = trim($form[$filter_dimension][$data_source . '_spending_issue_date_from']['#value']['date']);
    $spending_issue_date_to = trim($form[$filter_dimension][$data_source . '_spending_issue_date_to']['#value']['date']);
    $spending_fund_class = trim($form[$filter_dimension][$data_source . '_spending_fund_class']['#value']);

    $spending_department = trim($form[$filter_dimension][$data_source . '_spending_department']['#value']);
    $spending_department = $spending_department == "Select Department" ? 0 : $spending_department;

    $spending_budget_code = trim($form[$filter_dimension][$data_source . '_spending_budget_code']['#value']);
    $spending_capital_project = trim($form[$filter_dimension][$data_source . '_spending_capital_project']['#value']);
    $spending_capital_project_exact = trim($form[$filter_dimension][$data_source . '_spending_capital_project_exact']['#value']);
    $spending_expense_type = trim($form[$filter_dimension][$data_source . '_spending_expense_type']['#value']);
    $spending_fiscal_year = trim($form[$filter_dimension][$data_source . '_spending_fiscal_year']['#value']);
    $spending_date_filter = trim($form[$filter_dimension][$data_source . '_spending_date_filter']['#value']);

    $spending_entity_contract_number = trim($form[$filter_dimension][$data_source . '_spending_entity_contract_number']['#value']);
    $spending_entity_contract_number_exact = trim($form[$filter_dimension][$data_source . '_spending_entity_contract_number_exact']['#value']);
    $spending_commodity_line = trim($form[$filter_dimension][$data_source . '_spending_commodity_line']['#value']);
    $spending_commodity_line_exact = trim($form[$filter_dimension][$data_source . '_spending_commodity_line_exact']['#value']);
    $spending_budget_name = trim($form[$filter_dimension][$data_source . '_spending_budget_name']['#value']);
    $spending_budget_name_exact = trim($form[$filter_dimension][$data_source . '_spending_budget_name_exact']['#value']);

    $spending_mwbe_category = trim($form[$filter_dimension][$data_source . '_spending_mwbe_category']['#value']);
    $spending_mwbe_category = $spending_mwbe_category == "Select Category" ? 0 : $spending_mwbe_category;

    $spending_industry_type_id = trim($form[$filter_dimension][$data_source . '_spending_industry']['#value']);
    $spending_industry_type_id = $spending_industry_type_id == "Select Industry" ? 0 : $spending_industry_type_id;

    if ($spending_fiscal_year == 'fy~all') {
        $spending_fiscal_year = null;
    }

    $redirect_url = 'spending/search/transactions';
    //append data source if not default (checkbook)
    if($data_source != 'checkbook') $redirect_url .= '/datasource/' . $data_source;

    if (!empty($spending_mwbe_category)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_mwbe_category, 'mwbe');
    }
    if (!empty($spending_industry_type_id)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_industry_type_id, 'industry');
    }
    if (!empty($spending_fiscal_year) && $spending_date_filter == 0) {
        $redirect_url .= _checkbook_advanced_search_year_arg($spending_fiscal_year, 'year');
    }
    if (!empty($spending_payee_name)) {
        if ($spending_payee_name_exact == $spending_payee_name) {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_payee_name, 'vendornm_exact');
        } else {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_payee_name, 'vendornm');
        }
    }
    if (!empty($spending_contract_num)) {
        if ($spending_contract_num_exact == $spending_contract_num) {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_contract_num, 'contnum_exact');
        } else {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_contract_num, 'contnum');
        }
    }
    if (!empty($spending_document_id)) {
        if ($spending_document_id_exact == $spending_document_id) {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_document_id, 'expdoc_exact');
        } else {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_document_id, 'expdoc');
        }
    }
    if (!empty($spending_agencies)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_agencies, 'agency');
    }
    if (!empty($spending_expense_category)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_expense_category, 'expcategorycode');
    }
    if (!empty($spending_fund_class)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_fund_class, 'fundcls');
    }
    if (!empty($spending_department)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_department, 'deptnm');
    }
    if (!empty($spending_budget_code)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_budget_code, 'bdgcodenm');
    }
    if (!empty($spending_capital_project)) {
        if ($spending_capital_project_exact == $spending_capital_project) {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_capital_project, 'captprj_exact');
        } else {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_capital_project, 'captprj');
        }
    }
    if (!empty($spending_expense_type)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_expense_type, 'category');
    }

    if (!empty($spending_entity_contract_number)) {
        if ($spending_entity_contract_number_exact == $spending_entity_contract_number) {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_entity_contract_number, 'entcontnum_exact');
        } else {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_entity_contract_number, 'entcontnum');
        }
    }
    if (!empty($spending_commodity_line)) {
        if ($spending_commodity_line_exact == $spending_commodity_line) {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_commodity_line, 'comline_exact');
        } else {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_commodity_line, 'comline');
        }
    }
    if (!empty($spending_budget_name)) {
        if ($spending_budget_name_exact == $spending_budget_name) {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_budget_name, 'budname_exact');
        } else {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_budget_name, 'budname');
        }
    }

    //URL for range parameters, need to specify whether the range is an amount or a date
    $spending_check_amount_range_array = array('from' => $spending_check_amount_from, 'to' => $spending_check_amount_to, 'type' => 'amount');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_check_amount_range_array, 'checkamt', TRUE);

    if ($spending_date_filter == 1) {
        $spending_issue_date_range_array = array('from' => $spending_issue_date_from, 'to' => $spending_issue_date_to, 'type' => 'date');
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($spending_issue_date_range_array, 'chkdate', TRUE);
    }

    return $redirect_url;
}

/*
 * Constructs the URL for Budget transactions based on the input by the users
 */
function _checkbook_advanced_search_budget_submit($form, &$form_state)
{
    $budget_agency = trim($form['budget']['budget_agencies']['#value']);
    $budget_department = trim($form_state['input']['budget_department']);
    $budget_expense_category = trim($form_state['input']['budget_expense_category']);
    $budget_budget_code = trim($form['budget']['budget_budget_code']['#value']);
    $budget_budget_name = trim($form['budget']['budget_budget_name']['#value']);
    $budget_fiscal_year = trim($form['budget']['budget_fiscal_year']['#value']);
    $budget_adopted_budget_from = trim($form['budget']['budget_adopted_budget_from']['#value']);
    $budget_adopted_budget_to = trim($form['budget']['budget_adopted_budget_to']['#value']);
    $budget_current_modified_from = trim($form['budget']['budget_current_modified_from']['#value']);
    $budget_current_modified_to = trim($form['budget']['budget_current_modified_to']['#value']);
    $budget_pre_encumbered_from = trim($form['budget']['budget_pre_encumbered_from']['#value']);
    $budget_pre_encumbered_to = trim($form['budget']['budget_pre_encumbered_to']['#value']);
    $budget_encumbered_from = trim($form['budget']['budget_encumbered_from']['#value']);
    $budget_encumbered_to = trim($form['budget']['budget_encumbered_to']['#value']);
    $budget_accrued_expense_from = trim($form['budget']['budget_accrued_expense_from']['#value']);
    $budget_accrued_expense_to = trim($form['budget']['budget_accrued_expense_to']['#value']);
    $budget_cash_expense_from = trim($form['budget']['budget_cash_expense_from']['#value']);
    $budget_cash_expense_to = trim($form['budget']['budget_cash_expense_to']['#value']);
    $budget_post_adjustments_from = trim($form['budget']['budget_post_adjustments_from']['#value']);
    $budget_post_adjustments_to = trim($form['budget']['budget_post_adjustments_to']['#value']);
    $redirect_url = "budget/transactions";
    if (!empty($budget_fiscal_year)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_fiscal_year, 'year');
    }
    if (!empty($budget_expense_category)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_expense_category, 'expcategorynm');
    }
    if (!empty($budget_agency)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_agency, 'agency');
    }
    if (!empty($budget_department)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_department, 'deptnm');
    }
    if (!empty($budget_budget_code)) {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_budget_code, 'bdgcode_code');
    } 
    if (!empty($budget_budget_name)) {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_budget_name, 'bdgcodenm');
    }
    

    //URL for ranges
    $budget_adopted_budget_range_array = array('from' => $budget_adopted_budget_from, 'to' => $budget_adopted_budget_to, 'type' => 'amount');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_adopted_budget_range_array, 'adpamt', TRUE);

    $budget_pre_encumbered_range_array = array('from' => $budget_pre_encumbered_from, 'to' => $budget_pre_encumbered_to, 'type' => 'amount');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_pre_encumbered_range_array, 'preamt', TRUE);

    $budget_encumbered_range_array = array('from' => $budget_encumbered_from, 'to' => $budget_encumbered_to, 'type' => 'amount');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_encumbered_range_array, 'encamt', TRUE);

    $budget_cash_expense_range_array = array('from' => $budget_cash_expense_from, 'to' => $budget_cash_expense_to, 'type' => 'amount');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_cash_expense_range_array, 'cshamt', TRUE);

    $budget_post_adjustments_range_array = array('from' => $budget_post_adjustments_from, 'to' => $budget_post_adjustments_to, 'type' => 'amount');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_post_adjustments_range_array, 'postamt', TRUE);

    $budget_accrued_expense_range_array = array('from' => $budget_accrued_expense_from, 'to' => $budget_accrued_expense_to, 'type' => 'amount');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_accrued_expense_range_array, 'acramt', TRUE);

    $budget_current_modified_range_array = array('from' => $budget_current_modified_from, 'to' => $budget_current_modified_to, 'type' => 'amount');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($budget_current_modified_range_array, 'curamt', TRUE);

    return $redirect_url;
}

/*
 * Constructs the URL for Revenue transactions based on the input by the users
 */
function _checkbook_advanced_search_revenue_submit($form, &$form_state, $button = NULL)
{

    $revenue_fund_class = trim($form['revenue']['revenue_fund_class']['#value']);
    $revenue_agency = trim($form['revenue']['revenue_agencies']['#value']);
    $revenue_fiscal_year = trim($form['revenue']['revenue_fiscal_year']['#value']);
    $revenue_budget_fiscal_year = trim($form['revenue']['revenue_budget_fiscal_year']['#value']);
    $revenue_revenue_category = trim($form['revenue']['revenue_revenue_category']['#value']);
    $revenue_revenue_class = trim($form['revenue']['revenue_revenue_class']['#value']);
    $revenue_revenue_class_exact = trim($form['revenue']['revenue_revenue_class_exact']['#value']);
    $revenue_revenue_source = trim($form['revenue']['revenue_revenue_source']['#value']);
    $revenue_revenue_source_exact = trim($form['revenue']['revenue_revenue_source_exact']['#value']);
    $revenue_funding_source = trim($form['revenue']['revenue_funding_source']['#value']);
    $revenue_adopted_from = trim($form['revenue']['revenue_adopted_from']['#value']);
    $revenue_adopted_to = trim($form['revenue']['revenue_adopted_to']['#value']);
    $revenue_recognized_from = trim($form['revenue']['revenue_recognized_from']['#value']);
    $revenue_recognized_to = trim($form['revenue']['revenue_recognized_to']['#value']);
    $revenue_modified_from = trim($form['revenue']['revenue_modified_from']['#value']);
    $revenue_modified_to = trim($form['revenue']['revenue_modified_to']['#value']);

    $redirect_url = "revenue/transactions";

    if (!empty($revenue_agency)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_agency, 'agency');
    }
    if (!empty($revenue_fiscal_year)) {
        if (strtolower($revenue_fiscal_year) != 'all fiscal years')
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_fiscal_year, 'fy');
    }
    if (!empty($revenue_budget_fiscal_year)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_budget_fiscal_year, 'year');
    }
    if (!empty($revenue_fund_class)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_fund_class, 'fundcls');
    }
    if (!empty($revenue_revenue_category)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_revenue_category, 'revcat');
    }
    if (!empty($revenue_revenue_class)) {
        if ($revenue_revenue_class == $revenue_revenue_class_exact) {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_revenue_class, 'revclsnm_exact');
        } else {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_revenue_class, 'revclsnm');
        }
    }

    if (!empty($revenue_revenue_source)) {
        if ($revenue_revenue_source == $revenue_revenue_source_exact) {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_revenue_source, 'revsrcnm_exact');
        } else {
            $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_revenue_source, 'revsrcnm');
        }
    }
    if (!empty($revenue_funding_source)) {
        $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_funding_source, 'fundsrccode');
    }

    //URL for ranges
    $revenue_adopted_range_array = array('from' => $revenue_adopted_from, 'to' => $revenue_adopted_to, 'type' => 'amount');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_adopted_range_array, 'adopted_amt', TRUE);
    
    $revenue_recognized_range_array = array('from' => $revenue_recognized_from, 'to' => $revenue_recognized_to, 'type' => 'amount');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_recognized_range_array, 'amt', TRUE);
    
    $revenue_modified_range_array = array('from' => $revenue_modified_from, 'to' => $revenue_modified_to, 'type' => 'amount');
    $redirect_url .= _checkbook_advanced_search_generate_redirect_url($revenue_modified_range_array, 'modified_amt', TRUE);


    return $redirect_url;
}

/*
 * Constructs the URL for year value and type
 * @param: $year_arg (contains year type and year value separated by '~')
 */
function _checkbook_advanced_search_year_arg($year_arg)
{
    $year_args = explode('~', $year_arg);
    $year_type = $year_args[0];
    $year_id = $year_args[1];

    if ($year_type == 'fy') {
        $year_url = '/yeartype/B/year/' . $year_id;
    } elseif ($year_type == 'cy') {
        $year_url = '/yeartype/C/calyear/' . $year_id;
    }

    return $year_url;
}

function _checkbook_advanced_search_replaceSlash($string)
{
    return str_replace('/', '__', $string);
}

/**
 * Get field definition configurations.
 *
 * @return mixed
 *   configuration
 */
function _checkbook_advanced_search_get_field_def_configs() {
    $config_str = file_get_contents(realpath(drupal_get_path('module', 'checkbook_advanced_search')) . "/config/checkbook_advanced_search_field_definitions.json");

    $converter = new Json2PHPArray();
    $configuration = $converter->convert($config_str);

    return $configuration;
}

/**
 * Get field configurations.
 *
 * @param string $domain
 *   domain
 *
 * @return mixed
 *   configuration
 */
function _checkbook_advanced_search_get_field_configs($domain)
{
    $config_str = file_get_contents(realpath(drupal_get_path('module', 'checkbook_advanced_search')) . "/config/checkbook_advanced_search_".strtolower($domain)."_field_configurations.json");

    $converter = new Json2PHPArray();
    $configuration = $converter->convert($config_str);

    return $configuration;
}

/**
 * Get create alert wizard configuration.
 *
 * @return mixed
 *   configuration
 */
function _checkbook_advanced_search_create_alert_config()
{
    $config_str = file_get_contents(realpath(drupal_get_path('module', 'checkbook_advanced_search')) . "/config/checkbook_advanced_search_create_alert_config.json");

    $converter = new Json2PHPArray();
    $configuration = $converter->convert($config_str);

    return $configuration;
}

function _checkbook_advanced_search_create_alert_results_ajax($form, &$form_state) {

    /* Add new step to hidden field for persistence */
    $commands[] = ajax_command_invoke('input:hidden[name="step"]', 'val', array('customize_results'));

    /* Schedule Alert */
    $commands[] = ajax_command_invoke(NULL, 'onScheduleAlertNextClick', array('select_criteria'));

    //clear the page cache for create alerts ajax complete command to finish properly with memcache enabled
    $cache_tables = array_merge(module_invoke_all('flush_caches'), array('cache_page'));
    foreach ($cache_tables as $table) {
        cache_clear_all('*', $table, TRUE);
    }

    /* Load Customize Results Form */
    $redirect_url = '/' . checkbook_advanced_search_form_submit($form, $form_state) . '/createalert';
    $redirect_url = _checkbook_advanced_search_encode_url($redirect_url);
    $results_html = '<div class="create-alert-customize-results">';
    $results_html .= '<iframe visibility="hidden" src="'.$redirect_url.'" id="checkbook_advanced_search_result_iframe" frameBorder="0" style="overflow-x:hidden; overflow-y:scroll;height:600px;width:996px;"></iframe>';
    $results_html .= '</div>';
    $commands[] = ajax_command_replace('.create-alert-customize-results', $results_html);

    return array('#type' => 'ajax', '#commands' => $commands);

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
function _checkbook_advanced_search_encode_url($string) {

    $string = str_replace("%", "%25", $string);
    $string = str_replace("#", "%23", $string);
    $string = str_replace("<", "%3C", $string);
    $string = str_replace(">", "%3E", $string);
    $string = str_replace("?", "\%3F", $string);
//    $string = str_replace(".", "\%2E", $string);

    return $string;
}

function _checkbook_advanced_search_create_alert_instructions($step)
{
    $create_alert_config =  _checkbook_advanced_search_create_alert_config();
    $instructions = $create_alert_config[$step]['instructions'];
    return $instructions;
}

function _checkbook_advanced_search_create_alert_header($create_alert_config, $current_step)
{
    $header_html = '';

    foreach($create_alert_config as $step) {
        if(!isset($step['title'])) continue;
        $header_html .= $header_html != '' ? "<span class='inactive'>&nbsp;|&nbsp;</span>" : "";
        $header_html .= $current_step == $step['name'] ? "<span class='active'>" : "<span class='inactive'>";
        $header_html .= $step['title'];
        $header_html .= "</span>";
    }

    $header_html = "<span class='create-alert-header'>".$header_html."</span>";
    return $header_html;
}
