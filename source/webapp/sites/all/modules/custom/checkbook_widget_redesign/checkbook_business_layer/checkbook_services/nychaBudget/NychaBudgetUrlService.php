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

  /* Gets the Committed budget link in a generic way
  * @param $dynamic_parameter - custom dynamic parameters to be used in the URL
  * @param null $legacy_node_id
  * @return string
  */
  static function committedBudgetUrl($dynamic_parameter, $widget,$budgetype) {
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

  public static function expenseCategoryURL($expenditure_type_type){
    $url =   "/nycha_budget"
      .RequestUtilities::buildUrlFromParam('year')
      .RequestUtilities::buildUrlFromParam('datasource')
      .RequestUtilities::buildUrlFromParam('respcenter')
      .RequestUtilities::buildUrlFromParam('fundsrc')
      .RequestUtilities::buildUrlFromParam('program')
      .RequestUtilities::buildUrlFromParam('project')
      .'/expcategory/'.$expenditure_type_type;
    return $url;
  }

  public static function responsibilityCenterURL($responsibilty_center_type){
    $url =   "/nycha_budget"
      .RequestUtilities::buildUrlFromParam('year')
      .RequestUtilities::buildUrlFromParam('datasource')
      .RequestUtilities::buildUrlFromParam('fundsrc')
      .RequestUtilities::buildUrlFromParam('program')
      .RequestUtilities::buildUrlFromParam('expcategory')
      .RequestUtilities::buildUrlFromParam('project')
      .'/respcenter/'.$responsibilty_center_type;
    return $url;
  }

  public static function fundingSourceURL($funding_source_type){
    $url =   "/nycha_budget"
      .RequestUtilities::buildUrlFromParam('year')
      .RequestUtilities::buildUrlFromParam('datasource')
      .RequestUtilities::buildUrlFromParam('respcenter')
      .RequestUtilities::buildUrlFromParam('program')
      .RequestUtilities::buildUrlFromParam('expcategory')
      .RequestUtilities::buildUrlFromParam('project')
      .'/fundsrc/'.$funding_source_type;
    return $url;
  }

  public static function programNameLink($program_phase_type){
    $url =   "/nycha_budget"
      .RequestUtilities::buildUrlFromParam('year')
      .RequestUtilities::buildUrlFromParam('datasource')
      .RequestUtilities::buildUrlFromParam('respcenter')
      .RequestUtilities::buildUrlFromParam('fundsrc')
      .RequestUtilities::buildUrlFromParam('expcategory')
      .RequestUtilities::buildUrlFromParam('project')
      .'/program/'.$program_phase_type;
    return $url;
  }

  public static function projectNameLink($gl_project_type){
    $url =   "/nycha_budget"
      .RequestUtilities::buildUrlFromParam('year')
      .RequestUtilities::buildUrlFromParam('datasource')
      .RequestUtilities::buildUrlFromParam('respcenter')
      .RequestUtilities::buildUrlFromParam('fundsrc')
      .RequestUtilities::buildUrlFromParam('expcategory')
      .RequestUtilities::buildUrlFromParam('program')
      .'/project/'.$gl_project_type;
    return $url;
  }
}
