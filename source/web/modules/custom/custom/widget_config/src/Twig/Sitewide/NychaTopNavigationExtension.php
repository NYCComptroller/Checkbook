<?php
namespace Drupal\widget_config\Twig\Sitewide;

use Drupal\checkbook_infrastructure_layer\Constants\Common\CheckbookDomain;
use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Drupal\checkbook_project\CommonUtilities\RequestUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NychaTopNavigationExtension extends AbstractExtension
{
  /**
   * @return TwigFunction[]
   */
  public function getFunctions()
  {
    return [
      'generateNychaTopNavigation' => new TwigFunction('generateNychaTopNavigation', [
        $this,
        'generateNychaTopNavigation',
      ]),
      'isNychaTopNavigationItemActive' => new TwigFunction('isNychaTopNavigationItemActive', [
        $this, 'isNychaTopNavigationItemActive'
      ])
    ];
  }

  /**
   * @param $domain
   * @return bool
   */
  public function isNychaTopNavigationItemActive($domain): bool
  {
    $current_domain = CheckbookDomain::getCurrent();
    return $current_domain == $domain;
  }

  /**
   * @param $node
   * @return \array[][]
   */
  public function generateNychaTopNavigation($node)
  {
    $budget_link = $revenue_link = $spending_link = $contracts_link = $payroll_link = array(
      'link' => false,
      'dollar_amount' => FormattingUtilities::custom_number_formatter_format(0 ,1,'$')
    );


    //Payroll Link
    if ($node->data[0]['total_gross_pay'] > 0) {
      $payroll_link['dollar_amount'] = FormattingUtilities::custom_number_formatter_format($node->data[0]['total_gross_pay'], 1, '$');
      $payroll_link['link'] = RequestUtil::getTopNavURL("payroll");
    }

    //Contracts Link
    if ($node->data[1]['total_maximum_contract_amount'] > 0) {
      $contracts_link['dollar_amount'] = FormattingUtilities::custom_number_formatter_format($node->data[1]['total_maximum_contract_amount'], 1, '$');
      $contracts_link['link'] = RequestUtil::getTopNavURL("nycha_contracts");
    }

    //Budget Link
    if ($node->data[2]['budget_adopted_amount'] > 0) {
      $budget_link['dollar_amount'] = FormattingUtilities::custom_number_formatter_format($node->data[2]['budget_adopted_amount'], 1, '$');
      $budget_link['link'] = RequestUtil::getTopNavURL("nycha_budget");
    }

    //Revenue Link
    if ($node->data[3]['revenue_recognized_amount'] > 0) {
      $revenue_link['dollar_amount']  = FormattingUtilities::custom_number_formatter_format($node->data[3]['revenue_recognized_amount'], 1, '$');
      $revenue_link['link'] = RequestUtil::getTopNavURL("nycha_revenue");
    }

    //Spending Link
    $total_spending = 0;
    foreach ($node->data as $key => $row) {
      $row['invoice_amount_sum'] = (isset($row['category_name_category_name']) && $row['category_name_category_name'] == 'Payroll') ? $row['check_amount_sum'] : ($row['invoice_amount_sum'] ?? null);
      $total_spending += $row['invoice_amount_sum'] ;
    }
    if ($total_spending > 0) {
      $spending_link['dollar_amount'] = FormattingUtilities::custom_number_formatter_format($total_spending, 1, '$');
      $spending_link['link'] = RequestUtil::getTopNavURL("nycha_spending");
    }

    $top_navigation_render_first = [
      'nycha_budget' => array(
        'label' => 'Budget',
        'dollar_amount' => $budget_link['dollar_amount'],
        'link' => $budget_link['link'],
        'domain' => 'nycha_budget',
      ),
      'nycha_revenue' => array(
        'label' => 'Revenue',
        'dollar_amount' => $revenue_link['dollar_amount'],
        'link' => $revenue_link['link'],
        'domain' => 'nycha_revenue',
      ),
      'nycha_spending' => array(
        'label' => 'Spending',
        'dollar_amount' => $spending_link['dollar_amount'],
        'link' => $spending_link['link'],
        'domain' => 'nycha_spending',
      ),
      'nycha_contracts' => array(
        'label' => 'Contracts',
        'dollar_amount' => $contracts_link['dollar_amount'],
        'link' => $contracts_link['link'],
        'domain' => 'nycha_contracts',
      ),
      'payroll' => array(
        'label' => 'Payroll',
        'dollar_amount' => $payroll_link['dollar_amount'],
        'link' => $payroll_link['link'],
        'domain' => 'payroll',
      )
    ];
    return [
      'first' => $top_navigation_render_first,
    ];
  }
}
