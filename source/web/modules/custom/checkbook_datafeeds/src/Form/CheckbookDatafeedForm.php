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

namespace Drupal\checkbook_datafeeds\Form;

use Drupal;
use Drupal\checkbook_datafeeds\BudgetFeedCitywide;
use Drupal\checkbook_datafeeds\CheckbookDataFeedsBudget;
use Drupal\checkbook_datafeeds\Common\FeedFactory;
use Drupal\checkbook_datafeeds\DefaultController;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

require_once(Drupal::service('extension.list.module')->getPath('checkbook_datafeeds') . "/src/Form/Budget/checkbook_datafeeds_budget.inc");
require_once(Drupal::service('extension.list.module')->getPath('checkbook_datafeeds') . "/src/Form/Contracts/checkbook_datafeeds_contracts.inc");
require_once(Drupal::service('extension.list.module')->getPath('checkbook_datafeeds') . "/src/Form/Payroll/checkbook_datafeeds_payroll.inc");
require_once(Drupal::service('extension.list.module')->getPath('checkbook_datafeeds') . "/src/Form/Revenue/checkbook_datafeeds_revenue.inc");
require_once(Drupal::service('extension.list.module')->getPath('checkbook_datafeeds') . "/src/Form/Spending/checkbook_datafeeds_spending.inc");

/**
 * Provides a form with three steps.
 *
 * @see \Drupal\Core\Form\FormBase
 */
class CheckbookDatafeedForm extends FormBase
{

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'checkbook_datafeeds_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $route = \Drupal::routeMatch()->getRouteObject();
    $route->setDefault('_title', 'Data Feeds');

    $form['datafeeds-rotator'] = array(
      '#type' => 'markup',
      '#markup' => '<div id="datafeeds-rotator" style="display: none;"><br><br></div>',
    );

    if ($form_state->has('page_num') && $form_state->get('page_num') == 2) {
      return $this->cbFormPageTwo($form, $form_state);
    }

    if ($form_state->has('page_num') && $form_state->get('page_num') == 3) {
      return $this->cbFormPageThree($form, $form_state);
    }

    if ($form_state->has('page_num') && $form_state->get('page_num') == 4) {
      if ($form_state->get('step') == 'queue_final') {
        $route->setDefault('_title', 'Thank You');
        return checkbook_datafeeds_queue_final($form, $form_state);
      }
    }

    $form_state->set('page_num', 1);
    $form_state->set('step', 'type');

    $form['type'] = [
      '#type' => 'radios',
      '#title' => $this->t('1. Select the Data Type:'),
      '#default_value' => $form_state->getValue('type', 'spending'),
      '#options' => [
        'budget' => $this->t('Budget'),
        'revenue' => $this->t('Revenue'),
        'spending' => $this->t('Spending'),
        'contracts' => $this->t('Contracts'),
        'payroll' => $this->t('Payroll'),
      ],
      '#attributes' => [
        'class' => ['datafeed-type'],
      ],
    ];
    $form['format'] = [
      '#type' => 'radios',
      '#title' => $this->t('2. Select the Format:'),
      '#default_value' => $form_state->getValue('format', 'csv'),
      '#options' => [
        'csv' => $this->t('CSV'),
        'xml' => $this->t('XML'),
      ],
      '#attributes' => [
        'class' => ['datafeed-format'],
      ],
    ];



    // Group submit handlers in an actions element with a key of "actions" so
    // that it gets styled correctly, and so that other modules may add actions
    // to the form. This is not required, but is convention.
    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['next'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Next'),
      // Custom submission handler for page 1.
      '#submit' => ['::cbFormMultistepFormNextSubmit'],
      // Custom validation handler for page 1.
      '#validate' => ['::cbFormMultistepFormNextValidate'],
      '#attributes' => array("onclick" => "
              jQuery(this).attr('disabled', true);
              jQuery('#checkbook-datafeeds-form').addClass('disable_me');
                jQuery('#datafeeds-rotator').css('display', 'block').addClass('loading_bigger_gif');
              jQuery(this).parents('form').submit();
        ")

    ];
    return $form;
  }

  /**
   * Form submitions handled by custom hadlers
   *
   * @param array $form
   *   Data Feeds wizard form array
   * @param array $form_state
   *   Data Feeds wizard form_state array
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {

  }

  /**
   * Provides custom validation handler for page 1.
   *
   * @param array $form
   *   An associative array containing the structure   the form.
   * @param FormStateInterface $form_state
   *   The current state of the form.
   */
  public function cbFormMultistepFormNextValidate(array &$form, FormStateInterface $form_state)
  {
    $birth_year = $form_state->getValue('birth_year');

    if ($birth_year != '' && ($birth_year < 1900 || $birth_year > 2000)) {
      // Set an error for the form element with a key of "birth_year".
      $form_state->setErrorByName('birth_year', $this->t('Enter a year between 1900 and 2000.'));
    }
  }

  /**
   * Provides custom submission handler for page 1.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param FormStateInterface $form_state
   *   The current state of the form.
   */
  public function cbFormMultistepFormNextSubmit(array &$form, FormStateInterface $form_state)
  {
    $current_step = &$form_state->get('step');
    $form_state->set(['step_information', $current_step, 'stored_values'], $form_state->getValues());
    $df_type = $form_state->getValue('type');
    switch ($df_type) {
      case 'budget':
        $current_step = 'budget';
        if (!empty($form_state->get(['step_information', 'budget', 'stored_values']))) {
          $form_state->setValue('values', $form_state->get(['step_information', 'budget', 'stored_values']));
        } else {
          $form_state->setValue('values', array());
        }
        $form_state->setRebuild();
        break;

      case 'contracts':
        $current_step = 'contracts';
        if (!empty($form_state->get(['step_information', 'contracts', 'stored_values']))) {
          $form_state->setValue('values', $form_state->get(['step_information', 'contracts', 'stored_values']));
        } else {
          $form_state->setValue('values', array());
        }
        $form_state->setRebuild();
        break;

      case 'payroll':
        $current_step = 'payroll';
        if (!empty($form_state->get(['step_information', 'payroll', 'stored_values']))) {
          $form_state->setValue('values', $form_state->get(['step_information', 'payroll', 'stored_values']));
        } else {
          $form_state->setValue('values', array());
        }
        $form_state->setRebuild();
        break;

      case 'revenue':
        $current_step = 'revenue';
        if (!empty($form_state->get(['step_information', 'revenue', 'stored_values']))) {
          $form_state->setValue('values', $form_state->get(['step_information', 'revenue', 'stored_values']));
        } else {
          $form_state->setValue('values', array());
        }
        $form_state->setRebuild();
        break;

      case 'spending':
        $current_step = 'spending';
        if (!empty($form_state->get(['step_information', 'spending', 'stored_values']))) {
          $form_state->set('values', $form_state->get(['step_information', 'spending', 'stored_values']));
        } else {
          $form_state->set('values', array());
        }
        $form_state->setRebuild();
        break;
    }

    //need all other domains below
    $form_state
      ->set('page_values', [
        // Keep only first step values to minimize stored data.
        'type' => $form_state->getValue('type'),
        'format' => $form_state->getValue('format'),
      ])
      ->set('page_num', 2)
      // Since we have logic in our buildForm() method, we have to tell the form
      // builder to rebuild the form. Otherwise, even though we set 'page_num'
      // to 2, the AJAX-rendered form will still show page 1.
      ->setRebuild();
  }

  /**
   * Ajax callback to update department and expense_category fields based on year.
   *
   * @param array $form
   *   Data Feeds wizard form array
   * @param array $form_state
   *   Data Feeds wizard form_state array
   *
   * @return array
   *   Array of ajax commands
   */
  function checkbook_datafeeds_year_ajax($form, FormStateInterface &$form_state)
  {

    $html = array($form['filter']['dept'], $form['filter']['expense_category']);
    $html = Drupal::service("renderer")->render($html);
    $commands[] = ajax_command_replace('#dynamic-fields', $html);
    return array('#type' => 'ajax', '#commands' => $commands);
  }

  /**
   * Builds the second step form (page 2).
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The render array defining the elements of the form.
   */

  public function cbFormPageTwo(array &$form, FormStateInterface $form_state)
  {
    //$form['#attached']['library'][] = 'checkbook_datafeeds/datafeeds.multi-select';
    // 1. retrieve the datafeed type
    $page_values = $form_state->get('page_values');

    // 2. Switch to the select type: budget, spending etc.
    switch ($page_values['type']) {
      case 'budget':
        $form = checkbook_datafeeds_budget($form, $form_state);
        return $form;;

      case 'contracts':
        return checkbook_datafeeds_contracts($form, $form_state);

      case 'payroll':
        return checkbook_datafeeds_payroll($form, $form_state);

      case 'revenue':
        return checkbook_datafeeds_revenue($form, $form_state);

      case 'spending':
        $form = checkbook_datafeeds_spending($form, $form_state);
        return $form;

      case 'queue_final':
        return checkbook_datafeeds_queue_final($form, $form_state);
    }
    $form_state->setRebuild();

    // 2. Populate the second page according to the type by calling the respective class of budget, payroll etc.
    $form['description'] = [
      '#type' => 'item',
      '#title' => $this->t($page_values['type'] . 'Data Feeds (page 2)'),
    ];

    $form['back'] = [
      '#type' => 'submit',
      '#value' => $this->t('Previous'),
      // Custom submission handler for 'Back' button.
      '#submit' => ['::cbFormPageTwoBack'],
      '#limit_validation_errors' => [],
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  public function cbFormPageThree(array &$form, FormStateInterface $form_state)
  {
    // 1. retrieve the datafeed type
    $page_values = $form_state->get('page_values');
    //2. Get submitted data
    $datafeeds = $form_state->getValues();
    // 3. Submission Confirmation .
    $data_domain_filter = 'datafeeds-' . $page_values['type'] . '-domain-filter';
    $data_source = $form_state->getValue($data_domain_filter) ?? Datasource::CITYWIDE;
    $feed = FeedFactory::getFeed($data_source, $page_values['type']);
    return $feed->process_confirmation($form, $form_state);
  }

  /**
   * Provides custom submission handler for 'Back' button (page 2).
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param FormStateInterface $form_state
   *   The current state of the form.
   */
  public function cbFormPageTwoBack(array &$form, FormStateInterface $form_state)
  {
    $form_state
      // Restore values for the first step.
      ->setValues($form_state->get('page_values'))
      ->set('page_num', 1)
      // Since we have logic in our buildForm() method, we have to tell the form
      // builder to rebuild the form. Otherwise, even though we set 'page_num'
      // to 1, the AJAX-rendered form will still show page 2.
      ->setRebuild();

  }

}
