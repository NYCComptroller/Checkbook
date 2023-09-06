<?php

namespace Drupal\widget_config\Twig\Budget;

use Drupal\checkbook_custom_breadcrumbs\BudgetBreadcrumbs;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BudgetConfigExtension extends AbstractExtension
{
  public function getFunctions()
  {
    return [
      'budgetGridViewTitle' => new TwigFunction('budgetGridViewTitle', [
        $this,
        'budgetGridViewTitle',
      ])
    ];
  }

  public function budgetGridViewTitle()
  {
    if(RequestUtilities::get('datasource', ['q' => RequestUtilities::getRefUrl()]) == Datasource::NYCHA) {
      return BudgetBreadcrumbs::getNychaBudgetPageTitle();
    }else{
      return BudgetBreadcrumbs::getBudgetPageTitle();
    }
  }
}
