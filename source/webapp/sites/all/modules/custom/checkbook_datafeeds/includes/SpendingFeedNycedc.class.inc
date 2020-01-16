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
  protected $oge_label = 'Other Government Entity';
  protected $oge_name = "NEW YORK CITY ECONOMIC DEVELOPMENT CORPORATION";

  protected function _process_user_criteria_by_datasource(){
    //EDC
    $this->form['filter']['agency'] = array(
      '#markup' => '<div><strong>' . $this->oge_label .':</strong> ' . $this->oge_name . '</div>',
    );
    //Spending Category
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

    //Commodity Line
    if ($this->values['commodity_line']) {
      $this->form['filter']['commodity_line'] = array(
        '#markup' => '<div><strong>Commodity Line:</strong> ' . $this->values['commodity_line'] . '</div>',
      );
      $this->user_criteria['Commodity Line'] = $this->values['commodity_line'];
      $this->formatted_search_criteria['Commodity Line'] = $this->values['commodity_line'];
    }

    //Entity Contract Number
    if ($this->values['entity_contract_number']) {
      $this->form['filter']['entity_contract_number'] = array(
        '#markup' => '<div><strong>Entity Contract #:</strong> ' . $this->values['entity_contract_number'] . '</div>',
      );
      $this->user_criteria['Entity Contract #'] = $this->values['entity_contract_number'];
      $this->formatted_search_criteria['Entity Contract #'] = $this->values['entity_contract_number'];
    }

    //capital Project
    if ($this->values['capital_project']) {
      $this->form['filter']['capital_project'] = array(
        '#markup' => '<div><strong>Capital Project:</strong> ' . $this->values['capital_project'] . '</div>',
      );
      $this->user_criteria['Capital Project'] = $this->values['capital_project'];
      $this->formatted_search_criteria['Capital Project'] = $this->values['capital_project'];
    }

    //Budget Name
    if ($this->values['budget_name']) {
      $this->form['filter']['budget_name'] = array(
        '#markup' => '<div><strong>Budget Name:</strong> ' . $this->values['budget_name'] . '</div>',
      );
      $this->user_criteria['Budget Name'] = $this->values['budget_name'];
      $this->formatted_search_criteria['Budget Name'] = $this->values['budget_name'];
    }

  }

  protected function _process_datasource_values()
  {
    if ($this->values['nycedc_expense_type'] && $this->values['expense type'] != 'ts') {
      preg_match($this->bracket_value_pattern, $this->values['nycedc_expense_type'], $etmatches);
      $this->criteria['value']['spending_category'] = trim($etmatches[1], '[ ]');
    }

    if ($this->values['capital_project']) {
      $this->criteria['value']['capital_project_code'] = $this->values['capital_project'];
    }

    if ($this->values['commodity_line']) {
      $this->criteria['value']['commodity_line'] = $this->values['commodity_line'];
    }

    if ($this->values['budget_name']) {
      $this->criteria['value']['budget_name'] = $this->values['budget_name'];
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
