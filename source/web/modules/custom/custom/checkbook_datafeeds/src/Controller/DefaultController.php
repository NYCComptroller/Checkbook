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

namespace Drupal\checkbook_datafeeds\Controller;

use Drupal\checkbook_api\Queue\QueueUtil;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Controller\ControllerBase;

/**
 * Default controller for the checkbook_datafeeds module.
 */
class DefaultController extends ControllerBase {

  public function checkbook_datafeeds_data_feeds_page() {
    $page['instructions'] = [
      '#markup' => t('<div class="form-instructions"><h2>Select the Data Feeds</h2><p>Use this tool to design your own snapshots of NYC Financial Data</p><p>You can then save the resulting dataset as a file, or publish a dynamically updating XML feed of the data.  This feed will enable you to build your own web applications and widgets for your website.</p></div>'),
      '#prefix' => '<div class="data-feeds-sidebar">',
    ];
    $page['trackstatus'] = ['#markup' => '<div class="trackstatus">'];
    $page['tftitle'] = ['#markup' => '<h2>Track Status of Data Feed</h2>'];
    $page['tracking'] = \Drupal::formBuilder()->getForm('Drupal\checkbook_datafeeds\Form\CheckbookDatafeedsTrackingForm');
    $page['closediv'] = ['#markup' => '</div></div>'];
    $page['data-feed-wizard'] = \Drupal::formBuilder()->getForm('Drupal\checkbook_datafeeds\Form\CheckbookDatafeedForm');
    $page['rotator'] = ['#markup' => '<div id= "rotator"></div>'];
    return $page;
  }

  public function checkbook_datafeeds_api_page() {
    $entity_type = 'node';
    $node = \Drupal::entityTypeManager()->getStorage($entity_type)->load(299);
    $output =  \Drupal::service('renderer')->render(\Drupal::entityTypeManager()->getViewBuilder($entity_type)->view($node));
    $output = preg_replace('/<header>[\s\S]+?<\/header>/', '', $output);
    return array(
      '#markup' => $output,
    );
  }

  public function checkbook_datafeeds_tracking_results_page() {
    $page['instructions'] = [
      '#markup' => t('<div class="form-instructions"><p><h2>Select the Data Feeds</h2></p><p>Use this tool to design your own snapshots of NYC Financial Data</p><p>You can then save the resulting dataset as a file, or publish a dynamically updating XML feed of the data.  This feed will enable you to build your own web applications and widgets for your website.</p></div>'),
      '#prefix' => '<div class="data-feeds-sidebar">',
    ];
    $page['trackstatus'] = ['#markup' => '<div class="trackstatus">'];
    $page['tftitle'] = ['#markup' => '<h2>Track Status of Data Feed</h2>'];
    $page['tracking'] = \Drupal::formBuilder()->getForm('Drupal\checkbook_datafeeds\Form\CheckbookDatafeedsTrackingForm');
    $page['closediv'] = ['#markup' => '</div></div>'];
    $page['markupstart'] = ['#markup' => '<div id="data-feed-wizard">'];
    $page['tracking-status'] = \Drupal::formBuilder()->getForm('Drupal\checkbook_datafeeds\Form\CheckbookDatafeedsTrackingStatusForm');
    $page['markupend'] = ['#markup' => '</div>'];
    return $page;
  }

  /*
 * Function to hook into link to download the zip file for the data feeds from the server.
 * Used to track number of users that have downloaded the file.
 */
  /**
   * @param $token
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function checkbook_datafeeds_download_zip_ajax($token) {
    QueueUtil::incrementDownloadCount($token);

    $response = new AjaxResponse();
    $response->setStatusCode(200);
    return $response;
  }

}
