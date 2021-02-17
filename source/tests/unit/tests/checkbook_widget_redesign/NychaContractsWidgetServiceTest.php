<?php
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/nychaContracts/NychaContractsWidgetService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/nychaContracts/NychaContractsUrlService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/widget/WidgetDataService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/widget/WidgetService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/constants/Constants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/RequestUtil.php';

use PHPUnit\Framework\TestCase;

/**
 * Class NychaContractsWidgetServiceTest
 */
class NychaContractsWidgetServiceTest extends TestCase{
    /**
     * Tests initializeDataService() function
     */
    public function test_initializeDataService(){
        $result = NychaContractsWidgetService::initializeDataService();
        $this->assertInstanceOf(NychaContractsDataService::class, $result);
    }

    /**
     * Tests implementDerivedColumn() function
     */
    public function test_implementDerivedColumn(){
        $row['vendor_name'] = "<span title='NEW YORK POWER AUTHORITY'>NEW YORK POWER AUTHORITY</span>";
        $row['vendor_id'] = 2857;
        $row['purchase_order_count'] = 2;
        $row['total_amount'] = "$1.97B";
        $row['original_amount'] = "$1.98B";
        $row['spend_to_date'] = "$32.20M";
        $result = NychaContractsWidgetService::implementDerivedColumn("vendor_link", $row);
        $this->assertEquals("<a href='/nycha_contracts/year/121/agency/162/datasource/checkbook_nycha/vendor/2857'><span title='NEW YORK POWER AUTHORITY'>NEW YORK POWER AUTHORITY</span></a>", $result);
    }

    /**
     * Tests adjustParameters() function
     */
    public function test_adjustParameters(){
        $parameters['year'] = 121;
        $parameters['agency'] = 162;
        $result = NychaContractsWidgetService::adjustParameters($parameters);
        $this->assertEquals($parameters['agency'], $result['agency']);
    }

     /**
     * Tests getWidgetFooterUrl() function
     */
    public function test_getWidgetFooterUrl(){
        $result = NychaContractsWidgetService::getFooterUrl();
        $this->assertEquals("/panel_html/nycha_contracts_transactions_page/nycha_contracts/transactions", substr($result, 0, 74));
    }
}