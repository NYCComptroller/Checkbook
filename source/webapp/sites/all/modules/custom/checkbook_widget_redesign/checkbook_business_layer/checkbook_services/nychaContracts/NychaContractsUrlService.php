<?php
/**
 * Created by PhpStorm.
 * User: sgade
 * Date: 11/01/18
 * Time: 10:26 AM
 */

class NychaContractsUrlService
{
    /**
     * @param $parameters
     * @return string
     */
    static function getFooterUrl($parameters)
    {
        $url = "/panel_html/nycha_contracts_transactions_page/nycha_contracts/transactions"
            . RequestUtilities::buildUrlFromParam('year')
            . RequestUtilities::buildUrlFromParam('agency')
            . RequestUtilities::buildUrlFromParam('vendor')
            . RequestUtilities::buildUrlFromParam('industry')
            . RequestUtilities::buildUrlFromParam('csize')
            . RequestUtilities::buildUrlFromParam('awdmethod')
            . RequestUtilities::buildUrlFromParam('datasource');
        return $url;
    }

    /**
     * Returns NYCHA Contracts Vendor Landing page URL for the given vendor id
     * @param $urlParamName
     * @param $urlParamValue
     * @return string
     */
    static function generateLandingPageUrl($urlParamName, $urlParamValue)
    {
        $url = '/nycha_contracts'
            . RequestUtilities::buildUrlFromParam('year')
            . RequestUtilities::buildUrlFromParam('agency')
            . RequestUtilities::buildUrlFromParam('vendor')
            . RequestUtilities::buildUrlFromParam('industry')
            . RequestUtilities::buildUrlFromParam('csize')
            . RequestUtilities::buildUrlFromParam('awdmethod')
            . RequestUtilities::buildUrlFromParam('datasource')
            . '/'.$urlParamName.'/'. $urlParamValue;

        return $url;
    }

    /**
     * Returns NYCHA Contracts Vendor Landing page URL for the given vendor id
     * @param $contract_id
     * @return string
     */
    static function contractDetailsUrl($contract_id)
    {
        $url = '/nycha_contracts'
            . RequestUtilities::buildUrlFromParam('year')
            . RequestUtilities::buildUrlFromParam('agency')
            . RequestUtilities::buildUrlFromParam('vendor')
            . RequestUtilities::buildUrlFromParam('industry')
            . RequestUtilities::buildUrlFromParam('csize')
            . RequestUtilities::buildUrlFromParam('awdmethod')
            . RequestUtilities::buildUrlFromParam('datasource')
            . '?expandBottomContURL=/panel_html/nycha_contract_details/contract/' . $contract_id;

        return $url;
    }

    /**
     *  Returns NYCHA Agreement Type Code URL string for the given Agreement Type Code
     * @param $agreementTypeCode
     * @return string
     */
    static function agreementTypeUrl($agreementTypeCode)
    {
        $url = "/agreement_type/" . $agreementTypeCode;
        return $url;
    }

    /**
     *  Returns NYCHA  Type Code URL string for the given Widget
     * @param $TypeCode
     * @return string
     */
    static function TypeUrl($TypeCode)
    {
        $url = "/tCode/" . $TypeCode;
        return $url;
    }

    /**
     * @param int $agencyID
     * @return string
     */
    public static function agencyUrl($agencyID = 162)
    {
        $url = '/nycha_contracts'
            . '/datasource/checkbook_nycha'
            . '/year/' . _getCurrentYearID()
            . '/agency/' . $agencyID;
        return $url;
    }

    /**
     * @return string
     */
    public static function modificationUrl()
    {
        $url = "/modamt/0";
        return $url;
    }
}
