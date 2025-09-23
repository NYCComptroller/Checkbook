<?php
namespace Drupal\checkbook_transactions\Controller;

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\BudgetUtilities\NychaBudgetUtil;
use Drupal\checkbook_transactions\Utilities\TransactionsUtil;
use Drupal\Core\Controller\ControllerBase;


class BudgetController extends ControllerBase {

  /**
   * @return string[]
   */
  public function budgetTransactions($params) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/budget/transactions/budget_transactions');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = \Drupal\node\Entity\Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1088);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
  }

  /**
   * @return string[]
   */
  public function advancedBudgetTransactions($params) {
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(277)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/budget/transactions');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = \Drupal\node\Entity\Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1089);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
    }
    else{
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">There are no budget transactions.</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function nychaBudgetTransactions($params) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/nycha_budget/transactions/');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = \Drupal\node\Entity\Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1090);
      }
      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');
      return $output;
  }

  /**
   * @return string[]
   */
  public function nychaBudgetFundsrcDetailsTransactions($params) {
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(1047)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/nycha_budget/fundsrc_details/');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = \Drupal\node\Entity\Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1094);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
    }
    else{
      $customTitle = NychaBudgetUtil::getTransactionsTitle();
      $result = "<h2 class='contract-title' class='title'>{$customTitle}</h2>";
      if ($result) {
        $return['title_block'] = $result;
      }
      $message = TransactionsUtil::nychaBudgetNodataMessage();
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">There are no budget transactions.</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function nychaBudgetRespcenterDetailsTransactions($params) {
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(1048)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/nycha_budget/respcenter_details/');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = \Drupal\node\Entity\Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1095);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
    }
    else{
      $customTitle = NychaBudgetUtil::getTransactionsTitle();
      $result = "<h2 class='contract-title' class='title'>{$customTitle}</h2>";
      if ($result) {
        $return['title_block'] = $result;
      }
      $message = TransactionsUtil::nychaBudgetNodataMessage();
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">'.$message.'</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function nychaBudgetProgramDetailsTransactions($params) {
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(1049)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/nycha_budget/program_details/');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = \Drupal\node\Entity\Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1096);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
    }
    else{
      $customTitle = NychaBudgetUtil::getTransactionsTitle();
      $result = "<h2 class='contract-title' class='title'>{$customTitle}</h2>";
      if ($result) {
        $return['title_block'] = $result;
      }
      $message = TransactionsUtil::nychaBudgetNodataMessage();
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">'.$message.'</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function nychaBudgetProjectDetailsTransactions($params) {
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(1050)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/nycha_budget/project_details/');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = \Drupal\node\Entity\Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1097);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
    }
    else{
      $customTitle = NychaBudgetUtil::getTransactionsTitle();
      $result = "<h2 class='contract-title' class='title'>{$customTitle}</h2>";
      if ($result) {
        $return['title_block'] = $result;
      }
      $message = TransactionsUtil::nychaBudgetNodataMessage();
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">'.$message.'</div>',
      ];
      return $return;
    }
  }

  /**
   * @return string[]
   */
  public function nychaBudgetSearchTransactions($params) {
    RequestUtilities::resetUrl();
    if (_checkbook_project_recordsExists(1034)) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/nycha_budget/search/transactions');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = \Drupal\node\Entity\Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1123);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
    }
    else{
      $return['no_records_block'] = [
        '#markup' => '<div id="no-records" class="clearfix">There are no Expense Budget transactions.</div>',
      ];
      return $return;
    }
  }

  public function BudgetAgencyPercentTransactions($params) {

      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/budget_agency_perecent_difference_transactions/budget/agency_details');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = \Drupal\node\Entity\Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1170);
      }
      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');
      return $output;

  }
  public function BudgetDeptPercentTransactions($params) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/department_budget_details/budget/dept_details');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = \Drupal\node\Entity\Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1172);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
  }
  public function BudgetExpCatPercentTransactions($params) {
      $path = \Drupal::service('path_alias.manager')->getPathByAlias('/expense_category_budget_details/budget/expcategory_details');
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        $node = \Drupal\node\Entity\Node::load($matches[1]);
      }
      if (!$node) {
        //if node not found by alias, then load harcoded value
        $node = \Drupal::entityTypeManager()->getStorage('node')->load(1171);
      }

      $output = \Drupal::entityTypeManager()->getViewBuilder('node')->view($node, 'default');

      return $output;
  }


}
