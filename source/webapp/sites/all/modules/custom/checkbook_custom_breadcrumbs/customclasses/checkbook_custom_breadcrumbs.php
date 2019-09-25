<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CustomBreadcrumbs
{
  /** Returns Revenue page title and Breadcrumb */
  public static function getRevenueBreadcrumbTitle()
  {
    $bottomURL = isset($_REQUEST['expandBottomContURL']) ? $_REQUEST['expandBottomContURL'] : FALSE;
    $find = '_' . $bottomURL . current_path();
    if (
      stripos($bottomURL, 'transactions')
      || stripos($find, 'agency_revenue_by_cross_year_collections_details')
      || stripos($find, 'revenue_category_revenue_by_cross_year_collections_details')
      || stripos($find, 'funding_class_revenue_by_cross_year_collections_details')
      || stripos('_' . current_path(), 'revenue_transactions')
    ) {
      $dtsmnid = $bottomURL ? RequestUtil::getRequestKeyValueFromURL("dtsmnid", $bottomURL) : RequestUtil::getRequestKeyValueFromURL("dtsmnid", current_path());
      $smnid = $bottomURL ? RequestUtil::getRequestKeyValueFromURL("smnid", $bottomURL) : RequestUtil::getRequestKeyValueFromURL("smnid", current_path());
      if (isset($dtsmnid)) {
        $title = NodeSummaryUtil::getInitNodeSummaryTitle($dtsmnid);
      } else if (isset($smnid)) {
        $title = NodeSummaryUtil::getInitNodeSummaryTemplateTitle($smnid);
      } else {
        $title = _get_budget_breadcrumb_title_drilldown() . ' Revenue';
      }
    } else if (!$bottomURL && stripos('_' . current_path(), 'revenue/transactions/')) {
      $title = "Revenue Transactions";
    } else {
      $title = _get_budget_breadcrumb_title_drilldown() . ' Revenue';
    }

    return html_entity_decode($title);
  }

  /** Returns Budget page title and Breadcrumb */
  public static function getBudgetBreadcrumbTitle()
  {
    $bottomURL = isset($_REQUEST['expandBottomContURL']) ? $_REQUEST['expandBottomContURL'] : FALSE;
    $find = '_' . $bottomURL . current_path();

    $title = _get_budget_breadcrumb_title_drilldown() . ' Expense Budget';

    if (!$bottomURL && stripos('_'.current_path(),'budget/transactions/')) {
      $title = "Expense Budget Transactions";
    } elseif (
      stripos($find, 'transactions')
      || stripos($find, 'deppartment_budget_details')
      || stripos($find, 'expense_category_budget_details')
    ) {
      $dtsmnid = $bottomURL ? RequestUtil::getRequestKeyValueFromURL("dtsmnid", $bottomURL) : RequestUtil::getRequestKeyValueFromURL("dtsmnid", current_path());
      $smnid = $bottomURL ? RequestUtil::getRequestKeyValueFromURL("smnid", $bottomURL) : RequestUtil::getRequestKeyValueFromURL("smnid", current_path());
      if (isset($dtsmnid)) {
        $title = NodeSummaryUtil::getInitNodeSummaryTitle($dtsmnid);
      } else if (isset($smnid)) {
        $title = NodeSummaryUtil::getInitNodeSummaryTemplateTitle($smnid);
      }
    }

    return html_entity_decode($title);
  }

  /** Returns Spending page title and Breadcrumb */
  public static function getSpendingBreadcrumbTitle()
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
        $title = $title . ' ' . RequestUtil::getDashboardTitle() . ' '.SpendingUtil::getSpendingCategoryName();
      }
    } else {
      $title = _get_spending_breadcrumb_title_drilldown(false) . ' ' . RequestUtil::getDashboardTitle() . ' '.SpendingUtil::getSpendingCategoryName();
    }

    return html_entity_decode($title);
  }

  /** Returns Contracts page title and Breadcrumb */
  public static function getContractBreadcrumbTitle()
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

  /** Returns Payroll page title and Breadcrumb */
  public static function getPayrollBreadcrumbTitle()
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
      $payroll_type = RequestUtil::getRequestKeyValueFromURL("payroll_type", $bottomURL);
      if(isset($payroll_type)){
        $title = PayrollUtil::getPayrollTitlebyType($payroll_type);
      }
      else if ($smnid > 0) {
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
      if(Datasource::isNYCHA()) {
        $title = strtoupper(Dashboard::NYCHA)." ";
      }
      $title .= "Payroll Transactions";

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

  /** Returns NYCHA Spending page title and Breadcrumb */
  public static function getNYCHASpendingBreadcrumbTitle()
  {
    $bottomURL = $_REQUEST['expandBottomContURL'];
    if (isset($bottomURL) && preg_match('/transactions/', $bottomURL)) {
      $title = NychaSpendingUtil::getTransactionsTitle($bottomURL);
    }else {
      $lastReqParam = _getLastRequestParamValue();
      foreach ($lastReqParam as $key => $value) {
        switch ($key) {
          case 'vendor':
            $title = _checkbook_project_get_name_for_argument("vendor_id", $value);
            break;
          case 'industry':
            $title = _checkbook_project_get_name_for_argument("industry_type_id", $value);
            break;
          case 'fundsrc':
            $title = _checkbook_project_get_name_for_argument("funding_source_id", $value);
            break;
          default:
            $title = "New York City Housing Authority";
        }
        $title .= ' '. NYCHASpendingUtil::getCategoryName() .' Spending';
      }
    }
    return html_entity_decode($title);
  }

  /** Returns NYCHA Contracts page title and Breadcrumb */
  public static function getNYCHAContractBreadcrumbTitle()
  {
    $bottomURL = $_REQUEST['expandBottomContURL'];
    if (!$bottomURL && preg_match('/^nycha_contracts\/search\/transactions/', current_path()) || preg_match('/^nycha_contracts\/all\/transactions/', current_path())) {
      $title = 'NYCHA Contracts Transactions';
    } else if(stripos($bottomURL, 'transactions')){
      $code= RequestUtil::getRequestKeyValueFromURL("tCode",$bottomURL);
      $title = NYCHAContractUtil::getTitleByCode($code). ' Contracts Transactions';
    } else if (preg_match('/contract/', $bottomURL)) {
      $title = RequestUtil::getRequestKeyValueFromURL("contract", $bottomURL);
    }else {
      $lastReqParam = _getLastRequestParamValue();
      foreach ($lastReqParam as $key => $value) {
        switch ($key) {
          case 'vendor':
            $title = _checkbook_project_get_name_for_argument("vendor_id", $value);
            break;
          case 'awdmethod':
            $title = _checkbook_project_get_name_for_argument("award_method_id", $value);
            break;
          case 'award_method':
            $title = _checkbook_project_get_name_for_argument("award_method_id", $value);
            break;
          case 'csize':
            $title = _checkbook_project_get_name_for_argument("award_size_id", $value);
            break;
          case 'industry':
            $title = _checkbook_project_get_name_for_argument("industry_type_id", $value);
            break;
          default:
            $title = "New York City Housing Authority";
        }
        $title .= ' Contracts';
      }
    }
    return $title;
  }
}
?>
