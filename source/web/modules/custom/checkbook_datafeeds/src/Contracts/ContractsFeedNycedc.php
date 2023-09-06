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
    //Status
    $this->_process_user_criteria_by_datasource_single_field_and_check('df_contract_status', 'df_contract_status', 'Status', 'Contract Status');

    //Prime Vendor
    $this->_process_user_criteria_by_datasource_single_field_and_check('vendor', 'vendor', 'Prime Vendor');

    //Contract Type
    if ($this->form_state->getValue('contract_type') && $this->form_state->getValue('contract_type') != 'No Contract Type Selected') {
      $this->_process_user_criteria_by_datasource_single_field('contract_type', 'contract_type', 'Contract Type');
    }

    //Contract ID
    if ($this->form_state->getValue('contractno')) {
      $this->form['filter']['contractno'] = array(
        '#markup' => '<div><strong>Contract ID:</strong> ' . $this->form_state->getValue('contractno') . '</div>',
      );
      $this->user_criteria['Contract ID'] = $this->form_state->getValue('contractno');
      $this->formatted_search_criteria[''] = null;
    }

    //Commodity Line
    $this->_process_user_criteria_by_datasource_single_field_and_check('commodity_line', 'commodity_line', 'Commodity Line');

    //Entity Contract #
    $this->_process_user_criteria_by_datasource_single_field_and_check('entity_contract_number', 'entity_contract_number', 'Entity Contract #');

    //Budget Name
    $this->_process_user_criteria_by_datasource_single_field_and_check('budget_name', 'budget_name', 'Budget Name');

    $this->_process_user_criteria_by_datasource_ranged_amount_field('currentamtfrom', 'currentamtto', 'current_amount', 'Current Amount');

    $this->_process_user_criteria_by_datasource_ranged_date_field('recdatefrom', 'recdateto', 'received_date', 'Received Date');

    if ($this->form_state->getValue('agency')) {
      $this->form['filter']['agency'] = array(
        '#markup' => '<div><strong>Other Government Entities:</strong> NEW YORK CITY ECONOMIC DEVELOPMENT CORPORATION[z81]</div>',
      );
    }
    //Category
    $this->_process_user_criteria_by_datasource_single_field_and_check('category', 'category', 'Category');

    //Purpose
    $this->_process_user_criteria_by_datasource_single_field_and_check('purpose', 'purpose', 'Purpose');

    //PIN
    $this->_process_user_criteria_by_datasource_single_field_and_check('pin', 'pin', 'PIN');

    //APT PIN
    $this->_process_user_criteria_by_datasource_single_field_and_check('apt_pin', 'apt_pin', 'APT PIN');

    if ($this->form_state->getValue('award_method') && $this->form_state->getValue('award_method') != 'No Award Method Selected') {
      $this->_process_user_criteria_by_datasource_single_field('award_method', 'award_method', 'Award Method');
    }

    $this->_process_user_criteria_by_datasource_ranged_date_field('startdatefrom', 'startdateto', 'start_date', 'Start Date');

    $this->_process_user_criteria_by_datasource_ranged_date_field('enddatefrom', 'enddateto', 'end_date', 'End Date');

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

  protected function _validate_by_datasource(&$form, &$form_state)
  {
    //Validate Commodity Line
    $entity_contractno = $form_state->getValue('entity_contract_number');

    $commodity_line = $form_state->getValue('commodity_line');
    if ($commodity_line && !is_numeric($commodity_line)) {
      $form_state->setErrorByName('commodity_line', t('Commodity Line must be a number.'));
    }
    if ($entity_contractno && !is_numeric($entity_contractno)) {
      $form_state->setErrorByName('entity_contract_number', t('Entity Contract # must be a number.'));
    }

    // Check Columns
    $responseColumns = $form_state->getValue('oge_column_select');
    if (empty(array_filter($responseColumns))) {
      $form_state->setErrorByName('column_select', t($this->select_at_least_one_column_message));
    }

    $multi_select_hidden = $form_state->hasValue('oge_column_select') ? '|' . implode('||', $form_state->getValue('oge_column_select')) . '|' : '';

    if (!$multi_select_hidden) {
      $form_state->setErrorByName('oge_column_select', t($this->select_at_least_one_column_message));
    }
  }
}
