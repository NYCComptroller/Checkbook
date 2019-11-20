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
   * @param bool $just_bottom_url
   * @return string
   */
    static function contractDetailsUrl($contract_id, $just_bottom_url = false)
    {
        $url = '';
        if (!$just_bottom_url) {
          $year_id = RequestUtilities::getRequestParamValue('year');
          if(!isset($year_id)){
            $year_id = CheckbookDateUtil::getCurrentFiscalYearId();
          }
          $url = '/nycha_contracts'
            . '/year/'.$year_id
            . RequestUtilities::buildUrlFromParam('agency')
            . RequestUtilities::buildUrlFromParam('vendor')
            . RequestUtilities::buildUrlFromParam('industry')
            . RequestUtilities::buildUrlFromParam('csize')
            . RequestUtilities::buildUrlFromParam('awdmethod')
            . RequestUtilities::buildUrlFromParam('datasource');

          //Persist the last parameter in the current page URL as the last param only to fix the title issues
          $lastReqParam = _getLastRequestParamValue($_SERVER['HTTP_REFERER']);
          if ($lastReqParam != _getLastRequestParamValue($url)) {
            foreach ($lastReqParam as $key => $value) {
              $url = preg_replace("/\/" . $key . "\/" . $value . "/", "", $url);
              $url .= "/" . $key . "/" . $value;
            }
          }
        }

        $url .= '?expandBottomContURL=/panel_html/nycha_contract_details/year/'.$year_id.'/agency/162/datasource/checkbook_nycha/contract/' . $contract_id;
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
            . '/year/' . CheckbookDateUtil::getCurrentFiscalYearId()
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
    /**
     * Returns NYCHA Contracts Vendor Transaction page URL for the given vendor id
     * @param $vendor_id
     * @return string
     */
    static function vendorUrl($vendor_id)
    {


        $url = '/nycha_contracts'
             . _checkbook_project_get_year_url_param_string()
            . RequestUtilities::buildUrlFromParam('agency')
            . '/datasource/checkbook_nycha'
            . '/vendor/'. $vendor_id;

        return $url;
    }
}
