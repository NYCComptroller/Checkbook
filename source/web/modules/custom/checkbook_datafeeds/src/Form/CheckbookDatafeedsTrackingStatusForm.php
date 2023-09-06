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

use Drupal\checkbook_api\API\CheckBookAPI;
use Drupal\checkbook_log\LogHelper;
use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Exception;

/**
 * Provides a form with three steps.
 *
 * @see \Drupal\Core\Form\FormBase
 */
class CheckbookDatafeedsTrackingStatusForm extends FormBase {

  public function getFormId() {
    return 'checkbook_datafeeds_tracking_status';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form_state->setCached(FALSE);
    try {
      $results = CheckBookAPI::getRequestDetailsByToken(Html::escape($_REQUEST['code']));
    } catch (Exception $e) {
      LogHelper::log_error($e->getMessage());
    }
    $output = array();
    $output['download_feeds'] = array(
      '#markup' => '<h2 id="edit-description">Download Data</h2>',
    );
    $output['#attributes'] = array(
      'class' => array(
        'confirmation-page',
        'data-feeds-wizard',
      ),
    );
    if ($results) {

      $date_expires = strtotime($results['file_generated_time'] . ' + 1 week');
      $now = time();
      $expired = $now > $date_expires;
      if ($expired) {
        $msg = Html::escape($_REQUEST['tn']) == 1 ? 'tracking number' : 'download link';
        $output['status-fieldset']['available_date'] = array(
          '#markup' => 'Your ' . $msg . ' has expired.  Please create a new data feeds request.',
          '#prefix' => '<p>',
          '#suffix' => '</p>',
        );
        $output['actions']['#type'] = 'actions';
        $output['actions']['cancel'] = array(
          '#type' => 'submit',
          '#value' => $this->t('Cancel'),
          '#button_type' => 'primary',
          '#submit' => array([$this, 'checkbook_datafeeds_tracking_form_cancel']),
        );
        return $output;
      }

      $usercriteria = $results['user_criteria'];
      $output['columns-fieldset'] = array(
        '#title' => t('Selected Columns'),
        '#type' => 'fieldset',
      );
      $output['columns-fieldset']['columns'] = array(
        '#items' => $usercriteria['Columns'],
        '#theme' => 'item_list',
        '#prefix' => '<div class="search-columns">',
        '#suffix' => '</div>',
        '#attributes' => [
          'class' => ['add-list-reset'],
        ],
      );
      $output['search-criteria-fieldset'] = array(
        '#title' => t('Search Criteria'),
        '#type' => 'fieldset',
      );
      $output['search-criteria-fieldset']['search-criteria'] = array(
        '#theme' => 'user_criteria',
        '#usercriteria' => $results['user_criteria']['Formatted'],
        '#prefix' => '<div class="search-criteria">',
        '#suffix' => '</div>',
      );
      $output['record-count-fieldset'] = array(
        '#type' => 'fieldset',
      );
      $output['record-count-fieldset']['record-count'] = array(
        '#markup' => '<p>This request has ' . number_format($usercriteria['Record Count']) . ' records.</p><p><strong>Tracking Number: ' . Html::escape($_REQUEST['code']) . '</strong></p>',
        '#prefix' => '<div class="record-count">',
        '#suffix' => '</div>',
      );
      switch ($results['status']) {
        case 0:
          $output['status-fieldset'] = array(
            '#type' => 'fieldset',
          );
          $output['status-fieldset']['in-queue'] = array(
            '#markup' => '<strong>Status:</strong><br />File in Queue - Your data feed is in the queue to be generated. Please check back later.',
            '#prefix' => '<div class="feed-status">',
            '#suffix' => '</div>',
          );
          break;

        case 1:
          $output['status-fieldset'] = array(
            '#type' => 'fieldset',
          );
          $output['status-fieldset']['in-progress'] = array(
            '#markup' => '<strong>Status:</strong><br />Your file is currently being generated. It will be ready for download soon.',
            '#prefix' => '<div class="feed-status">',
            '#suffix' => '</div>',
          );
          break;

        case 2:
          $download = checkbook_datafeeds_file_download_page($results);
          $output = array_merge($output, $download);
          break;

        case 3:
          $output['status-fieldset'] = array(
            '#type' => 'fieldset',
          );
          $output['status-fieldset']['failed'] = array(
            '#markup' => '<strong>Status:</strong><br/>File generation failed.',
            '#prefix' => '<div class="feed-status">',
            '#suffix' => '</div>',
          );
          break;
      }
    } else {
      $output['error'] = array(
        '#markup' => 'No data was found for the entered tracking number. Please submit a valid tracking number.',
        '#prefix' => '<div class="feed-error">',
        '#suffix' => '</div>',
      );
    }
    $output['actions']['#type'] = 'actions';
    $output['actions']['cancel'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Cancel'),
      '#button_type' => 'primary',
      '#submit' => array([$this, 'checkbook_datafeeds_tracking_form_cancel']),
    );
    return $output;
  }

  /**
   * Tracking form cancel button submit handler.
   *
   * @param array $form
   *   Tracking form
   * @param array $form_state
   *   Tracking form form_state
   */
  public function checkbook_datafeeds_tracking_form_cancel(array &$form, FormStateInterface $form_state)
  {
    $url = Url::fromRoute('checkbook_datafeeds');
    $form_state->setRedirectUrl($url);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement submitForm() method.
  }

}
