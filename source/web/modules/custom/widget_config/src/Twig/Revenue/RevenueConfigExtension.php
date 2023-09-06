<?php

namespace Drupal\widget_config\Twig\Revenue;

use Drupal\checkbook_custom_breadcrumbs\RevenueBreacrumbs;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\checkbook_project\RevenueUtilities\NychaRevenueUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RevenueConfigExtension extends AbstractExtension
{
  public function getFunctions()
  {
    return [
      'revenueTitle' => new TwigFunction('revenueTitle', [
        $this,
        'revenueTitle',
      ]),
      'revenueYearTitle' => new TwigFunction('revenueYearTitle', [
        $this,
        'revenueYearTitle',
      ]),
      'revenueNychaTitle' => new TwigFunction('revenueNychaTitle', [
        $this,
        'revenueNychaTitle',
      ]),
      'revenueNychaGridTitle' => new TwigFunction('revenueNychaGridTitle', [
        $this,
        'revenueNychaGridTitle',
      ]),
    ];
  }

  // Revenue Title
  public function revenueTitle(): ?string
  {
    $title = 'New York City';
    $revcat = RequestUtilities::get('revcat');
    $fundsrccode = RequestUtilities::get('fundsrccode');
    $agency = RequestUtilities::get('agency');

    if (!empty($revcat)) {
      $title = _checkbook_project_get_name_for_argument('revenue_category_id', $revcat);
    } else if (!empty($fundsrccode)) {
      $title = _checkbook_project_get_name_for_argument('funding_class_code', $fundsrccode);
    } else if (!empty($agency)) {
      $title = _checkbook_project_get_name_for_argument('agency_id', $agency);
    }
    return $title;
  }

  public function revenueYearTitle(): string
  {
    $output = '';
    $urlPath = \Drupal::request()->query->get('q');
    $pathParams = explode('/', $urlPath);
    $yrIndex = array_search("year",$pathParams);
    $revcatIndex = array_search("revcat",$pathParams);
    $fundsrcIndex = array_search("fundsrccode",$pathParams);
    $agencyIndex = array_search("agency",$pathParams);

    if(!$revcatIndex && !$fundsrcIndex && !$agencyIndex){
      $output .= '<h2>'. CheckbookDateUtil::_getYearValueFromID($pathParams[$yrIndex+1]) .' NYC Revenue</h2>';
    }
    return $output;
  }

  // Nycha Revenue Title
  public function revenueNychaTitle()
  {
    $title = NychaRevenueUtil::getTransactionsTitle();
    //Transactions Page sub title
    $url = RequestUtilities::getBottomContUrl();
    $widget = RequestUtilities::_getRequestParamValueBottomURL('widget');

    if((str_contains($widget, 'rec_')) || ($widget == 'wt_year')) {
      $subTitle = NychaRevenueUtil::getTransactionsSubTitle($widget, $url);
    }

    $subTitle = $subTitle ?? ' ';

    return "<div class='contract-details-heading'>
                  <div class='contract-id'>
                    <h2 class='contract-title'>{$title}</h2></br>
                    {$subTitle}</div>";
  }
  // Nycha Revenue Title
  public function revenueNychaGridTitle(): ?string
  {
    return RevenueBreacrumbs::getNYCHARevenuePageTitle();
  }
}
