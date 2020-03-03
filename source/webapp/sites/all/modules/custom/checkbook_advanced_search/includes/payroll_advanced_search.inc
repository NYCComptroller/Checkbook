<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (C) 2019 New York City
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

/**
 * Constructs the URL for Payroll based on the input by the users
 *
 * @param $form
 * @param $form_state
 * @return string
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
  if (!empty($payroll_agency) && $data_source != Datasource::NYCHA) {
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

  if($data_source == DataSource::NYCHA){
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
