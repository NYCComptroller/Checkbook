<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Drupal\checkbook_datafeeds\Spending;

use Drupal\checkbook_datafeeds\Utilities\FeedConstants;
use Drupal\checkbook_infrastructure_layer\Constants\Common\CheckbookDomain;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Drupal\checkbook_log\LogHelper;
use Drupal\Core\Form\FormStateInterface;
use Drupal\data_controller\Controller\DataQueryControllerProxy;

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
  public function process_confirmation($form, FormStateInterface &$form_state)
  {
    $this->form = $form;
    $this->form_state = $form_state;

    $this->_process_user_criteria_confirmation();
    $this->user_criteria['Formatted'] = $this->formatted_search_criteria;
    $this->_process_criteria();
    $this->form_state->set(['step_information', 'confirmation', 'stored_values', 'criteria'], $this->criteria);
    $this->form_state->set(['step_information', 'confirmation', 'stored_values', 'user_criteria'], $this->user_criteria);

    $modified_form = checkbook_datafeeds_end_of_confirmation_form($this->form, $this->form_state, $this->criteria, $this->response_type, CheckbookDomain::$SPENDING);
    $form_state = $this->form_state;
    return $modified_form;
  }

  protected function _process_user_criteria_confirmation()
  {
    $this->values = $this->form_state->get(['step_information','spending','stored_values']);
    $pvalues = $this->form_state->get('page_values');

    $this->response_type = $this->form_state->get(['step_information', 'type', 'stored_values', 'format']) ?? '';
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

    foreach ($this->selected_columns as $column) {
      $this->form['columns'][$column] = array('#markup' => '<div>' . $column . '</div>');
      $this->user_criteria['Columns'][] = $column;
    }

    $this->filtered_columns = checkbook_datafeeds_spending_filter_selected_columns($this->selected_columns, $this->data_source, $this->response_type, 'nycha_export');

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
      '#markup' => '<div><strong>Type of File:</strong> ' . $this->form_state->get(['step_information', 'type', 'stored_values', 'format']). '</div>',
    );
    $this->formatted_search_criteria['Type of File'] = $this->form_state->get(['step_information', 'type', 'stored_values', 'format']);

    $this->_process_user_criteria_by_datasource();

    //Issued Date
    if ($this->form_state->getValue('date_filter') == 1) {
      $this->_process_user_criteria_by_datasource_ranged_date_field('issuedfrom', 'issuedto', 'issued_date', 'Issued Date', 'Issue Date');
    }
  }

  protected function _process_user_criteria_by_datasource_single_field($field_name, $form_filter_key, $visual_field_name, $user_criteria_name = null) {
    $this->form['filter'][$form_filter_key] = array('#markup' => '<div><strong>'.$visual_field_name.':</strong> ' . $this->form_state->getValue($field_name) . '</div>');
    if (is_null($user_criteria_name)) {
      $this->user_criteria[$visual_field_name] = $this->form_state->getValue($field_name);
    } else {
      $this->user_criteria[$user_criteria_name] = $this->form_state->getValue($field_name);
    }
    $this->formatted_search_criteria[$visual_field_name] = $this->form_state->getValue($field_name);
  }

  protected function _process_user_criteria_by_datasource_single_field_and_check($field_name, $form_filter_key, $visual_field_name, $user_criteria_name = null) {
    if ($this->form_state->getValue($field_name)) {
      $this->_process_user_criteria_by_datasource_single_field($field_name, $form_filter_key, $visual_field_name, $user_criteria_name);
    }
  }

  protected function _process_user_criteria_by_datasource_ranged_field($start_field_name, $end_field_name, $form_filter_id, $visual_field_name, $field_type = 'amount', $formatted_search_criteria_key=null) {
    $and = '';
    if ($field_type == 'amount') {
      $user_criteria_greater_than_id = $visual_field_name . ' Greater Than';
      $user_criteria_less_than_id = $visual_field_name . ' Less Than';
      $greater_than_equal_text = 'Greater Than Equal to: $';
      $less_than_equal_text = 'Less Than Equal to: $';
      $and = 'and ';
    } else {
      $user_criteria_greater_than_id = $visual_field_name . ' After';
      $user_criteria_less_than_id = $visual_field_name . ' Before';
      $greater_than_equal_text = 'From: ';
      $less_than_equal_text = 'To: ';
    }

    $formatted_search_criteria_id = $formatted_search_criteria_key ?? $visual_field_name;

    if (($this->form_state->getValue($start_field_name) || ($field_type == 'amount' && $this->form_state->getValue($start_field_name) === "0")) && ($this->form_state->getValue($end_field_name) || ($field_type == 'amount' && $this->form_state->getValue($end_field_name) === "0"))) {
      $this->form['filter'][$form_filter_id] = array('#markup' => '<div><strong>'.$visual_field_name.':</strong> '.$greater_than_equal_text . $this->form_state->getValue($start_field_name) . ' '.$and . $less_than_equal_text . $this->form_state->getValue($end_field_name) . '</div>');
      $this->user_criteria[$user_criteria_greater_than_id] = $this->form_state->getValue($start_field_name);
      $this->user_criteria[$user_criteria_less_than_id] = $this->form_state->getValue($end_field_name);
      $this->formatted_search_criteria[$formatted_search_criteria_id] = $greater_than_equal_text . ' ' . $this->form_state->getValue($start_field_name) . ' and ' . $less_than_equal_text . ' ' . $this->form_state->getValue($end_field_name);
    } elseif (!$this->form_state->getValue($start_field_name) && ($this->form_state->getValue($end_field_name) || ($field_type == 'amount' && $this->form_state->getValue($end_field_name) === "0"))) {
      $this->form['filter'][$form_filter_id] = array('#markup' => '<div><strong>'.$visual_field_name.':</strong> ' . $less_than_equal_text . $this->form_state->getValue($end_field_name) . '</div>');
      $this->user_criteria[$user_criteria_less_than_id] = $this->form_state->getValue($end_field_name);
      $this->formatted_search_criteria[$formatted_search_criteria_id] = $less_than_equal_text . ' ' . $this->form_state->getValue($end_field_name);
    } elseif (($this->form_state->getValue($start_field_name) || ($field_type == 'amount' && $this->form_state->getValue($start_field_name) === "0")) && !$this->form_state->getValue($end_field_name)) {
      $this->form['filter'][$form_filter_id] = array('#markup' => '<div><strong>'.$visual_field_name.':</strong> ' . $greater_than_equal_text . $this->form_state->getValue($start_field_name) . '</div>');
      $this->user_criteria[$user_criteria_greater_than_id] = $this->form_state->getValue($start_field_name);
      $this->formatted_search_criteria[$formatted_search_criteria_id] = $greater_than_equal_text . ' ' . $this->form_state->getValue($start_field_name);
    }
  }

  protected function _process_user_criteria_by_datasource_ranged_date_field($start_field_name, $end_field_name, $form_filter_id, $visual_field_name, $formatted_search_criteria_key=null) {
    $this->_process_user_criteria_by_datasource_ranged_field($start_field_name, $end_field_name, $form_filter_id, $visual_field_name, 'date', $formatted_search_criteria_key);
  }

  protected function _process_user_criteria_by_datasource_ranged_amount_field($start_field_name, $end_field_name, $form_filter_id, $visual_field_name, $formatted_search_criteria_key=null) {
    $this->_process_user_criteria_by_datasource_ranged_field($start_field_name, $end_field_name, $form_filter_id, $visual_field_name, 'amount', $formatted_search_criteria_key);
  }

  abstract protected function _process_user_criteria_by_datasource();

  /**
   * Convert values from Spending section of form to an array format expected by API SearchCriteria.
   */
  private function _process_criteria()
  {
    $this->criteria = [
      'global' => [
        //Set data source for query
        'type_of_data' => $this->type_of_data,
        'records_from' => 1,
        'max_records' => \Drupal::config('check_book')->get('data_feeds')['max_record_limit'] ?? 200000,
      ],
      'responseColumns' => $this->filtered_columns
    ];

    if ((!empty($this->form_state->getValue('dept'))) && $this->form_state->getValue('dept') != 'Select Department' && $this->form_state->getValue('dept') != '0' && (!($this->data_source == Datasource::CITYWIDE && $this->values['agency'] == FeedConstants::CITYWIDE_ALL_AGENCIES))) {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('dept'), $department_matches);
      $this->criteria['value']['department_code'] = trim($department_matches[1], '[ ]');
    }

    if (!empty($this->form_state->getValue('expense_category')) && $this->form_state->getValue('expense_category') != 'Select Expense Category' && $this->form_state->getValue('expense_category') != '0' && (!($this->data_source == Datasource::CITYWIDE && $this->values['agency'] == FeedConstants::CITYWIDE_ALL_AGENCIES))) {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('expense_category'), $expense_category_matches);
      $this->criteria['value']['expense_category'] = trim($expense_category_matches[1], '[ ]');
    }

    if (!empty($this->form_state->getValue('payee_name'))) {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('payee_name'), $payee_name_matches);
      if ($payee_name_matches) {
        $this->criteria['value']['payee_code'] = trim($payee_name_matches[1], '[ ]');
      } else {
        $this->criteria['value']['payee_name'] = $this->form_state->getValue('payee_name');
      }
    }

    $this->_process_ranged_datasource_values('check_amt_from', 'check_amt_to', 'check_amount');

    if (!empty($this->form_state->getValue('contractno'))) {
      $this->criteria['value']['contract_id'] = strtoupper($this->form_state->getValue('contractno'));
    }

    if (!empty($this->form_state->getValue('document_id'))) {
      $this->criteria['value']['document_id'] = $this->form_state->getValue('document_id');
    }

    if (!empty($this->form_state->getValue('entity_contract_number'))) {
      $this->criteria['value']['entity_contract_number'] = $this->form_state->getValue('entity_contract_number');
    }

    if ($this->form_state->getValue('date_filter') == '1') {
      $this->_process_ranged_datasource_values('issuedfrom', 'issuedto', 'issue_date');
    }

    $this->_process_datasource_values();

  }

  /**
   * This function will process ranged values for datasource and place inside criteria
   *
   * @param $start_field_name
   * @param $end_field_name
   * @param $criteria_key
   * @param $pvalues
   *
   * @return void
   */
  protected function _process_ranged_datasource_values($start_field_name, $end_field_name, $criteria_key, $pvalues=null) {
    if (is_null($pvalues)) {
      $start = $this->form_state->getValue($start_field_name);
      $end = $this->form_state->getValue($end_field_name);
    } else {
      $start = $pvalues[$start_field_name];
      $end = $pvalues[$end_field_name];
    }
    if ($start !== '' || $end !== '') {
      $this->criteria['range'][$criteria_key] = array(
        checknull($start),
        checknull($end),
      );
    }
  }

  protected function _process_datasource_values()
  {
  }


  public function checkbook_datafeeds_spending_validate(&$form, &$form_state)
  {
    $agency = $form_state->getValue('agency');
    $agency_code = FormattingUtilities::emptyToZero($agency);
    $vendor = $form_state->getValue('payee_name');
    $contract_id = $form_state->getValue('contractno');

    checkbook_datafeeds_check_ranged_amounts($form_state, 'check_amt_from', 'check_amt_to', 'Check Amount', 'Check Amount From', 'Check Amount To');

    checkbook_datafeeds_check_ranged_date($form_state, 'issuedfrom', 'issuedto', 'Issue Date');

    //Contract ID
    if ($contract_id && preg_match("/(^MMA1|^MA1)/", strtoupper($contract_id))) {
      $form_state->setErrorByName('contractno', t('Spending information for MMA1 and MA1 Contracts can be viewed using the Contract Data feeds feature.'));
    }

    //Vendor
    $this->checkbook_datafeeds_spending_validate_vendor($form, $form_state);

    //Set the hidden filed values on Spending form
    $form_state->set([FeedConstants::COMPLETE_FORM, 'dept_hidden', '#value'], $form_state->getValue('dept'));
    $form_state->set([FeedConstants::COMPLETE_FORM, 'expense_category_hidden', '#value'], $form_state->getValue('expense_category'));
    $form_state->set([FeedConstants::COMPLETE_FORM, 'date_filter_hidden', '#value'], $form_state->getValue('date_filter'));

    //Hidden Field for multi-select
    switch ($this->data_source) {
      case Datasource::OGE:
        $multi_select_hidden = !empty( $form_state->getValue('oge_column_select') ) ? '|' . implode('||',  $form_state->getValue('oge_column_select') ) . '|' : '';
        break;
      case Datasource::NYCHA:
        $multi_select_hidden = !empty( $form_state->getValue('nycha_column_select') ) ? '|' . implode('||',  $form_state->getValue('nycha_column_select') ) . '|' : '';
        break;
      default:
        $multi_select_hidden = !empty( $form_state->getValue('column_select') ) ? '|' . implode('||',  $form_state->getValue('column_select') ) . '|' : '';
        break;
    }
    $form_state->set([FeedConstants::COMPLETE_FORM, 'hidden_multiple_value', '#value'], $multi_select_hidden);

    $this->_validate_by_datasource($form, $form_state);
  }

  protected function checkbook_datafeeds_spending_validate_vendor(&$form, &$form_state) {
    $agency = $form_state->getValue('agency');
    $vendor = $form_state->getValue('payee_name');
    $agency_code = FormattingUtilities::emptyToZero($agency);
    if ($vendor) {
      preg_match($this->bracket_value_pattern, $vendor, $vmatches);
      if (!$vmatches) {
        try {
          $dataController = data_controller_get_instance();
          switch ($this->data_source) {
            case Datasource::OGE:

              $query = "SELECT DISTINCT vendor_name ";
              $query .= "FROM disbursement_line_item_details dld ";
              $query .= "JOIN ref_spending_category rsc on rsc.spending_category_id = dld.spending_category_id ";
              $query .= "JOIN ref_agency ra on ra.agency_code = dld.agency_code ";
              $query .= "WHERE ra.is_display = 'Y' ";
              $query .= "AND vendor_name ilike '" . $vendor . "'";
              if ($agency_code) {
                $query .= "AND dld.agency_code = '" . $agency_code . "'";
              }

              $results = _checkbook_project_execute_sql($query, "main", $this->data_source);

              break;
            default:
              $results = $dataController->queryDataset('checkbook:vendor', array('vendor_customer_code'), array('vendor_customer_code' => $vendor));
              break;
          }
        } catch (\Exception $e) {
          LogHelper::log_error($e->getMessage());
        }
        if (!($results[0] ?? true)) {
          $the_message = ($this->data_source == Datasource::OGE) ? 'Please enter a valid vendor name.' : 'Please enter a valid vendor code.';
          $form_state->setErrorByName('payee_name', t($the_message));
        }
      }
    }
  }

  protected function _validate_by_datasource(&$form, &$form_state)
  {
  }

  // -------------------- Migrated from Data Controller to here ------------------------------------------------------------
  /**
   * @return DataQueryController
   */
  function data_controller_get_instance() {
    return DataQueryControllerProxy::getInstance();
  }
}
