<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Drupal\checkbook_services\NychaBudget;

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;

class NychaBudgetUrlService {
  /**
   * Returns Footer URL for widget
   * @param null $parameters
   * @return string
   */
    public static function getFooterUrl($parameters = null) {
      $url = "/nycha_budget/transactions"
        . RequestUtilities::buildUrlFromParam('year')
        . RequestUtilities::buildUrlFromParam('datasource')
        . RequestUtilities::buildUrlFromParam('expcategory')
        . RequestUtilities::buildUrlFromParam('respcenter')
        . RequestUtilities::buildUrlFromParam('fundsrc')
        . RequestUtilities::buildUrlFromParam('program')
        . RequestUtilities::buildUrlFromParam('project');
      return $url;
    }

  /**
   * Function to build the footer url for the budget widgets
   * @param $footerUrl
   * @param $widget
   * @return string
   */
  public static function getPercentDiffFooterUrl($footerUrl, $widget = null){
    $url = null;
    if(isset($widget)) {
      switch ($widget) {
        case "exp_details":
          $url = "/nycha_budget_percent_difference_details/nycha_budget/details/budgettype/percdiff/widget/exp_details";
          break;
        case "resp_details":
          $url = "/nycha_budget/respcenter_details/budgettype/percdiff/widget/resp_details";
          break;
        case "prgm_details":
          $url = "/nycha_budget/program_details/budgettype/percdiff/widget/prgm_details";
          break;
        case "fund_details":
          $url = "/nycha_budget/fundsrc_details/budgettype/percdiff/widget/fund_details";
          break;
        case "proj_details":
          $url = "/nycha_budget/project_details/budgettype/percdiff/widget/proj_details";
          break;
      }
    }
    if(isset($url)){
      return str_replace("/nycha_budget/transactions", $url, $footerUrl);
    }else{
      return $footerUrl;
    }

  }

  /**
  * Gets the Committed budget link in a generic way
  * @param $dynamic_parameter - custom dynamic parameters to be used in the URL
  * @param  $widget
  * @param $budgetype
  * @return string
  */
  public static function committedBudgetUrl($dynamic_parameter, $widget, $budgetype) {
    $dynamic_parameter = isset($dynamic_parameter) ? $dynamic_parameter : '';
    $url = "/nycha_budget/transactions"
      . RequestUtilities::buildUrlFromParam('year')
      . RequestUtilities::buildUrlFromParam('datasource')
      . RequestUtilities::buildUrlFromParam('expcategory')
      . RequestUtilities::buildUrlFromParam('respcenter')
      . RequestUtilities::buildUrlFromParam('fundsrc')
      . RequestUtilities::buildUrlFromParam('program')
      . RequestUtilities::buildUrlFromParam('project')
      . '/widget/'. $widget
      . '/budgettype/'.$budgetype
      . $dynamic_parameter;

    return $url;
  }


  /**
   * Returns NYCHA Budget Landing page URL for the given Parameter
   * @param $urlParamName
   * @param $urlParamValue
   * @return string
   */
  public static function generateLandingPageUrl($urlParamName, $urlParamValue)
  {
    $url = '/nycha_budget'
      .RequestUtilities::buildUrlFromParam('year')
      .RequestUtilities::buildUrlFromParam('datasource')
      .RequestUtilities::buildUrlFromParam('agency')
      .RequestUtilities::buildUrlFromParam('respcenter')
      .RequestUtilities::buildUrlFromParam('fundsrc')
      .RequestUtilities::buildUrlFromParam('expcategory')
      .RequestUtilities::buildUrlFromParam('program')
      .RequestUtilities::buildUrlFromParam('project')
      . '/'.$urlParamName.'/'. $urlParamValue;
    return $url;
  }

}
