<?php

namespace Drupal\widget_config\Twig\Contracts;

use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\NychaContractUtilities\NYCHAContractUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NychaContractsConfigExtension extends AbstractExtension
{
  public function getFunctions()
  {
    return [
      'nychaContractsSummary' => new TwigFunction('nychaContractsSummary', [
        $this,
        'nychaContractsSummary',
      ]),
      'nychaContractsTotalAmount' => new TwigFunction('nychaContractsTotalAmount', [
        $this,
        'nychaContractsTotalAmount',
      ])
    ];
  }

  public function nychaContractsSummary()
  {
    $agreement_type = RequestUtilities::_getRequestParamValueBottomURL('agreement_type');
    $tcode = RequestUtilities::_getRequestParamValueBottomURL('tCode');
    $summaryTitle = '';
    global $checkbook_breadcrumb_title;

    $summaryTitle = NYCHAContractUtil::getTitleByCode($tcode);
    if(empty($summaryTitle)){
      $summaryTitle = 'NYCHA';
    }

    $summary = "<h2 class='contract-title' class='title'>{$summaryTitle} Contracts Transactions</h2>";
    $checkbook_breadcrumb_title =  "$summaryTitle Contracts Transactions";

    return $summary;
  }

  public function nychaContractsTotalAmount($node)
  {
    //$http_ref = $_SERVER['HTTP_REFERER'];
    $http_ref = \Drupal::service('path.current')->getPath();
    $current_url =  \Drupal::request()->query->get('q');;

   //Advanced Search page should not have static text
    $advanced_search_page = preg_match("/nycha_contract\/search\/transactions/",$current_url);
    $advanced_search_page = $advanced_search_page || preg_match("/nycha_contract\/all\/transactions/",$current_url);
    $advanced_search_page = $advanced_search_page || preg_match("/nycha_contract\/search\/transactions/",$http_ref);
    $advanced_search_page = $advanced_search_page || preg_match("/nycha_contract\/all\/transactions/",$http_ref);
    if($advanced_search_page) return;

    $amount =  '<div class="transactions-total-amount">$'
      . FormattingUtilities::custom_number_formatter_format($node->data[0]['total_current_amount'],2)
      .'<div class="amount-title">Total Current Contract Amount</div></div>';

    return $amount;
  }
}
