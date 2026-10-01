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

namespace Drupal\checkbook_widget_retroactivity\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\checkbook_datafeeds\Utilities\FormUtil;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\checkbook_widget_retroactivity\Plugin\Block\RetroactivityChartTilesBlock;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\checkbook_widget_retroactivity\Config\RetroactivityConfigService;

/**
 * Provides a Retroactivity Chart Filter form.
 */
class RetroactivityChartFilterForm extends FormBase {

 /**
   * The config service.
   *
   * @var Drupal\checkbook_widget_retroactivity\Config\RetroactivityConfigService
   */
  protected $configService;

  /**
   * Constructs a new instance.
   */
  public function __construct(RetroactivityConfigService $config_service) {
    $this->configService = $config_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('checkbook_widget_retroactivity.retroactivity_config')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'checkbook_widget_retroactivity_filter_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $base_url = \Drupal::request()->getSchemeAndHttpHost();
    $form['#attached']['library'][] = 'checkbook_widget_retroactivity/retroactivity-form';
    $data_source = Datasource::CITYWIDE;
    $agency_options = FormUtil::getAgencies($data_source);
    $industry_attributes =  FormUtil::getIndustry($data_source);
    //$mwbe_cats = FormUtil::getMWBECategory();

    // Remove the unwanted options.
    // Remove Individuals and Others. Remove Emerging and group them under Non-MWBE
    // Use a hardcoded array as NYC needs a specific order.
    $mwbe_cats = [
      "4~5~10" => "Asian American",
      "2" => "Black American",
      "3" => "Hispanic American",
      "9" => "Women (Non-Minority)",
      "6" => "Native American",
      //"99" => "Emerging (Non-Minority)",
      "7~99" => "Non-M/WBE"
    ];

    // Use a hardcoded array as NYC wants to customize the order and labels.
    $industry_options = [
      "1" => "Construction Service",
      "2" => "Goods",
      "6" => "Human Services",
      "3" => "Professional Service",
      "4" => "Standardized Service",
      "5" => "Not Classified"
    ];
    
    $form['retroactivity-rotator'] = [
      '#type' => 'markup',
      '#markup' => '<div id="retroactivity-rotator" class="loading_bigger_gif"></div>',
    ];
    // 1. Fiscal Year Range
    $form['fy_range'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['container-inline']],
    ];
    $fiscalYears = CheckbookDateUtil::getFiscalYearOptionsRange($data_source);
    $total_years = $this->loadConfig('defaultYearCount');
    $default_from_year = NULL;
    $counter = 0;
    //Default to show 4 years.
    foreach ($fiscalYears as $value) {
      if ($counter >= $total_years) {
        break;
      }
      if ($counter == 3) {
        $default_from_year = $value['year_id'];
      }
      $year_options[$value['year_id']] = "FY " . $value['year_value'];
      $counter++;
    }
    $form['fy_range']['year-from'] = [
      '#type' => 'select',
      '#options' => $year_options,
      '#attributes' => array('class' => array('watch')),
      '#default_value' => $default_from_year,
      '#prefix' => '<div class="datafield year">',
      '#suffix' => '</div>',
      '#id' => 'edit-year-from',
      '#name' => 'year-from',
      '#states' => array(
        'disabled' => array(
          'select[name="df_contract_status"]' => array('value' => 'pending')
        )
      )
    ];

    $form['fy_range']['year-to'] = array(
      '#type' => 'select',
      '#options' => $year_options,
      '#attributes' => array('class' => array('watch')),
      '#default_value' => $form_state->getValue('year-to', ''),
      '#prefix' => '<div class="datafield year">&nbsp;to&nbsp;',
      '#suffix' => '</div>',
      '#id' => 'edit-year-to',
      '#name' => 'year-to',
      '#states' => array(
        'disabled' => array(
          'select[name="df_contract_status"]' => array('value' => 'pending')
        )
      )
    );

    $form['agency'] = array(
      '#title' => $this->t('AGENCY'),
      '#type' => 'textfield',
      '#attributes' => [
        'placeholder' => $this->t('Type at least 3 characters to start search'),
        'class' => ['watch', 'js-agency-autocomplete'],
        'autocomplete' => 'new-password',
      ],
      '#prefix' => '<div class="datafield agency">',
      '#id' => 'edit-agency',
      '#name' => 'agency',
      '#size' => 34,
      '#default_value' => $form_state->getValue('agency', ''),
    );
    // Have a hidden field to hold the value of autocomplete selection.
    $form['agency_selected'] = array(
      '#type' => 'hidden',
      '#suffix' => '</div>',
      '#attributes' => [
        'id' => 'edit-agency-selected',
      ],
      '#name' => 'agency-selected'
    );

    $form['vendor'] = array(
      '#title' => $this->t('VENDOR'),
      '#type' => 'textfield',
      '#attributes' => [
        'placeholder' => $this->t('Type at least 3 characters to start search'),
        'class' => ['watch', 'js-vendor-autocomplete'],
        'autocomplete' => 'new-password',
      ],
      '#prefix' => '<div class="datafield vendor">',
      '#id' => 'edit-vendor',
      '#name' => 'vendor',
      '#size' => 34,
      '#default_value' => $form_state->getValue('vendor', ''),
    );
    // Have a hidden field to hold the value of autocomplete selection.
    $form['vendor_selected'] = array(
      '#type' => 'hidden',
      '#suffix' => '</div>',
      '#attributes' => [
        'id' => 'edit-vendor-selected',
      ],
      '#name' => 'vendor-selected'
    );

    $form['mwbe_category'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('M/WBE CATEGORY'),
      '#options' => $mwbe_cats,
      '#default_value' => $form_state->getValue('mwbe_category', []), //
      '#prefix' => '<div class="datafield mwbecategory">',
      '#suffix' => '</div>',
      '#attributes' => [
        'class' => ['watch'],
      ],
    ];

    $form['industry'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('INDUSTRY'),
      '#options' => $industry_options,
      '#default_value' => $form_state->getValue('industry', []),
      '#attributes' => array('class' => array('watch')),
      '#prefix' => '<div class="datafield industry">',
      '#suffix' => '</div>',
      '#id' => 'edit-industry',
      '#name' => 'industry'
    ];

    // Non-Profit Status
    $form['non_profit'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('NON-PROFIT STATUS'),
      '#options' => ['1' => $this->t('YES'), '0' => $this->t('NO')],
      '#default_value' => $form_state->getValue('non_profit', []),
      '#prefix' => '<div class="datafield non_profit">',
      '#suffix' => '</div>',
    ];
    // A hidden field to track the loaded chart.
    $form['chart_id'] = [
      '#type' => 'hidden',
      '#default_value' => 'contract-volume-chart-1',
      '#attributes' => [
        'id' => 'edit-chart-id',
      ],
    ];

    // A hidden field to toggle the sorting order.
    $form['sort_order'] = [
      '#type' => 'hidden',
      '#default_value' => 0,
      '#attributes' => [
        'id' => 'edit-sort-order',
      ],
    ];
    // Actions
    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#id' => 'edit-submit',
      '#attributes' => [
        'class' => ['button', 'js-form-submit', 'form-submit', 'chartfilter'],
      ],
    ];
    $form['actions']['clear'] = [
      '#type' => 'button',
      '#value' => $this->t('Clear All'),
      '#id' => 'edit-clear',
      '#attributes' => [
        'class' => ['button', 'chartfilter'],
      ],
    ];

    $form['#attached']['drupalSettings']['checkbook_retroactivity_config'] = $this->loadConfig();

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $form_state->setRebuild();
  }

/**
   * The AJAX Callback Method.
   *
   */
  public function ajaxSubmitCallback(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $arr_filters = [];
    $arr_filters['year_start'] = $form_state->getValue('year-from');
    $arr_filters['year_end']   = $form_state->getValue('year-to');
    $arr_filters['agency']     = $form_state->getValue('agency') ?: NULL;
    $arr_filters['vendor']     = $form_state->getValue('vendor') ?: NULL;

    $mwbe_values = array_filter($form_state->getValue('mwbe_category') ?: []);
    $arr_filters['mwbe'] = !empty($mwbe_values) ? implode('~', array_keys($mwbe_values)) : NULL;

    $industry_values = array_filter($form_state->getValue('industry') ?: []);
    $arr_filters['industry'] = !empty($industry_values) ? implode('~', array_keys($industry_values)) : NULL;

    $np_values = array_filter($form_state->getValue('non_profit') ?: []);
    $arr_filters['np_status'] = !empty($np_values) ? implode('~', array_keys($np_values)) : NULL;

    //"year-start",  "year-end", "np-status", "agency", "vendor",  "mwbe", "industry"
    $ajax_url = "/widget/ajax";
    $ajax_url .= "/" . (empty($form_state->getValue('chart_id')) ?? "contract-volume-chart-1");

    foreach ($arr_filters as $key => $value) {
      if (!empty($value)) {
        $ajax_url .= "/" . $key . "/" . $value;
      }
    }

    $response->addCommand(new InvokeCommand(NULL, 'updateRetroactivityChart', [$ajax_url]));

    return $response;
  }

  /**
   * Loads the tab configuration from the config service.
   */
  protected function loadConfig($configItem = NULL) {

    return is_null($configItem) ? $this->configService->getRetroactivityConfig() : $this->configService->getConfigVariable($configItem);
  }

}
