<?php

namespace Drupal\checkbook_custom_breadcrumbs;

/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City.
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

use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Constants\Common\PageType;
use Drupal\checkbook_project\CommonUtilities\RequestUtil;
use Drupal\checkbook_project\ContractsUtilities\ChildAgreementDetails;
use Drupal\checkbook_project\ContractsUtilities\MasterAgreementDetails;
use Drupal\checkbook_project\MwbeUtilities\MappingUtil;
use Drupal\checkbook_project\NychaContractUtilities\NYCHAContractUtil;
use Drupal\checkbook_project\WidgetUtilities\NodeSummaryUtil;

/**
 * Contracts breadcrumbs class.
 */
class ContractsBreadcrumbs {

  const NYC = "New York City";
  const NYCHA = "New York City Housing Authority";

  /**
   * Returns Contracts page title and Breadcrumb.
   */
  public static function getContractsBreadcrumbTitle() {
    $title = '';
    $current_path = \Drupal::service('path.current')->getPath();
    $expand_bottom_param = \Drupal::request()->query->get('expandBottomContURL');
    $bottomURL = $expand_bottom_param ?? FALSE;
    // For NYCEDC advanced search results.
    $edcSubTitle = (!isset($bottomURL) && str_contains($bottomURL, 'transactions') && Datasource::isOGE()) ? Datasource::EDC_TITLE . " " : '';

    if (str_contains($bottomURL, 'magid')) {
      $magid = RequestUtil::getRequestKeyValueFromURL("magid", $bottomURL);
      $contract_number = MasterAgreementDetails::_get_master_agreement_details($magid);
      return $contract_number['contract_number'];
    }
    elseif (str_contains($bottomURL, 'agid')) {
      $agid = RequestUtil::getRequestKeyValueFromURL("agid", $bottomURL);
      $contract_number = ChildAgreementDetails::_get_child_agreement_details($agid);
      return $contract_number['contract_number'];
    }
    elseif (str_contains($bottomURL, 'contract') && str_contains($bottomURL, 'pending_contract_transactions')) {
      return RequestUtil::getRequestKeyValueFromURL("contract", $bottomURL);
    }
    else {
      if (isset($bottomURL) && str_contains($bottomURL, 'transactions')) {
        $smnid = RequestUtil::getRequestKeyValueFromURL("smnid", $bottomURL);
        $dashboard = RequestUtil::getRequestKeyValueFromURL("dashboard", $bottomURL);
        $mocs = RequestUtil::getRequestKeyValueFromURL("mocs", $bottomURL);
        $title = NodeSummaryUtil::getInitNodeSummaryTitle($smnid);
        if ($smnid == 720 && $dashboard != 'mp') {
          $title = '';
        }
        $bottomNavigation = '';
        if (preg_match('/status\/A/', $current_path)) {
          $bottomNavigation = "Total Active Sub Vendor Contracts";
        }
        elseif (preg_match('/status\/R/', $current_path)) {
          $bottomNavigation = "New Sub Vendor Contracts by Fiscal Year";
        }
        if ($smnid == 722 || $smnid == 782) {
          $title = "Amount Modifications";
        }
        if ($smnid == 721 || $smnid == 720 || $smnid == 781 || $smnid == 784) {
          $title = "";
        }
        if ($smnid == 722 || $smnid == 726 || $smnid == 727 || $smnid == 728 || $smnid == 729 || $smnid == 782 || $smnid == 785 || $smnid == 786 || $smnid == 787 || $smnid == 788) {
          $title = $title . " by ";
        }
        if ($smnid == 725 || $smnid == 783) {
          $title = $title . " with ";
        }
        if (isset($mocs)) {
          $title = "MOCS Registered COVID-19 Contracts";
        }

        if (str_starts_with($current_path, '/contracts_landing') && preg_match('/status\/A/', $current_path)) {
          if (preg_match('/dashboard\/ss/', $current_path) || preg_match('/dashboard\/ms/', $current_path) || preg_match('/dashboard\/sp/', $current_path)) {
            if (preg_match('/dashboard\/ms/', $current_path) || preg_match('/dashboard\/sp/', $current_path)) {
              $bottomNavigation = "Total Active M/WBE Sub Vendor Contracts";
            }
            $title = $title . " " . $bottomNavigation . " Transactions";
          }
          else {
            $title = RequestUtil::getDashboardTitle() . ' ' . $title . ' ' . ' Active Expense Contracts Transactions';
          }
        }
        elseif (str_starts_with($current_path, '/contracts_landing') && preg_match('/status\/R/', $current_path)) {
          if (preg_match('/dashboard\/ss/', $current_path) || preg_match('/dashboard\/ms/', $current_path) || preg_match('/dashboard\/sp/', $current_path)) {
            if (preg_match('/dashboard\/ms/', $current_path) || preg_match('/dashboard\/sp/', $current_path)) {
              $bottomNavigation = "New M/WBE Sub Vendor Contracts by Fiscal Year";
            }
            $title = $title . " " . $bottomNavigation . " Transactions";

          }
          else {
            $title = RequestUtil::getDashboardTitle() . ' ' . $title . ' ' . ' Registered Expense Contracts Transactions';
          }

        }
        elseif (str_starts_with($current_path, '/contracts_landing') && !preg_match('/status\//', $current_path)) {
          if (preg_match('/dashboard\/ss/', $current_path)) {
            $title = 'Subcontract Status by Prime Contract ID';
          }
          if (preg_match('/dashboard\/ms/', $current_path) || preg_match('/dashboard\/sp/', $current_path)) {
            $title = 'M/WBE Subcontract Status by Prime Contract ID';
          }
        }
        elseif (str_starts_with($current_path, '/contracts_revenue_landing') && preg_match('/status\/A/', $current_path)) {
          $title = RequestUtil::getDashboardTitle() . ' ' . $title . ' ' . ' Active Revenue Contracts Transactions';
        }
        elseif (str_starts_with($current_path, '/contracts_revenue_landing') && preg_match('/status\/R/', $current_path)) {
          $title = RequestUtil::getDashboardTitle() . ' ' . $title . ' ' . ' Registered Revenue Contracts Transactions';
        }
        elseif (str_starts_with($current_path, '/contracts_pending_exp_landing')) {
          $title = RequestUtil::getDashboardTitle() . ' ' . $title . ' ' . ' Pending Expense Contracts Transactions';
        }
        elseif (str_starts_with($current_path, '/contracts_pending_rev_landing')) {
          $title = RequestUtil::getDashboardTitle() . ' ' . $title . ' ' . ' Pending Revenue Contracts Transactions';
        }
      }
      elseif (str_starts_with($current_path, '/contracts_landing') && preg_match('/status\/A/', $current_path)) {
        if (preg_match('/dashboard\/ss/', $current_path) || preg_match('/dashboard\/ms/', $current_path) || preg_match('/dashboard\/sp/', $current_path)) {
          $title = self::getContractsPageTitle(FALSE) . ' ' . ' Total Active Sub Vendor Contracts';
        }
        else {
          $title = self::getContractsPageTitle(FALSE) . ' ' . RequestUtil::getDashboardTitle() . ' Active Expense Contracts';
        }

      }
      elseif (str_starts_with($current_path, '/contracts_landing') && preg_match('/status\/R/', $current_path)) {
        if (preg_match('/dashboard\/ss/', $current_path) || preg_match('/dashboard\/ms/', $current_path) || preg_match('/dashboard\/sp/', $current_path)) {
          $title = self::getContractsPageTitle(FALSE) . ' ' . ' New Sub Vendor Contracts by Fiscal Year';
        }
        else {
          $title = self::getContractsPageTitle(FALSE) . ' ' . RequestUtil::getDashboardTitle() . ' Registered Expense Contracts';
        }
      }
      elseif (str_starts_with($current_path, '/contracts_landing') && !preg_match('/status\//', $current_path)) {
        if (preg_match('/dashboard\/ss/', $current_path) || preg_match('/dashboard\/ms/', $current_path) || preg_match('/dashboard\/sp/', $current_path)) {
          $title = self::getContractsPageTitle(FALSE) . ' ' . ' Status of Sub Vendor Contracts by Prime Vendor';
        }
      }
      elseif (str_starts_with($current_path, '/contracts_revenue_landing') && preg_match('/status\/A/', $current_path)) {
        $title = self::getContractsPageTitle(FALSE) . ' ' . RequestUtil::getDashboardTitle() . ' Active Revenue Contracts';
      }
      elseif (str_starts_with($current_path, '/contracts_revenue_landing') && preg_match('/status\/R/', $current_path)) {
        $title = self:: getContractsPageTitle(FALSE) . ' ' . RequestUtil::getDashboardTitle() . ' Registered Revenue Contracts';
      }
      elseif (str_starts_with($current_path, '/contracts_pending_exp_landing')) {
        $title = self::getPendingContractsTitleDrilldown() . ' ' . RequestUtil::getDashboardTitle() . ' Pending Expense Contracts';
      }
      elseif (str_starts_with($current_path, '/contracts_pending_rev_landing')) {
        $title = self::getPendingContractsTitleDrilldown() . ' ' . RequestUtil::getDashboardTitle() . ' Pending Revenue Contracts';
      }
      else {
        global $_checkbook_breadcrumb_title;
        $title = $_checkbook_breadcrumb_title;
      }
    }
    return html_entity_decode($edcSubTitle . $title);
  }

  /**
   * Custom function to get title for contracts landing pages.
   *
   * @param bool $ethinicty
   *   The ethinicty.
   *
   * @return string
   *   The title.
   */
  public static function getContractsPageTitle($ethinicty = TRUE) {
    $lastReqParam = RequestUtil::_getLastRequestParamValue();
    $title = self::NYC;

    foreach ($lastReqParam as $key => $value) {
      switch ($key) {
        case 'agency':
          $title = _checkbook_project_get_name_for_argument("agency_id", $value);
          break;

        case 'vendor':
          $title = _checkbook_project_get_name_for_argument("vendor_id", $value);
          if ($ethinicty) {
            $title .= MappingUtil::getPrimeVendorEthinictyTitle($value, "contracts");
          }
          break;

        case 'awdmethod':
          $title = _checkbook_project_get_name_for_argument("award_method_code", $value);
          break;

        case 'csize':
          $title = _checkbook_project_get_name_for_argument("award_size_id", $value);
          break;

        case 'cindustry':
          $title = _checkbook_project_get_name_for_argument("industry_type_id", $value);
          break;

        case 'subvendor':
          $title = _checkbook_project_get_name_for_argument("sub_vendor_id", $value);
          if ($ethinicty) {
            $title .= MappingUtil::getSubVendorEthinictyTitle($value, "contracts");
          }
          break;

        default:
          break;
      }
    }

    return $title;
  }

  /**
   * Custom function to set title for contracts landing pages.
   *
   * @param bool $ethinicty
   *   The ethinicty.
   *
   * @return string
   *   The title.
   */
  public static function getPendingContractsTitleDrilldown($ethinicty = TRUE): string {

    $lastReqParam = RequestUtil::_getLastRequestParamValue();
    $title = self::NYC;

    foreach ($lastReqParam as $key => $value) {
      switch ($key) {
        case 'agency':
          $title = _checkbook_project_get_name_for_argument("agency_id", $value);
          break;

        case 'vendor':
          $title = _checkbook_project_get_name_for_argument("pending_contracts_vendor_id", $value);
          if ($ethinicty) {
            $title .= MappingUtil::getPrimeVendorEthinictyTitle($value, "contracts");
          }
          break;

        case 'awrdmthd':
        case 'awdmethod':
          $title = _checkbook_project_get_name_for_argument("award_method_code", $value);
          break;

        case 'csize':
          $title = _checkbook_project_get_name_for_argument("award_size_id", $value);
          break;

        case 'cindustry':
          $title = _checkbook_project_get_name_for_argument("industry_type_id", $value);
          break;

        default:
          break;
      }
    }

    return $title;

  }

  /**
   * Returns NYCHA Contracts page title and Breadcrumb.
   *
   * @return string
   *   The title.
   */
  public static function getNychaContractsBreadcrumbTitle(): string {
    $title = "";
    $expand_bottom_param = \Drupal::request()->query->get('expandBottomContURL');
    $bottomURL = $expand_bottom_param ?? FALSE;
    if (PageType::getCurrent() == PageType::ADVANCED_SEARCH_PAGE) {
      $title = 'NYCHA Contracts Transactions';
    }
    else {
      if (stripos($bottomURL, 'transactions')) {
        $code = RequestUtil::getRequestKeyValueFromURL("tCode", $bottomURL);
        $title = NYCHAContractUtil::getTitleByCode($code) . ' Contracts Transactions';
      }
      else {
        if (preg_match('/contract/', $bottomURL)) {
          $title = RequestUtil::getRequestKeyValueFromURL("contract", $bottomURL);
        }
        else {
          $lastReqParam = RequestUtil::_getLastRequestParamValue();
          foreach ($lastReqParam as $key => $value) {
            switch ($key) {
              case 'vendor':
                $title = _checkbook_project_get_name_for_argument("vendor_id", $value);
                break;

              case 'awdmethod':
              case 'award_method':
                $title = _checkbook_project_get_name_for_argument("award_method_code", $value);
                break;

              case 'csize':
                $title = _checkbook_project_get_name_for_argument("award_size_id", $value);
                break;

              case 'industry':
                $title = _checkbook_project_get_name_for_argument("industry_type_id", $value);
                break;

              default:
                $title = self::NYCHA;
                break;
            }
            $title .= ' Contracts';
          }
        }
      }
    }
    return html_entity_decode($title);
  }

  /**
   * Custom function to get title for NYCHA CONTRACTS landing pages.
   *
   * @return string|null
   *   The title.
   */
  public static function getNychaContractsPageTitle(): ?string {
    $current_path = \Drupal::service('path.current')->getPath();
    $expand_bottom_param = \Drupal::request()->query->get('expandBottomContURL');
    $bottomURL = $expand_bottom_param;
    if (!$bottomURL && preg_match('/^nycha_contracts\/search\/transactions/', $current_path) || preg_match('/^nycha_contracts\/all\/transactions/', $current_path)) {
      $title = NULL;
    }
    else {
      $lastReqParam = RequestUtil::_getLastRequestParamValue();
      foreach ($lastReqParam as $key => $value) {
        $title = match ($key) {
          'vendor' => _checkbook_project_get_name_for_argument("vendor_id", $value),
          'awdmethod' => _checkbook_project_get_name_for_argument("award_method_code", $value),
          'csize' => _checkbook_project_get_name_for_argument("award_size_id", $value),
          'industry' => _checkbook_project_get_name_for_argument("industry_type_id", $value),
          default => self::NYCHA,
        };
      }
    }
    return $title ?? NULL;
  }

}
