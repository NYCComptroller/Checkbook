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

namespace Drupal\checkbook_alerts\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class CheckbookAlertsSettingsForm extends FormBase
{

  public function getFormId() {
    return 'checkbook_alerts_settings_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['checkbook_create_alert_pref'] = array(
      '#type' => 'markup',
      '#markup' => "<div id='dialog'>
                        <div id='errorMessages'></div>
                            <div>
                                <span class='bold'>Alert Settings</span>
                            </div>",
      '#allowed_tags' => ['div', 'button', 'h1', 'h2', 'h3', 'span'],
    );
    $form['alert_label'] = array(
      '#type' => 'textfield',
      '#title' => t('Description'),
      '#size' => 25,
      '#maxlength' => 32,
      '#required' => true,
      '#description' => 'This is how the alert will be described in the email text.',
    );
    $form['alert_email'] = array(
      '#type' => 'textfield',
      '#title' => t('Email'),
      '#size' => 25,
      '#required' => true,
      '#maxlength' => 50,
    );
    $form['alert_minimum_results'] = array(
      '#type' => 'textfield',
      '#title' => t('Minimum Additional Results'),
      '#size' => 5,
      '#maxlength' => 5,
      '#description' => 'Checkbook will not notify you until this many new results are returned.',
      '#default_value' => t('10')
    );
    $form['alert_minimum_days'] = array(
      '#type' => 'select',
      '#title' => t('Alert Frequency'),
      '#default_value' => t('Daily'),
      '#options' => ['1'=>'Daily','7'=>'Weekly','30'=>'Monthy','92'=>'Quarterly']
    );
    $form['alert_end'] = array(
      '#type' => 'date',
      '#title' => t('Expiration Date'),
      '#date_format' => 'Y-m-d',
      '#date_year_range' => "'-" . (date("Y") - 1900) . ":+" . (2500 - date("Y")) . "'",
      '#description' => 'This is the date the alert will expire.  The default is one year.'
    );
    $form['checkbook_create_alert_sufix'] = array(
      '#type' => 'markup',
      '#markup' => "</div>",
      '#allowed_tags' => ['div', 'button', 'h1', 'h2', 'h3'],
    );
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement submitForm() method.
  }

}
