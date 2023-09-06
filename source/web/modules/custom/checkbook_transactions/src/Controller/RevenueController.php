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

namespace Drupal\checkbook_transactions\Controller;

use Drupal\checkbook_custom_breadcrumbs\RevenueBreacrumbs;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_transactions\Utilities\TransactionsUtil;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;


class RevenueController extends ControllerBase {

  /**
   * @return string[]
   */
  public function revenueTransactions($params) {

      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/revenue/transactions/revenue_transactions');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1091);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;

  }

  /**
   * @return string[]
   */
  public function nychaRevenueTransactions($params) {
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(1051)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/nycha_revenue/transactions');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1092);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
    }
    else{
      $result = "<h2 id='contract-title' class='title'>NYCHA Revenue Transactions</h2>";
      if ($result) {
        $return['title_block'] = $result;
      }
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">There are no revenue transactions.</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function revenueAgencyDetails($params) {
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(595)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/revenue/agency_details');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1127);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
    }
    else{
      $result = RevenueBreacrumbs::getRevenueBreadcrumbTitle();
      if ($result) {
        $return['title_block'] = $result;
      }
      $message = TransactionsUtil::revenueNodataMessage();
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">'.$message.'</div>',
      ];
      return $return;
    }
  }
  /**
   * @return string[]
   */
  public function revenueRevcatDetails($params) {
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(596)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/revenue/revcat_details');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1128);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
    }
    else{
      $result = RevenueBreacrumbs::getRevenueBreadcrumbTitle();
      if ($result) {
        $return['title_block'] = $result;
      }
      $message = TransactionsUtil::revenueNodataMessage();
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">'.$message.'</div>',
      ];
      return $return;
    }
  }
  /**
   * @return string[]
   */
  public function revenueFundSrcDetails($params) {
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(597)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/revenue/fundsrc_details');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1129);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
    }
    else{
      $result = RevenueBreacrumbs::getRevenueBreadcrumbTitle();
      if ($result) {
        $return['title_block'] = $result;
      }
      $message = TransactionsUtil::revenueNodataMessage();
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">'.$message.'</div>',
      ];
      return $return;
    }

  }

  /**
   * @return string[]
   */
  public function AdvancedSearchRevenueTransactions($params) {
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(280)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/revenue/transactions');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1125);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
    }
    else{
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">There are no revenue transactions.</div>',
      ];
      return $return;
    }

  }

  /**
   * @return string[]
   */
  public function NychaRevenueAdvancedSearchTransactions($params) {
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(1051)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/nycha_revenue/search/transactions');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1130);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
    }
    else{
      $result = "<h2 id='contract-title' class='title'>NYCHA Revenue Transactions</h2>";
      if ($result) {
        $return['title_block'] = $result;
      }
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">There are no revenue transactions.</div>',
      ];
      return $return;
    }
  }
}
