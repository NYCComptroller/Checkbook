<?php
/**
 * Created by PhpStorm.
 * User: atorkelson
 * Date: 12/9/15
 * Time: 4:55 PM
 */

module_load_include('php', 'checkbook_project', 'customclasses/constants/Constants.php');
class PayrollUtil {
  /**
   * @param $amount_basis_id
   * @return string
   */
    public static function getEmploymentTypeByAmountBasisId($amount_basis_id): string
    {
        return $amount_basis_id == 1 ? PayrollType::$SALARIED : PayrollType::$NON_SALARIED;
    }

    /**
     * Function updates the current payroll facet data, maps amount_basis_id from the database to the Payroll Type
     * @param $node
     * @return array
     */
    public static function updatePayrollTypeFacetData($node): array
    {
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
          } else {
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

    public static function updateRateTypeFacetData($node): array
    {
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
    public static function isTitleLandingPage(): bool
    {
        $httpRef = $_SERVER['HTTP_REFERER'];
        $currentUrl = $_GET['q'];
        return preg_match('/title_landing/',$currentUrl) || preg_match('/title_landing/',$httpRef);
    }

    /**
     * Given the title code, returns the string title.
     * @param $civil_service_title_code
     * @return string
     */
    public static function getTitleByCode($civil_service_title_code): string
    {
        $data_source = Datasource::getCurrent();
        $title = "";
        $sql = "SELECT civil_service_title
                FROM lookup_civil_service_title
                WHERE civil_service_title_code = {$civil_service_title_code}";
        try {
            $result = _checkbook_project_execute_sql_by_data_source($sql,$data_source);
            $title = strlen($result[0]['civil_service_title']) > 0 ? $result[0]['civil_service_title'] : $title;
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
    public static function getSalariedEmployeeCount($year, $year_type, $agency_id = null, $title = null): ?int
    {
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
        try {
            $result = _checkbook_project_execute_sql_by_data_source($sql,$data_source);
            $employee_count = (int)$result[0]['record_count'];
        }
        catch (Exception $e) {
            log_error("Error in function getEmployeeCount() \nError getting data from controller: \n" . $e->getMessage());
        }
        return $employee_count ?? 0;
    }

    /**
     * Gets the total number of salaried and non-salaried employees by passed parameters.
     * @param $year
     * @param $year_type
     * @param null $title
     * @return array
     */
    public static function getAgencyEmployeeCountByType($year, $year_type, $title = null): array
    {
        $data_source = Datasource::getCurrent();
        $where = $sub_query_where = "";

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

  /**
   * @param $pay_frequency
   * @param $array
   * @return mixed|null
   */
    public static function getDataByPayFrequency($pay_frequency, $array) {
        foreach($array as $data) {
            if($data['pay_frequency'] == $pay_frequency) {
                return $data;
            }
        }
        return null;
    }

  /**
   * @param $employeeId
   * @param $agency_id
   * @param $year_type
   * @param $year
   * @return mixed
   */
    public static function getTitleByEmployeeId($employeeId,$agency_id,$year_type,$year){
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

  /**
   * @param $type
   * @return string
   */
    public static function getPayrollTitlebyType($type): string
    {
      $title = '';
      switch($type){
        case "nonsalaried":
          $title ="Payroll Summary by Number of Non-Salaried Employees";
          break;
      }
      return $title;
    }

  /**
   * @return string
   */
    public static function getPayrollType(): string
    {
      $URL =  $_SERVER['HTTP_REFERER'];
      $payroll_type = RequestUtil::getRequestKeyValueFromURL("payroll_type", $URL);
      if($payroll_type){
        return PayrollType::$NON_SALARIED;
      }
      else{
        return PayrollType::$SALARIED;
      }
    }

  /**
   * @param $row
   * @return string
   */
    public static function getAmountUrl($row): string
    {
      $landingPageUrl = '/payroll'.((RequestUtilities::getRequestParamValue(UrlParameter::AGENCY) || Datasource::isNYCHA()) ? '/agency_landing' : '')
        . '/yeartype/C/year/' . $row['calendar_fiscal_year_id'] . _checkbook_project_get_url_param_string('datasource')
        . ((Datasource::isNYCHA()) ? '/agency/' . $row['agency_id'] : '');
      $bottomUrl = '?expandBottomContURL=/panel_html/payroll_employee_transactions/payroll/employee/transactions/agency/' . $row['agency_id']
        . '/yeartype/C/year/' . $row['calendar_fiscal_year_id'] . _checkbook_project_get_url_param_string('datasource')
        . '/salamttype/' . $row['amount_basis_id'] . '/abc/' . $row['employee_id'] ;

      return $landingPageUrl . $bottomUrl;
    }

  /**
   * @param $row
   * @return string
   */
    public static function getAnnualSalaryLink($row): string
    {
      if($row['amount_basis_id'] === 1){
        $url = self::getAmountUrl($row);
        $link = '<a href='. $url . '>'. $row['formatted_salary_amount'] .'</a>';
        return $link;
      }else{
        return'-';
      }
    }

  /**
   * @param $row
   * @return string
   */
  public static function getNonSalaryLink($row): string
  {
    if($row['amount_basis_id'] === 1){
      return'-';
    }else{
      $url = self::getAmountUrl($row);
      return '<a href='. $url . '>'. $row['formatted_non_salary_amount'] .'</a>';
    }
  }

  /**
   * @param $row
   * @return string
   */
  public static function getDailyWageLink($row): string
  {
    if($row['amount_basis_id'] === 2 ){
      $url = self::getAmountUrl($row);
      return '<a href='. $url . '>'. $row['formatted_daily_wage_amount'] .'</a>';;
    }else{
      return'-';
    }
  }

  /**
   * @param $row
   * @return string
   */
  public static function getHourlyRateLink($row): string
  {
    if($row['amount_basis_id'] === 3 ){
      $url = self::getAmountUrl($row);
      return '<a href='. $url . '>'. $row['formatted_hourly_rate_amount'] .'</a>';;
    }else{
      return'-';
    }
  }
}
