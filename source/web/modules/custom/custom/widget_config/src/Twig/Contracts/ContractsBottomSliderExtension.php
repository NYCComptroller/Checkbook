<?php
namespace Drupal\widget_config\Twig\Contracts;

use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\checkbook_project\ContractsUtilities\ContractURLHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ContractsBottomSliderExtension extends AbstractExtension
{
  /**
   * @return TwigFunction[]
   */
  public function getFunctions()
  {
    return [
      'generateContractsBottomSlider' => new TwigFunction('generateContractsBottomSlider', [
        $this,
        'generateContractsBottomSlider',
      ]),
    ];
  }

  /**
   * @param $node
   * @return array[]
   */
  public static function generateContractsBottomSlider($node){
    $bottom_navigation_render = [];
    $currentPageUrlParts = explode('/',RequestUtilities::getCurrentPageUrl());
    $contractStatus = RequestUtilities::get('status');
    $yearParam = RequestUtilities::get('year');
    $currentFY = CheckbookDateUtil::getCurrentFiscalYearId();

    $tooltip = "";
    $is_edc_prime_vendor = RequestUtilities::get("vendor") == "5616";
    if ((in_array('contracts_landing', $currentPageUrlParts) && $contractStatus == 'A') && $is_edc_prime_vendor) {
      $tooltip = "Includes all multiyear contracts whose end date is greater than today's date or completed in the current fiscal year";
    }

    $bottom_navigation_render['active_expense'] = array(
      'label' => 'Active<br>Expense',
      'link' => ($node->data[0]['current_amount_sum'] > 0) ? ContractURLHelper::prepareActRegContractsSliderFilter('contracts_landing', 'A') : false,
      'dollar_amount' => FormattingUtilities::custom_number_formatter_format($node->data[0]['current_amount_sum'],1,'$'),
      'count' => number_format($node->data[0]['total_contracts']),
      'active_class' => (in_array('contracts_landing', $currentPageUrlParts) && $contractStatus == 'A') ? ' active' : "",
      'tooltip' => $tooltip
    );

    $bottom_navigation_render['reg_expense'] = array(
      'label' => 'Registered<br>Expense',
      'link' => ($node->data[1]['current_amount_sum'] > 0) ? ContractURLHelper::prepareActRegContractsSliderFilter('contracts_landing', 'R') : false,
      'dollar_amount' => FormattingUtilities::custom_number_formatter_format($node->data[1]['current_amount_sum'],1,'$'),
      'count' => number_format($node->data[1]['total_contracts']),
      'active_class' => (in_array('contracts_landing', $currentPageUrlParts) && $contractStatus == 'R') ? ' active' : null,
    );

    $bottom_navigation_render['active_revenue'] = array(
      'label' => 'Active<br>Revenue',
      'link' => ($node->data[2]['current_amount_sum'] > 0) ? ContractURLHelper::prepareActRegContractsSliderFilter('contracts_revenue_landing', 'A') : false,
      'dollar_amount' => FormattingUtilities::custom_number_formatter_format($node->data[2]['current_amount_sum'],1,'$'),
      'count' => number_format($node->data[2]['total_contracts']),
      'active_class' => (in_array('contracts_revenue_landing', $currentPageUrlParts) && $contractStatus == 'A') ? ' active' : "",
    );

    $bottom_navigation_render['reg_revenue'] = array(
      'label' => 'Registered<br>Revenue',
      'link' => ($node->data[3]['current_amount_sum'] > 0) ? ContractURLHelper::prepareActRegContractsSliderFilter('contracts_revenue_landing', 'R') : false,
      'dollar_amount' => FormattingUtilities::custom_number_formatter_format($node->data[3]['current_amount_sum'],1,'$'),
      'count' => number_format($node->data[3]['total_contracts']),
      'active_class' => (in_array('contracts_revenue_landing', $currentPageUrlParts) && $contractStatus == 'R') ? ' active' : "",
    );

    if ($yearParam != $currentFY) {
      $pending_expense_active_class = ' disable_me';
      $pending_revenue_active_class = ' disable_me';
    } else {
      $pending_expense_active_class = in_array('contracts_pending_exp_landing', $currentPageUrlParts) ? ' active' : "";
      $pending_revenue_active_class = in_array('contracts_pending_rev_landing', $currentPageUrlParts) ? ' active' : "";
    }

    // NYCCHKBK 13245 - The pending links have to be enabed for negative amounts
    // Updating the condition accordingly. verified by setting the
    // $node->data[4]['total_contract_amount'] = '-320.60';

    $bottom_navigation_render['pending_expense'] = array(
      'label' => 'Pending<br>Expense',
      'link' => ($node->data[4]['total_contract_amount'] != 0 && $yearParam == $currentFY) ? ContractURLHelper::preparePendingContractsSliderFilter('contracts_pending_exp_landing') : "",
      'dollar_amount' => FormattingUtilities::custom_number_formatter_format($node->data[4]['total_contract_amount'],1,'$'),
      'count' => number_format($node->data[5]['total_contracts']),
      'active_class' => $pending_expense_active_class,
      'tab_style' => $yearParam != $currentFY ? "pending" : ""
    );

    $bottom_navigation_render['pending_revenue'] = array(
      'label' => 'Pending<br>Revenue',
      'link' => ($node->data[6]['total_contract_amount'] != 0 && $yearParam == $currentFY) ? ContractURLHelper::preparePendingContractsSliderFilter('contracts_pending_rev_landing') : "",
      'dollar_amount' => FormattingUtilities::custom_number_formatter_format($node->data[6]['total_contract_amount'],1,'$'),
      'count' => number_format($node->data[7]['total_contracts']),
      'active_class' => $pending_revenue_active_class,
      'tab_style' => $yearParam != $currentFY ? "pending" : ""
    );

    return [
      'bottom_navigation' => $bottom_navigation_render,
    ];
  }
}
