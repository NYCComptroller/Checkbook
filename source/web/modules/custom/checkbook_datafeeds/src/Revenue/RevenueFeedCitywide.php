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
    if ($this->form_state->getValue('budget_fiscal_year')) {
      $this->form['filter']['budget_fiscal_year'] = array(
        '#markup' => '<div><strong>Budget Fiscal Year:</strong> ' . $this->form_state->getValue('budget_fiscal_year') . '</div>',
      );
      $this->user_criteria['Budget Fiscal Year'] = $this->form_state->getValue('budget_fiscal_year');
      $this->formatted_search_criteria['Budget Fiscal Year'] = $this->form_state->getValue('budget_fiscal_year');
    }

    //Agency
    if ($this->form_state->getValue('agency')) {
      $this->form['filter']['agency'] = array('#markup' => '<div><strong>Agency:</strong> ' . $this->form_state->getValue('agency') . '</div>');
      $this->user_criteria['Agency'] = $this->form_state->getValue('agency');
      $this->formatted_search_criteria['Agency'] = $this->form_state->getValue('agency');
    }

    //Revenue Category
    if ($this->form_state->getValue('revenue_category')) {
      $this->form['filter']['revenue_category'] = array(
        '#markup' => '<div><strong>Revenue Category:</strong> ' . $this->form_state->getValue('revenue_category') . '</div>',
      );
      $this->user_criteria['Revenue Category'] = $this->form_state->getValue('revenue_category');
      $this->formatted_search_criteria['Revenue Category'] = $this->form_state->getValue('revenue_category');
    }

    //Revenue Source
    if ($this->form_state->getValue('revenue_source')) {
      $this->form['filter']['revenue_source'] = array(
        '#markup' => '<div><strong>Revenue Source:</strong> ' . $this->form_state->getValue('revenue_source') . '</div>',
      );
      $this->user_criteria['Revenue Source'] = $this->form_state->getValue('revenue_source');
      $this->formatted_search_criteria['Revenue Source'] = $this->form_state->getValue('revenue_source');
    }

    //Catastrophic event filter
    if ($this->form_state->getValue('catastrophic_event') && $this->form_state->getValue('budget_fiscal_year') >= 2020 ) {
      $catastrophic_events = FormUtil::getEventNameAndId();
      $catastrophic_event = $catastrophic_events[$this->form_state->getValue('catastrophic_event')]. "[" .$this->form_state->getValue('catastrophic_event'). "]";
      $this->form['filter']['catastrophic_event'] = array('#markup' => '<div><strong>Catastrophic Event:</strong> ' . $catastrophic_event . '</div>');
      $this->user_criteria['Catastrophic Event'] = $this->form_state->getValue('catastrophic_event');
      $this->formatted_search_criteria['Catastrophic Event'] = $this->form_state->getValue('catastrophic_event');
    }

    //Adopted
    if (($this->form_state->getValue('adoptedfrom') || $this->form_state->getValue('adoptedfrom') === "0") && ($this->form_state->getValue('adoptedto') || $this->form_state->getValue('adoptedto') === "0")) {
      $this->form['filter']['adopted'] = array(
        '#markup' => '<div><strong>Adopted:</strong> Greater Than Equal to: $' . $this->form_state->getValue('adoptedfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('adoptedto') . '</div>',
      );
      $this->user_criteria['Adopted Greater Than'] = $this->form_state->getValue('adoptedfrom');
      $this->user_criteria['Adopted Less Than'] = $this->form_state->getValue('adoptedto');
      $this->formatted_search_criteria['Adopted'] = 'Greater Than Equal to: $' . $this->form_state->getValue('adoptedfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('adoptedto');
    } elseif (($this->form_state->getValue('adoptedfrom') || $this->form_state->getValue('adoptedfrom') === "0") && !$this->form_state->getValue('adoptedto')) {
      $this->form['filter']['adopted'] = array(
        '#markup' => '<div><strong>Adopted:</strong> Greater Than Equal to: $' . $this->form_state->getValue('adoptedfrom') . '</div>',
      );
      $this->user_criteria['Adopted Greater Than'] = $this->form_state->getValue('adoptedfrom');
      $this->formatted_search_criteria['Adopted'] = 'Greater Than Equal to: $' . $this->form_state->getValue('adoptedfrom');
    } elseif (!$this->form_state->getValue('adoptedfrom') && ($this->form_state->getValue('adoptedto') || $this->form_state->getValue('adoptedto') === "0")) {
      $this->form['filter']['adopted'] = array(
        '#markup' => '<div><strong>Adopted:</strong> Less Than Equal to: $' . $this->form_state->getValue('adoptedto') . '</div>',
      );
      $this->user_criteria['Adopted Less Than'] = $this->form_state->getValue('adoptedto');
      $this->formatted_search_criteria['Adopted'] = 'Less Than Equal to: $' . $this->form_state->getValue('adoptedto');
    }

    //Recognized
    if (($this->form_state->getValue('recognizedfrom') || $this->form_state->getValue('recognizedfrom') === "0") && ($this->form_state->getValue('recognizedto') || $this->form_state->getValue('recognizedto') === "0")) {
      $this->form['filter']['recognized'] = array(
        '#markup' => '<div><strong>Recognized:</strong> Greater Than Equal to: $' . $this->form_state->getValue('recognizedfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('recognizedto') . '</div>',
      );
      $this->user_criteria['Recognized Greater Than'] = $this->form_state->getValue('recognizedfrom');
      $this->user_criteria['Recognized Less Than'] = $this->form_state->getValue('recognizedto');
      $this->formatted_search_criteria['Recognized'] = 'Greater Than Equal to: $' . $this->form_state->getValue('recognizedfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('recognizedto');
    } elseif (($this->form_state->getValue('recognizedfrom') || $this->form_state->getValue('recognizedfrom') === "0") && !$this->form_state->getValue('recognizedto')) {
      $this->form['filter']['recognized'] = array(
        '#markup' => '<div><strong>Recognized:</strong> Greater Than Equal to: $' . $this->form_state->getValue('recognizedfrom') . '</div>',
      );
      $this->user_criteria['Recognized Greater Than'] = $this->form_state->getValue('recognizedfrom');
      $this->formatted_search_criteria['Recognized'] = 'Greater Than Equal to: $' . $this->form_state->getValue('recognizedfrom');
    } elseif (!$this->form_state->getValue('recognizedfrom') && ($this->form_state->getValue('recognizedto') || $this->form_state->getValue('recognizedto') === "0")) {
      $this->form['filter']['recognized'] = array(
        '#markup' => '<div><strong>Recognized:</strong> Less Than Equal to: $' . $this->form_state->getValue('recognizedto') . '</div>',
      );
      $this->user_criteria['Recognized Less Than'] = $this->form_state->getValue('recognizedto');
      $this->formatted_search_criteria['Recognized'] = 'Less Than Equal to: $' . $this->form_state->getValue('recognizedto');
    }

    //Fiscal Year
    if ($this->form_state->getValue('fiscal_year') && $this->form_state->getValue('fiscal_year') != '') {
      $this->form['filter']['fiscal_year'] = array(
        '#markup' => '<div><strong>Fiscal Year:</strong> ' . $this->form_state->getValue('fiscal_year') . '</div>',
      );
      $this->user_criteria['Fiscal Year'] = $this->form_state->getValue('fiscal_year');
      $this->formatted_search_criteria['Fiscal Year'] = $this->form_state->getValue('fiscal_year');
    } else {
      $this->form['filter']['fiscal_year'] = array(
        '#markup' => '<div><strong>Fiscal Year:</strong> All Fiscal Years</div>',
      );
      $this->formatted_search_criteria['Fiscal Year'] = 'All Fiscal Years';
    }

    //Funding Class
    if ($this->form_state->getValue('funding_class')) {
      $this->form['filter']['funding_class'] = array(
        '#markup' => '<div><strong>Funding Class:</strong> ' . $this->form_state->getValue('funding_class') . '</div>',
      );
      $this->user_criteria['Funding Class'] = $this->form_state->getValue('funding_class');
      $this->formatted_search_criteria['Funding Class'] = $this->form_state->getValue('funding_class');
    }

    //Revenue Class
    if ($this->form_state->getValue('revenue_class')) {
      $this->form['filter']['revenue_class'] = array(
        '#markup' => '<div><strong>Revenue Class:</strong> ' . $this->form_state->getValue('revenue_class') . '</div>',
      );
      $this->user_criteria['Revenue Class'] = $this->form_state->getValue('revenue_class');
      $this->formatted_search_criteria['Revenue Class'] = $this->form_state->getValue('revenue_class');
    }

    //Fund Class
    if ($this->form_state->getValue('fund_class')) {
      $this->form['filter']['fund_class'] = array(
        '#markup' => '<div><strong>Fund Class:</strong> ' . $this->form_state->getValue('fund_class') . '</div>',
      );
      $this->user_criteria['Fund Class'] = $this->form_state->getValue('fund_class');
      $this->formatted_search_criteria['Fund Class'] = $this->form_state->getValue('fund_class');
    }

    //Modified
    if (($this->form_state->getValue('modifiedfrom') || $this->form_state->getValue('modifiedfrom') === "0") && ($this->form_state->hasValue('modifiedto')  || $this->form_state->getValue('modifiedto') === "0")) {
      $this->form['filter']['modified'] = array(
        '#markup' => '<div><strong>Modified:</strong> Greater Than Equal to: $' . $this->form_state->getValue('modifiedfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('modifiedto') . '</div>',
      );
      $this->user_criteria['Modified Greater Than'] = $this->form_state->getValue('modifiedfrom');
      $this->user_criteria['Modified Less Than'] = $this->form_state->getValue('modifiedto');
      $this->formatted_search_criteria['Modified'] = 'Greater Than Equal to: $' . $this->form_state->getValue('modifiedfrom') . ' and Less Than Equal to: $' . $this->form_state->getValue('modifiedto');
    } elseif (($this->form_state->getValue('modifiedfrom') || $this->form_state->getValue('modifiedfrom') === "0") && !$this->form_state->getValue('modifiedto')) {
      $this->form['filter']['modified'] = array(
        '#markup' => '<div><strong>Modified:</strong> Greater Than Equal to: $' . $this->form_state->getValue('modifiedfrom') . '</div>',
      );
      $this->user_criteria['Modified Greater Than'] = $this->form_state->getValue('modifiedfrom');
      $this->formatted_search_criteria['Modified'] = 'Greater Than Equal to: $' . $this->form_state->getValue('modifiedfrom');
    } elseif (!$this->form_state->getValue('modifiedfrom') && ($this->form_state->getValue('modifiedto') || $this->form_state->getValue('modifiedto') === "0")) {
      $this->form['filter']['modified'] = array(
        '#markup' => '<div><strong>Modified:</strong> Less Than Equal to: $' . $this->form_state->getValue('modifiedto') . '</div>',
      );
      $this->user_criteria['Modified Less Than'] = $this->form_state->getValue('modifiedto');
      $this->formatted_search_criteria['Modified'] = 'Less Than Equal to: $' . $this->form_state->getValue('modifiedto');
    }
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
    if ($this->form_state->getValue('fiscal_year') != 'All Fiscal Years' && $this->form_state->getValue('fiscal_year') != '') {
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
       $this->criteria['value']['revenue_source'] = trim($rsmatches[1], '[ ]');;
    }

    if ($this->form_state->getValue('catastrophic_event') && $this->form_state->getValue('budget_fiscal_year') >= 2020 ) {
      $this->criteria['value']['catastrophic_event'] = $this->form_state->getValue('catastrophic_event');
    }

    if ($this->form_state->getValue('funding_class') != 'All Funding Classes') {
      preg_match($this->bracket_value_pattern, $this->form_state->getValue('funding_class'), $fsmatches);
      $this->criteria['value']['funding_class'] = trim($fsmatches[1], '[ ]');
    }

    if ($this->form_state->getValue('modifiedfrom') !== '' || $this->form_state->getValue('modifiedto') !== '') {
      $this->criteria['range']['modified'] = array(
        checknull($this->form_state->getValue('modifiedfrom')),
        checknull($this->form_state->getValue('modifiedto'))
      );
    }
    if ($this->form_state->getValue('adoptedfrom') !== '' || $this->form_state->getValue('adoptedto') !== '') {
      $this->criteria['range']['adopted'] = array(
        checknull($this->form_state->getValue('adoptedfrom')),
        checknull($this->form_state->getValue('adoptedto'))
      );
    }
    if ($this->form_state->getValue('recognizedfrom') !== '' || $this->form_state->getValue('recognizedto') !== '') {
      $this->criteria['range']['recognized'] = array(
        checknull($this->form_state->getValue('recognizedfrom')),
        checknull($this->form_state->getValue('recognizedto'))
      );
    }
  }

  protected function _validate_by_datasource(&$form, &$form_state){
    $adoptedfrom = $form_state->getValue('adoptedfrom');
    $adoptedto = $form_state->getValue('adoptedto');
    if ($adoptedfrom && !is_numeric($adoptedfrom)) {
//    form_set_error('adoptedfrom', t('Adopted From value must be a number.'));
      $form_state->setErrorByName('adoptedfrom', t('Adopted From value must be a number.'));
    }
    if ($adoptedto && !is_numeric($adoptedto)) {
//    form_set_error('adoptedto', t('Adopted To value must be a number.'));
      $form_state->setErrorByName('adoptedto', t('Adopted To value must be a number.'));
    }
    if (is_numeric($adoptedfrom) && is_numeric($adoptedto) && $adoptedto < $adoptedfrom) {
//    form_set_error('adoptedto', t('Invalid range for Adopted.'));
      $form_state->setErrorByName('adoptedto', t('Invalid range for Adopted.'));
    }

//    $modifiedfrom = $form_state['values']['modifiedfrom'];
      $modifiedfrom = $form_state->getValue('modifiedfrom');
//    $modifiedto = $form_state['values']['modifiedto'];
      $modifiedto = $form_state->getValue('modifiedto');
    if ($modifiedfrom && !is_numeric($modifiedfrom)) {
//    form_set_error('modifiedfrom', t('Modified From value must be a number.'));
      $form_state->setErrorByName('modifiedfrom', t('Modified From value must be a number.'));
    }
    if ($modifiedto && !is_numeric($modifiedto)) {
//    form_set_error('modifiedto', t('Modified To value must be a number.'));
      $form_state->setErrorByName('modifiedto', t('Modified To value must be a number.'));
    }
    if (is_numeric($modifiedfrom) && is_numeric($modifiedto) && $modifiedto < $modifiedfrom) {
//    form_set_error('modifiedto', t('Invalid range for Modified.'));
      $form_state->setErrorByName('modifiedto', t('Invalid range for Modified.'));
    }

//  $recognizedfrom = $form_state['values']['recognizedfrom'];
    $recognizedfrom = $form_state->getValue('recognizedfrom');
//  $recognizedto = $form_state['values']['recognizedto'];
    $recognizedto = $form_state->getValue('recognizedto');
    if ($recognizedfrom && !is_numeric($recognizedfrom)) {
//      form_set_error('recognizedfrom', t('Recognized From value must be a number.'));
      $form_state->setErrorByName('recognizedfrom', t('Recognized From value must be a number.'));
    }
    if ($recognizedto && !is_numeric($recognizedto)) {
//      form_set_error('recognizedto', t('Recognized To value must be a number.'));
      $form_state->setErrorByName('recognizedto', t('Recognized To value must be a number.'));
    }
    if (is_numeric($recognizedfrom) && is_numeric($recognizedto) && $recognizedto < $recognizedfrom) {
//      form_set_error('recognizedto', t('Invalid range for Recognized.'));
      $form_state->setErrorByName('recognizedto', t('Invalid range for Recognized.'));
    }

    // Columns
//  $responseColumns = $form_state['values']['column_select'];
    $responseColumns = $form_state->getValue('column_select');
//  if (!$responseColumns) {
    if (empty(array_filter($responseColumns))) {
//      form_set_error('column_select', t('You must select at least one column.'));
      $form_state->setErrorByName('column_select', t('You must select at least one column.'));
    }
  }
}
