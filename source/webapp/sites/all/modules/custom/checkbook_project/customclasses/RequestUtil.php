<?php
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace CheckbookProject\CustomClasses;

require_once(__DIR__ . '/constants/Constants.php');

/**
 * Class RequestUtil
 */
class RequestUtil
{

    //Links for landing pages. This can be avoided if ajax requests can be identified uniquely.
    /**
     * @var array
     */
    static $landing_links = array(
        "contracts_landing",
        "contracts_revenue_landing",
        "contracts_pending_rev_landing",
        "contracts_pending_exp_landing"
    );

    /**
     * @var array
     */
    static $contracts_spending_landing_links = array(
        "spending_landing",
        "contracts_landing",
        "contracts_revenue_landing",
        "contracts_pending_rev_landing",
        "contracts_pending_exp_landing"
    );

    /**
     * @var null
     */
    static $is_prime_mwbe_amount_zero_sub_mwbe_not_zero = null;

    /** Checks if the page bottom container is expanded */
    static function isExpandBottomContainer()
    {
        $referer = $_SERVER['HTTP_REFERER'];

        foreach (self::$landing_links as $landing_link) {
            if (preg_match("/$landing_link/i", $referer)) {
                return true;
            }
        }

        return false;
    }

    /** Checks if the current URL is opened in a new window */
    static function isNewWindow()
    {
        $referer = $_SERVER['HTTP_REFERER'];

        return preg_match('/newwindow/i', $referer);
    }

    /** Checks if the current page is Pending Expense Contratcts page
     * @param $path
     * @return bool
     */
    static function isPendingExpenseContractPath($path)
    {
        return 0 === stripos($path, 'contracts_pending_exp_landing');
    }

    /** Checks if the current page is Pending Revenue Contratcts page
     * @param $path
     * @return bool
     */
    static function isPendingRevenueContractPath($path)
    {
        return 0 === stripos($path, 'contracts_pending_rev_landing');
    }

    /** Checks if the current page is Active/Registered Expense Contratcs page
     * @param $path
     * @return bool
     */
    static function isExpenseContractPath($path)
    {
        return 0 === stripos($path, 'contracts_landing');
    }

    /** Checks if the current page is Active/Registered Pending Revenue Contratcs page
     * @param $path
     * @return bool
     */
    static function isRevenueContractPath($path)
    {
        return 0 === stripos($path, 'contracts_revenue_landing');
    }

    /** Returns Contracts page title and Breadcrumb */
    static function getContractBreadcrumbTitle()
    {
        $title = '';
        $bottomURL = $_REQUEST['expandBottomContURL'];
        if (preg_match('/magid/', $bottomURL)) {
            $magid = RequestUtil::getRequestKeyValueFromURL("magid", $bottomURL);
            $contract_number = _get_master_agreement_details($magid);
            return $contract_number['contract_number'];

        } elseif (preg_match('/agid/', $bottomURL)) {
            $agid = RequestUtil::getRequestKeyValueFromURL("agid", $bottomURL);
            $contract_number = _get_child_agreement_details($agid);
            return $contract_number['contract_number'];
        } elseif (preg_match('/contract/', $bottomURL) && preg_match('/pending_contract_transactions/', $bottomURL)) {
            $contract_number = RequestUtil::getRequestKeyValueFromURL("contract", $bottomURL);
            return $contract_number;
        } else if (isset($bottomURL) && preg_match('/transactions/', $bottomURL)) {
            $smnid = RequestUtil::getRequestKeyValueFromURL("smnid", $bottomURL);
            $dashboard = RequestUtil::getRequestKeyValueFromURL("dashboard", $bottomURL);
            $title = NodeSummaryUtil::getInitNodeSummaryTitle($smnid);
            if ($smnid == 720 && $dashboard != 'mp') {
                $title = '';
            }
            $bottomNavigation = '';
            if (preg_match('/status\/A/', current_path())) {
                $bottomNavigation = "Total Active Sub Vendor Contracts";
            } elseif (preg_match('/status\/R/', current_path())) {
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

            if (preg_match('/^contracts_landing/', current_path()) && preg_match('/status\/A/', current_path())) {
                if (preg_match('/dashboard\/ss/', current_path()) || preg_match('/dashboard\/ms/', current_path()) || preg_match('/dashboard\/sp/', current_path())) {
                    if (preg_match('/dashboard\/ms/', current_path()) || preg_match('/dashboard\/sp/', current_path())) {
                        $bottomNavigation = "Total Active M/WBE Sub Vendor Contracts";
                    }
                    $title = $title . " " . $bottomNavigation . " Transactions";

                } else {
                    $title = RequestUtil::getDashboardTitle() . ' ' . $title . ' ' . ' Active Expense Contracts Transactions';
                }
            } elseif (preg_match('/^contracts_landing/', current_path()) && preg_match('/status\/R/', current_path())) {
                if (preg_match('/dashboard\/ss/', current_path()) || preg_match('/dashboard\/ms/', current_path()) || preg_match('/dashboard\/sp/', current_path())) {
                    if (preg_match('/dashboard\/ms/', current_path()) || preg_match('/dashboard\/sp/', current_path())) {
                        $bottomNavigation = "New M/WBE Sub Vendor Contracts by Fiscal Year";
                    }
                    $title = $title . " " . $bottomNavigation . " Transactions";

                } else {
                    $title = RequestUtil::getDashboardTitle() . ' ' . $title . ' ' . ' Registered Expense Contracts Transactions';
                }

            } elseif (preg_match('/^contracts_landing/', current_path()) && !preg_match('/status\/', current_path())) {
                if (preg_match('/dashboard\/ss/', current_path())) {
                    $title = 'Sub Contract Status by Prime Contract ID';
                }
                if (preg_match('/dashboard\/ms/', current_path()) || preg_match('/dashboard\/sp/', current_path())) {
                    $title = 'M/WBE Sub Contract Status by Prime Contract ID';
                }
            } elseif (preg_match('/^contracts_revenue_landing/', current_path()) && preg_match('/status\/A/', current_path())) {
                $title = RequestUtil::getDashboardTitle() . ' ' . $title . ' ' . ' Active Revenue Contracts Transactions';
            } elseif (preg_match('/^contracts_revenue_landing/', current_path()) && preg_match('/status\/R/', current_path())) {
                $title = RequestUtil::getDashboardTitle() . ' ' . $title . ' ' . ' Registered Revenue Contracts Transactions';
            } elseif (preg_match('/^contracts_pending_exp_landing/', current_path())) {
                $title = RequestUtil::getDashboardTitle() . ' ' . $title . ' ' . ' Pending Expense Contracts Transactions';
            } elseif (preg_match('/^contracts_pending_rev_landing/', current_path())) {
                $title = RequestUtil::getDashboardTitle() . ' ' . $title . ' ' . ' Pending Revenue Contracts Transactions';
            }
        } elseif (preg_match('/^contracts_landing/', current_path()) && preg_match('/status\/A/', current_path())) {
            if (preg_match('/dashboard\/ss/', current_path()) || preg_match('/dashboard\/ms/', current_path()) || preg_match('/dashboard\/sp/', current_path())) {
                $title = _get_contracts_breadcrumb_title_drilldown(false) . ' ' . ' Total Active Sub Vendor Contracts';
            } else {
                $title = _get_contracts_breadcrumb_title_drilldown(false) . ' ' . RequestUtil::getDashboardTitle() . ' Active Expense Contracts';
            }

        } elseif (preg_match('/^contracts_landing/', current_path()) && preg_match('/status\/R/', current_path())) {
            if (preg_match('/dashboard\/ss/', current_path()) || preg_match('/dashboard\/ms/', current_path()) || preg_match('/dashboard\/sp/', current_path())) {
                $title = _get_contracts_breadcrumb_title_drilldown(false) . ' ' . ' New Sub Vendor Contracts by Fiscal Year';
            } else {
                $title = _get_contracts_breadcrumb_title_drilldown(false) . ' ' . RequestUtil::getDashboardTitle() . ' Registered Expense Contracts';
            }
        } elseif (preg_match('/^contracts_landing/', current_path()) && !preg_match('/status\/', current_path())) {
            if (preg_match('/dashboard\/ss/', current_path()) || preg_match('/dashboard\/ms/', current_path()) || preg_match('/dashboard\/sp/', current_path())) {
                $title = _get_contracts_breadcrumb_title_drilldown(false) . ' ' . ' Status of Sub Vendor Contracts by Prime Vendor';
            }
        } elseif (preg_match('/^contracts_revenue_landing/', current_path()) && preg_match('/status\/A/', current_path())) {
            $title = _get_contracts_breadcrumb_title_drilldown(false) . ' ' . RequestUtil::getDashboardTitle() . ' Active Revenue Contracts';
        } elseif (preg_match('/^contracts_revenue_landing/', current_path()) && preg_match('/status\/R/', current_path())) {
            $title = _get_contracts_breadcrumb_title_drilldown(false) . ' ' . RequestUtil::getDashboardTitle() . ' Registered Revenue Contracts';
        } elseif (preg_match('/^contracts_pending_exp_landing/', current_path())) {
            $title = _get_pending_contracts_breadcrumb_title_drilldown() . ' ' . RequestUtil::getDashboardTitle() . ' Pending Expense Contracts';
        } elseif (preg_match('/^contracts_pending_rev_landing/', current_path())) {
            $title = _get_pending_contracts_breadcrumb_title_drilldown() . ' ' . RequestUtil::getDashboardTitle() . ' Pending Revenue Contracts';
        } else {
            GLOBAL $checkbook_breadcrumb_title;
            $title = $checkbook_breadcrumb_title;
        }
        return html_entity_decode($title);
    }

    /** Returns the request parameter value from URL
     * @param $key
     * @param $urlPath
     * @return mixed|null|string
     */
    static function getRequestKeyValueFromURL($key, $urlPath)
    {
        $value = NULL;
        $pathParams = explode('/', $urlPath);
        $index = array_search($key, $pathParams);
        if ($index != FALSE) {
            $value = filter_xss($pathParams[($index + 1)]);
        }
        return $value;
    }

    /**
     * @param null $dashboard
     * @return string
     */
    static function getDashboardTitle($dashboard = null)
    {
        if ($dashboard == null) {
            $dashboard = _getRequestParamValue('dashboard');
        }
        switch ($dashboard) {
            case "mp" :
                return "M/WBE";
            case "sp" :
                return "Sub Vendors (M/WBE)";
            case "ms" :
                return "M/WBE (Sub Vendors)";
            case "ss" :
                return "Sub Vendors";
        }
        return '';
    }

    /** Returns Payroll page title and Breadcrumb */
    static function getPayrollBreadcrumbTitle()
    {
        $title = '';
        $bottomURL = $_REQUEST['expandBottomContURL'];
        if (isset($bottomURL) && preg_match('/payroll_agencytransactions/', $bottomURL)) {
            $smnid = RequestUtil::getRequestKeyValueFromURL("smnid", $bottomURL);
            $dtsmnid = RequestUtil::getRequestKeyValueFromURL("dtsmnid", $bottomURL);
            if ($dtsmnid > 0) {
                $title = NodeSummaryUtil::getInitNodeSummaryTitle($dtsmnid);
            } else if ($smnid > 0) {
                $title = NodeSummaryUtil::getInitNodeSummaryTitle($smnid);
            } else {
                $title = _checkbook_project_get_name_for_argument("agency_id", RequestUtil::getRequestKeyValueFromURL("agency", $bottomURL)) . ' Payroll Transactions';
            }
        } else if (isset($bottomURL) && preg_match('/payroll_employee_transactions/', $bottomURL)) {
            $title = "Individual Employee Payroll Transactions";
        } else if (isset($bottomURL) && preg_match('/payroll_title_transactions/', $bottomURL)) {
            $title = "Payroll Summary by Employee Title";
        } else if (isset($bottomURL) && preg_match('/payroll_nyc_transactions/', $bottomURL)) {
            $smnid = RequestUtil::getRequestKeyValueFromURL("smnid", $bottomURL);
            $dtsmnid = RequestUtil::getRequestKeyValueFromURL("dtsmnid", $bottomURL);
            if ($dtsmnid > 0) {
                $title = NodeSummaryUtil::getInitNodeSummaryTitle($dtsmnid);
            }
            if ($smnid > 0) {
                $title = NodeSummaryUtil::getInitNodeSummaryTitle($smnid);
            }
        } else if (isset($bottomURL) && preg_match('/payroll_nyc_title_transactions/', $bottomURL)) {
            $smnid = RequestUtil::getRequestKeyValueFromURL("smnid", $bottomURL);
            if ($smnid > 0) {
                $title = NodeSummaryUtil::getInitNodeSummaryTitle($smnid);
            }
        } else if (isset($bottomURL) && preg_match('/payroll_by_month_nyc_transactions/', $bottomURL)) {
            $smnid = RequestUtil::getRequestKeyValueFromURL("smnid", $bottomURL);
            if ($smnid == '491' || $smnid == '492') {
                $customTitle = "Overtime Payments by Month Transactions";
            } else {
                $customTitle = "Gross Pay by Month Transactions";
            }
            $title = $customTitle;
        } else if (isset($bottomURL) && preg_match('/payroll_agency_by_month_transactions/', $bottomURL)) {
            $smnid = RequestUtil::getRequestKeyValueFromURL("smnid", $bottomURL);
            if ($smnid == '491') {
                $customTitle = "Overtime Payments by Month Transactions";
            } else {
                $customTitle = "Gross Pay by Month Transactions";
            }
            $title = $customTitle;
        } elseif (preg_match('/^payroll\/search\/transactions/', current_path())) {
            $title = "Payroll Transactions";
        } elseif (preg_match('/^payroll/', current_path()) && preg_match('/agency_landing/', current_path())) {
            $title = _checkbook_project_get_name_for_argument("agency_id", RequestUtil::getRequestKeyValueFromURL("agency", current_path())) . ' Payroll';
        } elseif (preg_match('/^payroll/', current_path()) && preg_match('/title_landing/', current_path())) {
            $title_code = RequestUtil::getRequestKeyValueFromURL("title", current_path());
            $title = PayrollUtil::getTitleByCode($title_code) . ' Payroll';
            $title = mb_convert_case($title, MB_CASE_TITLE, "UTF-8");
        } elseif (preg_match('/^payroll/', current_path()) && !preg_match('/transactions/', current_path())) {
            $title = 'New York City Payroll';
        } else {
            GLOBAL $checkbook_breadcrumb_title;
            $title = $checkbook_breadcrumb_title;
        }
        return html_entity_decode($title);
    }

    /** Returns Spending Transaction page title based on 'category'/'featured dashboard' value from current path
     * @param string $defaultName
     * @return string
     */
    static function getSpendingTransactionTitle($defaultName = 'Total Spending')
    {
        $categoryId = _getRequestParamValue('category');
        if (isset($categoryId)) {
            $categoryDetails = SpendingUtil::getSpendingCategoryDetails($categoryId, 'display_name');
            if (is_array($categoryDetails)) {
                return "Total " . $categoryDetails[0]['display_name'];
            }
        }

        return $defaultName;
    }

    /**
     * Returns chart title for a prime or sub vendor page based on 'category'/'featured dashboard'/'domain'
     * using values from current path.
     *
     * The page title above the visualization for Prime vendor Level and Sub vendor level pages:
     *
     * 1. Follow a three-line format when the M/WBE Category is equal to any of the following:
     *    Asian American, Black American, Women or Hispanic
     * 2. Follow a two-line format when the M/WBE Category is equal to any of the following:
     *    Individual & Others or Non M/WBE
     *
     * @param string $domain
     * @param string $defaultTitle
     * @return string
     */
    static function getTitleByVendorType($domain, $defaultTitle = 'Total Spending')
    {
        if (_checkbook_check_is_mwbe_page()) {
            $minority_type_id = _getRequestParamValue('mwbe');
        } else {
            $lastReqParam = _getLastRequestParamValue();
            $minority_type_id = '';
            foreach ($lastReqParam as $key => $value) {
                switch ($key) {
                    case 'vendor':
                        $minority_type_id = self::getLatestMinorityTypeByVendorType($domain, $value, VendorType::$PRIME_VENDOR);
                        break;
                    case 'subvendor':
                        if ($value != 'all')
                            $minority_type_id = self::getLatestMinorityTypeByVendorType($domain, $value, VendorType::$SUB_VENDOR);
                        break;
                    default:
                }
            }
        }
        $minority_type_ids = explode('~', $minority_type_id);
        $minority_category = MappingUtil::getCurrenEhtnicityName($minority_type_ids);
        $MWBE_certified = MappingUtil::isMWBECertified($minority_type_ids);
        $title = $MWBE_certified
            ? '<p class="sub-chart-title">M/WBE Category: ' . $minority_category . '</p>'
            : $minority_category . ' ';
        $title .= RequestUtil::getSpendingCategoryName($defaultTitle);

        return html_entity_decode($title);
    }

    /**
     * returns the latest minority category by domain, vendor type and the selected year.
     *
     * @param $domain
     * @param $vendor_id
     * @param $vendor_type
     * @return mixed
     */
    static function getLatestMinorityTypeByVendorType($domain, $vendor_id, $vendor_type)
    {
        $year_id = _getRequestParamValue('year');
        $type_of_year = 'B';

        if (empty($year_id)) $year_id = _getRequestParamValue('calyear');
        if (empty($type_of_year)) $type_of_year = 'B';
        if (empty($year_id)) $year_id = _getCurrentYearID();

        $data_set = "";
        switch ($domain) {
            case Domain::$SPENDING:
                $data_set = "checkbook:spending_vendor_latest_mwbe_category";
                break;
            case Domain::$CONTRACTS:
                $data_set = "checkbook:contract_vendor_latest_mwbe_category";
                break;
        }
        $parameters = array('vendor_id' => $vendor_id, "is_prime_or_sub" => $vendor_type, 'type_of_year' => $type_of_year, 'year_id' => $year_id);
        $minority_types = _checkbook_project_querydataset($data_set, array('minority_type_id'), $parameters);
        $minority_type_id = $minority_types[0]['minority_type_id'];

        return $minority_type_id;
    }

    /** Returns Spending Category based on 'category' value from current path
     * @param string $defaultName
     * @return string
     */
    static function getSpendingCategoryName($defaultName = 'Total Spending')
    {
        $categoryId = _getRequestParamValue('category');
        if (isset($categoryId)) {
            $categoryDetails = SpendingUtil::getSpendingCategoryDetails($categoryId, 'display_name');
            if (is_array($categoryDetails)) {
                return $categoryDetails[0]['display_name'];
            }
        }

        return $defaultName;
    }

    /** Returns Spending page title and Breadcrumb */
    static function getSpendingBreadcrumbTitle()
    {
        $title = '';
        $bottomURL = $_REQUEST['expandBottomContURL'];
        if (preg_match('/transactions/', current_path())) {
            $title = SpendingUtil::getSpendingTransactionsTitle();
        } elseif (isset($bottomURL) && preg_match('/transactions/', $bottomURL)) {
            $dtsmnid = RequestUtil::getRequestKeyValueFromURL("dtsmnid", $bottomURL);
            $smnid = RequestUtil::getRequestKeyValueFromURL("smnid", $bottomURL);
            if ($dtsmnid > 0) {
                $title = NodeSummaryUtil::getInitNodeSummaryTitle($dtsmnid);
            } else if ($smnid > 0) {
                $title = NodeSummaryUtil::getInitNodeSummaryTitle($smnid);
            } else {
                $last_id = _getLastRequestParamValue($bottomURL);
                if ($last_id['vendor'] > 0) {
                    $title = _checkbook_project_get_name_for_argument("vendor_id", RequestUtil::getRequestKeyValueFromURL("vendor", $bottomURL));
                } elseif ($last_id["agency"] > 0) {
                    $title = _checkbook_project_get_name_for_argument("agency_id", RequestUtil::getRequestKeyValueFromURL("agency", $bottomURL));
                } elseif ($last_id["expcategory"] > 0) {
                    $title = _checkbook_project_get_name_for_argument("expenditure_object_id", RequestUtil::getRequestKeyValueFromURL("expcategory", $bottomURL));
                } elseif ($last_id["dept"] > 0) {
                    $title = _checkbook_project_get_name_for_argument("department_id", RequestUtil::getRequestKeyValueFromURL("dept", $bottomURL));
                } elseif (preg_match("/\/agid/", $bottomURL)) {
                    $title = _checkbook_project_get_name_for_argument("agreement_id", RequestUtil::getRequestKeyValueFromURL("agid", $bottomURL));
                } elseif (preg_match("/\/magid/", $bottomURL)) {
                    $title = _checkbook_project_get_name_for_argument("master_agreement_id", RequestUtil::getRequestKeyValueFromURL("magid", $bottomURL));
                }
                if (preg_match('/\/category\/1/', $bottomURL)) {
                    $title = $title . ' ' . RequestUtil::getDashboardTitle() . ' Contract Spending Transactions';
                } elseif (preg_match('/\/category\/2/', $bottomURL)) {
                    $title = $title . ' ' . RequestUtil::getDashboardTitle() . ' Payroll Spending Transactions';
                } elseif (preg_match('/\/category\/3/', $bottomURL)) {
                    $title = $title . ' ' . RequestUtil::getDashboardTitle() . ' Capital Contracts Spending Transactions';
                } elseif (preg_match('/\/category\/4/', $bottomURL)) {
                    $title = $title . ' ' . RequestUtil::getDashboardTitle() . ' Others Spending Transactions';
                } elseif (preg_match('/\/category\/5/', $bottomURL)) {
                    $title = $title . ' ' . RequestUtil::getDashboardTitle() . ' Trust & Agency Spending Transactions';
                } else {
                    $title = $title . ' ' . RequestUtil::getDashboardTitle() . ' Total Spending Transactions';
                }
            }
        } elseif (preg_match('/\/category\/1/', current_path())) {
            $title = _get_spending_breadcrumb_title_drilldown(false) . ' ' . RequestUtil::getDashboardTitle() . ' Contract Spending';
        } elseif (preg_match('/\/category\/2/', current_path())) {
            $title = _get_spending_breadcrumb_title_drilldown(false) . ' ' . RequestUtil::getDashboardTitle() . ' Payroll Spending';
        } elseif (preg_match('/\/category\/3/', current_path())) {
            $title = _get_spending_breadcrumb_title_drilldown(false) . ' ' . RequestUtil::getDashboardTitle() . ' Capital Contracts Spending';
        } elseif (preg_match('/\/category\/4/', current_path())) {
            $title = _get_spending_breadcrumb_title_drilldown(false) . ' ' . RequestUtil::getDashboardTitle() . ' Others Spending';
        } elseif (preg_match('/\/category\/5/', current_path())) {
            $title = _get_spending_breadcrumb_title_drilldown(false) . ' ' . RequestUtil::getDashboardTitle() . ' Trust & Agency Spending';
        } else {
            $title = _get_spending_breadcrumb_title_drilldown(false) . ' ' . RequestUtil::getDashboardTitle() . ' Total Spending';
        }

        return html_entity_decode($title);
    }

    /** Prepares Payroll bottom navigation filter
     * @param $page
     * @param $category
     * @return string
     */
    static public function preparePayrollBottomNavFilter($page, $category)
    {
        $pathParams = explode('/', drupal_get_path_alias($_GET['q']));
        $url = $page;
        if (strlen($category) > 0) {
            $url .= "/category/" . $category;
        }
        $url .= _checkbook_append_url_params();
        $allowedFilters = array("year", "calyear", "agency", "yeartype", "vendor", "industry");
        for ($i = 1; $i < count($pathParams); $i++) {
            if (in_array($pathParams[$i], $allowedFilters)) {
                $url .= '/' . $pathParams[$i] . '/' . $pathParams[($i + 1)];
            }
            $i++;
        }
        return $url;

    }

    /** Returns Budget page title and Breadcrumb */
    static function getBudgetBreadcrumbTitle()
    {
        $bottomURL = isset($_REQUEST['expandBottomContURL']) ? $_REQUEST['expandBottomContURL'] : '';
        $find = '_' . $bottomURL . current_path();
        if (
            stripos($find, 'transactions')
            || stripos($find, 'deppartment_budget_details')
            || stripos($find, 'expense_category_budget_details')
        ) {
            $dtsmnid = (isset($bottomURL)) ? RequestUtil::getRequestKeyValueFromURL("dtsmnid", $bottomURL) : RequestUtil::getRequestKeyValueFromURL("dtsmnid", current_path());
            $smnid = (isset($bottomURL)) ? RequestUtil::getRequestKeyValueFromURL("smnid", $bottomURL) : RequestUtil::getRequestKeyValueFromURL("smnid", current_path());
            if (isset($dtsmnid)) {
                $title = NodeSummaryUtil::getInitNodeSummaryTitle($dtsmnid);
            } else if (isset($smnid)) {
                $title = NodeSummaryUtil::getInitNodeSummaryTemplateTitle($smnid);
            } else {
                $title = _get_budget_breadcrumb_title_drilldown() . ' Expense Budget';
            }
        } else if (!$bottomURL && preg_match('/^budget\/transactions/', current_path())) {
            $title = "Expense Budget Transactions";
        } else {
            $title = _get_budget_breadcrumb_title_drilldown() . ' Expense Budget';
        }

        return html_entity_decode($title);
    }

    /** Returns Revenue page title and Breadcrumb */
    static function getRevenueBreadcrumbTitle()
    {
        $bottomURL = isset($_REQUEST['expandBottomContURL']) ? $_REQUEST['expandBottomContURL'] : '';
        $find = '_' . $bottomURL . current_path();
        if (
            stripos($bottomURL, 'transactions')
            || stripos($find, 'agency_revenue_by_cross_year_collections_details')
            || stripos($find, 'revenue_category_revenue_by_cross_year_collections_details')
            || stripos($find, 'funding_class_revenue_by_cross_year_collections_details')
            || stripos('_' . current_path(), 'revenue_transactions')
        ) {
            $dtsmnid = (isset($bottomURL)) ? RequestUtil::getRequestKeyValueFromURL("dtsmnid", $bottomURL) : RequestUtil::getRequestKeyValueFromURL("dtsmnid", current_path());
            $smnid = (isset($bottomURL)) ? RequestUtil::getRequestKeyValueFromURL("smnid", $bottomURL) : RequestUtil::getRequestKeyValueFromURL("smnid", current_path());
            if (isset($dtsmnid)) {
                $title = NodeSummaryUtil::getInitNodeSummaryTitle($dtsmnid);
            } else if (isset($smnid)) {
                $title = NodeSummaryUtil::getInitNodeSummaryTemplateTitle($smnid);
            } else {
                $title = _get_budget_breadcrumb_title_drilldown() . ' Revenue';
            }
        } else if (!$bottomURL && preg_match('/^revenue\/transactions/', current_path())) {
            $title = "Revenue Transactions";
        } else {
            $title = _get_budget_breadcrumb_title_drilldown() . ' Revenue';
        }

        return html_entity_decode($title);
    }

    /**
     * @return string
     */
    static function getRevenueNoRecordsMsg()
    {
        $title = '';
        $bottomURL = isset($_REQUEST['expandBottomContURL']) ? $_REQUEST['expandBottomContURL'] : '';
        $find = '_' . $bottomURL . current_path();
        if (
            stripos($bottomURL, 'transactions')
            || stripos($find, 'agency_revenue_by_cross_year_collections_details')
            || stripos($find, 'revenue_category_revenue_by_cross_year_collections_details')
            || stripos($find, 'funding_class_revenue_by_cross_year_collections_details')
            || stripos('_' . current_path(), 'revenue_transactions')
        ) {
            $smnid = ($bottomURL) ? RequestUtil::getRequestKeyValueFromURL("smnid", $bottomURL) : RequestUtil::getRequestKeyValueFromURL("smnid", current_path());
            $dtsmnid = ($bottomURL) ? RequestUtil::getRequestKeyValueFromURL("dtsmnid", $bottomURL) : RequestUtil::getRequestKeyValueFromURL("dtsmnid", current_path());
            if ($smnid > 0 || $dtsmnid > 0) {
                if ($dtsmnid > 0) {
                    $title = "There are no records to be displayed.";
                } else {
                    $bottomURL = ($bottomURL) ? $bottomURL : current_path();
                    $last_id = _getLastRequestParamValue($bottomURL);
                    if ($last_id["agency"] > 0) {
                        $title = _checkbook_project_get_name_for_argument("agency_id", RequestUtil::getRequestKeyValueFromURL("agency", $bottomURL));
                    } elseif ($last_id["revcat"] > 0) {
                        $title = _checkbook_project_get_name_for_argument("revenue_category_id", RequestUtil::getRequestKeyValueFromURL("revcat", $bottomURL));
                    } elseif (isset($last_id["fundsrccode"])) {
                        $title = _checkbook_project_get_name_for_argument("funding_class_code", RequestUtil::getRequestKeyValueFromURL("fundsrccode", $bottomURL));
                    }
                    $title = 'There are no records to be displayed for ' . $title . '.';
                }
            }
        } else {
            $title = "There are no revenue details.";
        }
        return html_entity_decode($title);
    }

    /** Returns top navigation URL
     * @param $domain
     * @return string
     */
    static function getTopNavURL($domain)
    {
        $path = '';
        $fiscalYearId = self::getFiscalYearIdForTopNavigation();
        switch ($domain) {
            case "contracts":
                //Get 'Contracts Bottom Slider' amounts
                $node = node_load(363);
                widget_config($node);
                widget_prepare($node);
                widget_invoke($node, 'widget_prepare');
                widget_data($node);
                if ($node->data[0]['total_contracts'] > 0 || $node->data[0]['current_amount_sum'] > 0) {
                    $contracts_landing_path = "contracts_landing/status/A";
                } else if ($node->data[1]['total_contracts'] > 0 || $node->data[1]['current_amount_sum'] > 0) {
                    $contracts_landing_path = "contracts_landing/status/R";
                } else if ($node->data[2]['total_contracts'] > 0 || $node->data[2]['current_amount_sum'] > 0) {
                    $contracts_landing_path = "contracts_revenue_landing/status/A";
                } else if ($node->data[3]['total_contracts'] > 0 || $node->data[3]['current_amount_sum'] > 0) {
                    $contracts_landing_path = "contracts_revenue_landing/status/R";
                } else if ($node->data[5]['total_contracts'] > 0 || $node->data[5]['current_amount_sum'] > 0) {
                    $contracts_landing_path = "contracts_pending_exp_landing";
                } else if ($node->data[6]['total_contracts'] > 0 || $node->data[6]['current_amount_sum'] > 0) {
                    $contracts_landing_path = "contracts_pending_rev_landing";
                } else {
                    return "";
                }

                $path = $contracts_landing_path . "/yeartype/B/year/" . $fiscalYearId . _checkbook_append_url_params(null, array(), true);
                if (_getRequestParamValue("agency") > 0) {
                    $path = $path . "/agency/" . _getRequestParamValue("agency");
                } else if (_checkbook_check_isEDCPage()) {
                    $path = $path . "/agency/9000";
                }
                if (_getRequestParamValue("vendor") > 0) {
                    $path = $path . "/vendor/" . _getRequestParamValue("vendor");
                }
                break;
            case "spending":
                $path = "spending_landing/yeartype/B/year/" . $fiscalYearId . _checkbook_append_url_params(null, array(), true);
                if (_getRequestParamValue("agency") > 0) {
                    $path = $path . "/agency/" . _getRequestParamValue("agency");
                } else if (_checkbook_check_isEDCPage()) {
                    $path = $path . "/agency/9000";
                }
                if (_getRequestParamValue("vendor") > 0) {
                    $path = $path . "/vendor/" . _getRequestParamValue("vendor");
                }
                break;
            case "payroll":
                $year = self::getCalYearIdForTopNavigation();
                //Payroll is always redirected to the respective Calendar Year irrespective of the 'yeartpe' paramenter in the URL for all the other Domains
                if (!preg_match('/payroll/', $_SERVER['REQUEST_URI'])) {
                    $yeartype = 'C';
                } else {
                    $yeartype = _getRequestParamValue("yeartype");
                }

                if (preg_match('/agency_landing/', current_path())) {
                    $path = "payroll/agency_landing/yeartype/" . $yeartype . "/year/" . $year;
                    $path .= _checkbook_project_get_url_param_string("title");
                    $path .= "/agency/" . _getRequestParamValue("agency");
                } else if (preg_match('/title_landing/', current_path())) {
                    $path = "payroll/title_landing/yeartype/" . $yeartype . "/year/" . $year;
                    $path .= _checkbook_project_get_url_param_string("agency");
                    $path .= "/title/" . _getRequestParamValue("title");
                } else {
                    $bottomURL = $_REQUEST['expandBottomContURL'];
                    $bottomURL = ($bottomURL) ? $bottomURL : current_path();
                    $last_parameter = _getLastRequestParamValue($bottomURL);
                    if ($last_parameter['agency'] > 0) {
                        $path = "payroll/agency_landing/yeartype/" . $yeartype . "/year/" . $year;
                        $path .= _checkbook_project_get_url_param_string("title");
                        $path .= "/agency/" . _getRequestParamValue("agency");
                    } else if ($last_parameter['title'] > 0) {
                        $path = "payroll/title_landing/yeartype/" . $yeartype . "/year/" . $year;
                        $path .= _checkbook_project_get_url_param_string("agency");
                        $path .= "/title/" . _getRequestParamValue("title");
                    } else { //NYC Level
                        $path = "payroll/yeartype/" . $yeartype . "/year/" . $year;
                    }
                }
                break;
            case "budget":
                if (_getRequestParamValue("agency") > 0) {
                    $path = "budget/yeartype/B/year/" . $fiscalYearId . "/agency/" . _getRequestParamValue("agency");
                } else {
                    $path = "budget/yeartype/B/year/" . $fiscalYearId;
                }
                break;
            case "revenue":
                if (_getRequestParamValue("agency") > 0) {
                    $path = "revenue/yeartype/B/year/" . $fiscalYearId . "/agency/" . _getRequestParamValue("agency");
                } else {
                    $path = "revenue/yeartype/B/year/" . $fiscalYearId;
                }
                break;
        }
        return $path;
    }

    /** Returns Year ID for Spending, Contracts, Budget and Revenue domains navigation URLs from Top Navigation
     * @return integer $fiscalYearId
     */
    static function getFiscalYearIdForTopNavigation()
    {
        if (_getRequestParamValue("year") != NULL) {
            $year = _getRequestParamValue("year");
        } else if (_getRequestParamValue("calyear") != NULL) {
            $year = _getRequestParamValue("calyear");
        } else {
            $year = _getCurrentYearID();
        }
        //For CY 2010 Payroll selection, other domains should be navigated to FY 2011
        $fiscalYearId = ($year == 111 && strtoupper(_getRequestParamValue("yeartype")) == 'C') ? 112 : $year;
        return $fiscalYearId;
    }

    /** Returns Year ID for Payroll domain navigation URLs from Top Navigation
     * @return integer $calYearId
     */
    static function getCalYearIdForTopNavigation()
    {
        $year = null;
        if (_getRequestParamValue("year") != NULL) {
            $year = _getRequestParamValue("year");
        } else if (_getRequestParamValue("calyear") != NULL) {
            $year = _getRequestParamValue("calyear");
        }
        $currentCalYear = _getCalendarYearID();
        if ($year > $currentCalYear) {
            $year = $currentCalYear;
        }
        return $year;
    }

    /** Checks if the current page is NYC level*/
    static function isNYCLevelPage()
    {
        self::isEDCPage();
        $landingPages = array("contracts_landing", "contracts_revenue_landing",
            "contracts_pending_rev_landing", "contracts_pending_exp_landing",
            "spending_landing", "payroll", "budget", "revenue");

        $url = $_GET['q'];
        $urlPath = drupal_get_path_alias($url);
        $pathParams = explode('/', $urlPath);
        if (in_array($pathParams[0], $landingPages)) {
            if ($pathParams[0] == "payroll" && $pathParams[1] == "search") {
                return false;
            }
            return true;

        }

        return false;
    }

    /**
     * @return bool
     */
    static function isEDCPage()
    {
        $vendor_id = _getRequestParamValue('vendor');
        if ($vendor_id != null) {
            $vendor = _checkbook_project_querydataset("checkbook:vendor", "vendor_customer_code", array("vendor_id" => $vendor_id));

            if ($vendor[0]['vendor_customer_code'] == "0000776804") {
                return true;
            } else {
                return false;
            }

        }
        return false;
    }

    /**
     * @return string
     */
    static function getEDCURL()
    {
        $vendor = _checkbook_project_querydataset("checkbook:vendor", "vendor_id", array("vendor_customer_code" => "0000776804"));
        $url = "contracts_landing/status/A/yeartype/B/year/" . _getCurrentYearID() . "/vendor/" . $vendor[0]['vendor_id'];
        return $url;
    }

    /**
     * @return string
     */
    static function getSpendingEDCURL()
    {
        $vendor = _checkbook_project_querydataset("checkbook:vendor", "vendor_id", array("vendor_customer_code" => "0000776804"));
        $url = "spending_landing/yeartype/B/year/" . _getCurrentYearID() . "/vendor/" . $vendor[0]['vendor_id'];
        return $url;
    }

    /**
     * @param null $current_state
     * @return string
     */
    static function getNextMWBEDashboardState($current_state = null)
    {
        if ($current_state == null) {
            $current_state = _getRequestParamValue('dashboard');
        }
        return "/dashboard/" . self::getNextMWBEDashboardStateParam($current_state);

    }

    /**
     * @param null $current_state
     * @return string
     */
    static function getNextMWBEDashboardStateParam($current_state = null)
    {
        $current_state = $current_state?:_getRequestParamValue('dashboard');

        if (!$current_state) {
            if (stripos('_' . current_path(), 'contracts')) {
                $domain = "contracts";
            } else {
                $domain = "spending";
            }
            $applicable_filters = MappingUtil::getCurrentPrimeMWBEApplicableFilters($domain);
            if (count($applicable_filters) == 0) {
                return 'ms';
            }
        }

        switch ($current_state) {
            case "mp" :
                return "mp";
                break;
            case "sp" :
                return "mp";
                break;
            case "ms" :
                return "ms";
                break;
            case "ss" :
                return "ms";
                break;
        }
        return "mp";

    }

    /*
     * 
     * 
     * 
     *  If MWBE is clicked first the flow becomes prim flow. states mp and sp
     *  If subvendor is clicked first the flow becomes subvendor flow. states ms and ms
     */

    /**
     * @param null $current_state
     * @return string
     */
    static function getNextSubvendorDashboardState($current_state = null)
    {
        $current_state = $current_state ?: _getRequestParamValue('dashboard');

        return "/dashboard/" . self::getNextSubvendorDashboardStateParam($current_state);
    }

    /*
    *
    *
    *
    *  If MWBE is clicked first the flow becomes prim flow. states mp and sp
    *  If subvendor is clicked first the flow becomes subvendor flow. states ms and ms
    */

    /**
     * @param null $current_state
     * @return string
     */
    static function getNextSubvendorDashboardStateParam($current_state = null)
    {
        $current_state = $current_state ?: _getRequestParamValue('dashboard');

        switch ($current_state) {
            case "mp" :
                return "sp";
                break;
            case "sp" :
                return "sp";
                break;
            case "ms" :
                return "ss";
                break;
            case "ss" :
                return "ss";
                break;
        }
        return "ss";
    }


    /*
     * 
     * 
     * If subvendor is clicked first the flow becomes subvendor flow
     * 
     */

    /**
     * @param null $current_state
     * @return bool
     */
    static function isDashboardSubvendor($current_state = null)
    {
        $current_state = $current_state ?: _getRequestParamValue('dashboard');

        switch ($current_state) {
            case "sp" :
                return true;
            case "ss" :
                return true;
        }

        return false;
    }

    /**
     * @param $dashboard_filter
     * @return mixed|string
     */
    static function getDashboardTopNavURL($dashboard_filter)
    {

        if (self::isContractsSpendingLandingPage()) {
            $url = $_GET['q'];

            //Exclude parameters that should not persist in the feature dashboards for Spending Domain
            if (preg_match('/contract/', $url)) {
                $url = ContractUtil::getLandingPageWidgetUrl();
            } else {
                //Default to total spending
                $override_params = array("category" => null);
                $url = SpendingUtil::getLandingPageWidgetUrl($override_params);
            }

        } else {
            $url = self::getCurrentDomainURLFromParams();
        }

        switch ($dashboard_filter) {
            case "mwbe":
                if (_getRequestParamValue("dashboard") != null) {
                    $url = preg_replace('/\/dashboard\/[^\/]*/', '', $url);
                }
                $url .= "/dashboard/" . self::getNextMWBEDashboardStateParam();
                if (!preg_match('/mwbe/', $url)) {
                    if (_getRequestParamValue("mwbe") != null) {
                        $url .= "/mwbe/" . _getRequestParamValue("mwbe");
                    } else {
                        $url .= "/mwbe/2~3~4~5~9";
                    }
                }
                break;
            case "subvendor":

                if (_getRequestParamValue("dashboard") != null) {
                    $url = preg_replace('/\/dashboard\/[^\/]*/', '', $url);
                }
                // tm_wbe is an exception case for total MWBE link. When prime data is not present but sub data is present for the agency vendor combination.
                if (_getRequestParamValue("dashboard") == 'ms' && _getRequestParamValue("mwbe") == '2~3~4~5~9' && _getRequestParamValue("tm_wbe") != 'Y') {
                    $url = preg_replace('/\/mwbe\/[^\/]*/', '', $url);
                }

                $url .= "/dashboard/" . self::getNextSubvendorDashboardStateParam();
                break;
        }

        //For MWBE and Sub Vendor dashboard links add status parameters if it is not there
        //If status parameter is existing, set it to 'Active' always
        //Do this only for contracts
        if (preg_match('/contracts/', $url)) {
            if (!preg_match('/status/', $url)) {
                $url .= "/status/A";
            } else {
                $url = preg_replace('/\/status\/[^\/]*/', '/status/A', $url);
            }
        }

        return $url;
    }

    /**
     * @return bool
     */
    static function isContractsSpendingLandingPage()
    {
        $first_part = preg_replace('/\/.*/', '', $_GET['q']);
        if (in_array($first_part, self::$contracts_spending_landing_links)) {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    static function getCurrentDomainURLFromParams()
    {
        if (stripos('_'.current_path(), 'contract')) {
            $reqParams = MappingUtil::$contractsMWBEParamMap;
            $prefix = 'contracts_landing';
        } else {
            $reqParams = MappingUtil::$spendingMWBEParamMap;
            $prefix = 'spending_landing';
        }

        foreach ($reqParams as $key => $value) {
            $value = _getRequestParamValue($key);
            if ($key == "year") {
                $value = self::getFiscalYearIdForTopNavigation();
            }
            if ($key == "yeartype") {
                $value = 'B';
            }

            if ($key == "status" && $value == null) {
                $value = 'A';
            }
            $prefix .= ($value != null) ? "/$key/" . $value : "";

        }
        return $prefix;
    }

    /**
     * @param $dashboard_filter
     * @return string
     */
    static function getDashboardTopNavTitle($dashboard_filter)
    {
        switch ($dashboard_filter) {
            case "mwbe":
                if (self::isDashboardFlowSubvendor()) {
                    return "M/WBE (Sub Vendors)";
                }
                return "M/WBE";
            case "subvendor":
                // tm_wbe is an exception case for total MWBE link. When prime data is not present but sub data is present for the agency vendor combination.
                if (self::isDashboardFlowPrimevendor() || _getRequestParamValue("tm_wbe") == 'Y') {
                    return "Sub Vendors (M/WBE)";
                }
                return "Sub Vendors";
        }
        return '';
    }

    /**
     * @param null $current_state
     * @return bool
     */
    static function isDashboardFlowSubvendor($current_state = null)
    {

        if (self::$is_prime_mwbe_amount_zero_sub_mwbe_not_zero) {
            return true;
        }

        $current_state = $current_state ?: _getRequestParamValue('dashboard');

        return in_array($current_state, array('ms', 'ss'));
    }

    /**
     * @param null $current_state
     * @return bool
     */
    static function isDashboardFlowPrimevendor($current_state = null)
    {
        $current_state = $current_state ?: _getRequestParamValue('dashboard');

        return in_array($current_state, array('mp', 'sp'));
    }

    /**
     * @param $string
     * @param $param
     * @param $value
     * @return mixed
     */
    static function replaceParamFromString($string, $param, $value)
    {
        return preg_replace('/\/' . $param . '\/[^\/]*/', '/' . $param . '/' . $value, $string);
    }

    /**
     * @return string
     */
    static function getTotalSubvendorsLink()
    {
        $urlParamMap = $table = $default_params = $sub_vendors_total_link = null;
        if (stripos('_'.current_path(), 'contract')) {
            $domain = "contracts";
        } else {
            $domain = "spending";
        }

        switch ($domain) {
            case "spending":
                $table = "aggregateon_subven_spending_coa_entities";
                $urlParamMap = array("year" => "year_id", "yeartype" => "type_of_year", "agency" => "agency_id", "vendor" => "prime_vendor_id");
                $sub_vendors_total_link = RequestUtil::getLandingPageUrl("spending", _getRequestParamValue("year"), 'B');
                break;
            case "contracts":
                $table = "aggregateon_subven_contracts_cumulative_spending";
                $urlParamMap = array("year" => "fiscal_year_id", "agency" => "agency_id", "yeartype" => "type_of_year", "vendor" => "prime_vendor_id");
                $default_params = array("status_flag" => "A");
                $sub_vendors_total_link = RequestUtil::getLandingPageUrl("contracts", _getRequestParamValue("year"), 'B');
                break;
        }
        if (self::get_top_nav_records_count($urlParamMap, $default_params, $table) > 0) {
            return "/" . $sub_vendors_total_link . _checkbook_project_get_url_param_string("agency")
                . _checkbook_project_get_url_param_string("vendor") .
                "/dashboard/ss";
        }
        return '';
    }

    /**
     * @param $domain
     * @param null $year
     * @param null $yearType
     * @return null|string
     */
    static function getLandingPageUrl($domain, $year = null, $yearType = null)
    {
        $path = null;
        $year = $year ?: _getCurrentYearID();
        $yearType = $yearType ?: 'B';

        switch ($domain) {
            case "contracts":
                $path = "contracts_landing/status/A/yeartype/B/year/" . $year;
                break;
            case "spending":
                $path = "spending_landing/yeartype/B/year/" . $year;
                break;
            case "payroll":
                $path = "payroll/yeartype/" . $yearType . "/year/" . $year;
                break;
            case "budget":
                $path = "budget/yeartype/B/year/" . $year;
                break;
            case "revenue":
                $path = "revenue/yeartype/B/year/" . $year;
                break;
        }

        return $path;
    }

    /**
     * @param $urlParamMap
     * @param $default_params
     * @param $table
     * @return mixed
     */
    static function get_top_nav_records_count($urlParamMap, $default_params, $table)
    {
        $where_filters = array();
        $where_filter = null;

        foreach ($urlParamMap as $param => $value) {
            if (_getRequestParamValue($param) != null) {
                $where_filters[] = _widget_build_sql_condition(' a1.' . $value, _getRequestParamValue($param));
            }
        }

        foreach ($default_params as $param => $value) {
            $where_filters[] = _widget_build_sql_condition(' a1.' . $param, $value);
        }

        if (count($where_filters) > 0) {
            $where_filter = ' where ' . implode(' and ', $where_filters);
        }

        $sql = 'select count(*) count
				    from ' . $table . ' a1
				   ' . $where_filter;
        $data = _checkbook_project_execute_sql($sql);
        return $data[0]['count'];
    }

    /**
     * @return string
     */
    static function getTotalMWBELink()
    {
        $urlParamMap = $default_params = $table_subven = $table = $urlParamMapSubven = null;

        if (stripos('_'.current_path(), 'contract')) {
            $domain = "contracts";
        } else {
            $domain = "spending";
        }

        switch ($domain) {
            case "spending":
                $table = "aggregateon_mwbe_spending_coa_entities";
                $table_subven = "aggregateon_subven_spending_coa_entities";
                $urlParamMap = array("year" => "year_id", "agency" => "agency_id", "vendor" => "vendor_id");
                $urlParamMapSubven = array("year" => "year_id", "agency" => "agency_id", "vendor" => "prime_vendor_id");
                $default_params = array("minority_type_id" => "2~3~4~5~9", 'type_of_year' => 'B');
                break;
            case "contracts":
                $table = "aggregateon_mwbe_contracts_cumulative_spending";
                $table_subven = "aggregateon_subven_contracts_cumulative_spending";
                $urlParamMap = array("year" => "fiscal_year_id", "agency" => "agency_id", "vendor" => "vendor_id");
                $urlParamMapSubven = array("year" => "fiscal_year_id", "agency" => "agency_id", "vendor" => "prime_vendor_id");
                $default_params = array("status_flag" => "A", "minority_type_id" => "2~3~4~5~9", 'type_of_year' => 'B');
                break;
        }
        if (self::get_top_nav_records_count($urlParamMap, $default_params, $table) > 0) {
            $dashboard = "mp";
        } elseif (self::get_top_nav_records_count($urlParamMapSubven, $default_params, $table_subven) > 0) {
            // tm_wbe is an exception case for total MWBE link. When prime data is not present but sub data is present for the agency vendor combination.
            $dashboard = "ms/tm_wbe/Y";
        } else {
            return "";
        }
        return '/' . RequestUtil::getLandingPageUrl($domain, self::getFiscalYearIdForTopNavigation(), 'B') . "/mwbe/" . MappingUtil::$total_mwbe_cats
            . "/dashboard/" . $dashboard .
            _checkbook_project_get_url_param_string("agency")
            . _checkbook_project_get_url_param_string("vendor");
    }

    /**
     * Function will derive whether this is an advanced search transaction page based on the query string
     * @return bool|int
     */
    static function isAdvancedSearchPage()
    {
        $bottomURL = $_REQUEST['expandBottomContURL'];
        $domain = self::getDomain();
        $currentPath = current_path();
        $isAdvancedSearch = !$bottomURL || !isset($domain);

        if ($isAdvancedSearch) {
            switch ($domain) {
                case 'budget':
                    $isAdvancedSearch = preg_match('/^budget\/transactions/', $currentPath);
                    break;
                case 'revenue':
                    $isAdvancedSearch = preg_match('/^revenue\/transactions/', $currentPath);
                    break;
                case 'spending':
                    $isAdvancedSearch = !preg_match('/smnid/', $currentPath) &&
                        (preg_match('/spending\/search\/transactions/', $currentPath));
                    break;
                case 'contract':
                    $isAdvancedSearch = !preg_match('/smnid/', $currentPath) &&
                        (preg_match('/contract\/all\/transactions/', $currentPath)
                            || preg_match('/contract\/search\/transactions/', $currentPath));
                    break;
                case 'payroll':
                    $isAdvancedSearch = preg_match('/^payroll\/search\/transactions/', $currentPath);
                    break;
            }
        }

        return $isAdvancedSearch;
    }

    /**
     * @return null|string
     */
    static function getDomain()
    {
        $currentPath = current_path();
        switch(true){
            case (0 === stripos($currentPath, 'budget')):   return 'budget';
            case (0 === stripos($currentPath, 'revenue')):   return 'revenue';
            case (0 === stripos($currentPath, 'spending')):   return 'spending';
            case (0 === stripos($currentPath, 'contract')):   return 'contract';
            case (0 === stripos($currentPath, 'payroll')):   return 'payroll';
        }
        return null;
    }
}
