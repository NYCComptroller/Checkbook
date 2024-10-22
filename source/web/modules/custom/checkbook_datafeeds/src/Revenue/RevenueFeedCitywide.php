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

namespace Drupal\checkbook_datafeeds\Revenue;

use Drupal\checkbook_datafeeds\Utilities\FormUtil;

class RevenueFeedCitywide extends RevenueFeed{
  protected $data_source = 'citywide';
  protected string $type_of_data = 'Revenue';
  protected $filtered_columns_container = 'column_select_expense';

  protected function _process_user_criteria_by_datasource(){
    //Budget Fiscal Year
    $this->_process_user_criteria_by_datasource_single_field_and_check('budget_fiscal_year', 'budget_fiscal_year', 'Budget Fiscal Year');

    //Agency
    $this->_process_user_criteria_by_datasource_single_field_and_check('agency', 'agency', 'Agency');

    //Revenue Category
    $this->_process_user_criteria_by_datasource_single_field_and_check('revenue_category', 'revenue_category', 'Revenue Category');

    //Revenue Source
    $this->_process_user_criteria_by_datasource_single_field_and_check('revenue_source', 'revenue_source', 'Revenue Source');

    // Conditional Category filter.
    if ($this->form_state->getValue('conditional_category') && $this->form_state->getValue('budget_fiscal_year') >= 2020 ) {
      $conditional_categories = FormUtil::getEventNameAndId();
      $conditional_category = $conditional_categories[$this->form_state->getValue('conditional_category')]. "[" .$this->form_state->getValue('conditional_category'). "]";
      $this->form['filter']['conditional_category'] = array('#markup' => '<div><strong>Conditional Category:</strong> ' . $conditional_category . '</div>');
      $this->user_criteria['Conditional Category'] = $this->form_state->getValue('conditional_category');
      $this->formatted_search_criteria['Conditional Category'] = $this->form_state->getValue('conditional_category');
    }

    //Adopted
    $this->_process_user_criteria_by_datasource_ranged_amount_field('adoptedfrom', 'adoptedto', 'adopted', 'Adopted');

    //Recognized
    $this->_process_user_criteria_by_datasource_ranged_amount_field('recognizedfrom', 'recognizedto', 'recognized', 'Recognized');

    //Fiscal Year
    if ($this->form_state->getValue('fiscal_year') && $this->form_state->getValue('fiscal_year') != '') {
      $this->_process_user_criteria_by_datasource_single_field('fiscal_year', 'fiscal_year', 'Fiscal Year');

    } else {
      $this->form['filter']['fiscal_year'] = array(
        '#markup' => '<div><strong>Fiscal Year:</strong> All Fiscal Years</div>',
      );
      $this->formatted_search_criteria['Fiscal Year'] = 'All Fiscal Years';
    }

    //Funding Class
    $this->_process_user_criteria_by_datasource_single_field_and_check('funding_class', 'funding_class', 'Funding Class');

    //Revenue Class
    $this->_process_user_criteria_by_datasource_single_field_and_check('revenue_class', 'revenue_class', 'Revenue Class');

    //Fund Class
    $this->_process_user_criteria_by_datasource_single_field_and_check('fund_class', 'fund_class', 'Fund Class');

    //Modified
    $this->_process_user_criteria_by_datasource_ranged_amount_field('modifiedfrom', 'modifiedto', 'modified', 'Modified');
  }

  protected function _process_datasource_values(){
    if ($this->form_state->getValue('fund_class') != 'All Fund Classes') {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('fund_class'), $fcmatches);
      $this->criteria['value']['fund_class'] = trim($fcmatches[1], '[ ]');
    }
    if ($this->form_state->getValue('agency') != 'Citywide (All Agencies)') {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('agency'), $amatches);
      $this->criteria['value']['agency_code'] = trim($amatches[1], '[ ]');
    }
    if ($this->form_state->getValue('budget_fiscal_year') != 'All Years') {
      $this->criteria['value']['budget_fiscal_year'] = $this->form_state->getValue('budget_fiscal_year');
    }

    if (!in_array($this->form_state->getValue('fiscal_year'), ['All Fiscal Years', ''])) {
      $this->criteria['value']['fiscal_year'] = $this->form_state->getValue('fiscal_year');
    }
    if ($this->form_state->getValue('revenue_category') != 'All Revenue Categories') {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('revenue_category'), $rcmatches);
      if ($rcmatches) {
        $this->criteria['value']['revenue_category'] = trim($rcmatches[1], '[ ]');
      }
    }
    if ($this->form_state->getValue('revenue_class')) {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('revenue_class'), $rclmatches);
      if ($rclmatches) {
        $this->criteria['value']['revenue_class'] = trim($rclmatches[1], '[ ]');
      } else {
        $this->criteria['value']['revenue_class_name'] = $this->form_state->getValue('revenue_class');
      }
    }
    if ($this->form_state->getValue('revenue_source')) {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('revenue_source'), $rsmatches);
      if ($rsmatches) {
        $this->criteria['value']['revenue_source'] = trim($rsmatches[1], '[ ]');
      }else{
        $this->criteria['value']['revenue_source_name'] = $this->form_state->getValue('revenue_source');
      }
    }

    if ($this->form_state->getValue('conditional_category') && $this->form_state->getValue('budget_fiscal_year') >= 2020 ) {
      $this->criteria['value']['conditional_category'] = $this->form_state->getValue('conditional_category');
    }

    if ($this->form_state->getValue('funding_class') != 'All Funding Classes') {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('funding_class'), $fsmatches);
      $this->criteria['value']['funding_class'] = trim($fsmatches[1], '[ ]');
    }

    $this->_process_ranged_datasource_values('modifiedfrom', 'modifiedto', 'modified');

    $this->_process_ranged_datasource_values('adoptedfrom', 'adoptedto', 'adopted');

    $this->_process_ranged_datasource_values('recognizedfrom', 'recognizedto', 'recognized');
  }

  protected function _validate_by_datasource(&$form, &$form_state){
    checkbook_datafeeds_check_ranged_amounts($form_state, 'adoptedfrom', 'adoptedto', 'Adopted', 'Adopted From', 'Adopted To');

    checkbook_datafeeds_check_ranged_amounts($form_state, 'modifiedfrom', 'modifiedto', 'Modified', 'Modified From', 'Modified To');

    checkbook_datafeeds_check_ranged_amounts($form_state, 'recognizedfrom', 'recognizedto', 'Recognized', 'Recognized From', 'Recognized To');

    // Columns
    $responseColumns = $form_state->getValue('column_select');
    if (empty(array_filter($responseColumns))) {
      $form_state->setErrorByName('column_select', t('You must select at least one column.'));
    }
  }
}
