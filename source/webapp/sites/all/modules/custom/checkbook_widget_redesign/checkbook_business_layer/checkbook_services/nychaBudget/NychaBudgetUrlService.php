<?php


class NychaBudgetUrlService {

    static function getFooterUrl($parameters = null) {
        // = '/panel_html/nycha_budget_transactions/nycha_budget/transactions'
          //  .RequestUtilities::buildUrlFromParam('year')
            //.RequestUtilities::buildUrlFromParam('expcategory');
        //return $url;

      $url = "/panel_html/nycha_budget_transactions/nycha_budget/transactions"
        . RequestUtilities::buildUrlFromParam('year')
        . RequestUtilities::buildUrlFromParam('fundsrc')
        . RequestUtilities::buildUrlFromParam('datasource');
      return $url;
    }

  public static function expenseCategoryURL($expenditure_type_code){
    $url =   "/nycha_budget"
      .RequestUtilities::buildUrlFromParam('year')
      .RequestUtilities::buildUrlFromParam('respcenter')
      .RequestUtilities::buildUrlFromParam('fundsrc')
      .RequestUtilities::buildUrlFromParam('program')
      .RequestUtilities::buildUrlFromParam('project')
      .'/expcategory/'.$expenditure_type_code;
    return $url;
  }

  public static function responsibilityCenterURL($responsibilty_center_code){
    $url =   "/nycha_budget"
      .RequestUtilities::buildUrlFromParam('year')
      .RequestUtilities::buildUrlFromParam('fundsrc')
      .RequestUtilities::buildUrlFromParam('program')
      .RequestUtilities::buildUrlFromParam('expcategory')
      .RequestUtilities::buildUrlFromParam('project')
      .'/respcenter/'.$responsibilty_center_code;
    return $url;
  }

  public static function fundingSourceURL($funding_source_code){
    $url =   "/nycha_budget"
      .RequestUtilities::buildUrlFromParam('year')
      .RequestUtilities::buildUrlFromParam('respcenter')
      .RequestUtilities::buildUrlFromParam('program')
      .RequestUtilities::buildUrlFromParam('expcategory')
      .RequestUtilities::buildUrlFromParam('project')
      .'/fundsrc/'.$funding_source_code;
    return $url;
  }

  public static function programNameLink($program_phase_code){
    $url =   "/nycha_budget"
      .RequestUtilities::buildUrlFromParam('year')
      .RequestUtilities::buildUrlFromParam('respcenter')
      .RequestUtilities::buildUrlFromParam('fundsrc')
      .RequestUtilities::buildUrlFromParam('expcategory')
      .RequestUtilities::buildUrlFromParam('project')
      .'/program/'.$program_phase_code;
    return $url;
  }

  public static function projectNameLink($gl_project_code){
    $url =   "/nycha_budget"
      .RequestUtilities::buildUrlFromParam('year')
      .RequestUtilities::buildUrlFromParam('respcenter')
      .RequestUtilities::buildUrlFromParam('fundsrc')
      .RequestUtilities::buildUrlFromParam('expcategory')
      .RequestUtilities::buildUrlFromParam('program')
      .'/project/'.$gl_project_code;
    return $url;
  }
}
