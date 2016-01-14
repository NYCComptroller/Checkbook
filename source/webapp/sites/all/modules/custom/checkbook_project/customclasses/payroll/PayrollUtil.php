<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 12/9/15
 * Time: 4:55 PM
 */

require_once(realpath(drupal_get_path('module', 'checkbook_project')) .'/customclasses/constants/Constants.php');


class PayrollUtil {

    static function getEmploymentTypeByAmountBasisId($amount_basis_id){
        $type_of_employment = $amount_basis_id == 1 ? PayrollType::$SALARIED : PayrollType::$NON_SALARIED;
        return $type_of_employment;
    }

    /**
     * Function updates the current payroll facet data, maps amount_basis_id from the database to the Payroll Type
     *
     * @param $node
     * @return array
     */
    static function updatePayrollTypeFacetData($node) {

        $data = array();
        $count = 0;
        foreach($node->data as $row) {
            if($row['payroll_type'] == PayrollType::$SALARIED) {
                $new_row = array(
                    'amount_basis_id_amount_basis_id' => '1',
                    'payroll_type' => $row['payroll_type'],
                    'txcount' => $row['txcount']
                );
                array_push($data, $new_row);
            }
            else {
                $count = $count+$row['txcount'];
            }
        }

        if($count > 0){
            array_push($data, array('amount_basis_id_amount_basis_id' => '2~3',
                'payroll_type' => PayrollType::$NON_SALARIED,
                'txcount' => $count
            ));
        }

        return $data;
    }

    /**
     * Returns the count of salaried employees
     * @param $year
     * @param $year_type
     * @param null $agency_id
     * @param null $employee_numb
     * @return int|null
     */
    static function getSalariedEmployeeCount($year, $year_type, $agency_id = null) {

        $employee_count = null;
        $agency = isset($agency_id);

        $sub_query_where = "WHERE fiscal_year_id = '$year' AND type_of_year = '$year_type'";
        $sub_query_group_by = "GROUP BY employee_number,fiscal_year_id,type_of_year";
        $sub_query_group_by .= $agency ? ",agency_id" : "";
        $where = $agency ? "WHERE agency_id = '$agency_id'" : "";

        $sql = "
                SELECT COUNT(DISTINCT emp.employee_number) AS record_count
                FROM aggregateon_payroll_employee_agency emp
                JOIN
                (
                    SELECT max(pay_date) as pay_date,
                    employee_number,fiscal_year_id,type_of_year
                    FROM aggregateon_payroll_employee_agency
                    {$sub_query_where}
                    {$sub_query_group_by}
                ) latest_emp ON latest_emp.pay_date = emp.pay_date
                AND latest_emp.employee_number = emp.employee_number
                AND latest_emp.fiscal_year_id = emp.fiscal_year_id
                AND latest_emp.type_of_year = emp.type_of_year
                AND type_of_employment = 'Salaried'
                {$where}";
        log_error('QUERY:' .$sql);

        try {
            $result = _checkbook_project_execute_sql_by_data_source($sql,_get_default_datasource());
            $employee_count= (int)$result[0]['record_count'];
        }
        catch (Exception $e) {
            log_error("Error in function getEmployeeCount() \nError getting data from controller: \n" . $e->getMessage());
        }


        return isset($employee_count) ? $employee_count : 0;
    }

    /**
     * Gets the total number of salaried and non-salaried employees by passed parameters.
     * @param $year
     * @param $year_type
     * @param null $title
     * @return int|null
     */
    static function getAgencyEmployeeCountByType($year, $year_type, $title = null) {

        $employee_totals = array();
        $where = "WHERE emp.fiscal_year_id = '$year' AND emp.type_of_year = '$year_type'";
        $where .= isset($title) ? " AND emp.civil_service_title = '$title'" : "";
        $sub_query_where = "WHERE fiscal_year_id = '$year' AND type_of_year = '$year_type'";

        $sql = "
        SELECT
        agency_id,
        COUNT(DISTINCT (CASE WHEN COALESCE(emp_type.type_of_employment,'Non-Salaried') = 'Salaried' THEN emp_type.employee_number_1 END)) AS total_salaried_employees,
        COUNT(DISTINCT (CASE WHEN COALESCE(emp_type.type_of_employment,'Non-Salaried') = 'Non-Salaried' THEN emp.employee_number END)) AS total_non_salaried_employees
        FROM aggregateon_payroll_employee_agency emp
        LEFT JOIN
        (
            SELECT DISTINCT
            emp.type_of_employment AS type_of_employment,
            emp.employee_number AS employee_number_1,
            emp.fiscal_year_id AS fiscal_year_id_1,
            emp.type_of_year AS type_of_year_1,
            emp.agency_id AS agency_id_1
            FROM aggregateon_payroll_employee_agency emp
            JOIN
            (
                SELECT max(pay_date) as pay_date,
                employee_number,fiscal_year_id,type_of_year,agency_id
                FROM aggregateon_payroll_employee_agency
                {$sub_query_where}
                GROUP BY employee_number,fiscal_year_id,type_of_year,agency_id
            ) latest_emp ON latest_emp.pay_date = emp.pay_date
            AND latest_emp.employee_number = emp.employee_number
            AND latest_emp.fiscal_year_id = emp.fiscal_year_id
            AND latest_emp.type_of_year = emp.type_of_year
            AND latest_emp.agency_id = emp.agency_id
            AND type_of_employment = 'Salaried'
        ) emp_type ON emp_type.employee_number_1 = emp.employee_number
        AND emp_type.type_of_year_1 = emp.type_of_year
        AND emp_type.fiscal_year_id_1 = emp.fiscal_year_id
        AND emp_type.agency_id_1 = emp.agency_id
        {$where}
        GROUP BY agency_id";
//        log_error('QUERY:' .$sql);
        try {
            $result = _checkbook_project_execute_sql_by_data_source($sql,_get_default_datasource());

            foreach ($result as $row) {
                $employee_totals[$row['agency_id']] = array(
                    'total_salaried_employees'=>$row['total_salaried_employees'],
                    'total_non_salaried_employees'=>$row['total_non_salaried_employees']);
            }
        }
        catch (Exception $e) {
            log_error("Error in function getEmployeeCount() \nError getting data from controller: \n" . $e->getMessage());
        }

        return $employee_totals;
    }
} 