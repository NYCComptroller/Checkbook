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

/**
 * Class SpendingFeed
 * @package checkbook_datafeeds
 */
abstract class SpendingFeed
{
  /**
   * @var string
   */
  protected $data_source = '';
  /**
   * @var string
   */
  protected $response_type = 'csv';
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
   * SpendingFeed constructor.
   */
  public function __construct()
  {
    $this->user_criteria = ['Type of Data' => $this->type_of_data];
  }

  /**
   * @param $form
   * @param $form_state
   * @return array
   */
  public function process_confirmation($form, &$form_state)
  {
    $this->form = $form;
    $this->form_state = $form_state;
    $this->_process_user_criteria_confirmation();
    $this->user_criteria['Formatted'] = $this->formatted_search_criteria;
    $this->_process_criteria();
    $this->form_state['step_information']['confirmation']['stored_values']['criteria'] = $this->criteria;
    $this->form_state['step_information']['confirmation']['stored_values']['user_criteria'] = $this->user_criteria;
    $modified_form = checkbook_datafeeds_end_of_confirmation_form($this->form, $this->form_state, $this->criteria, $this->response_type, 'spending');
    $form_state = $this->form_state;
    return $modified_form;
  }

  protected function _process_user_criteria_confirmation()
  {
    $this->values = $this->form_state['step_information']['spending']['stored_values'];
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
    $this->filtered_columns = checkbook_datafeeds_spending_filter_selected_columns($this->selected_columns, $this->data_source, $this->response_type);

    $this->form_state['step_information']['spending']['stored_values'][$this->filtered_columns_container] = $this->filtered_columns;

    foreach ($this->filtered_columns as $column) {
      $this->form['columns'][$column] = array('#markup' => '<div>' . $column . '</div>');
      $this->user_criteria['Columns'][] = $column;
    }

    $this->form['filter'] = array(
      '#type' => 'fieldset',
      '#title' => t('Search Criteria'),
    );

    $this->formatted_search_criteria = array();

    $this->form['filter']['data_type'] = array(
      '#markup' => '<div><strong>Type of Data:</strong> Spending</div>',
    );
    $this->formatted_search_criteria['Type of Data'] = 'Spending';
    $this->form['filter']['file_type'] = array(
      '#markup' => '<div><strong>Type of File:</strong> ' . $this->form_state['step_information']['type']['stored_values']['format'] . '</div>',
    );
    $this->formatted_search_criteria['Type of File'] = $this->form_state['step_information']['type']['stored_values']['format'];

    $this->_process_user_criteria_by_datasource();

    //Issued Date
    if ($this->values['date_filter'] == 1) {
      if ($this->values['issuedfrom'] && $this->values['issuedto']) {
        $this->form['filter']['issued_date'] = array(
          '#markup' => '<div><strong>Issue Date:</strong> From: ' . $this->values['issuedfrom'] . ' To: ' . $this->values['issuedto'] . '</div>'
        );
        $this->user_criteria['Issued Date After'] = $this->values['issuedfrom'];
        $this->user_criteria['Issued Date Before'] = $this->values['issuedto'];
        $this->formatted_search_criteria['Issue Date'] = 'From: ' . $this->values['issuedfrom'] . ' To: ' . $this->values['issuedto'];
      } elseif (!$this->values['issuedfrom'] && $this->values['issuedto']) {
        $this->form['filter']['issued_date'] = array(
          '#markup' => '<div><strong>Issue Date:</strong> From: ' . $this->values['issuedto'] . '</div>',
        );
        $this->user_criteria['Issued Date Before'] = $this->values['issuedto'];
        $this->formatted_search_criteria['Issue Date'] = 'From: ' . $this->values['issuedto'];
      } elseif ($this->values['issuedfrom'] && !$this->values['issuedto']) {
        $this->form['filter']['issued_date'] = array(
          '#markup' => '<div><strong>Issue Date:</strong> To: ' . $this->values['issuedfrom'] . '</div>',
        );
        $this->user_criteria['Issued Date After'] = $this->values['issuedfrom'];
        $this->formatted_search_criteria['Issue Date'] = 'To: ' . $this->values['issuedfrom'];
      }
    }

    //Year Filter
    if ($this->values['date_filter'] == 0 && $this->values['year'] && $this->values['year'] !== '0') {
      if (startsWith($this->values['year'], 'F')) {
        $this->form['filter']['year'] = array(
          '#markup' => '<div><strong>Year:</strong> ' . $this->values['year'] . '</div>',
        );
        $this->user_criteria['Fiscal Year'] = $this->values['year'];
        $this->formatted_search_criteria['Year'] = $this->values['year'];
      } elseif (startsWith($this->values['year'], 'C')) {
        $this->form['filter']['year'] = array(
          '#markup' => '<div><strong>Year:</strong> ' . $this->values['year'] . '</div>',
        );
        $this->user_criteria['Calendar Year'] = $this->values['year'];
        $this->formatted_search_criteria['Year'] = $this->values['year'];
      }
    }else if($this->values['date_filter'] == 0 && !$this->values['year']){
      $this->form['filter']['year'] = array(
        '#markup' => '<div><strong>Year:</strong> All Years</div>',
      );
      $this->formatted_search_criteria['Year'] = $this->values['year'];
    }

    return;
  }

  abstract protected function _process_user_criteria_by_datasource();

  /**
   * Convert values from Spending section of form to an array format expected by API SearchCriteria.
   */
  private function _process_criteria()
  {
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

    if ($this->values['dept'] && $this->values['dept'] != 'Select Department' && $this->values['dept'] != '0') {
      preg_match($this->bracket_value_pattern, $this->values['dept'], $department_matches);
      $this->criteria['value']['department_code'] = trim($department_matches[1], '[ ]');
    }

    if ($this->values['expense_category'] && $this->values['expense_category'] != 'Select Expense Category' && $this->values['expense_category'] != '0') {
      preg_match($this->bracket_value_pattern, $this->values['expense_category'], $expense_category_matches);
      $this->criteria['value']['expense_category'] = trim($expense_category_matches[1], '[ ]');
    }

    if ($this->values['payee_name']) {
      preg_match($this->bracket_value_pattern, $this->values['payee_name'], $payee_name_matches);
      if ($payee_name_matches) {
        $this->criteria['value']['payee_code'] = trim($payee_name_matches[1], '[ ]');
      } else {
        $this->criteria['value']['payee_name'] = $this->values['payee_name'];
      }
    }

    if ($this->values['check_amt_from'] !== '' || $this->values['check_amt_to'] !== '') {
      $this->criteria['range']['check_amount'] = array(
        checknull($this->values['check_amt_from']),
        checknull($this->values['check_amt_to']),
      );
    }

    if ($this->values['contractno']) {
      $this->criteria['value']['contract_id'] = strtoupper($this->values['contractno']);
    }

    if ($this->values['document_id']) {
      $this->criteria['value']['document_id'] = $this->values['document_id'];
    }

    if ($this->values['entity_contract_number']) {
      $this->criteria['value']['entity_contract_number'] = $this->values['entity_contract_number'];
    }

    if ($this->values['date_filter'] == '0') {
      if ($this->values['year'] && $this->values['year'] != '0') {
        if (startsWith($this->values['year'], 'F')) {
          $this->criteria['value']['fiscal_year'] = ltrim($this->values['year'], 'FY');
        } elseif (startsWith($this->values['year'], 'C')) {
          $this->criteria['value']['calendar_year'] = ltrim($this->values['year'], 'CY');
        }
      }
    } else {
      if ($this->values['date_filter'] == '1') {
        if ($this->values['issuedfrom'] !== '' || $this->values['issuedto'] !== '') {
          $this->criteria['range']['issue_date'] = array(
            checknull($this->values['issuedfrom']),
            checknull($this->values['issuedto'])
          );
        }
      }
    }

    $this->_process_datasource_values();

    return;
  }

  protected function _process_datasource_values()
  {
  }


  public function checkbook_datafeeds_spending_validate(&$form, &$form_state)
  {
    $agency = $form_state['values']['agency'];
    $agency_code = emptyToZero($agency);
    $check_from = $form_state['values']['check_amt_from'];
    $check_to = $form_state['values']['check_amt_to'];
    $date_from = $form_state['values']['issuedfrom'];
    $date_to = $form_state['values']['issuedto'];
    $vendor = $form_state['values']['payee_name'];
    $contract_id = $form_state['values']['contractno'];

    if ($check_from && !is_numeric($check_from)) {
      form_set_error('check_amt_from', t('Check Amount must be a number.'));
    }

    if ($check_to && !is_numeric($check_to)) {
      form_set_error('check_amt_to', t('Check Amount must be a number.'));
    }

    if ($check_from && $check_to && $check_to < $check_from) {
      form_set_error('check_amt_to', t('Invalid range for Check Amount.'));
    }

    if ($date_from && !checkDateFormat($date_from)) {
      form_set_error('issuedfrom', t('Issue Date must be a valid date (YYYY-MM-DD).'));
    }

    if ($date_to && !checkDateFormat($date_to)) {
      form_set_error('issuedto', t('Issue Date must be a valid date (YYYY-MM-DD).'));
    }

    if ($date_from && $date_to && strtotime($date_to) < strtotime($date_from)) {
      form_set_error('issuedto', t('Invalid range for Issue Date.'));
    }

    //Contract ID
    if ($contract_id && preg_match("/(^MMA1|^MA1)/", strtoupper($contract_id))) {
      form_set_error('contractno', t('Spending information for MMA1 and MA1 Contracts can be viewed using the Contract Data feeds feature.'));
    }

    //Vendor
    if ($vendor) {
      $pattern = "/.*?(\\[.*?\\])/is";
      preg_match($pattern, $vendor, $vmatches);
      if (!$vmatches) {
        try {
          $dataController = data_controller_get_instance();
          switch ($this->data_source) {
            case \Datasource::OGE:

              $query = "SELECT DISTINCT vendor_name ";
              $query .= "FROM disbursement_line_item_details dld ";
              $query .= "JOIN ref_spending_category rsc on rsc.spending_category_id = dld.spending_category_id ";
              $query .= "JOIN ref_agency ra on ra.agency_code = dld.agency_code ";
              $query .= "WHERE ra.is_display = 'Y' ";
              $query .= "AND vendor_name ilike '" . $vendor . "'";
              if ($agency_code) $query .= "AND dld.agency_code = '" . $agency_code . "'";

              $results = _checkbook_project_execute_sql($query, "main", $this->data_source);

              break;
            default:
              $results = $dataController->queryDataset('checkbook:vendor', array('vendor_customer_code'), array('vendor_customer_code' => $vendor));
              break;
          }
        } catch (\Exception $e) {
          log_error($e->getMessage());
        }
        if (!($results[0] ?? true)) {
          switch ($this->data_source) {
            case \Datasource::OGE:
              form_set_error('payee_name', t('Please enter a valid vendor name.'));
              break;
            default:
              form_set_error('payee_name', t('Please enter a valid vendor code.'));
              break;
          }
        }
      }
    }

    //Set the hidden filed values on Spending form
    $form_state['complete form']['agency_hidden']['#value'] = $form_state['values']['agency'];
    $form_state['complete form']['date_filter_hidden']['#value'] = $form_state['values']['date_filter'];

    $this->_validate_by_datasource($form, $form_state);
  }

  protected function _validate_by_datasource(&$form, &$form_state)
  {
  }
}
