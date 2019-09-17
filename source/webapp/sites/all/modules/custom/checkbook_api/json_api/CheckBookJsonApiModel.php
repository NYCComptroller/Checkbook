<?php

namespace checkbook_json_api;

/**
 * Class CheckBookJsonApiModel
 * @package checkbook_json_api
 */
class CheckBookJsonApiModel
{
    /**
     * @param $year
     * @param $year_type
     * @return array
     * @throws \Exception
     */
    public function get_subcontracts_data($year, $year_type)//: array
    {
        $query = "SELECT
                  SUM(CASE WHEN aprv_sta=2 THEN 1 ELSE 0 END) AS acco_rejected,
                  SUM(CASE WHEN aprv_sta=3 THEN 1 ELSE 0 END) AS acco_reviewing,
                  SUM(CASE WHEN aprv_sta=4 THEN 1 ELSE 0 END) AS acco_approved,
                  SUM(CASE WHEN aprv_sta=5 THEN 1 ELSE 0 END) AS acco_cancelled,
                  SUM(CASE WHEN aprv_sta>1 AND aprv_sta<6 THEN 1 ELSE 0 END) AS acco_submitted
                FROM aggregateon_mwbe_contracts_cumulative_spending a
                  LEFT JOIN (SELECT contract_number, aprv_sta FROM subcontract_details WHERE latest_flag='Y') sd ON a.contract_number=sd.contract_number
                  LEFT JOIN ref_document_code c ON a.document_code_id=c.document_code_id
                
                WHERE (a.fiscal_year = '{$year}' AND a.type_of_year = '{$year_type}' AND a.status_flag = 'A' AND c.document_code IN ('CTA1','CT1','CT2') AND a.scntrc_status = 2)";
        $response = _checkbook_project_execute_sql($query);
        return $response;
    }

    /**
     * @param $year
     * @param $year_type
     * @return array
     * @throws \Exception
     */
    public function get_expense_contracts($year, $year_type)//: array
    {
        $query = "SELECT COUNT(contract_number) AS total FROM aggregateon_mwbe_contracts_cumulative_spending a
                  JOIN ref_document_code b ON a.document_code_id = b.document_code_id
                WHERE a.fiscal_year = {$year} AND a.type_of_year = '{$year_type}' AND a.status_flag = 'A' AND b.document_code IN ('MA1','CTA1','CT1')";

        $response = _checkbook_project_execute_sql($query);
        return $response;
    }

    /**
     * @param $year
     * @param $year_type
     * @return array
     * @throws \Exception
     */
    public function get_active_subcontracts($year, $year_type)//: array
    {
        $query = "SELECT SUM(total_contracts) as total from aggregateon_subven_total_contracts
                WHERE fiscal_year='{$year}' AND status_flag='A' AND type_of_year='{$year_type}'";
        $response = _checkbook_project_execute_sql($query);
        return $response;
    }

    /**
     * @param $year
     * @param $year_type
     * @return array
     * @throws \Exception
     */
    public function get_total_payroll($year, $year_type)//: array
    {
        $year_id = 100 + ($year - 2000) + 1;
        $query = "SELECT
                  SUM(gross_pay) AS total_gross_pay,
                  SUM(base_pay) AS total_base_pay,
                  SUM(overtime_pay) AS total_overtime_pay
                FROM aggregateon_payroll_employee_agency s0
                WHERE s0.type_of_year = '{$year_type}' AND s0.fiscal_year_id = '{$year_id}'";
        $response = _checkbook_project_execute_sql($query);
        return $response;
    }

    /**
     * @param $year
     * @param $year_type
     * @return array
     * @throws \Exception
     */
    public function get_total_spending($year, $year_type)//: array
    {
        $year_id = 100 + ($year - 2000) + 1;
        $query = "SELECT SUM(total_spending_amount) AS total
                  FROM aggregateon_mwbe_spending_coa_entities s0
                WHERE s0.type_of_year = '{$year_type}'
                      AND s0.minority_type_id IN (2, 3, 4, 5, 9)
                      AND s0.year_id = '{$year_id}'";
        $response = _checkbook_project_execute_sql($query);
        return $response;
    }

    /**
     * @param $year
     * @return array
     * @throws \Exception
     */
    public function get_total_budget($year)//: array
    {
        $query = "SELECT SUM(COALESCE(current_budget_amount,0)) AS total
                  FROM budget s0
                WHERE s0.budget_fiscal_year = '{$year}'";
        $response = _checkbook_project_execute_sql($query);
        return $response;
    }

    /**
     * @param $year
     * @return array
     * @throws \Exception
     */
    public function get_total_revenue($year)//: array
    {
        $query = "SELECT SUM(COALESCE(posting_amount,0)) AS total
                  FROM revenue_details s0
                WHERE s0.budget_fiscal_year = '{$year}'";
        $response = _checkbook_project_execute_sql($query);
        return $response;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function get_etl_status()//: array
    {
        if (!defined('CHECKBOOK_NO_DB_CACHE')) {
          define('CHECKBOOK_NO_DB_CACHE', true);
        }
        $query = "SELECT DISTINCT 
                  MAX(refresh_end_date :: TIMESTAMP) AS last_successful_run
                FROM etl.refresh_shards_status
                WHERE latest_flag = 1";
        $response = _checkbook_project_execute_sql($query, 'etl');
        return $response;
    }
}
