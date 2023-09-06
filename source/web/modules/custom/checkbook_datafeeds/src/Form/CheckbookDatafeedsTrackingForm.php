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

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a form with three steps.
 *
 * @see \Drupal\Core\Form\FormBase
 */
class CheckbookDatafeedsTrackingForm extends FormBase {

  public function getFormId() {
    return 'checkbook_datafeeds_tracking_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = array();

    // Include the JavaScript for our pseudo-trigger.
    $form['#attached']['library'][] = 'checkbook_datafeeds/datafeeds.datafeeds';
    $form['text'] = array(
      '#markup' => '<p>To track status of data feed requests, please enter your tracking number:</p>'
    );
    $form['tracking_number'] = array(
      '#title' => t('Tracking Number:'),
      '#type' => 'textfield',
      '#size' => 30,
      '#maxlength' => 15
    );
    /*$form['go'] = array(
      '#value' => t('Go'),
      '#type' => 'submit',
      '#submit' => array('checkbook_datafeeds_tracking_form_submit')
    );*/
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Go'),
      '#button_type' => 'primary',
    );
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $code = $form_state->getValue('tracking_number');
    $path = '/track-data-feed';
    $path_params = [
      'code' => $code,
      'tn' => '1'
    ];
    $url = Url::fromUserInput($path, ['query' => $path_params]);
    $form_state->setRedirectUrl($url);
    /*$form_state['redirect'] = array(
      'track-data-feed',
      array('query' => array('code' => $code, 'tn' => '1'))
    );*/
  }

  /**
   * @param $form
   * @param $form_state
   */
  /*public function checkbook_datafeeds_tracking_form_submit(array &$form, FormStateInterface $form_state)
  {
    $code = trim($form_state['values']['tracking_number']);
    $form_state['redirect'] = array(
      'track-data-feed',
      array('query' => array('code' => $code, 'tn' => '1'))
    );
  }*/

}
