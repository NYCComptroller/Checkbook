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

namespace Drupal\widget\Path;

use Drupal;
use Drupal\Component\Utility\Xss;

class WidgetUrlpath{
  /**
   * @param null $widgetType
   * @param null $widgetPageUrl
   * @return mixed
   */
  public static function getRequestPathForWidget($nid,$widgetType = null): mixed
  {
    /**
     * TODO Implement the below in layout builder similar to visualizations
     */
    // Bottom and Top Slider should only use the current path url parameters and not expandBottomUrl
    // Quick Fix check the nodes and set path for processing.
    $bottom_slider_node = array(
      '1006',
      '482',
      '789',
      '484',
      '363',
      '632',
      '737',
      '608',
      '978',
      '472',
      'mwbe_contracts_bottom_slider',
      'mwbe_pending_contracts_bottom_slider');

    if (stripos(json_encode($bottom_slider_node),$nid) !== false) {
      $requestPath = Drupal::service('path.current')->getPath();
    }
    else {
      $requestPath = Drupal::service('path.current')->getPath();
      //Get the Request path from 'expandBottomContURL' param for bottom container
      if (Drupal::request()->query->has('expandBottomContURL') && $widgetType != 'highcharts') {
        $requestPath = Drupal::request()->query->get('expandBottomContURL');
      }

      //Get refURL for Gridview
      if (Drupal::request()->query->has('refURL')) {
        $requestPath = Drupal::request()->query->get('refURL');
      }

      if (Drupal::service('path.current')->getPath() == '/export/transactions') {
        $requestPath = Drupal::request()->query->get('q');
      }
    }
    return Xss::filter($requestPath);
  }
}
