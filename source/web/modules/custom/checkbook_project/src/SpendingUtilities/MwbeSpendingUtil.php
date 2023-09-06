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

use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\checkbook_project\CommonUtilities\RequestUtil;
use Drupal\checkbook_project\MwbeUtilities\MappingUtil;
use Drupal\checkbook_project\MwbeUtilities\VendorType;
use Drupal\data_controller\Common\Object\Exception\IllegalArgumentException;
use Drupal\data_controller\Datasource\Operator\Handler\NotEqualOperatorHandler;
use ReflectionException;

class MwbeSpendingUtil
{
  /**
   ** Transaction page from M/WBE Dashboard landing page
   * Top 10 agencies widget (759) - sub and prime data - reverted in NYCCHKBK-4798
   * Top 10 Sub Vendors widget (763) - sub data
   * All Others widgets - prime data
   *
   * @param $node
   * @param $parameters
   * @return mixed
   * @throws IllegalArgumentException
   * @throws ReflectionException
   */
  public static function _checkbook_project_adjust_mwbe_spending_parameter_filters(&$node, &$parameters)
  {
    $dtsmnid = RequestUtilities::getTransactionsParams('dtsmnid');
    $smnid = RequestUtilities::getTransactionsParams('smnid');
    $month = RequestUtilities::getTransactionsParams('month');
    $nid = $dtsmnid ?? $smnid;
    $current_node_nid = $node->nid;

    $magid = RequestUtilities::getTransactionsParams('magid');
    $industry = RequestUtilities::getTransactionsParams('industry');
    if ($smnid == 764 && $magid != null && $industry != null) {
      $parameters['master_contract_industry_type_id'] = $parameters['industry_type_id'];
      unset($parameters['industry_type_id']);
    }

    if (isset($parameters['vendor_id@checkbook:contracts_spending_transactions']) || isset($parameters['document_agency_id@checkbook:contracts_spending_transactions']) || isset($parameters['award_method_id@checkbook:contracts_spending_transactions'])
      || isset($parameters['award_size_id@checkbook:contracts_spending_transactions']) || isset($parameters['industry_type_id@checkbook:contracts_spending_transactions'])
    ) {
      $year = $parameters['check_eft_issued_nyc_year_id'];
      if (isset($year)) {
        $parameters['fiscal_year_id@checkbook:contracts_spending_transactions'] = $year;
        $parameters['type_of_year@checkbook:contracts_spending_transactions'] = 'B';
      }
    }

    if ($dtsmnid == 20) {//From spending landing page
      $data_controller_instance = data_controller_get_operator_factory_instance();
      $parameters['agreement_id'] = $data_controller_instance->initiateHandler(NotEqualOperatorHandler::$OPERATOR__NAME, NULL);
      $parameters['contract_number'] = $data_controller_instance->initiateHandler(NotEqualOperatorHandler::$OPERATOR__NAME, NULL);
    }

    if (isset($parameters['vendor_type'])) {
      $parameters['vendor_type'] = VendorType::getVendorTypeValue($parameters['vendor_type']);
    } else {
      switch ($nid) {
        case 763:
          $parameters['is_prime_or_sub'] = array('S');
          break;
        default:
          if (RequestUtilities::get('dashboard') == null) {
            //Static Amount for Citywide = Prime Only
            if ($current_node_nid == 775 || ($current_node_nid == 706 && isset($month)))
              $parameters['is_prime_or_sub'] = array('P');
            //Static Amount for Citywide from the month visualization = Prime Only
            else
              $parameters['is_prime_or_sub'] = array('P', 'S');
          } else {
            $parameters['is_prime_or_sub'] = array('P');
          }
          break;
      }
    }

    if ($dtsmnid == 764) {
      $parameters['contract_document_code'] = array('CT1', 'CTA1', 'POD', 'POC', 'PCC1', 'DO1', 'MA1', 'MMA1', 'PON1');
    }
    return $parameters;
  }

  /**
   * Returns M/WBE category for the given vendor id in the given year and year type
   * @param $vendor_id
   * @param $agency_id
   * @param $year_id
   * @param $year_type
   * @param $is_prime_or_sub
   * @return null
   */
  public static function getLatestMwbeCategoryByVendor($vendor_id, $agency_id = null, $year_id = null, $year_type = null, $is_prime_or_sub = "P")
  {
    static $spending_vendor_latest_mwbe_category;
    if ($agency_id == null) {
      $agency_id = RequestUtilities::get('agency');
    }
    if ($year_id == null) {
      $year_id = RequestUtilities::get('year');
    }

    if ($year_type == null) {
      $year_type = RequestUtilities::get('yeartype');
    }
    if (!isset($spending_vendor_latest_mwbe_category)) {
      $query = "SELECT vendor_id, agency_id, year_id, type_of_year, minority_type_id, is_prime_or_sub
                      FROM spending_vendor_latest_mwbe_category
                      WHERE minority_type_id IN (" . MappingUtil::getTotalMinorityIds() . ") AND year_id = '" . $year_id . "' AND type_of_year = '" . $year_type . "'
                      GROUP BY vendor_id, agency_id, year_id, type_of_year, minority_type_id, is_prime_or_sub";

      $results = _checkbook_project_execute_sql_by_data_source($query);
      foreach ($results as $row) {
        if (isset($row['agency_id'])) {
          $spending_vendor_latest_mwbe_category[$row['vendor_id']][$row['agency_id']][$row['is_prime_or_sub']]['minority_type_id'] = $row['minority_type_id'];
        } else {
          $spending_vendor_latest_mwbe_category[$row['vendor_id']][$row['is_prime_or_sub']]['minority_type_id'] = $row['minority_type_id'];
        }
      }
    }
    return isset($agency_id)
      ? $spending_vendor_latest_mwbe_category[$vendor_id][$agency_id][$is_prime_or_sub]['minority_type_id']
      : $spending_vendor_latest_mwbe_category[$vendor_id][$is_prime_or_sub]['minority_type_id'];
  }

  /**
   * Returns latest M/WBE category Name for the given vendor id in the given year and year type
   *
   * @param $vendor_id
   * @param $year_id
   * @param $year_type
   * @param string $is_prime_or_sub
   * @return string|null
   */
  public static function getLatestMwbeCategoryTitleByVendor($vendor_id, $year_id = NULL, $year_type = NULL, $is_prime_or_sub = "P")
  {
    if ($year_id == null) {
      $year_id = RequestUtilities::get('year');
    }

    if ($year_type == null) {
      $year_type = RequestUtilities::get('yeartype');
    }

    $query = "SELECT minority_type_id FROM(
            SELECT a.*, row_number() OVER (PARTITION BY a.vendor_id, a.year_id, a.type_of_year ORDER BY chk_date DESC) AS flag FROM(
                SELECT a.vendor_id,
                    a.year_id,
                    a.type_of_year,
                    CASE WHEN a.minority_type_id IS NULL OR a.minority_type_id = 11 THEN 7 ELSE a.minority_type_id END minority_type_id,
                    MAX(d.check_eft_issued_date ) AS chk_date
                FROM aggregateon_mwbe_spending_coa_entities a
                JOIN disbursement_line_item_details d ON a.vendor_id = d.vendor_id AND a.agency_id = d.agency_id
                        AND a.minority_type_id = d.minority_type_id AND a.year_id = d.check_eft_issued_nyc_year_id
                WHERE a.vendor_id = " . $vendor_id . " AND a.year_id = " . $year_id . " AND a.type_of_year = '" . $year_type . "'
                GROUP BY CASE WHEN a.minority_type_id IS NULL OR a.minority_type_id = 11 THEN 7 ELSE a.minority_type_id END,
                a.vendor_id, a.year_id, a.type_of_year ) a ) a WHERE flag = 2 AND a.minority_type_id IN (" . MappingUtil::getTotalMinorityIds() . ")
            UNION
            SELECT DISTINCT minority_type_id
            FROM spending_vendor_latest_mwbe_category
            WHERE vendor_id = " . $vendor_id . " AND is_prime_or_sub = '" . $is_prime_or_sub . "' AND type_of_year = '" . $year_type . "'
                  AND year_id = " . $year_id . " AND minority_type_id <> 7 ";
    $results = _checkbook_project_execute_sql_by_data_source($query);
    if ($results[0]['minority_type_id'] != '') {
      return $results[0]['minority_type_id'];
    } else {
      return null;
    }
  }

  /**
   * Returns M/WBE Category Link Url
   *
   * NYCCHKBK-4676:
   *   Do not hyperlink the M/WBE category within Top 5 Sub vendors widget if you are looking at prime data[M/WBE Featured Dashboard].
   *   Do not hyperlink the M/WBE category within Top 5 Prime vendors widget if you are looking at sub data[M/WBE(sub vendors) featured dashboard].
   *   The Details link from these widgets, also should follow same rule of not hyperlinking the M/WBE category.
   * NYCCHKBK-4798:
   *   From Top 5 Sub vendors widget, link should go to SP to maintain correct data
   *
   * @param $node
   * @param $row
   * @return string
   */
  public static function getMWBECategoryLinkUrl($node, $row)
  {
    $dtsmnid = RequestUtilities::get("dtsmnid");
    $smnid = RequestUtilities::get("smnid");
    $dashboard = RequestUtilities::get("dashboard");

    if ($dtsmnid != null) {
      $nid = $dtsmnid;
    } else if ($smnid != null) {
      $nid = $smnid;
    } else {
      $nid = $node->nid;
    }

    if ($dashboard == null) {
      $dashboard = ($row['is_sub_vendor'] == "Yes") ? "ms" : "mp";
    }
    $dashboard = (preg_match('/p/', $dashboard)) ? "mp" : "ms";
    $mwbe = $row["minority_type_id"] ?? $row["minority_type_minority_type"];
    //From sub vendors widget
    if ($nid == 719) {
      $dashboard = "sp";
    }
    $custom_params = array(
      'dashboard' => $dashboard,
      'mwbe' => $mwbe == 4 || $mwbe == 5 ? '4~5' : $mwbe
    );
    return  SpendingUrlHelper::getLandingPageWidgetUrl($custom_params);
  }

  /**
   * Returns true/false if M/WBE Category should be a link
   *
   * @param $node
   * @param $row
   * @return bool
   */
  public static function showMWBECategoryLink($node, $row)
  {
    $dtsmnid = RequestUtilities::get("dtsmnid");
    $smnid = RequestUtilities::get("smnid");

    return !RequestUtil::isNewWindow() &&
      MappingUtil::isMWBECertified(array($row['minority_type_id'])) &&
      $dtsmnid != 763 && $smnid != 763 && $dtsmnid != 747 && $smnid != 747 && $dtsmnid != 717 && $smnid != 717;
  }

  /**
   * Returns M/WBE Category Link Url for the advanced search page
   * @param $node
   * @param $row
   * @return string
   */
  public static function getAdvancedSearchMWBECategoryLinkUrl($node, $row)
  {
    $mwbe = $row["minority_type_id"] ?? $row["minority_type_minority_type"];
    $custom_params = array(
      'category' => $row["spending_category_id"],
      'dashboard' => $row["is_sub_vendor"] == "No" ? "mp" : "ms",
      'mwbe' => $mwbe == 4 || $mwbe == 5 ? '4~5' : $mwbe,
      'year' => $row['check_eft_issued_nyc_year_id'] ?? CheckbookDateUtil::getCurrentFiscalYearId()
    );
    return '/' . SpendingUrlHelper::getLandingPageWidgetUrl($custom_params);
  }

  /**
   * @param $year
   * @param $yeartype
   * @param string $non_mwbe_spending_prime
   * @param string $mwbe_spending_prime
   * @return string
   */
  public static function getMWBENYCLegend($year, $yeartype, int $non_mwbe_spending_prime = 0, int $mwbe_spending_prime = 0)
  {

    $where_filter = "where year_id = $year and type_of_year = '$yeartype' ";
    $prime_sql = 'select rm.minority_type_id, rm.minority_type_name , sum(total_spending_amount) total_spending
	    from aggregateon_mwbe_spending_coa_entities a1
	    join ref_minority_type rm on rm.minority_type_id = a1.minority_type_id
	   ' . $where_filter . '
	    group by rm.minority_type_id, rm.minority_type_name  ';

    $prime_spending_rows = _checkbook_project_execute_sql($prime_sql);
    foreach ($prime_spending_rows as $row) {
      switch ($row['minority_type_id']) {
        case '2':
        case '3':
        case '4':
        case '5':
        case '9':
          $mwbe_spending_prime += (int)$row['total_spending'];
          break;
        case '7':
          $non_mwbe_spending_prime += (int)$row['total_spending'];
          break;
      }
    }

    $mwbe_share = FormattingUtilities::custom_number_formatter_format($mwbe_spending_prime ? ($mwbe_spending_prime) / ($non_mwbe_spending_prime + $mwbe_spending_prime) * 100 : 0, 1, null, '%');
    $mwbe_spending = FormattingUtilities::custom_number_formatter_format($mwbe_spending_prime, 2, '$');
    $non_mwbe = FormattingUtilities::custom_number_formatter_format($non_mwbe_spending_prime, 2, '$');

    return '<div class="chart-nyc-legend">
    			<div class="legend-title"><span>NYC Total M/WBE</span></div>
    			<div class="legend-item"><span>M/WBE Share: ' . $mwbe_share . ' </span></div>
    			<div class="legend-item"><span>M/WBE Spending: ' . $mwbe_spending . ' </span></div>
    			<div class="legend-item"><span>Non M/WBE: ' . $non_mwbe . '</span></div>
    			</div>
    			';

  }

  /**
   * Returns the legend displayed in the Sub Vendors (M/WBE) dashboard for the "Sub Spending by M/WBE Share" visualization
   * @param $year
   * @param $yeartype
   * @return string
   */
  public static function getSubMWBENYCLegend($year, $yeartype)
  {
    $where_filter = "where year_id = $year and type_of_year = '$yeartype' ";
    $sql = 'select rm.minority_type_id, rm.minority_type_name , sum(total_spending_amount) total_spending
	    from aggregateon_subven_spending_coa_entities a1
	    join ref_minority_type rm on rm.minority_type_id = a1.minority_type_id
	   ' . $where_filter . '
	    group by rm.minority_type_id, rm.minority_type_name  ';

    $spending_rows = _checkbook_project_execute_sql($sql);
    foreach ($spending_rows as $row) {
      switch ($row['minority_type_id']) {
        case '2':
        case '3':
        case '4':
        case '5':
        case '9':
          $mwbe_spending_sub += $row['total_spending'];
          break;
        case '7':
          $non_mwbe_spending_sub += $row['total_spending'];
          break;

      }
    }
    $mwbe_share = FormattingUtilities::custom_number_formatter_format($mwbe_spending_sub ? ($mwbe_spending_sub) / ($non_mwbe_spending_sub + $mwbe_spending_sub) * 100 : 0, 1, null, '%');
    $mwbe_spending = FormattingUtilities::custom_number_formatter_format($mwbe_spending_sub, 2, '$');
    $non_mwbe = FormattingUtilities::custom_number_formatter_format($non_mwbe_spending_sub, 2, '$');

    return '<div class="chart-nyc-legend">
    			<div class="legend-title"><span>NYC Total M/WBE</span></div>
    			<div class="legend-item"><span>M/WBE Share: ' . $mwbe_share . ' </span></div>
    			<div class="legend-item"><span>M/WBE Spending: ' . $mwbe_spending . ' </span></div>
    			<div class="legend-item"><span>Non M/WBE: ' . $non_mwbe . '</span></div>
    			</div>
    			';
  }

  /**
   * @return bool
   */
  public static function _show_mwbe_custom_legend()
  {
    $mwbe_cats = RequestUtilities::get('mwbe');
    if (($mwbe_cats == '4~5' || $mwbe_cats == '4' || $mwbe_cats == '5' || $mwbe_cats == '2' || $mwbe_cats == '3' || $mwbe_cats == '9') && !(RequestUtilities::get('vendor') > 0)) {
      return true;
    }

    if (!(RequestUtilities::get('vendor') > 0) && (RequestUtilities::get('agency') > 0 || RequestUtilities::get('industry') > 0)) {
      return true;
    }
    return false;
  }
}
