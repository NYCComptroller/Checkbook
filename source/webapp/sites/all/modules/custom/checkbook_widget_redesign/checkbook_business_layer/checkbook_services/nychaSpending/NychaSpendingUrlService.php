<?php
/**
 * Created by PhpStorm.
 * User: sgade
 * Date: 05/06/19
 * Time: 10:26 AM
 */

class NychaSpendingUrlService{
    /**
     * @param $parameters
     * @return string
     */
    static function getFooterUrl($parameters = null)
    {
        $url = "/panel_html/nycha_spending_transactions/nycha_spending/transactions"
            . RequestUtilities::buildUrlFromParam('year')
            . RequestUtilities::buildUrlFromParam('category')
            . RequestUtilities::buildUrlFromParam('agency')
            . RequestUtilities::buildUrlFromParam('vendor')
            . RequestUtilities::buildUrlFromParam('fundsrc')
            . RequestUtilities::buildUrlFromParam('industry')
            . RequestUtilities::buildUrlFromParam('datasource');
        return $url;
    }

    /**
     * Returns NYCHA Spending Landing page URL
     * @param $urlParamName
     * @param $urlParamValue
     * @return string
     */
    static function generateLandingPageUrl($urlParamName, $urlParamValue)
    {
        $url = '/nycha_spending'
            . RequestUtilities::buildUrlFromParam('year')
            . RequestUtilities::buildUrlFromParam('category')
            . RequestUtilities::buildUrlFromParam('agency')
            . RequestUtilities::buildUrlFromParam('vendor')
            . RequestUtilities::buildUrlFromParam('fundsrc')
            . RequestUtilities::buildUrlFromParam('industry')
            . RequestUtilities::buildUrlFromParam('datasource')
            . '/'.$urlParamName.'/'. $urlParamValue;

        return $url;
    }

    /* Gets the YTD Spending link in a generic way
    * @param $dynamic_parameter - custom dynamic parameters to be used in the URL
    * @param null $legacy_node_id
    * @return string
    */
      static function ytdSpendingUrl($dynamic_parameter, $widget) {
        $dynamic_parameter = isset($dynamic_parameter) ? $dynamic_parameter : '';
        $url = "/panel_html/nycha_spending_transactions/nycha_spending/transactions"
          . RequestUtilities::buildUrlFromParam('year')
          . RequestUtilities::buildUrlFromParam('category')
          . RequestUtilities::buildUrlFromParam('agency')
          . RequestUtilities::buildUrlFromParam('vendor')
          . RequestUtilities::buildUrlFromParam('fundsrc')
          . RequestUtilities::buildUrlFromParam('industry')
          . RequestUtilities::buildUrlFromParam('datasource')
          . '/widget/'. $widget
          . $dynamic_parameter;

          return $url;
      }
}
