<?php


class NychaBudgetUrlService {

    static function getFooterUrl($parameters = null) {
      $url = "/panel_html/nycha_budget_transactions/nycha_budget/transactions"
        . RequestUtilities::buildUrlFromParam('year')
        . RequestUtilities::buildUrlFromParam('datasource')
        . RequestUtilities::buildUrlFromParam('respcenter')
        . RequestUtilities::buildUrlFromParam('fundsrc')
        . RequestUtilities::buildUrlFromParam('program')
        . RequestUtilities::buildUrlFromParam('expcategory')
        . RequestUtilities::buildUrlFromParam('project');
      return $url;
    }

  public static function expenseCategoryURL($expenditure_type_id){
    $url =   "/nycha_budget"
      .RequestUtilities::buildUrlFromParam('year')
      .RequestUtilities::buildUrlFromParam('datasource')
      .RequestUtilities::buildUrlFromParam('respcenter')
      .RequestUtilities::buildUrlFromParam('fundsrc')
      .RequestUtilities::buildUrlFromParam('program')
      .RequestUtilities::buildUrlFromParam('project')
      .'/expcategory/'.$expenditure_type_id;
    return $url;
  }

  public static function responsibilityCenterURL($responsibilty_center_id){
    $url =   "/nycha_budget"
      .RequestUtilities::buildUrlFromParam('year')
      .RequestUtilities::buildUrlFromParam('datasource')
      .RequestUtilities::buildUrlFromParam('fundsrc')
      .RequestUtilities::buildUrlFromParam('program')
      .RequestUtilities::buildUrlFromParam('expcategory')
      .RequestUtilities::buildUrlFromParam('project')
      .'/respcenter/'.$responsibilty_center_id;
    return $url;
  }

  public static function fundingSourceURL($funding_source_id){
    $url =   "/nycha_budget"
      .RequestUtilities::buildUrlFromParam('year')
      .RequestUtilities::buildUrlFromParam('datasource')
      .RequestUtilities::buildUrlFromParam('respcenter')
      .RequestUtilities::buildUrlFromParam('program')
      .RequestUtilities::buildUrlFromParam('expcategory')
      .RequestUtilities::buildUrlFromParam('project')
      .'/fundsrc/'.$funding_source_id;
    return $url;
  }

  public static function programNameLink($program_phase_id){
    $url =   "/nycha_budget"
      .RequestUtilities::buildUrlFromParam('year')
      .RequestUtilities::buildUrlFromParam('datasource')
      .RequestUtilities::buildUrlFromParam('respcenter')
      .RequestUtilities::buildUrlFromParam('fundsrc')
      .RequestUtilities::buildUrlFromParam('expcategory')
      .RequestUtilities::buildUrlFromParam('project')
      .'/program/'.$program_phase_id;
    return $url;
  }

  public static function projectNameLink($gl_project_id){
    $url =   "/nycha_budget"
      .RequestUtilities::buildUrlFromParam('year')
      .RequestUtilities::buildUrlFromParam('datasource')
      .RequestUtilities::buildUrlFromParam('respcenter')
      .RequestUtilities::buildUrlFromParam('fundsrc')
      .RequestUtilities::buildUrlFromParam('expcategory')
      .RequestUtilities::buildUrlFromParam('program')
      .'/project/'.$gl_project_id;
    return $url;
  }
}
