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


class SpendingFeedNycha extends SpendingFeed
{
  protected $data_source = 'checkbook_nycha';
  protected $type_of_data = 'Spending_NYCHA';
  protected $filtered_columns_container = 'nycha_column_select';
  const oge_label = 'Other Government Entity';
  const oge_name = "NEW YORK CITY HOUSING AUTHORITY";

  protected function _process_expense_type_by_datasource()
  {
    if ($this->values['nycha_expense_type'] && $this->values['nycha_expense_type'] !== 'Total Spending') {
      preg_match("/.*?(\\[.*?])/is", $this->values['nycha_expense_type'], $matches);
      $expense_type_name = str_replace($matches[1], "", $matches[0]);
      $this->form['filter']['nycha_expense_type'] = array('#markup' => '<div><strong>Spending Category:</strong> ' . $expense_type_name . '</div>');
      $this->user_criteria['Expense Type'] = $this->values['nycha_expense_type'];
      $this->formatted_search_criteria['Spending Category'] = $expense_type_name;
    }else{
      $this->form['filter']['nycha_expense_type'] = array('#markup' => '<div><strong>Spending Category:</strong> Total Spending</div>');
      $this->formatted_search_criteria['Spending Category'] = 'Total Spending';
    }
  }

  protected function _process_industry_by_datasource()
  {
    if ($this->values['nycha_industry']) {
      $industry_type = trim($this->values['nycha_industry']);
      $this->form['filter']['nycha_industry'] = array('#markup' => '<div><strong>Industry:</strong> ' . $industry_type . '</div>');
      $this->user_criteria['Industry'] = $industry_type;
      $this->formatted_search_criteria['Industry'] = $industry_type;
    }
  }

  protected function _process_datasource_values()
  {
    if ($this->values['nycha_expense_type'] && $this->values['expense type'] != 'ts') {
      preg_match($this->bracket_value_pattern, $this->values['nycha_expense_type'], $etmatches);
      $this->criteria['value']['spending_category'] = trim($etmatches[1], '[ ]');
    }

    if ($this->values['nycha_industry'] && $this->values['nycha_industry'] != 'Select Industry') {
      preg_match($this->bracket_value_pattern, $this->values['nycha_industry'], $imatches);
      $this->criteria['value']['industry_type'] = trim($imatches[1], '[ ]');
    }

    if ($this->values['funding_source'] && $this->values['funding_source'] != 'Select Funding Source') {
      preg_match($this->bracket_value_pattern, $this->values['funding_source'], $imatches);
      $this->criteria['value']['funding_source'] = trim($imatches[1], '[ ]');
    }

    if ($this->values['resp_center'] && $this->values['resp_center'] != 'Select Responsibility Center') {
      preg_match($this->bracket_value_pattern, $this->values['resp_center'], $imatches);
      $this->criteria['value']['responsibility_center'] = trim($imatches[1], '[ ]');
    }

    if ($this->values['spent_amt_from'] !== '' || $this->values['spent_amt_to'] !== '') {
      $this->criteria['range']['amount_spent'] = array(
        checknull($this->values['spent_amt_from']),
        checknull($this->values['spent_amt_to']),
      );
    }
  }

  protected function _validate_by_datasource(&$form, &$form_state)
  {
    $multi_select_hidden = isset($form_state['input']['nycha_column_select']) ? '|' . implode('||', $form_state['input']['nycha_column_select']) . '|' : '';
    if (!$multi_select_hidden) {
      form_set_error('nycha_column_select', t('You must select at least one column.'));
    }
  }
}
