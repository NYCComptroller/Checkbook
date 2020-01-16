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

namespace checkbook_datafeeds;


class SpendingFeedCitywide extends SpendingFeed
{
  protected $data_source = 'citywide';
  protected $type_of_data = 'Spending';
  protected $filtered_columns_container = 'column_select';

  protected function _process_user_criteria_by_datasource(){
    //Agency
    if ($this->values['agency']) {
      $this->agency_name = $this->values['agency'];
      $this->form['filter']['agency'] = array(
        '#markup' => '<div><strong>' . $this->agency_label . ':</strong> ' . $this->agency_name . '</div>',
      );
      $this->user_criteria['Agency'] = $this->values['agency'];
      $this->formatted_search_criteria[$this->agency_label] = $this->agency_name;
    }

    //Department
    if ($this->values['dept'] && $this->values['dept'] != 'Select Department' && $this->values['dept'] != '0') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->values['dept'])) {
        $this->values['dept'] = htmlspecialchars($this->values['dept']);
      }
      $this->form['filter']['dept'] = array('#markup' => '<div><strong>Department:</strong>' . $this->values['dept'] . '</div>');
      $this->user_criteria['Department'] = $this->values['dept'];
      $this->formatted_search_criteria['Department'] = $this->values['dept'];
    }

    //Expense Category
    if ($this->values['expense_category'] && $this->values['expense_category'] != 'Select Expense Category' && $this->values['expense_category'] != '0') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->values['expense_category'])) {
        $this->values['expense_category'] = htmlspecialchars($this->values['expense_category']);
      }
      $this->form['filter']['expense_category'] = array('#markup' => '<div><strong>Expense Category:</strong> ' . $this->values['expense_category'] . '</div>');
      $this->user_criteria['Expense Category'] = $this->values['expense_category'];
      $this->formatted_search_criteria['Expense Category'] = $this->values['expense_category'];
    }

    //Spending Category
    if ($this->values['expense_type']) {
      preg_match("/.*?(\\[.*?])/is", $this->values['expense_type'], $matches);
      $expense_type_name = str_replace($matches[1], "", $matches[0]);
      $this->form['filter']['expense_type'] = array('#markup' => '<div><strong>Spending Category:</strong> ' . $expense_type_name . '</div>');
      $this->user_criteria['Expense Type'] = $this->values['expense_type'];
      $this->formatted_search_criteria['Spending Category'] = $expense_type_name;
    } else {
      $this->form['filter']['expense_type'] = array('#markup' => '<div><strong>Spending Category:</strong> Total Spending</div>');
      $this->formatted_search_criteria['Spending Category'] = 'Total Spending';
    }

    //Industry
    if ($this->values['industry']) {
      preg_match("/.*?(\\[.*?])/is", $this->values['industry'], $matches);
      $industry_type_name = str_replace($matches[1], "", $matches[0]);
      $industry_type_id = trim($matches[1], '[ ]');
      $this->form['filter']['industry'] = array('#markup' => '<div><strong>Industry:</strong> ' . $industry_type_name . '</div>');
      $this->user_criteria['Industry'] = $industry_type_id;
      $this->formatted_search_criteria['Industry'] = $industry_type_name;
    }

    //M/WBE Category
    if ($this->values['mwbe_category']) {
      $this->form['filter']['mwbe_category'] = array('#markup' => '<div><strong>M/WBE Category:</strong> ' . \MappingUtil::getCurrenEthnicityName(explode('~', $this->values['mwbe_category'])) . '</div>');
      $this->user_criteria['M/WBE Category'] = $this->values['mwbe_category'];
      $this->formatted_search_criteria['M/WBE Category'] = \MappingUtil::getCurrenEthnicityName(explode('~', $this->values['mwbe_category']));
    }

    //Vendor
    if ($this->values['payee_name']) {
      $this->form['filter']['payee_name'] = array(
        '#markup' => '<div><strong>Payee Name:</strong> ' . $this->values['payee_name'] . '</div>',
      );
      $this->user_criteria['Payee Name'] = $this->values['payee_name'];
      $this->formatted_search_criteria['Payee Name'] = $this->values['payee_name'];
    }

    //Check Amount
    if ($this->values['check_amt_from'] && $this->values['check_amt_to']) {
      $this->form['filter']['chkamount'] = array(
        '#markup' => '<div><strong>Check Amount:</strong> Greater Than Equal to: $' . $this->values['check_amt_from'] . ' Less Than Equal to: $' . $this->values['check_amt_to'] . '</div>',
      );
      $this->user_criteria['Check Amount Greater Than'] = $this->values['check_amt_from'];
      $this->user_criteria['Check Amount Less Than'] = $this->values['check_amt_to'];
      $this->formatted_search_criteria['Check Amount'] = 'Greater Than Equal to: $' . $this->values['check_amt_from'] . ' Less Than Equal to: $' . $this->values['check_amt_to'];
    } elseif (!$this->values['check_amt_from'] && $this->values['check_amt_to']) {
      $this->form['filter']['chkamount'] = array(
        '#markup' => '<div><strong>Check Amount:</strong> Less Than Equal to: $' . $this->values['check_amt_to'] . '</div>',
      );
      $this->user_criteria['Check Amount Less Than'] = $this->values['check_amt_to'];
      $this->formatted_search_criteria['Check Amount'] = 'Less Than Equal to: $' . $this->values['check_amt_to'];
    } elseif ($this->values['check_amt_from'] && !$this->values['check_amt_from']) {
      $this->form['filter']['chkamount'] = array(
        '#markup' => '<div><strong>Check Amount:</strong> Greater Than Equal to: $' . $this->values['check_amt_from'] . '</div>',
      );
      $this->user_criteria['Check Amount Greater Than'] = $this->values['check_amt_to'];
      $this->formatted_search_criteria['Check Amount'] = 'Greater Than Equal to: $' . $this->values['check_amt_from'];
    }

    //Contract ID
    if ($this->values['contractno']) {
      $this->form['filter']['contractno'] = array(
        '#markup' => '<div><strong>Contract ID:</strong> ' . $this->values['contractno'] . '</div>',
      );
      $this->user_criteria['Contract ID'] = $this->values['contractno'];
      $this->formatted_search_criteria['Contract ID'] = $this->values['contractno'];
    }

    //Document ID
    if ($this->values['document_id']) {
      $this->form['filter']['document_id'] = array(
        '#markup' => '<div><strong>Document ID:</strong> ' . $this->values['document_id'] . '</div>',
      );
      $this->user_criteria['Document ID'] = $this->values['document_id'];
      $this->formatted_search_criteria['Document ID'] = $this->values['document_id'];
    }

    //capital Project
    if ($this->values['capital_project']) {
      $this->form['filter']['capital_project'] = array(
        '#markup' => '<div><strong>Capital Project:</strong> ' . $this->values['capital_project'] . '</div>',
      );
      $this->user_criteria['Capital Project'] = $this->values['capital_project'];
      $this->formatted_search_criteria['Capital Project'] = $this->values['capital_project'];
    }
  }

  protected function _process_datasource_values()
  {
    if ($this->values['expense_type'] && $this->values['expense type'] != 'ts') {
      preg_match($this->bracket_value_pattern, $this->values['expense_type'], $etmatches);
      $this->criteria['value']['spending_category'] = trim($etmatches[1], '[ ]');
    }
    if ($this->values['agency'] != 'Citywide (All Agencies)') {
      preg_match($this->bracket_value_pattern, $this->values['agency'], $agency_matches);
      $this->criteria['value']['agency_code'] = trim($agency_matches[1], '[ ]');
    }

    if ($this->values['mwbe_category'] && $this->values['mwbe_category'] != 'Select Category') {
      $this->criteria['value']['mwbe_category'] = $this->values['mwbe_category'];
    }
    if ($this->values['industry'] && $this->values['industry'] != 'Select Industry') {
      preg_match($this->bracket_value_pattern, $this->values['industry'], $imatches);
      $this->criteria['value']['industry_type_id'] = trim($imatches[1], '[ ]');
    }

    if ($this->values['capital_project']) {
      $this->criteria['value']['capital_project_code'] = $this->values['capital_project'];
    }
  }

  protected function _validate_by_datasource(&$form, &$form_state)
  {
    $multi_select_hidden = isset($form_state['input']['column_select']) ? '|' . implode('||', $form_state['input']['column_select']) . '|' : '';
    if (!$multi_select_hidden) {
      form_set_error('column_select', t('You must select at least one column.'));
    }
  }

}
