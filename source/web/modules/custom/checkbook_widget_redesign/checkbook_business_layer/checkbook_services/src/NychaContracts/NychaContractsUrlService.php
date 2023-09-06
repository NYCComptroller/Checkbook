<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\checkbook_services\NychaContracts;

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\checkbook_project\CommonUtilities\CustomURLHelper;
use Drupal\checkbook_project\CommonUtilities\RequestUtil;

class NychaContractsUrlService
{
    /**
     * @param $parameters
     * @return string
     */
    static function getFooterUrl($parameters = null)
    {
        $url = "/nycha_contracts_transactions_page/nycha_contracts/transactions"
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
        $year_id = RequestUtilities::get('year');
        if (!$just_bottom_url) {
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
          //$lastReqParam = RequestUtil::_getLastRequestParamValue(\Drupal::request()->query->get('q'));
          $lastReqParam = RequestUtil::_getLastRequestParamValue();
          if ($lastReqParam != RequestUtil::_getLastRequestParamValue($url)) {
            foreach ($lastReqParam as $key => $value) {
              $url = preg_replace("/\/" . $key . "\/" . $value . "/", "", $url);
              $url .= "/" . $key . "/" . $value;
            }
          }
        }

        $url .= '?expandBottomContURL=/nycha_contract_details/year/'.$year_id.'/agency/162/datasource/checkbook_nycha/contract/' . $contract_id;
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
             . CustomURLHelper::_checkbook_project_get_year_url_param_string()
            . RequestUtilities::buildUrlFromParam('agency')
            . '/datasource/checkbook_nycha'
            . '/vendor/'. $vendor_id;

        return $url;
    }
}
