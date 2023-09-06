<?php
namespace Drupal\checkbook_project\WidgetUtilities;

use Drupal\checkbook_infrastructure_layer\Constants\Common\CheckbookDomain;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\RequestUtil;
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
    $url = RequestUtilities::getRefUrl() ?? RequestUtilities::getCurrentPageUrl();
    $status =RequestUtilities::get('status');
    if (strtolower($domain) == CheckbookDomain::$CONTRACTS) {
      switch ($url) {
        case(RequestUtil::isExpenseContractPath($url)):
          if ($status == "A") {
          $title = 'Active Expense Contracts';
          }
        elseif ($status == "R") {
        $title = 'Registered Expense Contracts';
        }
          break;
        case(RequestUtil::isRevenueContractPath($url)):
          if ($status == "A") {
          $title = 'Active Revenue Contracts';
        } else if ($status == "R") {
          $title = 'Registered Revenue Contracts';
        }
          break;
        case(RequestUtil::isPendingRevenueContractPath($url)):
        $title = 'Pending Revenue Contracts';
        break;
        case(RequestUtil::isPendingExpenseContractPath($url)):
        $title = 'Pending Expense Contracts';
        break;
        default:
          break;
      }
    }
    if ($chart == 'contracts') {
      return 'Top Ten ' . $title . ' by Current Amount';
    } else {
      return isset($by) ? $subTitle . ' by ' . $title : $title . ' ' . $subTitle;
    }
  }

}
