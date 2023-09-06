<?php
namespace Drupal\widget_services\Common;

class CommonService  {

  /**
   * Function to call the service class
   * @return mixed
   */
  public static function callServiceName($serviceName)
  {
    $service = null;
    $serviceName = !isset($serviceName) ? "CommonService" : $serviceName;

      switch ($serviceName) {
        case 'SpendingService':
          $service = '\Drupal\widget_services\Spending\SpendingService';
          break;
        case 'NychaSpendingWidgetService':
          //$service = new \Drupal\checkbook_services\NychaSpending\NychaSpendingWidgetService($node->widgetConfig);
          break;
        case 'ContractService':
          $service = '\Drupal\widget_services\Contracts\ContractService';
          break;
        case 'NychaContractsService':
          //$service = new \Drupal\checkbook_services\NychaContracts\NychaContractsWidgetService($node->widgetConfig);
          break;
        case 'BudgetService':
          //$service = new \Drupal\checkbook_services\Budget\BudgetWidgetService($node->widgetConfig);
          break;
        case 'NychaBudgetService':
          //$service = new \Drupal\checkbook_services\NychaBudget\NychaBudgetWidgetService($node->widgetConfig);
          break;
        case 'RevenueService':
          $service = '\Drupal\widget_services\Revenue\RevenueService';
          break;
        case 'NychaRevenueService':
          //$service = new \Drupal\checkbook_services\NychaRevenue\NychaRevenueWidgetService($node->widgetConfig);
          break;
        case 'PayrollService':
          //$service = new \Drupal\checkbook_services\Payroll\PayrollWidgetService($node->widgetConfig);
          break;
      }
    return $service;
  }

  public static function getTemplatePath($templateName)
  {
    $widget_config = \Drupal::service('extension.list.module')->getPath('widget_config');
    switch ($templateName) {
      case 'budget_expense_transaction_total_amount':
        $templatePath = $widget_config . '/templates/Citywide/Budget/TransactionsSummary/expense_transaction_total_amount.html.twig';
        break;
      case 'budget_expense_transactions_summary':
        $templatePath = $widget_config . '/templates/Citywide/Budget/TransactionsSummary/expense_transactions_summary.html.twig';
        break;
      case 'budget_revenue_transaction_total_amount':
        $templatePath = $widget_config . '/templates/Citywide/Budget/TransactionsSummary/revenue_transaction_total_amount.html.twig';
        break;
      case 'highchart_spending_vendor_grid_view':
        $templatePath = $widget_config . '/templates/Citywide/Spending/ChartGridview/spending_highchart_vendor.html.twig';
        break;
      case 'highchart_revenue_grid_view':
        $templatePath = $widget_config . '/templates/Citywide/Revenue/ChartGridview/revenue_highchart.html.twig';
        break;
      case 'highchart_revenue_agency_grid_view':
        $templatePath = $widget_config . '/templates/Citywide/Revenue/ChartGridview/revenue_agency_highchart.html.twig';
        break;
      case 'highchart_revenue_revcat_grid_view':
        $templatePath = $widget_config . '/templates/Citywide/Revenue/ChartGridview/revenue_revcat_highchart.html.twig';
        break;
      case 'highchart_revenue_comparisons_grid_view':
        $templatePath = $widget_config . '/templates/Citywide/Revenue/ChartGridview/revenue_year_comparisons.html.twig';
        break;
      case 'highchart_revenue_fndcls_grid_view':
        $templatePath = $widget_config . '/templates/Citywide/Revenue/ChartGridview/revenue_fndcls_highchart.html.twig';
        break;
      case 'highchart_nycha_revenue_grid_view':
        $templatePath = $widget_config . '/templates/Nycha/Revenue/ChartGridview/revenue_nycha_highchart.html.twig';
        break;
      case 'highchart_nycha_revenue_respcenter_grid_view':
        $templatePath = $widget_config . '/templates/Nycha/Revenue/ChartGridview/nycha_revenue_respcenter_highchart.html.twig';
        break;
      case 'highchart_nycha_revenue_categories_grid_view':
        $templatePath = $widget_config . '/templates/Nycha/Revenue/ChartGridview/nycha_revenue_categories_highchart.html.twig';
        break;
      case 'highchart_nycha_revenue_expcategory_grid_view':
        $templatePath = $widget_config . '/templates/Nycha/Revenue/ChartGridview/nycha_revenue_expcategory_highchart.html.twig';
        break;
      case 'highchart_nycha_revenue_fundsrc_grid_view':
        $templatePath = $widget_config . '/templates/Nycha/Revenue/ChartGridview/nycha_revenue_fundsrc_highchart.html.twig';
        break;
      case 'highchart_nycha_revenue_programs_grid_view':
        $templatePath = $widget_config . '/templates/Nycha/Revenue/ChartGridview/nycha_revenue_program_highchart.html.twig';
        break;
      case 'highchart_nycha_revenue_projects_grid_view':
        $templatePath = $widget_config . '/templates/Nycha/Revenue/ChartGridview/nycha_revenue_project_highchart.html.twig';
        break;
      case 'highchart_budget_grid_view':
        $templatePath = $widget_config . '/templates/Citywide/Budget/ChartGridview/budget_highchart.html.twig';
        break;
      case 'highchart_budget_agency_grid_view':
        $templatePath = $widget_config . '/templates/Citywide/Budget/ChartGridview/budget_agency_highchart.html.twig';
        break;
      case 'highchart_budget_department_grid_view':
        $templatePath = $widget_config . '/templates/Citywide/Budget/ChartGridview/budget_department_highchart.html.twig';
        break;
      case 'highchart_budget_expenditure_grid_view':
        $templatePath = $widget_config . '/templates/Citywide/Budget/ChartGridview/budget_expenditure_highchart.html.twig';
        break;
      case 'highchart_nycha_budget_expcategory_grid_view':
        $templatePath = $widget_config . '/templates/Nycha/Budget/ChartGridview/nycha_budget_expcategory_highchart.html.twig';
        break;
      case 'highchart_nycha_budget_fundsrc_grid_view':
        $templatePath = $widget_config . '/templates/Nycha/Budget/ChartGridview/nycha_budget_fundsrc_highchart.html.twig';
        break;
      case 'highchart_nycha_budget_programs_grid_view':
        $templatePath = $widget_config . '/templates/Nycha/Budget/ChartGridview/nycha_budget_program_highchart.html.twig';
        break;
      case 'highchart_nycha_budget_projects_grid_view':
        $templatePath = $widget_config . '/templates/Nycha/Budget/ChartGridview/nycha_budget_project_highchart.html.twig';
        break;
      case 'highchart_nycha_budget_respcenter_grid_view':
        $templatePath = $widget_config . '/templates/Nycha/Budget/ChartGridview/nycha_budget_respcenter_highchart.html.twig';
        break;
      case 'payroll_by_agency_gross_ot_pay':
        $templatePath = $widget_config . '/templates/Citywide/Payroll/ChartGridview/payroll_by_agency_gross_ot_pay.html.twig';
        break;
      case 'payroll_by_gross_ot_pay':
        $templatePath = $widget_config . '/templates/Citywide/Payroll/ChartGridview/payroll_by_gross_ot_pay.html.twig';
        break;
      case 'chart_grid_view':
        $templatePath = $widget_config . '/templates/Citywide/Payroll/ChartGridview/chart_grid_view.html.twig';
        break;
      case 'highchart_spending_contract_grid_view':
        $templatePath = $widget_config . '/templates/Citywide/Spending/ChartGridview/spending_highchart_contract.html.twig';
        break;
      case 'highchart_spending_fy_grid_view':
        $templatePath = $widget_config . '/templates/Citywide/Spending/ChartGridview/spending_highchart_fy.html.twig';
        break;
      case 'highchart_spending_agency_grid_view':
        $templatePath = $widget_config . '/templates/Citywide/Spending/ChartGridview/spending_highchart_agency.html.twig';
        break;
      case 'highchart_spending_contract_disbursement_grid_view':
        $templatePath = $widget_config . '/templates/Citywide/Spending/ChartGridview/spending_highchart_contract_disbursement.html.twig';
        break;
      case 'highchart_spending_prime_vendor_grid_view':
        $templatePath = $widget_config . '/templates/Citywide/Spending/ChartGridview/spending_highchart_prime_vendor.html.twig';
        break;
      case 'highchart_spending_subvendor_grid_view':
        $templatePath = $widget_config . '/templates/Citywide/Spending/ChartGridview/spending_highchart_subvendor.html.twig';
        break;
      case 'nycha_revenue_transactions_summary':
        $templatePath = $widget_config . '/templates/Nycha/Revenue/TransactionsSummary/transactions_summary.html.twig';
        break;
      case 'nycha_budget_transactions_summary':
        $templatePath = $widget_config . '/templates/Nycha/Budget/TransactionsSummary/transactions_summary.html.twig';
        break;
      case 'revenue_recognized_cross_year_total_amount':
        $templatePath = $widget_config . '/templates/Citywide/Revenue/TransactionsSummary/recognized_cross_year_total_amount.html.twig';
        break;
      case 'revenue_transaction_cross_year_total_amount':
        $templatePath = $widget_config . '/templates/Citywide/Revenue/TransactionsSummary/transactions_cross_year_total_amount.html.twig';
        break;
      case 'revenue_transactions_summary':
        $templatePath = $widget_config . '/templates/Citywide/Revenue/TransactionsSummary/transactions_summary.html.twig';
        break;
      case 'revenue_transaction_total_amount':
        $templatePath = $widget_config . '/templates/Citywide/Revenue/TransactionsSummary/transactions_total_amount.html.twig';
        break;
      case 'spending_agency_summary':
        $templatePath = $widget_config . '/templates/Citywide/Spending/TransactionsSummary/agency_summary.html.twig';
        break;
      case 'spending_exp_category_summary':
        $templatePath = $widget_config . '/templates/Citywide/Spending/TransactionsSummary/exp_category_summary.html.twig';
        break;
      case 'spending_vendor_summary':
        $templatePath = $widget_config . '/templates/Citywide/Spending/TransactionsSummary/vendor_summary.html.twig';
        break;
      case 'spending_sub_vendor_summary':
        $templatePath = $widget_config . '/templates/Citywide/Spending/TransactionsSummary/sub_vendor_summary.html.twig';
        break;
      case 'spending_contract_summary':
        $templatePath = $widget_config . '/templates/Citywide/Spending/TransactionsSummary/contract_summary.html.twig';
        break;
      case 'spending_industry_summary':
        $templatePath = $widget_config . '/templates/Citywide/Spending/TransactionsSummary/industry_summary.html.twig';
        break;
      case 'nycha_spending_transactions_summary':
        $templatePath = $widget_config . '/templates/Nycha/Spending/TransactionsSummary/transactions_summary.html.twig';
        break;
      case 'nycha_spending_bottom_slider':
        $templatePath = $widget_config . '/templates/Nycha/Spending/TransactionsSummary/nycha_spending_bottom_slider.html.twig';
        break;
      case 'highchart_nycha_spending_by_month_grid_view':
        $templatePath = $widget_config . '/templates/Nycha/Spending/ChartGridview/nycha_spending_by_month.html.twig';
        break;
      case 'payroll_title_payroll_summary':
        $templatePath = $widget_config . '/templates/Citywide/Payroll/TransactionsSummary/title_payroll_summary.html.twig';
        break;
      case 'payroll_agency_payroll_summary':
        $templatePath = $widget_config . '/templates/Citywide/Payroll/TransactionsSummary/agency_payroll_summary.html.twig';
        break;
      case 'payroll_title_agency_payroll_summary':
        $templatePath = $widget_config . '/templates/Citywide/Payroll/TransactionsSummary/title_agency_payroll_summary.html.twig';
        break;
      case 'payroll_employee_payroll_summary':
        $templatePath = $widget_config . '/templates/Citywide/Payroll/TransactionsSummary/employee_payroll_summary.html.twig';
        break;
      case 'payroll_employee_agency_payroll_summary':
        $templatePath = $widget_config . '/templates/Citywide/Payroll/TransactionsSummary/employee_agency_payroll_summary.html.twig';
        break;
      case 'payroll_nyc_title_payroll_summary':
        $templatePath = $widget_config . '/templates/Citywide/Payroll/TransactionsSummary/nyc_title_payroll_summary.html.twig';
        break;
      case 'payroll_nyc_payroll_summary_by_month':
        $templatePath = $widget_config . '/templates/Citywide/Payroll/TransactionsSummary/nyc_payroll_summary_by_month.html.twig';
        break;
      case 'active_registered_transaction_title':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/TransactionsSummary/ActiveRegisteredExpense/active_registered_transaction_title.html.twig';
        break;
      case 'active_registered_contract_transaction_total_amount':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/TransactionsSummary/ActiveRegisteredExpense/active_registered_transaction_total_amount.html.twig';
        break;
      case 'contracts_ca_details':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/ContractDetails/contracts_ca_details.html.twig';
        break;
      case 'contracts_cta_history':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/ContractDetails/contracts_cta_history.html.twig';
        break;
      case 'contracts_cta_spending_top':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/ContractDetails/contracts_cta_spending_top.html.twig';
        break;
      case 'contracts_cta_spending_bottom':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/ContractDetails/contracts_cta_spending_bottom.html.twig';
        break;
      case 'contracts_cta_spending_by_exp_cat':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/ContractDetails/contracts_cta_spending_by_exp_cat.html.twig';
        break;
      case 'contracts_cta_spending_history':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/ContractDetails/contracts_cta_spending_history.html.twig';
        break;
      case 'contracts_ma_assoc_contracts':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/ContractDetails/contracts_ma_assoc_contracts.html.twig';
        break;
      case 'contracts_ma_history':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/ContractDetails/contracts_ma_history.html.twig';
        break;
      case 'contracts_mma_details':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/ContractDetails/contracts_mma_details.html.twig';
        break;
      case 'contracts_bottom_slider':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/Slider/contracts_bottom_slider.html.twig';
        break;
      case 'contracts_subvendors_bottom_slider':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/Slider/subvendor_bottom_slider.html.twig';
        break;
      case 'contracts_oge_cta_all_vendor_info':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/ContractDetails/contracts_oge_cta_all_vendor_info.html.twig';
        break;
      case 'contracts_oge_cta_spending_bottom':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/ContractDetails/contracts_oge_cta_spending_bottom.html.twig';
        break;
      case 'contracts_oge_ma_assoc_contracts':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/ContractDetails/contracts_oge_ma_assoc_contracts.html.twig';
        break;
      case 'contract_summary_expense_expense':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/TransactionsSummary/expense.html.twig';
        break;
      case 'contract_summary_expense_contract':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/TransactionsSummary/contract.html.twig';
        break;
      case 'contract_summary_expense_contract_modification':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/TransactionsSummary/contract_modification.html.twig';
        break;
      case 'contract_vendor_info':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/ContractDetails/contract_vendor_info.html.twig';
        break;
      case 'contract_date_summary':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/TransactionsSummary/date_summary.html.twig';
        break;
      case 'sub_contract_summary_expense_contract':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/TransactionsSummary/sub_contract.html.twig';
        break;
      case 'spending_date_summary':
        $templatePath = $widget_config . '/templates/Citywide/Spending/TransactionsSummary/date_summary.html.twig';
        break;
      case 'nycha_contract_assoc_releases':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/ContractDetails/nycha_contract_assoc_releases.html.twig';
        break;
      case 'nycha_contract_details':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/ContractDetails/nycha_contract_details.html.twig';
        break;
      case 'pending_contract_details':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/Pending/pending_contract_details.html.twig';
        break;
      case 'pending_contract_vendor_info':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/Pending/pending_contract_vendor_info.html.twig';
        break;
      case 'mini_panel_contracts_cta_history':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/ContractDetails/mini_panel_contracts_cta_history.html.twig';
        break;
      case 'nycha_contract_transaction_total_amount':
        $templatePath = $widget_config . '/templates/Nycha/Contracts/TransactionsSummary/nycha_contract_transaction_total_amount.html.twig';
        break;
      case 'nycha_contracts_summary':
        $templatePath = $widget_config . '/templates/Nycha/Contracts/TransactionsSummary/nycha_contracts_summary.html.twig';
        break;
      case 'year_list':
        $templatePath = $widget_config . '/templates/Sitewide/YearList/year_list.html.twig';
        break;
      case 'agencies_list':
        $templatePath = $widget_config . '/templates/Sitewide/AgencyList/agencies_list.html.twig';
        break;
      case 'top_navigation':
        $templatePath = $widget_config . '/templates/Sitewide/TopNavigation/top_navigation.html.twig';
        break;
      case 'edc_top_navigation':
        $templatePath = $widget_config . '/templates/Sitewide/TopNavigation/edc_top_navigation.html.twig';
        break;
      case 'nycha_top_navigation':
        $templatePath = $widget_config . '/templates/Sitewide/TopNavigation/nycha_top_navigation.html.twig';
        break;
      case 'spending_bottom_slider':
        $templatePath = $widget_config . '/templates/Citywide/Spending/Slider/spending_bottom_slider.html.twig';
        break;
      case 'highchart_contracts_pending_topamount_grid_view':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/ChartGridview/contract_pending_topamount_highchart.html.twig';
        break;
      case 'highchart_contracts_pending_agency_vendor':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/ChartGridview/contracts_highchart_pending_agency_vendor.html.twig';
        break;
      case 'highchart_contracts_topamount_grid_view':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/ChartGridview/contract_topamount_highchart.html.twig';
        break;
      case 'highchart_contract_grid_view':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/ChartGridview/contacts_nyc_highchart.html.twig';
        break;
      case 'highchart_contracts_active_agency_vendor':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/ChartGridview/contracts_highchart_active_agency_vendor.html.twig';
        break;
      case 'spending_transaction_total_amount':
        $templatePath = $widget_config . '/templates/Citywide/Spending/TransactionsSummary/transaction_total_amount.html.twig';
        break;
      case 'spending_prime_vendor_summary':
        $templatePath = $widget_config . '/templates/Citywide/Spending/TransactionsSummary/prime_vendor_summary.html.twig';
        break;
      case 'spending_dept_summary':
        $templatePath = $widget_config . '/templates/Citywide/Spending/TransactionsSummary/dept_summary.html.twig';
        break;
      case 'spending_sub_contract_summary':
        $templatePath = $widget_config . '/templates/Citywide/Spending/TransactionsSummary/sub_contract_summary.html.twig';
        break;
      case 'pending_transaction_title':
        $templatePath = $widget_config . '/templates/Citywide/Contracts/TransactionsSummary/pending_transaction_title.html.twig';
        break;

      case 'trends_ratios_of_outstanding_debt':
        $templatePath = $widget_config . '/templates/Sitewide/Trends/Debt_capacity_trends/ratios_of_outstanding_debt.html.twig';
        break;
      case 'trends_ratios_of_general_bonded_debt':
        $templatePath = $widget_config . '/templates/Sitewide/Trends/Debt_capacity_trends/ratios_of_general_bonded_debt.html.twig';
        break;
      case 'trends_legal_debt_margin':
        $templatePath = $widget_config . '/templates/Sitewide/Trends/Debt_capacity_trends/legal_debt_margin.html.twig';
        break;
      case 'trends_pledged_rev_cov_nyc_trans':
        $templatePath = $widget_config . '/templates/Sitewide/Trends/Debt_capacity_trends/pledged_rev_cov_nyc_trans.html.twig';
        break;

      case 'trends_assesed_val_and_estd_act_val':
        $templatePath = $widget_config . '/templates/Sitewide/Trends/Revenue_capacity_trends/assesed_val_and_estd_act_val.html.twig';
        break;
      case 'trends_property_tax_rates':
        $templatePath = $widget_config . '/templates/Sitewide/Trends/Revenue_capacity_trends/property_tax_rates.html.twig';
        break;
      case 'trends_property_tax_levies':
        $templatePath = $widget_config . '/templates/Sitewide/Trends/Revenue_capacity_trends/property_tax_levies.html.twig';
        break;
      case 'trends_tax_rate_by_class':
        $templatePath = $widget_config . '/templates/Sitewide/Trends/Revenue_capacity_trends/assessed_val_and_tax_rate_by_class.html.twig';
        break;
      case 'trends_collections_cancellations_abatements':
        $templatePath = $widget_config . '/templates/Sitewide/Trends/Revenue_capacity_trends/collections_cancellations_abatements.html.twig';
        break;
      case 'trends_uncollected_parking_violation_fee':
        $templatePath = $widget_config . '/templates/Sitewide/Trends/Revenue_capacity_trends/uncollected_parking_violation_fee.html.twig';
        break;
      case 'trends_hudson_yards_infra_corp':
        $templatePath = $widget_config . '/templates/Sitewide/Trends/Revenue_capacity_trends/hudson_yards_infra_corp.html.twig';
        break;

      case 'trends_cap_assets_stats_by_program':
        $templatePath = $widget_config . '/templates/Sitewide/Trends/Operational_trends/cap_assets_stats_by_program.html.twig';
        break;
      case 'trends_no_of_city_employees':
        $templatePath = $widget_config . '/templates/Sitewide/Trends/Operational_trends/no_of_city_employees.html.twig';
        break;

      case 'trends_changes_in_net_assets':
        $templatePath = $widget_config . '/templates/Sitewide/Trends/Financial_trends/changes_in_net_assets.html.twig';
        break;
      case 'trends_fund_bal_govt_funds':
        $templatePath = $widget_config . '/templates/Sitewide/Trends/Financial_trends/fund_bal_govt_funds.html.twig';
        break;
      case 'trends_changes_in_fund_bal':
        $templatePath = $widget_config . '/templates/Sitewide/Trends/Financial_trends/changes_in_fund_bal.html.twig';
        break;
      case 'trends_general_fund_revenue_other_fin_sources':
        $templatePath = $widget_config . '/templates/Sitewide/Trends/Financial_trends/general_fund_revenue_other_fin_sources.html.twig';
        break;
      case 'trends_general_fund_expend_other_fin_sources':
        $templatePath = $widget_config . '/templates/Sitewide/Trends/Financial_trends/general_fund_expend_other_fin_sources.html.twig';
        break;
      case 'trends_capital_proj_rev_by_agency':
        $templatePath = $widget_config . '/templates/Sitewide/Trends/Financial_trends/capital_proj_rev_by_agency.html.twig';
        break;
      case 'trends_nyc_edu_const_fund':
        $templatePath = $widget_config . '/templates/Sitewide/Trends/Financial_trends/nyc_edu_const_fund.html.twig';
        break;

      case 'trends_nyc_population':
        $templatePath = $widget_config . '/templates/Sitewide/Trends/Demographic_trends/nyc_population.html.twig';
        break;
      case 'trends_personal_income_tax_revenues':
        $templatePath = $widget_config . '/templates/Sitewide/Trends/Demographic_trends/personal_income_tax_revenues.html.twig';
        break;
      case 'trends_non_agr_employment':
        $templatePath = $widget_config . '/templates/Sitewide/Trends/Demographic_trends/non_agr_employment.html.twig';
        break;
      case 'trends_persons_rec_pub_asst':
        $templatePath = $widget_config . '/templates/Sitewide/Trends/Demographic_trends/persons_rec_pub_asst.html.twig';
        break;
      case 'trends_emp_status_of_resident_population':
        $templatePath = $widget_config . '/templates/Sitewide/Trends/Demographic_trends/emp_status_of_resident_population.html.twig';
        break;
    }
    return $templatePath;
  }
}
