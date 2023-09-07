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

namespace Drupal\checkbook_datafeeds\Spending;
use Drupal\checkbook_datafeeds\Utilities\FormUtil;
use Drupal\checkbook_project\MwbeUtilities\MappingUtil;

class SpendingFeedCitywide extends SpendingFeed
{
  protected $data_source = 'citywide';
  protected $type_of_data = 'Spending';
  protected $filtered_columns_container = 'column_select';

  protected function _process_user_criteria_by_datasource(){
    //Agency
    if ($this->form_state->getValue('agency')) {
      $this->agency_name = $this->form_state->getValue('agency');
      $this->form['filter']['agency'] = array(
        '#markup' => '<div><strong>' . $this->agency_label . ':</strong> ' . $this->agency_name . '</div>',
      );
      $this->user_criteria['Agency'] = $this->form_state->getValue('agency');
      $this->formatted_search_criteria[$this->agency_label] = $this->agency_name;
    }

    //Department
    if ($this->form_state->getValue('dept') && $this->form_state->getValue('dept') != 'Select Department' && $this->form_state->getValue('dept') != '0' && $this->form_state->getValue('agency') != 'Citywide (All Agencies)') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->form_state->getValue('dept'))) {
        $this->form_state->setValue('dept', htmlspecialchars($this->form_state->getValue('dept')));
      }
      $this->form['filter']['dept'] = array('#markup' => '<div><strong>Department:</strong>' . $this->form_state->getValue('dept') . '</div>');
      $this->user_criteria['Department'] = $this->form_state->getValue('dept');
      $this->formatted_search_criteria['Department'] = $this->form_state->getValue('dept');
    }

    //Expense Category
    if ($this->form_state->getValue('expense_category') && $this->form_state->getValue('expense_category') != 'Select Expense Category' && $this->form_state->getValue('expense_category') != '0' && $this->form_state->getValue('agency') != 'Citywide (All Agencies)') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->form_state->getValue('expense_category'))) {
        $this->form_state->setValue('expense_category', htmlspecialchars($this->form_state->getValue('expense_category')));
      }
      $this->form['filter']['expense_category'] = array('#markup' => '<div><strong>Expense Category:</strong> ' . $this->form_state->getValue('expense_category') . '</div>');
      $this->user_criteria['Expense Category'] = $this->form_state->getValue('expense_category');
      $this->formatted_search_criteria['Expense Category'] = $this->form_state->getValue('expense_category');
    }

    //Spending Category
    if ($this->form_state->getValue('expense_type')) {
      $this->form['filter']['expense_type'] = array('#markup' => '<div><strong>Spending Category:</strong> ' . $this->form_state->getValue('expense_type') . '</div>');
      $this->user_criteria['Expense Type'] = $this->form_state->getValue('expense_type');
      $this->formatted_search_criteria['Spending Category'] = $this->form_state->getValue('expense_type');
    } else {
      $this->form['filter']['expense_type'] = array('#markup' => '<div><strong>Spending Category:</strong> Total Spending</div>');
      $this->formatted_search_criteria['Spending Category'] = 'Total Spending';
    }

    //Catastrophic event filter - Not applicable for categories Payroll and Others
    if ($this->form_state->getValue('expense_type') != 'Payroll [p]' && $this->form_state->getValue('expense_type') != 'Others [o]') {
      if ($this->form_state->getValue('catastrophic_event') && ($this->form_state->getValue('year') == '0' || substr($this->form_state->getValue('year'), -4) >= 2020)) {
        $catastrophic_events = FormUtil::getEventNameAndId();
        $catastrophic_event = $catastrophic_events[$this->form_state->getValue('catastrophic_event')] . "[" . $this->form_state->getValue('catastrophic_event') . "]";
        $this->form['filter']['catastrophic_event'] = array('#markup' => '<div><strong>Catastrophic Event:</strong> ' . $catastrophic_event . '</div>');
        $this->user_criteria['Catastrophic Event'] = $this->form_state->getValue('catastrophic_event');
        $this->formatted_search_criteria['Catastrophic Event'] = $this->form_state->getValue('catastrophic_event');
      }
    }

    //Industry
    if ($this->form_state->getValue('industry')) {
      preg_match("/.*?(\\[.*?])/is", $this->form_state->getValue('industry'), $matches);
      $industry_type_name = str_replace($matches[1], "", $matches[0]);
      $industry_type_id = trim($matches[1], '[ ]');
      $this->form['filter']['industry'] = array('#markup' => '<div><strong>Industry:</strong> ' . $this->form_state->getValue('industry') . '</div>');
      $this->user_criteria['Industry'] = $industry_type_id;
      $this->formatted_search_criteria['Industry'] = $industry_type_name;
    }

    //M/WBE Category
    if ($this->form_state->getValue('mwbe_category')) {
      $this->form['filter']['mwbe_category'] = array('#markup' => '<div><strong>M/WBE Category:</strong> ' . MappingUtil::getCurrenEthnicityName(explode('~', $this->form_state->getValue('mwbe_category'))) . '</div>');
      $this->user_criteria['M/WBE Category'] = $this->form_state->getValue('mwbe_category');
      $this->formatted_search_criteria['M/WBE Category'] = MappingUtil::getCurrenEthnicityName(explode('~', $this->form_state->getValue('mwbe_category')));
    }

    //Vendor
    if ($this->form_state->getValue('payee_name')) {
      $this->form['filter']['payee_name'] = array(
        '#markup' => '<div><strong>Payee Name:</strong> ' . $this->form_state->getValue('payee_name') . '</div>',
      );
      $this->user_criteria['Payee Name'] = $this->form_state->getValue('payee_name');
      $this->formatted_search_criteria['Payee Name'] = $this->form_state->getValue('payee_name');
    }

    //Check Amount
    if (($this->form_state->getValue('check_amt_from') || $this->form_state->getValue('check_amt_from') === "0") && ($this->form_state->getValue('check_amt_to') || $this->form_state->getValue('check_amt_to') === "0")) {
      $this->form['filter']['chkamount'] = array(
        '#markup' => '<div><strong>Check Amount:</strong> Greater Than Equal to: $' . $this->form_state->getValue('check_amt_from') . ' and Less Than Equal to: $' . $this->form_state->getValue('check_amt_to') . '</div>',
      );
      $this->user_criteria['Check Amount Greater Than'] = $this->form_state->getValue('check_amt_from');
      $this->user_criteria['Check Amount Less Than'] = $this->form_state->getValue('check_amt_to');
      $this->formatted_search_criteria['Check Amount'] = 'Greater Than Equal to: $' . $this->form_state->getValue('check_amt_from') . ' and Less Than Equal to: $' . $this->form_state->getValue('check_amt_to');
    } elseif (!$this->form_state->getValue('check_amt_from') && ($this->form_state->getValue('check_amt_to') || $this->form_state->getValue('check_amt_to') === "0")) {
      $this->form['filter']['chkamount'] = array(
        '#markup' => '<div><strong>Check Amount:</strong> Less Than Equal to: $' . $this->form_state->getValue('check_amt_to') . '</div>',
      );
      $this->user_criteria['Check Amount Less Than'] = $this->form_state->getValue('check_amt_to');
      $this->formatted_search_criteria['Check Amount'] = 'Less Than Equal to: $' . $this->form_state->getValue('check_amt_to');
    } elseif (($this->form_state->getValue('check_amt_from') || $this->form_state->getValue('check_amt_from') === "0") && !$this->form_state->getValue('check_amt_to')) {
      $this->form['filter']['chkamount'] = array(
        '#markup' => '<div><strong>Check Amount:</strong> Greater Than Equal to: $' . $this->form_state->getValue('check_amt_from') . '</div>',
      );
      $this->user_criteria['Check Amount Greater Than'] = $this->form_state->getValue('check_amt_to');
      $this->formatted_search_criteria['Check Amount'] = 'Greater Than Equal to: $' . $this->form_state->getValue('check_amt_from');
    }

    //Contract ID
    if ($this->form_state->getValue('contractno')) {
      $this->form['filter']['contractno'] = array(
        '#markup' => '<div><strong>Contract ID:</strong> ' . $this->form_state->getValue('contractno') . '</div>',
      );
      $this->user_criteria['Contract ID'] = $this->form_state->getValue('contractno');
      $this->formatted_search_criteria['Contract ID'] = $this->form_state->getValue('contractno');
    }

    //Document ID
    if ($this->form_state->getValue('document_id')) {
      $this->form['filter']['document_id'] = array(
        '#markup' => '<div><strong>Document ID:</strong> ' . $this->form_state->getValue('document_id') . '</div>',
      );
      $this->user_criteria['Document ID'] = $this->form_state->getValue('document_id');
      $this->formatted_search_criteria['Document ID'] = $this->form_state->getValue('document_id');
    }

    //capital Project
    if ($this->form_state->getValue('capital_project')) {
      $this->form['filter']['capital_project'] = array(
        '#markup' => '<div><strong>Capital Project:</strong> ' . $this->form_state->getValue('capital_project') . '</div>',
      );
      $this->user_criteria['Capital Project'] = $this->form_state->getValue('capital_project');
      $this->formatted_search_criteria['Capital Project'] = $this->form_state->getValue('capital_project');
    }

    //Year Filter
    if($this->form_state->getValue('date_filter') == 0) {
      if ($this->form_state->getValue('year') && $this->form_state->getValue('year') !== '0') {
        $this->form['filter']['year'] = array(
          '#markup' => '<div><strong>Year:</strong> ' . substr($this->form_state->getValue('year'), 0, -4) . ' ' . substr($this->form_state->getValue('year'), -4) . '</div>',
        );
        $this->user_criteria['Fiscal Year'] = $this->form_state->getValue('year');
        $this->formatted_search_criteria['Year'] = $this->form_state->getValue('year');
      } else{
        $this->form['filter']['year'] = array(
          '#markup' => '<div><strong>Year:</strong> All Years</div>',
        );
        $this->formatted_search_criteria['Year'] = ' All Years';
      }
    }
  }

  protected function _process_datasource_values()
  {
    if ($this->form_state->getValue('expense_type') && $this->form_state->getValue('expense_type') != 'ts') {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('expense_type'), $etmatches);
      $this->criteria['value']['spending_category'] = trim($etmatches[1], '[ ]');
    }

    if ($this->form_state->getValue('expense_type') != 'Payroll [p]' && $this->form_state->getValue('expense_type') != 'Others [o]') {
      if ($this->form_state->getValue('catastrophic_event') && ($this->form_state->getValue('year') == '0' || substr($this->form_state->getValue('year'), -4) >= 2020))
      {
        $this->criteria['value']['catastrophic_event'] = $this->form_state->getValue('catastrophic_event');
      }
    }
    if ($this->form_state->getValue('agency') != 'Citywide (All Agencies)') {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('agency'), $agency_matches);
      $this->criteria['value']['agency_code'] = trim($agency_matches[1], '[ ]');
    }

    if ($this->form_state->getValue('mwbe_category') && $this->form_state->getValue('mwbe_category') != 'Select Category') {
      $this->criteria['value']['mwbe_category'] = $this->form_state->getValue('mwbe_category');
    }
    if ($this->form_state->getValue('industry') && $this->form_state->getValue('industry') != 'Select Industry') {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('industry'), $imatches);
      $this->criteria['value']['industry'] = trim($imatches[1], '[ ]');
    }

    if ($this->form_state->getValue('capital_project')) {
      $this->criteria['value']['capital_project_code'] = $this->form_state->getValue('capital_project');
    }

    if ($this->form_state->getValue('year') && $this->form_state->getValue('year') != '0') {
      $this->criteria['value']['fiscal_year'] = ltrim($this->form_state->getValue('year'), 'FY');
    }
  }

  protected function _validate_by_datasource(&$form, &$form_state)
  {
    // Check Columns
    $responseColumns = $form_state->getValue('column_select');
    if (empty(array_filter($responseColumns))) {
      $form_state->setErrorByName('column_select', t('You must select at least one column.'));
    }

//  $multi_select_hidden = isset($form_state['input']['column_select']) ? '|' . implode('||', $form_state['input']['column_select']) . '|' : '';
    $multi_select_hidden = $form_state->hasValue('column_select') ? '|' . implode('||', $form_state->getValue('column_select')) . '|' : '';

//  if ( Datasource::NYCHA == $data_source && (array_key_first($oge_response_columns) == '' || array_key_first($oge_response_columns) == null)){
    if (!$multi_select_hidden) {
//      form_set_error('column_select', t('You must select at least one column.'));
      $form_state->setErrorByName('column_select', t('You must select at least one column.'));
    }
  }

}
