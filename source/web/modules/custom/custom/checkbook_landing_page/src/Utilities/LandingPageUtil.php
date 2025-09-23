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

namespace Drupal\checkbook_landing_page\Utilities;

use Drupal\checkbook_project\EdcUtilities\EdcUtilities;

class LandingPageUtil
{
  /**
   * @param string $paramName
   * @return bool
   */
  public static function hasQueryParam(string $paramName="expandBottomContURL"): bool
  {
    $param = $_REQUEST[$paramName] ?? null;
    if (empty($param)) {
      return false;
    } else {
      return true;
    }
  }

  /**
   * @param string $paramName
   * @return mixed
   */
  public  static function getQueryParam(string $paramName="expandBottomContURL"): mixed
  {
    return $_REQUEST[$paramName] ?? null;
  }

  /**
   * @param $url
   * @return string
   */
  public static function getURLOutput($url): string
  {
    return '';
  }

  /**
   * @param $nid
   * @return mixed
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function getTransactionsNodeOutputByNodeId($nid): mixed
  {
    $entity_type = 'node';
    $view_mode = 'default';

    $builder = \Drupal::entityTypeManager()->getViewBuilder($entity_type);
    $storage = \Drupal::entityTypeManager()->getStorage($entity_type);
    $node = $storage->load($nid);
    $build = $builder->view($node, $view_mode);
    return \Drupal::service('renderer')->render($build);
  }

  /**
   * Gets the HTML for links with View as Prime Vendor / Vies as Agency
   * Used in Layout Builder by below landing pages
   * - NYC Contracts Landing
   * - Spending Landing
   */
  public static function getToggleVendorLinks() {
    $link = EdcUtilities::_get_toggle_view_links();
    if (count($link) > 0) {
      if ($link[0] == 'agency') {
        print "<div class='grid-row'><div class='toggleVendorContainer grid-col flex-12'><a class='toggleVendor' href='" . $link[1] . "'>View as Prime Vendor</a><span class='toggle'>&nbsp;/&nbsp;</span><span class='toggleAgency'>Viewing as Agency</span></div></div>";
      }
      else {
        if ($link[0] == 'vendor') {
          print "<div class='grid-row'><div class='toggleVendorContainer grid-col flex-12'><span class='toggleVendor'>Viewing as Prime Vendor</span><span class='toggle'>&nbsp;/&nbsp;</span><a class='toggleAgency' href='" . $link[1] . "'>View as Agency</a></div></div>";
        }
      }
    }
  }
}
