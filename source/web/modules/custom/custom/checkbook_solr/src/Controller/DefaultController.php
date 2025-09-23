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


namespace Drupal\checkbook_solr\Controller;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Default controller for the checkbook_solr module.
 */

class DefaultController extends ControllerBase {

  /**
   * @param string $data_source
   * @param string $domain
   * @param string $facet
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function checkbook_print_solr_options_json($data_source = 'citywide', $domain = 'budget', $facet = '') {
    $filters = UrlHelper::filterQueryParameters( \Drupal::request()->query->all() );
    $data = checkbook_solr_options_labels($data_source, $domain, $facet, $filters);
    if (isset($filters['term'])) {
      if (!$data) {
        $data = [['label' => 'No Matches Found', 'value' => '']];
      }
    }
    return new JsonResponse($data);
  }

}
