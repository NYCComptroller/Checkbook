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

namespace Drupal\checkbook_transactions\PathProcessor;

use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Symfony\Component\HttpFoundation\Request;

class CheckbookTransactionsPathProcessor implements InboundPathProcessorInterface {

  public function processInbound($path, Request $request) {
    // Budget paths.
    if (strpos($path, '/budget/transactions/budget_transactions/') === 0) {
      $params = preg_replace('|^\/budget\/transactions\/budget_transactions\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/budget/transactions/budget_transactions/$params";
    }
    elseif (strpos($path, '/budget/transactions/') === 0) {
      $params = preg_replace('|^\/budget\/transactions\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/budget/transactions/$params";
    }
    if (strpos($path, '/nycha_budget/transactions/') === 0) {
      preg_replace('|^\/nycha_budget\/transactions\/|', '', $path);
      $budgettype = RequestUtilities::getTransactionsParams('budgettype');
      if ($budgettype == 'committed') {
        $params = preg_replace('|\/budgettype\/committed|', '', $path);
        $params = str_replace('/', ':', $params);
        return "/nycha_budget/transactions/budgettype/committed/$params";
      }
      elseif ($budgettype == 'remaining') {
        $params = preg_replace('|\/budgettype\/remaining|', '', $path);
        $params = str_replace('/', ':', $params);
        return "/nycha_budget/transactions/budgettype/remaining/$params";
      }
      else {
        $params = str_replace('/', ':', $path);
        return "/nycha_budget/transactions/$params";
      }
    }
    if (strpos($path, '/nycha_budget/fundsrc_details/') === 0) {
      $params = preg_replace('|^\/nycha_budget\/fundsrc_details\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/nycha_budget/fundsrc_details/$params";
    }
    if (strpos($path, '/nycha_budget/project_details/') === 0) {
      $params = preg_replace('|^\/nycha_budget\/project_details\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/nycha_budget/project_details/$params";
    }
    if (strpos($path, '/nycha_budget/program_details/') === 0) {
      $params = preg_replace('|^\/nycha_budget\/program_details\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/nycha_budget/program_details/$params";
    }
    if (strpos($path, '/nycha_budget/respcenter_details/') === 0) {
      $params = preg_replace('|^\/nycha_budget\/respcenter_details\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/nycha_budget/respcenter_details/$params";
    }
    if (strpos($path, '/nycha_budget/search/transactions') === 0) {
      $params = preg_replace('|^\/nycha_budget\/search\/transactions\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/nycha_budget/search/transactions/$params";
    }

    // Revenue paths.
    if (strpos($path, '/revenue/transactions/revenue_transactions/') === 0) {
      $params = preg_replace('|^\/revenue\/transactions\/revenue_transactions\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/revenue/transactions/revenue_transactions/$params";
    }
    if (strpos($path, '/nycha_revenue/transactions/') === 0) {
      $params = preg_replace('|^\/nycha_revenue\/transactions\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/nycha_revenue/transactions/$params";
    }
    if (strpos($path, '/revenue/agency_details/') === 0) {
      $params = preg_replace('|^\/revenue\/agency_details\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/revenue/agency_details/$params";
    }
    if (strpos($path, '/revenue/revcat_details/') === 0) {
      $params = preg_replace('|^\/revenue\/revcat_details\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/revenue/revcat_details/$params";
    }
    if (strpos($path, '/revenue/fundsrc_details/') === 0) {
      $params = preg_replace('|^\/revenue\/fundsrc_details\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/revenue/fundsrc_details/$params";
    }
    if (strpos($path, '/revenue/transactions/') === 0) {
      $params = preg_replace('|^\/revenue\/transactions\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/revenue/transactions/$params";
    }
    if (strpos($path, '/nycha_revenue/search/transactions/') === 0) {
      $params = preg_replace('|^\/nycha_revenue\/\/search\/transactions\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/nycha_revenue/search/transactions/$params";
    }

    // Contracts Advanced Search Transactions
    if (strpos($path, '/contract/search/transactions') === 0) {
      preg_replace('|^\/contract\/search\/transactions\/|', '', $path);
      $contSatus = RequestUtilities::get('contstatus');
      $contCategory = RequestUtilities::get('contcat');
      $datasource = RequestUtilities::get('datasource');

      if ($contSatus == 'P' && $datasource != 'checkbook_oge') {
        // if (_checkbook_project_recordsExists(714)) {
        $params = preg_replace('|\/contstatus\/P|', '', $path);
        $params = str_replace('/', ':', $params);
        return "/contract/search/transactions/contstatus/P/$params";
        // }
      }
      elseif ($contSatus != 'P' && $contCategory == 'revenue' && $datasource != 'checkbook_oge'){
        // if (_checkbook_project_recordsExists(667)) {
        $params = preg_replace('|\/contcat\/revenue|', '', $path);
        $params = str_replace('/', ':', $params);
        return "/contract/search/transactions/contcat/revenue/$params";
        // }
      }
      elseif ($contSatus != 'P' && $contCategory == 'expense' && $datasource != 'checkbook_oge'){
        // if (_checkbook_project_recordsExists(939)) {
        $params = preg_replace('|\/contcat\/expense|', '', $path);
        $params = str_replace('/', ':', $params);
        return "/contract/search/transactions/contcat/expense/$params";
        // }
      }
      elseif ($contSatus != 'P' && $contCategory == 'all' && $datasource != 'checkbook_oge'){
        // if (_checkbook_project_recordsExists(939)) {
        $params = preg_replace('|\/contcat\/all|', '', $path);
        $params = str_replace('/', ':', $params);
        return "/contract/search/transactions/contcat/all/$params";
        //}
      }
      elseif ($datasource == 'checkbook_oge'){
        $params = preg_replace('|\/datasource\/checkbook_oge|', '', $path);
        $params = str_replace('/', ':', $params);
        return "/contract/search/transactions/datasource/checkbook_oge/$params";
      }
    }

    if (strpos($path, '/contract/all/transactions') === 0) {
      preg_replace('|^\/contract\/all\/transactions\/|', '', $path);
      $contcat = RequestUtilities::get('contcat');
      $datasource = RequestUtilities::get('datasource');
      $contStatus = RequestUtilities::get('contstatus');
      //@ToDo: check if /contract/all/transactions varient for EDC needs migrated (using /contract/search/transactions for now)
      if ($contcat != 'revenue' && $contStatus != 'P' && $datasource == 'checkbook_oge') {
        $params = preg_replace('|\/datasource\/checkbook_oge|', '', $path);
        $params = str_replace('/', ':', $params);
        return "/contract/search/transactions/datasource/checkbook_oge/$params";
      }
      elseif ($contcat == 'all') {
        $params = preg_replace('|\/contcat\/all|', '', $path);
        $params = str_replace('/', ':', $params);
        //$params = str_replace('/', ':', $path);
        return "/contract/search/all/transactions/contcat/all/$params";
      }
      elseif ($contcat == 'expense') {
        $params = preg_replace('|\/contcat\/expense|', '', $path);
        $params = str_replace('/', ':', $params);
        //$params = str_replace('/', ':', $path);
        return "/contract/search/all/transactions/contcat/expense/$params";
      }
      elseif ($contcat == 'revenue') {
        $params = preg_replace('|\/contcat\/revenue|', '', $path);
        $params = str_replace('/', ':', $params);
        //$params = str_replace('/', ':', $path);
        return "/contract/search/all/transactions/contcat/revenue/$params";
      }
    }
    // Contract All year Advanced Search
    /* if (strpos($path, '/contract/all/transactions/contcat/expense') === 0) {
      $params = preg_replace('|^\/contract\/search\/all\/transactions\/contcat\/expense\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/contract/search/all/transactions/contcat/expense/$params";
    }
    if (strpos($path, '/contract/all/transactions/contcat/revenue') === 0) {
      $params = preg_replace('|^\/contract\/search\/all\/transactions\/contcat\/revenue\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/contract/search/all/transactions/contcat/revenue/$params";
    }*/
    if (strpos($path, '/subcontract/transactions/') === 0) {
      $params = preg_replace('|^\/subcontract\/transactions\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/subcontract/transactions/$params";
    }
    // Contract Transactions paths.
    if (strpos($path, '/contract/transactions/') === 0) {
      $contCategory = RequestUtilities::get('contcat');
      $isEDCPage = Datasource::isOGE();
      $dashboard = RequestUtilities::get('dashboard');
      $path = preg_replace('|^\/contract\/transactions\/|', '', $path);
      //Citywide Active/Registered Revenue Contracts
      if ($contCategory == 'revenue' && $contSatus != 'P') {
        $path = str_replace('contcat/revenue/', '', $path);
        if (_checkbook_project_recordsExists(667)) {
          if (isset($dashboard)) {
            $path = str_replace('dashboard/'.$dashboard.'/', '', $path);
            $params = str_replace('/', ':', $path);
            return  "/contract/transactions/contcat/revenue/dashboard/".$dashboard."/".$params;
          }
          else {
            $params = str_replace('/', ':', $path);
            return "/contract/transactions/contcat/revenue/$params";
          }
        }
      }
      // EDC  Active/Registered Expense Contracts.
      if ($isEDCPage) {
        if (_checkbook_project_recordsExists(634)) {
          $params = str_replace('datasource/checkbook_oge/', '', $path);
          $params = str_replace('/', ':', $params);
          return "/contract/transactions/datasource/checkbook_oge/$params";
        }
      }
      // Pending Contracts.
      if ($contSatus == 'P'){
        if (_checkbook_project_recordsExists(714)) {
          $params = str_replace('contstatus/P/', '', $path);
          $params = str_replace('/', ':', $params);
          return "/contract/transactions/contstatus/P/$params";
        }
      }
      //Citywide Active/Registered Expense Contracts
      if ($contCategory == 'expense' && $contSatus != 'P') {
        $path = str_replace('contcat/expense/', '', $path);
        if (_checkbook_project_recordsExists(939)) {
          //MWBE Active/Registered Expense Contracts
          if (isset($dashboard)) {
            $path = str_replace('dashboard/'.$dashboard.'/', '', $path);
            $params = str_replace('/', ':', $path);
            return  "/contract/transactions/contcat/expense/dashboard/".$dashboard."/".$params;
          } else {
            $params = str_replace('/', ':', $path);
            return "/contract/transactions/contcat/expense/$params";
          }
        }
      }
    }

    // Contracts spending paths.
    if (strpos($path, '/contract/spending/transactions/') === 0) {
      $dashboard = RequestUtilities::get('dashboard');
      $path = preg_replace('|^\/contract/spending\/transactions\/|', '', $path);
      // SP ,SS, MS DASHBOARD
      if ($dashboard == 'sp' || $dashboard == 'ss' || $dashboard == 'ms') {
        // $path = preg_replace('|^\/contract/spending\/transactions\/|', '', $path);
        if ($dashboard == 'sp') {
          $params = str_replace('dashboard/sp/', '', $path);
          $params = str_replace('/', ':', $params);
          return "/contract/spending/transactions/dashboard/sp/" . $params;
        }
        elseif ($dashboard == 'ss') {
          $params = str_replace('dashboard/ss/', '', $path);
          $params = str_replace('/', ':', $params);
          return "/contract/spending/transactions/dashboard/ss/" . $params;
        }
        else {
          $params = str_replace('dashboard/ms/', '', $path);
          $params = str_replace('/', ':', $params);
          return "/contract/spending/transactions/dashboard/ms/" . $params;
        }
      }
      elseif (!$dashboard && Datasource::isOGE()) {
        $params = str_replace('datasource/checkbook_oge/', '', $path);
        $params = str_replace('/', ':', $params);
        return "/contract/spending/transactions/datasource/checkbook_oge/" . $params;
      }
      // CY , MP (DASHBOARD)
      else {
        $params = str_replace('/', ':', $path);
        return "/contract/spending/transactions/" . $params;
      }
    }

    // Contract details paths.
    if (strpos($path, '/contract_details/') === 0) {
      $params = preg_replace('|^\/contract_details\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/contract_details/$params";
    }
    // Below is for pending_contract_transactions mini_panel from d7.
    if (strpos($path, '/pending_contract_transactions/') === 0) {
      $params = preg_replace('|^\/pending_contract_transactions\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/pending_contract_transactions/$params";
    }
    // nycha contracts
    if (strpos($path, '/nycha_contract_assoc_releases/') === 0) {
      $params = preg_replace('|^\/nycha_contract_assoc_releases\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/nycha_contract_assoc_releases/$params";
    }
    if (strpos($path, '/nycha_contract_details/') === 0) {
      $params = preg_replace('|^\/nycha_contract_details\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/nycha_contract_details/$params";
    }
    if (strpos($path, '/nycha_contracts/transactions/') === 0) {
      $params = preg_replace('|^\/nycha_contracts\/transactions\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/nycha_contracts/transactions/$params";
    }
    if (strpos($path, '/nycha_contracts/search/transactions/') === 0) {
      $params = preg_replace('|^\/nycha_contracts\/search\/transactions\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/nycha_contracts/search/transactions/$params";
    }
    if (strpos($path, '/nycha_contracts/all/transactions/') === 0) {
      $params = preg_replace('|^\/nycha_contracts\/all\/transactions\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/nycha_contracts/all/transactions/$params";
    }

    // Payroll paths.
    if (strpos($path, '/payroll/transactions/') === 0) {
      $params = preg_replace('|^\/payroll\/transactions\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/payroll/transactions/$params";
    }
    if (strpos($path, '/payroll/payroll_title/transactions/') === 0) {
      $params = preg_replace('|^\/payroll\/payroll_title\/transactions\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/payroll/payroll_title/transactions/$params";
    }
    if (strpos($path, '/payroll/monthly/transactions/') === 0) {
      $params = preg_replace('|^\/payroll\/monthly\/transactions\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/payroll/monthly/transactions/$params";
    }
    if (strpos($path, '/payroll/agencywide/monthly/transactions/') === 0) {
      $params = preg_replace('|^\/payroll\/agencywide\/monthly\/transactions\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/payroll/agencywide/monthly/transactions/$params";
    }
    if (strpos($path, '/payroll/agencywide/transactions/') === 0) {
      $params = preg_replace('|^\/payroll\/agencywide\/transactions\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/payroll/agencywide/transactions/$params";
    }
    if (strpos($path, '/payroll/employee/transactions/') === 0) {
      $params = preg_replace('|^\/payroll\/employee\/transactions\/|', '', $path);
      $params = str_replace('/',':', $params);

      $pathParams = explode('/', $path);
      $index = array_search('yeartype',$pathParams);

      if ($index && $pathParams[($index+1)] == 'C') {
        return "/payroll/employee/transactions/cy/$params";
      }
      else {
        return "/payroll/employee/transactions/$params";
      }
    }

    if (strpos($path, '/payroll/search/transactions/') === 0) {
      $params = preg_replace('|^\/payroll\/search\/transactions\/|', '', $path);
      $params = str_replace('/',':', $params);

      $pathParams = explode('/', $path);
      $index = array_search('yeartype',$pathParams);
      if( $index &&  $pathParams[($index+1)] == 'C'){
        return "/payroll/search/transactionsCY/$params";
      }
      else {
        return "/payroll/search/transactionsFY/$params";
      }
    }

    //Spending Transactions paths
    if (strpos($path, '/spending/transactions/') === 0) {
      $dashboard = RequestUtilities::get('dashboard');

      // SP AND SS DASHBOARD
      if ($dashboard == 'sp' || $dashboard == 'ss') {
        $path = preg_replace('|^\/spending\/transactions\/|', '', $path);
        if (_checkbook_project_recordsExists(723)) {
          if ($dashboard == 'sp') {
            $params = str_replace('dashboard/sp/', '', $path);
          }
          else{
            $params = str_replace('dashboard/ss/', '', $path);
          }
          $params = str_replace('/', ':', $params);
          return  "/spending/transactions/dashboard/sp/".$params;
        }
      }
      // Mwbe Subvendor Dashborad
      elseif ($dashboard == 'ms'){
        $path = preg_replace('|^\/spending\/transactions\/|', '', $path);
        $params = str_replace('dashboard/ms/', '', $path);
        $params = str_replace('/', ':', $params);
        return  "/spending/transactions/dashboard/ms/".$params;
      }
      elseif (!$dashboard && Datasource::isOGE()) {
        $path = preg_replace('|^\/spending\/transactions\/|', '', $path);
        $params = str_replace('datasource/checkbook_oge/', '', $path);
        $params = str_replace('/', ':', $params);
        return  "/spending/transactions/datasource/checkbook_oge/".$params;
      }
      // Citywide and MWBE
      else {
        $path = preg_replace('|^\/spending\/transactions\/|', '', $path);
       // if (_checkbook_project_recordsExists(939)) {
          $params = str_replace('/', ':', $path);
          return "/spending/transactions/" . $params;
        //}
      }
    }
    // Spending Advanced Search Transactions
    if (strpos($path, '/spending/search/transactions') === 0) {
      $datasource = RequestUtilities::get('datasource');
      if ($datasource == 'checkbook_oge') {
        $path = preg_replace('|^\/spending\/search\/transactions\/|', '', $path);
        $params = str_replace('datasource/checkbook_oge/', '', $path);
        $params = str_replace('/', ':', $params);
        return "/spending/search/transactions/datasource/checkbook_oge/$params";
      }
      else{
        $params = preg_replace('|^\/spending\/search\/transactions\/|', '', $path);
        $params = str_replace('/', ':', $params);
        return "/spending/search/transactions/".$params;
      }
    }

    // Nycha Spending Transactions.
    if (strpos($path, '/nycha_spending/transactions/') === 0) {
      $params = preg_replace('|^\/nycha_spending\/transactions\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/nycha_spending/transactions/$params";
    }

    // Nycha Spending Advanced Search.
    if (strpos($path, '/nycha_spending/search/transactions/') === 0) {
      $params = preg_replace('|^\/nycha_spending\/search\/transactions\/|', '', $path);
      $params = str_replace('/',':', $params);
      return "/nycha_spending/search/transactions/$params";
    }
    return $path;
  }

}
