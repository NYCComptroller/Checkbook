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

use Drupal\checkbook_api\config\ConfigUtil;
use Drupal\checkbook_datafeeds\Budget\BudgetFormUtil;
use Drupal\checkbook_datafeeds\Common\FeedFactory;
use Drupal\checkbook_datafeeds\DatafeedsConfigUtil;
use Drupal\checkbook_datafeeds\Utilities\FeedUtil;
use Drupal\checkbook_datafeeds\Utilities\FormUtil;
use Drupal\checkbook_infrastructure_layer\Constants\Common\CheckbookDomain;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\Core\Form\FormStateInterface;

/**
 * First step of Budget section.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 *
 * @return array
 *   Renderable array of first step of Budget
 */
function checkbook_datafeeds_budget($form, FormStateInterface &$form_state)
{
  $format = $form_state->get(['step_information', 'type', 'stored_values', 'format']);
  $form['loaded'] = array(
    '#markup' => '<div class="updated-date">Last updated: ' . FeedUtil::getDataFeedsUpdatedDate(CheckbookDomain::$BUDGET) . '</div>',
  );
  $form['requiredmessage'] = array(
    '#markup' => '<p class="required-message">Fields marked with an asterisk (<span class="form-required"></span>) are required.</p><br/>',
  );
  $form['description'] = array(
    '#markup' => '<h2 class="domain-title">Budget</h2>'
  );

  $data_source = $form_state->hasValue('datafeeds-budget-domain-filter') ? $form_state->getValue('datafeeds-budget-domain-filter') : Datasource::CITYWIDE;
  $form['datafeeds-budget-domain-filter'] = DatafeedsConfigUtil::dataSourceRadio($data_source, CheckbookDomain::$BUDGET);
//kint($data_source);
  //</editor-fold>

  $form['filter'] = array(
    '#type' => 'fieldset',
    '#title' => t('3. Filter the Data:'),
    '#attributes' => array('class' => array('budget-fieldset')),
  );
  //GET COLUMNS
  $coldata = $form_state->getValue('column_select_expense');
  $coldata_id = $form_state->getValue('column_select_expense');
  //THE FOLLOWING WORKS INSERTING SELECTED
  $coldata_id = ['Agency' => 'Agency',  'Adopted'=> 'Adopted', 'Accrued Expense' => 'Accrued Expense'];

  //<editor-fold desc="CityWide Fields">
  $form['filter']['checkbook-fields-begin'] = array(
    '#markup' => '<div class="checkbook_fields">',
  );
  $form = checkbook_datafeeds_budget_filter_data($form, $form_state, Datasource::CITYWIDE);
  $form['filter']['checkbook-fields-end'] = array(
    '#markup' => '</div>',
  );
  //</editor-fold>

  //<editor-fold desc="NYCHA Fields">
  $form['filter']['checkbook-nycha-fields-begin'] = array(
    '#markup' => '<div class="checkbook_nycha_fields">',
  );
  $form = checkbook_datafeeds_nycha_budget_filter_data($form, $form_state, Datasource::NYCHA);
  $form['filter']['checkbook-nycha-fields-end'] = array(
    '#markup' => '</div>',
  );
  //</editor-fold>

  $form['columns'] = array(
    '#type' => 'fieldset',
    '#title' => t('4. Select Columns:'),
  );

  //<editor-fold desc="CityWide and NYCHA Multi-select Options">
  $form['columns']['column_select_expense'] = array(
    '#type' => 'select',
    '#title' => t('Columns<span class="form-required" title="This field is required."></span>'),
    '#options' => _checkbook_datafeeds_budget_column_options(Datasource::CITYWIDE, $format),
    '#multiple' => TRUE,
    '#validated' => TRUE,
//  '#default_value' => !empty($form_state->getValue('column_select_expense')) ?? '',
    //'#default_value' => checkbook_datafeeds_budget_get_column_default($form, $form_state, 'column_select_expense'),
    //'#default_value' => $coldata_id,
    '#default_value' => $form_state->getValue('column_select_expense', ''),
    '#name' => 'column_select_expense',
  );

  $form['columns']['nycha_column_select'] = array(
    '#type' => 'select',
    '#title' => t('Columns<span class="form-required" title="This field is required."></span>'),
    '#options' => _checkbook_datafeeds_budget_column_options(Datasource::NYCHA, $format),
    '#multiple' => TRUE,
    '#validated' => TRUE,
//  '#default_value' => !empty($form_state->getValue('nycha_column_select')) ?? '',
    '#default_value' => checkbook_datafeeds_budget_get_column_default($form, $form_state, 'nycha_column_select'),
  );

  //Hidden Field for multi-select
  if ($data_source == Datasource::NYCHA) {
    $multi_select_hidden = !empty( $form_state->getValue('nycha_column_select') ) ? '|' . implode('||', $form_state->getValue('nycha_column_select') ) . '|' : '';
  } else {
    $multi_select_hidden = !empty( $form_state->getValue('column_select_expense') ) ? '|' . implode('||', $form_state->getValue('column_select_expense') ) . '|' : '';
  }

  //Hidden Field for multi-select
  $form['columns']['hidden_multiple_value'] = array(
    '#value' => $multi_select_hidden,
    '#type' => 'hidden',
  );
  //</editor-fold>
  //$form['#attached']['library'][] = drupal_get_path('module', 'checkbook_datafeeds') . '/js/budget.js';



  $form['prev'] = array(
    '#type' => 'submit',
//  '#value' => t('Previous'),
    '#value' => t('Previous'),
    '#name' => 'prev',
    '#submit' => array('initial_previous_submit'),
    '#limit_validation_errors' => array(),
    '#attributes' => array("onclick" => "
           jQuery('#checkbook-datafeeds-form').addClass('disable_me');
             jQuery('#datafeeds-rotator').show();
        ")
  );
  $form['feeds_budget_next'] = array(
    '#type' => 'submit',
//  '#value' => t('NEXT Submit'),
    '#value' => t('Submit'),
    '#submit' => array('checkbook_datafeeds_budget_next_submit'),
    '#attributes' => array("onclick" => "
           jQuery('#checkbook-datafeeds-form').addClass('disable_me');
             jQuery('#datafeeds-rotator').show();
        ")
  );

//  $form['#attached']['library'][] = 'checkbook_datafeeds/datafeeds.multi-select';
  /* Pass even variable from this function to JS settings */
  // $form['#attached']['drupalSettings']['checkbook_datafeeds']['<route_name>'] ['variable'] = 'PHP Value To JS';ß
  /*
  $form['#attached'] = array(
    'library' => array(
      array('system', 'ui.autocomplete', FALSE),
      array('checkbook_datafeeds', 'jquery_multiselect', FALSE),
    ),
    'js' => array(
      'data' => drupal_get_path('module', 'checkbook_datafeeds') . '/js/budget.js',
    ),
  );
*/
  $form['#attached']['library'][] = 'checkbook_datafeeds/datafeeds.budget';
  $form['#validate'] = array('checkbook_datafeeds_budget_validate');
  $form['#attributes'] = array('class' => array('budget', 'data-feeds-wizard', 'datafeeds-domain-form'));
  return $form;
}

/**Expense Category
 * @param $form
 * @param FormStateInterface $form_state
 * @param string $data_source
 * @return mixed
 */
function checkbook_datafeeds_budget_filter_data($form, FormStateInterface $form_state, string $data_source = Datasource::CITYWIDE)
{
  $agency_options = FormUtil::getAgencies($data_source);
  $form['filter']['leftbegin'] = array(
    '#markup' => '<div class="column column-left">',
  );
  $form['filter']['agency'] = array(
    '#type' => 'select',
    '#title' => t('Agency:'),
    '#options' => $agency_options['options'],
    '#options_attributes' => $agency_options['options_attributes'],
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield agency">',
    '#suffix' => '</div>',
//  '#default_value' => !empty($form_state->getValue('agency')) ?? 'Citywide (All Agencies)',
    '#default_value' => $form_state->getValue('agency', 'Citywide (All Agencies)'),

  );
  $form['filter']['dept'] = array(
    '#type' => 'select',
    '#title' => t('Department:'),
    '#prefix' => '<div id="department" class="datafield department">',
    '#suffix' => '</div>',
    '#validated' => TRUE,
//  '#options' => array( t('Select Department'), t('ADMINISTRATIVE-PS [005] '),  t('ADMINISTRATIVE-PS [005] '),  t('ADOPTION SUBSIDY - OTPS [010] '),  t('ADOPTION SUBSIDY - PS [009] '), ),
//  '#default_value' => 'Select Department',
//  '#default_value' => !empty($form_state->getValue('dept')) ?? 'Select Department',
    '#default_value' => $form_state->getValue('dept', 'Select Department'),
  );

  $form['dept_hidden'] = array(
    '#title' => 'dept_hidden',
    // '#value' => $form_state['values']['dept'],
    '#value' => $form_state->getValue('dept'),
    '#type' => 'hidden',
  );

  $form['filter']['expense_category'] = array(
    '#type' => 'select',
    '#title' => t('Expense Category:'),
    '#prefix' => '<div id="expense-category" class="datafield expensecategory">',
    '#suffix' => '</div>',
//  '#options' => array( t('Expense Select Expense Category: '), t('ALLOWANCE FOR UNIFORMS [064] '),  t('AMOUNT TO BE SCHEDULED-PS [053]  '),  t('ANNUITY CONTRIBUTIONS [081]  '),  t('ASSIGNMENT DIFFERENTIAL [041] '), ),
    '#validated' => TRUE,
    '#default_value' => $form_state->getValue('expense_category', ''),
  );

  $form['expense_category_hidden'] = array(
    '#title' => 'expense_category_hidden',
    '#value' => $form_state->getValue('expense_category'),
    '#type' => 'hidden'
  );

  $form['filter']['budget_code'] = array(
    '#type' => 'textfield',
    '#title' => t('Budget Code:'),
    '#attributes' => array('class' => array('watch')),
    '#default_value' => $form_state->getValue('budget_code', ''),
    '#prefix' => '<div class="datafield budgetcode">',
    '#suffix' => '</div>',
  );
  $form['filter']['conditional_category'] = array(
    '#type' => 'select',
    '#title' => t('Conditional Category:'),
    '#options' => FormUtil::getEventNameAndId('attributes'),
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div id="conditional-category" class="datafield conditional-category">',
    '#suffix' => '</div>',
    '#validated' => TRUE,
    '#default_value' => $form_state->getValue('conditional_category', ''),
  );
  $form['filter']['fiscal_year'] = array(
    '#type' => 'select',
    '#title' => t('Year:'),
    '#options' => CheckbookDateUtil::getFiscalYearsRange(),
    '#default_value' => $form_state->getValue('fiscal_year', ''),
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield fiscalyear">',
    '#suffix' => '</div>',
  );

  $form['filter']['leftend'] = array(
    '#markup' => '</div><div class="column column-right">',
  );

  $form['filter']['currentfrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield currentmodified datarange"><label>Modified Budget:</label><div class="ranges">',
//  '#default_value' => (!empty($form_state->getValue('currentfrom')) || $form_state->getValue('currentfrom') === "0") ?? '',
    '#default_value' => $form_state->getValue('currentfrom', ''),
  );
  $form['filter']['currentto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
//  '#default_value' => (!empty($form_state->getValue('currentto')) || $form_state->getValue('currentto') === "0") ?? '',
    '#default_value' => $form_state->getValue('currentto', ''),
  );

  $form['filter']['adoptedfrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield adoptedbudget datarange"><label>Adopted Budget:</label><div class="ranges">',
    '#default_value' => $form_state->getValue('adoptedfrom', ''),
  );
  $form['filter']['adoptedto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => $form_state->getValue('adoptedto', ''),
  );
  $form['filter']['preencumberedfrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield preencumbered datarange"><label>Pre-encumbered:</label><div class="ranges">',
    '#default_value' => $form_state->getValue('preencumberedfrom', ''),
  );
  $form['filter']['preencumberedto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => $form_state->getValue('preencumberedto', ''),
  );
  $form['filter']['encumberedfrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
//    '#default_value' => (!empty($form_state->getValue('preencumberedto')) || $form_state->getValue('preencumberedto') === "0") ?? '',
    '#default_value' => $form_state->getValue('encumberedfrom', ''),
    '#prefix' => '<div class="datafield encumbered datarange"><label>Encumbered:</label><div class="ranges">',
  );
  $form['filter']['encumberedto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#default_value' => $form_state->getValue('encumberedto', ''),
    '#suffix' => '</div></div>',
  );
  $form['filter']['accruedexpensefrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield accruedexpense datarange"><label>Accrued Expense:</label><div class="ranges">',
    '#default_value' => $form_state->getValue('accruedexpensefrom', ''),
  );
  $form['filter']['accruedexpenseto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => $form_state->getValue('accruedexpenseto', ''),
  );
  $form['filter']['cashfrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#default_value' => $form_state->getValue('cashfrom', ''),
    '#prefix' => '<div class="datafield cashexpense datarange"><label>Cash Expense:</label><div class="ranges">',
  );
  $form['filter']['cashto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#default_value' => $form_state->getValue('cashto', ''),
    '#suffix' => '</div></div>',
  );
  $form['filter']['postadjustmentsfrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield postadjustments datarange"><label>Post Adjustment:</label><div class="ranges">',
    '#default_value' => $form_state->getValue('postadjustmentsfrom', ''),
  );
  $form['filter']['postadjustmentsto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => $form_state->getValue('postadjustmentsto', ''),
  );
  $form['filter']['rightend'] = array(
    '#markup' => '</div>',
  );
  return $form;
}

/**
 * @param $form
 * @param $form_state
 * @param $data_source
 * @return mixed
 */


function checkbook_datafeeds_nycha_budget_filter_data($form, FormStateInterface $form_state, $data_source)
{
  $first_time_load = checkbook_datafeeds_is_first_load($form_state, $data_source);
  $form['filter']['nycha-leftbegin'] = array(
    '#markup' => '<div class="column column-left">',
  );
  $form['filter']['nycha_fiscal_year'] = array(
    '#type' => 'select',
    '#title' => t('Year:'),
    '#options' => CheckbookDateUtil::getFiscalYearsRange($data_source, CheckbookDomain::$BUDGET),
    '#default_value' => $form_state->getValue('nycha_fiscal_year', ''),
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield fiscalyear">',
    '#suffix' => '</div>',
  );

  $expenseCategories = BudgetFormUtil::getBudgetExpCatOptions(CheckbookDomain::$BUDGET, NUll, NUll, NULL, Datasource::NYCHA);
  $form['filter']['nycha_expense_category'] = array(
    '#type' => 'select',
    '#title' => t('Expense Category:'),
    '#options' => $expenseCategories['options'],
    '#options_attributes' => $expenseCategories['option_attributes'],
    '#prefix' => '<div id="expense-category" class="datafield expensecategory">',
    '#suffix' => '</div>',
    '#validated' => TRUE,
    '#default_value' => $form_state->getValue('nycha_expense_category', ''),
  );

  $resp_center_options = FormUtil::getResponsibilityCenters(Datasource::NYCHA, true);
  $form['filter']['nycha_resp_center'] = array(
    '#type' => 'select',
    '#title' => t('Responsibility Center:'),
    '#options' => $resp_center_options['options'],
    '#options_attributes' => $resp_center_options['option_attributes'],
    '#prefix' => '<div class="datafield nycha_resp_center">',
    '#suffix' => '</div>',
    '#validated' => TRUE,
    '#default_value' => $form_state->getValue('nycha_resp_center', ''),
  );
  $funding_source_options = FormUtil::getFundingSourceOptions(Datasource::NYCHA);
  $form['filter']['nycha_funding_source'] = array(
    '#type' => 'select',
    '#title' => t('Funding Source:'),
    '#options' => $funding_source_options['options'],
    '#options_attributes' => $funding_source_options['option_attributes'],
    '#prefix' => '<div class="datafield nycha_funding_source nycha">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#validated' => TRUE,
    '#default_value' => $form_state->getValue('nycha_funding_source', 'Select Funding Source'),
  );

  $program_options = FormUtil::getProgram(Datasource::NYCHA);
  $form['filter']['nycha_program'] = array(
    '#type' => 'select',
    '#title' => t('Program:'),
    '#options' => $program_options['options'],
    '#options_attributes' => $program_options['option_attributes'],
    '#prefix' => '<div class="datafield program nycha">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#validated' => TRUE,
    //'#default_value' => !empty($form_state['values']['nycha_program']) ? $form_state['values']['nycha_program'] : 'Select Program',
    '#default_value' => $form_state->getValue('nycha_program', 'Select Program'),
  );

  $project_options = FormUtil::getProject(Datasource::NYCHA);
  $form['filter']['nycha_project'] = array(
    '#type' => 'select',
    '#title' => t('Project:'),
    '#options' => $project_options['options'],
    '#options_attributes' => $project_options['option_attributes'],
    '#prefix' => '<div class="datafield project nycha">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#validated' => TRUE,
    '#default_value' => $form_state->getValue('nycha_project', 'Select Project'),
  );

  $budget_type_options = BudgetFormUtil::getBudgetType(CheckbookDomain::$BUDGET);
  $form['filter']['nycha_budget_type'] = array(
    '#type' => 'select',
    '#title' => t('Budget Type:'),
    '#options' => $budget_type_options['options'],
    '#options_attributes' => $budget_type_options['option_attributes'],
    '#prefix' => '<div class="datafield budgettype nycha">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#validated' => TRUE,
    '#default_value' => $form_state->getValue('nycha_budget_type', 'Select Budget Type'),
  );

  $form['nycha_budget_type_hidden'] = array(
    '#title' => 'nycha_budget_type_hidden',
    '#value' => $form_state->getValue('nycha_budget_type'),
    '#type' => 'hidden',
  );

  $budget_name_options = BudgetFormUtil::getBudgetName(CheckbookDomain::$BUDGET, Datasource::NYCHA);
  $form['filter']['nycha_budget_name'] = array(
    '#type' => 'select',
    '#title' => t('Budget Name:'),
    '#options' => $budget_name_options['options'],
    '#options_attributes' => $budget_name_options['option_attributes'],
    '#prefix' => '<div class="datafield budgetname nycha">',
    '#suffix' => '</div>',
    '#attributes' => array('class' => array('watch')),
    '#validated' => TRUE,
    '#default_value' => $form_state->getValue('nycha_budget_name', 'Select Budget Name'),
  );

  $form['nycha_budget_name_hidden'] = array(
    '#title' => 'nycha_budget_name_hidden',
    '#value' => $form_state->getValue('nycha_budget_name'),
    '#type' => 'hidden',
  );

  $form['filter']['nycha-leftend'] = array(
    '#markup' => '</div>',
  );
  $form['filter']['nycha-rightbegin'] = array(
    '#markup' => '<div class="column column-right">',
  );
  //Adopted Amount
  $form['filter']['nycha_adopted_from'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield nycha_adopted_from datarange"><label>Adopted:</label><div class="ranges">',
    '#default_value' => $form_state->getValue('nycha_adopted_from', ''),
  );

  $form['filter']['nycha_adopted_to'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => $form_state->getValue('nycha_adopted_to', ''),
  );
  //Modified Amount
  $form['filter']['nycha_modified_from'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield nycha_modified_from datarange"><label>Modified:</label><div class="ranges">',
    '#default_value' => $form_state->getValue('nycha_modified_from', ''),
  );
  $form['filter']['nycha_modified_to'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => $form_state->getValue('nycha_modified_to', ''),
  );
  //Committed Amount
  $form['filter']['nycha_committed_from'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield nycha_committed_from datarange"><label>Committed:</label><div class="ranges">',
    '#default_value' => $form_state->getValue('nycha_committed_from', ''),
  );
  $form['filter']['nycha_committed_to'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => $form_state->getValue('nycha_committed_to', ''),
  );
  //Encumbered  Amount
  $form['filter']['nycha_encumbered_from'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield nycha_encumbered_from datarange"><label>Encumbered:</label><div class="ranges">',
    '#default_value' => $form_state->getValue('nycha_encumbered_from', ''),
  );
  $form['filter']['nycha_encumbered_to'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => $form_state->getValue('nycha_encumbered_to', ''),
  );
  //Actual Amount
  $form['filter']['nycha_actual_from'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield nycha_actual_from datarange"><label>Actual Amount:</label><div class="ranges">',
    '#default_value' => $form_state->getValue('nycha_actual_from', ''),
  );
  $form['filter']['nycha_actual_to'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => $form_state->getValue('nycha_actual_to', ''),
  );
  $form['filter']['nycha-rightend'] = array(
    '#markup' => '</div>',
  );
  return $form;
}

function checkbook_datafeeds_budget_get_column_default($form, $form_state, $column_id){
  $col_default = $form_state->getValue($column_id, '');
  return $col_default;
}

function _checkbook_datafeeds_budget_column_options($dataSource, $dataType)
{
  $options = DatafeedsConfigUtil::getConfig(strtolower(CheckbookDomain::$BUDGET));

  $options = $options['data_source'][$dataSource] ?? $options['data_source']['data_source'][Datasource::CITYWIDE];
  $options = $options['data_type'][$dataType] ?? 'CSV';

  ksort($options);
  return $options;
}

function checkbook_datafeeds_budget_next_submit($form, &$form_state)
{
  $current_step = &$form_state->get('step');

  $form_state->set(['step_information', $current_step, 'stored_values'], $form_state->getValues());

  $current_step = 'budget_confirmation';

  if ($form_state->hasValue(['step_information', $current_step, 'stored_values'])) {
    $form_state->set('values', $form_state->getValues(['step_information', $current_step, 'stored_values']));
  } else {
    $form_state->setValue('values', array());
  }

  $form_state
    ->set('user_values', $form_state->getValues())
    ->set('page_num', 3)
    // Since we have logic in our buildForm() method, we have to tell the form
    // builder to rebuild the form. Otherwise, even though we sØet 'page_num'
    // to 2, the AJAX-rendered form will still show page 1.
    ->setRebuild();
}

//@TODO: Check if called
function checkbook_datafeeds_budget_confirmation($form, &$form_state)
{
  //Get current data source
  $data_source = $form_state->getValue('datafeeds-budget-domain-filter');

  $feed = FeedFactory::getFeed($data_source, CheckbookDomain::$BUDGET);
  return $feed->process_confirmation($form, $form_state);
}

function checkbook_datafeeds_budget_confirmation_previous_submit($form, FormStateInterface $form_state)
{
  $current_step = &$form_state->get('step');
  $form_state->set(['step_information', $current_step, 'stored_values'], $form_state->getValues());
  $current_step = 'budget';
  $form_state
    ->setValues($form_state->get('user_values'))
    ->set('page_num', 2)
    ->setRebuild();
}

function checkbook_datafeeds_budget_validate($form, &$form_state)
{
  $data_source = $form_state->getValue('datafeeds-budget-domain-filter') ?? Datasource::CITYWIDE;
  $feed = FeedFactory::getFeed($data_source, CheckbookDomain::$BUDGET);
  $feed->checkbook_datafeeds_budget_validate($form, $form_state);
}

function checkbook_datafeeds_budget_filter_selected_columns($selected_columns, $data_source, $response_type, $export_order = null)
{
  //Get column configurations
  switch ($data_source) {
    case Datasource::NYCHA:
      $domain = 'budget_nycha';
      break;
    default:
      $domain = 'budget';
  }
  $domain_config = ConfigUtil::getDomainConfiguration($domain);

  //Get columns which should be included for the current data source
  $columns_included = $domain_config->$domain->dataset->displayConfiguration->$response_type->elementsColumn;

  //Filter out selected columns which are not configured to show for this data source
  $filtered_columns = array_intersect_key($selected_columns, (array)$columns_included);

  //Only for NYCHA, export order is not same as multi-select options
  if ($data_source == Datasource::NYCHA && isset($export_order)) {
    $filtered_columns = [];
    foreach ($columns_included as $key => $value) {
      if (in_array($key, $selected_columns)) {
        $filtered_columns[$key] = $key;
      }
    }
  }
  return $filtered_columns;
}

