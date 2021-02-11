<?php

include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/contracts/ContractsUrlService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/constants/Constants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/RequestUtil.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_infrastructure_layer/constants/ContractConstants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/common/MinorityTypeService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_infrastructure_layer/constants/CommonConstants.php';

use PHPUnit\Framework\TestCase;

/**
 * Class ContractsUrlServiceTest
 */
class ContractsUrlServiceTest extends TestCase
{
    /**
     * Tests contractIdUrl() function
     */
    public function test_contractIdUrl(){
        $result = ContractsUrlService::contractIdUrl("4224034", "CT1");
        $this->assertEquals("/panel_html/contract_transactions/agid/4224034", substr($result, 0, 46));
        $this->assertEquals("/doctype/CT1", substr($result, -12));
    }

    /**
     * Tests masterContractIdUrl() function
     */
    public function test_masterContractIdUrl(){
        $result = ContractsUrlService::masterContractIdUrl("5868332", "MMA1");
        $this->assertEquals("/panel_html/contract_transactions/contract_details/magid/5868332", substr($result, 0, 64));
        $this->assertEquals("/doctype/MMA1", substr($result, -13));
    }

    /**
     * Tests pendingMasterContractIdUrl() function
     */
    public function test_pendingMasterContractIdUrl(){
        $result = ContractsUrlService::pendingMasterContractIdUrl("5868332", "MA1", NULL, NULL, NULL, NULL);
        $this->assertEquals("/panel_html/contract_transactions/magid/5868332", substr($result, 0, 47));
        $this->assertEquals("/doctype/MA1", substr($result, -12));
        
        $result = ContractsUrlService::pendingMasterContractIdUrl(NULL, NULL, "RCT184620218200842", NULL, "0", NULL);
        $this->assertEquals("/minipanels/pending_contract_transactions/contract/RCT184620218200842", substr($result, 0, 69));
        $this->assertEquals("/version/0", substr($result, -10));
    }   

    /**
     * Tests pendingContractIdLink() function
     */
    public function test_pendingContractIdLink(){
        $result = ContractsUrlService::pendingContractIdLink("5868332", "CT1", NULL, NULL, NULL, NULL);
        $this->assertEquals("/panel_html/contract_transactions/agid/5868332/doctype/CT1", $result);
        
        $result = ContractsUrlService::pendingContractIdLink(NULL, NULL, NULL, "RCT184620218200842", "7", NULL);
        $this->assertEquals("/minipanels/pending_contract_transactions/contract/RCT184620218200842/version/7", $result);
    }

    /**
     * Tests spentToDateUrl() function
     */
    public function test_spentToDateUrl(){
        $result = ContractsUrlService::spentToDateUrl("awdmethod", NULL);
        $this->assertEquals("/contract/spending/transactionsawdmethod", substr($result, 0, 40));
    }

    /**
     * Tests masterAgreementSpentToDateUrl() function
     */
    public function test_masterAgreementSpentToDateUrl(){
        $result = ContractsUrlService::masterAgreementSpentToDateUrl("awdmethod", NULL);
        $this->assertEquals("/contract/spending/transactionsawdmethod", substr($result, 0, 40));
    }

    /**
     * Tests primeMinorityTypeUrl() function
     */
    public function test_primeMinorityTypeUrl(){
        $result = ContractsUrlService::primeMinorityTypeUrl(2);
        $this->assertEquals("dashboard/mp/mwbe/2", substr($result, -19));
    }

    /**
     * Tests minorityTypeUrl() function
     */
    public function test_minorityTypeUrl(){
        $result = ContractsUrlService::minorityTypeUrl(2,'mp');
        $this->assertEquals("dashboard/mp/mwbe/2", substr($result, -19));
    }

    /**
     * Tests getFooterUrl() function
     */
    public function test_getFooterUrl(){
        $parameter["doctype"] = "MA1,CTA1,CT1";
        $parameter["status"] = "A";
        $parameter["yeartype"] = "B";
        $parameter["year"] = "121";
        $parameter["dashboard"] = "mp";
        $parameter["mwbe"] = "2";
        $result = ContractsUrlService::getFooterUrl($parameter,454);
        $this->assertEquals("/panel_html/contract_details/contract/transactions/contcat/expense/contstatus/P/yeartype/C/year/119/doctype/MA1~CTA1~CT1/smnid/454", $result);
    }
    
    /**
     * Tests getAmtModificationUrlString() function
     */
    public function test_getAmtModificationUrlString(){
        $result = ContractsUrlService::getAmtModificationUrlString(false, NULL);
        $this->assertEquals("/modamt/0/pmodamt/0/smodamt/0", $result);
    }
}