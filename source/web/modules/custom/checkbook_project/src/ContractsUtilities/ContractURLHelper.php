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

namespace Drupal\checkbook_project\ContractsUtilities;

use Drupal\checkbook_infrastructure_layer\Constants\Common\PageType;
use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\checkbook_project\CommonUtilities\CustomURLHelper;
use Drupal\checkbook_project\CommonUtilities\RequestUtil;
use Drupal\checkbook_project\MwbeUtilities\VendorType;
use Drupal\checkbook_services\Contracts\ContractsUrlService;
use Drupal\checkbook_services\VendorUtil\PrimeVendorService;

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

class ContractURLHelper {

  static array $landingPageParams = array("status" => "status", "vendor" => "vendor", "agency" => "agency", "awdmethod" => "awdmethod", "cindustry" => "cindustry", "csize" => "csize","mwbe" => "mwbe" , "dashboard"=> "dashboard");
  static array $transactionPageParams = array("status" => "status", "vendor" => "cvendor", "agency" => "cagency", "awdmethod" => "awdmethod", "cindustry" => "cindustry", "csize" => "csize" ,"mwbe" => "mwbe" , "dashboard"=> "dashboard");

  /**
   * @param $row
   * @param $node
   * @param bool $parent
   * @param null $original_agreement_id
   * @return null|string
   */
  public static function prepareExpenseContractLink($row, $node, $parent = false, $original_agreement_id = null): ?string {
    if (isset($row['contract_original_agreement_id'])){
      $row['original_agreement_id'] = $row['contract_original_agreement_id'];
    }
    $row['original_agreement_id'] = ($original_agreement_id) ?: ($row['original_agreement_id'] ?? null);
    $effective_end_year_id_row = isset($row['effective_end_year_id']) ? $row['effective_end_year_id'] : null;
    $effective_year_id = ContractsUrlService::applyYearParameter($effective_end_year_id_row);
    if ($parent && strlen($row['master_contract_number']) > 0) {
        $agrParamName = 'magid';
        $docTypeStr = substr($row['master_contract_number'], 0, 3);
        $docType = ($docTypeStr == 'MA1') ? 'MA1' : 'MMA1';
        $row['original_agreement_id'] = $row['master_agreement_id'] ?? null;
        $row['contract_number'] = $row['master_contract_number'] ?? null;
    }
    elseif ($parent && strlen($row['master_contract_number']) == 0) {
      return "N/A";
    }
    else {
      $docType = $row['document_code@checkbook:ref_document_code'] ?? ContractUtil::_get_contract_type($row['contract_number']);
      $agrParamName = in_array($docType, array('MMA1', 'MA1')) ? 'magid' : 'agid';
    }

    if (RequestUtil::isExpandBottomContainer()) {
      $link = '<a href="/contract_details/' . $agrParamName . '/' . $row['original_agreement_id'] . '/doctype/' . $docType . CustomURLHelper::_checkbook_append_url_params() . '" class=bottomContainerReload>' . $row['contract_number'] . '</a>';
    }
    else {
      $link = '<a href="/contracts_landing'
        . RequestUtilities::buildUrlFromParam('contstatus|status')
        . CustomURLHelper::_checkbook_append_url_params()
        . (isset($row['type_of_year@checkbook:contracts_coa_aggregates']) ? ('/yeartype/B/year/' . $row['fiscal_year_id@checkbook:contracts_coa_aggregates']) : (CustomURLHelper::_checkbook_project_get_year_url_param_string()))
        . ((_checkbook_check_isEDCPage()) ? '/agency/' . ($row['agency_id'] ?? null) : '')
        . '?expandBottomContURL=/contract_details/' . $agrParamName . '/' . $row['original_agreement_id'] . '/doctype/' . $docType . CustomURLHelper::_checkbook_append_url_params()
        . '">' . $row['contract_number'] . '</a>';
    }
    if (isset($effective_year_id)) {
      $link =  preg_replace("/\/year\/\d+/", $effective_year_id, $link) ;
    }
    return $link;
  }

  /**
   * @param $row
   * @param $node
   * @return null|string
   */
  public static function prepareRevenueContractLink($row, $node): ?string {
    $docType = $row['document_code'];
    if ($docType == "RCT1") {
      $page = "/contracts_revenue_landing";
    } else {
      $page = "/contracts_landing";
    }

    $effective_end_year_id_row = isset($row['effective_end_year_id']) ? $row['effective_end_year_id'] : null;
    $effective_year_id = ContractsUrlService::applyYearParameter($effective_end_year_id_row);
    $agrParamName = 'magid';//in_array($docType, array('MMA1','MA1')) ? 'magid' : 'agid';
    $agid = $row['original_agreement_id'] ?? $row['contract_original_agreement_id'];

    if (RequestUtil::isExpandBottomContainer()) {
      $link = '<a href=/contract_details/' . $agrParamName . '/' . $agid . '/doctype/' . $docType . ' class=bottomContainerReload>' . $row['contract_number'] . '</a>';
    }
    else {
      $link = '<a href='
        . $page . RequestUtilities::buildUrlFromParam('contstatus|status')
        . (isset($row['type_of_year@checkbook:contracts_coa_aggregates']) ? '/yeartype/B/year/' . $row['fiscal_year_id@checkbook:contracts_coa_aggregates'] : (CustomURLHelper::_checkbook_project_get_year_url_param_string()))
        . '?expandBottomContURL=/contract_details/' . $agrParamName . '/' . $agid . '/doctype/' . $docType
        . ' >' . $row['contract_number'] . '</a>';

    }

    if (PageType::getCurrent() == PageType::ADVANCED_SEARCH_PAGE) {
      $link =  preg_replace("/\/year\/\d+/", $effective_year_id, $link) ;
    }

    return $link;
  }

  /**
   * @param $row
   * @param $node
   * @return null|string
   */
  public static function preparePendingContractLink($row, $node): ?string {

    $agreementId = $row['original_agreement_id'];
    if (!isset($agreementId)) {//No link if mag is not present
      return '<a class="bottomContainerReload" href = "/pending_contract_transactions/contract/' . $row['fms_contract_number'] . '/version/' . $row['document_version'] . '">' . $row['contract_number'] . '</a>';
    }

    $link = NULL;
    $docType = $row['document_code@checkbook:ref_document_code'];
    $agrParamName = in_array($docType, array('MMA1', 'MA1', 'RCT1')) ? 'magid' : 'agid';

    if (RequestUtil::isExpandBottomContainer()) {
      $link = '<a href=/contract_details/' . $agrParamName . '/' . $row['original_agreement_id'] . '/doctype/' . $docType . ' class=bottomContainerReload>' . $row['contract_number'] . '</a>';
    }
    else {
      $link = '<a href=/' . ($docType == 'RCT1' ? 'contracts_pending_rev_landing' : 'contracts_pending_exp_landing') . '/'
      . RequestUtilities::buildUrlFromParam('contstatus|status')
      . (isset($row['type_of_year@checkbook:contracts_coa_aggregates']) ? '/yeartype/B/year/' . $row['fiscal_year_id@checkbook:contracts_coa_aggregates']: CustomURLHelper::_checkbook_project_get_year_url_param_string())
      . '?expandBottomContURL=/contract_details/' . $agrParamName . '/' . $row['original_agreement_id'] . '/doctype/' . $docType
      . ' >' . $row['contract_number'] . '</a>';
    }

    return $link;
  }

  /**
   * @param $row
   * @param $node
   * @return null|string
   */
  public static function prepareSpendingContractLink($row, $node) {
    if ($row['spending_category_name'] == 'Payroll' || $row['spending_category_name'] == 'Others') {
      return 'N/A';
    }

    if (empty($row['agreement_id'])) {
      return $row['reference_document_number'];
    }

    $docType = $row['reference_document_code'];

    if (RequestUtil::isExpandBottomContainer()) {
      $link = '<a href=/contract_details/agid/' . $row['agreement_id'] . '/doctype/' . $docType . CustomURLHelper::_checkbook_append_url_params() . ' class=bottomContainerReload>' . $row['reference_document_number'] . '</a>';
    }
    elseif (RequestUtil::isNewWindow()) {
      $link = '<span href=/contracts_landing/status/A'
        . CustomURLHelper::_checkbook_project_get_year_url_param_string()
        . CustomURLHelper::_checkbook_append_url_params()
        . '?expandBottomContURL=/contract_details/agid/' . $row['agreement_id'] . '/doctype/' . $docType . CustomURLHelper::_checkbook_append_url_params()
        . ' class=loadParentWindow>' . $row['reference_document_number'] . '</span>';
    }
    else {
      $link = "<a class='new_window' href='/contract_details" . CustomURLHelper::_checkbook_append_url_params() . ContractURLHelper::_checkbook_project_get_contract_url($row['reference_document_number'], $row['agreement_id']) . "/newwindow'>" . $row['reference_document_number'] . "</a>";
    }

    return $link;
  }

  /**
   * @param $row
   * @param $node
   * @return null|string
   */
  public static function prepareSpendingContractTransactionsLink($row, $node) {
    $link = NULL;
    $docType = $row['document_code@checkbook:ref_document_code'];

    if (RequestUtil::isExpandBottomContainer()) {
      $link = '<a href=/contract_details/agid/' . $row['disb_agreement_id'] . '/doctype/' . $docType . CustomURLHelper::_checkbook_append_url_params() . ' class=bottomContainerReload>' . $row['disb_contract_number'] . '</a>';
    }
    elseif (RequestUtil::isNewWindow()) {
      $link = '<span href=/contracts_landing/status/A'
        . CustomURLHelper::_checkbook_project_get_year_url_param_string()
        . '?expandBottomContURL=/contract_details/agid/' . $row['disb_agreement_id'] . '/doctype/' . $docType . CustomURLHelper::_checkbook_append_url_params()
        . ' class=loadParentWindow>' . $row['disb_contract_number'] . '</span>';
    }
    else {
      $link = '<a href=/contracts_landing/status/A'
        . CustomURLHelper::_checkbook_project_get_year_url_param_string()
        . '?expandBottomContURL=/contract_details/agid/' . $row['disb_agreement_id'] . '/doctype/' . $docType . CustomURLHelper::_checkbook_append_url_params()
        . ' >' . $row['disb_contract_number'] . '</a>';
    }

    return $link;
  }

  /**
   * @param $page
   * @param $status
   * @return string
   */
  public static function prepareActRegContractsSliderFilter($page, $status) {
    $urlPath = RequestUtilities::getCurrentPageUrl();
    $pathParams = explode('/', $urlPath);
    $url = "/".$page;
    if (strlen($status) > 0) {
      $url .= "/status/" . $status;
    }
    $url .= CustomURLHelper::_checkbook_append_url_params();

    if (str_starts_with($urlPath, "/contracts_pending")) {
      $allowedFilters = array("agency", "vendor", "awrdmthd", "awdmethod", "csize", "cindustry", "agid", "dashboard", "subvendor", "mwbe");
      $url .= "/yeartype/B/year/" . CheckbookDateUtil::getCurrentFiscalYearId();
    }
    else {
      $allowedFilters = array("year", "agency", "yeartype", "awdmethod", "vendor", "csize", "cindustry", "agid", "dashboard", "subvendor", "mwbe");
    }

    for ($i = 2; $i < count($pathParams); $i++) {
      if (in_array($pathParams[$i], $allowedFilters)) {
        $newPathParams = explode('/', $url);
        $url .= (!in_array($pathParams[$i], $newPathParams)) ? '/' . $pathParams[$i] . '/' . $pathParams[($i + 1)] : '';
      }
    }
    return $url;
  }

  /**
   * @param $page
   * @param null $dashboard
   * @param bool $third_bottom_slider
   * @return null|string|string[]
   */
  public static function prepareSubvendorContractsSliderFilter($page, $dashboard = NULL, $third_bottom_slider = FALSE){
    $urlPath = RequestUtilities::getCurrentPageUrl();
    $pathParams = explode('/', $urlPath);
    $url = "/".$page;
    $url .= CustomURLHelper::_checkbook_append_url_params();
    if (str_starts_with($urlPath, "/contracts_pending")) {
      $allowedFilters = array("agency", "vendor", "awrdmthd", "awdmethod", "csize", "cindustry", "agid", "dashboard", "subvendor", "mwbe");
      $url .= "/yeartype/B/year/" . CheckbookDateUtil::getCurrentFiscalYearId();
    }
    else {
      $allowedFilters = array("year", "agency", "yeartype", "awdmethod", "vendor", "csize", "cindustry", "agid", "subvendor", "mwbe", "status");
      // Add new parameter for bottom slider.
      $dashboard = $dashboard ?? RequestUtilities::get("dashboard");

      // Remove dashboard parameter before appending the new value.
      $url = preg_replace("/\/dashboard\/../", "", $url);
      $url .= (($third_bottom_slider) ? "/bottom_slider/sub_vendor" : "") . "/status/A"
        . (isset($dashboard) ? '/dashboard/' . $dashboard : "");
    }

    for ($i = 1; $i < count($pathParams); $i++) {
      if (in_array($pathParams[$i], $allowedFilters)) {
        $newPathParams = explode('/', $url);
        $url .= (!in_array($pathParams[$i], $newPathParams)) ? '/' . $pathParams[$i] . '/' . $pathParams[($i + 1)] : '';
      }
      $i++;
    }

    // Persist the last parameter in the current page URL as the last param only to fix the title issues.
    $lastReqParam = RequestUtil::_getLastRequestParamValue();
    if ($lastReqParam != RequestUtil::_getLastRequestParamValue($url)) {
      foreach ($lastReqParam as $key => $value) {
        if (in_array($pathParams[$i], $allowedFilters)){
          if ($value != 'subvendor_landing') {
            $url = preg_replace("/\/" . $key . "\/" . $value . "/", "", $url);
            $url .= "/" . $key . "/" . $value;
          }
        }
      }
    }

    return $url;
  }

  /**
   * @param $page
   * @return string
   */
  public static function preparePendingContractsSliderFilter($page): string {
    $urlPath = RequestUtilities::getCurrentPageUrl();
    // Remove additional path params to generate the correct url
    $urlPath = str_replace('/mwbe_landing', '', $urlPath);
    $pathParams = explode('/', $urlPath);
    $url = "/".$page;
    if (str_starts_with($urlPath, "/contracts_pending")) {
      $allowedFilters = array("year", "agency", "yeartype", "awrdmthd", "awdmethod", "vendor", "csize", "cindustry", "mwbe", "dashboard");
    }
    else {
      $allowedFilters = array("year", "agency", "yeartype", "awdmethod", "vendor", "csize", "cindustry", "mwbe", "dashboard");
    }
    for ($i = 2; $i < count($pathParams); $i++) {
      if (in_array($pathParams[$i], $allowedFilters)) {
        $newPathParams = explode('/', $url);
        $url .= (!in_array($pathParams[$i], $newPathParams)) ? '/' . $pathParams[$i] . '/' . $pathParams[($i + 1)] : '';
      }
      $i++;
    }

    return $url;
  }

  /**
   * returns the year type and year values string to be appended to the URL for spending trans link.
   * @return string
   */
  public static function _checkbook_project_spending_get_year_url_param_string() {
    $currentPath = \Drupal::service('path.current')->getPath();
    $urlPath = \Drupal::service('path_alias.manager')->getAliasByPath($currentPath);
    $pathParams = explode('/', $urlPath);

    $yeartypeIndex = array_search("yeartype", $pathParams);
    $yearIndex = array_search("year", $pathParams);

    if ($yeartypeIndex) {
      $yeartypeValue = $pathParams[($yeartypeIndex + 1)];
      return CustomURLHelper::_checkbook_append_url_params() . "/yeartype/B/year/" . $pathParams[($yearIndex + 1)] . "/syear/" . $pathParams[($yearIndex + 1)];
    }
  }

  /**
   * @param $row
   * @param $node
   * @return string
   */
  public static function _prepare_oge_contracts_spending_url($row, $node) {
    $agencies = _checkbook_project_querydataset('checkbook_oge:agency', array('agency_id', 'agency_name'), array('agency_id' => $row['agency_id'], 'is_oge_agency' => 'Y'));
    $oge_agency_name = $agencies[0]['agency_name'];

    $vendors = _checkbook_project_querydataset('checkbook_oge:vendor', array('vendor_id', 'legal_name'), array('vendor_id' => $row['vendor_id']));
    $oge_vendor_name = $vendors[0]['legal_name'];

    $vendor_url = '';
    if (strtolower($oge_agency_name) != strtolower($oge_vendor_name)) {
        $vendor_url = '/svendor/' . $row['vendor_id'];
    }

    if (!(RequestUtilities::get('year') )) {
        $year_url = '/yeartype/B/year/' . CheckbookDateUtil::_getFiscalYearID() . '/syear/' . CheckbookDateUtil::_getFiscalYearID();
    }
    else {
        $year_url = $row['type_of_year'] == 'B' ?? '/year/' . $row['fiscal_year_id'] . '/syear/' . $row['fiscal_year_id'] ;
    }

    return "<a href='/spending/transactions"
      . ($row['master_agreement_yn'] == 'Y' ? '/magid/' : '/agid/') . $row['original_agreement_id']
      . ($row['master_agreement_yn'] == 'Y' ? $vendor_url : '/svendor/' . $row['vendor_id'])
      . ($row['master_agreement_yn'] == 'Y' ? '' : ('/scomline/' . $row['fms_commodity_line']))
      . $year_url
      . RequestUtilities::buildUrlFromParam('vendor')
      . CustomURLHelper::_checkbook_append_url_params()
      . "/newwindow' class='new_window'>" . FormattingUtilities::custom_number_formatter_basic_format($row['spending_amount_disb']) . '</a>';
  }

  /**
   * @param $row
   * @param $node
   * @return string
   */
  public static function _prepare_oge_spent_to_date_url($row, $node) {
    $oge_agency_name = isset($row['agency_name_checkbook_oge_agency']) ? $row['agency_name_checkbook_oge_agency'] : null;
    $oge_vendor_name = isset($row['legal_name_checkbook_oge_vendor']) ? $row['legal_name_checkbook_oge_vendor'] : null;

    $vendor_url = $year_url = '';
    if (strtolower($oge_agency_name) != strtolower($oge_vendor_name)) {
        $vendor_url = '/svendor/' . $row['vendor_id'];
    }
    if (!(RequestUtilities::get('year') )) {
        $year_url = '/yeartype/B/year/' . CheckbookDateUtil::_getFiscalYearID() . '/syear/' . CheckbookDateUtil::_getFiscalYearID();
    }
    else {
        $year_url = $row['type_of_year'] == 'B' ?? '/year/' . $row['fiscal_year_id'] . '/syear/' . $row['fiscal_year_id'] ;
    }

    $master_agreement_yn = isset($row['master_agreement_yn']) ? $row['master_agreement_yn'] : null;
    return "<a href='/spending/transactions"
      . ($master_agreement_yn == 'Y' ? '/magid/' : '/agid/') . (isset($row['original_agreement_id']) ? $row['original_agreement_id'] : null)
      . ($master_agreement_yn == 'Y' ? $vendor_url : '/svendor/' . (isset($row['vendor_id']) ? $row['vendor_id'] : null))
      . ($master_agreement_yn == 'Y' ? '' : ('/scomline/' . (isset($row['fms_commodity_line']) ? $row['fms_commodity_line'] : null)))
      . $year_url
      . RequestUtilities::buildUrlFromParam('vendor')
      . CustomURLHelper::_checkbook_append_url_params()
      . "/newwindow' class='new_window'>" . FormattingUtilities::custom_number_formatter_basic_format($row['spending_amount_disb']) . '</a>';
  }

  /**
   * @param $row
   * @param $node
   * @return string
   */
  public static function prepareExpandLink($row, $node): string {
    $flag = (str_starts_with(RequestUtilities::getCurrentPageUrl(), "mwbe")) ? "has_mwbe_children" : "has_children";
    $show_expander = $row[$flag] == 'Y';

    $year = $row['fiscal_year_id@checkbook:all_contracts_coa_aggregates'];
    $year_type = $row['type_of_year@checkbook:all_contracts_coa_aggregates'];

    $year = !$year ? CheckbookDateUtil::getCurrentFiscalYearId() : $year;
    $year_type = !$year_type ? 'B' : $year_type;

    return ($show_expander) ? '<span id=dtl_expand class="toggler collapsed"  magid="' . ((isset($row['contract_original_agreement_id'])) ? $row['contract_original_agreement_id'] : $row['original_agreement_id']) . '" '
      . (RequestUtilities::get('dashboard') != '' ? ('dashboard="' . RequestUtilities::get('dashboard') . '" ') : '')
      . (RequestUtilities::get('mwbe') != '' ? ('mwbe="' . RequestUtilities::get('mwbe') . '" ') : '')
      . (RequestUtilities::get('smnid') != '' ? ('smnid="' . RequestUtilities::get('smnid') . '" ') : '')
      . (RequestUtilities::get('contstatus') != '' ? ('contstatus="' . RequestUtilities::get('contstatus') . '" ') : '')
      . 'year="' . $year . '" '
      . 'yeartype="' . $year_type . '" '
      . ('mastercode="' . $row['document_code@checkbook:ref_document_code'] . '"')
      . '></span>' : '';
  }

  /* Start Expense Contracts Transaction Page */

  /**
   * @param $row
   * @param $node
   * @return string
   */
  public static function expenseContractsExpandLink($row, $node): string {
    $flag = (str_starts_with(RequestUtilities::getCurrentPageUrl(), "mwbe")) ? "has_mwbe_children" : "has_children";
    $show_expander = $row[$flag] == 'Y';
    $effective_end_year_id_row = isset($row['effective_end_year_id']) ? $row['effective_end_year_id'] : null;
    $effective_year_id = ContractsUrlService::applyYearParameter($effective_end_year_id_row);
    $link = ($show_expander) ? '<span id=dtl_expand class="toggler collapsed"  magid="' . ((isset($row['contract_original_agreement_id'])) ? $row['contract_original_agreement_id'] : $row['original_agreement_id']) . '" '
        . (RequestUtilities::get('dashboard') != '' ? ('dashboard="' . RequestUtilities::get('dashboard') . '" ') : '')
        . (RequestUtilities::get('mwbe') != '' ? ('mwbe="' . RequestUtilities::get('mwbe') . '" ') : '')
        . (RequestUtilities::get('smnid') != '' ? ('smnid="' . RequestUtilities::get('smnid') . '" ') : '')
        . (RequestUtilities::get('contstatus') != '' ? ('contstatus="' . RequestUtilities::get('contstatus') . '" ') : '')
        . CustomURLHelper::_checkbook_project_get_year_url_param_string()
        . ('mastercode="' . $row['document_code'] . '"')
        . '></span>' : '';
    if ((PageType::getCurrent() == PageType::ADVANCED_SEARCH_PAGE)) {
    $link =  preg_replace("/\/year\/\d+/", $effective_year_id, $link) ;
    }
    return $link;
  }

  /**
   * @param $row
   * @param $node
   * @param bool $parent
   * @param null $original_agreement_id
   * @return null|string
   */
  public static function expenseContractsLink($row, $node, $parent = false, $original_agreement_id = null){
    $link = NULL;
    $expandBottomContURL = \Drupal::request()->get('expandBottomContURL');

    if (isset($row['contract_original_agreement_id'])) {
      $row['original_agreement_id'] = $row['contract_original_agreement_id'];
    }

    $row['original_agreement_id'] = ($original_agreement_id) ? $original_agreement_id : $row['original_agreement_id'];
    $effective_end_year_id_row = isset($row['effective_end_year_id']) ? $row['effective_end_year_id'] : null;
    $effective_year_id = ContractsUrlService::applyYearParameter($effective_end_year_id_row);

    if ($parent && strlen($row['master_contract_number']) > 0) {
      $agrParamName = 'magid';
      $docTypeStr = substr($row['master_contract_number'], 0, 3);
      $docType = ($docTypeStr == 'MA1') ? 'MA1' : 'MMA1';
      $row['original_agreement_id'] = $row['master_agreement_id'];
      $row['contract_number'] = $row['master_contract_number'];
    }
    elseif ($parent && strlen($row['master_contract_number']) == 0) {
      return "N/A";
    }
    else {
      $docType = $row['document_code'];
      $agrParamName = in_array($docType, array('MMA1', 'MA1')) ? 'magid' : 'agid';
    }

    if ($docType == "RCT1") {
      $page = "/contracts_revenue_landing";
    }
    else {
      $page = "/contracts_landing";
    }

    if (RequestUtil::isExpandBottomContainer()) {
      $link = '<a href=/contract_details/' .$agrParamName . '/' . $row['original_agreement_id'] . '/doctype/' . $docType . CustomURLHelper::_checkbook_append_url_params() . ' class=bottomContainerReload>' . $row['contract_number'] . '</a>';
    }
    else {
      $link = '<a href='
        . $page
        . RequestUtilities::buildUrlFromParam('contstatus|status')
        . CustomURLHelper::_checkbook_append_url_params()
        . CustomURLHelper::_checkbook_project_get_year_url_param_string()
        . ((_checkbook_check_isEDCPage()) ? '/agency/' . $row['agency_id'] : '')
        . '?expandBottomContURL=/contract_details/' . $agrParamName . '/' . $row['original_agreement_id'] . '/doctype/' . $docType . CustomURLHelper::_checkbook_append_url_params()
        . ' >' . $row['contract_number'] . '</a>';
    }

    if (isset($effective_year_id) && (PageType::getCurrent() == PageType::ADVANCED_SEARCH_PAGE)) {
      $link =  preg_replace("/\/year\/\d+/", $effective_year_id, $link) ;
    }
    return $link;
  }

  /**
   * @return bool
   */
  public static function thirdBottomSliderValue(){
    $node = \Drupal\node\Entity\Node::load(737);
    widget_config($node);
    widget_prepare($node);
    widget_invoke($node, 'widget_prepare');
    widget_data($node);
    $contracts = $node->data[0]['total_contracts'];
    if ($contracts > 0) {
      $third_bottom_slider = false;
    }
    else {
      $third_bottom_slider = true;
    }
    return $third_bottom_slider;
  }

  /**
   * @param $contnum
   * @param $agreement_id
   *
   * @return string
   */
  public static function _checkbook_project_get_contract_url($contnum, $agreement_id){
    $contract_type = ContractUtil::_get_contract_type($contnum);
    if (strtolower($contract_type) == 'mma1' || strtolower($contract_type) == 'ma1') {
      return '/magid/' . $agreement_id . '/doctype/' . $contract_type;
    }
    else {
      return '/agid/' . $agreement_id . '/doctype/' . $contract_type;
    }
  }

  /**
   * @param $agency_id
   * @param bool $prime
   * @return string
   */
  public static function _checkbook_agency_link($agency_id, $prime = FALSE){
    $issubvendor = 'false';
    $status = '/status/' . (RequestUtilities::get("status") ?: 'A');

    $datasource = '';
    if ('checkbook_oge' == RequestUtilities::get("datasource")) {
      $datasource = "/datasource/checkbook_oge";
    }

    if (RequestUtilities::get("doctype") == "RCT1") {
      $is_mwbe = VendorType::_is_mwbe_vendor(RequestUtilities::get("magid"));
      $mwbe = ($is_mwbe) ? RequestUtilities::_appendMWBESubVendorDatasourceUrlParams() : '';
      $agency_link = '/contracts_revenue_landing' . $status . $mwbe . '/year/' . CheckbookDateUtil::getCurrentFiscalYearId() . $datasource . '/yeartype/B/agency/'
        . $agency_id;
    }
    else {
      $dashboard = '/contracts_landing';
      $url = '  ' . RequestUtilities::getCurrentPageUrl();
      if (stripos($url, 'contracts_pending_exp_landing')) {
        $dashboard = '/contracts_pending_exp_landing';
      }
      elseif (stripos($url, 'contracts_pending_rev_landing')) {
        $dashboard = '/contracts_pending_rev_landing';
      }
      elseif (stripos($url, 'contracts_revenue_landing')) {
        $dashboard = '/contracts_revenue_landing';
      }
      elseif (stripos($url, 'bottom_slider/sub_vendor')) {
        $dashboard = '/contracts_landing/bottom_slider/sub_vendor';
      }
      if (stripos($url, 'dashboard/ss')) {
        $issubvendor = true;
      }
      if($dashboard =='/contracts_pending_rev_landing' || $dashboard == '/contracts_pending_exp_landing'){
        $status = '';
      }
      $mwbe = (VendorType::_is_mwbe_vendor(RequestUtilities::get("magid")) || VendorType::_is_mwbe_vendor(RequestUtilities::get("agid"))
        || $issubvendor)  ? RequestUtilities::_appendMWBESubVendorDatasourceUrlParams() : '';

      $agency_link = $dashboard . $status . '/year/' . CheckbookDateUtil::getCurrentFiscalYearId() . $datasource . '/yeartype/B/agency/'
        . $agency_id . $mwbe;
    }
    return $agency_link;
  }

  /**
   * @param $vendor_id
   * @param bool $prime
   *
   * @return string
   */
  public static function _checkbook_vendor_link($vendor_id, $prime = FALSE){
    $status = '/status/' . (RequestUtilities::get("status") ?: 'A');
    $current_year_id = CheckbookDateUtil::getCurrentFiscalYearId();
    if (RequestUtilities::get("doctype") == "RCT1") {
      $page = '/contracts_revenue_landing';
      $is_mwbe = VendorType::_is_mwbe_vendor(RequestUtilities::get("magid"));
      $mwbe = ($is_mwbe)? '/dashboard/mp' : '';
    }
    else {
      $page = '/contracts_landing';
      $url = '  ' . RequestUtilities::getCurrentPageUrl();
      if (stripos($url, 'contracts_pending_exp_landing') && $status === '/status/A') {
        $page = '/contracts_landing';
      }
      elseif (stripos($url, 'contracts_pending_exp_landing')) {
        $page = '/contracts_pending_exp_landing';
      }
      elseif (stripos($url, 'contracts_pending_rev_landing')) {
        $page = '/contracts_pending_rev_landing';
      }
      elseif (stripos($url, 'contracts_revenue_landing')) {
        $page = '/contracts_revenue_landing';
      }
      $minority_type_id = PrimeVendorService::getLatestMinorityTypeByYear($vendor_id, $current_year_id, 'B');
      if ($minority_type_id == "4" || $minority_type_id == "5" || $minority_type_id == "10") {
        $minority_type_id = "4~5~10";
      }
      $mwbe = (VendorType::_is_mwbe_vendor(RequestUtilities::get("agid")) || VendorType::_is_mwbe_vendor(RequestUtilities::get("magid"))
        || $minority_type_id) ?
        '/dashboard/mp/mwbe/'.$minority_type_id : '';
    }
    if($page == '/contracts_pending_rev_landing' || $page == '/contracts_pending_exp_landing'){
      $status = '';
    }
    return $page . $status . '/year/' . $current_year_id . '/yeartype/B/vendor/' . $vendor_id . $mwbe;
  }
}
