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

namespace Drupal\checkbook_datafeeds\Contracts;

class ContractsFeedNycedc extends ContractsFeed
{
  protected $data_source = 'checkbook_oge';
  protected $type_of_data = 'Contracts_OGE';
  protected $filtered_columns_container = 'oge_column_select';
  protected $oge_label = 'Other Government Entity';
  protected $oge_name_code = "NEW YORK CITY ECONOMIC DEVELOPMENT CORPORATION[z81]";
  protected $oge_name = "NEW YORK CITY ECONOMIC DEVELOPMENT CORPORATION";

  protected function _process_user_criteria_by_datasource(){
    if ($this->form_state->getValue('df_contract_status')) {
      $this->form['filter']['df_contract_status'] = array('#markup' => '<div><strong>Status:</strong> ' . $this->form_state->getValue('df_contract_status') . '</div>');
      $this->user_criteria['Contract Status'] = $this->form_state->getValue('df_contract_status');
      $this->formatted_search_criteria['Status'] = $this->form_state->getValue('df_contract_status');
    }
    if ($this->form_state->getValue('vendor')) {
      $this->form['filter']['vendor'] = array(
        '#markup' => '<div><strong>Prime Vendor:</strong> ' . $this->form_state->getValue('vendor') . '</div>',
      );
      $this->user_criteria['Prime Vendor'] = $this->form_state->getValue('vendor');
      $this->formatted_search_criteria['Prime Vendor'] = $this->form_state->getValue('vendor');
    }
    if ($this->form_state->getValue('contract_type')) {
      if ($this->form_state->getValue('contract_type') != 'No Contract Type Selected') {
        $this->form['filter']['contract_type'] = array('#markup' => '<div><strong>Contract Type:</strong> ' . $this->form_state->getValue('contract_type') . '</div>');
        $this->user_criteria['Contract Type'] = $this->form_state->getValue('contract_type');
        $this->formatted_search_criteria['Contract Type'] = $this->form_state->getValue('contract_type');
      }
    }
    if ($this->form_state->getValue('contractno')) {
      $this->form['filter']['contractno'] = array(
        '#markup' => '<div><strong>Contract ID:</strong> ' . $this->form_state->getValue('contractno') . '</div>',
      );
      $this->user_criteria['Contract ID'] = $this->form_state->getValue('contractno');
      $this->formatted_search_criteria[''] = null;
    }
    if ($this->form_state->getValue('commodity_line')) {
      $this->form['filter']['commodity_line'] = array(
        '#markup' => '<div><strong>Commodity Line:</strong> ' . $this->form_state->getValue('commodity_line') . '</div>',
      );
      $this->user_criteria['Commodity Line'] = $this->form_state->getValue('commodity_line');
      $this->formatted_search_criteria['Commodity Line'] = $this->form_state->getValue('commodity_line');
    }
    if ($this->form_state->getValue('entity_contract_number')) {
      $this->form['filter']['entity_contract_number'] = array(
        '#markup' => '<div><strong>Entity Contract #:</strong> ' . $this->form_state->getValue('entity_contract_number') . '</div>',
      );
      $this->user_criteria['Entity Contract #'] = $this->form_state->getValue('entity_contract_number');
      $this->formatted_search_criteria['Entity Contract #'] = $this->form_state->getValue('entity_contract_number');
    }
    if ($this->form_state->getValue('budget_name')) {
      $this->form['filter']['budget_name'] = array(
        '#markup' => '<div><strong>Budget Name:</strong> ' . $this->form_state->getValue('budget_name') . '</div>',
      );
      $this->user_criteria['Budget Name'] = $this->form_state->getValue('budget_name');
      $this->formatted_search_criteria['Budget Name'] = $this->form_state->getValue('budget_name');
    }
    if (($this->form_state->getValue('currentamtfrom') || $this->form_state->getValue('currentamtfrom') === "0") && ($this->form_state->getValue('currentamtto') || $this->form_state->getValue('currentamtto') === "0")) {
      $this->form['filter']['current_amount'] = array('#markup' => '<div><strong>Current Amount:</strong> Greater Than Equal to: $' . $this->form_state->getValue('currentamtfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('currentamtto') . '</div>');
      $this->user_criteria['Current Amount Greater Than'] = $this->form_state->getValue('currentamtfrom');
      $this->user_criteria['Current Amount Less Than'] = $this->form_state->getValue('currentamtto');
      $this->formatted_search_criteria['Current Amount'] = 'Greater Than Equal to: $' . $this->form_state->getValue('currentamtfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('currentamtto');
    } elseif (!$this->form_state->getValue('currentamtfrom') && ($this->form_state->getValue('currentamtto') || $this->form_state->getValue('currentamtto') === "0")) {
      $this->form['filter']['current_amount'] = array('#markup' => '<div><strong>Current Amount:</strong> Less Than Equal to: $' . $this->form_state->getValue('currentamtto') . '</div>');
      $this->user_criteria['Current Amount Less Than'] = $this->form_state->getValue('currentamtto');
      $this->formatted_search_criteria['Current Amount'] = 'Less Than Equal to: $' . $this->form_state->getValue('currentamtto');
    } elseif (($this->form_state->getValue('currentamtfrom') || $this->form_state->getValue('currentamtfrom') === "0") && !$this->form_state->getValue('currentamtto')) {
      $this->form['filter']['current_amount'] = array('#markup' => '<div><strong>Current Amount:</strong> Greater Than Equal to: $' . $this->form_state->getValue('currentamtfrom') . '</div>');
      $this->user_criteria['Current Amount Greater Than'] = $this->form_state->getValue('currentamtfrom');
      $this->formatted_search_criteria['Current Amount'] = 'Greater Than Equal to: $' . $this->form_state->getValue('currentamtfrom');
    }
    if ($this->form_state->getValue('recdateto') && $this->form_state->getValue('recdateto')) {
      $this->form['filter']['received_date'] = array('#markup' => '<div><strong>Received Date:</strong> Greater Than Equal to: ' . $this->form_state->getValue('recdateto') . ' Less Than Equal to: ' . $this->form_state->getValue('recdateto') . '</div>');
      $this->user_criteria['Received Date Greater Than'] = $this->form_state->getValue('recdateto');
      $this->user_criteria['Received Date Less Than'] = $this->form_state->getValue('recdateto');
      $this->formatted_search_criteria['Received Date'] = 'Greater Than Equal to: ' . $this->form_state->getValue('recdateto') . ' Less Than Equal to: ' . $this->form_state->getValue('recdateto');
    } elseif (!$this->form_state->getValue('recdateto') && $this->form_state->getValue('recdateto')) {
      $this->form['filter']['received_date'] = array('#markup' => '<div><strong>Received Date:</strong> Less Than Equal to: ' . $this->form_state->getValue('recdateto') . '</div>');
      $this->user_criteria['Received Date Less Than'] = $this->form_state->getValue('recdateto');
      $this->formatted_search_criteria['Received Date'] = 'Less Than Equal to: ' . $this->form_state->getValue('recdateto');
    } elseif ($this->form_state->getValue('recdateto') && !$this->form_state->getValue('recdateto')) {
      $this->form['filter']['received_date'] = array('#markup' => '<div><strong>Received Date:</strong> Greater Than Equal to: ' . $this->form_state->getValue('recdateto') . '</div>');
      $this->user_criteria['Received Date Greater Than'] = $this->form_state->getValue('recdateto');
      $this->formatted_search_criteria['Received Date'] = 'Greater Than Equal to: ' . $this->form_state->getValue('recdateto');
    }
    if ($this->form_state->getValue('agency')) {
      $this->form['filter']['agency'] = array(
        '#markup' => '<div><strong>Other Government Entities:</strong> NEW YORK CITY ECONOMIC DEVELOPMENT CORPORATION[z81]</div>',
      );
    }
    if ($this->form_state->getValue('category')) {
      $this->form['filter']['category'] = array('#markup' => '<div><strong>Category:</strong> ' . $this->form_state->getValue('category') . '</div>');
      $this->user_criteria['Category'] = $this->form_state->getValue('category');
      $this->formatted_search_criteria['Category'] = $this->form_state->getValue('category');
    }
    if ($this->form_state->getValue('purpose')) {
      $this->form['filter']['purpose'] = array(
        '#markup' => '<div><strong>Purpose:</strong> ' . $this->form_state->getValue('purpose') . '</div>',
      );
      $this->user_criteria['Purpose'] = $this->form_state->getValue('purpose');
      $this->formatted_search_criteria['Purpose'] = $this->form_state->getValue('purpose');
    }
    if ($this->form_state->getValue('pin')) {
      $this->form['filter']['pin'] = array(
        '#markup' => '<div><strong>PIN:</strong> ' . $this->form_state->getValue('pin') . '</div>',
      );
      $this->user_criteria['PIN'] = $this->form_state->getValue('pin');
      $this->formatted_search_criteria['PIN'] = $this->form_state->getValue('pin');
    }
    if ($this->form_state->getValue('apt_pin')) {
      $this->form['filter']['apt_pin'] = array(
        '#markup' => '<div><strong>APT PIN:</strong> ' . $this->form_state->getValue('apt_pin') . '</div>',
      );
      $this->user_criteria['APT PIN'] = $this->form_state->getValue('apt_pin');
      $this->formatted_search_criteria['APT PIN'] = $this->form_state->getValue('apt_pin');
    }
    if ($this->form_state->getValue('award_method')) {
      if ($this->form_state->getValue('award_method') != 'No Award Method Selected') {
        $this->form['filter']['award_method'] = array('#markup' => '<div><strong>Award Method:</strong> ' . $this->form_state->getValue('award_method') . '</div>');
        $this->user_criteria['Award Method'] = $this->form_state->getValue('award_method');
        $this->formatted_search_criteria['Award Method'] = $this->form_state->getValue('award_method');
      }
    }
    if ($this->form_state->getValue('startdatefrom') && $this->form_state->getValue('startdateto')) {
      $this->form['filter']['start_date'] = array('#markup' => '<div><strong>Start Date:</strong> Greater Than Equal to: ' . $this->form_state->getValue('startdatefrom') . ' Less Than Equal to: ' . $this->form_state->getValue('startdateto') . '</div>');
      $this->user_criteria['Start Date Greater Than'] = $this->form_state->getValue('startdatefrom');
      $this->user_criteria['Start Date Less Than'] = $this->form_state->getValue('startdateto');
      $this->formatted_search_criteria['Start Date'] = 'Greater Than Equal to: ' . $this->form_state->getValue('startdatefrom') . ' Less Than Equal to: ' . $this->form_state->getValue('startdateto');
    } elseif (!$this->form_state->getValue('startdatefrom') && $this->form_state->getValue('startdateto')) {
      $this->form['filter']['start_date'] = array('#markup' => '<div><strong>Start Date:</strong> Less Than Equal to: ' . $this->form_state->getValue('startdateto') . '</div>');
      $this->user_criteria['Start Date Less Than'] = $this->form_state->getValue('startdateto');
      $this->formatted_search_criteria['Start Date'] = 'Less Than Equal to: ' . $this->form_state->getValue('startdateto');
    } elseif ($this->form_state->getValue('startdatefrom') && !$this->form_state->getValue('startdateto')) {
      $this->form['filter']['start_date'] = array('#markup' => '<div><strong>Start Date:</strong> Greater Than Equal to: ' . $this->form_state->getValue('startdatefrom') . '</div>');
      $this->user_criteria['Start Date Greater Than'] = $this->form_state->getValue('startdatefrom');
      $this->formatted_search_criteria['Start Date'] = 'Greater Than Equal to: ' . $this->form_state->getValue('startdatefrom');
    }
    if ($this->form_state->getValue('enddatefrom') && $this->form_state->getValue('enddateto')) {
      $this->form['filter']['end_date'] = array('#markup' => '<div><strong>End Date:</strong> Greater Than Equal to: ' . $this->form_state->getValue('enddatefrom') . ' Less Than Equal to: ' . $this->form_state->getValue('enddateto') . '</div>');
      $this->user_criteria['End Date Greater Than'] = $this->form_state->getValue('enddatefrom');
      $this->user_criteria['End Date Less Than'] = $this->form_state->getValue('enddateto');
      $this->formatted_search_criteria['End Date'] = 'Greater Than Equal to: ' . $this->form_state->getValue('enddatefrom') . ' Less Than Equal to: ' . $this->form_state->getValue('enddateto');
    } elseif (!$this->form_state->getValue('enddatefrom') && $this->form_state->getValue('enddateto')) {
      $this->form['filter']['end_date'] = array('#markup' => '<div><strong>End Date:</strong> Less Than Equal to: ' . $this->form_state->getValue('enddateto') . '</div>');
      $this->user_criteria['End Date Less Than'] = $this->form_state->getValue('enddateto');
      $this->formatted_search_criteria['End Date'] = 'Less Than Equal to: ' . $this->form_state->getValue('enddateto');
    } elseif ($this->form_state->getValue('enddatefrom') && !$this->form_state->getValue('enddateto')) {
      $this->form['filter']['end_date'] = array('#markup' => '<div><strong>End Date:</strong> Greater Than Equal to: ' . $this->form_state->getValue('enddatefrom') . '</div>');
      $this->user_criteria['End Date Greater Than'] = $this->form_state->getValue('enddatefrom');
      $this->formatted_search_criteria['End Date'] = 'Greater Than Equal to: ' . $this->form_state->getValue('enddatefrom');
    }
    if ($this->form_state->hasValue('year')) {
      if ($this->form_state->getValue('year') == '0') {
        $this->form['filter']['year'] = array(
          '#markup' => '<div><strong>Year:</strong> All Years</div>',
        );
        $this->formatted_search_criteria['Year'] = 'All Years';
      } else {
        $this->form['filter']['year'] = array(
          '#markup' => '<div><strong>Year:</strong> ' . substr($this->form_state->getValue('year'), 0, -4) . ' ' . substr($this->form_state->getValue('year'), -4) . '</div>',
        );
        $this->formatted_search_criteria['Year'] = $this->form_state->getValue('year');
      }
    }
  }

  /*protected function _process_datasource_values()
  {
    if ($this->form_state->hasValue('nycedc_expense_type') && $this->form_state->getValue('expense type') != 'ts') {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('nycedc_expense_type'), $etmatches);
      $this->criteria['value']['contracts_category'] = trim($etmatches[1], '[ ]');
    }

    if ($this->form_state->hasValue('capital_project')) {
      $this->criteria['value']['capital_project_code'] = $this->form_state->getValue('capital_project');
    }

    if ($this->form_state->hasValue('commodity_line')) {
      $this->criteria['value']['commodity_line'] = $this->form_state->getValue('commodity_line');
    }

    if ($this->form_state->hasValue('capital_project')) {
      $this->criteria['value']['budget_name'] = $this->form_state->getValue('capital_project');
    }

    if ($this->form_state->hasValue('year') && $this->form_state->getValue('year') != '0') {
      $this->criteria['value']['fiscal_year'] = ltrim($this->form_state->getValue('year'), 'FY');
    }
  }*/

  protected function _validate_by_datasource(&$form, &$form_state)
  {
    //Validate Commodity Line
//    $entity_contractno = $form_state['values']['entity_contract_number'];
    $entity_contractno = $form_state->getValue('entity_contract_number');
//    $commodity_line = $form_state['values']['commodity_line'];
    $commodity_line = $form_state->getValue('commodity_line');
    if ($commodity_line && !is_numeric($commodity_line)) {
//      form_set_error('commodity_line', t('Commodity Line must be a number.'));
      $form_state->setErrorByName('commodity_line', t('Commodity Line must be a number.'));
    }
    if ($entity_contractno && !is_numeric($entity_contractno)) {
//      form_set_error('entity_contract_number', t('Entity Contract # must be a number'));
      $form_state->setErrorByName('entity_contract_number', t('Entity Contract # must be a number.'));
    }

    // Check Columns
    $responseColumns = $form_state->getValue('oge_column_select');
    if (empty(array_filter($responseColumns))) {
      $form_state->setErrorByName('column_select', t('You must select at least one column.'));
    }

//  $multi_select_hidden = isset($form_state['input']['oge_column_select']) ? '|' . implode('||', $form_state['input']['oge_column_select']) . '|' : '';
    $multi_select_hidden = $form_state->hasValue('oge_column_select') ? '|' . implode('||', $form_state->getValue('oge_column_select')) . '|' : '';

    if (!$multi_select_hidden) {
//      form_set_error('oge_column_select', t('You must select at least one column.'));
      $form_state->setErrorByName('oge_column_select', t('You must select at least one column.'));
    }
  }
}
