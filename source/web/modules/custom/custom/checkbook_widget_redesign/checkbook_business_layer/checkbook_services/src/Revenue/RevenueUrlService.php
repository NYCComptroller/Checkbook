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

namespace Drupal\checkbook_services\Revenue;
//require_once(\Drupal::service('extension.list.module')->getPath('checkbook_project') . "/includes/checkbook_project.inc");

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\CustomURLHelper;

/**
 * Created by PhpStorm.
 * User: sgade
 * Date: 01/10/2017
 * Time: 1:16 PM
 */
class RevenueUrlService {
    /**
     * @param $parameters
     * @param null $legacy_node_id
     * @return string
     */
    static function getFooterUrl($parameters=null,$legacy_node_id = null) {
        $parameters = isset($parameters) ? $parameters : null;
        $legacy_node_id = isset($legacy_node_id) ? $legacy_node_id : null;
        $url = '/revenue/transactions/revenue_transactions'.'/dtsmnid/' . $legacy_node_id;
        $url .= RequestUtilities::buildUrlFromParam('agency');
        $url .= RequestUtilities::buildUrlFromParam('revcat');
        $url .= RequestUtilities::buildUrlFromParam('fundsrccode');
        $url .= CustomURLHelper::_checkbook_project_get_year_url_param_string();
        return $url;
    }

    /**
     * @param $footerUrl
     * @param $crossYearFooterUrl
     * @return string
     */
    static function getCrossYearFooterUrl($footerUrl=null,$crossYearFooterUrl=null) {
        $footerUrl = isset($footerUrl) ? $footerUrl : null;
        $crossYearFooterUrl = isset($crossYearFooterUrl) ? $crossYearFooterUrl : null ;
        $url = str_replace('/revenue/transactions/revenue_transactions/',$crossYearFooterUrl,$footerUrl);
        return $url;
    }

    /**
     * @param $agencyId
     * @param null $legacy_node_id
     * @return string
     */
    static function getAgencyUrl($agencyId,$legacy_node_id = null) {
        $legacy_node_id = isset($legacy_node_id) ? $legacy_node_id : null;
        $url = '/revenue'.RequestUtilities::buildUrlFromParam('year')
                .RequestUtilities::buildUrlFromParam('yeartype')
                .'/agency/'.$agencyId;
        return $url;
    }

    /**
     * @param $param
     * @param $value
     * @param $legacy_node_id Legacy Node Id
     * @param $crorss_year Prevoius Year
     * @return string
     */

    static function getRecognizedAmountUrl($param, $value,$legacy_node_id = null, $crorss_year = null) {
        $legacy_node_id = isset($legacy_node_id)?$legacy_node_id : null;
        $url = '/revenue/transactions/revenue_transactions'.'/smnid/' . $legacy_node_id;
        $url .= RequestUtilities::buildUrlFromParam('agency');
        $url .= RequestUtilities::buildUrlFromParam('revcat');
        $url .= RequestUtilities::buildUrlFromParam('fundsrccode');
        $url .= CustomURLHelper::_checkbook_project_get_year_url_param_string();
        $url .= isset($crorss_year) ? '/fiscal_year/'.(RequestUtilities::get('year')+$crorss_year) : "";
        $url .= '/'.$param.'/'.$value;
        return $url;
    }

}
