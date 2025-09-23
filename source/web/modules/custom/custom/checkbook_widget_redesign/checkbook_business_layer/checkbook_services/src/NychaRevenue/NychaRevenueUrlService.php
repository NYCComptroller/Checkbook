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

namespace Drupal\checkbook_services\NychaRevenue;

use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;

class NychaRevenueUrlService {

  /**
   * Returns Footer URL for widget
   * @param null $parameters
   * @return string
   */
  public static function getFooterUrl($parameters = null) {
      $url = "/nycha_revenue/transactions/nycha_revenue_transactions"
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
   * @param $yearId
   * @return string
   */
  public static function generateLandingPageUrl($urlParamName, $urlParamValue, $yearId = null)
  {
    $yearId = (isset($yearId)) ? $yearId : RequestUtilities::get('year');
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
   * @param $yearId
   * @return string
   */
  public static function recRevenueUrl($dynamic_parameter, $widget) {
    $dynamic_parameter = isset($dynamic_parameter) ? $dynamic_parameter : '';
    $url = "/nycha_revenue/transactions/nycha_revenue_transactions"
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
