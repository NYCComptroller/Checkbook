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

use Drupal\checkbook_custom_breadcrumbs\SpendingBreadcrumbs;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\SpendingUtilities\SpendingUtil;
use Drupal\checkbook_transactions\Utilities\TransactionsUtil;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\widget\Controller\DefaultController;


class SpendingController extends ControllerBase {

  /**
   * @return string[]
   */
  public function spendingTransactions($params) {
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(706)) {
      $path = \Drupal::service('path_alias.manager')
        ->getPathByAlias('/spending/transactions');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1103);
      }

      $output = \Drupal::entityTypeManager()
        ->getViewBuilder('node')
        ->view($node, 'default');

      return $output;
    } else{
      $result = TransactionsUtil::spendingNodataTitle();

      $return['no_records_block'] = [
        '#markup' => $result . '<div id="no-records" class="clearfix">There are no spending transactions.</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function spendingDashboardMsTransactions($params) {
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(757)) {
      $path = \Drupal::service('path_alias.manager')
        ->getPathByAlias('/spending/transactions/dashboard/ms');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1104);
      }

      $output = \Drupal::entityTypeManager()
        ->getViewBuilder('node')
        ->view($node, 'default');

      return $output;
    } else{
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">There are no spending transactions.</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function spendingDashboardSSTransactions($params) {
    if (_checkbook_project_recordsExists(723)) {
      $path = \Drupal::service('path_alias.manager')
        ->getPathByAlias('/spending/transactions/dashboard/ss');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1105);
      }

      $output = \Drupal::entityTypeManager()
        ->getViewBuilder('node')
        ->view($node, 'default');

      return $output;
    } else{
      $result = TransactionsUtil::spendingNodataTitleSubvendors();
      $return['no_records_block'] = [
        '#markup' => $result . '<div id="no-records" class="clearfix">There are no spending transactions.</div>',
      ];
      return $return;
    }
  }

  public function spendingDashboardSPTransactions($params) {

    if (_checkbook_project_recordsExists(723)) {
      $path = \Drupal::service('path_alias.manager')
        ->getPathByAlias('/spending/transactions/dashboard/s*');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1105);
      }

      $output = \Drupal::entityTypeManager()
        ->getViewBuilder('node')
        ->view($node, 'default');

      return $output;
    } else{
      $result = TransactionsUtil::spendingNodataTitleSubvendors();
      $return['no_records_block'] = [
        '#markup' => $result . '<div id="no-records" class="clearfix">There are no spending transactions.</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function spendingDatasourceTransactions($params) {
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(652)) {
      $path = \Drupal::service('path_alias.manager')
        ->getPathByAlias('/spending/transactions/datasource/checkbook_oge');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1106);
      }

      $output = \Drupal::entityTypeManager()
        ->getViewBuilder('node')
        ->view($node, 'default');

      return $output;
    } else{
      $result = TransactionsUtil::spendingNodataTitle();

      $return['no_records_block'] = [
        '#markup' => $result . '<div id="no-records" class="clearfix">There are no spending transactions.</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function nychaSpendingTransactions($params) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/nycha_spending/transactions');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1107);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;

  }

  public function SpendingAdvancedSearch($params) {
    $request_params= \Drupal::request()->attributes->get('params');
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(766)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/spending/search/transactions');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1162);
      }
      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');
      return $output;
    }
    else{
      $isContNum = SpendingUtil::_checkbook_project_ma1_mma1_exist();
      if(isset($isContNum) && $isContNum){
        $page = str_contains(RequestUtilities::getCurrentPageUrl(), 'createalert') ? "Create Alert" : "Advanced Search";
        $message = "Spending information for MMA1 and MA1 Contracts can be viewed using the Contract ".$page." feature.";
      }
      else{
        $message = "There are no spending transactions";
      }
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">'.$message.'</div>',
      ];
      return $return;
    }
  }

  public function SpendingEDCAdvancedSearch($params) {
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(652)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/spending/search/transactions/datasource/checkbook_oge');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1164);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
    }
    else{
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">There are no spending transactions.</div>',
      ];
      return $return;
    }
  }

  public function NychSpendingAdvancedSearchTransactions($params)
  {
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(1012)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/nycha_spending/search/transactions');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1169);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');
      return $output;
    }
  else
    {
  $result = '<div class="contract-details-heading">
                  <div class="contract-id"> <h2 class="contract-title">' . SpendingBreadcrumbs::getNychaSpendingBreadcrumbTitle() . '</h2> </div></div>';
  $return['no_records_block'] = [
    '#markup' => $result.'<div id="no-records" class="clearfix">There are no spending transactions.</div>',
  ];
  return $return;
    }
  }

}
