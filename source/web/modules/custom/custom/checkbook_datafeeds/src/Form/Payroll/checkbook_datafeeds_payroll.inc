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
use Drupal\checkbook_datafeeds\Common\FeedFactory;
use Drupal\checkbook_datafeeds\DatafeedsConfigUtil;
use Drupal\checkbook_datafeeds\Utilities\FeedUtil;
use Drupal\checkbook_datafeeds\Utilities\FormUtil;
use Drupal\checkbook_infrastructure_layer\Constants\Common\CheckbookDomain;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;

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

/**
 * First step of Payroll section.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 *
 * @return array
 *   Renderable array of first step of Payroll
 */
function checkbook_datafeeds_payroll($form, &$form_state)
{
  //$datatype = $form_state->getValue('format');
  $datatype = $form_state->get(['step_information','type','stored_values','format']);
  //datafeeds.multi-select
  // $form['#attached']['library'][] = 'checkbook_datafeeds/datafeeds.payroll';

  $form['loaded'] = array(
    '#markup' => '<div class="updated-date">Last updated: ' . FeedUtil::getDataFeedsUpdatedDate(CheckbookDomain::$PAYROLL) . '</div>',
  );
  $form['requiredmessage'] = array(
    '#markup' => '<p class="required-message">Fields marked with an asterisk (<span class="form-required"></span>) are required.</p><br/>',
  );
  $form['description'] = array(
    '#markup' => '<h2 class="domain-title">Payroll</h2>'
  );

  $agency_options = FormUtil::getAgencies(Datasource::CITYWIDE);
  $format = $form_state->get(['step_information','type','stored_values','format']);
  $oge_options = FormUtil::getAgencies(Datasource::NYCHA);
  $oge_options = 'NEW YORK CITY HOUSING AUTHORITY [996]';
  $data_source = ($form_state->hasValue('datafeeds-payroll-domain-filter') ? $form_state->getValue('datafeeds-payroll-domain-filter') : Datasource::CITYWIDE);
  //<editor-fold desc="Heading">



  //</editor-fold>

  $form['datafeeds-payroll-domain-filter'] = DatafeedsConfigUtil::dataSourceRadio($data_source, CheckbookDomain::$PAYROLL);
  $form['filter'] = array(
    '#type' => 'fieldset',
    '#title' => t('3. Filter the Data:'),
    '#attributes' => array('class' => array('payroll-fieldset')),
  );

  $form['filter']['title'] = array(
    '#type' => 'textfield',
    '#title' => t('Title:'),
    '#attributes' => array('class' => array('watch')),
    '#default_value' => $form_state->getValue('title', ''),
    '#prefix' => '<div class="column column-left"><div class="datafield title">',
    //'#suffix' => '<input name="title_exact" value="' . $_POST['title_exact'] . '" type="hidden"></div>',
    //'#suffix' => '</div>',
  );
  $form['filter']['title_exact'] = array(
    '#value' => $form_state->getValue('title_exact', ''),
    '#type' => 'hidden',
    '#suffix' => '</div>',
  );
  $form['filter']['agency'] = array(
    '#type' => 'select',
    '#title' => t('Agency:'),
    '#options' => $agency_options['options'],
    '#options_attributes' => $agency_options['options_attributes'],
    '#attributes' => array('class' => array('watch')),
    '#default_value' => $form_state->getValue('agency', 'Citywide (All Agencies)'),
    '#prefix' => '<div class="datafield agency">',
    '#suffix' => '</div>',
  );
  $form['filter']['otherfrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield otherpay datarange"><label>Other Payments:</label><div class="ranges">',
    '#default_value' => $form_state->getValue('otherfrom', ''),
  );
  $form['filter']['otherto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => $form_state->getValue('otherto', ''),
  );
  $form['filter']['grossfrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield grosspay datarange"><label>Gross Pay:</label><div class="ranges">',
    '#default_value' => $form_state->getValue('grossfrom', ''),
  );
  $form['filter']['grossto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => $form_state->getValue('grossto', ''),
  );
  $form['filter']['totalgrossfrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield totalgross datarange"><label>Gross Pay YTD:</label><div class="ranges">',
    '#default_value' => $form_state->getValue('totalgrossfrom', ''),
  );
  $form['filter']['totalgrossto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => $form_state->getValue('totalgrossto', ''),
  );
  $form['filter']['salaryfrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield salary datarange"><label>Amount:</label><div class="ranges">',
    '#default_value' => $form_state->getValue('salaryfrom', ''),
  );
  $form['filter']['salaryto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => $form_state->getValue('salaryto', ''),
  );
  $form['filter']['salary_type'] = array(
    '#type' => 'radios',
    '#options' => array(
      '' => 'All',
      'ANNUAL' => 'Annual',
      'RATE' => 'Rate'
    ),
    '#default_value' => $form_state->getValue('salary_type', ''),
    '#prefix' => '<div class="datafield salarytype">',
    '#suffix' => '</div></div>',
  );
  $form['filter']['basepayfrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="column column-right"><div class="datafield basepay datarange"><label>Base Pay:</label><div class="ranges">',
    '#default_value' => $form_state->getValue('basepayfrom', ''),

  );
  $form['filter']['basepayto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => $form_state->getValue('basepayto', ''),

  );
  $form['filter']['overtimefrom'] = array(
    '#type' => 'textfield',
    '#size' => 15,
    '#maxlength' => 15,
    '#prefix' => '<div class="datafield overtime datarange"><label>Overtime Payments:</label><div class="ranges">',
    '#default_value' => $form_state->getValue('overtimefrom', ''),

  );
  $form['filter']['overtimeto'] = array(
    '#type' => 'textfield',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#suffix' => '</div></div>',
    '#default_value' => $form_state->getValue('overtimeto', ''),

  );

  $form['filter']['payfrequency'] = array(
    '#type' => 'select',
    '#title' => t('Pay Frequency:'),
    //@ToDo: Pay Frequency options were coming from _checkbook_advanced_search_get_payroll_frequency() function in d7
    '#options' => _checkbook_advanced_search_get_payroll_frequency(),
    /*'#options' => array('' => t('Select Pay Frequency'),
                        'BI-WEEKLY' => t('BI-WEEKLY'),
                        'HOURLY' => t('HOURLY'),
                        'SEMI-MONTHLY' => t('SEMI-MONTHLY'),
                        'SUPPLEMENTAL' => t('SUPPLEMENTAL'),
                        'WEEKLY' => t('WEEKLY'),),
    */
    '#attributes' => array('class' => array('watch')),
    '#default_value' => $form_state->getValue('payfrequency', ''),
    '#prefix' => '<div class="datafield payfrequency">',
    '#suffix' => '</div>',
  );
  $form['filter']['year'] = array(
    '#type' => 'select',
    '#options' => FormUtil::getYearOptions(),
    '#title' => t('Year:'),
    '#attributes' => array('class' => array('watch')),
    '#prefix' => '<div class="datafield fiscalyear">',
    '#default_value' => $form_state->getValue('year', ''),
    '#suffix' => '</div>',
    '#id' => 'edit-year',
    '#name' => 'year',
    '#validated' => true,
  );
  $form['filter']['paydatefrom'] = array(
    '#type' => 'date',
    '#size' => 15,
    '#maxlength' => 15,
    '#description' => 'E.g., ' . date('Y-m-d'),
    '#prefix' => '<div class="datafield paydate datarange"><label>Pay Date:</label><div class="ranges">',
    '#default_value' => $form_state->getValue('paydatefrom', ''),
    '#attributes' => array('class' => array('datepicker')),
  );
  $form['filter']['paydateto'] = array(
    '#type' => 'date',
    '#title' => t('to'),
    '#size' => 15,
    '#maxlength' => 15,
    '#description' => 'E.g., ' . date('Y-m-d'),
    '#suffix' => '</div></div></div>',
    '#default_value' => $form_state->getValue('paydateto', ''),
    '#attributes' => array('class' => array('datepicker')),
  );
  //THE COLUMNS - 01

  $form['columns'] = array(
    '#type' => 'fieldset',
    '#title' => t('4. Select Columns:'),
  );
  $form['columns']['column_select'] = array(
    '#type' => 'select',
    '#title' => t('Columns<span class="form-required" title="This field is required."></span>'),
    '#options' => _checkbook_datafeeds_payroll_column_options($datatype, Datasource::CITYWIDE),
    '#default_value' => $form_state->getValue('column_select', ''),
    '#multiple' => TRUE,
    '#validated' => TRUE,
  );

  $form['columns']['oge_column_select'] = array(
    '#type' => 'select',
    '#title' => t('Columns<span class="form-required" title="This field is required."></span>'),
    //'#options' => _checkbook_datafeeds_payroll_column_options($datatype, Datasource::NYCHA),
    '#options' => _checkbook_datafeeds_payroll_column_options($datatype, Datasource::NYCHA),
    '#multiple' => TRUE,
    '#validated' => TRUE,
    '#default_value' => $form_state->getValue('oge_column_select', ''),

  );
  //Hidden Field for multi-select

  if ($data_source == Datasource::NYCHA) {
    //$multi_select_hidden = isset($form_state['input']['oge_column_select']) ? '|' . implode('||', $form_state['input']['oge_column_select']) . '|' : '';
    //$multi_select_hidden = $form_state->hasValue('oge_column_select') ? '|' . implode('||', $form_state->getValue('oge_column_select')) . '|' : '';
    $multi_select_hidden = $form_state->get(['input', 'oge_column_select']) !== null ? '|' . implode('||', $form_state->get(['input', 'oge_column_select']) . '|') : '';
  } else {
    //$multi_select_hidden = isset($form_state['input']['column_select']) ? '|' . implode('||', $form_state['input']['column_select']) . '|' : '';
    //$multi_select_hidden = $form_state->hasValue('column_select') ? '|' . implode('||', $form_state->getValue('column_select')) . '|' : '';
    $multi_select_hidden = $form_state->get(['input', 'column_select']) !== null ? '|' . implode('||', $form_state->get(['input', 'column_select']) . '|') : '';
  }

  $form['columns']['hidden_multiple_value'] = array(
    '#value' => $multi_select_hidden,
    '#type' => 'hidden',
  );

  //datafeeds.multi-select
  //$form['#attached']['library'][] = 'checkbook_datafeeds/datafeeds.payroll';

  $form['prev'] = array(
    '#type' => 'submit',
    '#value' => t('Previous'),
    '#name' => 'prev',
    '#submit' => array('initial_previous_submit'),
    '#limit_validation_errors' => array(),
    '#attributes' => array("onclick" => "
             jQuery('#checkbook-datafeeds-form').addClass('disable_me');
             jQuery('#datafeeds-rotator').show();
        ")
  );

  $form['feeds_payroll_next'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
    '#submit' => array('checkbook_datafeeds_payroll_next_submit'),
    '#attributes' => array("onclick" => "
           jQuery('#checkbook-datafeeds-form').addClass('disable_me');
             jQuery('#datafeeds-rotator').show();
        ")
  );

  $form['#attached']['library'][] = 'checkbook_datafeeds/datafeeds.payroll';
  /*
  $form['#attached'] = array(
    'library' => array(
      array('system', 'ui.autocomplete', FALSE),
      array('system', 'ui.datepicker', FALSE),
      array('checkbook_datafeeds', 'jquery_multiselect', FALSE),
    ),
    'js' => array(
      'data' => drupal_get_path('module', 'checkbook_datafeeds') . '/js/payroll.js',
    )
  );
  */
  $form['#validate'] = array('checkbook_datafeeds_payroll_validate');
  $form['#attributes'] = array('class' => array('payroll', 'data-feeds-wizard', 'datafeeds-domain-form'));

  return $form;
}

/**
 * Column options for Payroll domain.
 *
 * @param string $datatype
 *   Type of data for Data Feeds to return (xml or csv)
 *
 * @param string $data_source
 * @return array
 *   FAPI structured array for select box #options
 */

function _checkbook_datafeeds_payroll_column_options($datatype, $data_source = "checkbook")
{
  $options = DatafeedsConfigUtil::getConfig(strtolower(CheckbookDomain::$PAYROLL));

  $options = $options['data_source'][$data_source] ?? $options['data_source']['data_source'][Datasource::CITYWIDE];
  $options = $options['data_type'][$datatype] ?? $options['data_type']['default'];
  ksort($options);
  return $options;
}

/**
 * Stores form values and moves to next step of Data Feeds wizard in Payroll.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 */

function checkbook_datafeeds_payroll_next_submit($form, &$form_state)
{
  $current_step = &$form_state->get('step');
  $form_state->set(['step_information', $current_step, 'stored_values'], $form_state->getValues());

  $current_step = 'payroll_confirmation';
  if (!empty($form_state->get(['step_information', $current_step, 'stored_values']))) {
    $form_state->set('values', $form_state->get(['step_information', $current_step, 'stored_values']));
  } else {
    //    $form_state['values'] = array();
    $form_state->setValue('values', array());
  }

  $form_state
    ->set('user_values', $form_state->getValues())
    ->set('page_num', 3)
    // Since we have logic in our buildForm() method, we have to tell the form
    // builder to rebuild the form. Otherwise, even though we set 'page_num'
    // to 2, the AJAX-rendered form will still show page 1.
    ->setRebuild();

}

function startsWith($haystack, $needle)
{
  $length = strlen($needle);
  return (substr($haystack, 0, $length) === $needle);
}

/**
 * Filters selected columns to only contain columns configured for this domain snd data_source
 *
 * @param array $selected_columns
 *   selected column array
 * @param string $data_source
 *   current selected data source (i.e. checkbook, checkbook_nycha)
 * @param string $response_type
 *   response type (i.e. csv, xml)
 * @param string $year_type
 * @return array
 */

function checkbook_datafeeds_payroll_filter_selected_columns($selected_columns, $data_source, $response_type, $year_type = 'calendar_year')
{
  //Get column configurations
  $domain = CheckbookDomain::$PAYROLL;
  $domain = $data_source == Datasource::NYCHA ? $domain . '_nycha' : $domain;
  $domain_config = ConfigUtil::getDomainConfiguration($domain);
  $domain = $domain . '_' . $year_type;

  //Get columns which should be included for the current data source
  $columns_included = $domain_config->$domain->dataset->displayConfiguration->$response_type->elementsColumn;
  //Filter out selected columns which are not configured to show for this data source
  $filtered_columns = array_intersect_key($selected_columns, (array)$columns_included);
  return $filtered_columns;
}

/**
 * Outputs selected search criteria and number of records found for Payroll.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 *
 * @return array
 *   Renderable array of Contracts confirmation step
 */
function checkbook_datafeeds_payroll_confirmation($form, &$form_state) {
  $data_source = $form_state->getValue('datafeeds-payroll-domain-filter');

  $feed = FeedFactory::getFeed($data_source, CheckbookDomain::$PAYROLL);
  return $feed->process_confirmation($form, $form_state);
}

/**
 * Retreives form values from previous step and moves back one step in Payroll.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 */

function checkbook_datafeeds_payroll_confirmation_previous_submit($form, &$form_state)
{
  $current_step = &$form_state->get('step');
  $form_state->set(['step_information', $current_step, 'stored_values'], $form_state->getValues());
  $current_step = 'payroll';
  $form_state
    ->setValues($form_state->get('user_values'))
    ->set('page_num', 2)
    ->setRebuild();
}

/**
 * Convert values from Payroll section of form to an array format expected by API SearchCriteria.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 *
 * @param $data_source
 * @return array
 *   Structured array expected by API
 */

function checkbook_datafeeds_process_payroll_values($form, &$form_state, $data_source)
{
  $values = $form_state->get(['step_information', 'payroll', 'stored_values']);
  $pattern = "/.*?(\\[.*?\\])/is";

  $criteria = array(
    'global' => array(
      //Set data source for query
      'type_of_data' => $data_source == Datasource::NYCHA ? 'Payroll_NYCHA' : 'Payroll',
      'records_from' => 1,
      'max_records' => \Drupal::config('check_book')->get('data_feeds')['max_record_limit'] ?? 200000,
    ),
    'responseColumns' => ($data_source == Datasource::NYCHA) ? $values['oge_column_select'] : $values['column_select'],
  );
  //$values['year'] = ckbk_datafeeds_payroll_fix_year($values['year']);
  if (startsWith($values['year'], 'F')) {
    $criteria['value']['fiscal_year'] = ltrim($values['year'], 'FY ');
  } elseif (startsWith($values['year'], 'C')) {
    $criteria['value']['calendar_year'] = ltrim($values['year'], 'CY ');
  }

  if ($data_source !== Datasource::NYCHA) {
    if ($values['agency'] != 'Citywide (All Agencies)') {
      preg_match($pattern, $values['agency'], $amatches);
      $criteria['value']['agency_code'] = trim($amatches[1], '[ ]');
    }
  }
  if ($values['title']) {
    $title_exact = $_POST['title_exact'];
    $title = $values['title'];
    if ($title_exact && $title_exact == $title)
      $criteria['value']['title_exact'] = $title_exact;
    else
      $criteria['value']['title'] = $title;
  }
  if ($values['payfrequency'] != '') {
    $criteria['value']['pay_frequency'] = $values['payfrequency'];
  }
  if ($values['basepayfrom'] !== '' || $values['basepayto'] !== '') {
    $criteria['range']['base_pay'] = array(
      checknull($values['basepayfrom']),
      checknull($values['basepayto']),
    );
  }
  if ($values['overtimefrom'] !== '' || $values['overtimeto'] !== '') {
    $criteria['range']['overtime_payments'] = array(
      checknull($values['overtimefrom']),
      checknull($values['overtimeto']),
    );
  }
  if ($values['otherfrom'] !== '' || $values['otherto'] !== '') {
    $criteria['range']['other_payments'] = array(
      checknull($values['otherfrom']),
      checknull($values['otherto']),
    );
  }
  if ($values['grossfrom'] !== '' || $values['grossto'] !== '') {
    $criteria['range']['gross_pay'] = array(
      checknull($values['grossfrom']),
      checknull($values['grossto']),
    );
  }
  if ($values['paydatefrom'] !== '' || $values['paydateto'] !== '') {
    $criteria['range']['pay_date'] = array(
      checknull($values['paydatefrom']),
      checknull($values['paydateto']),
    );
  }
  if ($values['totalgrossfrom'] !== '' || $values['totalgrossto'] !== '') {
    $criteria['range']['gross_pay_ytd'] = array(
      checknull($values['totalgrossfrom']),
      checknull($values['totalgrossto']),
    );
  }
  if ($values['salary_type']) {
    $criteria['value']['amount_type'] = $values['salary_type'];
  }
  if ($values['salaryfrom'] !== '' || $values['salaryto'] !== '') {
    $criteria['range']['amount'] = array(
      checknull($values['salaryfrom']),
      checknull($values['salaryto']),
    );
  }
  return $criteria;
}

/**
 * Validate handler for Payroll section of form.
 *
 * @param array $form
 *   Data Feeds wizard form array
 * @param array $form_state
 *   Data Feeds wizard form_state array
 */
function checkbook_datafeeds_payroll_validate($form, &$form_state)
{
//  $data_source = $form_state['input']['datafeeds-revenue-domain-filter'];
  $data_source = $form_state->getValue('datafeeds-payroll-domain-filter') ?? Datasource::CITYWIDE;
//  kint($data_source);
  $feed = FeedFactory::getFeed($data_source, CheckbookDomain::$PAYROLL);
  $feed->checkbook_datafeeds_payroll_validate($form, $form_state);
//  checkbook_datafeeds_revenue_validate($form, $form_state);
}
/**
 * Get payroll frequency using the data controller
 *
 * @return array|void
 */
function _checkbook_advanced_search_get_payroll_frequency()
{
  try {
    $dataController = data_controller_get_instance();
    $data = $dataController->queryDataset('checkbook:ref_pay_frequency', array(
      'pay_frequency'
    ), NULL, 'pay_frequency');

    $results = array('' => 'Select Pay Frequency');
    foreach ($data as $row) {
      $results[$row['pay_frequency']] = $row['pay_frequency'];
    }
    return array_unique($results);
  } catch (Exception $e) {
    LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
    return;
  }
}
