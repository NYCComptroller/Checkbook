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

namespace Drupal\checkbook_services\VendorUtil;

require_once(dirname(__FILE__) . "/../../../../../checkbook_project/includes/checkbook_database.inc");
require_once(dirname(__FILE__) . "/../../../../../checkbook_project/includes/checkbook_project.inc");

use Drupal\data_controller\Datasource\Operator\Handler\RegularExpressionOperatorHandler;

class PrimeVendorService extends VendorService {

    /**
     * Returns the Latest Minority Type for the given Vendor and the current selected year
     * @param $vendor_id
     * @param null $agency_id
     * @param string $vendor_type
     * @param string $domain
     * @return mixed
     */
    public static function getLatestMinorityType($vendor_id, $agency_id = null, $vendor_type = 'P', $domain = 'spending') {
        return parent::getLatestMinorityType($vendor_id, $agency_id, $vendor_type, $domain);
    }

    /**
     *  Returns the Latest Minority Type for the given Vendor and the current provided transaction year
     * @param $vendor_id
     * @param $year_id
     * @param $type_of_year
     * @param string $vendor_type
     * @param string $domain
     * @return bool
     */
    public static function getLatestMinorityTypeByYear($vendor_id, $year_id, $type_of_year, $vendor_type = 'P', $domain = 'spending') {
        return parent::getLatestMinorityTypeByYear($vendor_id, $year_id, $type_of_year, $vendor_type, $domain);
    }

    /**
     * Given the vendor id, returns the corresponding vendor customer code
     * @param $vendor_id
     * @return null
     */
    public static function getVendorCode($vendor_id){
        $vendor = _checkbook_project_querydataset("checkbook:vendor","vendor_customer_code",array("vendor_id"=>$vendor_id));
        return $vendor[0] ? $vendor[0]['vendor_customer_code'] : null;
    }

    /**
     * Given the vendor name, returns the corresponding prime vendor id
     * @param $vendor_name
     * @return null
     */
    public static function getVendorIdByName($vendor_name){
        $parameters = array();
        $data_controller_instance = data_controller_get_operator_factory_instance();
        $parameters["legal_name"] = $data_controller_instance ? $data_controller_instance->initiateHandler(RegularExpressionOperatorHandler::$OPERATOR__NAME, "(^" . _checkbook_regex_replace_pattern($vendor_name) . "$)"): "";
        $vendor = _checkbook_project_querydataset("checkbook:vendor","vendor_id",$parameters);
        return $vendor[0] ? $vendor[0]['vendor_id'] : null;
    }
}
