<?php
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
 * Callback to display queue tracking form in a block
 *
 * @return array
 * @see checkbook_datafeeds_block_view()
 *
 */
function checkbook_datafeeds_tracking_form()
{
  $form = array();

  // Include the JavaScript for our pseudo-trigger.
  $form['#attached'] = array(
    'js' => array(
      'data' => drupal_get_path('module', 'checkbook_datafeeds') . '/js/datafeeds.js',
    )
  );
  $form['text'] = array(
    '#markup' => '<p>To track status of data feed requests, please enter your tracking number:</p>'
  );
  $form['tracking_number'] = array(
    '#title' => t('Tracking Number:'),
    '#type' => 'textfield',
    '#size' => 30,
    '#maxlength' => 15
  );
  $form['go'] = array(
    '#value' => t('Go'),
    '#type' => 'submit',
    '#submit' => array('checkbook_datafeeds_tracking_form_submit')
  );
  return $form;
}

/**
 * @param $form
 * @param $form_state
 */
function checkbook_datafeeds_tracking_form_submit($form, &$form_state)
{
  $code = trim($form_state['values']['tracking_number']);
  $form_state['redirect'] = array(
    'track-data-feed',
    array('query' => array('code' => $code, 'tn' => '1'))
  );
}

/**
 * Page callback for API queue tracking page.
 *
 * @return array
 *   Renderable array to display results of entire track status page
 * @see checkbook_datafeeds_menu()
 *
 */
function checkbook_datafeeds_tracking_results_page()
{
  $page['instructions'] = array(
    '#markup' => t('<div class="form-instructions"><p><h2>Select the Data Feeds</h2></p><p>Use this tool to design your own snapshots of NYC Financial Data</p><p>You can then save the resulting dataset as a file, or publish a dynamically updating XML feed of the data.  This feed will enable you to build your own web applications and widgets for your website.</p></div>'),
    '#prefix' => '<div class="data-feeds-sidebar">',
  );
  $page['trackstatus'] = array(
    '#markup' => '<div class="trackstatus">',
  );
  $page['tftitle'] = array(
    '#markup' => '<h2>Track Status of Data Feed</h2>',
  );
  $page['tracking'] = drupal_get_form('checkbook_datafeeds_tracking_form');
  $page['closediv'] = array(
    '#markup' => '</div></div>',
  );
  $page['markupstart'] = array(
    '#markup' => '<div id="data-feed-wizard">',
  );
  $page['tracking-status'] = drupal_get_form('checkbook_datafeeds_tracking_status');
  $page['markupend'] = array(
    '#markup' => '</div>',
  );
  return $page;
}

/**
 * Track status page callback.
 *
 * @param array $form
 *   Track status form array
 * @param array $form_state
 *   Track status form form_state array
 *
 * @return array
 *   Renderable array of results section of track status page
 * @see checkbook_datafeeds_tracking_results_page()
 *
 */
function checkbook_datafeeds_tracking_status($form, &$form_state)
{
  $form_state['cache'] = FALSE;
  try {
    $results = CheckBookAPI::getRequestDetailsByToken(check_plain($_REQUEST['code']));
  } catch (Exception $e) {
    log_error($e->getMessage());
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
      $msg = check_plain($_REQUEST['tn']) == 1 ? 'tracking number' : 'download link';
      $output['status-fieldset']['available_date'] = array(
        '#markup' => 'Your ' . $msg . ' has expired.  Please create a new data feeds request.',
        '#prefix' => '<p>',
        '#suffix' => '</p>',
      );
      $output['cancel'] = array(
        '#type' => 'submit',
        '#value' => t('Cancel'),
        '#submit' => array('checkbook_datafeeds_tracking_form_cancel'),
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
      '#markup' => '<p>This request has ' . number_format($usercriteria['Record Count']) . ' records.</p><p><strong>Tracking Number: ' . check_plain($_REQUEST['code']) . '</strong></p>',
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
  $output['cancel'] = array(
    '#type' => 'submit',
    '#value' => t('Cancel'),
    '#submit' => array('checkbook_datafeeds_tracking_form_cancel'),
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
function checkbook_datafeeds_tracking_form_cancel($form, &$form_state)
{
  drupal_goto('data-feeds');
}
