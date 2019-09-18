<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (C) 2012, 2013 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class NYCHAContractUtil
{
    static function adjustYearParams(&$node, &$parameters) {
        if(isset($parameters['release_year_id'])){
            $year_id = $parameters['release_year_id'];
            $data_controller_instance = data_controller_get_operator_factory_instance();
            $parameters['agreement_start_year_id'] = $data_controller_instance->initiateHandler(LessOrEqualOperatorHandler::$OPERATOR__NAME, $year_id);
            $parameters['agreement_end_year_id'] = $data_controller_instance->initiateHandler(GreaterOrEqualOperatorHandler::$OPERATOR__NAME, $year_id);
            unset($parameters['release_year_id']);
        }
        return $parameters;
    }

    /**
     * @param $tcode String -- Widget Key
     * @return string -- Returns Widget Title for the given key
     */
    public static function getTitleByCode($tcode){
      $summaryTitle='';
      switch($tcode){
        case 'BA':
          $summaryTitle = 'Blanket Agreements';
          break;
        case 'BAM':
          $summaryTitle='Blanket Agreement Modifications';
          break;
        case 'PA':
          $summaryTitle='Planned Agreements';
          break;
        case 'PAM':
          $summaryTitle='Planned Agreement Modifications';
          break;
        case 'PO':
          $summaryTitle='Purchase Orders';
          break;
        case 'VO':
          $summaryTitle='Vendors';
          break;
        case 'AWD':
          $summaryTitle='Award Methods';
          break;
        case 'IND':
          $summaryTitle='Contracts by Industries';
          break;
        case 'RESC':
          $summaryTitle='Responsibility Centers';
          break;
        case 'DEP':
          $summaryTitle='Departments';
          break;
        case 'SZ':
          $summaryTitle='Contracts by Size';
          break;
      }
      return $summaryTitle;
    }
}
