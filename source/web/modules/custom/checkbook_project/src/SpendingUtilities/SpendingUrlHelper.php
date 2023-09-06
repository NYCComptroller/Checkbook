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

namespace Drupal\checkbook_project\SpendingUtilities;

use Drupal;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\CustomURLHelper;
use Drupal\checkbook_project\ContractsUtilities\ContractURLHelper;

class SpendingUrlHelper{

  /**
   * Returns Spending Footer Url based on values from current path
   * @param $node
   * @return string
   */
  public static function getSpendingFooterUrl($node)
  {
    $override_params = array(
      "dtsmnid" => $node->nid,
      "fvendor" => VendorSpendingUtil::getVendorFacetParameter($node)
    );
    return '/' . self::getSpendingTransactionPageUrl($override_params);
  }


  /** Returns Spending Footer Url based on values from current path,
   *
   * @param $node
   * @param $row
   * @return string
   */
  public static function getAgencyNameLinkUrl($node, $row)
  {
    $custom_params = array('agency' => (isset($row["agency_id"]) ? $row["agency_id"] : $row["agency_agency"]));
    return '/' . self::getLandingPageWidgetUrl($custom_params);
  }

  /**
   * Returns Agency Amount Link Url based on values from current path & data row.
   *
   * @param $node
   * @param $row
   * @return string
   */
  public static function getAgencyAmountLinkUrl($node, $row)
  {
    $override_params = array(
      'agency' => $row["agency_agency"],
      "fvendor" => VendorSpendingUtil::getVendorFacetParameter($node),
      "smnid" => $node->nid
    );
    return '/' . self::getSpendingTransactionPageUrl($override_params);
  }

  /**
   * Returns Department Amount Link Url based on values from current path & data row
   *
   * @param $node
   * @param $row
   * @return string
   */
  public static function getDepartmentAmountLinkUrl($node, $row)
  {
    $override_params = array(
      'agency' => $row["agency_agency"],
      'dept' => $row["department_department"],
      "fvendor" => VendorSpendingUtil::getVendorFacetParameter($node),
      "smnid" => $node->nid
    );
    return '/' . self::getSpendingTransactionPageUrl($override_params);
  }

  /**
   * Returns Check Amount Sum Link Url based on values from current path & data row
   *
   * Transaction page from M/WBE Dashboard landing page
   * @param $node
   * @param $row
   * @return string
   */
  public static function getCheckAmountSumLinkUrl($node, $row)
  {
    $override_params = array(
      'expcategory' => $row["expenditure_object_expenditure_object"],
      "fvendor" => VendorSpendingUtil::getVendorFacetParameter($node),
      "smnid" => $node->nid
    );
    return '/' . self::getSpendingTransactionPageUrl($override_params);
  }

  /**
   * Returns Contract Amount Link Url based on values from current path & data row
   *
   * @param $node
   * @param $row
   * @return string
   */
  public static function getContractAmountLinkUrl($node, $row)
  {
    $contract_url_part = ContractURLHelper::_checkbook_project_get_contract_url($row["document_id_document_id"], $row["agreement_id_agreement_id"]);
    $override_params = array(
      "fvendor" => VendorSpendingUtil::getVendorFacetParameter($node),
      "smnid" => $node->nid
    );
    return '/' . self::getSpendingTransactionPageUrl($override_params) . $contract_url_part;
  }

  /**
   * Returns Sub Contract Amount Link Url based on values from current path & data row
   *
   * @param $node
   * @param $row
   * @return string
   */
  public static function getSubContractAmountLinkUrl($node, $row)
  {
    $agreement_id = $row["agreement_id_agreement_id"];
    $document_id = isset($row["document_id_document_id"]) ? $row["document_id_document_id"] : $row["reference_document_code"];
    $contract_url_part = ContractURLHelper::_checkbook_project_get_contract_url($document_id, $agreement_id);
    $override_params = array(
      "fvendor" => VendorSpendingUtil::getVendorFacetParameter($node),
      "smnid" => $node->nid
    );
    return '/' . self::getSpendingTransactionPageUrl($override_params) . $contract_url_part;
  }


  /**
   * Returns Contract Number Link Url based on values from current path & data row
   *
   * @param $node
   * @param $row
   * @return string
   */
  public static function getContractNumberLinkUrl($node, $row)
  {
    //contract_number_link
    $agreement_id = isset($row["agreement_id_agreement_id"]) ? $row["agreement_id_agreement_id"] : $row["agreement_id"];
    $document_id = isset($row["document_id_document_id"]) ? $row["document_id_document_id"] : $row["reference_document_code"];
    return '/contract_details'
      . CustomURLHelper::_checkbook_append_url_params()
      . ContractURLHelper::_checkbook_project_get_contract_url($document_id, $agreement_id)
      . '/newwindow';
  }

  /**
   * Returns Sub Contract Number Link Url based on values from current path & data row
   *
   * @param $node
   * @param $row
   * @return string
   */
  public static function getSubContractNumberLinkUrl($node, $row)
  {
    //contract_number_link
    $agreement_id = isset($row["sub_contract_number_sub_contract_number_original_agreement_id"]) ? $row["sub_contract_number_sub_contract_number_original_agreement_id"] : $row["original_agreement_id@checkbook:sub_vendor_agid"];
    $document_id = isset($row["document_id_document_id"]) ? $row["document_id_document_id"] : $row["reference_document_code"];
    return '/contract_details'
      . CustomURLHelper::_checkbook_append_url_params()
      . ContractURLHelper::_checkbook_project_get_contract_url($document_id, $agreement_id)
      . '/newwindow';
  }

  /**
   * Returns Industry Name Link Url based on values from current path & data row
   *
   * @param $node
   * @param $row
   * @return string
   */
  static function getIndustryNameLinkUrl($node, $row)
  {
    $custom_params = array('industry' => isset($row['industry_industry_industry_type_id']) ? $row['industry_industry_industry_type_id'] : $row['industry_type_industry_type']);
    return '/' . self::getLandingPageWidgetUrl($custom_params);
  }

  /**
   * Returns Industry Ytd Spending Link Url based on values from current path & data row
   *
   * @param $node
   * @param $row
   * @return string
   */
  public static function getIndustryYtdSpendingLinkUrl($node, $row)
  {
    $override_params = array(
      'industry' => isset($row['industry_industry_industry_type_id']) ? $row['industry_industry_industry_type_id'] : $row['industry_type_industry_type'],
      "fvendor" => VendorSpendingUtil::getVendorFacetParameter($node),
      "smnid" => $node->nid
    );
    return '/' . self::getSpendingTransactionPageUrl($override_params);
  }

  /**
   * Returns Agency YTD Spending Link Url based on values from current path & data row.
   * This is for sub vendors Top 5 Agencies widget
   *
   * @param $node
   * @param $row
   * @return string
   */
  public static function getAgencyYtdSpendingUrl($node, $row)
  {
    //ytd_spending_sub_vendors_link
    return '/spending/transactions'
      . '/agency/' . $row["agency_agency"]
      . RequestUtilities::buildUrlFromParam('vendor')
      . RequestUtilities::buildUrlFromParam('category')
      . RequestUtilities::buildUrlFromParam('industry')
      . CustomURLHelper::_checkbook_project_get_year_url_param_string(false, false, true)
      . '/smnid/' . $node->nid . '/dtsmnid/' . $node->nid . '/newwindow';
  }

  /**
   *  Returns a spending landing page Url with custom parameters appended but instead of persisted
   *
   * @param array $override_params
   * @return string
   */
  public static function getLandingPageWidgetUrl($override_params = array())
  {
    $url = self::getSpendingUrl('/spending_landing', $override_params);
    return str_replace("calyear", "year", $url);
  }

  /**
   *  Returns a spending transaction page Url with custom parameters appended but instead of persisted
   *
   * @param array $override_params
   * @return string
   */
  public static function getSpendingTransactionPageUrl($override_params = array())
  {
    return self::getSpendingUrl('/spending/transactions', $override_params);
  }

  /**
   *  Returns a spending contract details page Url with custom parameters appended but instead of persisted
   *
   * @param array $override_params
   * @return string
   */
  public static function getSpendingContractDetailsPageUrl(array $override_params = array())
  {
    return self::getSpendingUrl('contract_details', $override_params);
  }

  /**
   * Function build the url using the path and the current Spending URL parameters.
   * The Url parameters can be overridden by the override parameter array.
   *
   * @param $path
   * @param array $override_params
   * @return string
   */
  public static function getSpendingUrl($path, array $override_params = array())
  {
    $current_path = Drupal::service('path.current')->getPath();
    $q = Drupal::service('path_alias.manager')->getAliasByPath($current_path);
    //$q = drupal_get_path_alias($_GET['q']);
    if (RequestUtilities::_checkbook_current_request_is_ajax()) {
      // remove query part
      $q = strtok($_SERVER['HTTP_REFERER'], '?');
    }

    $pathParams = explode('/', $q);
    $url_params = SpendingUtil::$landingPageParams;
    $exclude_params = array_keys($override_params);

    $url = !in_array('year', $exclude_params) ? $path . CustomURLHelper::_checkbook_project_get_year_url_param_string() : $path;

    if (is_array($url_params)) {
      foreach ($url_params as $key => $value) {
        if (!in_array($key, $exclude_params)) {
          $url .= CustomURLHelper::get_url_param($pathParams, $key, $value);
        }
      }
    }

    if (is_array($override_params)) {
      foreach ($override_params as $key => $value) {
        if (isset($value)) {
          if ($key == 'yeartype' && $value == 'C') {
            $value = 'B';
          }
          $url .= "/$key";
          $url .= "/$value";
        }
      }
    }
    return $url;
  }
}
