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

namespace { //global
    class NYCHAContractUtil
    {
        public static function adjustYearParams(&$node, &$parameters) {
            if(isset($parameters['release_approved_year_id'])){
                $year = $parameters['release_approved_year_id'];
                $data_controller_instance = data_controller_get_operator_factory_instance();
                $parameters['agreement_start_year_id'] = $data_controller_instance->initiateHandler(LessOrEqualOperatorHandler::$OPERATOR__NAME, $year);
                $parameters['agreement_end_year_id'] = $data_controller_instance->initiateHandler(GreaterOrEqualOperatorHandler::$OPERATOR__NAME, $year);
                unset($parameters['release_approved_year_id']);
            }
            return $parameters;
        }
    }
}
