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

namespace Drupal\widget_highcharts\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;

/**
 * Default controller for the checkbook_project module.
 */
class DefaultController extends ControllerBase {
  public function widget_highcharts_grid_view_widget_popup($key) {
    if ( \Drupal::request()->query->get('refURL') !== NULL) {
      $refUrl = Xss::filter(htmlspecialchars(\Drupal::request()->query->get('refURL'), ENT_QUOTES));
    }
    \Drupal::request()->query->set('q', '/'.$refUrl);
    $node = _widget_node_load_file($key);
    if (isset($node->widgetConfig->gridConfig->adjustWidgetConfig)) {
      eval($node->widgetConfig->gridConfig->adjustWidgetConfig);
    }
    if (isset($node->widgetConfig->getTotalDataCount)) {
      $node->widgetConfig->getTotalDataCount = TRUE;
    }
    if (isset($node->widgetConfig->displayType)) {
      $node->widgetConfig->displayType = 'gridview';
    }
    widget_node_view($node);
      return $node->content['body'];
  }
}
