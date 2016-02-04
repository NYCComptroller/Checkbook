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
        $month = _getRequestParamValue("month");
        $smnid = _getRequestParamValue("smnid");

        $dataset = 'aggregateon_payroll_employee_agency';
        $where = $sub_query_where = "";
        if(isset($year)) {
            $where .= $where == "" ? "WHERE emp.fiscal_year_id = $year" : " AND emp.fiscal_year_id = $year";
            $sub_query_where .= $sub_query_where == "" ? "WHERE fiscal_year_id = $year" : " AND fiscal_year_id = $year";
        }
        if(isset($yeartype)) {
            $where .= $where == "" ? "WHERE emp.type_of_year = '$yeartype'" : " AND emp.type_of_year = '$yeartype'";
            $sub_query_where .= $sub_query_where == "" ? "WHERE type_of_year = '$yeartype'" : " AND type_of_year = '$yeartype'";
        }
        if(isset($agency)) {
            $where .= $where == "" ? "WHERE emp.agency_id = '$agency'" : " AND emp.agency_id = '$agency'";
        }
        if(isset($month)) {
            $dataset = 'aggregateon_payroll_employee_agency_month';
            $where .= $where == "" ? "WHERE emp.month_id = '$month'" : " AND emp.month_id = '$month'";
            $ref_month_join = "JOIN ref_month month ON month.month_id = emp.month_id";
            $month_select = ",month_id, month_name";
            $month_id_select = ",month_id";
            $month_sub_select = ",month.month_id, month.month_name";
            $month_id_sub_select = ",emp.month_id AS month_id_1";
            $month_join = ' AND latest_emp.month_id = emp.month_id';
            $month_sub_query_group_by = ',month_id';
            $month_sub_group_by = ',month.month_id,month.month_name';
            $month_group_by = ',month_id,month_name';
            $sub_query_where .= $sub_query_where == "" ? "WHERE month_id = '$month'" : " AND month_id = '$month'";
        }
        if(isset($title)) {
            $where .= $where == "" ? "WHERE emp.civil_service_title_code = '$title'" : " AND emp.civil_service_title_code = '$title'";
            $title_select = ",civil_service_title_code";
            $title_sub_select = ",emp.civil_service_title_code";
            $title_sub_group_by = ',emp.civil_service_title_code';
            $title_group_by = ',civil_service_title_code';
        }
        $show_salaried = $smnid == 881 || $smnid == 882;

        if(isset($agency)) {
            $agency_select = ",agency_id,agency_name";
            $agency_sub_select = ",agency.agency_id,agency.agency_name";
            $agency_join = "JOIN ref_agency agency ON agency.agency_id = emp.agency_id";
            $agency_join_on = "AND emp_type.agency_id_1 = emp.agency_id";
            $agency_id_sub_select = ",agency_id AS agency_id_1";
            $agency_group_by = ", agency_id, agency_name";
            $agency_sub_group_by = ", agency.agency_id, agency.agency_name";
        }

        $query = "
            SELECT
                type_of_employment,
                type_of_year,
                fiscal_year_id,
                SUM(total_annual_salary) as total_annual_salary,
                SUM(total_gross_pay) as total_gross_pay,
                SUM(total_base_pay) as total_base_pay,
                SUM(total_other_payments) as total_other_payments,
                SUM(total_overtime_pay) as total_overtime_pay,
                COUNT(DISTINCT (CASE WHEN total_overtime_employees <> 0 THEN employee_number END)) AS total_overtime_employees,
                CASE WHEN type_of_employment = 'Salaried' THEN SUM(total_salaried_employees) ELSE SUM(total_non_salaried_employees) END AS number_employees,
                COUNT(DISTINCT employee_number) as total_number_employees
                {$agency_select}
                {$title_select}
                {$month_select}
            FROM (
                    SELECT
                    emp.type_of_year,
                    emp.fiscal_year_id,
                    SUM(emp.gross_pay) AS total_gross_pay,
                    SUM(emp.base_pay) AS total_base_pay,
                    SUM(emp.other_payments) AS total_other_payments,
                    SUM(emp.overtime_pay) AS total_overtime_pay,
                    SUM(emp.annual_salary) AS total_annual_salary,
                    SUM(emp.positive_overtime_pay) AS total_overtime_employees,
                    COUNT(DISTINCT (CASE WHEN COALESCE(emp_type.type_of_employment,'Non-Salaried') = 'Salaried' THEN emp_type.employee_number_1 END)) AS total_salaried_employees,
                    COUNT(DISTINCT (CASE WHEN COALESCE(emp_type.type_of_employment,'Non-Salaried') = 'Non-Salaried' AND emp.pay_date = latest_emp.pay_date THEN emp.employee_number END)) AS total_non_salaried_employees,
                    emp.employee_number,
                    emp.type_of_employment
                    {$agency_sub_select}
                    {$title_sub_select}
                    {$month_sub_select}
                    FROM {$dataset} emp
                    {$agency_join}
                    {$ref_month_join}
                    LEFT JOIN
                    (
                        SELECT DISTINCT
                        emp.type_of_employment AS type_of_employment,
                        emp.employee_number AS employee_number_1,
                        emp.fiscal_year_id AS fiscal_year_id_1,
                        emp.type_of_year AS type_of_year_1
                        {$agency_id_sub_select}
                        {$month_id_sub_select}
                        FROM {$dataset} emp
                        JOIN
                        (
                            SELECT max(pay_date) as pay_date,
                            employee_number,fiscal_year_id,type_of_year
                            {$month_id_select}
                            FROM {$dataset}
                            {$sub_query_where}
                            GROUP BY employee_number,fiscal_year_id,type_of_year
                            {$month_sub_query_group_by}
                        ) latest_emp ON latest_emp.pay_date = emp.pay_date
                        AND latest_emp.employee_number = emp.employee_number
                        AND latest_emp.fiscal_year_id = emp.fiscal_year_id
                        AND latest_emp.type_of_year = emp.type_of_year
                        AND type_of_employment = 'Salaried'
                        {$month_join}
                        {$where}
                    ) emp_type ON emp_type.employee_number_1 = emp.employee_number
                    AND emp_type.type_of_year_1 = emp.type_of_year
                    AND emp_type.fiscal_year_id_1 = emp.fiscal_year_id
                    {$agency_join_on}
                    LEFT JOIN
                    (
                        SELECT max(pay_date) as pay_date,
                        employee_number,fiscal_year_id,type_of_year
                            {$month_id_select}
                        FROM {$dataset}
                        WHERE fiscal_year_id = {$year} AND type_of_year = '{$yeartype}'
                        GROUP BY employee_number,fiscal_year_id,type_of_year
                        {$month_sub_query_group_by}
                    ) latest_emp ON latest_emp.pay_date = emp.pay_date
                    AND latest_emp.employee_number = emp.employee_number
                    AND latest_emp.fiscal_year_id = emp.fiscal_year_id
                    AND latest_emp.type_of_year = emp.type_of_year
                    {$month_join}
                    {$where}
                    GROUP BY emp.fiscal_year_id, emp.type_of_year, emp.employee_number, emp.type_of_employment
                    {$agency_sub_group_by}
                    {$title_sub_group_by}
                    {$month_sub_group_by}
                ) employees
                GROUP BY type_of_employment, type_of_year, fiscal_year_id
                {$agency_group_by}
                {$title_group_by}
                {$month_group_by}
    ";

//        log_error('QUERY:' .$query);
        $results = _checkbook_project_execute_sql_by_data_source($query,"checkbook");
        $total_employees = 0;
        $salaried_employees = 0;
        $non_salaried_employees = 0;
        foreach($results as $result){
            $total_employees += $result['number_employees'];
            if($result['type_of_employment'] == PayrollType::$NON_SALARIED) {
                $non_salaried_employees += $result['number_employees'];
            }
            else if($result['type_of_employment'] == PayrollType::$SALARIED) {
                $salaried_employees += $result['number_employees'];
            }
        }
        if($show_salaried) {
            foreach($results as $result){
                if($result['type_of_employment'] == PayrollType::$SALARIED) {
                    $salaried_results[0] = $result;
                    $node->data = $salaried_results;
                }
            }
        }
        else {
            $node->data = $results;
        }
        $node->total_employees = $total_employees;
        $node->non_salaried_employees = $non_salaried_employees;
        $node->salaried_employees = $salaried_employees;

    }

}