<?php
namespace Drupal\checkbook_project\WidgetUtilities;

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\Component\Utility\Html;

class ChartUtil
{
    static function generateGridViewLink($node){
        if(empty($node->data) || (isset($node->disableGridViewLink) && $node->disableGridViewLink)) {
            return '<a class="chart-grid-view no-data" style="display:none" oncontextmenu="return false;">Grid View</a>';
        }
        return '<a class="chart-grid-view gridpopup" style="display:none"
                href="/gridview/popup/widget/' . $node->nid . '?refURL='. Html::escape(\Drupal::service('path.current')->getPath()) .'">Grid View</a>';
    }

    static function generateWidgetGridViewLink($node){
        if(empty($node->data) || isset($node->disableGridViewLink)) {
            return '<a class="chart-grid-view no-data" style="display:none" oncontextmenu="return false;">Grid View</a>';
        }
    	return '<a class="chart-grid-view gridpopup" style="display:none"
                href="/gridview/popup/widget/' . $node->nid . '?refURL='. Html::escape(\Drupal::service('path.current')->getPath()) .'">Grid View</a>';
    }

  /**
   * @param $domain
   * @param $subTitle
   * @param null $by
   * @param null $chart
   *
   * @return string
   */
  public static function _checkbook_project_getChartTitle($domain, $subTitle, $by = null, $chart = null){
    $title = NULL;
    // need url and refurl to get the correct title text
    $refUrl = \Drupal::request()->query->get('refURL');
    $url = $refUrl ?? RequestUtilities::getCurrentPageUrl();
    $status =RequestUtilities::get('status');
    if (strtolower($domain) == "contracts") {
      if (str_starts_with($url, "/contracts_landing")) {
        if ($status == "A") {
          $title = 'Active Expense Contracts';
        } else if ($status == "R") {
          $title = 'Registered Expense Contracts';
        }
      } else if (str_starts_with($url, "/contracts_revenue_landing")) {
        if ($status == "A") {
          $title = 'Active Revenue Contracts';
        } else if ($status == "R") {
          $title = 'Registered Revenue Contracts';
        }
      } else if (str_contains($url, "/contracts_pending_rev_landing")) {
        $title = 'Pending Revenue Contracts';
      } else if (str_contains($url, "/contracts_pending_exp_landing")) {
        $title = 'Pending Expense Contracts';
      }
    }
    if ($chart == 'contracts') {
      return 'Top Ten ' . $title . ' by Current Amount';
    } else {
      if (!$by)
        return $title . ' ' . $subTitle;
      else
        return $subTitle . ' by ' . $title;
    }
  }

}
