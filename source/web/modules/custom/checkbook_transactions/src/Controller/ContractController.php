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

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_transactions\Utilities\TransactionsUtil;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\widget\Controller\DefaultController;


class ContractController extends ControllerBase
{

  /**
   * @return string[]
   */
  public function subcontractTransactions($params)
  {
    $request_params= \Drupal::request()->attributes->get('params');
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(932)) {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/subcontract/transactions');
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1109);
    }
    $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');
    return $output;
    } else {
      $request_params =  '429:' . $request_params;
      $contentController = new DefaultController();
      $result = $contentController->_widget_node_view_page($request_params);
      if ($result) {
        $return['title_block'] = $result;
      }
      $message = TransactionsUtil::contractNodataTitle();
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">'.$message.'</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function citywideExpenseContractTransactions($params)
  {
    $request_params= \Drupal::request()->attributes->get('params');
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(939)) {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/contract/transactions/contcat/expense');
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1110);
    }

    $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

    return $output;
    } else {
      $request_params =  '429:' . $request_params;
      $contentController = new DefaultController();
      $result = $contentController->_widget_node_view_page($request_params);
      if ($result) {
        $return['title_block'] = $result;
      }
      $message = TransactionsUtil::contractNodataTitle();
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">'.$message.'</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function citywideRevenueContractTransactions($params)
  {
    $request_params= \Drupal::request()->attributes->get('params');
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(667)) {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/contract/transactions/contcat/revenue');
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1111);
    }

    $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

    return $output;
    } else {
      $request_params =  '429:' . $request_params;
      $contentController = new DefaultController();
      $result = $contentController->_widget_node_view_page($request_params);
      if ($result) {
        $return['title_block'] = $result;
      }
      $message = TransactionsUtil::contractNodataTitle();
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">'.$message.'</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function mwbeExpenseContractTransactions($params)
  {
    $request_params= \Drupal::request()->attributes->get('params');
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(939)) {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/contract/transactions/contcat/expense/dashboard/*');
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1112);
    }

    $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

    return $output;
    } else {
      $request_params =  '429:' . $request_params;
      $contentController = new DefaultController();
      $result = $contentController->_widget_node_view_page($request_params);
      if ($result) {
        $return['title_block'] = $result;
      }
      $message = TransactionsUtil::contractNodataTitle();
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">'.$message.'</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function mwbeRevenueContractTransactions($params)
  {
    $request_params= \Drupal::request()->attributes->get('params');
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(667)) {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/contract/transactions/contcat/revenue/dashboard/*');
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1113);
    }

    $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

    return $output;
    } else {
      $request_params =  '429:' . $request_params;
      $contentController = new DefaultController();
      $result = $contentController->_widget_node_view_page($request_params);
      if ($result) {
        $return['title_block'] = $result;
      }
      $message = TransactionsUtil::contractNodataTitle();
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">'.$message.'</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function nychaContractsTransactions($params)
  {
    $request_params= \Drupal::request()->attributes->get('params');
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(979)) {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/nycha_contracts/transactions');
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1114);
    }

    $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

    return $output;
    } else {
      $request_params =  '991:' . $request_params;
      $contentController = new DefaultController();
      $result = $contentController->_widget_node_view_page($request_params);
      if ($result) {
        $return['title_block'] = $result;
      }
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">There are no contracts transactions.</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function contractSpendingTransactions($params)
  {
    $request_params= \Drupal::request()->attributes->get('params');
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(707)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/contract/spending/transactions');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1116);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
    }
    else{
      $result = TransactionsUtil::contractSpendingNodataTitle();
      $return['no_records_block'] = [
        '#markup' => $result.'<div id="no-records" class="clearfix">There are no spending transactions.</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function subvendorContractSpendingTransactions($params)
  {
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(724)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/contract/spending/transactions/dashboard');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1119);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
    }
    else{
      $result = TransactionsUtil::contractSpendingNodataTitle();
      $return['no_records_block'] = [
      '#markup' => $result.'<div id="no-records" class="clearfix">There are no spending transactions.</div>',
      ];
    return $return;
  }
  }

  /**
   * @return string[]
   */
  public function ogeContractSpendingTransactions($params)
  {
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(477)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/contract/spending/transactions/datasource/checkbook_oge');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1122);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
    }
  else{
    $result = TransactionsUtil::contractSpendingNodataTitle();
    $return['no_records_block'] = [
      '#markup' => $result.'<div id="no-records" class="clearfix">There are no spending transactions.</div>',
    ];
    return $return;
  }
  }

  /**
   * @return string[]
   */
  public function citywidePendingContractTransactions($params)
  {
    $request_params= \Drupal::request()->attributes->get('params');
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(714)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/contract/transactions/contstatus/P/');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1117);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
    }
    else{
      $request_params =  '474:' . $request_params;
      $contentController = new DefaultController();
      $result = $contentController->_widget_node_view_page($request_params);
      if ($result) {
        $return['title_block'] = $result;
      }
      if(RequestUtilities::getTransactionsParams('contcat') == 'revenue'){
        $message = "There are no pending revenue contract transactions.";
      }else{
        $message = "There are no pending expense contract transactions.";
      }

      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">'.$message.'</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function mwbePendingContractTransactions($params)
  {
    $request_params= \Drupal::request()->attributes->get('params');
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(714)) {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/contract/transactions/mwbe_pending_contracts');
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1118);
    }

    $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

    return $output;
    } else {
      $request_params =  '474:' . $request_params;
      $contentController = new DefaultController();
      $result = $contentController->_widget_node_view_page($request_params);
      if ($result) {
        $return['title_block'] = $result;
      }
      if(RequestUtilities::getTransactionsParams('contcat') == 'revenue'){
        $message = "There are no pending revenue contract transactions.";
      }else{
        $message = "There are no pending expense contract transactions.";
      }

      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">'.$message.'</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function ContractsPendingTransactionsMiniPanel($params)
  {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/pending_contract_transactions');
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1173);
    }

    $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

    return $output;
  }

  /**
   * @return string[]
   */
  public function nychaContractAssocReleases($params)
  {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/nycha_contract_assoc_releases');
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1120);
    }

    $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

    return $output;

  }

  public function nychaContractDetails($params)
  {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/nycha_contract_details');
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1152);
    }

    $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

    return $output;
  }


  /**
   * @return string[]
   */
  public function EdcContractTransactions($params)
  {

    $request_params= \Drupal::request()->attributes->get('params');
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(634)) {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/contract/transactions/edc_contracts');
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1121);
    }

    $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

    return $output;
    } else {
      $request_params =  '429:' . $request_params;
      $contentController = new DefaultController();
      $result = $contentController->_widget_node_view_page($request_params);
      if ($result) {
        $return['title_block'] = $result;
      }
      $message = TransactionsUtil::contractNodataTitle();
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">'.$message.'</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function contractDetails($params)
  {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/contract_details');
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1124);
    }

    $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

    return $output;
  }

  /**
   * @return string[]
   */
  public function citywideContractsAdvancedSearchExpense($params) {
    $request_params= \Drupal::request()->attributes->get('params');
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(939)) {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/contract/search/transactions/contcat/expense');
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1131);
    }
    $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');
    return $output;
    } else {
      $request_params =  '429:' . $request_params;
      $contentController = new DefaultController();
      $result = $contentController->_widget_node_view_page($request_params);
      if ($result) {
        $return['title_block'] = $result;
      }
      $message = TransactionsUtil::contractNodataTitle();
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">'.$message.'</div>',
      ];
      return $return;
    }
  }
  /**
   * @return string[]
   */
  public function citywideContractsAdvancedSearchStatusAll($params) {
      $request_params= \Drupal::request()->attributes->get('params');
      RequestUtilities::resetUrl();
      if (_checkbook_project_recordsExists(939)) {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/contract/search/transactions/contcat/all');
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1131);
    }
    $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');
    return $output;
      } else {
        $request_params =  '429:' . $request_params;
        $contentController = new DefaultController();
        $result = $contentController->_widget_node_view_page($request_params);
        if ($result) {
          $return['title_block'] = $result;
        }
        $message = TransactionsUtil::contractNodataTitle();
        $return['no_records_block'] = [
          '#markup' => '<div id="no-records" class="clearfix">'.$message.'</div>',
        ];
        return $return;
      }
  }

  /**
   * @return string[]
   */
  public function citywideContractsAdvancedSearchRevenue($params) {
        $request_params= \Drupal::request()->attributes->get('params');
        RequestUtilities::resetUrl();
        if (_checkbook_project_recordsExists(667)) {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/contract/search/transactions/contcat/revenue');
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1132);
    }
    $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');
    return $output;
        } else {

          $request_params =  '429:' . $request_params;
          $contentController = new DefaultController();
          $result = $contentController->_widget_node_view_page($request_params);
          if ($result) {
            $return['title_block'] = $result;
          }
          $message = TransactionsUtil::contractNodataTitle();
          $return['no_records_block'] = [
            '#markup' => '<div id="no-records" class="clearfix">'.$message.'</div>',
          ];
          return $return;
        }
  }

  /**
   * @return string[]
   */
  public function citywideContractsAdvancedSearchExpenseAllYears($params) {
          $request_params= \Drupal::request()->attributes->get('params');
          RequestUtilities::resetUrl();
          if (_checkbook_project_recordsExists(939)) {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/contract/search/all/transactions/contcat/expense');
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1133);
    }
    $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');
    return $output;
          } else {
            $request_params =  '429:' . $request_params;
            $contentController = new DefaultController();
            $result = $contentController->_widget_node_view_page($request_params);
            if ($result) {
              $return['title_block'] = $result;
            }
            $message = TransactionsUtil::contractNodataTitle();
            $return['no_records_block'] = [
              '#markup' => '<div id="no-records" class="clearfix">'.$message.'</div>',
            ];
            return $return;
          }
  }

  /**
   * @return string[]
   */
  public function citywideContractsAdvancedSearchRevenueAllYears($params) {
            $request_params= \Drupal::request()->attributes->get('params');
            RequestUtilities::resetUrl();
            if (_checkbook_project_recordsExists(688)) {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/contract/search/all/transactions/contcat/revenue');
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1134);
    }
    $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');
    return $output;
            } else {
              $request_params =  '429:' . $request_params;
              $contentController = new DefaultController();
              $result = $contentController->_widget_node_view_page($request_params);
              if ($result) {
                $return['title_block'] = $result;
              }
              $message = TransactionsUtil::contractNodataTitle();
              $return['no_records_block'] = [
                '#markup' => '<div id="no-records" class="clearfix">'.$message.'</div>',
              ];
              return $return;
            }
  }

  /**
   * @return string[]
   */
  public function EdcContractsExpenseAdvancedSearch($params) {
    $request_params= \Drupal::request()->attributes->get('params');
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(634)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/contract/search/transactions/datasource/checkbook_oge');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1135);
      }
      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');
      return $output;
    } else {
      $request_params =  '429:' . $request_params;
      $contentController = new DefaultController();
      $result = $contentController->_widget_node_view_page($request_params);
      $result['#prefix'] = '<div class="node-widget-429">';
      $result['#suffix'] = '</div>';
      if ($result) {
        $return['title_block'] = $result;
      }
      $contactStatus = RequestUtilities::get('contstatus');
      $contactStatusLabel = 'active';
      if($contactStatus == 'R'){
        $contactStatusLabel = 'registered';
      }
      $no_records_message = 'There are no ' . $contactStatusLabel . ' expense contracts transactions.';
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">' . $no_records_message . '</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function EdcContractsExpenseAdvancedSearchAllYears($params) {
              $request_params= \Drupal::request()->attributes->get('params');
              RequestUtilities::resetUrl();
              if (_checkbook_project_recordsExists(634)) {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/contract/all/transactions/datasource/checkbook_oge');
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1136);
    }
    $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');
    return $output;
              } else {
                $request_params =  '429:' . $request_params;
                $contentController = new DefaultController();
                $result = $contentController->_widget_node_view_page($request_params);
                if ($result) {
                  $return['title_block'] = $result;
                }
                $return['no_records_block'] = [
                  '#markup' => '<div id="no-records" class="clearfix">There are no active expense contracts transactions.</div>',
                ];
                return $return;
              }
  }

  /**
   * @return string[]
   */
  public function ContractsPendingAdvancedSearch($params) {
    $request_params= \Drupal::request()->attributes->get('params');
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(714)) {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('contract/search/transactions/contstatus/P');
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1137);
    }
    $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');
    return $output;
    } else {
      $request_params =  '474:' . $request_params;
      $contentController = new DefaultController();
      $result = $contentController->_widget_node_view_page($request_params);
      if ($result) {
        $return['title_block'] = $result;
      }
      if(RequestUtilities::getTransactionsParams('contcat') == 'revenue'){
        $message = "There are no pending revenue contract transactions.";
      }else{
        $message = "There are no pending expense contract transactions.";
      }
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">'.$message.'</div>',
      ];
      return $return;
    }
  }
  /**
   * @return string[]
   */
  public function ContractsNychaAdvancedSearch($params) {
    $request_params= \Drupal::request()->attributes->get('params');
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(979)) {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/nycha_contracts/search/transactions');
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1165);
    }
    $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');
    return $output;
    } else {
      $request_params =  '991:' . $request_params;
      $contentController = new DefaultController();
      $result = $contentController->_widget_node_view_page($request_params);
      if ($result) {
        $return['title_block'] = $result;
      }
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">There are no contracts transactions.</div>',
      ];
      return $return;
    }
  }
  /**
   * @return string[]
   */
  public function ContractsNychaAllAdvancedSearch($params) {
    $request_params= \Drupal::request()->attributes->get('params');
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(979)) {
    $path = \Drupal::service('path_alias.manager')->getPathByAlias('/nycha_contracts/all/transactions');
    if (preg_match('/node\/(\d+)/', $path, $matches)) {
      $node = Node::load($matches[1]);
    }
    if (!$node) {
      //if node not found by alias, then load harcoded value
      $node = \Drupal::entityTypeManager()->getStorage('node')->load(1168);
    }
    $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');
    return $output;
    } else {
      $request_params =  '991:' . $request_params;
      $contentController = new DefaultController();
      $result = $contentController->_widget_node_view_page($request_params);
      if ($result) {
        $return['title_block'] = $result;
      }
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">There are no contracts transactions.</div>',
      ];
      return $return;
    }
  }

}
