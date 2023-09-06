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

namespace Drupal\checkbook_api_ref\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Default controller for the checkbook_api_ref module.
 */
class DefaultController extends ControllerBase {

  /**
   * @param $code_list_name
   * @return Response
   */
  public function checkbook_api_load_ref_data($code_list_name) {
    $file_name = $code_list_name . '_code_list.csv';
    $file = \Drupal::state()->get('file_public_path','sites/default/files') . '/' . \Drupal::config('check_book')->get('data_feeds')['output_file_dir'] . '/'. \Drupal::config('check_book')->get('ref_data_dir') . '/' . $file_name;

    $response = new Response();
    $response->headers->set("Content-Type", "text/csv");
    $response->headers->set("Content-Disposition", "attachment; filename=$file_name");
    $response->headers->set("Pragma", "cache");
    $response->headers->set("Expires", "-1");

    if (is_file($file)) {
      $data = file_get_contents($file);
      $response->headers->set("Content-Length", strlen($data));
      $response->setContent($data);
    }
    else {
      echo "Data is not generated.. Please contact support team.";
    }
    return $response;
  }

  /**
   * @param $code_list_name
   * @return Response
   */
  public function checkbook_api_load_ref_data_excel($code_list_name) {
    $file_name = $code_list_name . '_code_list.xlsx';
    $file = \Drupal::state()->get('file_public_path','sites/default/files') . '/' . \Drupal::config('check_book')->get('data_feeds')['output_file_dir'] . '/'. \Drupal::config('check_book')->get('ref_data_dir') . '/' . $file_name;

    $response = new Response();
    $response->headers->set("Content-Type", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    $response->headers->set("Content-Disposition", "attachment; filename=$file_name");
    $response->headers->set("Pragma", "cache");
    $response->headers->set("Expires", "-1");

    if (is_file($file)) {
      $data = file_get_contents($file);
      $response->headers->set("Content-Length", strlen($data));
      $response->setContent($data);
    }
    else {
      echo "Data is not generated.. Please contact support team.";
    }
    return $response;
  }

}
