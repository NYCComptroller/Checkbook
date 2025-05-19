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

namespace Drupal\checkbook_services\NychaSpending;

use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\checkbook_project\CommonUtilities\RequestUtil;

class NychaSpendingUrlService{

    /**
     * @param $parameters
     * @return string
     */
    public static function getFooterUrl($parameters = null): string
    {
        return "/nycha_spending/transactions"
            . RequestUtilities::buildUrlFromParam('year')
            . RequestUtilities::buildUrlFromParam('category')
            . RequestUtilities::buildUrlFromParam('agency')
            . RequestUtilities::buildUrlFromParam('vendor')
            . RequestUtilities::buildUrlFromParam('fundsrc')
            . RequestUtilities::buildUrlFromParam('industry')
            . RequestUtilities::buildUrlFromParam('datasource');
    }

  /**
   * @param $urlParamName
   * @param $urlParamValue
   * @param null $yearId
   * @return string
   */
    public static function generateLandingPageUrl($urlParamName, $urlParamValue, $yearId = null): string
    {
        $yearId = (isset($yearId)) ? $yearId : RequestUtilities::get('year');
        $yearURL = '/year/'. ((isset($yearId)) ? $yearId : CheckbookDateUtil::getCurrentFiscalYearId(Datasource::NYCHA));
        return '/nycha_spending'
            . $yearURL
            . RequestUtilities::buildUrlFromParam('category')
            . RequestUtilities::buildUrlFromParam('agency')
            . RequestUtilities::buildUrlFromParam('vendor')
            . RequestUtilities::buildUrlFromParam('fundsrc')
            . RequestUtilities::buildUrlFromParam('industry')
            . RequestUtilities::buildUrlFromParam('datasource')
            . '/'.$urlParamName.'/'. $urlParamValue;
    }

    /** Gets the YTD Spending link in a generic way
    * @param $dynamic_parameter - custom dynamic parameters to be used in the URL
    * @param $widget
    * @return string
   */
    public static function ytdSpendingUrl($dynamic_parameter, $widget): string
      {
        $dynamic_parameter = $dynamic_parameter ?? '';
        return "/nycha_spending/transactions"
          . RequestUtilities::buildUrlFromParam('year')
          . RequestUtilities::buildUrlFromParam('issue_date')
          . RequestUtilities::buildUrlFromParam('category')
          . RequestUtilities::buildUrlFromParam('agency')
          . RequestUtilities::buildUrlFromParam('vendor')
          . RequestUtilities::buildUrlFromParam('fundsrc')
          . RequestUtilities::buildUrlFromParam('industry')
          . RequestUtilities::buildUrlFromParam('datasource')
          . '/widget/'. $widget
          . $dynamic_parameter;
      }

  /**
   * @param $dynamic_parameter
   * @param $year_parameter
   * @param $widget
   * @return string
   */
  public static function idateSpendingUrl($dynamic_parameter,$year_parameter, $widget): string
  {
    $year_parameter = $year_parameter ?? '';
    $category = RequestUtilities::buildUrlFromParam('category');
    $category = str_replace("category", "category_inv", $category);
    $dynamic_parameter = $dynamic_parameter ?? '';
    return "/nycha_spending/transactions"
      . RequestUtilities::buildUrlFromParam('datasource')
      . $year_parameter.$category
      . RequestUtilities::buildUrlFromParam('vendor')
      . '/widget/'. $widget
      . $dynamic_parameter;
    }

    /** Gets the Invoice amount Spending link in a generic way for NYCHA Contracts
   * @param null $dynamic_parameter
   * @param $widget
   * @param null $agreement_type
   * @param null $tcode
   * @return string
   */
     public static function invContractSpendingUrl($dynamic_parameter = null , $widget = null,$agreement_type = null, $tcode = null ): string
      {
        //$url = \Drupal::service('path.current')->getPath();
        $url = RequestUtilities::getCurrentPageUrl();
        $year = RequestUtil::getRequestKeyValueFromURL('year', $url);
        $vendor = RequestUtilities::buildUrlFromParam('vendor');
        $industry = RequestUtilities::buildUrlFromParam('industry');
        $vendor = str_replace("vendor", "vendor_inv", $vendor);
        $industry = str_replace("industry", "industry_inv", $industry);
        $dynamic_parameter = $dynamic_parameter ?? '';
        $syear = "/syear/".$year;
        $agreement_type = $agreement_type ?? '';
        $newwindow='/newwindow'; // open content in new window and also strip menu contents
        $tcode = $tcode ?? '';
        return "/nycha_spending/transactions"
          . RequestUtilities::buildUrlFromParam('year')
          .$vendor.$industry
          . RequestUtilities::buildUrlFromParam('category')
          . RequestUtilities::buildUrlFromParam('agency')
          . RequestUtilities::buildUrlFromParam('dept')
          . RequestUtilities::buildUrlFromParam('resp_center')
          . RequestUtilities::buildUrlFromParam('csize')
          . RequestUtilities::buildUrlFromParam('awdmethod')
          . RequestUtilities::buildUrlFromParam('datasource')
          . $syear
          . '/widget/'. $widget
          . $dynamic_parameter.$agreement_type.$tcode.$newwindow;
      }
  /** Gets the Invoice amount Spending link in a generic way for NYCHA Contracts
   * @param null $dynamic_parameter
   * @param null $widget
   * @param null $agreement_type
   * @param null $tcode
   * @return string
   */
  public static function invIDContractSpendingUrl($dynamic_parameter = null , $widget = null, $agreement_type = null ,$tcode =null): string
  {
    $vendor = RequestUtilities::buildUrlFromParam('vendor');
    $industry = RequestUtilities::buildUrlFromParam('industry');
    $vendor = str_replace("vendor", "vendor_inv", $vendor);
    $industry = str_replace("industry", "industry_inv", $industry);
    $dynamic_parameter = $dynamic_parameter ?? '';
    $agreement_type = $agreement_type ?? '';
    $newwindow = '/newwindow'; // open content in new window and also strip menu contents
    $tcode = $tcode ?? '';
    return "/nycha_spending/transactions"
      .$vendor.$industry
      . RequestUtilities::buildUrlFromParam('category')
      . RequestUtilities::buildUrlFromParam('agency')
      . RequestUtilities::buildUrlFromParam('dept')
      . RequestUtilities::buildUrlFromParam('resp_center')
      . RequestUtilities::buildUrlFromParam('csize')
      . RequestUtilities::buildUrlFromParam('awdmethod')
      . RequestUtilities::buildUrlFromParam('datasource')
      . '/widget/' . $widget
      . $dynamic_parameter . $agreement_type . $tcode . $newwindow;
  }

      /**
     * Builds Contract ID link for Spending widgets
     * @param $contract_id
     * @param null $year_id
     * @return string
     */
      public static function generateContractIdLink($contract_id, $year_id = null): string {
        $year_id = (isset($year_id)) ? $year_id : RequestUtilities::get('year');
        $year_id = (isset($year_id)) ? $year_id : CheckbookDateUtil::getCurrentFiscalYear(Datasource::NYCHA);
        $class = "new_window";
        $url ='/nycha_contract_details' . '/year/'.$year_id.'/contract/' . $contract_id . '/newwindow';
        return "<a class='{$class}' href='{$url}'>{$contract_id}</a>";
      }
}
