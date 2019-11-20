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

  protected function _process_expense_type_by_datasource()
  {
    if ($this->values['nycha_expense_type'] && $this->values['nycha_expense_type'] !== 'Select Spending Category') {
      preg_match("/.*?(\\[.*?])/is", $this->values['nycha_expense_type'], $matches);
      $expense_type_name = str_replace($matches[1], "", $matches[0]);
      $this->form['filter']['nycha_expense_type'] = array('#markup' => '<div><strong>Spending Category:</strong> ' . $expense_type_name . '</div>');
      $this->user_criteria['Expense Type'] = $this->values['nycha_expense_type'];
      $this->formatted_search_criteria['Spending Category'] = $expense_type_name;
    }
  }

  protected function _process_industry_by_datasource()
  {
    if ($this->values['nycha_industry']) {
      preg_match("/.*?(\\[.*?])/is", $this->values['nycha_industry'], $matches);
      $industry_type_name = str_replace($matches[1], "", $matches[0]);
      $industry_type_code = trim($matches[1], '[ ]');
      $this->form['filter']['nycha_industry'] = array('#markup' => '<div><strong>Industry:</strong> ' . $industry_type_name . '</div>');
      $this->user_criteria['Industry'] = $industry_type_code;
      $this->formatted_search_criteria['Industry'] = $industry_type_name;
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
      $this->criteria['value']['industry_type_code'] = trim($imatches[1], '[ ]');
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
