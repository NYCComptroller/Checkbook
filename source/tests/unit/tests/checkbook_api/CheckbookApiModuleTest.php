<?php

include_once CUSTOM_MODULES_DIR . '/checkbook_api/checkbook_api.module';
include_once CUSTOM_MODULES_DIR . '/checkbook_api/includes/spending.inc';

/**
 * Class CheckbookApiModuleTest
 */
class CheckbookApiModuleTest extends \PHPUnit\Framework\TestCase
{


    /**
     *
     */
    public function test_checkbook_api_adjustSpendingSql()
    {
        $query = <<<SQLEND
    SELECT agency_name,
       prime_vendor_name,
       reporting_code,
       check_amount,
       reference_document_number,
       purpose,
       department_name,
       disbursement_number,
       expenditure_object_name,
       fiscal_year,
       industry_type_name,
       check_eft_issued_date,
       mwbe_category_ui,
       vendor_name,
       spending_category_name,
       sub_contract_id,
       vendor_type
  FROM all_disbursement_transactions
 WHERE fiscal_year = 2021
 ORDER BY check_amount DESC, disbursement_line_item_id DESC
 LIMIT 200
SQLEND;
        $adjustedQuery = checkbook_api_adjustSpendingSql($query);
        $this->assertEquals(<<<SQLEND
    SELECT agency_name,
       CASE WHEN is_prime_or_sub = 'S' THEN prime_vendor_name ELSE 'N/A' END AS prime_vendor_name,
       CASE WHEN is_prime_or_sub = 'P' THEN reporting_code ELSE 'N/A' END AS reporting_code,
       check_amount,
       reference_document_number,
       purpose,
       CASE WHEN is_prime_or_sub = 'P' THEN department_name ELSE 'N/A' END AS department_name,
       CASE WHEN is_prime_or_sub = 'P' THEN disbursement_number ELSE 'N/A' END AS disbursement_number,
       CASE WHEN is_prime_or_sub = 'P' THEN expenditure_object_name ELSE 'N/A' END AS expenditure_object_name,
       fiscal_year,
       industry_type_name,
       check_eft_issued_date,
       mwbe_category_ui,
       vendor_name,
       spending_category_name,
       sub_contract_id,
       CASE WHEN is_prime_or_sub = 'P' THEN 'No' ELSE 'Yes' END AS vendor_type
  FROM all_disbursement_transactions
  WHERE  fiscal_year = 2021
 ORDER BY check_amount DESC, disbursement_line_item_id DESC
 LIMIT 200
SQLEND
, $adjustedQuery);

    }
}
