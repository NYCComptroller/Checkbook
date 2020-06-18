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


class RevenueFeedCitywide extends RevenueFeed{
  protected $data_source = 'citywide';
  protected $type_of_data = 'Revenue';
  protected $filtered_columns_container = 'column_select_expense';

  protected function _process_user_criteria_by_datasource(){
    if ($this->values['agency']) {
      $this->form['filter']['agency'] = array('#markup' => '<div><strong>Agency:</strong> ' . $this->values['agency'] . '</div>');
      $this->user_criteria['Agency'] = $this->values['agency'];
      $this->formatted_search_criteria['Agency'] = $this->values['agency'];
    }

  }

  protected function _process_datasource_values(){
  }

  protected function _validate_by_datasource(&$form, &$form_state){

  }
}
