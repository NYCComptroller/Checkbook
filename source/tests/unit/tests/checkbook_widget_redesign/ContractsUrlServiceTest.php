<?php

include_once CUSTOM_MODULES_DIR . '/checkbook_widget_redesign/checkbook_business_layer/checkbook_services/contracts/ContractsUrlService.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/constants/Constants.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/RequestUtil.php';

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
        $this->assertEquals("/panel_html/contract_transactions/magid/5868332", substr($result, 0, 64));
        $this->assertEquals("/doctype/MA1", substr($result, -12));
        
        $result = ContractsUrlService::pendingMasterContractIdUrl(NULL, NULL, "RCT184620218200842", NULL, "0", NULL);
        $this->assertEquals("/minipanels/pending_contract_transactions/contract/RCT184620218200842");
        $this->assertEquals("/version/0", substr($result, 0, 10));
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

    

}