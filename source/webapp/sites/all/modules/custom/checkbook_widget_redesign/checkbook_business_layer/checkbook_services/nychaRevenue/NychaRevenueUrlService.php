<?php


class NychaRevenueUrlService {

  /**
   * Returns Footer URL for widget
   * @param null $parameters
   * @return string
   */
  public static function getFooterUrl($parameters = null) {
      $url = "/panel_html/nycha_revenue_transactions/nycha_revenue/transactions"
        . RequestUtilities::buildUrlFromParam('year')
        . RequestUtilities::buildUrlFromParam('expcategory')
        . RequestUtilities::buildUrlFromParam('project')
        . RequestUtilities::buildUrlFromParam('program')
        . RequestUtilities::buildUrlFromParam('fundsrc')
        . RequestUtilities::buildUrlFromParam('respcenter')
        . RequestUtilities::buildUrlFromParam('datasource');
      return $url;
    }

  /**
   * Returns NYCHA Revenue Landing page URL
   * @param $urlParamName
   * @param $urlParamValue
   * @param $yearId parameter
   * @return string
   */
  public static function generateLandingPageUrl($urlParamName, $urlParamValue, $yearId = null)
  {
    $yearId = (isset($yearId)) ? $yearId : RequestUtilities::getRequestParamValue('year');
    $yearURL = '/year/'. ((isset($yearId)) ? $yearId : CheckbookDateUtil::getCurrentFiscalYearId(Datasource::NYCHA));
    $url = '/nycha_revenue'
      . $yearURL
      . RequestUtilities::buildUrlFromParam('expcategory')
      . RequestUtilities::buildUrlFromParam('project')
      . RequestUtilities::buildUrlFromParam('program')
      . RequestUtilities::buildUrlFromParam('fundsrc')
      . RequestUtilities::buildUrlFromParam('respcenter')
      . RequestUtilities::buildUrlFromParam('datasource')
      . '/'.$urlParamName.'/'. $urlParamValue;

    return $url;
  }

  /**
   * Returns NYCHA Revenue recognized link URL
   * @param $urlParamName
   * @param $urlParamValue
   * @param $yearId parameter
   * @return string
   */
  public static function recRevenueUrl($dynamic_parameter, $widget) {
    $dynamic_parameter = isset($dynamic_parameter) ? $dynamic_parameter : '';
    $url = "/panel_html/nycha_revenue_transactions/nycha_revenue/transactions"
      . RequestUtilities::buildUrlFromParam('year')
      . RequestUtilities::buildUrlFromParam('expcategory')
      . RequestUtilities::buildUrlFromParam('project')
      . RequestUtilities::buildUrlFromParam('program')
      . RequestUtilities::buildUrlFromParam('fundsrc')
      . RequestUtilities::buildUrlFromParam('respcenter')
      . RequestUtilities::buildUrlFromParam('datasource')
      . '/widget/'. $widget
      . $dynamic_parameter;

    return $url;
  }
}
