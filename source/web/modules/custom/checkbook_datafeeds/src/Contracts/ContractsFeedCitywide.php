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

    if ($this->form_state->getValue('df_contract_status')) {
     $this->form['filter']['df_contract_status'] = array('#markup' => '<div><strong>Status:</strong> ' .$this->form_state->getValue('df_contract_status') . '</div>');
      $this->user_criteria['Contract Status'] = $this->form_state->getValue('df_contract_status');
      $this->formatted_search_criteria['Status'] = $this->form_state->getValue('df_contract_status');
    }
    if ($this->form_state->getValue('vendor')) {
     $this->form['filter']['vendor'] = array(
        '#markup' => '<div><strong>Vendor:</strong> ' . $this->form_state->getValue('vendor') . '</div>',
      );
      $this->user_criteria['Vendor'] = $this->form_state->getValue('vendor');
      $this->formatted_search_criteria['Vendor'] = $this->form_state->getValue('vendor');
    }
    if ($this->form_state->getValue('mwbe_category')) {
     $this->form['filter']['mwbe_category'] = array('#markup' => '<div><strong>M/WBE Category:</strong> ' . MappingUtil::getCurrenEthnicityName(explode('~', $this->form_state->getValue('mwbe_category'))) . '</div>');
     $this->user_criteria['M/WBE Category'] = $this->form_state->getValue('mwbe_category');
     $this->formatted_search_criteria['M/WBE Category'] = MappingUtil::getCurrenEthnicityName(explode('~', $this->form_state->getValue('mwbe_category')));
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
    if ($this->form_state->getValue('contract_includes_sub_vendors_id') != '' && $this->form_state->getValue('contract_includes_sub_vendors_id') != 0 && !empty($this->form_state->getValue('contract_includes_sub_vendors_id'))) {
      $scntrc_status_name = MappingUtil::getscntrc_status_name($this->form_state->getValue('contract_includes_sub_vendors_id'));
     $this->form['filter']['contract_includes_sub_vendors_id'] = array('#markup' => '<div><strong>Contract Includes Sub Vendors:</strong> ' . $scntrc_status_name . '</div>');
      $this->user_criteria['Contract Includes Sub Vendors'] = $this->form_state->getValue('contract_includes_sub_vendors_id');
      $this->formatted_search_criteria['Contract Includes Sub Vendors'] = $scntrc_status_name;
    }
    if ($this->form_state->getValue('pin')) {
     $this->form['filter']['pin'] = array(
        '#markup' => '<div><strong>PIN:</strong> ' . $this->form_state->getValue('pin') . '</div>',
      );
      $this->user_criteria['PIN'] = $this->form_state->getValue('pin');
      $this->formatted_search_criteria['PIN'] = $this->form_state->getValue('pin');
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
    if ($this->form_state->getValue('regdatefrom') && $this->form_state->getValue('regdateto')) {
     $this->form['filter']['regdate'] = array('#markup' => '<div><strong>Registration Date:</strong> Greater Than Equal to: ' . $this->form_state->getValue('regdatefrom') . ' Less Than Equal to: ' . $this->form_state->getValue('regdateto') . '</div>');
      $this->user_criteria['Registered Date Greater Than'] = $this->form_state->getValue('regdatefrom');
      $this->user_criteria['Registered Date Less Than'] = $this->form_state->getValue('regdateto');
      $this->formatted_search_criteria['Registration Date'] = 'Greater Than Equal to: ' . $this->form_state->getValue('regdatefrom') . ' Less Than Equal to: ' . $this->form_state->getValue('regdateto');
    } elseif (!$this->form_state->getValue('regdatefrom') && $this->form_state->getValue('regdateto')) {
     $this->form['filter']['regdate'] = array('#markup' => '<div><strong>Registration Date:</strong> Less Than Equal to: ' . $this->form_state->getValue('regdateto') . '</div>');
      $this->user_criteria['Registered Date Less Than'] = $this->form_state->getValue('regdateto');
      $this->formatted_search_criteria['Registration Date'] = 'Less Than Equal to: ' . $this->form_state->getValue('regdateto');
    } elseif ($this->form_state->getValue('regdatefrom') && !$this->form_state->getValue('regdateto')) {
     $this->form['filter']['regdate'] = array('#markup' => '<div><strong>Registration Date:</strong> Greater Than Equal to: $' . $this->form_state->getValue('regdatefrom') . '</div>');
      $this->user_criteria['Registered Date Greater Than'] = $this->form_state->getValue('regdatefrom');
      $this->formatted_search_criteria['Registration Date'] = 'Greater Than Equal to: $' . $this->form_state->getValue('regdatefrom');
    }
    if ($this->form_state->getValue('category')) {
     $this->form['filter']['category'] = array('#markup' => '<div><strong>Category:</strong> ' . $this->form_state->getValue('category') . '</div>');
      $this->user_criteria['Category'] = $this->form_state->getValue('category');
      $this->formatted_search_criteria['Category'] = $this->form_state->getValue('category');
    }
    if ($this->form_state->getValue('category') != 'revenue') {
      if ($this->form_state->getValue('catastrophic_event') && ($this->form_state->getValue('year') == '0' || (substr($this->form_state->getValue('year'), -4) >= 2020))) {
       $this->form['filter']['catastrophic_event'] = array('#markup' => '<div><strong>Catastrophic Event:</strong> ' . $this->form_state->getValue('catastrophic_event') . '</div>');
        $this->user_criteria['Catastrophic Event'] = $this->form_state->getValue('catastrophic_event');
        $this->formatted_search_criteria['Catastrophic Event'] = $this->form_state->getValue('catastrophic_event');
      }
    }
    if ($this->form_state->getValue('sub_vendor_status_in_pip_id')) {
      if ($this->form_state->getValue('sub_vendor_status_in_pip_id') != 'Select Status' && $this->form_state->getValue('sub_vendor_status_in_pip_id') != 0 && !empty($this->form_state->getValue('sub_vendor_status_in_pip_id'))) {
        $aprv_sta_name = MappingUtil::getaprv_sta_name($this->form_state->getValue('sub_vendor_status_in_pip_id'));
       $this->form['filter']['sub_vendor_status_in_pip_id'] = array('#markup' => '<div><strong><nobr>Sub Vendor Status in PIP&nbsp;:</nobr></strong> ' . $aprv_sta_name . '</div>');
        $this->user_criteria['Sub Vendor Status in PIP'] = $this->form_state->getValue('sub_vendor_status_in_pip_id');
        $this->formatted_search_criteria['Sub Vendor Status in PIP'] = $aprv_sta_name;
      }
    }
    if ($this->form_state->getValue('purpose')) {
     $this->form['filter']['purpose'] = array(
        '#markup' => '<div><strong>Purpose:</strong> ' . $this->form_state->getValue('purpose') . '</div>',
      );
      $this->user_criteria['Purpose'] = $this->form_state->getValue('purpose');
      $this->formatted_search_criteria['Purpose'] = $this->form_state->getValue('purpose');
    }
    if ($this->form_state->getValue('agency')) {
     $this->form['filter']['agency'] = array(
        '#markup' => '<div><strong>Agency:</strong> ' . $this->form_state->getValue('agency') . '</div>',
      );
      $this->user_criteria['Agency'] = $this->form_state->getValue('agency');
      $this->formatted_search_criteria['Agency'] = $this->form_state->getValue('agency');
    }
    if ($this->form_state->getValue('industry')) {
      preg_match("/.*?(\\[.*?\\])/is", $this->form_state->getValue('industry'), $matches);
      $industry_type_name = str_replace($matches[1], "", $matches[0]);
      $industry = trim($matches[1], '[ ]');
     $this->form['filter']['industry'] = array('#markup' => '<div><strong>Industry:</strong> ' . $this->form_state->getValue('industry') . '</div>');
      $this->user_criteria['Industry'] = $industry;
      $this->formatted_search_criteria['Industry'] = $industry_type_name;
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
    if ($this->form_state->getValue('recdatefrom') && $this->form_state->getValue('recdateto')) {
     $this->form['filter']['received_date'] = array('#markup' => '<div><strong>Received Date:</strong> Greater Than Equal to: ' . $this->form_state->getValue('recdatefrom') . ' Less Than Equal to: ' . $this->form_state->getValue('recdateto') . '</div>');
      $this->user_criteria['Received Date Greater Than'] = $this->form_state->getValue('recdatefrom');
      $this->user_criteria['Received Date Less Than'] = $this->form_state->getValue('recdateto');
      $this->formatted_search_criteria['Received Date'] = 'Greater Than Equal to: ' . $this->form_state->getValue('recdatefrom') . ' Less Than Equal to: ' . $this->form_state->getValue('recdateto');
    } elseif (!$this->form_state->getValue('recdatefrom') && $this->form_state->getValue('recdateto')) {
     $this->form['filter']['received_date'] = array('#markup' => '<div><strong>Received Date:</strong> Less Than Equal to: ' . $this->form_state->getValue('recdateto') . '</div>');
      $this->user_criteria['Received Date Less Than'] = $this->form_state->getValue('recdateto');
      $this->formatted_search_criteria['Received Date'] = 'Less Than Equal to: ' . $this->form_state->getValue('recdateto');
    } elseif ($this->form_state->getValue('recdatefrom') && !$this->form_state->getValue('recdateto')) {
     $this->form['filter']['received_date'] = array('#markup' => '<div><strong>Received Date:</strong> Greater Than Equal to: ' . $this->form_state->getValue('recdatefrom') . '</div>');
      $this->user_criteria['Received Date Greater Than'] = $this->form_state->getValue('recdatefrom');
      $this->formatted_search_criteria['Received Date'] = 'Greater Than Equal to: ' . $this->form_state->getValue('recdatefrom');
    }
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

  /*protected function _process_datasource_values()
  {
    if ($this->form_state->getValue('expense_type') && $this->form_state->getValue('expense type') != 'ts') {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('expense_type'), $etmatches);
      $this->criteria['value']['spending_category'] = trim($etmatches[1], '[ ]');
    }

    if ($this->form_state->getValue('expense_type') != 'Payroll [p]' && $this->form_state->getValue('expense_type') != 'Others [o]') {
      if ($this->form_state->getValue('catastrophic_event') && ($this->form_state->getValue('year') == '0' || substr($this->form_state->getValue('year'), -4) >= 2020))
      {
        $this->criteria['value']['catastrophic_event'] = $this->form_state->getValue('catastrophic_event');
      }
    }
    if ($this->form_state->getValue('agency') != 'Citywide (All Agencies)') {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('agency'), $agency_matches);
      $this->criteria['value']['agency_code'] = trim($agency_matches[1], '[ ]');
    }

    if ($this->form_state->getValue('mwbe_category') && $this->form_state->getValue('mwbe_category') != 'Select Category') {
      $this->criteria['value']['mwbe_category'] = $this->form_state->getValue('mwbe_category');
    }
    if ($this->form_state->getValue('industry') && $this->form_state->getValue('industry') != 'Select Industry') {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('industry'), $imatches);
      $this->criteria['value']['industry'] = trim($imatches[1], '[ ]');
    }

    if ($this->form_state->getValue('capital_project')) {
      $this->criteria['value']['capital_project_code'] = $this->form_state->getValue('capital_project');
    }

    if ($this->form_state->getValue('year') && $this->form_state->getValue('year') != '0') {
      $this->criteria['value']['fiscal_year'] = ltrim($this->form_state->getValue('year'), 'FY');
    }
  }*/

  protected function _validate_by_datasource(&$form, &$form_state)
  {
    // Check Columns
    $responseColumns = $form_state->getValue('column_select');
    if (empty(array_filter($responseColumns))) {
      $form_state->setErrorByName('column_select', t('You must select at least one column.'));
    }

//  $multi_select_hidden = isset($form_state['input']['column_select']) ? '|' . implode('||', $form_state['input']['column_select']) . '|' : '';
    $multi_select_hidden = !empty($form_state->getValue('column_select')) ? '|' . implode('||', $form_state->getValue('column_select')) . '|' : '';

//  if ( Datasource::NYCHA == $data_source && (array_key_first($oge_response_columns) == '' || array_key_first($oge_response_columns) == null)){
    if (!$multi_select_hidden) {
//      form_set_error('column_select', t('You must select at least one column.'));
      $form_state->setErrorByName('column_select', t('You must select at least one column.'));
    }
  }

}
