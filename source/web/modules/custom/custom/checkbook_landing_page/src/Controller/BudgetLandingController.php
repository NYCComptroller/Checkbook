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

namespace Drupal\checkbook_landing_page\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;

class BudgetLandingController extends ControllerBase {
  /**
   * @param $params
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function nycBudget($params) {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/budget');
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1126);
    }
    return \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');
  }

  /**
   * @param $params
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function nychaBudget($params) {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/nycha_budget');
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1141);
    }
    return \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');
  }
}
