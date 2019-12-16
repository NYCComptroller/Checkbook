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


class SpendingFeedNycedc extends SpendingFeed
{
  protected $data_source = 'checkbook_oge';
  protected $type_of_data = 'Spending_OGE';
  protected $filtered_columns_container = 'oge_column_select';
  const oge_label = 'Other Government Entity';
  const oge_name = "NEW YORK CITY ECONOMIC DEVELOPMENT CORPORATION";

  protected function _process_expense_type_by_datasource()
  {
    if ($this->values['oge_expense_type']) {
      preg_match("/.*?(\\[.*?])/is", $this->values['oge_expense_type'], $matches);
      $expense_type_name = str_replace($matches[1], "", $matches[0]);
      $this->form['filter']['oge_expense_type'] = array('#markup' => '<div><strong>Spending Category:</strong> ' . $expense_type_name . '</div>');
      $this->user_criteria['Expense Type'] = $this->values['oge_expense_type'];
      $this->formatted_search_criteria['Spending Category'] = $expense_type_name;
    } else {
      $this->form['filter']['expense_type'] = array('#markup' => '<div><strong>Spending Category:</strong> Total Spending</div>');
      $this->formatted_search_criteria['Spending Category'] = 'Total Spending';
    }
  }

  protected function _process_datasource_values()
  {
    if ($this->values['nycedc_expense_type'] && $this->values['expense type'] != 'ts') {
      preg_match($this->bracket_value_pattern, $this->values['nycedc_expense_type'], $etmatches);
      $this->criteria['value']['spending_category'] = trim($etmatches[1], '[ ]');
    }

    //if ($this->values['agency'] != 'Select One') {
    //  preg_match($this->bracket_value_pattern, $this->values['agency'], $agency_matches);
    // $this->criteria['value']['other_government_entities_code'] = trim($agency_matches[1], '[ ]');
    // }

    if ($this->values['contractno']) {
      $this->criteria['value']['contract_id'] = $this->values['contractno'];
    }

    if ($this->values['payee_name']) {
      preg_match($this->bracket_value_pattern, $this->values['payee_name'], $payee_name_matches);
      if ($payee_name_matches) {
        $this->criteria['value']['payee_name'] = trim($payee_name_matches[1], '[ ]');
      } else {
        $this->criteria['value']['payee_name'] = $this->values['payee_name'];
      }
    }
  }

  protected function _validate_by_datasource(&$form, &$form_state)
  {
    //Validate Commodity Line
    $entity_contractno = $form_state['values']['entity_contract_number'];
    $commodity_line = $form_state['values']['commodity_line'];
    if ($commodity_line && !is_numeric($commodity_line)) {
      form_set_error('commodity_line', t('Commodity Line must be a number.'));
    }
    if ($entity_contractno && !is_numeric($entity_contractno)) {
      form_set_error('entity_contract_number', t('Entity Contract # must be a number'));
    }

    $multi_select_hidden = isset($form_state['input']['oge_column_select']) ? '|' . implode('||', $form_state['input']['oge_column_select']) . '|' : '';
    if (!$multi_select_hidden) {
      form_set_error('oge_column_select', t('You must select at least one column.'));
    }
  }
}
