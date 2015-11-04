<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 11/3/15
 * Time: 3:57 PM
 */

class payrollDetails {

    public function getData(&$node){

        $year = _getRequestParamValue("year");
        $yeartype = _getRequestParamValue("yeartype");
        $title = _getRequestParamValue("title");
        $agency = _getRequestParamValue("agency");

        $where = "";
        if(isset($year)) {
            $where .= $where == "" ? "WHERE fiscal_year_id = $year" : " AND fiscal_year_id = $year";
        }
        if(isset($yeartype)) {
            $where .= $where == "" ? "WHERE type_of_year = '$yeartype'" : " AND type_of_year = '$yeartype'";
        }
        if(isset($title)) {
            $where .= $where == "" ? "WHERE civil_service_title = '$title'" : " AND civil_service_title = '$title'";
        }
        if(isset($agency)) {
            $where .= $where == "" ? "WHERE agency_id = '$agency'" : " AND agency_id = '$agency'";
        }

        $query1 = "
            SELECT
               SUM(total_annual_salary) as total_annual_salary,
               SUM(total_gross_pay) as total_gross_pay,
               SUM(total_base_pay) as total_base_pay,
               SUM(total_other_payments) as total_other_payments,
               SUM(total_overtime_pay) as total_overtime_pay,
               SUM(total_employees) as total_employees,
               SUM(total_salaried_employees) as total_salaried_employees,
               SUM(total_non_salaried_employees) as total_non_salaried_employees
            FROM (
                SELECT type_of_year,
                       fiscal_year_id,
                       SUM(gross_pay) AS total_gross_pay,
                       SUM(base_pay) AS total_base_pay,
                       SUM(other_payments) AS total_other_payments,
                       SUM(overtime_pay) AS total_overtime_pay,
                       MAX(annual_salary) AS total_annual_salary,
                       COUNT(DISTINCT employee_number) AS total_employees,
                       COUNT(DISTINCT (CASE WHEN type_of_employment = 'Salaried' THEN employee_number END)) AS total_salaried_employees,
                       COUNT(DISTINCT (CASE WHEN type_of_employment = 'Non-Salaried' THEN employee_number END)) AS total_non_salaried_employees,
                      employee_number
                FROM aggregateon_payroll_employee_agency s0
                $where
                GROUP BY fiscal_year_id, type_of_year, employee_number
            ) x
    ";


        $results1 = _checkbook_project_execute_sql_by_data_source($query1,"checkbook");
        $node->data = $results1;

    }

}