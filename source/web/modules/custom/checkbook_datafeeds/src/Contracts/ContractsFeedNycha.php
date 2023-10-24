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

class ContractsFeedNycha extends ContractsFeed
{
  protected $data_source = 'checkbook_nycha';
  protected $type_of_data = 'Contracts_NYCHA';
  protected $filtered_columns_container = 'nycha_column_select';
  protected $oge_label = 'Other Government Entity';
  protected $oge_name_code = "NEW YORK CITY HOUSING AUTHORITY[996]";
  protected $oge_name = "NEW YORK CITY HOUSING AUTHORITY";

  protected function _process_user_criteria_by_datasource(){

    $this->_process_user_criteria_by_datasource_single_field_and_check('purchase_order_type', 'purchase_order_type', 'Purchase Order Type');

    $this->_process_user_criteria_by_datasource_single_field_and_check('nycha_contract_id', 'nycha_contract_id', 'Contract ID');

    $this->_process_user_criteria_by_datasource_single_field_and_check('nycha_vendor', 'nycha_vendor', 'Vendor');

    $this->_process_user_criteria_by_datasource_single_field_and_check('resp_center', 'resp_center', 'Responsibility Center');

    if ($this->form_state->getValue('nycha_contract_type') && $this->form_state->getValue('nycha_contract_type') != 'No Contract Type Selected') {
      $this->_process_user_criteria_by_datasource_single_field('nycha_contract_type', 'nycha_contract_type', 'Contract Type');
    }
    if ($this->form_state->getValue('nycha_awd_method') && $this->form_state->getValue('nycha_awd_method') != 'No Award Method Selected') {
      $this->_process_user_criteria_by_datasource_single_field('nycha_awd_method', 'nycha_awd_method', 'Award Method');
    }
    if ($this->form_state->getValue('nycha_industry') && $this->form_state->getValue('nycha_industry') != 'No Industry Selected') {
      $this->_process_user_criteria_by_datasource_single_field('nycha_industry', 'nycha_industry', 'Industry');
    }

    $this->_process_user_criteria_by_datasource_ranged_amount_field('nycha_currentamtfrom', 'nycha_currentamtto', 'nycha_current_amount', 'Current Amount');

    $this->_process_user_criteria_by_datasource_single_field_and_check('agency', 'agency', 'Other Government Entities');

    $this->_process_user_criteria_by_datasource_single_field_and_check('nycha_purpose', 'nycha_purpose', 'Purpose');

    $this->_process_user_criteria_by_datasource_single_field_and_check('nycha_apt_pin', 'nycha_apt_pin', 'PIN');

    $this->_process_user_criteria_by_datasource_ranged_date_field('nycha_startdatefrom', 'nycha_startdateto', 'nycha_start_date', 'Start Date');

    $this->_process_user_criteria_by_datasource_ranged_date_field('nycha_enddatefrom', 'nycha_enddateto', 'nycha_end_date', 'End Date');

    $this->_process_user_criteria_by_datasource_ranged_date_field('nycha_appr_datefrom', 'nycha_appr_dateto', 'nycha_approved_date', 'Approved Date');

    if ($this->form_state->hasValue('nycha_year')) {
      if ($this->form_state->getValue('nycha_year') == '0') {
        $this->form['filter']['nycha_year'] = array('#markup' => '<div><strong>Year:</strong> All Years</div>',);
        $this->formatted_search_criteria['Year'] = 'All Years';
      } else {
        $this->form['filter']['nycha_year'] = array('#markup' => '<div><strong>Year:</strong> ' . substr($this->form_state->getValue('nycha_year'), 0, -4) . ' ' . substr($this->form_state->getValue('nycha_year'), -4) . '</div>',);
        $this->formatted_search_criteria['Year'] = $this->form_state->getValue('nycha_year');
      }
    }
  }

  protected function _validate_by_datasource(&$form, &$form_state)
  {
    $spent_amount_from = $form_state->getValue('spent_amt_from');
    $spent_amount_to = $form_state->getValue('spent_amt_to');

    if ($spent_amount_from && !is_numeric($spent_amount_from)) {
      $form_state->setErrorByName('spent_amt_from', t('Spent Amount must be a number.'));
    }

    if ($spent_amount_to && !is_numeric($spent_amount_to)) {
      $form_state->setErrorByName('spent_amt_to', t('Spent Amount must be a number.'));
    }
    if (is_numeric($spent_amount_from) && is_numeric($spent_amount_to) && $spent_amount_to < $spent_amount_from) {
      $form_state->setErrorByName('spent_amt_to', t('Invalid range for Spent Amount.'));
    }
    // Check Columns
    $responseColumns = $form_state->getValue('nycha_column_select');
    if (empty(array_filter($responseColumns))) {
      $form_state->setErrorByName('column_select', t($this->select_at_least_one_column_message));
    }

    $multi_select_hidden = $form_state->hasValue('nycha_column_select') ? '|' . implode('||', $form_state->getValue('nycha_column_select')) . '|' : '';
    if (!$multi_select_hidden) {
      $form_state->setErrorByName('nycha_column_select', t($this->select_at_least_one_column_message));
    }
  }
}
