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
    static function getFooterUrl($parameters)
    {
        $url = "/panel_html/nycha_spending_transactions_page/nycha_spending/transactions"
            . RequestUtilities::buildUrlFromParam('year')
            . RequestUtilities::buildUrlFromParam('agency')
            . RequestUtilities::buildUrlFromParam('vendor')
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
            . RequestUtilities::buildUrlFromParam('agency')
            . RequestUtilities::buildUrlFromParam('vendor')
            . RequestUtilities::buildUrlFromParam('datasource')
            . '/'.$urlParamName.'/'. $urlParamValue;

        return $url;
    }
}
