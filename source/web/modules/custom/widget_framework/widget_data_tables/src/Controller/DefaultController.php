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

namespace Drupal\widget_data_tables\Controller;

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\RequestUtil;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;


/**
 * Default controller for the widget_data_tables module.
 */
class DefaultController extends ControllerBase {

  /**
   * supports ajax data callback for data table widget type..simpleAjax
   * @param $node
   */
  public function _widget_data_tables_ajaxdata($node) {
    $id = RequestUtil::_getnodeid($node);
    RequestUtilities::resetUrl();
    $node = _widget_node_load_file($id);
     widget_data_tables_get_data($node);
    _widget_add_padding_data($node);
    $result = _widget_data_tables_ajaxdata_json($node);
    $response = new Response();
    $response->setContent(json_encode($result));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

  /**
   * supports ajax data callback for data table widget type..dataTableList...
   * @param $node
   */
  public function _widget_data_tables_list_ajaxdata($node) {
    $id = RequestUtil::_getnodeid($node);
    RequestUtilities::resetUrl();
    $node = _widget_node_load_file($id);
    $node = widget_data_tables_get_data($node);
    widget_data_tables_widget_view($node);
    _widget_add_padding_data($node);
    $result = _widget_data_tables_ajaxdata_json($node);
    // Return the data in json format after preprocessing donot use JsonResponse (does not encode html tags correctly)
    $response = new Response();
    $response->setContent(json_encode($result));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }
}
