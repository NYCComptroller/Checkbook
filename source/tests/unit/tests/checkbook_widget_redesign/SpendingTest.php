<?php

include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/spending/SpendingUrlService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/spending/SpendingUtil.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/common/VendorService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/util/CustomURLHelper.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_infrastructure_layer/constants/CommonConstants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/common/MinorityTypeService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/common/SubVendorService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_infrastructure_layer/constants/SpendingConstants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/constants/Constants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/RequestUtil.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/util/CheckbookDateUtil.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/includes/checkbook_database.inc';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/includes/checkbook_datafeeds.inc';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/includes/checkbook_date.inc';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/includes/checkbook_project.inc';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/includes/checkbook_widget_process.inc';
include_once CUSTOM_MODULES_DIR . '/custom_number_formatter/custom_number_formatter.module';

use PHPUnit\Framework\TestCase;

/**
 * Class SpendingUrlServiceTest
 */
class SpendingUrlServiceTest extends TestCase
{


    /**
     * Tests contractIdUrl() function
     */
    public function test_contractIdUrl(){
        $result = SpendingUrlService::contractIdUrl("5868332", "MMA1");
        $this->assertEquals("/contract_details/magid/5868332/doctype/MMA1/newwindow", $result);
    }

    /**
     * Tests agencyUrl() function
     */
    public function test_agencyUrl()
    {
        $result = SpendingUrlService::agencyUrl("138");
       //$this->assertEquals("/spending_landing/yeartype/B/year/122/agency/138", $result);
        $this->assertEquals("/agency/138", substr($result, -11));
    }

    /**
     * Tests payrollAgencyUrl() function
     */
    public function test_payrollAgencyUrl()
    {
        $result = SpendingUrlService::payrollAgencyUrl("138");
        $this->assertEquals("/category/2/agency/138", substr($result, -22));
    }

    /**
     * Tests payrollAgencyUrl() function
     */
    public function test_industryUrl()
    {
        $result = SpendingUrlService::industryUrl("2");
        $this->assertEquals("/industry/2", substr($result, -11));
    }

    /**
     * Tests getFooter() function
     */
    public function test_getFooterUrl()
    {
        $_GET['q'] ='spending_landing/yeartype/B/year/119/dashboard/mp/mwbe/2~3~4~5~6~9~99/vendor/36036';
        $result = SpendingUrlService::getFooterUrl(null,'763');
        $expected = '/panel_html/spending_transactions/spending/transactions/vendor/36036/mwbe/2~3~4~5~6~9~99/dashboard/mp/yeartype/B/year/119/dtsmnid/763';
        $this->assertEquals($expected,$result);
    }

    /**
     * Tests ytdSpendingUrl() function
     */
    public function test_ytdSpendingUrl()
    {
        $_GET['q'] ='spending_landing/yeartype/B/year/122';
        $result = SpendingUrlService::ytdSpendingUrl(null,'763');
        $this->assertEquals("/panel_html/spending_transactions/spending/transactions/yeartype/B/year/122/smnid/763", $result);
    }

    /**
     * Tests mwbeUrl() function
     */
    public function test_mwbeUrl()
    {
        $result = SpendingUrlService::mwbeUrl('2~3~4~5~9','mp');
        $this->assertEquals("/spending_landing/yeartype/B/year/122/dashboard/mp/mwbe/2~3~4~5~9?expandBottomCont=true", $result);
    }

    /**
     * Tests PrimeMwbeCategoryUrl function
     */
    public function test_PrimeMwbeCategoryUrl()
    {
        $_GET['q'] = 'spending_landing/yeartype/B/year/122/dashboard/mp/mwbe/2~3~4~5~9/vendor/1696';
        $result = SpendingUrlService::PrimeMwbeCategoryUrl(2);
        $this->assertEquals('/spending_landing/yeartype/B/year/122/vendor/1696/dashboard/mp/mwbe/2?expandBottomCont=true',$result);
    }

    /**
     * Tests SubMwbeCategoryUrl function
     */
    public function test_SubMwbeCategoryUrl()
    {
        $_GET['q'] = '/spending_landing/yeartype/B/year/122/dashboard/ms/mwbe/2~3~4~5~9/subvendor/207992';
        $result = SpendingUrlService::SubMwbeCategoryUrl(2);
        $this->assertEquals('/spending_landing/yeartype/B/year/122/dashboard/sp/mwbe/2?expandBottomCont=true',$result);
    }

}

class SpendingUtilTest extends TestCase
{
    /**
     * Tests getSpendingCategoryName function
     */
    public function test_getSpendingCategoryName()
    {
        $result = SpendingUtil::getSpendingCategoryName();
        $this->assertEquals('Total Spending', $result);
    }

    /**
     * Tests getSpendingTransactionsTitle function
     */
    public function test_getSpendingTransactionsTitle()
    {
        $result = SpendingUtil::getSpendingTransactionsTitle();
        $this->assertEquals('Total Spending Transactions', $result);
    }

    /**
     * Tests getSpendingUrl function
     */
    public function test_getSpendingUrl()
    {
        $_GET['q'] = 'spending_landing/yeartype/B/year/122';
        $override_params = array(
            "smnid"=>23,
            "agency"=>98
        );

        $result = SpendingUtil::getSpendingUrl(null,$override_params);
        $this->assertEquals('/yeartype/B/year/122/smnid/23/agency/98', $result);
    }

    /**
     * Tests getSpendingTransactionPageUrl function
     */
    public function test_getSpendingTransactionPageUrl()
    {
        $_GET['q'] = 'spending_landing/yeartype/B/year/122';
        $override_params = array(
            "smnid"=>23,
            "agency"=>98
        );

        $result = SpendingUtil::getSpendingTransactionPageUrl($override_params);
        $this->assertEquals('panel_html/spending_transactions/spending/transactions/yeartype/B/year/122/smnid/23/agency/98', $result);
    }

    /**
     * Tests calculatePercent function
     */
    public function test_calculatePercent()
    {
        $result = SpendingUtil::calculatePercent(6,12);
        $this->assertEquals('50.00%',$result);
    }

    /**
     * Tests getContractTitle function
     */
    public function test_getContractTitle()
    {
        $_GET['q'] ='/contracts_landing/status/R/yeartype/B/year/122';
        $this->assertEquals('by Registered',SpendingUtil::getContractTitle());

        $_GET['q'] ='/contracts_landing/status/A/yeartype/B/year/122';
        $this->assertEquals('by Active',SpendingUtil::getContractTitle());

        $_GET['q'] ='/contracts_landing/status/P/yeartype/B/year/122';
        $this->assertEquals('by Pending',SpendingUtil::getContractTitle());

        $_GET['q'] = '/contract/spending/transactions/magid/17752/contstatus/A/yeartype/B/year/122/syear/122/contcat/expense/smnid/371/newwindow';
        $this->assertEquals('by Active Expense',SpendingUtil::getContractTitle());

        $_GET['q'] = '/contract/spending/transactions/magid/17752/contstatus/A/yeartype/B/year/122/syear/122/contcat/revenue/smnid/371/newwindow';
        $this->assertEquals('by Active Revenue',SpendingUtil::getContractTitle());
    }

    /**
     * Tests prepareSpendingBottomNavFilter function
     */
    public function test_prepareSpendingBottomNavFilter()
    {
        $_GET['q'] = 'spending_landing/yeartype/B/year/122';
        $result = SpendingUtil::prepareSpendingBottomNavFilter('spending_landing',2);
        $this->assertEquals('spending_landing/category/2/yeartype/B/year/122',$result);
    }

    /**
     * Tests getDataSourceParams function
     */
    public function test_getDataSourceParams()
    {
        $_GET['q'] = 'spending_landing/yeartype/B/year/122/datasource/checkbook_oge/agency/9000';
        $result = SpendingUtil::getDataSourceParams();
        $this->assertEquals('/datasource/checkbook_oge', $result);
    }
}
