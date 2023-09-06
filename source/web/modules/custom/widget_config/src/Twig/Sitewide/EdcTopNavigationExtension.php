<?php
namespace Drupal\widget_config\Twig\Sitewide;

use Drupal\checkbook_infrastructure_layer\Constants\Common\CheckbookDomain;
use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Drupal\checkbook_project\CommonUtilities\RequestUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EdcTopNavigationExtension extends AbstractExtension
{
  /**
   * @return TwigFunction[]
   */
  public function getFunctions()
  {
    return [
      'generateEdcTopNavigation' => new TwigFunction('generateEdcTopNavigation', [
        $this,
        'generateEdcTopNavigation',
      ]),
      'isEdcTopNavigationItemActive' => new TwigFunction('isEdcTopNavigationItemActive', [
        $this, 'isEdcTopNavigationItemActive'
      ])
    ];
  }

  /**
   * @param $domain
   * @return bool
   */
  public function isEdcTopNavigationItemActive($domain): bool
  {
    $current_domain = CheckbookDomain::getCurrent();
    return $current_domain == $domain;
  }

  /**
   * @param $node
   * @return array
   */
  public function generateEdcTopNavigation($node)
  {
    $contract_amount = $node->data[0]['current_amount_sum'];
    $spending_amount = $node->data[1]['check_amount_sum'];

    $spending_link = $contracts_link = $budget_link = $revenue_link = $payroll_link = $mwbe_link = $subvendors_link = array(
      'link' => false,
      'dollar_amount' => FormattingUtilities::custom_number_formatter_format(0 ,1,'$')
    );

    //Spending
    if($spending_amount  > 0){
      $spending_link = array(
        'link' => RequestUtil::getTopNavURL("spending"),
        'dollar_amount' => FormattingUtilities::custom_number_formatter_format($spending_amount ,1,'$')
      );
    }

    //Contracts
    if ($contract_amount > 0) {
      $contracts_link = array(
        'link' => RequestUtil::getTopNavURL("contracts"),
        'dollar_amount' => FormattingUtilities::custom_number_formatter_format($contract_amount, 1, '$')
      );
    }

    $top_navigation_render_first = [
      'budget' => array(
        'label' => 'Budget',
        'dollar_amount' => $budget_link['dollar_amount'],
        'link' => $budget_link['link'],
      ),
      'revenue' => array(
        'label' => 'Revenue',
        'dollar_amount' => $revenue_link['dollar_amount'],
        'link' => $revenue_link['link'],
      ),
      'spending' => array(
        'label' => 'Spending',
        'dollar_amount' => $spending_link['dollar_amount'],
        'link' => $spending_link['link'],
      ),
      'contracts' => array(
        'label' => 'Contracts',
        'dollar_amount' => $contracts_link['dollar_amount'],
        'link' => $contracts_link['link'],
      ),
      'payroll' => array(
        'label' => 'Payroll',
        'dollar_amount' => $payroll_link['dollar_amount'],
        'link' => $payroll_link['link'],
      )
    ];

    $top_navigation_render_second = [
      'mwbe' => [
        'label' => RequestUtil::getDashboardTopNavTitle("mwbe"),
        'dollar_amount' => $mwbe_link['dollar_amount'],
        'link' => $mwbe_link['link'],
        'active' => "",
        'menu' => false,
        'indicator' => false
      ],
      'sub_vendors' => [
        'label' => RequestUtil::getDashboardTopNavTitle("subvendor"),
        'dollar_amount' => $subvendors_link['dollar_amount'],
        'link' => $subvendors_link['link'],
        'active' => "",
        'menu' => false,
        'indicator' => false
      ]
    ];

    return [
      'page_type' => (CheckbookDomain::getCurrent() == CheckbookDomain::$CONTRACTS) ? 'Contracts' : 'Spending',
      'first' => $top_navigation_render_first,
      'second' => $top_navigation_render_second
    ];
  }
}
