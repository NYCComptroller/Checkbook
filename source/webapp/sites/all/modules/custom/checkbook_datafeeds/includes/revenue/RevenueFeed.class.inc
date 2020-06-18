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
use checkbook_advanced_search\Domain;

/**
 * Class RevenueFeed
 * @package checkbook_datafeeds
 */
abstract class RevenueFeed{
  /**
   * @var string
   */
  protected $data_source = '';
  /**
   * @var string
   */
  protected $response_type = 'csv';
  /**
  /**
   * @var
   */
  protected $form;
  /**
   * @var array
   */
  protected $user_criteria = [];
  /**
   * @var
   */
  protected $form_state;
  /**
   * @var array
   */
  protected $formatted_search_criteria;

  /**
   * @var string
   */
  protected $agency_label = 'Agency';
  /**
   * @var
   */
  protected $values;
  /**
   * @var array
   */
  protected $selected_columns;
  /**
   * @var array
   */
  protected $filtered_columns;

  protected $type_of_data = '';
  /**
   * @var
   */
  protected $filtered_columns_container;
  /**
   * @var array
   */
  protected $criteria;

  protected $bracket_value_pattern = "/.*?(\\[.*?\\])/is";

  /**
   * RevenueFeed constructor.
   */
  public function __construct(){
    $this->user_criteria = ['Type of Data' => $this->type_of_data];
  }

  /**
   * @param $form
   * @param $form_state
   * @return array
   */
  public function process_confirmation($form, &$form_state){
    $this->form = $form;
    $this->form_state = $form_state;
    $this->_process_user_criteria_confirmation();
    $this->user_criteria['Formatted'] = $this->formatted_search_criteria;
    $this->_process_criteria();
    $this->form_state['step_information']['confirmation']['stored_values']['criteria'] = $this->criteria;
    $this->form_state['step_information']['confirmation']['stored_values']['user_criteria'] = $this->user_criteria;
    $modified_form = checkbook_datafeeds_end_of_confirmation_form($this->form, $this->form_state, $this->criteria, $this->response_type, Domain::Revenue);
    $form_state = $this->form_state;
    return $modified_form;
  }
  protected function _process_user_criteria_confirmation(){
    $this->values = $this->form_state['step_information']['revenue']['stored_values'];
    $this->response_type = $this->form_state['step_information']['type']['stored_values']['format'];
    $this->user_criteria = [];
    $this->user_criteria['Type of File'] = $this->response_type;

    $this->form['download_feeds'] = [
      '#markup' => '<h2 id="edit-description">Download Data</h2>',
    ];
    $this->form['columns'] = [
      '#type' => 'fieldset',
      '#title' => t('Selected Columns'),
    ];
    $this->form['#attributes'] = [
      'class' => [
        'confirmation-page',
        'data-feeds-wizard',
      ]
    ];

    //Used to maintain the order of the columns
    $this->selected_columns = checkbook_datafeeds_format_columns();
    //Filter columns for current data source
    $this->filtered_columns = checkbook_datafeeds_revenue_filter_selected_columns($this->selected_columns, $this->data_source, $this->response_type);

    foreach ($this->selected_columns as $column) {
      $this->form['columns'][$column] = array('#markup' => '<div>' . $column . '</div>');
      $this->user_criteria['Columns'][] = $column;
    }

    $this->filtered_columns = checkbook_datafeeds_revenue_filter_selected_columns($this->selected_columns, $this->data_source, $this->response_type, 'nycha_export');

    $this->form['filter'] = array(
      '#type' => 'fieldset',
      '#title' => t('Search Criteria'),
    );

    $this->formatted_search_criteria = array();

    $this->form['filter']['data_type'] = array(
      '#markup' => '<div><strong>Type of Data:</strong> Revenue</div>',
    );
    $this->formatted_search_criteria['Type of Data'] = 'Revenue';
    $this->form['filter']['file_type'] = array(
      '#markup' => '<div><strong>Type of File:</strong> ' . $this->form_state['step_information']['type']['stored_values']['format'] . '</div>',
    );
    $this->formatted_search_criteria['Type of File'] = $this->form_state['step_information']['type']['stored_values']['format'];

    $this->_process_user_criteria_by_datasource();

    return;
  }

  abstract protected function _process_user_criteria_by_datasource();

  /**
   * Convert values from Revenue section of form to an array format expected by API SearchCriteria.
   */
  private function _process_criteria(){
    global $conf;
    $this->criteria = [
      'global' => [
        //Set data source for query
        'type_of_data' => $this->type_of_data,
        'records_from' => 1,
        'max_records' => $conf['check_book']['data_feeds']['max_record_limit'] ?? 200000,
      ],
      'responseColumns' => $this->filtered_columns
    ];
    $this->_process_datasource_values();
    return;
  }

  protected function _process_datasource_values(){}

  public function checkbook_datafeeds_revenue_validate(&$form, &$form_state){
    //Hidden Field for multi-select
    switch ($this->data_source) {
      case \Datasource::NYCHA:
        $multi_select_hidden = isset($form_state['input']['nycha_column_select']) ? '|' . implode('||', $form_state['input']['nycha_column_select']) . '|' : '';
        break;
      default:
        $multi_select_hidden = isset($form_state['input']['column_select_expense']) ? '|' . implode('||', $form_state['input']['column_select_expense']) . '|' : '';
    }
    $form_state['complete form']['hidden_multiple_value']['#value'] = $multi_select_hidden;

    $this->_validate_by_datasource($form, $form_state);
  }

  protected function _validate_by_datasource(&$form, &$form_state){}
}
