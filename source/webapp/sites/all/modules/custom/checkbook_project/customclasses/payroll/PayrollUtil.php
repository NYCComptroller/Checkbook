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
    static function getSalariedEmployeeCount($year, $year_type, $agency_id = null, $employee_number = null) {

        $employee_count = self::getEmployeeCount(PayrollType::$SALARIED, $year, $year_type, $agency_id, $employee_number);
        return isset($employee_count) ? $employee_count : 0;
    }

    /**
     * Returns the count of non-salaried employees
     * @param $year
     * @param $year_type
     * @param null $agency_id
     * @param null $employee_number
     * @return int|null
     */
    static function getNonSalariedEmployeeCount($year, $year_type, $agency_id = null, $employee_number = null) {

        $employee_count = self::getEmployeeCount(PayrollType::$NON_SALARIED, $year, $year_type, $agency_id, $employee_number);
        return isset($employee_count) ? $employee_count : 0;
    }

    /**
     * Returns the count of employees based on type
     * @param PayrollType $payrollType
     * @param $year
     * @param $year_type
     * @param $agency_id
     * @param $employee_number
     * @return null
     */
    static function getEmployeeCount($payrollType, $year, $year_type, $agency_id, $employee_number) {

        $employee_count = null;

        try {
            $data_set = isset($agency_id) ? 'aggregateon_payroll_employment_type' : 'aggregateon_payroll_employment_type_nyc';
            $parameters = array(
                'fiscal_year_id' => $year,
                'type_of_year' => $year_type,
                'type_of_employment' => $payrollType
            );
            if(isset($agency_id)) $parameters['agency_id'] = $agency_id;
            if(isset($employee_number)) $parameters['employee_number'] = $employee_number;

            $where_filter = '';
            $where_filters = array();
            foreach($parameters as $param => $value) {
                $where_filters[] = _widget_build_sql_condition($param, $value);
            }
            if(count($where_filters) > 0){
                $where_filter = ' WHERE ' . implode(' AND ', $where_filters);
            }
            $sql = "SELECT SUM(count_employment) AS record_count
            FROM {$data_set}".$where_filter;

            $result = _checkbook_project_execute_sql_by_data_source($sql,_get_default_datasource());
            $employee_count= (int)$result[0]['record_count'];
        }
        catch (Exception $e) {
            log_error("Error in function getEmployeeCount() \nError getting data from controller: \n" . $e->getMessage());
        }
        return $employee_count;
    }
} 