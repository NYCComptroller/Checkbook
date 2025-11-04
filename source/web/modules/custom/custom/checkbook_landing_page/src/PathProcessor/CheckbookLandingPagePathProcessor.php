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

namespace Drupal\checkbook_landing_page\PathProcessor;

use Drupal\checkbook_infrastructure_layer\Constants\Common\CheckbookDomain;
use Drupal\checkbook_infrastructure_layer\Constants\Common\PageType;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Symfony\Component\HttpFoundation\Request;

class CheckbookLandingPagePathProcessor implements InboundPathProcessorInterface {
  public function processInbound($path, Request $request) {
    // Need to check if path is not /budget/transactions since Transactions page uses that. see CheckbookTransactionsPathProcessor.
    if (!str_contains($path, '/budget/transactions')) {
      //now checking budget landing page paths
      if (str_starts_with($path, '/budget/')) {
        $path = preg_replace('|^\/budget\/|', '', $path);
        $urlPath = "/budget/";
      }
    }

    // Need to see if path is not a NYCHA Budget transaction page. see CheckbookTransactionsPathProcessor.
    if (!str_contains($path, '/nycha_budget/transactions') &&
        !str_contains($path, '/nycha_budget/details') &&
        !str_contains($path, '/nycha_budget/fundsrc_details') &&
        !str_contains($path, '/nycha_budget/project_details') &&
        !str_contains($path, '/nycha_budget/program_details') &&
        !str_contains($path, '/nycha_budget/respcenter_details') &&
        !str_contains($path, '/nycha_budget/search/transactions')) {
      // Now checking NYCHA budget landing page paths.
      if (str_starts_with($path, '/nycha_budget/')) {
        $path = preg_replace('|^\/nycha_budget\/|', '', $path);
        $urlPath = "/nycha_budget/";
      }
    }

    //need to see if path is not a NYC Revenue transaction page. see CheckbookTransactionsPathProcessor
    if (!str_contains($path, '/revenue/transactions') &&
        !str_contains($path, '/revenue/agency_details') &&
        !str_contains($path, '/revenue/revcat_details') &&
        !str_contains($path, '/revenue/fundsrc_details')) {
      // Now checking NYCHA budget landing page paths.
      if (str_starts_with($path, '/revenue/')) {
        $path = preg_replace('|^\/revenue\/|', '', $path);
        $urlPath = "/revenue/";
      }
    }

    // Need to see if path is not a NYCHA Revenue transaction page. see CheckbookTransactionsPathProcessor.
    if (!str_contains($path, '/nycha_revenue/transactions') &&
        !str_contains($path, '/nycha_revenue/search/transactions')) {
      // Now checking NYCHA budget landing page paths.
      if (str_starts_with($path, '/nycha_revenue/')) {
        $path = preg_replace('|^\/nycha_revenue\/|', '', $path);
        $urlPath =  "/nycha_revenue/";
      }
    }

    // Need to see if path is not a NYCHA Spending transaction page. see CheckbookTransactionsPathProcessor.
    if (!str_contains($path, '/nycha_spending/transactions') &&
        !str_contains($path, '/nycha_spending/search/transactions')) {
      // Now checking NYCHA budget landing page paths.
      if (str_starts_with($path, '/nycha_spending/')) {
        $path = preg_replace('|^\/nycha_spending\/|', '', $path);
        $urlPath = "/nycha_spending/";
      }
    }

    // Spending paths.
    if (!str_contains($path, '/spending/transactions') &&
        !str_contains($path, '/spending/transactions/datasource') &&
        !str_contains($path, '/spending/transactions/dashboard')) {
      if (str_starts_with($path, '/spending_landing/')) {
        $params = preg_replace('|^\/spending_landing\/|', '', $path);
        $params = str_replace('/', ':', $params);
        $params = "$params";
        if (preg_match('*dashboard/s*', $path)) {
          if (preg_match('*dashboard:ss*', $params)){
            $params = str_replace(":dashboard:ss", '', $params);
            $params = "$params";
            return "/spending_landing/dashboard/ss/$params";
          }
          else {
            $params = str_replace(":dashboard:sp", '', $params);
            $params = "$params";
            return "/spending_landing/dashboard/sp/$params";
          }
        }
        elseif (preg_match('*dashboard/ms*', $path)) {
          $params = str_replace(":dashboard:ms", '', $params);
          $params = "$params";
          return "/spending_landing/dashboard/ms/$params";
        }
        elseif (preg_match('*dashboard/mp*', $path)) {
          $params = str_replace(":dashboard:mp", '', $params);
          $params = "$params";
          return "/spending_landing/dashboard/mp/$params";
        }
        else {
          return "/spending_landing/$params";
        }
      }
    }

    // Need to see if path is not a Citywide NYCHA Payroll transaction page. see CheckbookTransactionsPathProcessor.
    if (!str_contains($path, '/payroll/transactions') &&
        !str_contains($path, '/payroll/payroll_title/transactions') &&
        !str_contains($path, '/payroll/monthly/transactions') &&
        !str_contains($path, '/payroll/agencywide/monthly/transactions') &&
        !str_contains($path, '/payroll/agencywide/transactions') &&
        !str_contains($path, '/payroll/search/transactions') &&
        !str_contains($path, '/payroll/employee/transactions')) {
      // Now checking payroll landing page paths.
      if (str_starts_with($path, '/payroll/')) {
        $path = preg_replace('|^\/payroll\/|', '', $path);
        $urlPath = "/payroll/";
      }
    }

    // NYCHA Contracts Path Processor.
    if (CheckbookDomain::getCurrent() == CheckbookDomain::$NYCHA_CONTRACTS && PageType::getCurrent() == PageType::LANDING_PAGE){
      $path = preg_replace('|^\/nycha_contracts\/|', '', $path);
      $urlPath = "/nycha_contracts/";
    }

    // NYC Contracts Landing Page path processor.
    if (str_starts_with($path, '/contracts_landing/')) {
      $path = preg_replace('|^\/contracts_landing\/|', '', $path);
      if (preg_match('*dashboard/s*', $path)) {
        $path = str_replace('/contracts_landing/subvendor_landing','', $path);
        $urlPath = "/contracts_landing/subvendor_landing/";
      }
      elseif (preg_match('*dashboard/mp*', $path)) {
        $urlPath = "/contracts_landing/mwbe_landing/";
      }
      elseif (preg_match('*dashboard/ms*', $path)) {
        $urlPath = "/contracts_landing/mwbe_subvendor/";
      }
      else {
        $urlPath = "/contracts_landing/";
      }
    }

    // Contracts Revenue Landing Page path processor.
    if (str_starts_with($path, '/contracts_revenue_landing/')) {
      $path = preg_replace('|^\/contracts_revenue_landing\/|', '', $path);
      $urlPath = "/contracts_revenue_landing/";
    }

    if (str_starts_with($path, '/contracts_pending_exp_landing/')) {
      $path = preg_replace('|^\/contracts_exp_pending_landing\/|', '', $path);
      $urlPath = "/contracts_pending_exp_landing/";
    }

    if (str_starts_with($path, '/contracts_pending_rev_landing/')) {
      $path = preg_replace('|^\/contracts_rev_pending_landing\/|', '', $path);
      $urlPath = "/contracts_pending_rev_landing/";
    }

    if (isset($urlPath)) {
      $params = RequestUtilities::replaceSlash($path);
      $path =  $urlPath . $params;
    }

    return $path;
  }
}
