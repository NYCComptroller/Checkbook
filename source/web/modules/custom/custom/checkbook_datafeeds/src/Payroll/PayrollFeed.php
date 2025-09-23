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

namespace Drupal\checkbook_datafeeds\Payroll;

use Drupal\checkbook_datafeeds\Utilities\FeedConstants;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;

class PayrollFeed extends PayrollFeedBase
{
  protected string $data_source = 'citywide';
  protected string $type_of_data = 'Payroll';
  protected $filtered_columns_container = 'column_select_expense';

  public function __construct($ds = 'citywide')
  {
    $this->data_source = $ds;
    $this->type_of_data = $this->data_source == Datasource::NYCHA ? 'Payroll_NYCHA' : 'Payroll';
  }

  protected function _process_user_criteria_by_datasource()
  {
    if ($this->data_source == Datasource::NYCHA) {
      $other_government_entity = 'NEW YORK CITY HOUSING AUTHORITY[996]';
      $this->form['filter']['other_government_entity'] = array(
        '#markup' => '<div><strong>Other Government Entities:</strong> ' . $other_government_entity . '</div>',
      );
      $this->user_criteria['Other Government Entity'] = $other_government_entity;
      $this->formatted_search_criteria['Other Government Entities'] = $other_government_entity;
    } else {
      if ($this->form_state->getValue('agency')) {
        $this->form['filter']['agency'] = array(
          '#markup' => '<div><strong>Agency:</strong> ' . $this->form_state->getValue('agency')  . '</div>',
        );
        $this->user_criteria['Agency'] = $this->form_state->getValue('agency');
        $this->formatted_search_criteria['Agency'] = $this->form_state->getValue('agency');
      }
    }

    if ($this->form_state->getValue('title')) {
      $this->form['filter']['title'] = array(
        '#markup' => '<div><strong>Title:</strong> ' . $this->form_state->getValue('title')  . '</div>',
      );
      $this->user_criteria['Title'] = $this->form_state->getValue('title');
      $this->formatted_search_criteria['Title'] = $this->form_state->getValue('title');
    }

    // PAYROLL STARTS
    $this->_process_user_criteria_by_datasource_ranged_amount_field('otherfrom', 'otherto', 'other_pay', 'Other Pay', 'Other Payments');

    $this->_process_user_criteria_by_datasource_ranged_amount_field('grossfrom', 'grossto', 'gross_pay', 'Gross Pay');

    $this->_process_user_criteria_by_datasource_ranged_amount_field('totalgrossfrom', 'totalgrossto', 'total_gross', 'Gross Pay YTD');

    $this->_process_user_criteria_by_datasource_single_field_and_check('salary_type', 'salary_type', 'Salary Type');

    $this->_process_user_criteria_by_datasource_ranged_amount_field('salaryfrom', 'salaryto', 'annual_salary', 'Amount');

    $this->_process_user_criteria_by_datasource_ranged_amount_field('basepayfrom', 'basepayto', 'base_pay', 'Base Pay');

    $this->_process_user_criteria_by_datasource_ranged_amount_field('overtimefrom', 'overtimeto', 'overtime_pay', 'Overtime Pay', 'Overtime Payments');

    $this->_process_user_criteria_by_datasource_single_field_and_check('payfrequency', 'payfrequency', 'Pay Frequency');

    if (startsWith($this->form_state->getValue('year'), 'F')) {
      $this->form['filter']['year'] = array(
        '#markup' => '<div><strong>Year:</strong> ' . $this->form_state->getValue('year') . '</div>',
      );
      $this->user_criteria['Fiscal Year'] = $this->form_state->getValue('year');
      $this->formatted_search_criteria['Year'] = $this->form_state->getValue('year');
    } elseif (startsWith($this->form_state->getValue('year'), 'C')) {
      $this->form['filter']['year'] = array(
        '#markup' => '<div><strong>Year:</strong> ' . $this->form_state->getValue('year') . '</div>',
      );
      $this->user_criteria['Calendar Year'] = $this->form_state->getValue('year');
      $this->formatted_search_criteria['Year'] = $this->form_state->getValue('year');
    }

    $this->_process_user_criteria_by_datasource_ranged_date_field('paydatefrom', 'paydateto', 'pay_date', 'Pay Date');

    $this->_process_user_criteria_by_datasource_ranged_amount_field('payratefrom', 'payrateto', 'pay_rate', 'Pay Rate');

    //PAYROLL ENDS

    if ($this->form_state->getValue('dept') && $this->form_state->getValue('dept') != 'Select Department') {
      $this->_process_user_criteria_by_datasource_single_field('dept', 'department', 'Department');
    }
    if ($this->form_state->getValue('expense_category') && $this->form_state->getValue('expense_category') != 'Select Expense Category') {
      $this->_process_user_criteria_by_datasource_single_field('expense_category', 'expense_category', 'Expense Category');
    }

    $this->_process_user_criteria_by_datasource_single_field_and_check('budget_code', 'budget_code', 'Budget Code');

    if ($this->form_state->getValue('conditional_category') !== "0" && $this->form_state->getValue('conditional_category') >= 2020) {
      $this->_process_user_criteria_by_datasource_single_field('conditional_category', 'conditional_category', 'Conditional Category');
    }

    $this->_process_user_criteria_by_datasource_single_field_and_check('fiscal_year', 'fiscal_year', 'Year');

    $this->_process_user_criteria_by_datasource_ranged_amount_field('currentfrom', 'currentto', 'current_budget', 'Modified Budget');

    $this->_process_user_criteria_by_datasource_ranged_amount_field('adoptedfrom', 'adoptedto', 'adopted_budget', 'Adopted Budget');

    $this->_process_user_criteria_by_datasource_ranged_amount_field('preencumberedfrom', 'preencumberedto', 'preencumbered', 'Pre-encumbered');

    $this->_process_user_criteria_by_datasource_ranged_amount_field('encumberedfrom', 'encumberedto', 'encumbered', 'Encumbered');

    $this->_process_user_criteria_by_datasource_ranged_amount_field('accruedexpensefrom', 'accruedexpenseto', 'accrued_expense', 'Accrued Expense');

    $this->_process_user_criteria_by_datasource_ranged_amount_field('cashfrom', 'cashto', 'cash_amount', 'Cash Expense');

    $this->_process_user_criteria_by_datasource_ranged_amount_field('postadjustmentsfrom', 'postadjustmentsto', 'post_adjustments', 'Post Adjustments');
  }

  protected function _process_user_criteria_by_datasource_single_field($field_name, $form_filter_key, $visual_field_name, $user_criteria_name = null) {
    $this->form['filter'][$form_filter_key] = array('#markup' => '<div><strong>'.$visual_field_name.':</strong> ' . $this->form_state->getValue($field_name) . '</div>');
    if (is_null($user_criteria_name)) {
      $this->user_criteria[$visual_field_name] = $this->form_state->getValue($field_name);
    } else {
      $this->user_criteria[$user_criteria_name] = $this->form_state->getValue($field_name);
    }
    $this->formatted_search_criteria[$visual_field_name] = $this->form_state->getValue($field_name);
  }

  protected function _process_user_criteria_by_datasource_single_field_and_check($field_name, $form_filter_key, $visual_field_name, $user_criteria_name = null) {
    if ($this->form_state->getValue($field_name)) {
      $this->_process_user_criteria_by_datasource_single_field($field_name, $form_filter_key, $visual_field_name, $user_criteria_name);
    }
  }

  protected function _process_user_criteria_by_datasource_ranged_field($start_field_name, $end_field_name, $form_filter_id, $visual_field_name, $field_type = 'amount', $formatted_search_criteria_key=null) {
    $and = '';
    if ($field_type == 'amount') {
      $user_criteria_greater_than_id = $visual_field_name . ' Greater Than';
      $user_criteria_less_than_id = $visual_field_name . ' Less Than';
      $greater_than_equal_text = 'Greater Than Equal to: $';
      $less_than_equal_text = 'Less Than Equal to: $';
      $and = 'and ';
    } else {
      $user_criteria_greater_than_id = $visual_field_name . ' After';
      $user_criteria_less_than_id = $visual_field_name . ' Before';
      $greater_than_equal_text = 'From: ';
      $less_than_equal_text = 'To: ';
    }

    $formatted_search_criteria_id = $formatted_search_criteria_key ?? $visual_field_name;

    if (($this->form_state->getValue($start_field_name) || ($field_type == 'amount' && $this->form_state->getValue($start_field_name) === "0")) && ($this->form_state->getValue($end_field_name) || ($field_type == 'amount' && $this->form_state->getValue($end_field_name) === "0"))) {
      $this->form['filter'][$form_filter_id] = array('#markup' => '<div><strong>'.$visual_field_name.':</strong> '.$greater_than_equal_text . $this->form_state->getValue($start_field_name) . ' '.$and . $less_than_equal_text . $this->form_state->getValue($end_field_name) . '</div>');
      $this->user_criteria[$user_criteria_greater_than_id] = $this->form_state->getValue($start_field_name);
      $this->user_criteria[$user_criteria_less_than_id] = $this->form_state->getValue($end_field_name);
      $this->formatted_search_criteria[$formatted_search_criteria_id] = $greater_than_equal_text . ' ' . $this->form_state->getValue($start_field_name) . ' and ' . $less_than_equal_text . ' ' . $this->form_state->getValue($end_field_name);
    } elseif (!$this->form_state->getValue($start_field_name) && ($this->form_state->getValue($end_field_name) || ($field_type == 'amount' && $this->form_state->getValue($end_field_name) === "0"))) {
      $this->form['filter'][$form_filter_id] = array('#markup' => '<div><strong>'.$visual_field_name.':</strong> ' . $less_than_equal_text . $this->form_state->getValue($end_field_name) . '</div>');
      $this->user_criteria[$user_criteria_less_than_id] = $this->form_state->getValue($end_field_name);
      $this->formatted_search_criteria[$formatted_search_criteria_id] = $less_than_equal_text . ' ' . $this->form_state->getValue($end_field_name);
    } elseif (($this->form_state->getValue($start_field_name) || ($field_type == 'amount' && $this->form_state->getValue($start_field_name) === "0")) && !$this->form_state->getValue($end_field_name)) {
      $this->form['filter'][$form_filter_id] = array('#markup' => '<div><strong>'.$visual_field_name.':</strong> ' . $greater_than_equal_text . $this->form_state->getValue($start_field_name) . '</div>');
      $this->user_criteria[$user_criteria_greater_than_id] = $this->form_state->getValue($start_field_name);
      $this->formatted_search_criteria[$formatted_search_criteria_id] = $greater_than_equal_text . ' ' . $this->form_state->getValue($start_field_name);
    }
  }

  protected function _process_user_criteria_by_datasource_ranged_date_field($start_field_name, $end_field_name, $form_filter_id, $visual_field_name) {
    $this->_process_user_criteria_by_datasource_ranged_field($start_field_name, $end_field_name, $form_filter_id, $visual_field_name, 'date');
  }

  protected function _process_user_criteria_by_datasource_ranged_amount_field($start_field_name, $end_field_name, $form_filter_id, $visual_field_name, $formatted_search_criteria_key=null) {
    $this->_process_user_criteria_by_datasource_ranged_field($start_field_name, $end_field_name, $form_filter_id, $visual_field_name, 'amount', $formatted_search_criteria_key);
  }

  protected function _process_datasource_values() {
    $values = $this->form_state->get(['step_information', 'payroll', 'stored_values']);

    if (startsWith($values['year'], 'F')) {
      $this->criteria['value']['fiscal_year'] = ltrim($values['year'], 'FY ');
    } elseif (startsWith($values['year'], 'C')) {
      $this->criteria['value']['calendar_year'] = ltrim($values['year'], 'CY ');
    }

    if ($this->data_source !== Datasource::NYCHA && $values['agency'] != 'Citywide (All Agencies)') {
        preg_match($this->bracket_value_pattern, $values['agency'], $amatches);
        $this->criteria['value']['agency_code'] = trim($amatches[1], '[ ]');
    }

    if ($values['title']) {
      $title_exact = $_POST['title_exact'];
      $title = $values['title'];

      if ($title_exact && $title_exact == $title) {
        $this->criteria['value']['title_exact'] = $title_exact;
      }
      else {
        $this->criteria['value']['title'] = $title;
      }
    }

    if ($values['payfrequency'] != '') {
      $this->criteria['value']['pay_frequency'] = $values['payfrequency'];
    }

    $this->_process_ranged_datasource_values('basepayfrom', 'basepayto', 'base_pay', $values);

    $this->_process_ranged_datasource_values('overtimefrom', 'overtimeto', 'overtime_payments', $values);

    $this->_process_ranged_datasource_values('otherfrom', 'otherto', 'other_payments', $values);

    $this->_process_ranged_datasource_values('grossfrom', 'grossto', 'gross_pay', $values);

    $this->_process_ranged_datasource_values('paydatefrom', 'paydateto', 'pay_date', $values);

    $this->_process_ranged_datasource_values('totalgrossfrom', 'totalgrossto', 'gross_pay_ytd', $values);

    if ($values['salary_type']) {
      $this->criteria['value']['amount_type'] = $values['salary_type'];
    }

    $this->_process_ranged_datasource_values('salaryfrom', 'salaryto', 'amount', $values);
  }

  /**
   * This function will process ranged values for datasource and place inside criteria
   *
   * @param $start_field_name
   * @param $end_field_name
   * @param $criteria_key
   * @param $pvalues
   *
   * @return void
   */
  protected function _process_ranged_datasource_values($start_field_name, $end_field_name, $criteria_key, $pvalues=null) {
    if (is_null($pvalues)) {
      $start = $this->form_state->getValue($start_field_name);
      $end = $this->form_state->getValue($end_field_name);
    } else {
      $start = $pvalues[$start_field_name];
      $end = $pvalues[$end_field_name];
    }
    if ($start !== '' || $end !== '') {
      $this->criteria['range'][$criteria_key] = array(
        checknull($start),
        checknull($end),
      );
    }
  }

  protected function _validate_by_datasource(&$form, &$form_state)
  {
    $submitted_data_source = $form_state->getValue('datafeeds-payroll-domain-filter');
    $oge_response_columns = $form_state->getValue('oge_column_select');
    $response_columns = $form_state->getValue('column_select');

    // Base Pay:
    checkbook_datafeeds_check_ranged_amounts($form_state, 'basepayfrom', 'basepayto', 'Base Pay', 'Base Pay From', 'Base Pay To');

    // Overtime Pay:
    checkbook_datafeeds_check_ranged_amounts($form_state, 'overtimefrom', 'overtimeto', 'Overtime Payments', 'Overtime Payments From', 'Overtime Payments To');

    // Other Pay:
    checkbook_datafeeds_check_ranged_amounts($form_state, 'otherfrom', 'otherto', 'Other Payments', 'Other Payments From', 'Other Payments To');

    // Gross Pay:
    checkbook_datafeeds_check_ranged_amounts($form_state, 'grossfrom', 'grossto', 'Gross Pay', 'Gross Pay From', 'Gross Pay To');

    // Pay Date:
    checkbook_datafeeds_check_ranged_date($form_state, 'paydatefrom', 'paydateto', 'Pay Date', 'Pay Date From', 'Pay Date To');

    // Total Gross YTD:
    checkbook_datafeeds_check_ranged_amounts($form_state, 'totalgrossfrom', 'totalgrossto', 'Gross Pay YTD', 'Gross Pay YTD From', 'Gross Pay YTD To');

    // Annual Salary:
    checkbook_datafeeds_check_ranged_amounts($form_state, 'salaryfrom', 'salaryto', 'Amount', 'Amount From', 'Amount To');

    //Validate response columns
    if ( Datasource::NYCHA == $submitted_data_source && (array_key_first($oge_response_columns) == '' || array_key_first($oge_response_columns) == null)){
      $form_state->setErrorByName('oge_column_select', t('You must select at least one column.'));
    } else if (Datasource::CITYWIDE == $submitted_data_source && (array_key_first($response_columns) == '' || array_key_first($response_columns) == null)) {
      $form_state->setErrorByName('column_select', t('You must select at least one column.'));
    }

    //Set the hidden filed values on Spending form
    $form_state->setValue([FeedConstants::COMPLETE_FORM, 'data_source', '#value'], $submitted_data_source);
    //Hidden Field for multi-select
    if ($submitted_data_source == Datasource::NYCHA) {
      $multi_select_hidden = $form_state->hasValue(['input', 'oge_column_select']) ? '|' . implode('||', $form_state->getValue(['input', 'oge_column_select'])) . '|' : '';

    } else {
      $multi_select_hidden = $form_state->hasValue(['input', 'column_select']) ? '|' . implode('||', $form_state->getValue(['input', 'column_select'])) . '|' : '';

    }
    $form_state->set([FeedConstants::COMPLETE_FORM, 'data_source', '#value'], $submitted_data_source);
    $form_state->set([FeedConstants::COMPLETE_FORM, 'hidden_multiple_value', '#value'], $multi_select_hidden);
  }
}
