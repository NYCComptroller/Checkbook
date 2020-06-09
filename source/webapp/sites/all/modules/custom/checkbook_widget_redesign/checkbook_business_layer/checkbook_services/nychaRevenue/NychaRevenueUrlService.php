<?php


class NychaRevenueUrlService {

    static function getFooterUrl($parameters = null) {
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
  static function generateLandingPageUrl($urlParamName, $urlParamValue, $yearId = null)
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
}
