<?php
namespace Drupal\widget_config\Twig\Contracts;

use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\ContractsUtilities\ContractURLHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SubvendorsBottomSliderExtension extends AbstractExtension
{
  /**
   * @return TwigFunction[]
   */
    public function getFunctions()
    {
      return [
        'generateSubvendorBottomSlider' => new TwigFunction('generateSubvendorBottomSlider', [
          $this,
          'generateSubvendorBottomSlider',
        ]),
      ];
    }

  /**
   * @param $node
   * @return array[]
   */
  public static function generateSubvendorBottomSlider($node)
  {
    $bottom_navigation_render = [];
    $currentPage = RequestUtilities::getCurrentPageUrl();
    $currentPageUrlParts = explode('/',RequestUtilities::getCurrentPageUrl());
    $contractStatus = RequestUtilities::get('status');

    $is_active_expense_contracts = in_array('contracts_landing', $currentPageUrlParts) && $contractStatus == 'A' && RequestUtilities::get("bottom_slider") != "sub_vendor";
    $is_edc_prime_vendor = RequestUtilities::get("vendor") == "5616";

    $bottom_navigation_render['active_expense'] = array(
      'label' => 'Total Active<br>Sub Vendor Contracts',
      'link' => ContractURLHelper::prepareActRegContractsSliderFilter('contracts_landing', 'A'),
      'dollar_amount' => FormattingUtilities::custom_number_formatter_format($node->data[0]['current_amount_sum'],1,'$'),
      'count' => number_format($node->data[0]['total_contracts']),
      'active_class' => $is_active_expense_contracts ?' active' : "",
      'prime_vendor_class ' => ($is_active_expense_contracts && $is_edc_prime_vendor) ? ' activeExpenseContract' : ""
    );

    $bottom_navigation_render['registered_expense'] = array(
      'label' => 'New Sub Vendor Contracts<br>by Fiscal Year',
      'link' => ContractURLHelper::prepareActRegContractsSliderFilter('contracts_landing', 'R'),
      'dollar_amount' => FormattingUtilities::custom_number_formatter_format($node->data[1]['current_amount_sum'],1,'$'),
      'count' => number_format($node->data[1]['total_contracts']),
      'active_class' => (in_array('contracts_landing', $currentPageUrlParts) && $contractStatus == 'R') ?' active' : "",
    );

    $bottom_navigation_render['subvendors'] = array(
      'label' => 'Status of Sub Vendor<br>Contracts by Prime Vendor',
      'link' => ContractURLHelper::prepareSubvendorContractsSliderFilter('contracts_landing', NULL, TRUE),
      'dollar_amount' => false,
      'count' => false,
      'active_class' => (in_array('contracts_landing', $currentPageUrlParts) && RequestUtilities::get("bottom_slider") == "sub_vendor")?' active':"",
    );

    return [
      'bottom_navigation' => $bottom_navigation_render,
    ];
  }
}
