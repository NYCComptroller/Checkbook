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
    $this->_process_confirmation_common();
    $modified_form = $this->_process_confirmation_end();
    $form_state = $this->form_state;
    return $modified_form;
  }

  protected function _process_confirmation_common()
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

    if ($this->values['agency']) {
      $agency = $this->values['agency'];
      $this->form['filter']['fund_class'] = array(
        '#markup' => '<div><strong>' . $this->agency_label . ':</strong> ' . $agency . '</div>',
      );
      $this->user_criteria['Agency'] = $this->values['agency'];
      $this->formatted_search_criteria[$this->agency_label] = $agency;
    }
    if ($this->values['dept'] && $this->values['dept'] != 'Select Department') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->values['dept'])) {
        $this->values['dept'] = htmlspecialchars($this->values['dept']);
      }
      $this->form['filter']['department'] = array('#markup' => '<div><strong>Department:</strong>' . $this->values['dept'] . '</div>');
      $this->user_criteria['Department'] = $this->values['dept'];
      $this->formatted_search_criteria['Department'] = $this->values['dept'];
    }
    if ($this->values['expense_category'] && $this->values['expense_category'] != 'Select Expense Category') {
      // converts special characters to HTML entities
      if (preg_match('/[\'^£$%&*()}{@#~?><,|=_+¬-]/', $this->values['expense_category'])) {
        $this->values['expense_category'] = htmlspecialchars($this->values['expense_category']);
      }
      $this->form['filter']['expense_category'] = array('#markup' => '<div><strong>Expense Category:</strong> ' . $this->values['expense_category'] . '</div>');
      $this->user_criteria['Expense Category'] = $this->values['expense_category'];
      $this->formatted_search_criteria['Expense Category'] = $this->values['expense_category'];
    }

    $this->process_expense_type();
    $this->process_industry();

    if ($this->values['mwbe_category']) {
      $this->form['filter']['mwbe_category'] = array('#markup' => '<div><strong>M/WBE Category:</strong> ' . MappingUtil::getCurrenEthnicityName(explode('~', $this->values['mwbe_category'])) . '</div>');
      $this->user_criteria['M/WBE Category'] = $this->values['mwbe_category'];
      $this->formatted_search_criteria['M/WBE Category'] = MappingUtil::getCurrenEthnicityName(explode('~', $this->values['mwbe_category']));
    }

    if ($this->values['payee_name']) {
      $this->form['filter']['payee_name'] = array(
        '#markup' => '<div><strong>Payee Name:</strong> ' . $this->values['payee_name'] . '</div>',
      );
      $this->user_criteria['Payee Name'] = $this->values['payee_name'];
      $this->formatted_search_criteria['Payee Name'] = $this->values['payee_name'];
    }
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
    if ($this->values['contractno']) {
      $this->form['filter']['contractno'] = array(
        '#markup' => '<div><strong>Contract ID:</strong> ' . $this->values['contractno'] . '</div>',
      );
      $this->user_criteria['Contract ID'] = $this->values['contractno'];
      $this->formatted_search_criteria['Contract ID'] = $this->values['contractno'];
    }
    if ($this->values['document_id']) {
      $this->form['filter']['document_id'] = array(
        '#markup' => '<div><strong>Document ID:</strong> ' . $this->values['document_id'] . '</div>',
      );
      $this->user_criteria['Document ID'] = $this->values['document_id'];
      $this->formatted_search_criteria['Document ID'] = $this->values['document_id'];
    }
    if ($this->values['commodity_line']) {
      $this->form['filter']['commodity_line'] = array(
        '#markup' => '<div><strong>Commodity Line:</strong> ' . $this->values['commodity_line'] . '</div>',
      );
      $this->user_criteria['Commodity Line'] = $this->values['commodity_line'];
      $this->formatted_search_criteria['Commodity Line'] = $this->values['commodity_line'];
    }
    if ($this->values['entity_contract_number']) {
      $this->form['filter']['entity_contract_number'] = array(
        '#markup' => '<div><strong>Entity Contract #:</strong> ' . $this->values['entity_contract_number'] . '</div>',
      );
      $this->user_criteria['Entity Contract #'] = $this->values['entity_contract_number'];
      $this->formatted_search_criteria['Entity Contract #'] = $this->values['entity_contract_number'];
    }
    if ($this->values['capital_project']) {
      $this->form['filter']['capital_project'] = array(
        '#markup' => '<div><strong>Capital Project:</strong> ' . $this->values['capital_project'] . '</div>',
      );
      $this->user_criteria['Capital Project'] = $this->values['capital_project'];
      $this->formatted_search_criteria['Capital Project'] = $this->values['capital_project'];
    }
    if ($this->values['budget_name']) {
      $this->form['filter']['budget_name'] = array(
        '#markup' => '<div><strong>Budget Name:</strong> ' . $this->values['budget_name'] . '</div>',
      );
      $this->user_criteria['Budget Name'] = $this->values['budget_name'];
      $this->formatted_search_criteria['Budget Name'] = $this->values['budget_name'];
    }

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
    if ($this->values['date_filter'] == 0) {
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
      } else {
        $this->form['filter']['year'] = array(
          '#markup' => '<div><strong>Year:</strong> All Years</div>',
        );
        $this->formatted_search_criteria['Year'] = $this->values['year'];
      }
    }

    return;
  }

  /**
   * @return array
   */
  private function _process_confirmation_end()
  {
    $this->user_criteria['Formatted'] = $this->formatted_search_criteria;
    $this->_checkbook_datafeeds_process_spending_values();
    $this->form_state['step_information']['confirmation']['stored_values']['criteria'] = $this->criteria;
    $this->form_state['step_information']['confirmation']['stored_values']['user_criteria'] = $this->user_criteria;
    $modified_form = checkbook_datafeeds_end_of_confirmation_form($this->form, $this->form_state, $this->criteria, $this->response_type, 'spending');
    return $modified_form;
  }

  abstract protected function process_expense_type();

  protected function process_industry()
  {
    if ($this->values['industry']) {
      preg_match("/.*?(\\[.*?])/is", $this->values['industry'], $matches);
      $industry_type_name = str_replace($matches[1], "", $matches[0]);
      $industry_type_id = trim($matches[1], '[ ]');
      $this->form['filter']['industry'] = array('#markup' => '<div><strong>Industry:</strong> ' . $industry_type_name . '</div>');
      $this->user_criteria['Industry'] = $industry_type_id;
      $this->formatted_search_criteria['Industry'] = $industry_type_name;
    }
  }

  /**
   * Convert values from Spending section of form to an array format expected by API SearchCriteria.
   */
  private function _checkbook_datafeeds_process_spending_values()
  {
    global $conf;
    $response_columns = $this->values[$this->filtered_columns_container];

    $this->criteria = [
      'global' => [
        //Set data source for query
        'type_of_data' => $this->type_of_data,
        'records_from' => 1,
        'max_records' => $conf['check_book']['data_feeds']['max_record_limit'] ?? 200000,
      ],
      'responseColumns' => $response_columns
    ];

    if ($this->values['capital_project']) {
      $this->criteria['value']['capital_project_code'] = $this->values['capital_project'];
    }
    if ($this->values['budget_name']) {
      $this->criteria['value']['budget_name'] = $this->values['budget_name'];
    }
    if ($this->values['check_amt_from'] !== '' || $this->values['check_amt_to'] !== '') {
      $this->criteria['range']['check_amount'] = array(
        checknull($this->values['check_amt_from']),
        checknull($this->values['check_amt_to']),
      );
    }
    if ($this->values['commodity_line']) {
      $this->criteria['value']['commodity_line'] = $this->values['commodity_line'];
    }
    if ($this->values['dept'] && $this->values['dept'] != 'Select Department') {
      preg_match($this->bracket_value_pattern, $this->values['dept'], $department_matches);
      $this->criteria['value']['department_code'] = trim($department_matches[1], '[ ]');
    }
    if ($this->values['document_id']) {
      $this->criteria['value']['document_id'] = $this->values['document_id'];
    }
    if ($this->values['entity_contract_number']) {
      $this->criteria['value']['entity_contract_number'] = $this->values['entity_contract_number'];
    }
    if ($this->values['expense_category'] && $this->values['expense_category'] != 'Select Expense Category') {
      preg_match($this->bracket_value_pattern, $this->values['expense_category'], $expense_category_matches);
      $this->criteria['value']['expense_category'] = trim($expense_category_matches[1], '[ ]');
    }

    if ($this->values['date_filter'] == '0') {
      if ($this->values['year'] && $this->values['year'] != 'ALL') {
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

}
