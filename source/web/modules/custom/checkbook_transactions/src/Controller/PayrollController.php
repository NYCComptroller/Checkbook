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

use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\widget\Controller\DefaultController;


class PayrollController extends ControllerBase {

  /**
   * @return string[]
   */
  public function payrollTransactions($params) {
    $request_params= \Drupal::request()->attributes->get('params');
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(317)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/payroll/transactions');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1093);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
    }
    else{
      $contentController = new DefaultController();
      $result = $contentController->_widget_node_view_page($request_params);
      if ($result) {
        $return['title_block'] = $result;
      }
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">There are no payroll transactions.</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function payrollTitleTransactions($params) {
    $request_params= \Drupal::request()->attributes->get('params');
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(886)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/payroll/payroll_title/transactions');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1098);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
    }
    else{
      $contentController = new DefaultController();
      $result = $contentController->_widget_node_view_page($request_params);
      if ($result) {
        $return['title_block'] = $result;
      }
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">There are no payroll transactions.</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function payrollMonthlyTransactions($params) {
    $request_params= \Drupal::request()->attributes->get('params');
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(317)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/payroll/monthly/transactions');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1099);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
    }
    else{
      $contentController = new DefaultController();
      $result = $contentController->_widget_node_view_page($request_params);
      if ($result) {
        $return['title_block'] = $result;
      }
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">There are no payroll transactions.</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function payrollAgencywideMonthlyTransactions($params) {
    $request_params= \Drupal::request()->attributes->get('params');
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(317)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/payroll/agencywide/monthly/transactions');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1100);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
    }
    else{
      $contentController = new DefaultController();
      $result = $contentController->_widget_node_view_page($request_params);
      if ($result) {
        $return['title_block'] = $result;
      }
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">There are no payroll transactions.</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function payrollAgencywideTransactions($params) {
    $request_params= \Drupal::request()->attributes->get('params');
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(317)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/payroll/agencywide/transactions');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1101);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
    }
    else{
      $contentController = new DefaultController();
      $result = $contentController->_widget_node_view_page($request_params);
      if ($result) {
        $return['title_block'] = $result;
      }
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">There are no payroll transactions.</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function payrollEmployeeFYTransactions($params) {
    $request_params= \Drupal::request()->attributes->get('params');
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(310)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/payroll/employee/transactions/fy');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1102);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
    }
    else
    {
      $contentController = new DefaultController();
      $result = $contentController->_widget_node_view_page($request_params);
      if ($result) {
        $return['title_block'] = $result;
      }
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">There are no payroll transactions.</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function payrollEmployeeCYTransactions($params) {
    $request_params= \Drupal::request()->attributes->get('params');
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(330)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/payroll/employee/transactions/cy');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1108);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
    }
    {
      $contentController = new DefaultController();
      $result = $contentController->_widget_node_view_page($request_params);
      if ($result) {
        $return['title_block'] = $result;
      }
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">There are no payroll transactions.</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function PayrollFyAdvancedSearch($params) {
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(337)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/payroll/search/transactionsFY');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1166);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
    }
    {
      $datasource = '';
      if (RequestUtilities::get('datasource') == DataSource::NYCHA) {
        $datasource = 'NYCHA ';
      }
      $customTitle = $datasource . 'Payroll Transactions';
      $result = "<h2 class='contract-title' class='title'>".$customTitle."</h2>";
      $return['no_records_block'] = [
        '#markup' => $result.'<div id="no-records" class="clearfix">There are no payroll transactions.</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function PayrollCyAdvancedSearch($params) {
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(336)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/payroll/search/transactionsCY');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1167);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
    }
    else{
      $datasource = '';
      if (RequestUtilities::get('datasource') == DataSource::NYCHA) {
        $datasource = 'NYCHA ';
      }
      $customTitle = $datasource . 'Payroll Transactions';
      $result = "<h2 class='contract-title' class='title'>".$customTitle."</h2>";
      $return['no_records_block'] = [
        '#markup' => $result.'<div id="no-records" class="clearfix">There are no payroll transactions.</div>',
      ];
      return $return;
    }
  }
}
