<?php

namespace checkbook_json_api;

use PHPUnit\Runner\Exception;

/**
 * Class CheckBookJsonApi
 * @package checkbook_json_api
 * swagger  -o ../json_api_docs/
 *
 * @SWG\Info(title="Checkbook JSON API", version="0.1")
 */
class CheckBookJsonApi
{
  /**
   * @var
   */
  private $args;

  /**
   * @var int
   */
  private $data = 0;

  /**
   * @var bool
   */
  private $success = true;

  /**
   * @var string
   */
  private $message = '';

  /**
   * CheckBookJsonApi constructor.
   * @param $args
   */
  public function __construct($args = [])
  {
    $this->args = $args;
  }

  /**
   * @param $year
   * @return false|int|string
   */
  private function validate_year($year)
  {
    $year = $year ?: date('Y');
    $year = (is_numeric($year) && $year > 2009 && $year <= (int)date('Y')) ? $year : false;
    if (!$year) {
      $this->message = 'invalid year';
      $this->success = false;
    }
    return $year;
  }

  /**
   * @param $year_type
   * @return string
   */
  private function year_type_string($year_type)
  {
    if ('C' == $year_type) {
      return 'calendar';
    }
    return 'fiscal';
  }

  /**
   * @param $year_type
   * @param $default
   * @return string
   */
  private function validate_year_type($year_type, $default = 'B')
  {
    switch (strtolower($year_type)) {
      case 'c':
      case 'calendar':
        return 'C';
      case 'b':
      case 'fiscal':
        return 'B';
      default:
        return $default;
    }
  }

  /**
   * @param string $status
   * @return array
   * @throws \Exception
   */
  private function get_subcontracts_by_status($status = '')
  {
    $year = $this->validate_year($this->args[1]);
    $year_type = $this->validate_year_type($this->args[2]);

    if ($this->success) {
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

      if (!empty($response) && !empty($response[0]['acco_' . $status])) {
        $this->data = $response[0]['acco_' . $status];
      }
      $this->message = 'There are ' . $this->data . ' subcontracts ' . $status . ' in ' . $this->year_type_string($year_type) . ' year ' . $year;
    }

    return [
      'success' => $this->success,
      'data' => $this->data,
      'message' => $this->message,
      'info' => 'usage: /json_api/' . $status . '_subcontracts/2018/fiscal , can be used without extra params, ' .
        'like /json_api/' . $status . '_subcontracts ; current year will be used and default type is "fiscal"'
    ];
  }

  /**
   * @SWG\Get(
   *     path="/json_api/active_expense_contracts",
   *     @SWG\Response(response="200", description="active_expense_contracts")
   * )
   */
  public function active_expense_contracts()
  {
    $year = $this->validate_year($this->args[1]);
    $year_type = $this->validate_year_type($this->args[2]);

    if ($this->success) {
      $query = "SELECT COUNT(contract_number) AS total FROM aggregateon_mwbe_contracts_cumulative_spending a
                  JOIN ref_document_code b ON a.document_code_id = b.document_code_id
                WHERE a.fiscal_year = {$year} AND a.type_of_year = '{$year_type}' AND a.status_flag = 'A' AND b.document_code IN ('MA1','CTA1','CT1')";

      $response = _checkbook_project_execute_sql($query);

      if (!empty($response) && $response[0]['total']) {
        $this->data = $response[0]['total'];
      }
      $this->message = 'There are ' . $this->data . ' active expense contracts for ' .
        $this->year_type_string($year_type) . ' year ' . $year;
    }

    return [
      'success' => $this->success,
      'data' => $this->data,
      'message' => $this->message,
      'info' => 'usage: /json_api/active_expense_contracts/2018/fiscal , can be used without extra params, ' .
        'like /json_api/active_expense_contracts ; current year will be used and default type is "fiscal"'
    ];
  }

  /**
   * @SWG\Get(
   *     path="/json_api/active_subcontracts",
   *     @SWG\Response(response="200", description="active_subcontracts")
   * )
   */
  public function active_subcontracts()
  {
    $year = $this->validate_year($this->args[1]);
    $year_type = $this->validate_year_type($this->args[2]);

    if ($this->success) {
      $query = "SELECT SUM(total_contracts) as total from aggregateon_subven_total_contracts
                WHERE fiscal_year='{$year}' AND status_flag='A' AND type_of_year='{$year_type}'";
      $response = _checkbook_project_execute_sql($query);

      if (!empty($response) && $response[0]['total']) {
        $this->data = $response[0]['total'];
      }

      $this->message = 'There are ' . $this->data . ' active subcontracts for ' .
        $this->year_type_string($year_type) . ' year ' . $year;
    }

    return [
      'success' => $this->success,
      'data' => $this->data,
      'message' => $this->message,
      'info' => 'usage: /json_api/active_subcontracts/2018/fiscal , can be used without extra params, ' .
        'like /json_api/active_subcontracts ; current year will be used and default type is "fiscal"'
    ];
  }

  /**
   * @SWG\Get(
   *     path="/json_api/subcontracts_approved",
   *     @SWG\Response(response="200", description="subcontracts_approved")
   * )
   */
  public function subcontracts_approved()
  {
    return $this->get_subcontracts_by_status('approved');
  }

  /**
   * @SWG\Get(
   *     path="/json_api/subcontracts_under_review",
   *     @SWG\Response(response="200", description="subcontracts_under_review")
   * )
   */
  public function subcontracts_under_review()
  {
    return $this->get_subcontracts_by_status('reviewing');
  }

  /**
   * @SWG\Get(
   *     path="/json_api/subcontracts_submitted",
   *     @SWG\Response(response="200", description="subcontracts_submitted")
   * )
   */
  public function subcontracts_submitted()
  {
    return $this->get_subcontracts_by_status('submitted');
  }

  /**
   * @SWG\Get(
   *     path="/json_api/subcontracts_canceled",
   *     @SWG\Response(response="200", description="subcontracts_canceled")
   * )
   */
  public function subcontracts_canceled()
  {
    return $this->get_subcontracts_by_status('cancelled');
  }

  /**
   * @SWG\Get(
   *     path="/json_api/subcontracts_rejected",
   *     @SWG\Response(response="200", description="subcontracts_rejected")
   * )
   */
  public function subcontracts_rejected()
  {
    return $this->get_subcontracts_by_status('rejected');
  }

  /**
   * @SWG\Get(
   *     path="/json_api/total_payroll",
   *     @SWG\Response(response="200", description="total_payroll")
   * )
   */
  public function total_payroll()
  {
    $year = $this->validate_year($this->args[1]);
    $year_type = $this->validate_year_type($this->args[2], 'C');

    if ($this->success) {
      $year_id = 100 + ($year - 2000) + 1;
      $query = "SELECT
                  SUM(gross_pay) AS total_gross_pay,
                  SUM(base_pay) AS total_base_pay,
                  SUM(overtime_pay) AS total_overtime_pay
                FROM aggregateon_payroll_employee_agency s0
                WHERE s0.type_of_year = '{$year_type}' AND s0.fiscal_year_id = '{$year_id}'";
      $response = _checkbook_project_execute_sql($query);

      if (!empty($response) && $response[0]['total_gross_pay']) {
        $this->data = $response[0]['total_gross_pay'];
        $this->data = round($this->data, -6);
        $this->data = money_format('%i', $this->data);
      }

      $this->message = 'Total payroll for ' . $this->year_type_string($year_type) .
        ' year ' . $year . ' is ' . $this->data;
    }

    return [
      'success' => $this->success,
      'data' => $this->data,
      'message' => $this->message,
      'info' => 'usage: /json_api/total_payroll/2018/calendar , can be used without extra params, ' .
        'like /json_api/total_payroll ; current year will be used and default type is "calendar"'
    ];
  }

  /**
   * @SWG\Get(
   *     path="/json_api/total_spending",
   *     @SWG\Response(response="200", description="total_spending")
   * )
   */
  public function total_spending()
  {
    $year = $this->validate_year($this->args[1]);
    $year_type = $this->validate_year_type($this->args[2]);

    if ($this->success) {
      $year_id = 100 + ($year - 2000) + 1;
      $query = "SELECT SUM(total_spending_amount) AS total
                  FROM aggregateon_mwbe_spending_coa_entities s0
                WHERE s0.type_of_year = '{$year_type}'
                      AND s0.minority_type_id IN (2, 3, 4, 5, 9)
                      AND s0.year_id = '{$year_id}'";
      $response = _checkbook_project_execute_sql($query);

      if (!empty($response) && $response[0]['total']) {
        $this->data = $response[0]['total'];
        $this->data = round($this->data, -6);
        $this->data = money_format('%i', $this->data);
      }

      $this->message = 'Total spending for ' . $this->year_type_string($year_type) .
        ' year ' . $year . ' is ' . $this->data;
    }

    return [
      'success' => $this->success,
      'data' => $this->data,
      'message' => $this->message,
      'info' => 'usage: /json_api/total_spending/2018/calendar , can be used without extra params, ' .
        'like /json_api/total_spending ; current year will be used and default type is "calendar"'
    ];
  }

  /**
   * @SWG\Get(
   *     path="/json_api/total_budget",
   *     @SWG\Response(response="200", description="total_budget")
   * )
   */
  public function total_budget()
  {
    $year = $this->validate_year($this->args[1]);

    if ($this->success) {
      $query = "SELECT SUM(COALESCE(current_budget_amount,0)) AS total
                  FROM budget s0
                WHERE s0.budget_fiscal_year = '{$year}'";
      $response = _checkbook_project_execute_sql($query);

      if (!empty($response) && $response[0]['total']) {
        $this->data = $response[0]['total'];
        $this->data = round($this->data, -6);
        $this->data = money_format('%i', $this->data);
      }

      $this->message = 'Total budget for year ' . $year . ' is ' . $this->data;
    }

    return [
      'success' => $this->success,
      'data' => $this->data,
      'message' => $this->message,
      'info' => 'usage: /json_api/total_budget/2018 , can be used without extra params, ' .
        'like /json_api/total_budget ; current year will be used'
    ];
  }

  /**
   * @SWG\Get(
   *     path="/json_api/total_revenue",
   *     @SWG\Response(response="200", description="total_revenue")
   * )
   */
  public function total_revenue()
  {
    $year = $this->validate_year($this->args[1]);

    if ($this->success) {
      $query = "SELECT SUM(COALESCE(posting_amount,0)) AS total
                  FROM revenue_details s0
                WHERE s0.budget_fiscal_year = '{$year}'";
      $response = _checkbook_project_execute_sql($query);

      if (!empty($response) && $response[0]['total']) {
        $this->data = $response[0]['total'];
        $this->data = round($this->data, -6);
        $this->data = money_format('%i', $this->data);
      }

      $this->message = 'Total revenue for year ' . $year . ' is ' . $this->data;
    }

    return [
      'success' => $this->success,
      'data' => $this->data,
      'message' => $this->message,
      'info' => 'usage: /json_api/total_revenue/2018 , can be used without extra params, ' .
        'like /json_api/total_revenue ; current year will be used'
    ];
  }

  /**
   * @SWG\Get(
   *     path="/json_api/etl_status",
   *     @SWG\Response(response="200", description="etl_status")
   * )
   */
  public function etl_status()
  {
    drupal_page_is_cacheable(FALSE);

    global $base_url, $conf, $databases;

    $return = [];
    if ('uat-checkbook-nyc.reisys.com' == parse_url($base_url, PHP_URL_HOST)) {
      $return = $this->getUatEtlStatus();
    } elseif (!empty($conf['etl-status-path'])) {
      $return = $this->getProdEtlStatus();
    }

    $return['connections'] = [];
    if (!empty($databases['default']['default']['host'])) {
      $return['connections']['mysql'] = $databases['default']['default']['host'];
    }
    if (!empty($databases['checkbook']['main']['host'])) {
      $return['connections']['psql_main'] = $databases['checkbook']['main']['host'];
    }
    if (!empty($databases['checkbook']['etl']['host'])) {
      $return['connections']['psql_etl'] = $databases['checkbook']['etl']['host'];
    }
    if (!empty($databases['checkbook_oge']['main']['host'])) {
      $return['connections']['psql_oge'] = $databases['checkbook_oge']['main']['host'];
    }
    if (!empty($databases['checkbook_nycha']['main']['host'])) {
      $return['connections']['psql_nycha'] = $databases['checkbook_nycha']['main']['host'];
    }
    if (!empty($conf['check_book']['solr']['url'])) {
      $return['connections']['solr'] = $conf['check_book']['solr']['url'];
    }

    return $return;
  }

  /**
   * @return array
   */
  private function getProdEtlStatus()
  {
    global $conf;

    try {
      $data = file_get_contents($conf['etl-status-path'] . 'etl_status.txt');
      list(, $date) = explode(',', $data);
      $this->data = trim($date);
    } catch (Exception $e) {
      $this->message .= $e->getMessage();
    }

    $invalid_records = '';
    $invalid_records_timestamp = 0;
    $invalid_records_csv_path = $conf['etl-status-path'] . 'invalid_records_details.csv';
    try {
      if (is_file($invalid_records_csv_path)) {
        $invalid_records = array_map('str_getcsv', file($invalid_records_csv_path));
        $invalid_records_timestamp = filemtime($invalid_records_csv_path);
      } else {
        $invalid_records = [
          'FATAL ERROR',
          'Could not find `invalid_records_details.csv` on server'
        ];
      }
    } catch (Exception $e) {
      $this->message .= $e->getMessage();
    }

    return [
      'success' => $this->success,
      'data' => $this->data,
      'message' => $this->message,
      'invalid_records' => $invalid_records,
      'invalid_records_timestamp' => $invalid_records_timestamp,
    ];
  }

  /**
   * @return array
   * @throws \Exception
   */
  private function getUatEtlStatus()
  {
    $query = "SELECT DISTINCT 
                  MAX(refresh_end_date :: TIMESTAMP) AS last_successful_run
                FROM etl.refresh_shards_status
                WHERE latest_flag = 1";

    try {
      $response = _checkbook_project_execute_sql($query, 'etl');
    } catch (Exception $e) {
      $this->success = false;
      $this->message = $e->getMessage();
    }

    if (!empty($response) && $response[0]['last_successful_run']) {
      $this->data = $response[0]['last_successful_run'];
    }

    return [
      'success' => $this->success,
      'data' => $this->data,
      'message' => $this->message,
      'info' => 'Last successful ETL run date'
    ];
  }

}
