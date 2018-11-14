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
     * @param null $legacy_node_id
     * @return string
     */
    static function getFooterUrl($parameters, $legacy_node_id = null)
    {
        $url = "";
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
}
