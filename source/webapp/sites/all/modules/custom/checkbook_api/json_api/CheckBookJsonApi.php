<?php
/**
 * Created by IntelliJ IDEA.
 * User: alexandr.perfilov
 * Date: 4/9/18
 * Time: 5:50 PM
 */

/**
 * swagger  -o ../json_api_docs/
 *
 * @SWG\Info(title="Checkbook JSON API", version="0.1")
 */

namespace checkbook_json_api;

/**
 * Class CheckBookJsonApi
 * @package checkbook_json_api
 */
/**
 * Class CheckBookJsonApi
 * @package checkbook_json_api
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
  public function __construct($args)
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
   * @return string
   */
  private function validate_year_type($year_type)
  {
    switch (strtolower($year_type)) {
      case 'c':
      case 'calendar':
        return 'C';
        break;
      case 'b':
      case 'fiscal':
      default:
        return 'B';
    }
  }

  /**
   * @param string $status
   * @return array
   */
  private function get_subcontracts_by_status($status = '')
  {
    $year = $this->validate_year($this->args[1]);
    $year_type = $this->validate_year_type($this->args[2]);

    if ($this->success) {
      $query = "SELECT
                  SUM(CASE WHEN aprv_sta=3 THEN 1 ELSE 0 END) AS acco_reviewing,
                  SUM(CASE WHEN aprv_sta=4 THEN 1 ELSE 0 END) AS acco_approved,
                  SUM(CASE WHEN aprv_sta=2 THEN 1 ELSE 0 END) AS acco_rejected,
                  SUM(CASE WHEN aprv_sta=5 THEN 1 ELSE 0 END) AS acco_cancelled
                FROM aggregateon_mwbe_contracts_cumulative_spending a
                  LEFT JOIN (SELECT contract_number, aprv_sta FROM subcontract_details WHERE latest_flag='Y') sd ON a.contract_number=sd.contract_number
                  LEFT JOIN ref_document_code c ON a.document_code_id=c.document_code_id
                
                WHERE (a.fiscal_year = '{$year}' AND a.type_of_year = '{$year_type}' AND a.status_flag = 'A' AND c.document_code IN ('CTA1','CT1','CT2') AND a.scntrc_status = 2)";
      $response = _checkbook_project_execute_sql($query);

      if (sizeof($response) && $response[0]['acco_' . $status]) {
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
//      $query = "SELECT SUM(total_contracts) as total from aggregateon_total_contracts
//                WHERE fiscal_year='{$year}' AND status_flag='A' AND type_of_year='{$year_type}'";

      $query = "SELECT COUNT(contract_number) AS total FROM aggregateon_mwbe_contracts_cumulative_spending a
                  JOIN ref_document_code b ON a.document_code_id = b.document_code_id
                WHERE a.fiscal_year = {$year} AND a.type_of_year = '{$year_type}' AND a.status_flag = 'A' AND b.document_code IN ('MA1','CTA1','CT1')";

      $response = _checkbook_project_execute_sql($query);

      if (sizeof($response) && $response[0]['total']) {
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

      if (sizeof($response) && $response[0]['total']) {
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
   *     path=" / json_api / approved_subcontracts",
   *     @SWG\Response(response="200", description="approved_subcontracts")
   * )
   */
  public function approved_subcontracts()
  {
    return $this->get_subcontracts_by_status('approved');
  }

  /**
   * @SWG\Get(
   *     path=" / json_api / reviewing_subcontracts",
   *     @SWG\Response(response="200", description="reviewing_subcontracts")
   * )
   */
  public function reviewing_subcontracts()
  {
    return $this->get_subcontracts_by_status('reviewing');
  }

  /**
   * @SWG\Get(
   *     path=" / json_api / cancelled_subcontracts",
   *     @SWG\Response(response="200", description="cancelled_subcontracts")
   * )
   */
  public function cancelled_subcontracts()
  {
    return $this->get_subcontracts_by_status('cancelled');
  }

  /**
   * @SWG\Get(
   *     path=" / json_api / rejected_subcontracts",
   *     @SWG\Response(response="200", description="rejected_subcontracts")
   * )
   */
  public function rejected_subcontracts()
  {
    return $this->get_subcontracts_by_status('rejected');
  }

  /**
   * @SWG\Get(
   *     path=" / json_api / total_payroll",
   *     @SWG\Response(response="200", description="total_payroll")
   * )
   */
  public function total_payroll()
  {
    $year = $this->args[1];
    $year = $year ?: date('Y');
    $year_type = $this->args[2] ?: 'fiscal';
    if (!in_array($year_type, ['calendar', 'fiscal'])) {
      $year_type = 'calendar';
    }
    $message = 'Total payroll for ' . $this->year_type_string($year_type) . ' year ' . $year;
    $success = true;
    $data = '$7.0B';
    if (!is_numeric($year) || $year > date('Y') || $year < 2000) {
      $this->success = false;
      $this->message = 'invalid year';
      $data = null;
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
   *     path=" / json_api / subcontracts_cancelled",
   *     @SWG\Response(response="200", description="subcontracts_cancelled")
   * )
   */
  public function subcontracts_cancelled()
  {
    $year = $this->args[1];
    $year = $year ?: date('Y');
    $year_type = $this->args[2] ?: 'fiscal';
    if (!in_array($year_type, ['calendar', 'fiscal'])) {
      $year_type = 'fiscal';
    }
    $message = 'Subcontracts cancelled for ' . $this->year_type_string($year_type) . ' year ' . $year;
    $success = true;
    $data = '124';
    if (!is_numeric($year) || $year > date('Y') || $year < 2000) {
      $this->success = false;
      $this->message = 'invalid year';
      $data = null;
    }
    return [
      'success' => $this->success,
      'data' => $this->data,
      'message' => $this->message,
      'info' => 'usage: /json_api/subcontracts_cancelled/2018/fiscal , can be used without extra params, ' .
        'like /json_api/subcontracts_cancelled ; current year will be used and default type is "fiscal"'
    ];
  }
}
