<?php
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/nychaSpending/NychaSpendingUrlService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/spending/NychaSpendingUtil.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/constants/Constants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/RequestUtil.php';

use PHPUnit\Framework\TestCase;

/**
 * Class NychaContractsUrlServiceTest
 */
class NychaSpendingUrlServiceTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Tests getFooterUrl() function
     */
    public function test_getFooterUrl()
    {
        $result = NychaSpendingUrlService::getFooterUrl();
        $this->assertEquals("/panel_html/nycha_spending_transactions/nycha_spending/transactions", substr($result, 0, 67));
    }

    /**
     * Tests generateLandingPageUrl() function
     */
    public function test_generateLandingPageUrl()
    {
        $result = NychaSpendingUrlService::generateLandingPageUrl('vendor', '138576');
        $this->assertEquals("/nycha_spending/year/119/vendor/138576", $result);
    }

    /**
     * Tests generateLandingPageUrl() function
     */
    public function test_ytdSpendingUrl()
    {
        $result = NychaSpendingUrlService::ytdSpendingUrl('/dept_code/1', 'ytd_department');
        $this->assertEquals("/panel_html/nycha_spending_transactions/nycha_spending/transactions/year/119/widget/ytd_department/dept_code/1", $result);
    }
    /**
     * Tests generateContractIdLink() function
     */
    public function test_generateContractIdLink()
    {
        $result = NychaSpendingUrlService::generateContractIdLink('BA1000126', '122');
        $this->assertEquals('<a class=\'new_window\' href=\'/nycha_contract_details/year/122/contract/BA1000126/newwindow\'>BA1000126</a>', $result);

    }
    /**
     * Tests invIDContractSpendingUrl() function
     */
    public function test_invIDContractSpendingUrl()
    {
        $result = NychaSpendingUrlService::invIDContractSpendingUrl('/po_num_inv/BA1000126', 'inv_contract_id','/agg_type/BA','/tcode/BA');
        $this->assertEquals('/nycha_spending/transactions/widget/inv_contract_id/po_num_inv/BA1000126/agg_type/BA/tcode/BA/newwindow', $result);
    }

    /**
     * Tests invContractSpendingUrl() function
     */
    public function test_invContractSpendingUrl()
    {
        $result = NychaSpendingUrlService::invIDContractSpendingUrl('/industry_inv/4', 'inv_contract',null,'/tcode/IND');
        $this->assertEquals('/nycha_spending/transactions/widget/inv_contract/industry_inv/4/tcode/IND/newwindow', $result);
    }


}

/**
 * Class NychaSpendingUtilTest
 */
class NychaSpendingUtilTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests getTransactionsTitle function
     */
    public function test_getTransactionsTitle()
    {
        $result = NychaSpendingUtil::getTransactionsTitle('/panel_html/nycha_spending_transactions/nycha_spending/transactions/year/122/agency/162/datasource/checkbook_nycha/widget/ytd_resp_center/resp_center/350');
        $this->assertEquals('Responsibility Center Total Spending Transactions', $result);
    }
    /**
     * Tests getCategoryName function
     */
    public function test_getCategoryName()
    {
        $result = NychaSpendingUtil::getCategoryName();
        $this->assertEquals('Total', $result);
    }
    /**
     * Tests getTransactionsSubTitle function
     */
    public function test_getTransactionsSubTitle()
    {
        $result = NychaSpendingUtil::getTransactionsSubTitle('ytd_expense_category', '/panel_html/nycha_spending_transactions/nycha_spending/transactions/year/122/agency/162/datasource/checkbook_nycha/widget/ytd_expense_category/expcategorycode/85511');
        $this->assertEquals("<b>Expense Category: </b>", $result);
    }
}
