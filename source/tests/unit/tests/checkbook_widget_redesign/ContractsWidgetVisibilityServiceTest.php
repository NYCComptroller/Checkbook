<?php


include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/contracts/ContractsParameters.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/contracts/ContractsWidgetVisibilityService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/constants/Constants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/RequestUtil.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_infrastructure_layer/constants/ContractConstants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/common/MinorityTypeService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_infrastructure_layer/constants/CommonConstants.php';

use PHPUnit\Framework\TestCase;

/**
* Class ContractsUrlServiceTest
*/
class ContractsWidgetVisibilityServiceTest extends TestCase
{
    /**
    * Tests getWidgetVisibility() function
    */
    public function test_getWidgetVisibility()
    {
        // contracts_modifications
        $_GET['q'] = '/contracts_revenue_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('contracts_modifications');
        $this->assertEquals("revenue_contracts_modifications_view", $result);

        $_GET['q'] = '/contracts_pending_exp_landing/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('contracts_modifications');
        $this->assertEquals("expense_pending_contracts_modifications_view", $result);

        $_GET['q'] = '/contracts_pending_rev_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('contracts_modifications');
        $this->assertEquals("revenue_pending_contracts_modifications_view", $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('contracts_modifications');
        $this->assertEquals("contracts_modifications_view", $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123/dashboard/ms/mwbe/2~3~4~5~6~9~99';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('contracts_modifications');
        $this->assertEquals("mwbe_sub_contracts_modifications_view", $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123/dashboard/sp/mwbe/2~3~4~5~6~9~99';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('contracts_modifications');
        $this->assertEquals("subcontracts_modifications_view", $result);

        // contracts
        $_GET['q'] = '/contracts_revenue_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('contracts');
        $this->assertEquals("revenue_contracts_view", $result);

        $_GET['q'] = '/contracts_pending_exp_landing/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('contracts');
        $this->assertEquals("expense_pending_contracts_view", $result);

        $_GET['q'] = '/contracts_pending_rev_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('contracts');
        $this->assertEquals("revenue_pending_contracts_view", $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('contracts');
        $this->assertEquals("contracts_view", $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123/dashboard/ms/mwbe/2~3~4~5~6~9~99';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('contracts');
        $this->assertEquals("mwbe_sub_contracts_view", $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123/dashboard/sp/mwbe/2~3~4~5~6~9~99';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('contracts');
        $this->assertEquals("sub_contracts_view", $result);

        // industries
        $_GET['q'] = '/contracts_revenue_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('industries');
        $this->assertEquals("revenue_contracts_by_industries_view", $result);

        $_GET['q'] = '/contracts_pending_exp_landing/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('industries');
        $this->assertEquals("pending_contracts_by_industries_view", $result);

        $_GET['q'] = '/contracts_pending_rev_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('industries');
        $this->assertEquals("pending_contracts_by_industries_view", $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('industries');
        $this->assertEquals("contracts_by_industries_view", $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123/dashboard/ms/mwbe/2~3~4~5~6~9~99';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('industries');
        $this->assertEquals("mwbe_sub_contracts_by_industries_view", $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123/dashboard/sp/mwbe/2~3~4~5~6~9~99';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('industries');
        $this->assertEquals("sub_contracts_by_industries_view", $result);

        // size
        $_GET['q'] = '/contracts_revenue_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('size');
        $this->assertEquals("revenue_contracts_by_size_view", $result);

        $_GET['q'] = '/contracts_pending_exp_landing/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('size');
        $this->assertEquals("pending_contracts_by_size_view", $result);

        $_GET['q'] = '/contracts_pending_rev_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('size');
        $this->assertEquals("pending_contracts_by_size_view", $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('size');
        $this->assertEquals("contracts_by_size_view", $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123/dashboard/ms/mwbe/2~3~4~5~6~9~99';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('size');
        $this->assertEquals("mwbe_sub_contracts_by_size_view", $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123/dashboard/sp/mwbe/2~3~4~5~6~9~99';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('size');
        $this->assertEquals("sub_contracts_by_size_view", $result);

        // award_methods
        $_GET['q'] = '/contracts_revenue_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('award_methods');
        $this->assertEquals("revenue_award_methods_view", $result);

        $_GET['q'] = '/contracts_pending_exp_landing/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('award_methods');
        $this->assertEquals("pending_award_methods_view", $result);

        $_GET['q'] = '/contracts_pending_rev_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('award_methods');
        $this->assertEquals("pending_award_methods_view", $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('award_methods');
        $this->assertEquals("expense_award_methods_view", $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123/dashboard/ms/mwbe/2~3~4~5~6~9~99';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('award_methods');
        $this->assertEquals("subvendor_award_methods_view", $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123/dashboard/sp/mwbe/2~3~4~5~6~9~99';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('award_methods');
        $this->assertEquals("subvendor_award_methods_view", $result);

        // master_agreements
        $_GET['q'] = '/contracts_revenue_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('master_agreements');
        $this->assertEquals(null, $result);

        $_GET['q'] = '/contracts_pending_exp_landing/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('master_agreements');
        $this->assertEquals("pending_master_agreements_view", $result);

        $_GET['q'] = '/contracts_pending_rev_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('master_agreements');
        $this->assertEquals(null, $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('master_agreements');
        $this->assertEquals('master_agreements_view', $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123/dashboard/ms/mwbe/2~3~4~5~6~9~99';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('master_agreements');
        $this->assertEquals(null, $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123/dashboard/sp/mwbe/2~3~4~5~6~9~99';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('master_agreements');
        $this->assertEquals(null, $result);

        // master_agreement_modifications
        $_GET['q'] = '/contracts_revenue_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('master_agreement_modifications');
        $this->assertEquals(null, $result);

        $_GET['q'] = '/contracts_pending_exp_landing/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('master_agreement_modifications');
        $this->assertEquals("pending_master_agreement_modifications_view", $result);

        $_GET['q'] = '/contracts_pending_rev_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('master_agreement_modifications');
        $this->assertEquals(null, $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('master_agreement_modifications');
        $this->assertEquals('master_agreement_modifications_view', $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123/dashboard/ms/mwbe/2~3~4~5~6~9~99';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('master_agreement_modifications');
        $this->assertEquals(null, $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123/dashboard/sp/mwbe/2~3~4~5~6~9~99';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('master_agreement_modifications');
        $this->assertEquals(null, $result);

        // vendors
        $_GET['q'] = '/contracts_revenue_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('vendors');
        $this->assertEquals('revenue_contracts_by_prime_vendors_view', $result);

        $_GET['q'] = '/contracts_pending_exp_landing/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('vendors');
        //$this->assertEquals(null, $result);

        $_GET['q'] = '/contracts_pending_rev_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('vendors');
        //$this->assertEquals('pending_contracts_by_prime_vendors_view', $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('vendors');
        $this->assertEquals('expense_contracts_by_prime_vendors_view', $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123/dashboard/ms/mwbe/2~3~4~5~6~9~99';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('vendors');
        $this->assertEquals('mwbe_subcontracts_by_prime_vendors_view', $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123/dashboard/sp/mwbe/2~3~4~5~6~9~99';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('vendors');
        $this->assertEquals('subcontracts_by_prime_vendors_view', $result);

        // sub_vendors
        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123/dashboard/ms/mwbe/2~3~4~5~6~9~99';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('sub_vendors');
        $this->assertEquals('contracts_subvendor_view', $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123/dashboard/sp/mwbe/2~3~4~5~6~9~99';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('sub_vendors');
        $this->assertEquals('contracts_subvendor_view', $result);

        // agencies
        $_GET['q'] = '/contracts_revenue_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('agencies');
        $this->assertEquals('revenue_contracts_by_agencies_view', $result);

        $_GET['q'] = '/contracts_pending_exp_landing/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('agencies');
        $this->assertEquals('pending_contracts_by_agencies_view', $result);

        $_GET['q'] = '/contracts_pending_rev_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('agencies');
        $this->assertEquals('pending_contracts_by_agencies_view', $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('agencies');
        $this->assertEquals('expense_contracts_by_agencies_view', $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123/dashboard/ms/mwbe/2~3~4~5~6~9~99';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('agencies');
        $this->assertEquals('mwbe_subcontracts_by_agencies_view', $result);

        $_GET['q'] = '/contracts_landing/status/A/yeartype/B/year/123/dashboard/sp/mwbe/2~3~4~5~6~9~99';
        $result = ContractsWidgetVisibilityService::getWidgetVisibility('agencies');
        $this->assertEquals('subcontracts_by_agencies_view', $result);




    }
}
