<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

use Drupal\checkbook_infrastructure_layer\Constants\Common\CheckbookDomain;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Drupal\checkbook_log\LogHelper;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\checkbook_project\MwbeUtilities\MappingUtil;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\data_controller\Common\Object\Converter\Handler\Json2PHPArray;

/**
 * Advanced search submit form handler, used to figure which submit button
 * has been clicked and invoke the respective functions that generate the
 * redirect URL with the necessary parameters
 * @param $form
 * @param $form_state
 * @return string
 */
function checkbook_advanced_search_form_submit($form, &$form_state)
{
    switch ($form_state->getTriggeringElement()['#name']) {//switch ($form_state['clicked_button']['#name']) {
        case "budget_submit":
          $data_source = $form_state->getValue('budget_advanced_search_domain_filter') ?? Datasource::CITYWIDE;
          $redirect_url = _checkbook_advanced_search_budget_submit($form, $form_state, $data_source);
          $form_state->setResponse(new TrustedRedirectResponse($redirect_url));
          break;
        case "revenue_submit":
          $data_source = $form_state->getValue('revenue_advanced_search_domain_filter') ?? Datasource::CITYWIDE;
          $redirect_url = _checkbook_advanced_search_revenue_submit($form, $form_state, $data_source);
          $form_state->setResponse(new TrustedRedirectResponse($redirect_url));
          break;
        case "spending_submit":
          $data_source = $form_state->getValue('spending_advanced_search_domain_filter') ?? Datasource::CITYWIDE;
          $redirect_url = _checkbook_advanced_search_spending_submit($form, $form_state, $data_source);
          $form_state->setResponse(new TrustedRedirectResponse($redirect_url));
          break;
        case "contracts_submit":
          $data_source = $form_state->getValue('contracts_advanced_search_domain_filter') ?? Datasource::CITYWIDE;
          $redirect_url = _checkbook_advanced_search_contracts_submit($form, $form_state, $data_source);
          $form_state->setResponse(new TrustedRedirectResponse($redirect_url));
          break;
        case "payroll_submit":
          $data_source = $form_state->getValue('payroll_advanced_search_domain_filter') ?? Datasource::CITYWIDE;
          $redirect_url = _checkbook_advanced_search_payroll_submit($form, $form_state,$data_source);
          $form_state->setResponse(new TrustedRedirectResponse($redirect_url));
          break;
        case "budget_next":
          $data_source = $form_state->getValue('budget_advanced_search_domain_filter') ?? Datasource::CITYWIDE;
          $redirect_url = _checkbook_advanced_search_budget_submit($form, $form_state, $data_source);
          $form_state->set('alert_form_step_num', 2);
          $form_state->set('alert_form_state', 'customize_results');
          $form_state->set('alert_form_redirect_url', $redirect_url);
          return $redirect_url;
          break;
        case "revenue_next":
          $data_source = $form_state->getValue('revenue_advanced_search_domain_filter') ?? Datasource::CITYWIDE;
          $redirect_url = _checkbook_advanced_search_revenue_submit($form, $form_state, $data_source);
          $form_state->set('alert_form_step_num', 2);
          $form_state->set('alert_form_state', 'customize_results');
          $form_state->set('alert_form_redirect_url', $redirect_url);
          return $redirect_url;
          break;
        case "spending_next":
          $data_source = $form_state->getValue('spending_advanced_search_domain_filter') ?? Datasource::CITYWIDE;
          $redirect_url = _checkbook_advanced_search_spending_submit($form, $form_state, $data_source);
          $form_state->set('alert_form_step_num', 2);
          $form_state->set('alert_form_state', 'customize_results');
          $form_state->set('alert_form_redirect_url', $redirect_url);
          return $redirect_url;
          break;
        case "contracts_next":
          $data_source = $form_state->getValue('contracts_advanced_search_domain_filter') ?? Datasource::CITYWIDE;
          $redirect_url = _checkbook_advanced_search_contracts_submit($form, $form_state, $data_source);
          $form_state->set('alert_form_step_num', 2);
          $form_state->set('alert_form_state', 'customize_results');
          $form_state->set('alert_form_redirect_url', $redirect_url);
          return $redirect_url;
          break;
        case "payroll_next":
          $data_source = $form_state->getValue('payroll_advanced_search_domain_filter') ?? Datasource::CITYWIDE;
          $redirect_url = _checkbook_advanced_search_payroll_submit($form, $form_state, $data_source);
          $form_state->set('alert_form_step_num', 2);
          $form_state->set('alert_form_state', 'customize_results');
          $form_state->set('alert_form_redirect_url', $redirect_url);
          return $redirect_url;
    }
}

/**
 * Get year_value and id using the data controller
 * To populate Year Filter drop downs on Advanced Search form
 *
 * @param null $domain
 * @param null $form
 * @param string $data_source
 * @return array
 */
function _checkbook_advanced_search_get_year($domain = null, $form = null, $data_source = Datasource::CITYWIDE)
{
  try {
    $fiscalYears = CheckbookDateUtil::getFiscalYearOptionsRange($data_source, $domain);
    $calendarYears = CheckbookDateUtil::getCalendarYearOptionsRange($data_source);
    $fiscal_year_array = [];
    $calendar_year_array = [];

    //Fiscal Years
    foreach ($fiscalYears as $row) {
      if ($domain != CheckbookDomain::$REVENUE && $domain != CheckbookDomain::$BUDGET) {
        $fiscal_year_array['fy~' . $row['year_id']] = 'FY ' . $row['year_value'];
      } else {
        if ($form == 'datafeeds')
          $fiscal_year_array[$row['year_value']] = $row['year_value'];
        else
          $fiscal_year_array[$row['year_id']] = $row['year_value'];
      }
    }

    if (isset($fiscal_year_array)) {
      arsort($fiscal_year_array);
    }

    if ($domain == CheckbookDomain::$CONTRACTS || $domain == CheckbookDomain::$SPENDING) {
      $fiscal_year_array = array_reverse($fiscal_year_array, true);
      $fiscal_year_array['fy~all'] = 'All Years';
      $fiscal_year_array = array_reverse($fiscal_year_array, true);
    }

    if ($domain == CheckbookDomain::$PAYROLL) {
      //Calendar Years
      foreach ($calendarYears as $row) {
        $calendar_year_array['cy~' . $row['year_id']] = 'CY ' . $row['year_value'];
      }
      if (isset($calendar_year_array)) {
        arsort($calendar_year_array);
      }

      if ($data_source == Datasource::NYCHA) {
        $fiscal_year_array = $calendar_year_array;
      } else {
        $fiscal_year_array = isset($calendar_year_array) ? array_merge($calendar_year_array, $fiscal_year_array) : $fiscal_year_array;
      }
    }

    return $fiscal_year_array;

  } catch (Exception $e) {
    LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
  }
}

/**
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
  foreach ($minority_cat_map as $category => $minority_types) {
    $results[implode('~', $minority_types)] = $category;
  }
  return $results;
}

/**
 * Get expense name and id using the data controller
 * @return array
 * @return array|void
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
    LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
    return;
  }
}

/**
 * Gets the subcontract_approval_status and id using the data controller
 * @param null $attributes
 * @return mixed
 */
function _checkbook_advanced_search_get_subcontract_approval_status_and_id($attributes = NULL)
{
  try {
    $dataController = data_controller_get_instance();
    $values = $dataController->queryDataset('checkbook:subcontract_approval_status', array('aprv_sta_id', 'aprv_sta_value'), NULL, 'sort_order', 0, 10, NULL);

    if ($attributes) {
      $statuses = array('title' => 'Select Status');
    } else {
      $statuses = array('Select Status');
    }
    foreach ($values as $value) {
      if ($value['aprv_sta_id'] != "7") {
        if ($attributes) {
          $statuses[$value['aprv_sta_id']] = array('title' => $value['aprv_sta_value']);
        } else {
          $statuses[$value['aprv_sta_id']] = FormattingUtilities::_ckbk_excerpt($value['aprv_sta_value']);
        }
      }
    }
    return $statuses;
  } catch (Exception $e) {
    LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
    return;
  }
}


/**
 * Gets the scntrc_status_name, scntrc_status(id), and display_flag using the data controller
 * @param null $attributes
 * @return mixed
 */
function _checkbook_advanced_search_get_contract_includes_subvendors_status($attributes = NULL)
{
  try {
    $dataController = data_controller_get_instance();
    $values = $dataController->queryDataset('checkbook:ref_subcontract_status',
      array('scntrc_status', 'scntrc_status_name', 'display_flag'),
      NULL, 'sort_order', 0, 10, NULL);

    if ($attributes) {
      $statuses = array('title' => 'Select Status');
    } else {
      $statuses = array('Select Status');
    }
    foreach ($values as $value) {
      if ($attributes) {
        $statuses[$value['scntrc_status']] = array('title' => $value['scntrc_status_name']);
      } else {
        if ($value['display_flag'] == 1) {
          $statuses[$value['scntrc_status']] = FormattingUtilities::_ckbk_excerpt($value['scntrc_status_name']);
        }
      }
    }
    return $statuses;
  } catch (Exception $e) {
    LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
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
  if (isset($from_value) && isset($to_value)) {

    // 1. 'Greater than $100M' ==> X > 100000000
    if ($from_value > 100000000 && $to_value > 100000000) {
      $amount_ids[] = 1;
    }
    // 2. '$51M - $100M' ==> X > 50000000 AND X <= 100000000
    if (($from_value > 100000000 && $from_value <= 100000000) || ($to_value > 100000000 && $to_value <= 100000000)) {
      $amount_ids[] = 2;
    }
    // 3. '$26M - $50M' ==> X > 25000000 AND X <= 50000000
    if (($from_value > 25000000 && $from_value <= 50000000) || ($to_value > 25000000 && $to_value <= 50000000)) {
      $amount_ids[] = 3;
    }
    // 4. '$11M - $25M' ==> X > 10000000 AND X <= 25000000
    if (($from_value > 10000000 && $from_value <= 25000000) || ($to_value > 10000000 && $to_value <= 25000000)) {
      $amount_ids[] = 4;
    }
    // 5. '$1M - $10M' ==> x >= 1000000 AND X <= 10000000
    if (($from_value >= 1000000 && $from_value <= 10000000) || ($to_value >= 1000000 && $to_value <= 10000000)) {
      $amount_ids[] = 5;
    }
    // 6. 'Less than $1M' ==> X < 1000000
    if ($from_value < 1000000 && $to_value < 1000000) {
      $amount_ids[] = 6;
    }
  }

  if (count($amount_ids) > 0) {
    $amount_param = implode("~", $amount_ids);
    $redirect_url = '/' . "curamtid" . "/" . $amount_param;
  }
  return $redirect_url;
}

/**
 * this functiocheckbook_advanced_searchn is used to generate the URL based on what the users have selected on advanced search form
 * @param $value
 * @param string $url_param
 * @param bool $is_range
 * @return string
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

/**
 * Get revenue category and id using the data controller
 *
 * @return array|void
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
    LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
    return;
  }
}

/**
 * Get fund class name and id using the data controller
 *
 * @return array|void
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
    LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
    return;
  }
}

/**
 * Get funding class name and code using the data controller
 *
 * @return array|void
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
    LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
    return;
  }
}

/*
 * Get document id using the data controller
 * @return array
 */
/**
 * @return array|void
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
    LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
    return;
  }
}

/**
 * Get payroll frequency using the data controller
 *
 * @return array|void
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
    LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
    return;
  }
}

/**
 * Get spending category name and id using data controller
 *
 * @param string $data_source
 * @return array|void
 */
function _checkbook_advanced_search_get_expensetype_and_id($data_source = Datasource::CITYWIDE)
{
  try {
    $dataController = data_controller_get_instance();
    if ($data_source == Datasource::NYCHA) {
      $query = "SELECT spending_category_id, spending_category_code,  display_spending_category_name FROM ref_spending_category ORDER BY display_spending_category_name";
      $data = _checkbook_project_execute_sql_by_data_source($query, $data_source);
      $results = array('' => "Total Spending");
      foreach ($data as $value) {
        $results[$value['spending_category_id']] = $value['display_spending_category_name'];
      }
    } else {
      $data = $dataController->queryDataset($data_source . ':spending_category', array(
        'spending_category_id', 'spending_category_name'
      ), NULL, 'display_order');
      $results = array('' => "Total Spending");
      foreach ($data as $row) {
        if ($row['spending_category_id'] != 6)
          $results[$row['spending_category_id']] = $row['spending_category_name'];
      }
    }
    return array_unique($results);
  } catch (Exception $e) {
    LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
    return;
  }
}

/**
 * Get contract type name and id using the data controller
 *
 * @param string $data_source
 * @return array|void
 */
function _checkbook_advanced_search_get_contract_type_and_id($data_source = Datasource::CITYWIDE)
{
  try {
    $dataController = data_controller_get_instance();
    $agreement_types = $dataController->queryDataset($data_source . ':agreement_type', array('agreement_type_name', 'agreement_type_id', 'agreement_type_code'), NULL, 'agreement_type_name', 0, NULL, NULL);
    $agreement_type_val = array('Select Contract Type');
    $agreement_type_val_option_attributes = array('title' => 'Select Contract Type');
    foreach ($agreement_types as $value) {
      $keys = 'id=>' . $value['agreement_type_id'] . '~code=>' . $value['agreement_type_code'];
      $agreement_type_val[$keys] = $value['agreement_type_name'];
      $agreement_type_val_option_attributes[$keys] = array('title' => $value['agreement_type_name']);
    }
    return array('options' => $agreement_type_val, 'option_attributes' => $agreement_type_val_option_attributes);
  } catch (Exception $e) {
    LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
    return;
  }
}

/**
 * Get Responsibility center options for NYCHA Contracts
 *
 * @param string $data_source
 * @param bool $feeds
 * @return array|void
 */
function _checkbook_advanced_search_get_responsibility_centers($data_source = Datasource::NYCHA, $feeds = false)
{
  try {
    // Query update to remove null and junk data from drop down display
    $query = "SELECT DISTINCT responsibility_center_id, responsibility_center_code, responsibility_center_description FROM ref_responsibility_center
                  WHERE responsibility_center_description IS NOT NULL AND responsibility_center_id NOT IN (1032,2066)
                  ORDER BY responsibility_center_description";
    $results = _checkbook_project_execute_sql_by_data_source($query, $data_source);

    $res_center_key_val_option_attributes = array('title' => 'Select Responsibility Center');
    $res_center_key_val_options = array('Select Responsibility Center');

    foreach ($results as $value) {
      if ($feeds) {
        $text = $value['responsibility_center_description'] . ' [' . $value['responsibility_center_code'] . ']';
        $res_center_key_val_option_attributes[$text] = array('title' => $text);
        $res_center_key_val_options[$text] = FormattingUtilities::_ckbk_excerpt($text);
      } else {
        $keys = $value['responsibility_center_id'];
        $res_center_key_val_option_attributes[$keys] = array('title' => $value['responsibility_center_description']);
        $res_center_key_val_options[$keys] = FormattingUtilities::_ckbk_excerpt($value['responsibility_center_description']);
      }
    }

    return array('options' => $res_center_key_val_options, 'option_attributes' => $res_center_key_val_option_attributes);
  } catch (Exception $e) {
    LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
    return;
  }
}

/**
 * Get Contract Type options for NYCHA
 * @param string $data_source
 * @param bool $feeds
 * @return array|void
 */
function _checkbook_advanced_search_get_nycha_contract_types($data_source = Datasource::NYCHA, $feeds = false)
{
  try {
    $query = "SELECT DISTINCT contract_type_id, contract_type_code, contract_type_name, contract_type_description FROM ref_contract_type ORDER BY contract_type_name";
    $results = _checkbook_project_execute_sql_by_data_source($query, $data_source);

    $contract_type_key_val_option_attributes = array('title' => 'Select Contract Type');
    $contract_type_key_val_options = array('Select Contract Type');

    foreach ($results as $value) {
      if ($feeds) {
        $text = $value['contract_type_name'] . ' [' . $value['contract_type_code'] . ']';
        $contract_type_key_val_option_attributes[$text] = array('title' => $text);
        $contract_type_key_val_options[$text] = FormattingUtilities::_ckbk_excerpt($text);
      } else {
        $keys = 'id=>' . $value['contract_type_id'] . '~code=>' . $value['contract_type_code'];
        $contract_type_key_val_option_attributes[$keys] = array('title' => $value['contract_type_name']);
        $contract_type_key_val_options[$keys] = FormattingUtilities::_ckbk_excerpt($value['contract_type_name']);
      }
    }
    return array('options' => $contract_type_key_val_options, 'option_attributes' => $contract_type_key_val_option_attributes);
  } catch (Exception $e) {
    LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
    return;
  }
}


/**
 * Get Nycha Funding Source options for NYCHA
 * @param string $data_source
 * @param bool $feeds
 * @return array|void
 */
function checkbook_advanced_search_get_nycha_funding_sources($data_source = Datasource::NYCHA, $feeds = false)
{
  try {
    $query = "SELECT funding_source_id, funding_source_code, display_funding_source_descr FROM ref_funding_source
              ORDER BY display_funding_source_descr";
    $results = _checkbook_project_execute_sql_by_data_source($query, $data_source);

    $fundsrc_key_val_option_attributes = array('title' => 'Select Funding Source');
    $fundsrc_key_val_options = array('Select Funding Source');

    foreach ($results as $value) {
      if ($feeds) {
        $text = $value['display_funding_source_descr'] . ' [' . $value['funding_source_code'] . ']';
        $fundsrc_key_val_option_attributes[$text] = array('title' => $text);
        $fundsrc_key_val_options[$text] = FormattingUtilities::_ckbk_excerpt($text);
      } else {
        $keys = $value['funding_source_id'];
        $fundsrc_key_val_option_attributes[$keys] = array('title' => $value['display_funding_source_descr']);
        $fundsrc_key_val_options[$keys] = FormattingUtilities::_ckbk_excerpt($value['display_funding_source_descr']);
      }
    }
    return array('options' => $fundsrc_key_val_options, 'option_attributes' => $fundsrc_key_val_option_attributes);

  } catch (Exception $e) {
    LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
    return;
  }
}

/**
 * @param $from
 * @param $to
 * @param $url_param
 * @return string
 */
function _checkbook_advanced_search_add_payroll_range_filter_param($from, $to, $url_param)
{
  $redirect_url = "";
  if ($from == "" && $to == "") return $redirect_url;
  $parameters = array();
  if ($from < 20000 || $to < 20000)
    $parameters[] = 1;
  if (($from >= 21000 && $from <= 50000) || ($to >= 21000 && $to <= 50000))
    $parameters[] = 2;
  if (($from >= 51000 && $from <= 100000) || ($to >= 51000 && $to <= 100000))
    $parameters[] = 3;
  if (($from >= 101000 && $from <= 250000) || ($to >= 101000 && $to <= 250000))
    $parameters[] = 4;
  if ($to > 250000 || $from > 250000)
    $parameters[] = 5;

  if (!empty($parameters)) {
    $redirect_url = "/" . $url_param . "/";
    $redirect_url .= implode('~', $parameters);
  }

  return $redirect_url;
}

/**
 * Constructs the URL for year value and type
 *
 * @param string $year_arg // (contains year type and year value separated by '~')
 * @return string
 */
function _checkbook_advanced_search_year_arg(string $year_arg)
{
  $year_args = explode('~', $year_arg);
  $year_type = $year_args[0];
  $year_id = $year_args[1];

  if ($year_type == 'fy') {
    $year_url = '/yeartype/B/year/' . $year_id;
  } elseif ($year_type == 'cy') {
    $year_url = '/yeartype/C/calyear/' . $year_id . '/year/' . $year_id;
  }

  return $year_url;
}

/**
 * @param mixed $string
 * @return mixed
 */
function _checkbook_advanced_search_replaceSlash($string)
{

  $string = str_replace(':', '@Q', $string);
  $string = str_replace('/', '__', $string);
  return  $string;
}

/**
 * Get field definition configurations.
 *
 * @return mixed
 *   configuration
 */
function _checkbook_advanced_search_get_field_def_configs($domain)
{

  $checkbook_advanced_search_path = Drupal::service('extension.list.module')->getPath('checkbook_advanced_search');
  $config_str = file_get_contents(realpath($checkbook_advanced_search_path) . "/src/config/checkbook_advanced_search_" . strtolower($domain) . "_field_configurations.json");

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
  $config_str = file_get_contents(realpath((new Drupal\Core\Extension\ExtensionPathResolver)->getPath('module', 'checkbook_advanced_search')) . "/config/checkbook_advanced_search_" . strtolower($domain) . "_field_configurations.json");

  $converter = new Json2PHPArray();
  $configuration = $converter->convert($config_str);
  return $configuration;
}


/**
 * @param $form
 * @param $form_state
 * @return array
 */
function _checkbook_advanced_search_create_alert_results_ajax($form, &$form_state)
{

  /* Add new step to hidden field for persistence */
  $commands[] = ajax_command_invoke('input:hidden[name="step"]', 'val', array('customize_results'));

  /* Schedule Alert */
  $commands[] = ajax_command_invoke(NULL, 'onScheduleAlertNextClick', array('select_criteria'));

  //clear the page cache for create alerts ajax complete command to finish properly with memcache enabled
  $cache_tables = array_merge(\Drupal::moduleHandler()->invokeAll('flush_caches'), array('cache_page'));
  foreach ($cache_tables as $table) {
    cache_clear_all('*', $table, TRUE);
  }

  /* Load Customize Results Form */
  $redirect_url = '/' . checkbook_advanced_search_form_submit($form, $form_state) . '/createalert';
  $redirect_url = _checkbook_advanced_search_encode_url($redirect_url);
  $results_html = '<div class="create-alert-customize-results">';
  $results_html .= '<iframe visibility="hidden" src="' . $redirect_url . '" id="checkbook_advanced_search_result_iframe" frameBorder="0" style="overflow-x:hidden; overflow-y:scroll;height:600px;width:960px;"></iframe>';
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
function _checkbook_advanced_search_encode_url($string)
{
  $string = str_replace("%", "%25", $string);
  $string = str_replace("#", "%23", $string);
  $string = str_replace("&", "%26", $string);
  $string = str_replace("<", "%3C", $string);
  $string = str_replace(">", "%3E", $string);
  $string = str_replace("+", "%2B", $string);
  $string = str_replace("?", "\%3F", $string);
  return $string;
}


/**
 * Returns input value from drupal form
 *
 * @param $form_state
 * @param $filter_dimension
 * @param $field_name
 * @return string
 */
function _checkbook_get_selected_value($form_state, $filter_dimension, $field_name)
{
  return trim($form_state->getValue($filter_dimension . '_' . $field_name));
}
