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

namespace Drupal\checkbook_custom_breadcrumbs\Breadcrumb;

use Drupal\checkbook_custom_breadcrumbs\CustomBreadcrumbs;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Routing\RouteMatchInterface;

class CheckbookBreadcrumbBuilder implements BreadcrumbBuilderInterface {

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {
    // You can do additional checks here for the node type, etc.
    return !CustomBreadcrumbs::isHierarchical() || !empty(CustomBreadcrumbs::getLinks());
  }

  /**
   * {@inheritdoc}
   */
  public function build(RouteMatchInterface $route_match) {
    // Define a new object of type Breadcrumb
    $breadcrumb = new CheckbookBreadcrumb();

    // Get current page is hierarchical.
    $hierarchical = CustomBreadcrumbs::isHierarchical();

    // Set regular/historical breadcrumbs.
    $breadcrumb->setHistory(!$hierarchical);

    if ($hierarchical) {
      // Get links.
      $links = CustomBreadcrumbs::getLinks();

      // Set links.
      if ($links) {
        $breadcrumb->setLinks($links);
      }
    }

    // Cache control.
    $breadcrumb->addCacheContexts(['route', 'url.path']);
    $breadcrumb->mergeCacheMaxAge(0);

    // Return object of type breadcrumb.
    return $breadcrumb;
  }

}
