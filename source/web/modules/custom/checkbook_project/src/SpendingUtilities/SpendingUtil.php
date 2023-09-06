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
use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\CustomURLHelper;
use Drupal\checkbook_project\CommonUtilities\RequestUtil;
use Drupal\checkbook_project\ContractsUtilities\ContractUtil;
use Drupal\checkbook_project\MwbeUtilities\MappingUtil;
use Drupal\data_controller\Datasource\Operator\Handler\NotEqualOperatorHandler;

/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (C) 2012, 2013 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
class SpendingUtil
{

  /**
   * @var array
   */
  static $landingPageParams = array("category" => "category", "industry" => "industry", "mwbe" => "mwbe", "dashboard" => "dashboard", "agency" => "agency", "vendor" => "vendor", "subvendor" => "subvendor");
  static $spendingCategories = array(0 => 'Total', 2 => 'Payroll', 3 => 'Capital', 1 => 'Contract', 5 => 'Trust & Agency', 4 => 'Other',);

  /**
   * @param $categoryId
   * @param array $columns
   * @return array|null
   */
  public static function getSpendingCategoryDetails($categoryId, $columns = array('spending_category_id', 'display_name'))
  {
    if (!isset($categoryId)) {
      return NULL;
    }
    return _checkbook_project_querydataset('checkbook:category', $columns, array('spending_category_id' => $categoryId));
  }

  /**
   * @return string
   */
  static public function getSpendingTransactionsTitle()
  {
    $categories = self::$spendingCategories;
    $category = RequestUtilities::get('category');
    return ($categories[$category] ?? $categories[0]) . " Spending Transactions";
  }

  /** Returns Spending Category based on 'category' value from current path
   * @param string $defaultName
   * @return string
   */
  public static function getSpendingCategoryName(string $defaultName = 'Total Spending')
  {
    //For Gridview Title
    $refURL = RequestUtilities::getRefUrl();
    $categoryId = isset($refURL) ? RequestUtilities::get('category', ['q' => $refURL]) : RequestUtilities::get('category');
    if ($categoryId) {
      $categories = self::$spendingCategories;
      return $categories[$categoryId] . " Spending";
    }
    return $defaultName;
  }


  /** Returns Ytd Spending percent
   * @param $node
   * @param $row
   * @return string
   */
  public static function getPercentYtdSpending($totalAggregateColumns, $row)
  {
    if ($totalAggregateColumns > 0) {
      $ytd_spending = $row / $totalAggregateColumns * 100;
    }
    $ytd_spending = $ytd_spending < 0 ? 0.00 : $ytd_spending;
    return FormattingUtilities::custom_number_formatter_format($ytd_spending, 2, '', '%');
  }


  /**
   * Returns percent paid to sub vendors defined as:
   * The sum of all checks issued to all sub vendors associated to each agency
   * within the selected fiscal year or calendar year divided by the sum of all checks
   * issued to all vendors associated to the same agency and within the same fiscal (without payroll)
   * year or calendar multiplied by 100% and display the results as '% Paid Sub Vendors'
   *
   * @param $row
   * @return string
   */
  public static function getSubVendorsPercentPaid($row)
  {
    return self::calculatePercent($row['ytd_spending_sub_vendors'], $row['check_amount_sum_no_payroll@checkbook:spending_data']);
  }

  /**
   * Given a numerator and denominator, calculates the percent.
   * Returns value with up to 2 decimal places.
   * If the value is negative, 0 is returned.
   *
   * @param $numerator
   * @param $denominator
   * @return string
   */
  public static function calculatePercent($numerator, $denominator)
  {
    if ($denominator > 0) {
      $results = $numerator / $denominator * 100;
    }
    $results = $results < 0 ? 0.00 : $results;
    return FormattingUtilities::custom_number_formatter_format($results, 2, '', '%');
  }

  /**
   * Checks to see if this is from the Advanced search page,
   * if so, need to append the data source but not the m/wbe parameter.
   */
  public static function getDataSourceParams()
  {
    if (self::isAdvancedSearchResults()) {
      $data_source = RequestUtilities::get("datasource");
      return isset($data_source) ? "/datasource/checkbook_oge" : "";
    }
    return CustomURLHelper::_checkbook_append_url_params();
  }

  /**
   * Returns true if this is from spending advanced search for citywide
   * OR if this is from the transaction page for M/WBE.
   */
  public static function showMwbeFields()
  {
    $is_mwbe = _checkbook_check_is_mwbe_page();
    $is_mwbe = $is_mwbe || (!_checkbook_check_isEDCPage() && self::isAdvancedSearchResults());
    return $is_mwbe;
  }

  /**
   * Returns true if this is from spending advanced search
   */
  public static function isAdvancedSearchResults()
  {
    return !self::isSpendingLanding();
  }

  /**
   * Returns true if this is the spending landing page
   */
  public static function isSpendingLanding()
  {
    $url_ref = $_SERVER['HTTP_REFERER'];
    $match_landing = '"/spending_landing/"';
    return preg_match($match_landing, $url_ref);
  }

  /**
   * Spending transaction page should be shown for citywide, oge
   * @return bool
   */
  public static function showSpendingTransactionPage()
  {
    $subvendor_exist = _checkbook_check_is_sub_vendor_page();
    $ma1_mma1_contracts_exist = self::_checkbook_project_ma1_mma1_exist();
    $edc_records_exist = _checkbook_check_isEDCPage() && _checkbook_project_recordsExists(6);
    $mwbe_records_exist = _checkbook_check_is_mwbe_page() && !$subvendor_exist && _checkbook_project_recordsExists(706);
    $citywide_exist = !$subvendor_exist && !$mwbe_records_exist && !$edc_records_exist && _checkbook_project_recordsExists(6);

    if ($ma1_mma1_contracts_exist || $subvendor_exist) {
      return false;
    }
    return ($edc_records_exist || $mwbe_records_exist || $citywide_exist);
  }

  /**
   * Spending transaction no results page should be shown for citywide, oge
   * @return bool
   */
  public static function showNoSpendingTransactionPage()
  {
    $subvendor_exist = _checkbook_check_is_sub_vendor_page();
    $ma1_mma1_contracts_exist = self::_checkbook_project_ma1_mma1_exist();
    $edc_records_exist = _checkbook_check_isEDCPage() && _checkbook_project_recordsExists(6);

    if ($ma1_mma1_contracts_exist || $subvendor_exist) {
      return false;
    }
    return $subvendor_exist || $ma1_mma1_contracts_exist || $edc_records_exist;
  }

  /**
   * @param string $widgetTitle
   * @return string
   */
  public static function getTransactionPageTitle($widgetTitle = '')
  {
    $catName = self::getTransactionPageCategoryName();
    $dashboard_title = RequestUtil::getDashboardTitle();
    $dashboard = RequestUtilities::getTransactionsParams('dashboard');
    $category = RequestUtilities::getTransactionsParams('category');
    $smnid = RequestUtilities::getTransactionsParams('smnid');

    //Sub Vendors Exception
    if (($widgetTitle == "Sub Vendors" || $widgetTitle == "Sub Vendor") && $dashboard == "ss") {
      $dashboard_title = MappingUtil::getCurrenEthnicityName();
    } elseif (($widgetTitle == "Contracts" || $widgetTitle == "Contract") && $category == 1) {//Contract Exception
      $catName = "Spending";
    }

    if (RequestUtilities::getTransactionsParams('mocs') == 'Yes') {
      if ($category != 1) {
        return "MOCS Registered COVID-19 Contract " . $catName . " Transactions";
      } else {
        return "MOCS Registered COVID-19 Contract Spending Transactions";
      }
    } //Visualization - Sub Vendors (M/WBE) "Ethnicity" Exception
    elseif ($smnid == "723" && $dashboard == "sp") {
      $dashboard_title = MappingUtil::getCurrenEthnicityName();
      return $widgetTitle . " " . $dashboard_title . " " . $catName . " Transactions";
    } //Visualization - Sub Vendors (M/WBE) "Ethnicity" Exception
    elseif ($smnid == "723" && $dashboard == "ss") {
      $dashboard_title = MappingUtil::getCurrenEthnicityName();
    }
    return $dashboard_title . " " . $widgetTitle . " " . $catName . " Transactions";
  }

  /**
   * Returns Spending Category based on 'category' value from current path
   * @param string $defaultName
   * @return string
   */
  public static function getTransactionPageCategoryName($defaultName = 'Total Spending')
  {
    $categoryId = RequestUtilities::getTransactionsParams('category');
    $category_name = $defaultName;
    if (isset($categoryId)) {
      $categoryDetails = SpendingUtil::getSpendingCategoryDetails($categoryId, 'display_name');
      $category_name = is_array($categoryDetails) ? $categoryDetails[0]['display_name'] : $defaultName;
    }
    return $category_name;
  }

  /**
   * @param $widgetTitle
   * @return string
   */
  public static function getSpentToDateTitle($widgetTitle)
  {
    $dashboard = RequestUtil::getDashboardTitle();
    $contractTitle = ContractUtil::getContractTitle();

    $dashboard_param = RequestUtilities::get('dashboard');
    $smnid = RequestUtilities::getTransactionsParams('smnid');
    $status = RequestUtilities::getTransactionsParams('contstatus');
    if ($smnid == 720) {
      if ($dashboard_param == "ms") {
        $dashboard = "M/WBE";
      } elseif ($dashboard_param == "ss") {
        $dashboard = "";
      }
    } //Visualization - M/WBE (Sub Vendors) Exception
    elseif ($smnid == "subven_mwbe_contracts_visual_2" && $dashboard_param == "ms" || $smnid == "mwbe_contracts_visual_2" || $smnid == "subvendor_contracts_visual_1" && $dashboard_param == "ss" || $smnid == "subvendor_contracts_visual_1" && $dashboard_param == "sp") {
      $dashboard = MappingUtil::getCurrenEthnicityName();
    }
    //Visualization - "Ethnicity" Spending by Active Expense Contracts Transactions Exception
    if ($status == 'A') {
      $bottomNavigation = "Total Active Sub Vendor Contracts";
    } else {
      $bottomNavigation = "New Sub Vendor Contracts by Fiscal Year";
    }

    if ($smnid == 721 || $smnid == 720 || $smnid == 781 || $smnid == 784) {
      $widgetTitle = 'Spending';
    }
    if ($smnid == 'subvendor_contracts_visual_1' && $dashboard_param == 'sp') {
      return (MappingUtil::getCurrenEthnicityName() . " Sub Vendors Spending by <br />" . $bottomNavigation);
    }
    if ($smnid == 'subven_mwbe_contracts_visual_2' && $dashboard_param == 'ms') {

      return (MappingUtil::getCurrenEthnicityName() . " Sub Spending by <br />" . $bottomNavigation);
    }

    if ($dashboard_param == 'ss' || $dashboard_param == 'ms' || $dashboard_param == 'sp') {
      //Title for Contract Spending Transactions page (Total Spent to Date link under 'Sub Vendor Information' section)
      $contract_num = RequestUtilities::getTransactionsParams('contnum');
      if ($smnid == 721 && preg_match("/contract\/spending\/transactions/", RequestUtilities::getCurrentPageUrl()) && isset($contract_num)) {
        return "Total Sub Vendors Spending Transactions";
      }
      if ($dashboard_param == 'ms' || $dashboard_param == 'sp') {
        if ($status == 'A') {
          $bottomNavigation = "Total Active M/WBE Sub Vendor Contracts";
        } else {
          $bottomNavigation = "New M/WBE Sub Vendor Contracts by Fiscal Year";
        }
      }
      return ($widgetTitle . " by  " . $bottomNavigation . " " . "Transactions");
    }

    return ($dashboard . " " . $widgetTitle . " " . $contractTitle . " Contracts Transactions");
  }


  /**
   * @return bool
   */
  public static function _mwbe_spending_use_subvendor()
  {
    if (RequestUtilities::get('vendor') > 0 || RequestUtilities::get('mwbe') == '7' || RequestUtilities::get('mwbe') == '11') {
      return true;
    } else {
      return false;
    }
  }

  /** Prepares Spending bottom navigation filter
   * @param $page
   * @param $category
   * @return string
   */
  public static function prepareSpendingBottomNavFilter($page, $category)
  {
    $current_path = Drupal::service('path.current')->getPath();
    $result = Drupal::service('path_alias.manager')->getAliasByPath($current_path);
    $pathParams = explode('/', $result);
    $url = "/" . $page;
    if (strlen($category) > 0) {
      $url .= "/category/" . $category;
    }
    $url .= CustomURLHelper::_checkbook_append_url_params();
    $allowedFilters = array("year", "calyear", "agency", "yeartype", "vendor", "industry", "mwbe", "dashboard");
    for ($i = 0; $i < count($pathParams); $i++) {
      if (in_array($pathParams[$i], $allowedFilters)) {
        $url .= '/' . $pathParams[$i] . '/' . $pathParams[($i + 1)];
      }
      $i++;
    }
    return $url;
  }

  /**
   * Handle calendar/fiscal year parameters in centralized function for spending
   *
   * @param $node
   * @param $parameters
   * @return mixed
   */
  public static function _checkbook_project_adjust_date_spending_parameter_filters(&$node, &$parameters)
  {
    $yearType = $parameters['year_type'][0];
    $year = $parameters['year_id'][0];

    if (isset($yearType)) {
      if ($yearType == 'B')
        $parameters['check_eft_issued_nyc_year_id'] = $year;
      else if ($yearType == 'C')
        $parameters['calendar_fiscal_year_id'] = $year;
    }

    unset($parameters['year_type']);
    unset($parameters['year_id']);

    //Adjust Certification parameters
    $parameters = ContractUtil::adjustCertificationFacetParameters($node, $parameters);

    return $parameters;
  }

  /**
   * @param $node
   * @param $parameters
   *
   * @return mixed
   */
  public static function _checkbook_project_adjust_spending_parameter_filters(&$node, &$parameters)
  {
    if (isset($parameters['ctx.ctx.vendor_id']) || isset($parameters['ctx.ctx.document_agency_id']) || isset($parameters['ctx.ctx.award_method_id']) || isset($parameters['ctx.ctx.award_size_id']) || isset($parameters['ctx.ctx.industry_type_id'])) {
      $year = $parameters['check_eft_issued_nyc_year_id'];
      if (isset($year)) {
        $parameters['ctx.ctx.fiscal_year_id'] = $year;
        $parameters['ctx.ctx.type_of_year'] = 'B';
      }
    }
    $dtsmnid = RequestUtilities::getTransactionsParams('dtsmnid');
    if ($dtsmnid == 20) {//From spending landing page
      $data_controller_instance = data_controller_get_operator_factory_instance();
      $parameters['agreement_id'] = $data_controller_instance->initiateHandler(NotEqualOperatorHandler::$OPERATOR__NAME, NULL);
      $parameters['contract_number'] = $data_controller_instance->initiateHandler(NotEqualOperatorHandler::$OPERATOR__NAME, NULL);
    }
    return $parameters;
  }

  /**
   * @return bool
   */
  public static function _checkbook_project_ma1_mma1_exist()
  {
    //$url = $_GET['q'];
    //$urlPath = drupal_get_path_alias($url);
    $url = \Drupal::service('path.current')->getPath();
    $urlPath = \Drupal::service('path_alias.manager')->getAliasByPath($url);
    $pathParams = explode('/', $urlPath);
    $index = array_search('contnum', $pathParams);

    if ($index !== false) {
      $value = $pathParams[($index + 1)];
      $doc_type1 = strtolower(substr($value, 0, 3));
      $doc_type2 = strtolower(substr($value, 0, 4));
      if ('ma1' == $doc_type1 || 'mma1' == $doc_type2) {
        return true;
      }
    }

    return false;
  }

  public static function subvendorStaticText($node)
  {
    $agency = RequestUtilities::getTransactionsParams('agency');
    $year = RequestUtilities::getTransactionsParams('year');
    $yearType =RequestUtilities::getTransactionsParams('yeartype');
    $where_filters = array();
    //var_dump($node->widgetConfig->requestParams);
    foreach ($node->widgetConfig->requestParams as $param => $value) {
      $where_filters[] = _widget_build_sql_condition('s0.' . $param, $value);
    }
      if (count($where_filters) > 0) {
        $where_filter = ' where ' . implode(' and ', $where_filters);
      }
      $sql = "SELECT j1.agency_name AS agency_agency_agency_name,
       j.yeartype_yeartype,
       j.year_year,
       j.agency_agency,
       j.sub_vendor_count,
       j.ytd_spending_sub_vendors,
       j.ytd_spending_vendors,
       r2.check_amount_sum_checkbook_spending_data,
       r2.check_amount_sum_no_payroll_checkbook_spending_data
  FROM (SELECT s0.type_of_year AS yeartype_yeartype,
               s0.year_id AS year_year,
               s0.agency_id AS agency_agency,
               COUNT(DISTINCT vendor_id) AS sub_vendor_count,
               SUM(total_spending_amount) AS ytd_spending_sub_vendors,
               SUM(total_spending_amount) AS ytd_spending_vendors
          FROM aggregateon_subven_spending_coa_entities s0".
         $where_filter."
         GROUP BY s0.agency_id, s0.year_id, s0.type_of_year) j
       LEFT OUTER JOIN (SELECT s0.agency_id AS agency_checkbook_spending_data_agency,
                               s0.year_id AS year_checkbook_spending_data_year,
                               s0.type_of_year AS yeartype_checkbook_spending_data_yeartype,
                               SUM(total_spending_amount) AS check_amount_sum_checkbook_spending_data,
                               SUM(CASE WHEN spending_category_id !=2 THEN total_spending_amount ELSE 0 END) AS check_amount_sum_no_payroll_checkbook_spending_data
                          FROM aggregateon_spending_coa_entities s0
                         WHERE s0.agency_id = ".$agency."
                           AND s0.year_id = ".$year."
                           AND s0.type_of_year = '".$yearType."'
                         GROUP BY s0.agency_id, s0.year_id, s0.type_of_year) r2 ON r2.agency_checkbook_spending_data_agency = j.agency_agency AND r2.year_checkbook_spending_data_year = j.year_year AND r2.yeartype_checkbook_spending_data_yeartype = j.yeartype_yeartype
       LEFT OUTER JOIN ref_agency j1 ON j1.agency_id = j.agency_agency
       ORDER BY ytd_spending_sub_vendors DESC
       LIMIT 1";

      $node->data = _checkbook_project_execute_sql($sql, 'main', 'checkbook');
      $node->data['sub_vendors_percent_paid'] = self::calculatePercent($node->data[0]['ytd_spending_sub_vendors'], $node->data[0]['check_amount_sum_no_payroll_checkbook_spending_data']);
      return $node;
    }

}
