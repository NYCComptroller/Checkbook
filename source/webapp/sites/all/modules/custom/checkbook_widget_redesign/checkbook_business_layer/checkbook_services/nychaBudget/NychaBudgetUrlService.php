<?php


class NychaBudgetUrlService {

    static function getFooterUrl($parameters = null) {
      $url = "/panel_html/nycha_budget_transactions/nycha_budget/transactions"
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
  static function getPercentDiffFooterUrl($footerUrl, $widget){
    $url = null;
    switch($widget){
      case "exp_details":
        $url = "/panel_html/nycha_budget_percent_difference_details/nycha_budget/details/budgettype/percdiff/widget/exp_details";
        break;
      case "resp_details":
        $url = "/panel_html/nycha_budget_respcenter_details/nycha_budget/respcenter_details/budgettype/percdiff/widget/resp_details";
        break;
      case "prgm_details":
        $url = "/panel_html/nycha_budget_program_details/nycha_budget/program_details/budgettype/percdiff/widget/prgm_details";
        break;
      case "fund_details":
        $url = "/panel_html/nycha_budget_fundsrc_details/nycha_budget/fundsrc_details/budgettype/percdiff/widget/fund_details";
        break;
      case "proj_details":
        $url = "/panel_html/nycha_budget_project_details/nycha_budget/project_details/budgettype/percdiff/widget/proj_details";
        break;
    }
    if(isset($url)){
      return str_replace("/panel_html/nycha_budget_transactions/nycha_budget/transactions", $url, $footerUrl);
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
  static function committedBudgetUrl($dynamic_parameter, $widget, $budgetype) {
    $dynamic_parameter = isset($dynamic_parameter) ? $dynamic_parameter : '';
    $url = "/panel_html/nycha_budget_transactions/nycha_budget/transactions"
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
  static function generateLandingPageUrl($urlParamName, $urlParamValue)
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
