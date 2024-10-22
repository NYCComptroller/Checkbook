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

namespace Drupal\widget\Controller;

use Drupal\checkbook_infrastructure_layer\Constants\Common\PageType;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\RequestUtil;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Default controller for the widget module.
 */
class DefaultController extends ControllerBase {

  public function widget_ajax_get_node($node) {
    $node = widget_node_view($node);
  }

  public function _widget_node_view_page($key) {
    $id = RequestUtil::_getnodeid($key);
    RequestUtilities::resetUrl();
    $node = _widget_node_load_file($id);
    $node = widget_node_view($node);
    // Render the custom template.
    // TODO: Move template rendering to phpparser module.
    // $template = $node->widgetConfig->summaryView->template ?? $node->widgetConfig->template;
    if (isset($node->widgetConfig->template)) {
      $output = [
        '#theme' => $node->widgetConfig->template,
        '#node' => $node,
      ];
    }
    else {
      // display the node body content
      $output = $node->content['body'] ?? NULL;
    }

    if (!empty($output)) {
      if (PageType::getCurrent() == PageType::TRENDS_PAGE) {
        $output['#attached']['library'] = 'widget/FixedColumns';
      }

      $output += ['#prefix' => '', '#suffix' => ''];
      $output['#prefix'] = '<div id="node-widget-' . $id . '">' . $output['#prefix'];
      $output['#suffix'] .= '</div>';
    }
    elseif (is_null($output)) {
      throw new NotFoundHttpException();
    }

    return $output;
  }
}
