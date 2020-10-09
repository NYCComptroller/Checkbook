<?php

include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/budget/NychaBudgetUtil.php';
include_once CUSTOM_MODULES_DIR . '/checkbook_project/customclasses/constants/Constants.php';
include_once CUSTOM_MODULES_DIR . '/widget_framework/widget/widget.module';

use PHPUnit\Framework\TestCase;
/**
 * Class NychaBudgetUtilTest
 */
class NychaBudgetUtilTest extends TestCase
{
    /**
     * Tests getTransactionsTitle() function
     */
    public function test_getTransactionsTitle()
    {
        $result = NychaBudgetUtil::getTransactionsTitle('/panel_html/nycha_budget_transactions/nycha_budget/transactions/year/121/datasource/checkbook_nycha/widget/wt_program');
        $this->assertEquals('Programs Expense Budget Transactions', $result);
    }

    /**
     * Tests getTransactionsSubTitle() function
     */
    public function test_getTransactionsSubTitle()
    {
        $result = NychaBudgetUtil::getTransactionsSubTitle('comm_prgm', '/panel_html/nycha_budget_transactions/nycha_budget/transactions/year/121/datasource/checkbook_nycha/widget/comm_prgm/budgettype/committed/program/29');
        $this->assertEquals("<b>Program:</b>Default", $result);
    }

    public function test_alterPercentDifferenceQuery(){
        $query = "SELECT COUNT(*) AS record_count
  FROM ( SELECT a.budget_fiscal_year_id, ma.modified_budget_amount AS current_amount, ma.modified_budget_amount_py_1 AS previous_amount, ma.modified_budget_amount_py_2 AS previous_1_amount, ma.modified_budget_amount_py_3 AS previous_2_amount, a.program_phase_code, b.program_phase_description AS program_phase_description, b.program_phase_id, CASE SUM(COALESCE(ma.modified_budget_amount_py_1,0)) WHEN 0 THEN 0 ELSE ((SUM(COALESCE(ma.modified_budget_amount,0)) - SUM(COALESCE(ma.modified_budget_amount_py_1,0)))/SUM(COALESCE(ma.modified_budget_amount_py_1,0)))*100 END AS percent_difference1, CASE SUM(COALESCE(ma.modified_budget_amount_py_2,0)) WHEN 0 THEN 0 ELSE ((SUM(COALESCE(ma.modified_budget_amount,0)) - SUM(COALESCE(ma.modified_budget_amount_py_2,0)))/SUM(COALESCE(ma.modified_budget_amount_py_2,0)))*100 END AS percent_difference2, CASE SUM(COALESCE(ma.modified_budget_amount_py_3,0)) WHEN 0 THEN 0 ELSE ((SUM(COALESCE(ma.modified_budget_amount,0)) - SUM(COALESCE(ma.modified_budget_amount_py_3,0)))/SUM(COALESCE(ma.modified_budget_amount_py_3,0)))*100 END AS percent_difference3 FROM aggregateon_budget_by_year a JOIN ( SELECT program_phase_id, SUM(COALESCE(a.modified_budget_amount,0)) AS modified_budget_amount, SUM(COALESCE(a.modified_budget_amount_py_1,0)) AS modified_budget_amount_py_1, SUM(COALESCE(a.modified_budget_amount_py_2,0)) AS modified_budget_amount_py_2, SUM(COALESCE(a.modified_budget_amount_py_3,0)) AS modified_budget_amount_py_3 FROM aggregateon_budget_by_year a WHERE (a.filter_type = 'H') GROUP BY program_phase_id ) ma ON ma.program_phase_id = a.program_phase_id JOIN ref_program_phase b ON a.program_phase_id = b.program_phase_id WHERE (a.filter_type = 'H' AND a.is_active = 1) GROUP BY a.budget_fiscal_year_id, a.program_phase_code, b.program_phase_description, b.program_phase_id, ma.modified_budget_amount, ma.modified_budget_amount_py_1, ma.modified_budget_amount_py_2, ma.modified_budget_amount_py_3 ) b
 WHERE b.budget_fiscal_year_id = 121";
        $result = NychaBudgetUtil::alterPercentDifferenceQuery();
        $expectedQuery = "SELECT COUNT(*) AS record_count
  FROM ( SELECT a.budget_fiscal_year_id, ma.modified_budget_amount AS current_amount, ma.modified_budget_amount_py_1 AS previous_amount, ma.modified_budget_amount_py_2 AS previous_1_amount, ma.modified_budget_amount_py_3 AS previous_2_amount, a.program_phase_code, b.program_phase_description AS program_phase_description, b.program_phase_id, CASE SUM(COALESCE(ma.modified_budget_amount_py_1,0)) WHEN 0 THEN 0 ELSE ((SUM(COALESCE(ma.modified_budget_amount,0)) - SUM(COALESCE(ma.modified_budget_amount_py_1,0)))/SUM(COALESCE(ma.modified_budget_amount_py_1,0)))*100 END AS percent_difference1, CASE SUM(COALESCE(ma.modified_budget_amount_py_2,0)) WHEN 0 THEN 0 ELSE ((SUM(COALESCE(ma.modified_budget_amount,0)) - SUM(COALESCE(ma.modified_budget_amount_py_2,0)))/SUM(COALESCE(ma.modified_budget_amount_py_2,0)))*100 END AS percent_difference2, CASE SUM(COALESCE(ma.modified_budget_amount_py_3,0)) WHEN 0 THEN 0 ELSE ((SUM(COALESCE(ma.modified_budget_amount,0)) - SUM(COALESCE(ma.modified_budget_amount_py_3,0)))/SUM(COALESCE(ma.modified_budget_amount_py_3,0)))*100 END AS percent_difference3 FROM aggregateon_budget_by_year a JOIN ( SELECT program_phase_id, SUM(COALESCE(a.modified_budget_amount,0)) AS modified_budget_amount, SUM(COALESCE(a.modified_budget_amount_py_1,0)) AS modified_budget_amount_py_1, SUM(COALESCE(a.modified_budget_amount_py_2,0)) AS modified_budget_amount_py_2, SUM(COALESCE(a.modified_budget_amount_py_3,0)) AS modified_budget_amount_py_3 FROM aggregateon_budget_by_year a WHERE (a.filter_type = 'H' AND a.budget_fiscal_year_id = 121) GROUP BY program_phase_id ) ma ON ma.program_phase_id = a.program_phase_id JOIN ref_program_phase b ON a.program_phase_id = b.program_phase_id WHERE (a.filter_type = 'H' AND a.is_active = 1 AND a.budget_fiscal_year_id = 121) GROUP BY a.budget_fiscal_year_id, a.program_phase_code, b.program_phase_description, b.program_phase_id, ma.modified_budget_amount, ma.modified_budget_amount_py_1, ma.modified_budget_amount_py_2, ma.modified_budget_amount_py_3 ) b";
        $this->assertEquals($expectedQuery, $result);
    }

}
