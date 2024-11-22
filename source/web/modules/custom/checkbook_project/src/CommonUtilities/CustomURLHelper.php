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

namespace Drupal\checkbook_project\CommonUtilities;

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;

class CustomURLHelper
{

  static function get_url_param($pathParams, $key, $key_alias = null)
  {
    if (!is_array($pathParams)) {
      return NULL;
    }
    $keyIndex = array_search($key, $pathParams);

    if ($keyIndex) {
      if ($key_alias == null) {
        return "/$key/" . $pathParams[($keyIndex + 1)];
      } else {
        return "/$key_alias/" . $pathParams[($keyIndex + 1)];
      }
    }
    return NULL;

  }

  public static function prepareUrl($path, $params = array(), $requestParams = array(), $customPathParams = array(), $applyPreviousYear = false, $applySpendingYear = false)
  {
    $current_path = \Drupal::service('path.current')->getPath();
    $urlPath = \Drupal::service('path_alias.manager')->getAliasByPath($current_path);

    //$pathParams = explode('/', drupal_get_path_alias($_GET['q']));
    $pathParams = explode('/', $urlPath);
    $url = "/".$path . self::_checkbook_append_url_params() . self::_checkbook_project_get_year_url_param_string($applySpendingYear, $applyPreviousYear);
    if (is_array($params)) {
      foreach ($params as $key => $value) {
        $url .= self::get_url_param($pathParams, $key, $value);
      }
    }
    if (is_array($customPathParams)) {
      foreach ($customPathParams as $key => $value) {
        $url .= "/$key";
        if (isset($value)) {
          $url .= "/$value";
        }
      }
    }
    if (is_array($requestParams) && !empty($requestParams)) {
      $cnt = 0;
      foreach ($requestParams as $key => $value) {
        if ($cnt == 0) {
          $url .= "?$key=$value";
        } else {
          $url .= "&$key=$value";
        }

        $cnt++;
      }
    }

    return $url;
  }

  /**
   * @param null $source
   * @param array $overidden_params
   * @param bool $top_nav
   * @return string
   * Adds mwbe, subvendor and datasource parameters to url.  Precedence ,$source > $overidden_params > requestparam
   */
  public static function _checkbook_append_url_params($source = null, $overidden_params = array(), $top_nav = false){
    $datasource = (isset($overidden_params['datasource'])) ? $overidden_params['datasource'] : RequestUtilities::get('datasource');
    $mwbe = (isset($overidden_params['mwbe'])) ? $overidden_params['mwbe'] : RequestUtilities::get('mwbe');
    $dashboard = (isset($overidden_params['dashboard'])) ? $overidden_params['dashboard'] : RequestUtilities::get('dashboard');

    $url = "";
    if (isset($datasource)) {
      $url = "/datasource/" . $datasource;
    } else {
      //$current_url = explode('/', $_SERVER['HTTP_REFERER']);
      $current_url =  explode('/',\Drupal::request()->query->get('q'));
      if (count($current_url) > 3 && ($current_url[3] == 'contract' && ($current_url[4] == 'search' || $current_url[4] == 'all') && $current_url[5] == 'transactions')) {
        $advanced_search = true;
      }
      if (isset($advanced_search) && !$advanced_search) {
        if ($source) {
          $source = explode("/", $source);
          if (!in_array("mwbe", $source)) {
            $url = isset($mwbe) ? "/mwbe/" . $mwbe : "";
          }
          if (!in_array("dashboard", $source)) {
            $url = isset($dashboard) ? "/dashboard/" . $dashboard : "";
          }
        } else {
          if (!$top_nav || (isset($mwbe) && RequestUtilities::get('vendor') > 0 && RequestUtilities::get('dashboard') != "ms")) {
            $url = isset($mwbe) ? "/mwbe/" . $mwbe : "";
            $url .= isset($dashboard) ? "/dashboard/" . $dashboard : "";
          }
        }
      }
    }
    return $url;
  }

  /**
   * returns the year type and year values string to be appended to the URL.
   * @param bool $applySpendingYear
   * @param bool $applyPreviousYear
   * @param bool $spendingTransactions
   * @param bool $landing_page_link
   * @return string
   */
  public static function _checkbook_project_get_year_url_param_string($applySpendingYear = false, $applyPreviousYear = false, $spendingTransactions = false, $landing_page_link = false){

    $urlPath = RequestUtilities::getCurrentPageUrl();
    $pathParams = explode('/', $urlPath);
    $byear = "/yeartype/B/year/";
    $cyear = "/yeartype/C/calyear/";
    $syear = "/syear/";

    $calyrIndex = array_search("calyear", $pathParams);
    $yeartypeIndex = array_search("yeartype", $pathParams);
    $yrIndex = array_search("year", $pathParams);
    if ($spendingTransactions) {
      $yearId = $calyrIndex ? $pathParams[($calyrIndex + 1)] : $pathParams[($yrIndex + 1)];
      if ($calyrIndex || $pathParams[$yeartypeIndex + 1] == "C") {
        return $cyear . $yearId;
      } else {
        return $byear . $yearId;
      }
    } else {
      if ($calyrIndex) {
        $year_param_name = ($landing_page_link) ? 'year' : 'calyear';
        $calYear = ($applyPreviousYear ? ($pathParams[($calyrIndex + 1)] - 1) : $pathParams[($calyrIndex + 1)]);
        return "/yeartype/C/" . $year_param_name . "/" . $calYear . ($applySpendingYear ? ('/scalyear/' . $calYear) : '');
      }

      if ($yrIndex !== FALSE && $yeartypeIndex && $pathParams[($yeartypeIndex + 1)] == "C") {
        $calYear = ($applyPreviousYear ? ($pathParams[($yrIndex + 1)] - 1) : $pathParams[($yrIndex + 1)]);
        return $cyear . $calYear . ($applySpendingYear ? ($syear . $calYear) : '');
      }

      if ($yrIndex) {
        $year = ($applyPreviousYear ? ($pathParams[($yrIndex + 1)] - 1) : $pathParams[($yrIndex + 1)]);
        return $byear . $year . ($applySpendingYear ? ($syear . $year) : '');
      }

      $curYear = CheckbookDateUtil::getCurrentFiscalYearId();
      $curYear = ($applyPreviousYear ? ($curYear - 1) : $curYear);
      return $byear . $curYear . ($applySpendingYear ? ($syear . $curYear) : '');
    }
  }

  /**
   * Forms the url parameter string for the fvendor param.
   * This is used to populate the vendor name facet for pages with sub and prime vendors
   *
   * @param null $node
   * @return string
   */
  public static function _checkbook_project_get_vendor_facet_url_param_string($node = null){
    $fvendor = null;
    $vendor = RequestUtilities::get('vendor');
    $subvendor = RequestUtilities::get('subvendor');
    $nid = $node != null ? $node->nid : null;

    switch ($nid) {
      case 720:
      case 722:
      case 721:
      case 725:
      case 726:
      case 727:
      case 728:
      case 729:
      case 781:
      case 782:
      case 783:
      case 785:
      case 786:
      case 787:
      case 788:
        if ($subvendor != null) {
          $fvendor = $subvendor;
        }
        break;
      default:
        if ($vendor != null) {
          $fvendor = $vendor;
        }
        break;
    }
    return $fvendor != null ? '/fvendor/' . $fvendor : '';
  }
}
