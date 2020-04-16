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


class BudgetFeedNycha extends BudgetFeed
{
  protected $data_source = \Datasource::NYCHA;
  protected $type_of_data = 'Budget_NYCHA';
  protected $filtered_columns_container = 'nycha_column_select';
  protected $oge_label = 'Other Government Entity';
  protected $oge_name_code = "NEW YORK CITY HOUSING AUTHORITY[996]";
  protected $oge_name = "NEW YORK CITY HOUSING AUTHORITY";

  protected function _process_user_criteria_by_datasource(){
    //OGE Display
    $this->form['filter']['agency'] = array('#markup' => '<div><strong>' . $this->oge_label .':</strong> ' . $this->oge_name_code . '</div>',);
    $this->formatted_search_criteria[$this->oge_label] = $this->oge_name;

    //Expense Category
    if ($this->values['nycha_expense_category'] && $this->values['nycha_expense_category'] != 'Select Expense Category' && $this->values['nycha_expense_category'] != '0') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->values['nycha_expense_category'])) {
        $this->values['nycha_expense_category'] = htmlspecialchars($this->values['nycha_expense_category']);
      }
      $this->form['filter']['nycha_expense_category'] = array('#markup' => '<div><strong>Expense Category:</strong> ' . $this->values['nycha_expense_category'] . '</div>');
      $this->user_criteria['Expense Category'] = $this->values['nycha_expense_category'];
      $this->formatted_search_criteria['Expense Category'] = $this->values['nycha_expense_category'];
    }

  }

  protected function _process_datasource_values(){

  }

  protected function _validate_by_datasource(&$form, &$form_state){
    $multi_select_hidden = isset($form_state['input']['nycha_column_select']) ? '|' . implode('||', $form_state['input']['nycha_column_select']) . '|' : '';
    if (!$multi_select_hidden) {
      form_set_error('nycha_column_select', t('You must select at least one column - nyhca.'));
    }
  }
}
