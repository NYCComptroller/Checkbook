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
use Drupal\checkbook_project\MwbeUtilities\MappingUtil;

class ContractsFeedCitywide extends ContractsFeed
{
  protected $data_source = 'citywide';
  protected $type_of_data = 'Contracts';
  protected $filtered_columns_container = 'column_select';

  protected function _process_user_criteria_by_datasource(){

    $this->_process_user_criteria_by_datasource_single_field_and_check('df_contract_status', 'df_contract_status', 'Status', 'Contract Status');

    $this->_process_user_criteria_by_datasource_single_field_and_check('vendor', 'vendor', 'Vendor');

    if ($this->form_state->getValue('mwbe_category')) {
     $this->form['filter']['mwbe_category'] = array('#markup' => '<div><strong>M/WBE Category:</strong> ' . MappingUtil::getCurrenEthnicityName(explode('~', $this->form_state->getValue('mwbe_category'))) . '</div>');
     $this->user_criteria['M/WBE Category'] = $this->form_state->getValue('mwbe_category');
     $this->formatted_search_criteria['M/WBE Category'] = MappingUtil::getCurrenEthnicityName(explode('~', $this->form_state->getValue('mwbe_category')));
    }

    if ($this->form_state->getValue('contract_type') && $this->form_state->getValue('contract_type') != 'No Contract Type Selected') {
      $this->_process_user_criteria_by_datasource_single_field('contract_type', 'contract_type', 'Contract Type');
    }
    if ($this->form_state->getValue('contractno')) {
     $this->form['filter']['contractno'] = array(
        '#markup' => '<div><strong>Contract ID:</strong> ' . $this->form_state->getValue('contractno') . '</div>',
      );
      $this->user_criteria['Contract ID'] = $this->form_state->getValue('contractno');
      $this->formatted_search_criteria[''] = null;
    }

    $this->_process_user_criteria_by_datasource_single_field_and_check('pin', 'pin', 'PIN');

    $this->_process_user_criteria_by_datasource_ranged_amount_field('currentamtfrom', 'currentamtto', 'current_amount', 'Current Amount');

    $this->_process_user_criteria_by_datasource_ranged_date_field('enddatefrom', 'enddateto', 'end_date', 'End Date');

    $this->_process_user_criteria_by_datasource_ranged_date_field('regdatefrom', 'regdateto', 'regdate', 'Registration Date');

    $this->_process_user_criteria_by_datasource_single_field_and_check('category', 'category', 'Category');

    if ($this->form_state->getValue('category') != 'revenue' && ($this->form_state->getValue('conditional_category') && ($this->form_state->getValue('year') == '0' || (substr($this->form_state->getValue('year'), -4) >= 2020)))) {
      $this->_process_user_criteria_by_datasource_single_field('conditional_category', 'conditional_category', 'Conditional Category');
    }

    $this->_process_user_criteria_by_datasource_sub_vendors();

    $this->_process_user_criteria_by_datasource_single_field_and_check('purpose', 'purpose', 'Purpose');

    $this->_process_user_criteria_by_datasource_single_field_and_check('agency', 'agency', 'Agency');

    if ($this->form_state->getValue('industry')) {
      preg_match("/.*?(\\[.*?\\])/is", $this->form_state->getValue('industry'), $matches);
      $industry_type_name = str_replace($matches[1], "", $matches[0]);
      $industry = trim($matches[1], '[ ]');
     $this->form['filter']['industry'] = array('#markup' => '<div><strong>Industry:</strong> ' . $this->form_state->getValue('industry') . '</div>');
      $this->user_criteria['Industry'] = $industry;
      $this->formatted_search_criteria['Industry'] = $industry_type_name;
    }

    $this->_process_user_criteria_by_datasource_single_field_and_check('apt_pin', 'apt_pin', 'APT PIN');

    if ($this->form_state->getValue('award_method') && $this->form_state->getValue('award_method') != 'No Award Method Selected') {
      $this->_process_user_criteria_by_datasource_single_field('award_method', 'award_method', 'Award Method');
    }

    $this->_process_user_criteria_by_datasource_ranged_date_field('startdatefrom', 'startdateto', 'start_date', 'Start Date');

    $this->_process_user_criteria_by_datasource_ranged_date_field('recdatefrom', 'recdateto', 'received_date', 'Received Date');

    if ($this->form_state->hasValue('year') && $this->form_state->getValue('df_contract_status') != 'pending') {
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

  protected function _process_user_criteria_by_datasource_sub_vendors() {
    if ($this->form_state->getValue('contract_includes_sub_vendors_id') != '' && $this->form_state->getValue('contract_includes_sub_vendors_id') != 0 && !empty($this->form_state->getValue('contract_includes_sub_vendors_id'))) {
      $scntrc_status_name = MappingUtil::getscntrc_status_name($this->form_state->getValue('contract_includes_sub_vendors_id'));
      $this->form['filter']['contract_includes_sub_vendors_id'] = array('#markup' => '<div><strong>Contract Includes Sub Vendors:</strong> ' . $scntrc_status_name . '</div>');
      $this->user_criteria['Contract Includes Sub Vendors'] = $this->form_state->getValue('contract_includes_sub_vendors_id');
      $this->formatted_search_criteria['Contract Includes Sub Vendors'] = $scntrc_status_name;
    }

    if ($this->form_state->getValue('sub_contract_status_id') && ($this->form_state->getValue('sub_contract_status_id') != 'Select Status' && $this->form_state->getValue('sub_contract_status_id') != 0 && !empty($this->form_state->getValue('sub_contract_status_id')))) {
      $aprv_sta_name = MappingUtil::getaprv_sta_name($this->form_state->getValue('sub_contract_status_id'));
      $this->form['filter']['sub_contract_status_id'] = array('#markup' => '<div><strong><nobr>Subcontract Status&nbsp;:</nobr></strong> ' . $aprv_sta_name . '</div>');
      $this->user_criteria['Subcontract Status'] = $this->form_state->getValue('sub_contract_status_id');
      $this->formatted_search_criteria['Subcontract Status'] = $aprv_sta_name;
    }
  }

  protected function _validate_by_datasource(&$form, &$form_state)
  {
    // Check Columns
    $responseColumns = $form_state->getValue('column_select');
    if (empty(array_filter($responseColumns))) {
      $form_state->setErrorByName('column_select', t($this->select_at_least_one_column_message));
    }

    $multi_select_hidden = !empty($form_state->getValue('column_select')) ? '|' . implode('||', $form_state->getValue('column_select')) . '|' : '';

    if (!$multi_select_hidden) {
      $form_state->setErrorByName('column_select', t($this->select_at_least_one_column_message));
    }
  }

}
