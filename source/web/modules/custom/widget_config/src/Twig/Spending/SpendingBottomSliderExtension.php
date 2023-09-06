<?php
namespace Drupal\widget_config\Twig\Spending;

use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\SpendingUtilities\SpendingUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SpendingBottomSliderExtension extends AbstractExtension
{
  /**
   * @return TwigFunction[]
   */
  public function getFunctions()
  {
    return [
      'generateSpendingBottomSlider' => new TwigFunction('generateSpendingBottomSlider', [
         $this,
        'generateSpendingBottomSlider',
      ]),
    ];
  }

  /**
   * @param $node
   * @return array[]
   */
  public static function generateSpendingBottomSlider($node){
    $category_names = SpendingUtil::$spendingCategories;
    $total_spending = 0;
    //Calculate total spending amount
    if(isset($node->data)) {
      foreach ($node->data as $row) {
        $categories[$row['category_category']] = array('amount' => $row['check_amount_sum']);
        $total_spending += $row['check_amount_sum'];
      }
    }
    $categories[0]['amount'] = $total_spending;
    $bottom_navigation_render = [];
    foreach ($category_names as $id => $name) {

      $active_class = "";
      if (RequestUtilities::get("category") == $id || (RequestUtilities::get("category") == "" && $id == 0)) {
        $active_class = ' active';
      }
      $link = ($categories[$id]['amount'] > 0) ? SpendingUtil::prepareSpendingBottomNavFilter("spending_landing", (($id == 0) ? null : $id)) : false;
      $amount = FormattingUtilities::custom_number_formatter_format($categories[$id]['amount'], 1, '$');
      $category_name = $name;

      $bottom_navigation_render[$category_name] = array(
        'label' => $category_name,
        'dollar_amount' => $amount,
        'link' => $link,
        'active_class' => $active_class
      );
    }
    return [
      'bottom_navigation' => $bottom_navigation_render,
    ];
  }
}
