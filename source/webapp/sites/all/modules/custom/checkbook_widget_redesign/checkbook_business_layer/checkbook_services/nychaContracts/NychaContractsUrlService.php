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
                . RequestUtilities::buildUrlFromParam('datasource');
        return $url;
    }

    /**
     * Returns NYCHA Contracts Vendor Landing page URL for the given vendor id
     * @param $vendor_id
     */
    static function vendorUrl($vendor_id)
    {
        $url = '/nycha_contracts'
            . RequestUtilities::buildUrlFromParam('year')
            . RequestUtilities::buildUrlFromParam('datasource')
            . '/vendor/'. $vendor_id;

        return $url;
    }

    /**
     *
     */
    static function agreementTypeUrl($agreementTypeCode){
        $url = "/agreement_type/".$agreementTypeCode;
        return $url;
    }
}
