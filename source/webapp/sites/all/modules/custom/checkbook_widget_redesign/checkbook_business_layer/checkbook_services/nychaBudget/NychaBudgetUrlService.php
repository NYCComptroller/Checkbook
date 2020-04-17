<?php


class NychaBudgetUrlService {

    static function getFooterUrl($parameters,$legacy_node_id = null) {
        $legacy_node_id = isset($legacy_node_id) ? '/dtsmnid/'.$legacy_node_id : '';
        $url = '/panel_html/nycha_budget_transactions/nycha_budget/transactions'
            . $legacy_node_id
            .RequestUtilities::buildUrlFromParam('agency')
            .RequestUtilities::buildUrlFromParam('dept')
            .RequestUtilities::buildUrlFromParam('expcategory')
            . _checkbook_project_get_year_url_param_string();

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
