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

namespace Drupal\checkbook_faceted_search\Controller;

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\RequestUtil;
use Drupal\Core\Controller\ControllerBase;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Default controller for the checkbook_faceted_search module.
 */
class DefaultController extends ControllerBase {

  /**
   * @param $node
   * @throws \Exception
   */
  public function _checkbook_faceted_search_node_ajax($nid) {
    $id = RequestUtil::_getnodeid($nid);
    RequestUtilities::resetUrl();
    $node = _widget_node_load_file($id);
    widget_config($node);
    widget_prepare($node);
    widget_invoke($node, 'widget_prepare');
    widget_data($node);
    $build = [
      '#theme' => 'individual_filter',
      '#node' => $node
    ];
    return new Response(\Drupal::service('renderer')->render($build));
  }

  public function _checkbook_faceted_search_node_autocomplete($node) {
    $id = RequestUtil::_getnodeid($node);
    RequestUtilities::resetUrl();
    $node = _widget_node_load_file($id);
    widget_config($node);
    widget_prepare($node);
    widget_invoke($node, 'widget_prepare');
    $node->widgetConfig->limit = 10;
    widget_data($node);
    $node->data = _checkbook_faceted_search_update_data($node);

    if(is_array($node->data['unchecked'])) {
      $output = [];
      foreach ($node->data['unchecked'] as $row) {
        if (isset($row[1])) {
          $output[] = [
            "value" => urlencode(html_entity_decode($row[0], ENT_QUOTES)),
            'label' => html_entity_decode($row[1], ENT_QUOTES) . " (" . $row[2] . ")",
          ];
        }
      }
      return new JsonResponse($output);
    }
    else{
        return new JsonResponse(["No Matches Found"]);
    }
  }

  /**
   * @param $nid
   *
   * @throws Exception
   */
  function _checkbook_faceted_search_node_pagination($nid) {
    $id = RequestUtil::_getnodeid($nid);
    RequestUtilities::resetUrl();
    $node = _widget_node_load_file($id);
    widget_config($node);
    widget_prepare($node);
    widget_invoke($node, 'widget_prepare');
    widget_data($node);
    return [
      '#theme' => 'pagination',
      '#node' => $node
    ];
  }

}
