<?php

include_once CUSTOM_MODULES_DIR . '/checkbook_api/checkbook_api.module';

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
        $query = 'l1.prime_vendor_name';
        $this->assertEquals("CASE WHEN l1.is_prime_or_sub = 'S' THEN l1.prime_vendor_name ELSE 'N/A' END AS prime_vendor_name",
            checkbook_api_adjustSpendingSql($query));

        $query = 'l1.vendor_type';
        $this->assertEquals("CASE WHEN l1.is_prime_or_sub = 'P' THEN 'No' ELSE 'Yes' END AS vendor_type",
            checkbook_api_adjustSpendingSql($query));

        $query = 'l1.reporting_code';
        $this->assertEquals("CASE WHEN l1.is_prime_or_sub = 'P' THEN l1.reporting_code ELSE 'N/A' END AS reporting_code",
            checkbook_api_adjustSpendingSql($query));

        $query = 'l1.department_name';
        $this->assertEquals("CASE WHEN l1.is_prime_or_sub = 'P' THEN l1.department_name ELSE 'N/A' END AS department_name",
            checkbook_api_adjustSpendingSql($query));

        $query = 'l1.disbursement_number';
        $this->assertEquals("CASE WHEN l1.is_prime_or_sub = 'P' THEN l1.disbursement_number ELSE 'N/A' END AS disbursement_number",
            checkbook_api_adjustSpendingSql($query));

        $query = 'l1.expenditure_object_name';
        $this->assertEquals("CASE WHEN l1.is_prime_or_sub = 'P' THEN l1.expenditure_object_name ELSE 'N/A' END AS expenditure_object_name",
            checkbook_api_adjustSpendingSql($query));

        $query = 'prime_vendor_name';
        $this->assertEquals("CASE WHEN is_prime_or_sub = 'S' THEN prime_vendor_name ELSE 'N/A' END AS prime_vendor_name",
            checkbook_api_adjustSpendingSql($query));

        $query = 'vendor_type';
        $this->assertEquals("CASE WHEN is_prime_or_sub = 'P' THEN 'No' ELSE 'Yes' END AS vendor_type",
            checkbook_api_adjustSpendingSql($query));

        $query = 'reporting_code';
        $this->assertEquals("CASE WHEN is_prime_or_sub = 'P' THEN reporting_code ELSE 'N/A' END AS reporting_code",
            checkbook_api_adjustSpendingSql($query));

        $query = 'department_name';
        $this->assertEquals("CASE WHEN is_prime_or_sub = 'P' THEN department_name ELSE 'N/A' END AS department_name",
            checkbook_api_adjustSpendingSql($query));

        $query = 'disbursement_number';
        $this->assertEquals("CASE WHEN is_prime_or_sub = 'P' THEN disbursement_number ELSE 'N/A' END AS disbursement_number",
            checkbook_api_adjustSpendingSql($query));

        $query = 'expenditure_object_name';
        $this->assertEquals("CASE WHEN is_prime_or_sub = 'P' THEN expenditure_object_name ELSE 'N/A' END AS expenditure_object_name",
            checkbook_api_adjustSpendingSql($query));

        $query = 'minority_type_name';
        $this->assertEquals(<<<SQLEND
    CASE
        WHEN minority_type_name= 2 THEN 'Black American'
        WHEN minority_type_name= 3 THEN 'Hispanic American'
        WHEN minority_type_name= 4 THEN 'Asian American'
        WHEN minority_type_name= 5 THEN 'Asian American'
        WHEN minority_type_name= 7 THEN 'Non-M/WBE'
        WHEN minority_type_name= 9 THEN 'Women'
        WHEN minority_type_name= 11 THEN 'Individuals and Others'
        WHEN minority_type_name= 'African American' THEN 'Black American'
        WHEN minority_type_name= 'Hispanic American' THEN 'Hispanic American'
        WHEN minority_type_name= 'Asian-Pacific' THEN 'Asian American'
        WHEN minority_type_name= 'Asian-Indian' THEN 'Asian American'
        WHEN minority_type_name= 'Non-Minority' THEN 'Non-M/WBE'
        WHEN minority_type_name= 'Caucasian Woman' THEN 'Women'
        WHEN minority_type_name= 'Individuals & Others' THEN 'Individuals and Others'
    END AS minority_type_name
SQLEND
            , checkbook_api_adjustSpendingSql($query));

    }
}
