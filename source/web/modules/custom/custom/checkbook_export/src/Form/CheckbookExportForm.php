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

namespace Drupal\checkbook_export\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class CheckbookExportForm extends FormBase {
  /**
   * Getter method for Form ID
   *
   * @return string
   *    The unique ID of the form defined by this class.
   */
  public function getFormId() {
    return 'checkbook_export_form';
  }

  /**
   * build the Checkbook Export Form
   *
   * @param array $form
   *    Default form array structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *    Object containing current form state.
   *
   * @return array
   *    The render array defining the elements of the form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $request_params = \Drupal::request()->query;
    $maxPages = $request_params->get('maxPages');
    $totalRecords = $request_params->get('iRecordsTotal');
    $displayRecords = $request_params->get('iRecordsDisplay');

    $form['#myvars']['maxPages'] = $maxPages;
    $form['#myvars']['totalRecords'] = $totalRecords;
    $form['#myvars']['displayRecords'] = $displayRecords;

    //var_dump(\Drupal::request());
    //die();

    if ($totalRecords > 0) {
      $form['export-frmt-csv'] = [
        '#type' => 'checkbox',
        '#disabled' => TRUE,
        '#return_value' => 'csv',
        '#default_value' => 'csv',
        '#title' => $this->t('CSV (Comma-separated values)'),
      ];

      $form['dc'] = [
        '#type' => 'radios',
        '#title' => 'Data Selection',
        '#options' => [
          'cp' => $this->t('Current Page'),
          'all' => $this->t('All Pages'),
          'range' => $this->t('Pages Range '),
        ],
        '#default_value' => 'all',
      ];

      $form['rangefrom'] = [
        '#type' => 'number',
        '#title' => 'Range From',
        '#min' => 1,
      ];

      $form['rangeto'] = [
        '#type' => 'number',
        '#title' => 'Range To',
        '#min' => 1,
      ];

      $form['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Submit'),
      ];
    } else {
      $form['description'] = [
        '#type' => 'item',
        '#markup' => "<span class='export' exportid='939'>Export</span>",
      ];
    }

    $form['#attached'] = array(
      'library' => array('checkbook_export/export.transactions','jquery_ui_dialog/dialog'),
      'drupalSettings' => array(),
    );

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    //@todo
    //var_dump($form_state);
    //die();
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    //@todo
  }
}
