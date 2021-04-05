<?php

include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/RequestUtil.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/includes/checkbook_project.inc';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/constants/Constants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/util/CheckbookDateUtil.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_infrastructure_layer/constants/CommonConstants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_custom_breadcrumbs/customclasses/checkbook_custom_breadcrumbs.php';



use PHPUnit\Framework\TestCase;

/**
 * Class CustomBreadcrumbs
 */
class CustomBreadcrumbsTest extends TestCase
{

    function _getLastRequestParamValue($url)
    {
        return '5' . $url;
    }

    /**
     * Test GetBudgetBreadcrumbTitle function
     */
    public function testGetBudgetBreadcrumbTitle()
    {
        global $mock_current_path;

        function _get_budget_breadcrumb_title_drilldown($a = 3)
        {
            return '5' . $a;
        }

        function filter_xss($text)
        {
            return $text;
        }

        $mock_current_path = '/panel_html/budget_agency_perecent_difference_transactions/budget/agency_details/dtsmnid/560/yeartype/B/year/119';
        $_REQUEST['expandBottomContURL'] = 'budget/yeartype/B/year/119';

        $this->assertEquals('53 Expense Budget', CustomBreadcrumbs::getBudgetBreadcrumbTitle());

        $mock_current_path = 'panel_html/budget_agency_perecent_difference_transactions/budget/agency_details/dtsmnid/560/yeartype/B/year/119';
        unset($_REQUEST['expandBottomContURL']);

        $this->assertEquals('getInitNodeSummaryTitle :: 560', CustomBreadcrumbs::getBudgetBreadcrumbTitle());

        $mock_current_path = 'budget/transactions/year/119';
        unset($_REQUEST['expandBottomContURL']);
        $this->assertEquals('Expense Budget Transactions', CustomBreadcrumbs::getBudgetBreadcrumbTitle());
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
        $this->assertEquals('53 Revenue', CustomBreadcrumbs::getRevenueBreadcrumbTitle());

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
        function  _get_spending_breadcrumb_title_drilldown()
        {
            return '5' ;
        }

        $_GET['q']  = 'spending_landing/yeartype/B/year/122';
        $_REQUEST['expandBottomContURL'] = '/panel_html/spending_transactions/spending/transactions/yeartype/B/year/122/dtsmnid/22';
        $this->assertEquals('getInitNodeSummaryTitle :: 22', CustomBreadcrumbs::getSpendingBreadcrumbTitle());

        $_REQUEST['expandBottomContURL'] = '/panel_html/spending_transactions/spending/transactions/yeartype/B/year/122/expcategorycode/2110/smnid/22';
        $this->assertEquals('getInitNodeSummaryTitle :: 22', CustomBreadcrumbs::getSpendingBreadcrumbTitle());
        unset($_REQUEST['expandBottomContURL']);

        $_GET['q'] = 'spending/search/transactions/';
        $this->assertEquals('5  Total Spending', CustomBreadcrumbs::getSpendingBreadcrumbTitle());

        $_GET['q'] = 'spending/search/transactions/datasource/checkbook_oge/agency/9000';
        $this->assertEquals('5  Total Spending', CustomBreadcrumbs::getSpendingBreadcrumbTitle());

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
}
