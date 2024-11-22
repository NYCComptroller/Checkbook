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
namespace Drupal\checkbook_transactions\Utilities;

use Drupal\checkbook_custom_breadcrumbs\BudgetBreadcrumbs;
use Drupal\checkbook_custom_breadcrumbs\RevenueBreacrumbs;
use Drupal\checkbook_custom_breadcrumbs\SpendingBreadcrumbs;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\checkbook_project\PayrollUtilities\PayrollUtil;
use Drupal\checkbook_project\SpendingUtilities\SpendingUtil;
use Drupal\checkbook_project\WidgetUtilities\NodeSummaryUtil;
use Drupal\checkbook_project\WidgetUtilities\WidgetUtil;

class TransactionsUtil
{
  public static function narrowDownSearch() {
    print "<div class='pane-content' style='display: block;'><div class='narrow-down-title'>Narrow Down Your Search</div></div>";
  }

  //title for /budget/transactions/budget_transactions
  public static function budgetTransactionsTitle() {
    $sumnid = RequestUtilities::getTransactionsParams('smnid');
    $dtmnid = RequestUtilities::getTransactionsParams('dtsmnid');
    if (_checkbook_project_recordsExists(277)) {
      if (isset($sumnid)) {
        $customTitle = NodeSummaryUtil::getInitNodeSummaryContent($sumnid);
        print $customTitle;
      } else if (isset($dtmnid)) {
        $customTitle = NodeSummaryUtil::getInitNodeSummaryTitle($dtmnid);
        print "<h2 class='contract-title js-breadcrumb-title' class='title'>" . $customTitle . "</h2>";
      } else {
        $customTitle = BudgetBreadcrumbs::getBudgetBreadcrumbTitle();
        print "<h2 class='contract-title js-breadcrumb-title' class='title'>" . $customTitle . "</h2>";
      }
    }
    else{
      if(isset($sumnid) || isset($dtmnid)){
        $customTitle = NodeSummaryUtil::getInitNodeSummaryTitle();
      }	else {
        $customTitle = BudgetBreadcrumbs::getBudgetBreadcrumbTitle();
      }
      echo '<h2 class="contract-title js-breadcrumb-title" class="title">'.$customTitle.'</h2>';
    }
  }

  //visibility rule for facet 845 on page /contract/transactions/edc_contracts (node 1121)
  public static function visibilityRule845() {
    $docType = \Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities::get('doctype');
    $docTypes= explode('~', $docType);
    if( count(array_intersect(array('MA1', 'MMA1'), $docTypes)) > 0){
      return TRUE;
    }
    return FALSE;
  }

  public static function advancedBudgetTransactionsTitle() {
    $customTitle = 'Expense Budget Transactions';
    print "<h2 class='contract-title js-breadcrumb-title' class='title'>{$customTitle}</h2>";
  }

  //title for /subcontract/transactions
  public static function subcontractTransactionsTitle() {
    $dashboard = RequestUtilities::getRequestParamValue('dashboard');
    if($dashboard == 'ss')
    {
      $title = "Subcontract Status by Prime Contract ID";
    }
    if($dashboard == 'ms' || $dashboard == 'sp')
    {
      $title = "M/WBE Subcontract Status by Prime Contract ID";
    }
    echo  "<div class='contract-id'><h2 class='contract-title js-breadcrumb-title' class='title'>{$title}</h2></div>";
  }

  //title for /revenue/transactions/revenue_transactions
  public static function revenueTransactionsTitle() {
    $sumnid = RequestUtilities::getTransactionsParams('smnid');
    $dtmnid = RequestUtilities::getTransactionsParams('dtsmnid');
    if (_checkbook_project_recordsExists(277)) {
      if (isset($sumnid)) {
        $customTitle = NodeSummaryUtil::getInitNodeSummaryContent($sumnid);
        print $customTitle;
      } else if (isset($dtmnid)) {
        $customTitle = NodeSummaryUtil::getInitNodeSummaryTitle($dtmnid);
        print "<h2 class='contract-title js-breadcrumb-title' class='title'>{$customTitle}</h2>";
      } else {
        //@ToDo: need to add back CustomBreadcrumbs once that is migrated to d9
        $customTitle = RevenueBreacrumbs::getRevenueBreadcrumbTitle();
        print "<h2 class='contract-title js-breadcrumb-title' class='title'>{$customTitle}</h2>";
      }
    }
    else{
      if(isset($sumnid) || isset($dtmnid)){
        $customTitle = NodeSummaryUtil::getInitNodeSummaryTitle();
        print "<h2 class='contract-title js-breadcrumb-title' class='title'>{$customTitle}</h2>";
      }	else {
        $customTitle = RevenueBreacrumbs::getRevenueBreadcrumbTitle();
        print "<h2 class='contract-title js-breadcrumb-title' class='title'>{$customTitle}</h2>";
      }
    }
  }

  public static function payrollNYCTransactionsTitle() {
    $customTitle = NodeSummaryUtil::getInitNodeSummaryTitle();
    $payrollType = RequestUtilities::_getRequestParamValueBottomURL('payroll_type');
    if(isset($payrollType)){
      $customTitle =  PayrollUtil::getPayrollTitlebyType($payrollType);
    }
    else if(!isset($customTitle) ){
      $customTitle = "New York City Payroll Transactions";
    }

    print "<h2 class='page-payroll contract-title js-breadcrumb-title' class='title'>{$customTitle}</h2>";
  }

  public static function payrollByMonthNYCTransactionsTitle() {
    $customTitle = NodeSummaryUtil::getInitNodeSummaryTitle();

    if(!isset($customTitle)){
      $smnid = RequestUtilities::getTransactionsParams('smnid');
      if($smnid == '491' || $smnid == '492'){
        $customTitle = "Overtime Payments by Month Transactions";
      }else{
        $customTitle = "Gross Pay by Month Transactions";
      }
    }

    /*$monthDetails = CheckbookDateUtil::getMonthDetails(RequestUtilities::getRequestParamValue('month'));
    if(isset($monthDetails)){
      $customTitle .=  (" in the Month of ". $monthDetails[0]['month_name']) ;
    }*/

    print "<h2 class='page-payroll contract-title js-breadcrumb-title' class='title'>{$customTitle}</h2>";
  }

  public static function payrollAgencyByMonthTransactionsTitle() {
    $customTitle = NodeSummaryUtil::getInitNodeSummaryTitle();

    if(!isset($customTitle)){
      $smnid = RequestUtilities::getTransactionsParams('smnid');
      if($smnid == '491' || $smnid == '492'){
        $customTitle = "Overtime Payments by Month Transactions";
      }else{
        $customTitle = "Gross Pay by Month Transactions";
      }
    }

    print "<h2 class='page-payroll contract-title js-breadcrumb-title' class='title'>{$customTitle}</h2>";
  }

  public static function payrollEmployeeTransactionsTitle() {
    $customTitle = NodeSummaryUtil::getInitNodeSummaryTitle();

    if(!isset($customTitle)){
      $customTitle = "Individual Employee Payroll Transactions";
    }

    print "<h2 class='page-payroll contract-title js-breadcrumb-title' class='title'>{$customTitle}</h2>";
  }

  //title for /spending/transactions page and variants
  public static function spendingPageTitle($nid) {
    $title = SpendingBreadcrumbs::getSpendingTransactionTitle();
    $sumnid = RequestUtilities::getTransactionsParams('smnid');
    $dtsmnid = RequestUtilities::getTransactionsParams('dtsmnid');
    $month = RequestUtilities::getTransactionsParams('month');
    if($month > 0){
      $amount = WidgetUtil::getWidgetTemplate($nid);
      echo $amount;
    }
    else {
      if (_checkbook_project_recordsExists(757) || _checkbook_project_recordsExists(723)) {
        if (isset($sumnid)) {
          echo $title;
        } else if (isset($dtsmnid)) {
          $amount = WidgetUtil::getWidgetTemplate($nid);
          echo "<div class='contract-id'><h2 class='contract-title js-breadcrumb-title' class='title'>{$title}</h2></div>";
          echo $amount;
        } else {
          echo '<h1 class="padding-x-10px">' . SpendingUtil::getSpendingTransactionsTitle() . '</h1>';
        }
      }

      else{
        if (isset($sumnid) && !isset($month)) {
          echo '<div class="contract-id"><h2 class="contract-title js-breadcrumb-title" class="title">' . NodeSummaryUtil::getInitNodeSummaryTitle($sumnid) . '</h2></div>';
        } else if (isset($dtsmnid)) {
          echo '<div class="contract-id"><h2 class="contract-title js-breadcrumb-title" class="title">' . NodeSummaryUtil::getInitNodeSummaryTitle($dtsmnid) . '</h2></div>';
        }
      }
    }
  }

  /**
   * @throws \Exception
   */
  public static function spendingOGEPageTitle($widgetid) {
    $sumnid = RequestUtilities::getTransactionsParams('smnid');
    $dtmnid = RequestUtilities::getTransactionsParams('dtsmnid');
    if (_checkbook_project_recordsExists(652)){
    if (isset($sumnid)) {
      echo NodeSummaryUtil::getInitNodeSummaryContent();
    } else if (isset($dtmnid)) {
      $dtTitle = NodeSummaryUtil::getInitNodeSummaryTitle($dtmnid);
      $amount = WidgetUtil::getWidgetTemplate($widgetid);
      echo "<div class='contract-id'><h2 class='contract-title js-breadcrumb-title' class='title'>{$dtTitle}</h2></div>";
      echo $amount;
    } else {
      echo '<h1 class="padding-x-10px">' . SpendingUtil::getSpendingTransactionsTitle() . '</h1>';
    }
  }
  else {
      if(isset($sumnid)) {
        echo '<div class="contract-id"><h2 class="contract-title js-breadcrumb-title" class="title">'.NodeSummaryUtil::getInitNodeSummaryTitle($sumnid).'</h2></div>';
      }
      else if(isset($dtmnid)) {
        echo '<div class="contract-id"><h2 class="contract-title js-breadcrumb-title" class="title">'.NodeSummaryUtil::getInitNodeSummaryTitle($dtmnid).'</h2></div>';
      }
    }

  }

  //title for contract/spending/transactions page and variants
  public static function contractSpendingPageTitle($recordExistID = null) {
    $sumnid = RequestUtilities::get('smnid');
    if( !isset($sumnid) ){
      echo '<h2 class="title">Spending Transactions</h2>';
    }	else{
      if (isset($recordExistID) && !(_checkbook_project_recordsExists($recordExistID))) {
        $customTitle = NodeSummaryUtil::getInitNodeSummaryTemplateTitle($sumnid);
        $content = '<h2 class="title no-spending-transactions">'.$customTitle.'</h2>';
      } else {
        $content =  NodeSummaryUtil::getInitNodeSummaryContent($sumnid);
      }

      if (empty($content)) {
        echo '<h2 class="title">Spending Transactions</h2>';
      } else {
        echo $content;
      }
    }
  }

  //title for /payroll/search/transactionsFY (node 1166 Transactions page)
  //title for /payroll/search/transactionsCY (node 1167 Transactions page)
  public static function payrollSearchTransactionsTitle() {
    $datasource = '';
    if (RequestUtilities::get('datasource') == DataSource::NYCHA) {
      $datasource = 'NYCHA ';
    }
    $customTitle = $datasource . 'Payroll Transactions';
    print "<h1 class='padding-x-10px'>{$customTitle}</h1>";
  }

  public static function contractSpendingNodataTitle() {
    $smnid = RequestUtilities::getTransactionsParams('smnid');
    if( !isset($smnid) ){
      return '<h1 class="title no-spending-transactions">Spending Transactions</h1>';
    }
    else{
      $customTitle = NodeSummaryUtil::getInitNodeSummaryTemplateTitle($smnid);
      return '<h1 class="title no-spending-transactions">'.$customTitle.'</h1>';
    }

  }

  public static function contractNodataTitle() {
    $contactStatus = RequestUtilities::getTransactionsParams('contstatus');
    $contactStatusLabel = 'active';
    if($contactStatus == 'R'){
      $contactStatusLabel = 'registered';
    }
    $contractcat = RequestUtilities::getTransactionsParams('contcat');
    if($contractcat == 'all'){
      $contractcatlabel = '';
    }else{
      $contractcatlabel = $contractcat;
    }
    $message = "There are no {$contactStatusLabel} {$contractcatlabel} contracts transactions.";
    return $message;
  }

  public static function spendingNodataTitle() {
    $smnid =  RequestUtilities::getTransactionsParams('smnid');
    if( !isset($smnid) ){
      return '<h1 id="spending-title" class="title no-spending-transactions">Spending Transactions</h1>';
    }
    else{
      if (Datasource::isOGE()) {
        $customTitle = SpendingUtil::getTransactionPageTitle()($smnid);
      } else {
        $customTitle = NodeSummaryUtil::getInitNodeSummaryTemplateTitle($smnid);
      }
      return '<h1 id="spending-title" class="title no-spending-transactions">'.$customTitle.'</h1>';
    }
  }

  public static function spendingNodataTitleSubvendors() {
    $smnid = RequestUtilities::getTransactionsParams('smnid');
    $dtsmnid = RequestUtilities::getTransactionsParams('dtsmnid');
    if(isset($smnid)) {
      return '<h2 class="title no-spending-transactions">'.NodeSummaryUtil::getInitNodeSummaryTitle($smnid).'</h2>';
    }
    else if(isset($dtsmnid)) {
      return '<h2 class="title no-spending-transactions">'.NodeSummaryUtil::getInitNodeSummaryTitle($dtsmnid).'</h2>';
    }
  }

  public static function nychaBudgetNodataMessage() {
    $message = "There are no budget transactions.";

    if(CheckbookDateUtil::_getYearValueFromID(RequestUtilities::getTransactionsParams('year')) <= 2018){
      $message = "Since only one year of data is available no comparison can be performed.";
    }
    return $message;
  }

  public static function budgetNodataMessage() {
    $message = "There are no budget transactions.";

    if(CheckbookDateUtil::_getYearValueFromID(RequestUtilities::getTransactionsParams('year')) <= 2011){
      $message = "Since only one year of data is available no comparison can be performed.";
    }
    return '<div id="no-records" class="clearfix">'.$message.'</div>';
  }

  public static function revenueNodataMessage() {
    $message = "There are no revenue transactions.";
    if(RequestUtilities::getTransactionsParams('year') < CheckbookDateUtil::getCurrentFiscalYearId()){
      $message = "Since only one year of data is available no comparison can be performed.";
    }
    return '<div id="no-records" class="clearfix">'.$message.'</div>';
  }
}
