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
        //unset($_REQUEST['expandBottomContURL']);
        $this->assertEquals('NYCHA RevenueTransactions', CustomBreadcrumbs::getNYCHARevenueBreadcrumbTitle());

        $_GET['q'] = 'nycha_revenue/datasource/checkbook_nycha/agency/162/year/121';
        $this->assertEquals(' Revenue Transactions', CustomBreadcrumbs::getNYCHARevenueBreadcrumbTitle());
    }
}
