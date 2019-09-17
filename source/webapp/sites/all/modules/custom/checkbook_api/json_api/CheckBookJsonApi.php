<?php

namespace checkbook_json_api;

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
    public $success = true;

    /**
     * @var string
     */
    public $message = '';

    /**
     * @var CheckBookJsonApiHelper
     */
    private $Helper;

    /**
     * @var CheckBookJsonApiModel
     */
    public $Model;

    /**
     * CheckBookJsonApi constructor.
     * @param $args
     */
    public function __construct($args = [])
    {
        $this->args = $args;
        $this->Helper = new CheckBookJsonApiHelper($this);
        $this->Model = new CheckBookJsonApiModel();
    }

    /**
     * @param string $status
     * @return array
     * @throws \Exception
     */
    private function get_subcontracts_by_status($status = '')
    {
        $year = $this->Helper->validate_year($this->args);
        $year_type = $this->Helper->validate_year_type($this->args);

        if ($this->success) {
            $response = $this->Model->get_subcontracts_data($year, $year_type);

            if (!empty($response) && !empty($response[0]['acco_' . $status])) {
                $this->data = $response[0]['acco_' . $status];
            }
            $this->message = 'There are ' . $this->data . ' subcontracts ' . $status . ' in ' . $this->Helper->get_verbal_year_type($year_type) . ' year ' . $year;
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
        $year = $this->Helper->validate_year($this->args);
        $year_type = $this->Helper->validate_year_type($this->args);

        if ($this->success) {
            $response = $this->Model->get_expense_contracts($year, $year_type);

            if (!empty($response) && $response[0]['total']) {
                $this->data = $response[0]['total'];
            }
            $this->message = 'There are ' . $this->data . ' active expense contracts for ' .
                $this->Helper->get_verbal_year_type($year_type) . ' year ' . $year;
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
        $year = $this->Helper->validate_year($this->args);
        $year_type = $this->Helper->validate_year_type($this->args);

        if ($this->success) {
            $response = $this->Model->get_active_subcontracts($year, $year_type);

            if (!empty($response) && $response[0]['total']) {
                $this->data = $response[0]['total'];
            }

            $this->message = 'There are ' . $this->data . ' active subcontracts for ' .
                $this->Helper->get_verbal_year_type($year_type) . ' year ' . $year;
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
        $year = $this->Helper->validate_year($this->args);
        $year_type = $this->Helper->validate_year_type($this->args, 'C');

        if ($this->success) {
            $response = $this->Model->get_total_payroll($year, $year_type);

            if (!empty($response) && $response[0]['total_gross_pay']) {
                $this->data = $response[0]['total_gross_pay'];
                $this->data = round($this->data, -6);
                $this->data = money_format('%i', $this->data);
            }

            $this->message = 'Total payroll for ' . $this->Helper->get_verbal_year_type($year_type) .
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
        $year = $this->Helper->validate_year($this->args);
        $year_type = $this->Helper->validate_year_type($this->args);

        if ($this->success) {
            $response = $this->Model->get_total_spending($year, $year_type);

            if (!empty($response) && $response[0]['total']) {
                $this->data = $response[0]['total'];
                $this->data = round($this->data, -6);
                $this->data = money_format('%i', $this->data);
            }

            $this->message = 'Total spending for ' . $this->Helper->get_verbal_year_type($year_type) .
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
        $year = $this->Helper->validate_year($this->args);

        if ($this->success) {
            $response = $this->Model->get_total_budget($year);

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
        $year = $this->Helper->validate_year($this->args);

        if ($this->success) {
            $response = $this->Model->get_total_revenue($year);

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

        global $conf;

        $return = [];
        if ('UAT' == $conf['CHECKBOOK_ENV'] || ($conf['get_direct_uat_etl_status'] ?? false)) {
            $return = $this->Helper->getUatEtlStatus();
        } elseif ('PROD' == $conf['CHECKBOOK_ENV']) {
            $return = $this->Helper->getProdEtlStatus();
        }

        $return['connections'] = $this->Helper->get_connections_info();

        return $return;
    }

}
