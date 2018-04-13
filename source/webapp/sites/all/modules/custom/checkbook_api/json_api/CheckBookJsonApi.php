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
   * CheckBookJsonApi constructor.
   * @param $args
   */
  public function __construct($args)
  {
    $this->args = $args;
  }

  /**
   * @SWG\Get(
   *     path="/json_api/active_expense_contracts",
   *     @SWG\Response(response="200", description="active_expense_contracts")
   * )
   */
  public function active_expense_contracts()
  {
    $success = true;
    $data = 0;
    $year = $this->filter_year($this->args[1]);
    $year_type = $this->args[2] ?: 'fiscal';

    if (!$year) {
      $success = false;
      $message = 'invalid year';
    }  else {
      if (!in_array($year_type, ['calendar', 'fiscal'])) {
        $year_type = 'fiscal';
      }
      $message = 'Active expense contracts for ' . $year_type . ' year ' . $year;
      $query = "SELECT SUM(total_contracts) as total from aggregateon_total_contracts
                WHERE fiscal_year='{$year}' AND status_flag='A' AND type_of_year='B'";
      $response = _checkbook_project_execute_sql($query);

      if (sizeof($response) && $response[0]['total']) {
        $data = $response[0]['total'];
      }
    }

    return [
      'success' => $success,
      'data' => $data,
      'message' => $message,
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
    $success = true;
    $data = 0;
    $year = $this->filter_year($this->args[1]);

    if (!$year) {
      $success = false;
      $message = 'invalid year';
    }  else {
      $year_type = $this->args[2] ?: 'fiscal';
      if (!in_array($year_type, ['calendar', 'fiscal'])) {
        $year_type = 'fiscal';
      }
      $query = "SELECT SUM(total_contracts) as total from aggregateon_subven_total_contracts
                WHERE fiscal_year='{$year}' AND status_flag='A' AND type_of_year='B'";
      $response = _checkbook_project_execute_sql($query);

      if (sizeof($response) && $response[0]['total']) {
        $data = $response[0]['total'];
      }

      $message = 'There are ' . $data . ' active subcontracts for ' . $year_type . ' year ' . $year;
    }

    return [
      'success' => $success,
      'data' => $data,
      'message' => $message,
      'info' => 'usage: /json_api/active_subcontracts/2018/fiscal , can be used without extra params, ' .
        'like /json_api/active_subcontracts ; current year will be used and default type is "fiscal"'
    ];
  }

  /**
   * @param $year
   * @return false|int|string
   */
  private function filter_year($year) {
    $year = $year ?: date('Y');
    return (is_numeric($year) && $year > 2009 && $year <= (int)date('Y')) ? $year : false;
  }

  /**
   * @SWG\Get(
   *     path=" / json_api / approved_subcontracts",
   *     @SWG\Response(response="200", description="approved_subcontracts")
   * )
   */
  public function approved_subcontracts()
  {
    $year = $this->args[1];
    $year = $year ?: date('Y');
    $year_type = $this->args[2] ?: 'fiscal';
    if (!in_array($year_type, ['calendar', 'fiscal'])) {
      $year_type = 'fiscal';
    }
    $message = 'Subcontracts approved in ' . $year_type . ' year ' . $year;
    $success = true;
    $data = 9914;
    if (!is_numeric($year) || $year > date('Y') || $year < 2000) {
      $success = false;
      $message = 'invalid year';
      $data = null;
    }
    return [
      'success' => $success,
      'data' => $data,
      'message' => $message,
      'info' => 'usage: /json_api/approved_subcontracts/2018/fiscal , can be used without extra params, ' .
        'like /json_api/approved_subcontracts ; current year will be used and default type is "fiscal"'
    ];
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
    $message = 'Total payroll for ' . $year_type . ' year ' . $year;
    $success = true;
    $data = '$7.0B';
    if (!is_numeric($year) || $year > date('Y') || $year < 2000) {
      $success = false;
      $message = 'invalid year';
      $data = null;
    }
    return [
      'success' => $success,
      'data' => $data,
      'message' => $message,
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
    $message = 'Subcontracts cancelled for ' . $year_type . ' year ' . $year;
    $success = true;
    $data = '124';
    if (!is_numeric($year) || $year > date('Y') || $year < 2000) {
      $success = false;
      $message = 'invalid year';
      $data = null;
    }
    return [
      'success' => $success,
      'data' => $data,
      'message' => $message,
      'info' => 'usage: /json_api/subcontracts_cancelled/2018/fiscal , can be used without extra params, ' .
        'like /json_api/subcontracts_cancelled ; current year will be used and default type is "fiscal"'
    ];
  }
}
