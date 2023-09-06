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

namespace Drupal\checkbook_project\EdcUtilities;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\CustomURLHelper;

class EdcUtilities
{
  /**
   * @param $cont_num
   *
   * @return bool
   */
  public static function _checkbook_is_oge_contract($cont_num)
  {
    $rows = _checkbook_project_querydataset('checkbook_oge:oge_contract', array('fms_contract_number'), array('fms_contract_number' => $cont_num));
    if ($rows > 0) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * @param $cont_num
   *
   * @return bool
   */
  public static function _checkbook_is_oge_parent_contract($cont_num)
  {
    $rows = _checkbook_project_querydataset('checkbook_oge:agreement_snapshot', array('master_contract_number'), array('master_contract_number' => $cont_num));
    if ($rows > 0) {
      return true;
    } else {
      return false;
    }
  }


  /**
   * @param $vendor_id
   *
   * @return string
   */
  public static function _checkbook_get_oge_agency_id($vendor_id)
  {
    $vendors = _checkbook_project_querydataset('checkbook:vendor', array('vendor_id', 'legal_name'), array('vendor_id' => $vendor_id));
    $vendor_name = $vendors[0]['legal_name'];
    $agencies = _checkbook_project_querydataset('checkbook_oge:agency', array('agency_id', 'agency_name'), array('agency_name' => $vendor_name, 'is_display' => 'Y', 'is_oge_agency' => 'Y'));
    return isset($agencies[0]['agency_id']) ? htmlentities($agencies[0]['agency_id']) : NULL;
  }

  /**
   * @param $agency_id
   *
   * @return string
   */
  public static function _checkbook_get_toggle_vendor_id($agency_id)
  {
    $agencies = _checkbook_project_querydataset('checkbook_oge:agency', array('agency_id', 'agency_name'), array('agency_id' => $agency_id, 'is_oge_agency' => 'Y'));
    $agency_name = $agencies[0]['agency_name'];
    $vendors = _checkbook_project_querydataset('checkbook:vendor', array('vendor_id', 'legal_name'), array('legal_name' => $agency_name));
    return isset($vendors[0]['vendor_id']) ? htmlentities($vendors[0]['vendor_id'] - 100000) : NULL;
  }

  /**
   * @return array|null
   */
  public static function _get_toggle_view_links()
  {
    $q = \Drupal::request()->getRequestUri();
    $array_q = explode('/', $q);
    $array_q[1] = ($array_q[1] == 'contracts_landing') ? $array_q[1] . '/status/A' : $array_q[1];
    $year_string = CustomURLHelper::_checkbook_project_get_year_url_param_string();
    $links = [];
    $link = null;

    if (!_checkbook_check_isEDCPage() && RequestUtilities::get('vendor')) {
      $oge_agency_id = EdcUtilities::_checkbook_get_oge_agency_id(RequestUtilities::get('vendor'));
      if ($oge_agency_id > 0) {
        $link = '/' . $array_q[1] . $year_string . '/datasource/checkbook_oge/agency/' . $oge_agency_id;
        return array('vendor', $link);
      }
    } else if (_checkbook_check_isEDCPage()) {
      $vendor_id = EdcUtilities::_checkbook_get_toggle_vendor_id(RequestUtilities::get('agency'));
      if ($vendor_id > 0) {
        $link = '/' . $array_q[1] . $year_string . '/vendor/' . $vendor_id;
        return array('agency', $link);
      }
    }
    return $links;
  }
}
