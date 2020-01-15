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

  protected function _process_expense_type_by_datasource()
  {
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

    if ($this->values['industry']) {
      preg_match("/.*?(\\[.*?])/is", $this->values['industry'], $matches);
      $industry_type_name = str_replace($matches[1], "", $matches[0]);
      $industry_type_id = trim($matches[1], '[ ]');
      $this->form['filter']['industry'] = array('#markup' => '<div><strong>Industry:</strong> ' . $industry_type_name . '</div>');
      $this->user_criteria['Industry'] = $industry_type_id;
      $this->formatted_search_criteria['Industry'] = $industry_type_name;
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
