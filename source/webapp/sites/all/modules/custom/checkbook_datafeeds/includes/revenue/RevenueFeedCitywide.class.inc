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


class RevenueFeedCitywide extends RevenueFeed{
  protected $data_source = 'citywide';
  protected $type_of_data = 'Revenue';
  protected $filtered_columns_container = 'column_select_expense';

  protected function _process_user_criteria_by_datasource(){
    //Budget Fiscal Year
    if ($this->values['budget_fiscal_year']) {
      $this->form['filter']['budget_fiscal_year'] = array(
        '#markup' => '<div><strong>Budget Fiscal Year:</strong> ' . $this->values['budget_fiscal_year'] . '</div>',
      );
      $this->user_criteria['Budget Fiscal Year'] = $this->values['budget_fiscal_year'];
      $this->formatted_search_criteria['Budget Fiscal Year'] = $this->values['budget_fiscal_year'];
    }

    //Agency
    if ($this->values['agency']) {
      $this->form['filter']['agency'] = array('#markup' => '<div><strong>Agency:</strong> ' . $this->values['agency'] . '</div>');
      $this->user_criteria['Agency'] = $this->values['agency'];
      $this->formatted_search_criteria['Agency'] = $this->values['agency'];
    }

    //Revenue Category
    if ($this->values['revenue_category']) {
      $this->form['filter']['revenue_category'] = array(
        '#markup' => '<div><strong>Revenue Category:</strong> ' . $this->values['revenue_category'] . '</div>',
      );
      $this->user_criteria['Revenue Category'] = $this->values['revenue_category'];
      $this->formatted_search_criteria['Revenue Category'] = $this->values['revenue_category'];
    }

    //Revenue Source
    if ($this->values['revenue_source']) {
      $this->form['filter']['revenue_source'] = array(
        '#markup' => '<div><strong>Revenue Source:</strong> ' . $this->values['revenue_source'] . '</div>',
      );
      $this->user_criteria['Revenue Source'] = $this->values['revenue_source'];
      $this->formatted_search_criteria['Revenue Source'] = $this->values['revenue_source'];
    }

    //Catastrophic event filter
    if ($this->values['catastrophic_event'] !== 0 && $this->values['budget_fiscal_year'] >= 2020 ) {
      $catastrophic_events = _get_event_name_and_id();
      $catastrophic_event = $catastrophic_events[$this->values['catastrophic_event']]. "[" .$this->values['catastrophic_event']. "]";
      $this->form['filter']['catastrophic_event'] = array('#markup' => '<div><strong>Catastrophic Event:</strong> ' . $catastrophic_event . '</div>');
      $this->user_criteria['Catastrophic Event'] = $this->values['catastrophic_event'];
      $this->formatted_search_criteria['Catastrophic Event'] = $this->values['catastrophic_event'];
    }

    //Adopted
    if (($this->values['adoptedfrom'] || $this->values['adoptedfrom'] === "0") && ($this->values['adoptedto'] || $this->values['adoptedto'] === "0")) {
      $this->form['filter']['adopted'] = array(
        '#markup' => '<div><strong>Adopted:</strong> Greater Than Equal to: $' . $this->values['adoptedfrom'] . ' and Less Than Equal to: $' . $this->values['adoptedto'] . '</div>',
      );
      $this->user_criteria['Adopted Greater Than'] = $this->values['adoptedfrom'];
      $this->user_criteria['Adopted Less Than'] = $this->values['adoptedto'];
      $this->formatted_search_criteria['Adopted'] = 'Greater Than Equal to: $' . $this->values['adoptedfrom'] . ' and Less Than Equal to: $' . $this->values['adoptedto'];
    } elseif (($this->values['adoptedfrom'] || $this->values['adoptedfrom'] === "0") && !$this->values['adoptedto']) {
      $this->form['filter']['adopted'] = array(
        '#markup' => '<div><strong>Adopted:</strong> Greater Than Equal to: $' . $this->values['adoptedfrom'] . '</div>',
      );
      $this->user_criteria['Adopted Greater Than'] = $this->values['adoptedfrom'];
      $this->formatted_search_criteria['Adopted'] = 'Greater Than Equal to: $' . $this->values['adoptedfrom'];
    } elseif (!$this->values['adoptedfrom'] && ($this->values['adoptedto'] || $this->values['adoptedto'] === "0")) {
      $this->form['filter']['adopted'] = array(
        '#markup' => '<div><strong>Adopted:</strong> Less Than Equal to: $' . $this->values['adoptedto'] . '</div>',
      );
      $this->user_criteria['Adopted Less Than'] = $this->values['adoptedto'];
      $this->formatted_search_criteria['Adopted'] = 'Less Than Equal to: $' . $this->values['adoptedto'];
    }

    //Recognized
    if (($this->values['recognizedfrom'] || $this->values['recognizedfrom'] === "0") && ($this->values['recognizedto'] || $this->values['recognizedto'] === "0")) {
      $this->form['filter']['recognized'] = array(
        '#markup' => '<div><strong>Recognized:</strong> Greater Than Equal to: $' . $this->values['recognizedfrom'] . ' and Less Than Equal to: $' . $this->values['recognizedto'] . '</div>',
      );
      $this->user_criteria['Recognized Greater Than'] = $this->values['recognizedfrom'];
      $this->user_criteria['Recognized Less Than'] = $this->values['recognizedto'];
      $this->formatted_search_criteria['Recognized'] = 'Greater Than Equal to: $' . $this->values['recognizedfrom'] . ' and Less Than Equal to: $' . $this->values['recognizedto'];
    } elseif (($this->values['recognizedfrom'] || $this->values['recognizedfrom'] === "0") && !$this->values['recognizedto']) {
      $this->form['filter']['recognized'] = array(
        '#markup' => '<div><strong>Recognized:</strong> Greater Than Equal to: $' . $this->values['recognizedfrom'] . '</div>',
      );
      $this->user_criteria['Recognized Greater Than'] = $this->values['recognizedfrom'];
      $this->formatted_search_criteria['Recognized'] = 'Greater Than Equal to: $' . $this->values['recognizedfrom'];
    } elseif (!$this->values['recognizedfrom'] && ($this->values['recognizedto'] || $this->values['recognizedto'] === "0")) {
      $this->form['filter']['recognized'] = array(
        '#markup' => '<div><strong>Recognized:</strong> Less Than Equal to: $' . $this->values['recognizedto'] . '</div>',
      );
      $this->user_criteria['Recognized Less Than'] = $this->values['recognizedto'];
      $this->formatted_search_criteria['Recognized'] = 'Less Than Equal to: $' . $this->values['recognizedto'];
    }

    //Fiscal Year
    if ($this->values['fiscal_year'] && $this->values['fiscal_year'] != '') {
      $this->form['filter']['fiscal_year'] = array(
        '#markup' => '<div><strong>Fiscal Year:</strong> ' . $this->values['fiscal_year'] . '</div>',
      );
      $this->user_criteria['Fiscal Year'] = $this->values['fiscal_year'];
      $this->formatted_search_criteria['Fiscal Year'] = $this->values['fiscal_year'];
    } else {
      $this->form['filter']['fiscal_year'] = array(
        '#markup' => '<div><strong>Fiscal Year:</strong> All Fiscal Years</div>',
      );
      $this->formatted_search_criteria['Fiscal Year'] = 'All Fiscal Years';
    }

    //Funding Class
    if ($this->values['funding_class']) {
      $this->form['filter']['funding_class'] = array(
        '#markup' => '<div><strong>Funding Class:</strong> ' . $this->values['funding_class'] . '</div>',
      );
      $this->user_criteria['Funding Class'] = $this->values['funding_class'];
      $this->formatted_search_criteria['Funding Class'] = $this->values['funding_class'];
    }

    //Revenue Class
    if ($this->values['revenue_class']) {
      $this->form['filter']['revenue_class'] = array(
        '#markup' => '<div><strong>Revenue Class:</strong> ' . $this->values['revenue_class'] . '</div>',
      );
      $this->user_criteria['Revenue Class'] = $this->values['revenue_class'];
      $this->formatted_search_criteria['Revenue Class'] = $this->values['revenue_class'];
    }

    //Fund Class
    if ($this->values['fund_class']) {
      $this->form['filter']['fund_class'] = array(
        '#markup' => '<div><strong>Fund Class:</strong> ' . $this->values['fund_class'] . '</div>',
      );
      $this->user_criteria['Fund Class'] = $this->values['fund_class'];
      $this->formatted_search_criteria['Fund Class'] = $this->values['fund_class'];
    }

    //Modified
    if (($this->values['modifiedfrom'] || $this->values['modifiedfrom'] === "0") && ($this->values['modifiedto'] || $this->values['modifiedto'] === "0")) {
      $this->form['filter']['modified'] = array(
        '#markup' => '<div><strong>Modified:</strong> Greater Than Equal to: $' . $this->values['modifiedfrom'] . ' and Less Than Equal to: $' . $this->values['modifiedto'] . '</div>',
      );
      $this->user_criteria['Modified Greater Than'] = $this->values['modifiedfrom'];
      $this->user_criteria['Modified Less Than'] = $this->values['modifiedto'];
      $this->formatted_search_criteria['Modified'] = 'Greater Than Equal to: $' . $this->values['modifiedfrom'] . ' and Less Than Equal to: $' . $this->values['modifiedto'];
    } elseif (($this->values['modifiedfrom'] || $this->values['modifiedfrom'] === "0") && !$this->values['modifiedto']) {
      $this->form['filter']['modified'] = array(
        '#markup' => '<div><strong>Modified:</strong> Greater Than Equal to: $' . $this->values['modifiedfrom'] . '</div>',
      );
      $this->user_criteria['Modified Greater Than'] = $this->values['modifiedfrom'];
      $this->formatted_search_criteria['Modified'] = 'Greater Than Equal to: $' . $this->values['modifiedfrom'];
    } elseif (!$this->values['modifiedfrom'] && ($this->values['modifiedto'] || $this->values['modifiedto'] === "0")) {
      $this->form['filter']['modified'] = array(
        '#markup' => '<div><strong>Modified:</strong> Less Than Equal to: $' . $this->values['modifiedto'] . '</div>',
      );
      $this->user_criteria['Modified Less Than'] = $this->values['modifiedto'];
      $this->formatted_search_criteria['Modified'] = 'Less Than Equal to: $' . $this->values['modifiedto'];
    }
  }

  protected function _process_datasource_values(){
    if ($this->values['fund_class'] != 'All Fund Classes') {
      preg_match($this->bracket_value_pattern, $this->values['fund_class'], $fcmatches);
      $this->criteria['value']['fund_class'] = trim($fcmatches[1], '[ ]');
    }
    if ($this->values['agency'] != 'Citywide (All Agencies)') {
      preg_match($this->bracket_value_pattern, $this->values['agency'], $amatches);
      $this->criteria['value']['agency_code'] = trim($amatches[1], '[ ]');
    }
    if ($this->values['budget_fiscal_year'] != 'All Years') {
      $this->criteria['value']['budget_fiscal_year'] = $this->values['budget_fiscal_year'];
    }
    if ($this->values['fiscal_year'] != 'All Fiscal Years' && $this->values['fiscal_year'] != '') {
      $this->criteria['value']['fiscal_year'] = $this->values['fiscal_year'];
    }
    if ($this->values['revenue_category'] != 'All Revenue Categories') {
      preg_match($this->bracket_value_pattern, $this->values['revenue_category'], $rcmatches);
      if ($rcmatches) {
        $this->criteria['value']['revenue_category'] = trim($rcmatches[1], '[ ]');
      }
    }
    if ($this->values['revenue_class']) {
      preg_match($this->bracket_value_pattern, $this->values['revenue_class'], $rclmatches);
      if ($rclmatches) {
        $this->criteria['value']['revenue_class'] = trim($rclmatches[1], '[ ]');
      } else {
        $this->criteria['value']['revenue_class_name'] = $this->values['revenue_class'];
      }
    }
    if ($this->values['revenue_source']) {
       $this->criteria['value']['revenue_source'] = $this->values['revenue_source'];
    }

    if ($this->values['catastrophic_event'] !== 0 && $this->values['budget_fiscal_year'] >= 2020 ) {
      $this->criteria['value']['catastrophic_event'] = $this->values['catastrophic_event'];
    }

    if ($this->values['funding_class'] != 'All Funding Classes') {
      preg_match($this->bracket_value_pattern, $this->values['funding_class'], $fsmatches);
      $this->criteria['value']['funding_class'] = trim($fsmatches[1], '[ ]');
    }

    if ($this->values['modifiedfrom'] !== '' || $this->values['modifiedto'] !== '') {
      $this->criteria['range']['modified'] = array(
        checknull($this->values['modifiedfrom']),
        checknull($this->values['modifiedto'])
      );
    }
    if ($this->values['adoptedfrom'] !== '' || $this->values['adoptedto'] !== '') {
      $this->criteria['range']['adopted'] = array(
        checknull($this->values['adoptedfrom']),
        checknull($this->values['adoptedto'])
      );
    }
    if ($this->values['recognizedfrom'] !== '' || $this->values['recognizedto'] !== '') {
      $this->criteria['range']['recognized'] = array(
        checknull($this->values['recognizedfrom']),
        checknull($this->values['recognizedto'])
      );
    }
  }

  protected function _validate_by_datasource(&$form, &$form_state){
    $adoptedfrom = $form_state['values']['adoptedfrom'];
    $adoptedto = $form_state['values']['adoptedto'];
    if ($adoptedfrom && !is_numeric($adoptedfrom)) {
      form_set_error('adoptedfrom', t('Adopted From value must be a number.'));
    }
    if ($adoptedto && !is_numeric($adoptedto)) {
      form_set_error('adoptedto', t('Adopted To value must be a number.'));
    }
    if (is_numeric($adoptedfrom) && is_numeric($adoptedto) && $adoptedto < $adoptedfrom) {
      form_set_error('adoptedto', t('Invalid range for Adopted.'));
    }

    $modifiedfrom = $form_state['values']['modifiedfrom'];
    $modifiedto = $form_state['values']['modifiedto'];
    if ($modifiedfrom && !is_numeric($modifiedfrom)) {
      form_set_error('modifiedfrom', t('Modified From value must be a number.'));
    }
    if ($modifiedto && !is_numeric($modifiedto)) {
      form_set_error('modifiedto', t('Modified To value must be a number.'));
    }
    if (is_numeric($modifiedfrom) && is_numeric($modifiedto) && $modifiedto < $modifiedfrom) {
      form_set_error('modifiedto', t('Invalid range for Modified.'));
    }

    $recognizedfrom = $form_state['values']['recognizedfrom'];
    $recognizedto = $form_state['values']['recognizedto'];
    if ($recognizedfrom && !is_numeric($recognizedfrom)) {
      form_set_error('recognizedfrom', t('Recognized From value must be a number.'));
    }
    if ($recognizedto && !is_numeric($recognizedto)) {
      form_set_error('recognizedto', t('Recognized To value must be a number.'));
    }
    if (is_numeric($recognizedfrom) && is_numeric($recognizedto) && $recognizedto < $recognizedfrom) {
      form_set_error('recognizedto', t('Invalid range for Recognized.'));
    }

    // Columns
    $responseColumns = $form_state['values']['column_select'];
    if (!$responseColumns) {
      form_set_error('column_select', t('You must select at least one column.'));
    }
  }
}
