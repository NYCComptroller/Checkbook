<?php

include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/RequestUtil.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/includes/checkbook_project.inc';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/constants/Constants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/util/CheckbookDateUtil.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_infrastructure_layer/constants/CommonConstants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_custom_breadcrumbs/checkbook_custom_breadcrumbs.module';
include_once CUSTOM_MODULES_DIR . '/checkbook_custom_breadcrumbs/customclasses/checkbook_custom_breadcrumbs.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/spending/SpendingUtil.php';



use PHPUnit\Framework\TestCase;

/**
 * Class CustomBreadcrumbs
 */
class CustomBreadcrumbsTest extends TestCase
{

    /**
     * Test GetBudgetBreadcrumbTitle function
     */
    public function testGetBudgetBreadcrumbTitle()
    {
        function filter_xss($text)
        {
            return $text;
        }

        $_GET['q'] = '/budget/yeartype/B/year/122';
        $this->assertEquals('New York City Expense Budget', CustomBreadcrumbs::getBudgetBreadcrumbTitle());

        $_REQUEST['expandBottomContURL'] = 'panel_html/budget_agency_perecent_difference_transactions/budget/agency_details/dtsmnid/560/yeartype/B/year/119';
        $this->assertEquals('getInitNodeSummaryTitle :: 560', CustomBreadcrumbs::getBudgetBreadcrumbTitle());

        $_REQUEST['expandBottomContURL'] = '/panel_html/budget_transactions/budget/transactions/smnid/558/yeartype/B/year/122/agency/56';
        $this->assertEquals('getInitNodeSummaryTemplateTitle :: 558', CustomBreadcrumbs::getBudgetBreadcrumbTitle());

        $_GET['q'] = 'budget/transactions/year/119';
        unset($_REQUEST['expandBottomContURL']);
        $this->assertEquals('New York City Expense Budget', CustomBreadcrumbs::getBudgetBreadcrumbTitle());

    }

    /**
     * Test NYCHABudgetBreadcrumbTitle function
     *
     */
    public function testNYCHABudgetBreadcrumbTitle()
    {
        $_GET['q']  = 'nycha_budget/year/121/datasource/checkbook_nycha/agency/162';
        $_REQUEST['expandBottomContURL'] = '/panel_html/nycha_budget_transactions/nycha_budget/transactions/year/121/datasource/checkbook_nycha/respcenter/1913/widget/comm_expense_category/budgettype/committed/expcategory/408';
        $this->assertEquals('Expense Category by Committed  Expense Budget Transactions', CustomBreadcrumbs::getNYCHABudgetBreadcrumbTitle());

        $_REQUEST['expandBottomContURL'] = '/panel_html/nycha_budget_transactions/nycha_budget/transactions/year/121/datasource/checkbook_nycha/respcenter/1913/widget/comm_fundsrc/budgettype/committed/fundsrc/856';
        $this->assertEquals('Funding Source by Committed  Expense Budget Transactions', CustomBreadcrumbs::getNYCHABudgetBreadcrumbTitle());

        $_GET['q'] = 'nycha_budget/search/transactions/datasource/checkbook_nycha/agency/162/year/121';
        unset($_REQUEST['expandBottomContURL']);
        $this->assertEquals('NYCHA Expense Budget Transactions', CustomBreadcrumbs::getNYCHABudgetBreadcrumbTitle());

        $_GET['q'] = 'nycha_budget/datasource/checkbook_nycha/agency/162/year/121';
        $this->assertEquals(' Expense Budget Transactions', CustomBreadcrumbs::getNYCHABudgetBreadcrumbTitle());
    }

    /**
     * Test getRevenueBreadcrumbTitle function
     *
     */
    public function testgetRevenueBreadcrumbTitle()
    {
        $_GET['q'] = '/revenue/transactions/year/122/fundcls/2';
        $this->assertEquals('New York City Revenue', CustomBreadcrumbs::getRevenueBreadcrumbTitle());

        $_REQUEST['expandBottomContURL'] = '/panel_html/revenue_transactions/budget/transactions/smnid/580/yeartype/B/year/122/revcat/15';
        $this->assertEquals('getInitNodeSummaryTemplateTitle :: 580', CustomBreadcrumbs::getRevenueBreadcrumbTitle());
    }


    /**
     * Test getRevenueBreadcrumbTitle function
     *
     */
    public function testNYCHARevenueBreadcrumbTitle()
    {
        $_GET['q']  = 'nycha_revenue/year/121/datasource/checkbook_nycha/agency/162';
        $_REQUEST['expandBottomContURL'] = '/panel_html/nycha_revenue_transactions/nycha_revenue/transactions/year/121/datasource/checkbook_nycha/widget/rec_program/program/616';
        $this->assertEquals('Program by Recognized Revenue Transactions', CustomBreadcrumbs::getNYCHARevenueBreadcrumbTitle());

        $_REQUEST['expandBottomContURL'] = '/panel_html/nycha_revenue_transactions/nycha_revenue/transactions/year/121/datasource/checkbook_nycha/widget/wt_expense_categories';
        $this->assertEquals('Revenue Expense Categories Revenue Transactions', CustomBreadcrumbs::getNYCHARevenueBreadcrumbTitle());

        $_REQUEST['expandBottomContURL'] = 'nycha_revenue/search/transactions/datasource/checkbook_nycha/agency/162/year/121';
        $_GET['q'] = 'nycha_revenue/search/transactions/datasource/checkbook_nycha/agency/162/year/121';

        $this->assertEquals('NYCHA RevenueTransactions', CustomBreadcrumbs::getNYCHARevenueBreadcrumbTitle());

        $_GET['q'] = 'nycha_revenue/datasource/checkbook_nycha/agency/162/year/121';
        $this->assertEquals(' Revenue Transactions', CustomBreadcrumbs::getNYCHARevenueBreadcrumbTitle());

        unset($_REQUEST['expandBottomContURL']);
    }


    /**
     * Test getSpendingBreadcrumbTitle function
     *
     */
    public function testgetSpendingBreadcrumbTitle()
    {

        $_GET['q']  = '/spending_landing/yeartype/B/year/122';
        $this->assertEquals('New York City  Total Spending', CustomBreadcrumbs::getSpendingBreadcrumbTitle());

        $_REQUEST['expandBottomContURL'] = '/panel_html/spending_transactions/spending/transactions/yeartype/B/year/122/expcategorycode/2110/smnid/22';
        $this->assertEquals('getInitNodeSummaryTitle :: 22', CustomBreadcrumbs::getSpendingBreadcrumbTitle());
        unset($_REQUEST['expandBottomContURL']);

        $_GET['q'] = '/spending/search/transactions/';
        $this->assertEquals('New York City  Total Spending', CustomBreadcrumbs::getSpendingBreadcrumbTitle());

        $_GET['q'] = 'spending/search/transactions/datasource/checkbook_oge/agency/9000';
        $this->assertEquals('  Total Spending', CustomBreadcrumbs::getSpendingBreadcrumbTitle());

    }

    /**
     * Test getNYCHASpendingBreadcrumbTitle function
     *
     */
    public function testgetNYCHASpendingBreadcrumbTitle()
    {
        $_GET['q']  = 'nycha_spending/datasource/checkbook_nycha/year/121/agency/162';
        $_REQUEST['expandBottomContURL'] = '/panel_html/nycha_spending_transactions/nycha_spending/transactions/year/121/agency/162/datasource/checkbook_nycha/widget/wt_vendors';
        $this->assertEquals('Vendors Total Spending Transactions', CustomBreadcrumbs::getNYCHASpendingBreadcrumbTitle());

        $_REQUEST['expandBottomContURL'] = '/panel_html/nycha_spending_transactions/nycha_spending/transactions/year/121/agency/162/datasource/checkbook_nycha/widget/ytd_resp_center/resp_center/178';
        $this->assertEquals('Responsibility Center Total Spending Transactions', CustomBreadcrumbs::getNYCHASpendingBreadcrumbTitle());

        unset($_REQUEST['expandBottomContURL']);
        $_GET['q'] = 'nycha_spending/search/transactions/datasource/checkbook_nycha/agency/162/yeartype/B/year/122';
        $this->assertEquals('NYCHA Total Spending Transactions', CustomBreadcrumbs::getNYCHASpendingBreadcrumbTitle());

        $_GET['q'] = 'nycha_spending/year/122/agency/162/datasource/checkbook_nycha';
        $this->assertEquals('New York City Housing Authority Total Spending', CustomBreadcrumbs::getNYCHASpendingBreadcrumbTitle());
    }

    /**
     * Test getNYCHASpendingBreadcrumbTitle function
     *
     */
    public function testgetNYCHAContractBreadcrumbTitle()
    {
        $_GET['q']  = 'nycha_contracts/datasource/checkbook_nycha/year/122/agency/162';
        $_REQUEST['expandBottomContURL'] = '/panel_html/nycha_contracts_transactions_page/nycha_contracts/transactions/year/122/agency/162/datasource/checkbook_nycha/agreement_type/BA/tCode/BAM/modamt/0';
        $this->assertEquals('Blanket Agreement Modifications Contracts Transactions', CustomBreadcrumbs::getNYCHAContractBreadcrumbTitle());

        $_REQUEST['expandBottomContURL'] = 'nycha_spending/transactions/year/122/agency/162/datasource/checkbook_nycha/syear/122/widget/inv_contract/po_num_inv/BA1404493/agg_type/BA/tcode/BA/newwindow';
        $this->assertEquals(' Contracts Transactions', CustomBreadcrumbs::getNYCHAContractBreadcrumbTitle());

        unset($_REQUEST['expandBottomContURL']);
        $_GET['q'] = '/nycha_contracts/all/transactions/agency/162/datasource/checkbook_nycha';
        $this->assertEquals('NYCHA Contracts Transactions', CustomBreadcrumbs::getNYCHAContractBreadcrumbTitle());

        $_GET['q'] = '/nycha_contracts/search/transactions/agency/162/yeartype/B/year/122/datasource/checkbook_nycha';
        $this->assertEquals('NYCHA Contracts Transactions', CustomBreadcrumbs::getNYCHAContractBreadcrumbTitle());
    }

    /**
     * Test getContractBreadcrumbTitle function
     *
     */
    public function testgetContractBreadcrumbTitle()
    {
        global $checkbook_breadcrumb_title;

        $checkbook_breadcrumb_title = "New York City Active Expense Contracts";
        $_GET['q']  = 'contracts_landing/status/A/yeartype/B/year/122';
        $this->assertEquals('New York City Active Expense Contracts', CustomBreadcrumbs::getContractBreadcrumbTitle());

        $checkbook_breadcrumb_title = "New York City Registered Expense Contracts";
        $_GET['q']  = 'contracts_landing/status/R/yeartype/B/year/122';
        $this->assertEquals('New York City Registered Expense Contracts', CustomBreadcrumbs::getContractBreadcrumbTitle());

        $checkbook_breadcrumb_title = "New York City Active Revenue Contracts";
        $_GET['q']  = 'contracts_revenue_landing/status/A/yeartype/B/year/122';
        $this->assertEquals('New York City Active Revenue Contracts', CustomBreadcrumbs::getContractBreadcrumbTitle());

        $checkbook_breadcrumb_title = "New York City Registered Revenue Contracts";
        $_GET['q']  = 'contracts_revenue_landing/status/R/yeartype/B/year/122';
        $this->assertEquals('New York City Registered Revenue Contracts', CustomBreadcrumbs::getContractBreadcrumbTitle());

        $checkbook_breadcrumb_title = "New York City Pending Expense Contracts";
        $_GET['q']  = 'contracts_pending_exp_landing/yeartype/B/year/122';
        $this->assertEquals('New York City Pending Expense Contracts', CustomBreadcrumbs::getContractBreadcrumbTitle());

        $checkbook_breadcrumb_title = "New York City M/WBE Active Expense Contracts";
        $_GET['q']  = 'contracts_landing/status/A/yeartype/B/year/122/status/A/dashboard/mp/mwbe/2~3~4~5~9';
        $this->assertEquals('New York City M/WBE Active Expense Contracts', CustomBreadcrumbs::getContractBreadcrumbTitle());

        $checkbook_breadcrumb_title = "New York City Total Active Sub Vendor Contracts";
        $_GET['q']  = '/contracts_landing/status/A/yeartype/B/year/122/status/A/mwbe/2~3~4~5~9/dashboard/sp';
        $this->assertEquals('New York City Total Active Sub Vendor Contracts', CustomBreadcrumbs::getContractBreadcrumbTitle());

        $checkbook_breadcrumb_title = "New York City Total Active Sub Vendor Contracts";
        $_GET['q']  = '/contracts_landing/status/A/yeartype/B/year/121/status/A/mwbe/2~3~4~5~9/dashboard/ms';
        $this->assertEquals('New York City Total Active Sub Vendor Contracts', CustomBreadcrumbs::getContractBreadcrumbTitle());

        $checkbook_breadcrumb_title = "New York City Total Active Sub Vendor Contracts";
        $_GET['q']  = '/contracts_landing/year/121/yeartype/B/dashboard/ss/status/A';
        $this->assertEquals('New York City Total Active Sub Vendor Contracts', CustomBreadcrumbs::getContractBreadcrumbTitle());

        $checkbook_breadcrumb_title = "New York City Pending Revenue Contracts";
        $_GET['q']  = 'contracts_pending_rev_landing/yeartype/B/year/122';
        $this->assertEquals('New York City Pending Revenue Contracts', CustomBreadcrumbs::getContractBreadcrumbTitle());

        $checkbook_breadcrumb_title = "Active Contracts Transactions";
        $_GET['q']  = 'contract/search/transactions/contstatus/A/contcat/all/doctype/MMA1~MA1~CTA1~CT1~DO1~RCT1/yeartype/B/year/122';
        $this->assertEquals('Active Contracts Transactions', CustomBreadcrumbs::getContractBreadcrumbTitle());

        $checkbook_breadcrumb_title = " Active Expense Contracts Transactions";
        $_GET['q']  = 'contract/all/transactions/contstatus/A/contcat/expense/doctype/MMA1~MA1~CTA1~CT1~DO1';
        $this->assertEquals(' Active Expense Contracts Transactions', CustomBreadcrumbs::getContractBreadcrumbTitle());

        $checkbook_breadcrumb_title = " Active Revenue Contracts Transactions";
        $_GET['q']  = 'contract/all/transactions/contstatus/A/contcat/revenue/doctype/RCT1';
        $this->assertEquals(' Active Revenue Contracts Transactions', CustomBreadcrumbs::getContractBreadcrumbTitle());

        $checkbook_breadcrumb_title = "Registered Contracts Transactions";
        $_GET['q']  = 'contract/all/transactions/contstatus/R/contcat/all/doctype/MMA1~MA1~CTA1~CT1~DO1~RCT1';
        $this->assertEquals('Registered Contracts Transactions', CustomBreadcrumbs::getContractBreadcrumbTitle());

        $checkbook_breadcrumb_title = "Registered Expense Contracts Transactions";
        $_GET['q']  = 'contract/all/transactions/contstatus/R/contcat/expense/doctype/MMA1~MA1~CTA1~CT1~DO1';
        $this->assertEquals('Registered Expense Contracts Transactions', CustomBreadcrumbs::getContractBreadcrumbTitle());

        $checkbook_breadcrumb_title = "Registered Revenue Contracts Transactions";
        $_GET['q']  = 'contract/search/transactions/contstatus/R/contcat/revenue/doctype/RCT1/yeartype/B/year/121';
        $this->assertEquals('Registered Revenue Contracts Transactions', CustomBreadcrumbs::getContractBreadcrumbTitle());


        $_REQUEST['expandBottomContURL']  = '/panel_html/contract_details/contract/transactions/contcat/expense/contstatus/A/yeartype/B/year/122/doctype/MMA1~MA1/smnid/371';
        $this->assertEquals('getInitNodeSummaryTitle :: 371', CustomBreadcrumbs::getContractBreadcrumbTitle());

        $_REQUEST['expandBottomContURL']  = '/panel_html/contract_details/contract/transactions/contcat/expense/contstatus/A/yeartype/B/year/122/doctype/MA1~CTA1~CT1/smnid/369';
        $this->assertEquals('getInitNodeSummaryTitle :: 369', CustomBreadcrumbs::getContractBreadcrumbTitle());

        $_REQUEST['expandBottomContURL']  = '/panel_html/contract_details/contract/transactions/contcat/expense/contstatus/R/yeartype/B/year/122/doctype/MA1~CTA1~CT1/smnid/367';
        $this->assertEquals('getInitNodeSummaryTitle :: 367', CustomBreadcrumbs::getContractBreadcrumbTitle());



    }

    /**
     * Test getSpendingBreadcrumbTitle function
     *
     */
    public function testgetPayrollBreadcrumbTitle()
    {
        global $checkbook_breadcrumb_title;
        $checkbook_breadcrumb_title = "Payroll Transactions";
        $_GET['q']  = '/payroll/search/transactions/yeartype/C/calyear/122/year/122/salamttype/1~2~3';
        $this->assertEquals('Payroll Transactions', CustomBreadcrumbs::getPayrollBreadcrumbTitle());

        $checkbook_breadcrumb_title = "New York City Payroll";
        $_GET['q']  = '/payroll/yeartype/C/year/122';
        $this->assertEquals('New York City Payroll', CustomBreadcrumbs::getPayrollBreadcrumbTitle());

        $checkbook_breadcrumb_title = "Administration For Children's Services Payroll";
        $_GET['q']  = '/payroll/agency_landing/agency/50/yeartype/C/year/122';
        $this->assertEquals('Administration For Children\'s Services Payroll', CustomBreadcrumbs::getPayrollBreadcrumbTitle());

        $checkbook_breadcrumb_title = "Youth Development Specialist Payroll";
        $_GET['q']  = '/payroll/title_landing/yeartype/C/year/122/agency/50/title/3259';
        $this->assertEquals('Youth Development Specialist Payroll', CustomBreadcrumbs::getPayrollBreadcrumbTitle());

        $checkbook_breadcrumb_title = "New York City Housing Authority Payroll";
        $_GET['q']  = 'payroll/agency_landing/yeartype/C/year/122/datasource/checkbook_nycha/agency/162';
        $this->assertEquals('New York City Housing Authority Payroll', CustomBreadcrumbs::getPayrollBreadcrumbTitle());

        $_REQUEST['expandBottomContURL']  = '/panel_html/payroll_agencytransactions/payroll/agencywide/transactions/yeartype/C/year/122/smnid/325/agency/50';
        $this->assertEquals('getInitNodeSummaryTitle :: 325', CustomBreadcrumbs::getPayrollBreadcrumbTitle());

        $_REQUEST['expandBottomContURL']= '/panel_html/payroll_agencytransactions/payroll/agencywide/transactions/yeartype/C/year/122/datasource/checkbook_nycha/payroll_type/nonsalaried/agency/162';
        $this->assertEquals(' Payroll Transactions', CustomBreadcrumbs::getPayrollBreadcrumbTitle());

        $_REQUEST['expandBottomContURL']='/panel_html/payroll_employee_transactions/payroll/employee/transactions/agency/162/yeartype/C/year/122/datasource/checkbook_nycha/abc/841848';
        $this->assertEquals('Individual Employee Payroll Transactions', CustomBreadcrumbs::getPayrollBreadcrumbTitle());

        $_REQUEST['expandBottomContURL']='/panel_html/payroll_nyc_title_transactions/payroll/payroll_title/transactions/yeartype/B/year/119/smnid/881';
        $this->assertEquals('getInitNodeSummaryTitle :: 881', CustomBreadcrumbs::getPayrollBreadcrumbTitle());

        $_REQUEST['expandBottomContURL']='/panel_html/payroll_nyc_transactions/payroll/transactions/yeartype/B/year/119/smnid/320';
        $this->assertEquals('getInitNodeSummaryTitle :: 320', CustomBreadcrumbs::getPayrollBreadcrumbTitle());

        $_REQUEST['expandBottomContURL']='/panel_html/payroll_by_month_nyc_transactions/payroll/monthly/transactions/yeartype/B/year/119/month/2016/smnid/491';
        $this->assertEquals('Overtime Payments by Month Transactions', CustomBreadcrumbs::getPayrollBreadcrumbTitle());

        $_REQUEST['expandBottomContURL']= '/panel_html/payroll_agency_by_month_transactions/payroll/agencywide/monthly/transactions/yeartype/B/year/118/agency/86/month/3540';
        $this->assertEquals('Gross Pay by Month Transactions', CustomBreadcrumbs::getPayrollBreadcrumbTitle());

    }
}
