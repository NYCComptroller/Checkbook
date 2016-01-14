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
} 