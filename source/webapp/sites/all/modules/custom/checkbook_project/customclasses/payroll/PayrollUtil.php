<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 12/9/15
 * Time: 4:55 PM
 */

require_once(__DIR__ . '/../constants/Constants.php');

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
    static function updateRateTypeFacetData($node) {

        $data = array();
        foreach($node->data as $row) {
            if($row['amount_basis_id_amount_basis_id'] == 2) {
                $new_row = array(
                    'amount_basis_id_amount_basis_id' => '2',
                    'rate_type' => 'Daily',
                    'txcount' => $row['txcount']
                );
                array_push($data, $new_row);
            }
            else  if($row['amount_basis_id_amount_basis_id'] == 3) {
                $new_row = array(
                    'amount_basis_id_amount_basis_id' => '3',
                    'rate_type' => 'Hourly',
                    'txcount' => $row['txcount']
                );
                array_push($data, $new_row);
            }
        }



        return $data;
    }

    /**
     * Checks the current URL to determine if this is the title landing page.
     * @return bool
     */
    static function isTitleLandingPage(){
        $http_ref = $_SERVER['HTTP_REFERER'];
        $current_url = $_GET['q'];
        $title_landing_page = preg_match('/title_landing/',$current_url) || preg_match('/title_landing/',$http_ref);
        return $title_landing_page;
    }

    /**
     * Given the title code, returns the string title.
     * @param $civil_service_title_code
     * @return string
     */
    static function getTitleByCode($civil_service_title_code) {
        $data_source = Datasource::getCurrent();
        $title = "";
        $sql = "SELECT civil_service_title
                FROM lookup_civil_service_title
                WHERE civil_service_title_code = {$civil_service_title_code}";

        try {
            $result = _checkbook_project_execute_sql_by_data_source($sql,$data_source);
            $title = $result[0]['civil_service_title'];
        }
        catch (Exception $e) {
            log_error("Error in function getTitleByCode() \nError getting data from controller: \n" . $e->getMessage());
        }
        return $title;
    }

    /**
     * Returns the count of salaried employees
     * @param $year
     * @param $year_type
     * @param null $agency_id
     * @param null $title
     * @return int|null
     */
    static function getSalariedEmployeeCount($year, $year_type, $agency_id = null, $title = null) {

        $data_source = Datasource::getCurrent();
        $employee_count = null;
        $sub_query_where = "WHERE fiscal_year_id = '$year' AND type_of_year = '$year_type'";
        $sub_query_group_by = "GROUP BY employee_number,fiscal_year_id,type_of_year";
        $where = "WHERE emp.fiscal_year_id = '$year' AND emp.type_of_year = '$year_type'";
        $where .= isset($agency_id) ? " AND agency_id = $agency_id" : "";
        $where .= isset($title) ? " AND civil_service_title_code = '$title'" : "";

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
//        log_error('getSalariedEmployeeCount:' .$sql);

        try {
            $result = _checkbook_project_execute_sql_by_data_source($sql,$data_source);
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
     * @return array
     */
    static function getAgencyEmployeeCountByType($year, $year_type, $title = null) {
        $data_source = Datasource::getCurrent();

        $where = $sub_query_where = $agency_select = $latest_emp_where = "";
        if(isset($year)) {
            $where .= $where == "" ? "WHERE emp.fiscal_year_id = $year" : " AND emp.fiscal_year_id = $year";
            $sub_query_where .= $sub_query_where == "" ? "WHERE fiscal_year_id = $year" : " AND fiscal_year_id = $year";
        }
        if(isset($year_type)) {
            $where .= $where == "" ? "WHERE emp.type_of_year = '$year_type'" : " AND emp.type_of_year = '$year_type'";
            $sub_query_where .= $sub_query_where == "" ? "WHERE type_of_year = '$year_type'" : " AND type_of_year = '$year_type'";
        }
        if(isset($title)) {
            $where .= $where == "" ? "WHERE emp.civil_service_title_code = '$title'" : " AND emp.civil_service_title_code = '$title'";
        }
        if(isset($agency)) {
            $where .= $where == "" ? "WHERE emp.agency_id = '$agency'" : " AND emp.agency_id = '$agency'";
        }
        $dataset = 'aggregateon_payroll_employee_agency';

        $query = "
                SELECT
                emp.type_of_year,
                emp.fiscal_year_id,
                COUNT(DISTINCT (CASE WHEN COALESCE(emp_type.type_of_employment,'Non-Salaried') = 'Salaried' THEN emp_type.employee_number_1 END)) AS total_salaried_employees,
                COUNT(DISTINCT (CASE WHEN COALESCE(emp_type.type_of_employment,'Non-Salaried') = 'Non-Salaried' AND emp.pay_date = latest_emp.pay_date THEN emp.employee_number END)) AS total_non_salaried_employees,
                emp.agency_id
                FROM {$dataset} emp
                LEFT JOIN
                (
                    SELECT DISTINCT
                    emp.type_of_employment AS type_of_employment,
                    emp.employee_number AS employee_number_1,
                    emp.fiscal_year_id AS fiscal_year_id_1,
                    emp.type_of_year AS type_of_year_1
                    ,agency_id AS agency_id_1
                    FROM {$dataset} emp
                    JOIN
                    (
                        SELECT max(pay_date) as pay_date,
                        employee_number,fiscal_year_id,type_of_year
                        FROM {$dataset}
                        {$sub_query_where}
                        GROUP BY employee_number,fiscal_year_id,type_of_year
                    ) latest_emp ON latest_emp.pay_date = emp.pay_date
                    AND latest_emp.employee_number = emp.employee_number
                    AND latest_emp.fiscal_year_id = emp.fiscal_year_id
                    AND latest_emp.type_of_year = emp.type_of_year
                    AND type_of_employment = 'Salaried'
                    {$where}
                ) emp_type ON emp_type.employee_number_1 = emp.employee_number
                AND emp_type.type_of_year_1 = emp.type_of_year
                AND emp_type.fiscal_year_id_1 = emp.fiscal_year_id
                AND emp_type.agency_id_1 = emp.agency_id
                LEFT JOIN
                (
                    SELECT max(pay_date) as pay_date,
                    employee_number,fiscal_year_id,type_of_year
                    FROM aggregateon_payroll_employee_agency
                    WHERE fiscal_year_id = {$year} AND type_of_year = '{$year_type}'
                    GROUP BY employee_number,fiscal_year_id,type_of_year
                ) latest_emp ON latest_emp.pay_date = emp.pay_date
                AND latest_emp.employee_number = emp.employee_number
                AND latest_emp.fiscal_year_id = emp.fiscal_year_id
                AND latest_emp.type_of_year = emp.type_of_year
                {$where}
                GROUP BY
                emp.fiscal_year_id,
                emp.type_of_year,
                emp.agency_id
    ";
//        log_error('getAgencyEmployeeCountByType:' .$query);
        $employee_totals = array();
        try {
            $result = _checkbook_project_execute_sql_by_data_source($query,$data_source);

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

    static function getDataByPayFrequency($pay_frequency, $array) {

        foreach($array as $data) {
            if($data['pay_frequency'] == $pay_frequency) {
                return $data;
            }
        }
        return null;
    }
    static function getTitleByEmployeeId($employeeId,$agency_id,$year_type,$year){
        $data_source = Datasource::getCurrent();
        $where = "WHERE fiscal_year_id = $year AND type_of_year = '$year_type'";
        $where .= isset($agency_id) ? " AND agency_id = $agency_id" : "";
        $where .= isset($employeeId) ? " AND employee_id = $employeeId" : "";
        $query="select s1.pay_date,
          s1.civil_service_title_code,
          s1.civil_service_title 
        from aggregateon_payroll_employee_agency s1
        inner join
        (
          select max(pay_date) pay_date,
          employee_id
          from aggregateon_payroll_employee_agency 
            {$where}
          group by employee_id
        ) s2
          on s1.employee_id = s2.employee_id
          and s1.pay_date= s2.pay_date";
        try {
            $result = _checkbook_project_execute_sql_by_data_source($query,$data_source);
            $title = $result[0]['civil_service_title'];

           return $title;
        }
        catch (Exception $e) {
            log_error("Error in function getEmployeeCount() \nError getting data from controller: \n" . $e->getMessage());
        }


    }
}
