<?php

namespace Drupal\widget_config\Twig\Budget;

use Drupal\checkbook_custom_breadcrumbs\BudgetBreadcrumbs;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\BudgetUtilities\NychaBudgetUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NychaBudgetConfigExtension extends AbstractExtension
{
  public function getFunctions()
  {
    return [
      'nychaBudgetGridviewTitle' => new TwigFunction('nychaBudgetGridviewTitle', [
        $this,
        'nychaBudgetGridviewTitle',
      ]),
      'nychaBudgetSummaryTitle' => new TwigFunction('nychaBudgetSummaryTitle', [
        $this,
        'nychaBudgetSummaryTitle',
      ])
    ];
  }

  public function nychaBudgetGridviewTitle()
  {
    return BudgetBreadcrumbs::getNychaBudgetPageTitle();
  }

  public function nychaBudgetSummaryTitle()
  {
    //Transactions Details Page main title
    $title = NychaBudgetUtil::getTransactionsTitle();

    //Transactions Page sub title
    $url = RequestUtilities::getBottomContUrl();
    $widget = RequestUtilities::_getRequestParamValueBottomURL('widget');

    if((str_contains($widget, 'comm_')) || ($widget == 'wt_year')) {
      $subTitle = NychaBudgetUtil::getTransactionsSubTitle($widget, $url);
    }
    $subTitle = $subTitle ?? ' ';

    return "<div class='contract-details-heading'>
                  <div class='contract-id'>
                    <h2 class='contract-title'>{$title}</h2></br>
                    {$subTitle}</div>";

  }
}
