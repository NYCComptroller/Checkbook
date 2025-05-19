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

namespace Drupal\checkbook_advanced_search\Form;

use Drupal;
use Drupal\checkbook_datafeeds\Utilities\FormUtil;
use Drupal\checkbook_infrastructure_layer\Constants\Common\CheckbookDomain;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_log\LogHelper;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;
use Drupal\data_controller\Common\Object\Converter\Handler\Json2PHPArray;
use Exception;

require_once(dirname(__FILE__) . "/../Results/checkbook_advanced_search.inc");
require_once(dirname(__FILE__) . "/../Results/budget_advanced_search.inc");
require_once(dirname(__FILE__) . "/../Results/payroll_advanced_search.inc");
require_once(dirname(__FILE__) . "/../Results/revenue_advanced_search.inc");
require_once(dirname(__FILE__) . "/../Results/contracts_advanced_search.inc");
require_once(dirname(__FILE__) . "/../Results/spending_advanced_search.inc");

class CheckbookAdvancedSearchForm extends FormBase
{

  function getFormId()
  {
    return 'new_checkbook_advanced_search_form';
  }

  /**
   * Returns the smart search form
   * @param $form
   * @param $form_state
   * @return mixed
   */
  public function buildForm(array $form, FormStateInterface $form_state, $isAlert=false)
  {
    $requesting_page_datasource = RequestUtilities::get('datasource',['q' => RequestUtilities::getAjaxPath()]);
    //if requesting_page_datsource is not set, then default to checkbook
    $requesting_page_datasource = !isset($requesting_page_datasource) ? 'checkbook' : $requesting_page_datasource;

    $create_alert_view = "<div class='accordion' id='accordionAdvancedSearch'>";
    $agency_options = FormUtil::getAgencies(Datasource::CITYWIDE, false, false);
    $agencies = $agency_options['options'];
    $agency_attributes =  $agency_options['options_attributes'];

    $year_range = "'-" . (date("Y") - 1900) . ":+" . (2500 - date("Y")) . "'";

    if ($isAlert) {
      $form['alert_form_header'] = array(
        '#type' => 'markup',
        '#markup' => \Drupal::service('checkbook-alerts.helper')->checkbook_alerts_form_header('select_criteria'),
      );
      $form['advanced-search-rotator'] = array(
        '#type' => 'markup',
        '#markup' => '<div id="advanced-search-alert-rotator" class="hidden"></div>',
      );
      $form['alert_instruction'] = array(
        '#type' => 'markup',
        '#markup' => \Drupal::service('checkbook-alerts.helper')->checkbook_alerts_create_alert_instructions('select_criteria'),
      );
      $form['create_alert_results_loading'] = array(
        '#type' => 'markup',
        '#markup' => '<div class="create-alert-results-loading hidden">
          <div id="loading-icon"><img src="/themes/custom/nyccheckbook/images/loading_large.gif"></div>
        </div>',
      );
      $form["create-alert-customize-results"] = array(
        '#type' => 'markup',
        '#markup' => '<div class="create-alert-customize-results hidden"></div>',
      );
      $form["alert-error-messages"] = array(
        '#type' => 'markup',
        '#markup' => '<div class="alert-error-messages hidden"></div>',
        '#allowed_tags' => ['div', 'button', 'br', 'img', 'span'],
      );
      $form["create_alert_schedule_form"] = array(
        '#type' => 'markup',
        '#markup' => '<div class="create-alert-schedule-form hidden"></div>',
      );

      //adding hidden step and referal url fields
      $form['step'] = array(
        '#name' => 'step',
        '#value' => 'select_criteria',
        '#type' => 'hidden'
      );
      $form['user_redirect_url'] = array(
        '#name' => 'user_redirect_url',
        '#value' => '',
        '#type' => 'hidden'
      );
      $form['ajax_referral_url'] = array(
        '#name' => 'ajax_referral_url',
        '#value' => '',
        '#type' => 'hidden'
      );

      $form["create_alert_submit"] = array(
        '#type' => 'markup',
        '#markup' => '<div class="create-alert-submit clearfix">
          <input class="create_alerts_button form-submit hidden" type="submit" id="edit-next-submit" name="next_submit" value="Next" />
          <input class="create_alerts_button form-submit hidden" type="submit" id="edit-back-submit" name="back_submit" value="Back" />
        </div>',
        '#allowed_tags' => ['div', 'input'],
      );

      $form_state->set('alert_form_step_num', 1);
      $form_state->set('alert_form_state', 'select_criteria');
    }

    $form['rotator'] = array(
      '#type' => 'markup',
      '#markup' => '<div id="advanced-search-rotator" class="loading_bigger_gif hidden"> </div>',
    );
    $form['opening_div'] = array(
      '#type' => 'markup',
      '#markup' => $create_alert_view,
      '#allowed_tags' => ['div', 'button', 'h1', 'h2', 'h3'],
    );

    //<editor-fold desc="Budget">
    $disable_acordion_item_for_oge = ($requesting_page_datasource == Datasource::OGE) ? 'new-checkbook-advanced-search-disabled' : '';

    $display_none_style = 'display: none;';

    $budget_pref = "<div class='accordion-item'>
    <h3 class='accordion-header accordion-button collapsed $disable_acordion_item_for_oge' id='accord-budget' aria-controls='collapseBudget' aria-expanded='false' data-bs-target='#collapseBudget' data-bs-toggle='collapse'><span class='ui-icon ui-icon-triangle-1-e'></span> Budget</h3>
    <div aria-labelledby='accord-budget' class='accordion-collapse collapse' data-bs-parent='#accordionAdvancedSearch' id='collapseBudget' style='$display_none_style'>
    <div class='accordion-body'>";
    $form['budget_pref'] = array(
      '#type' => 'markup',
      '#markup' => $budget_pref,
      '#allowed_tags' => ['div', 'button', 'h1', 'h2', 'h3', 'span'],
    );

    if ($requesting_page_datasource != Datasource::OGE) {
      $form = $this->_checkbook_advanced_search_get_form($form, CheckbookDomain::$BUDGET, $agencies, $agency_attributes, $year_range);
      $form['checkbook_budget']['budget_advanced_search_domain_filter']['#default_value'] = $requesting_page_datasource;
    }

    $form['budget_sufix'] = array(
      '#type' => 'markup',
      '#markup' => "</div></div></div>",
      '#allowed_tags' => ['div', 'button', 'h1', 'h2', 'h3'],
    );
    //</editor-fold>

    //<editor-fold desc="Revenue">
    $revenue_pref = "<div class='accordion-item'>
    <h3 class='accordion-header accordion-button collapsed $disable_acordion_item_for_oge' id='accord-revenue' aria-controls='collapseRevenue' aria-expanded='false' data-bs-target='#collapseRevenue' data-bs-toggle='collapse'>
        <span class='ui-icon ui-icon-triangle-1-e'></span> Revenue
    </h3>
    <div aria-labelledby='accord-revenue' class='accordion-collapse collapse' data-bs-parent='#accordionAdvancedSearch' id='collapseRevenue' style='$display_none_style'>
    <div class='accordion-body'>";
    $form['revenue_pref'] = array(
      '#type' => 'markup',
      '#markup' => $revenue_pref,
      '#allowed_tags' => ['div', 'button', 'h1', 'h2', 'h3', 'span'],
    );

    if ($requesting_page_datasource != Datasource::OGE) {
      $form = $this->_checkbook_advanced_search_get_form($form, CheckbookDomain::$REVENUE, $agencies, $agency_attributes, $year_range);
      $form['checkbook_revenue']['revenue_advanced_search_domain_filter']['#default_value'] = $requesting_page_datasource;
    }

    $form['revenue_sufix'] = array(
      '#type' => 'markup',
      '#markup' => "</div></div></div>",
      '#allowed_tags' => ['div', 'button', 'h1', 'h2', 'h3'],
    );
    //</editor-fold>

    //<editor-fold desc="Spending">
    $spending_pref = "<div class='accordion-item'>
    <h3 class='accordion-header accordion-button collapsed' id='accord-spending' aria-controls='collapseSpending' aria-expanded='false' data-bs-target='#collapseSpending' data-bs-toggle='collapse'>
        <span class='ui-icon ui-icon-triangle-1-e'></span> Spending
    </h3>
    <div aria-labelledby='accord-spending' class='accordion-collapse collapse' data-bs-parent='#accordionAdvancedSearch' id='collapseSpending' style='$display_none_style'>
    <div class='accordion-body'>";
    $form['spending_pref'] = array(
      '#type' => 'markup',
      '#markup' => $spending_pref,
      '#allowed_tags' => ['div', 'button', 'h1', 'h2', 'h3', 'span'],
    );

    $form = $this->_checkbook_advanced_search_get_form($form, CheckbookDomain::$SPENDING, $agencies, $agency_attributes, $year_range);
    $form['checkbook_spending']['spending_advanced_search_domain_filter']['#default_value'] = $requesting_page_datasource;

    $form['spending_sufix'] = array(
      '#type' => 'markup',
      '#markup' => "</div></div></div>",
      '#allowed_tags' => ['div', 'button', 'h1', 'h2', 'h3'],
    );
    //</editor-fold>

    //<editor-fold desc="Contracts">
    $contracts_pref = "<div class='accordion-item'>
    <h3 class='accordion-header accordion-button collapsed' id='accord-contracts' aria-controls='collapseContracts' aria-expanded='false' data-bs-target='#collapseContracts' data-bs-toggle='collapse'>
        <span class='ui-icon ui-icon-triangle-1-e'></span> Contracts
    </h3>
    <div aria-labelledby='accord-contracts' class='accordion-collapse collapse' data-bs-parent='#accordionAdvancedSearch' id='collapseContracts' style='$display_none_style'>
    <div class='accordion-body'>";
    $form['contracts_pref'] = array(
      '#type' => 'markup',
      '#markup' => $contracts_pref,
      '#allowed_tags' => ['div', 'button', 'h1', 'h2', 'h3', 'span'],
    );

    $form = $this->_checkbook_advanced_search_get_form($form, CheckbookDomain::$CONTRACTS, $agencies, $agency_attributes, $year_range);
    $form['checkbook_contracts']['contracts_advanced_search_domain_filter']['#default_value'] = $requesting_page_datasource;

    $form['contracts_sufix'] = array(
      '#type' => 'markup',
      '#markup' => "</div></div></div>",
      '#allowed_tags' => ['div', 'button', 'h1', 'h2', 'h3'],
    );
    //</editor-fold>

    //<editor-fold desc="Payroll">
    $payroll_pref = "<div class='accordion-item'>
    <h3 class='accordion-header accordion-button collapsed $disable_acordion_item_for_oge' id='accord-payroll' aria-controls='collapsePayroll' aria-expanded='false' data-bs-target='#collapsePayroll' data-bs-toggle='collapse'>
        <span class='ui-icon ui-icon-triangle-1-e'></span> Payroll
        </h3>
    <div aria-labelledby='accord-payroll' class='accordion-collapse collapse' data-bs-parent='#accordionAdvancedSearch' id='collapsePayroll' style='$display_none_style'>
    <div class='accordion-body'>";
    $form['payroll_pref'] = array(
      '#type' => 'markup',
      '#markup' => $payroll_pref,
      '#allowed_tags' => ['div', 'button', 'h1', 'h2', 'h3', 'span'],
    );

    if ($requesting_page_datasource != Datasource::OGE) {
      $form['payroll']['payroll_advanced_search_domain_filter'] = [
        '#type' => 'radios',
        '#options' => [
          'checkbook' => 'Citywide Agencies',
          'checkbook_nycha' => 'New York City Housing Authority'
        ],
        '#default_value' => $requesting_page_datasource,
        '#prefix' => '<div id="payroll-advanced-search">',
      ];

      $form['payroll']['payroll_employee_name'] = [
        '#type' => 'textfield',
        '#title' => t('Title'),
        '#size' => 30,
        '#maxlength' => 100,
        '#prefix' => '<div class="column column-left">',
      ];
      $form['payroll']['payroll_employee_name_exact'] = [
        '#type' => 'hidden'
      ];
      $form['payroll']['checkbook_payroll_agencies'] = [
        '#type' => 'select',
        '#title' => t('Agency'),
        '#default_value' => t('Citywide(All Agencies)'),
        '#options' => $agencies,
        '#options_attributes' => $agency_attributes,
      ];
      $form['payroll']['payroll_other_payments_from'] = [
        '#type' => 'textfield',
        '#size' => 30,
        '#maxlength' => 32,
        '#prefix' => Markup::create('<div class="form-item form-item-payroll-other-payments"><label>Other Payments</label><div class="ranges">'),
      ];
      $form['payroll']['payroll_other_payments_to'] = [
        '#type' => 'textfield',
        '#title' => t('TO'),
        '#size' => 30,
        '#maxlength' => 32,
        '#suffix' => '</div></div>',
      ];
      $form['payroll']['payroll_gross_pay_amount_from'] = [
        '#type' => 'textfield',
        '#size' => 30,
        '#maxlength' => 32,
        '#prefix' => Markup::create('<div class="form-item form-item-payroll-pay-amount"><label>Gross Pay</label><div class="ranges">'),
      ];
      $form['payroll']['payroll_gross_pay_amount_to'] = [
        '#type' => 'textfield',
        '#title' => t('TO'),
        '#size' => 30,
        '#maxlength' => 32,
        '#suffix' => '</div></div>',
      ];
      $form['payroll']['payroll_total_gross_pay_from'] = [
        '#type' => 'textfield',
        '#size' => 30,
        '#maxlength' => 32,
        '#prefix' => Markup::create('<div class="form-item form-item-payroll-total-gross-pay"><label>Gross Pay YTD</label><div class="ranges">'),
      ];
      $form['payroll']['payroll_total_gross_pay_to'] = [
        '#type' => 'textfield',
        '#title' => t('TO'),
        '#size' => 30,
        '#maxlength' => 32,
        '#suffix' => '</div></div>',
      ];
      $form['payroll']['payroll_amount_from'] = [
        '#type' => 'textfield',
        '#size' => 30,
        '#maxlength' => 32,
        '#prefix' => Markup::create('<div class="form-item form-item-payroll-amount"><label>Amount</label><div class="ranges">'),
      ];
      $form['payroll']['payroll_amount_to'] = [
        '#type' => 'textfield',
        '#title' => t('TO'),
        '#size' => 30,
        '#maxlength' => 32,
        '#suffix' => '</div></div>',
      ];
      $form['payroll']['payroll_amount_type'] = [
        '#type' => 'radios',
        '#default_value' => 0,
        '#options' => [0 => t('All'), 1 => t('Annual'), 2 => t('Rate')],
        '#suffix' => '</div>',
      ];
      $form['payroll']['payroll_base_salary_from'] = [
        '#type' => 'textfield',
        '#size' => 30,
        '#maxlength' => 32,
        '#prefix' => Markup::create('<div class="column column-right"><div class="form-item form-item-payroll-base-salary"><label>Base Pay</label><div class="ranges">'),
      ];
      $form['payroll']['payroll_base_salary_to'] = [
        '#type' => 'textfield',
        '#title' => t('TO'),
        '#size' => 30,
        '#maxlength' => 32,
        '#suffix' => '</div></div>',
      ];
      $form['payroll']['payroll_overtime_amount_from'] = [
        '#type' => 'textfield',
        '#size' => 30,
        '#maxlength' => 32,
        '#prefix' => Markup::create('<div class="form-item form-item-payroll-overtime-amount"><label>Overtime Payments</label><div class="ranges">'),
      ];
      $form['payroll']['payroll_overtime_amount_to'] = [
        '#type' => 'textfield',
        '#title' => t('TO'),
        '#size' => 30,
        '#maxlength' => 32,
        '#suffix' => '</div></div>',
      ];
      $form['payroll']['payroll_pay_frequency'] = [
        '#type' => 'select',
        '#title' => t('Pay Frequency'),
        '#default_value' => t('Select Pay Frequency'),
        '#options' => _checkbook_advanced_search_get_payroll_frequency()
      ];
      $form['payroll']['payroll_year'] = [
        '#type' => 'select',
        '#title' => t('Year'),
        '#options' => _checkbook_advanced_search_get_year(CheckbookDomain::$PAYROLL),
      ];
      $form['payroll']['payroll_pay_date_from'] = [
        '#type' => 'date',
        '#description' => t('E.g., ' . date('Y-m-d')),
        '#date_format' => 'Y-m-d',
        '#date_year_range' => "'-" . (date("Y") - 1900) . ":+" . (2500 - date("Y")) . "'",
        '#prefix' => Markup::create('<div class="form-item form-item-payroll-pay-date"><label>Pay Date</label><div class="ranges">'),
      ];
      $form['payroll']['payroll_pay_date_to'] = [
        '#type' => 'date',
        '#description' => t('E.g., ' . date('Y-m-d')),
        '#date_format' => 'Y-m-d',
        '#date_year_range' => "'-" . (date("Y") - 1900) . ":+" . (2500 - date("Y")) . "'",
        '#title' => t('TO'),
        '#suffix' => '</div></div></div>',
      ];
      $next_visible = ($isAlert) ? '' : $display_none_style;
      $submit_visible = ($isAlert) ? $display_none_style : '';
      $form['payroll']['payroll_next'] = [
        '#type' => 'button',
        //'#type' => 'submit',
        '#value' => t('Next'),
        '#name' => 'payroll_next',
        '#prefix' => t('<div class="payroll-submit">'),
        '#attributes' => ['style' => $next_visible],
        '#submit' => ['checkbook_advanced_search_form_submit'],
        '#ajax' => [
          'callback' => 'checkbook_alerts_create_alert_results_ajax',
          'event' => 'click',
          'progress' => ['type' => 'none']
        ],
      ];
      $form['payroll']['payroll_submit'] = [
        '#type' => 'submit',
        '#value' => t('Submit'),
        '#name' => 'payroll_submit',
        '#attributes' => ['style' => $submit_visible],
        '#submit' => ['checkbook_advanced_search_form_submit'],
      ];
      $form['payroll']['payroll_clear'] = [
        '#suffix' => '</div></div>',
        '#type' => 'submit',
        '#value' => t('Clear All'),
      ];
    }
    $form['payroll_sufix'] = array(
      '#type' => 'markup',
      '#markup' => "</div></div></div>",
      '#allowed_tags' => ['div', 'button', 'h1', 'h2', 'h3'],
    );
    //</editor-fold>

    $form['closing_div'] = array(
      '#type' => 'markup',
      '#markup' => '</div>',
    );

    //show hide Submit and Next buttons
    if ($isAlert) {
      //budget
      $form['checkbook_budget']['budget_submit']['#attributes'] = array('style'=>"$display_none_style");
      $form['checkbook_nycha_budget']['budget_submit']['#attributes'] = array('style'=>"$display_none_style");
      //revenue
      $form['checkbook_revenue']['revenue_submit']['#attributes'] = array('style'=>"$display_none_style");
      $form['checkbook_nycha_revenue']['revenue_submit']['#attributes'] = array('style'=>"$display_none_style");
      //spending
      $form['checkbook_spending']['spending_submit']['#attributes'] = array('style'=>"$display_none_style");
      $form['checkbook_oge_spending']['spending_submit']['#attributes'] = array('style'=>"$display_none_style");
      $form['checkbook_nycha_spending']['spending_submit']['#attributes'] = array('style'=>"$display_none_style");
      //contracts
      $form['checkbook_contracts']['contracts_submit']['#attributes'] = array('style'=>"$display_none_style");
      $form['checkbook_oge_contracts']['contracts_submit']['#attributes'] = array('style'=>"$display_none_style");
      $form['checkbook_nycha_contracts']['contracts_submit']['#attributes'] = array('style'=>"$display_none_style");
    } else {
      //budget
      $form['checkbook_budget']['budget_next']['#attributes'] = array('style'=>"$display_none_style");
      $form['checkbook_nycha_budget']['budget_next']['#attributes'] = array('style'=>"$display_none_style");
      //revenue
      $form['checkbook_revenue']['revenue_next']['#attributes'] = array('style'=>"$display_none_style");
      $form['checkbook_nycha_revenue']['revenue_next']['#attributes'] = array('style'=>"$display_none_style");
      //spending
      $form['checkbook_spending']['spending_next']['#attributes'] = array('style'=>"$display_none_style");
      $form['checkbook_oge_spending']['spending_next']['#attributes'] = array('style'=>"$display_none_style");
      $form['checkbook_nycha_spending']['spending_next']['#attributes'] = array('style'=>"$display_none_style");
      //contracts
      $form['checkbook_contracts']['contracts_next']['#attributes'] = array('style'=>"$display_none_style");
      $form['checkbook_oge_contracts']['contracts_next']['#attributes'] = array('style'=>"$display_none_style");
      $form['checkbook_nycha_contracts']['contracts_next']['#attributes'] = array('style'=>"$display_none_style");
    }


    //<editor-fold desc="Create Alert Fields">
    $form['#attached']['library'][] = 'checkbook_advanced_search/search-iframe';
    //</editor-fold>
    return $form;
  }

  /**
   * Implements hook_form_alter().
   */

  function checkbook_advanced_search_form_alter(&$form, FormStateInterface $form_state, $form_id)
  {
    $form['#attached']['library'][] = 'checkbook_advanced_search/search-iframe';
    return $form;
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * This function is required to implement, using custom submit functions for form submission, so this will be empty.
   *
   * @return void
   */
  function submitForm(array &$form, FormStateInterface $form_state)
  {
  }

  function _checkbook_advanced_search_get_form($form, $domain, $agencies, $agency_attributes, $year_range)
  {
    $field_configurations = $this->_checkbook_advanced_search_get_field_configs($domain);
    $checkbook_form = new Form($domain);

    foreach ($field_configurations as $key => $value) {
      $data_source = $key;
      $field_configs = $value;
      $all_field_def = $this->_checkbook_advanced_search_filter_field_definition($domain, $data_source, $agencies, $agency_attributes, $year_range);
      $checkbook_content = new Content($data_source);
      foreach ($field_configs as $field_config) {
        $field_name = $field_config['field_name'];
        $column = $field_config['column'];
        $field_def = $all_field_def[$field_name];
        if (!isset($field_def)) {
          LogHelper::log_error('Could not find field definition for ' . $domain . ' domain field: ' . $field_name);
        } else {
          $disabled = $field_def['disabled'] ?? null;
          $disabled = (is_null($disabled)) ? FALSE : $disabled;
          $field = new Field($field_def['field_name'], $field_def['field_type'], $field_def['attributes'] ?? null, $disabled);
          $checkbook_content->add_field($field, $column);
        }
      }
      $checkbook_form->add_content($checkbook_content);
    }
    $form = $checkbook_form->generate_form($form);
    return $form;
  }

  function _checkbook_advanced_search_get_field_configs($domain)
  {
    $checkbook_advanced_search_path = Drupal::service('extension.list.module')->getPath('checkbook_advanced_search');
    $config_str = file_get_contents(realpath($checkbook_advanced_search_path) . "/src/config/checkbook_advanced_search_" . strtolower($domain) . "_field_configurations.json");
    $converter = new Json2PHPArray();
    return $converter->convert($config_str);
  }

  function checkbook_advanced_search_get_domain_field_value($form_state, $filter_dimension, $field) {
    $field_name = $filter_dimension . "_" . $field;
    $value = $form_state->getValue($field_name) ?? '';
    return trim($value);
  }

  function checkbook_advanced_search_generate_redirect_url_parameter($value, $parameter_name, $is_range = FALSE) {
    return "/" . $parameter_name . "/" . $value;
  }

  function _checkbook_advanced_search_filter_field_definition($domain, $data_source, $agencies, $agency_attributes, $year_range)
  {
    if ($data_source == Datasource::NYCHA) {
      $responsibility_center_attributes = _checkbook_advanced_search_get_responsibility_centers($data_source);
      $contract_type_attribute = _checkbook_advanced_search_get_nycha_contract_types($data_source);
      $fundsrc_attributes = checkbook_advanced_search_get_nycha_funding_sources($data_source);

      $year_attribute = _checkbook_advanced_search_get_year($domain, NULL, $data_source);
      if (in_array($domain, [CheckbookDomain::$BUDGET, CheckbookDomain::$REVENUE])) {
        $expense_category_attributes = $this->_budget_expcat_options($domain, NULL, NULL, NULL, Datasource::NYCHA, false);
        $program_attributes = $this->_budget_program_options(Datasource::NYCHA, false);
        $project_attributes = $this->_budget_project_options(Datasource::NYCHA, false);
      }
    } else {
      $mwbe_category_attributes =  FormUtil::getMWBECategory();
      $sub_vendor_status = FormUtil::getSubvendorStatusInPIP(false);
      $includes_sub_vendors = FormUtil::getContractIncludesSubvendors();
      $contract_type_attribute = FormUtil::getContractTypes();
      $event_type_attributes = FormUtil::getEventNameAndId();
      $year_attribute = _checkbook_advanced_search_get_year($domain, null, $data_source);
      $revenue_categories = _checkbook_advanced_search_get_revenue_category_and_id();
      $revenue_funding_classes = _checkbook_advanced_search_get_funding_source_and_id();
      $revenue_fund_classes = _checkbook_advanced_search_get_fund_class_and_id();
      $all_fiscal_years = array(0 => "All Fiscal Years");
      if (is_array($year_attribute)) {
        $all_fiscal_years += $year_attribute;
      }
    }
    $expense_attributes = _checkbook_advanced_search_get_expensetype_and_id($data_source);
    $industry_attributes =  FormUtil::getIndustry($data_source);
    $award_method_attributes = FormUtil::getAwardMethod($data_source);

    $field_definition_configs = $this->_checkbook_advanced_search_get_field_def_configs();
    $current_fiscal_year_id = CheckbookDateUtil::getCurrentFiscalYearId($data_source);
    foreach ($field_definition_configs as $i => $field_config) {
      if (!empty($field_config['attributes'])) {
        $attributes = $field_config['attributes'] ?? null;
        if (!empty($attributes['options'])) {
          $options = $attributes['options'];
          if ($domain == 'budget' && !empty($field_config['attributes']['options']['checkbook_oge'])){
            unset($field_config['attributes']['options']['checkbook_oge']);
          }
          switch ($options) {
            case '$agencies':
              $field_definition_configs[$i]['attributes']['options'] = $agencies;
              break;
            case '$expense_attributes':
              $field_definition_configs[$i]['attributes']['options'] = $expense_attributes;
              break;
            case'$year_attribute':
              $field_definition_configs[$i]['attributes']['options'] = $year_attribute;
              $field_definition_configs[$i]['attributes']['default_value'] = $current_fiscal_year_id;
              break;
            case'$award_method_attributes':
              $field_definition_configs[$i]['attributes']['options'] = $award_method_attributes['options'];
              $field_definition_configs[$i]['attributes']['option_attributes'] = $award_method_attributes['option_attributes'];
              break;
            case'$year_range':
              $field_definition_configs[$i]['attributes']['options'] = $year_range;
              break;
            case'$contract_type_attribute':
              $field_definition_configs[$i]['attributes']['options'] = $contract_type_attribute['options'] ?? null;
              $field_definition_configs[$i]['attributes']['option_attributes'] = $contract_type_attribute['option_attributes'] ?? null;
              break;
            case'$event_type_attributes':
              $field_definition_configs[$i]['attributes']['options'] = $event_type_attributes ?? null;
              break;
            case '$mwbe_category_attributes':
              $field_definition_configs[$i]['attributes']['options'] = $mwbe_category_attributes ?? null;
              break;
            case '$industry_attributes':
              $field_definition_configs[$i]['attributes']['options'] = $industry_attributes['options'];
              $field_definition_configs[$i]['attributes']['option_attributes'] = $industry_attributes['option_attributes'];
              break;
            case '$sub_vendor_status':
              $field_definition_configs[$i]['attributes']['options'] = $sub_vendor_status['options'] ?? null;
              break;
            case '$includes_sub_vendors':
              $field_definition_configs[$i]['attributes']['options'] = $includes_sub_vendors['options'] ?? null;
              break;
            case '$responsibility_center_attributes':
              $field_definition_configs[$i]['attributes']['options'] = $responsibility_center_attributes['options'] ?? null;
              $field_definition_configs[$i]['attributes']['option_attributes'] = $responsibility_center_attributes['option_attributes'] ?? null;
              break;
            case '$fundsrc_attributes':
              $field_definition_configs[$i]['attributes']['options'] = $fundsrc_attributes['options'] ?? null ;
              $field_definition_configs[$i]['attributes']['option_attributes'] = $fundsrc_attributes['option_attributes'] ?? null;
              break;
            case '$expense_category_attributes':
              if ($domain == CheckbookDomain::$BUDGET || $domain == CheckbookDomain::$REVENUE) {
                $field_definition_configs[$i]['attributes']['options'] = $expense_category_attributes['options']?? null;
                $field_definition_configs[$i]['attributes']['option_attributes'] = $expense_category_attributes['option_attributes']?? null;
              }
              break;
            case '$program_attributes':
              if ($domain == CheckbookDomain::$BUDGET || $domain == CheckbookDomain::$REVENUE) {
                $field_definition_configs[$i]['attributes']['options'] = $program_attributes['options'] ?? null;
                $field_definition_configs[$i]['attributes']['option_attributes'] = $program_attributes['option_attributes'] ?? null;
              }
              break;
            case '$project_attributes':
              if ($domain == CheckbookDomain::$BUDGET || $domain == CheckbookDomain::$REVENUE) {
                $field_definition_configs[$i]['attributes']['options'] = $project_attributes['options'] ?? null;
                $field_definition_configs[$i]['attributes']['option_attributes'] = $project_attributes['option_attributes'] ?? null;
              }
              break;
            case '$revenue_categories':
              $field_definition_configs[$i]['attributes']['options'] = $revenue_categories ?? null;
              break;
            case '$revenue_funding_classes':
              $field_definition_configs[$i]['attributes']['options'] = $revenue_funding_classes ?? null;
              break;
            case '$revenue_fund_classes':
              $field_definition_configs[$i]['attributes']['options'] = $revenue_fund_classes ?? null;
              break;
            case '$all_fiscal_years':
              $field_definition_configs[$i]['attributes']['options'] = $all_fiscal_years ?? null;
              break;
            default:
              //will not add a field_definition_configs if not matching preceding values in above cases
          }
        }
        if (!empty($attributes['option_attributes'])) {
          $option_attributes = $attributes['option_attributes'];
          switch ($option_attributes) {
            case '$agency_attributes':
              $field_definition_configs[$i]['attributes']['option_attributes'] = $agency_attributes;
              break;
            case '$sub_vendor_status_attributes':
              $field_definition_configs[$i]['attributes']['option_attributes'] = $sub_vendor_status['options_attributes'] ?? null ;
              break;
            case '$includes_sub_vendors_attributes':
              $field_definition_configs[$i]['attributes']['option_attributes'] = $includes_sub_vendors['options_attributes'] ?? null;
              break;
            case '':
              $field_definition_configs[$i]['attributes']['option_attributes'] = $industry_attributes;
            default:
              //do nothing if above case statements not match
          }
        }
      }
    }
    return $field_definition_configs;
  }

  /**
   * Get Expenditure Category from Data Controller and format into a FAPI select input #options array.
   *
   * @param $domain
   * @param $year
   *   Year
   * @param $agency
   *   Agency code
   * @param $dept
   *   Department code
   * @param $dataSource
   * @param $feeds
   * @return mixed Expenditure object codes and expenditure object names, filtered by agency, department, year
   *   Expenditure object codes and expenditure object names, filtered by agency, department, year
   */
  function _budget_expcat_options($domain, $year, $agency, $dept, $dataSource = Datasource::CITYWIDE, $feeds = true)
  {
    if ($dataSource == Datasource::NYCHA) {
      $query = "SELECT DISTINCT expenditure_type_description || ' [' || expenditure_type_code || ']' AS expenditure_object_code,
                                  expenditure_type_id, expenditure_type_description
                  FROM {$domain} ORDER BY expenditure_object_code ASC";
      $results = _checkbook_project_execute_sql_by_data_source($query, $dataSource);
      $title = ($domain == CheckbookDomain::$REVENUE) ? 'Select Revenue Expense Category' : 'Select Expense Category';
      $options[''] = $title;
      $option_attributes = array($title => array('title' => $title));
      foreach ($results as $row) {
        if ($feeds) {
          $text = $row['expenditure_object_code'];
          $option_attributes[$text] = array('title' => $text);
          $options[$text] = FormattingUtilities::_ckbk_excerpt($text);
        } else {
          $text = $row['expenditure_type_description'];
          $option_attributes[$row['expenditure_type_id']] = array('title' => $text);
          $options[$row['expenditure_type_id']] = FormattingUtilities::_ckbk_excerpt($text);
        }
      }
      return array('options' => $options, 'option_attributes' => $option_attributes);
    } else {
      $agency = emptyToZero(urldecode($agency));
      $dept = emptyToZero(urldecode($dept));
      if ($agency) {
        $agencyString = " agency_code = '" . $agency . "' ";
        $yearString = ($year) ? " AND budget_fiscal_year = " . ltrim($year, 'FY') . " " : "";
        $deptString = ($dept) ? " AND department_code = '" . $dept . "' " : "";

        $query = "SELECT DISTINCT object_class_name || ' [' || object_class_code || ']' expenditure_object_code  FROM {$domain} WHERE"
          . $agencyString . $yearString . $deptString . "ORDER BY expenditure_object_code ASC";
        $results = _checkbook_project_execute_sql_by_data_source($query, $dataSource);
        $options = array();
        if (count($results) > 0) {
          foreach ($results as $result) {
            $options[$result['expenditure_object_code']] = $result['expenditure_object_code'];
          }
        }
        $matches = array();
        foreach ($options as $value) {
          $matches[] = htmlentities($value);
        }
        drupal_json_output($matches);
      }
    }
  }

  /**
   * Get Program Phase from Data Controller and format into a FAPI select input #options array.
   * @param $dataSource
   * @param $feeds
   * @return array of Program Phase data
   */
  function _budget_program_options($dataSource = Datasource::CITYWIDE, $feeds = true)
  {
    if ($dataSource == Datasource::NYCHA) {
      $query = "SELECT DISTINCT program_phase_description || ' [' || program_phase_code  || ']' AS program,
                  program_phase_description, program_phase_id
                FROM ref_program_phase WHERE program_phase_description NOT iLIKE '%default%' ORDER BY program ASC";
      $data = _checkbook_project_execute_sql_by_data_source($query, $dataSource);
      $title = 'Select Program';
      $options[''] = $title;
      $option_attributes = array($title => array('title' => $title));
      foreach ($data as $row) {
        if ($feeds) {
          $text = $row['program'];
          $option_attributes[$text] = array('title' => $text);
          $options[$text] = FormattingUtilities::_ckbk_excerpt($text);
        } else {
          $text = $row['program_phase_description'];
          $option_attributes[$row['program_phase_id']] = array('title' => $text);
          $options[$row['program_phase_id']] = FormattingUtilities::_ckbk_excerpt($text);
        }
      }
      return array('options' => $options, 'option_attributes' => $option_attributes);
    }
  }

  /**
   * Get Program Phase from Data Controller and format into a FAPI select input #options array.
   * @param $dataSource
   * @return array of Program Phase data
   */
  function _budget_project_options($dataSource = Datasource::CITYWIDE, $feeds = true)
  {
    if ($dataSource == Datasource::NYCHA) {
      $query = "SELECT gl_project_description || ' [' || gl_project_code  || ']' AS project,
                gl_project_description, gl_project_id FROM ref_gl_project
                WHERE gl_project_description NOT iLIKE '%Default%' ORDER BY gl_project_description ASC";
      $data = _checkbook_project_execute_sql_by_data_source($query, $dataSource);
      $title = 'Select Project';
      $options[''] = $title;
      $option_attributes[''] = array('title' => $title);
      foreach ($data as $row) {
        if ($feeds) {
          $text = $row['project'];
          $option_attributes[$text] = array('title' => $text);
          $options[$text] = FormattingUtilities::_ckbk_excerpt($text);
        } else {
          $text = $row['gl_project_description'];
          $option_attributes[$row['gl_project_id']] = array('title' => $text);
          $options[$row['gl_project_id']] = FormattingUtilities::_ckbk_excerpt($text);
        }
      }
      return array('options' => $options, 'option_attributes' => $option_attributes);
    }
  }

  function _checkbook_advanced_search_get_field_def_configs()
  {
    $checkbook_advanced_search_path = Drupal::service('extension.list.module')->getPath('checkbook_advanced_search');
    $config_str = file_get_contents(realpath($checkbook_advanced_search_path) . "/src/config/checkbook_advanced_search_field_definitions.json");

    $converter = new Json2PHPArray();
    $configuration = $converter->convert($config_str);
    return $configuration;
  }

  /**
   * Get funding class name and code using the data controller
   *
   * @return array|void
   */
  function _checkbook_advanced_search_get_funding_source_and_id()
  {
    try {
      $dataController = data_controller_get_instance();
      $data = $dataController->queryDataset('checkbook:ref_funding_class', array(
        'funding_class_code',
        'funding_class_name'
      ), NULL, 'funding_class_name');
      $results = array('' => 'All Funding Classes');
      foreach ($data as $row) {
        $results[$row['funding_class_code']] = $row['funding_class_name'];
      }
      return array_unique($results);
    } catch (Exception $e) {
      LogHelper::log_error("Error getting data from controller: \n" . $e->getMessage());
      return;
    }
  }

}
