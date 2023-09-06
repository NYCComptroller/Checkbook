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

namespace Drupal\checkbook_project\BudgetUtilities;
use Drupal\checkbook_log\LogHelper;
use Drupal\data_controller\Common\Object\Exception\IllegalArgumentException;
use Drupal\data_controller\Datasource\Operator\Handler\WildcardOperatorHandler;

class BudgetUtil{
    /**
     * Function to handle Budget Code Name paramenter
     * Adjust Budget Code Name paramenter to do wildcard search on 'Budget Code Name' or 'Budget Code' columns
     * @param $node
     * @param $parameters
     * @return mixed
     */
    public static function adjustBudgetCodeNameParameter(&$node, &$parameters){
        if(isset($parameters['budget_code_name'])){
            $data_controller_instance = data_controller_get_operator_factory_instance();
            $budget_code = $parameters['budget_code_name'][0];
          try {
            $parameters['budget_code'] = $data_controller_instance->initiateHandler(WildcardOperatorHandler::$OPERATOR__NAME, array($budget_code, false, true));
          } catch (IllegalArgumentException|\ReflectionException $e) {
            LogHelper::log_error('Error initiating WildcardOperatorHandler');
          }
          $node->widgetConfig->logicalOrColumns[] = array('budget_code_name','budget_code');
        }
        return $parameters;
    }

    /**
     * Function to get Budget Code Id for the combination of Budget Code, Budget Code Name and Year
     * @param $budget_code_name
     * @param $budget_code
     * @param $year
     * @return
     */
    public static function getBudgetCodeId($budget_code_name, $budget_code, $year){
        $query = "SELECT DISTINCT budget_code_id FROM budget
                  WHERE budget_code = '". trim($budget_code). "'"
                ." AND budget_code_name ILIKE '". addslashes(trim($budget_code_name)) . "'"
                ." AND budget_fiscal_year_id = ".$year;

        $results = _checkbook_project_execute_sql_by_data_source($query);
        return $results[0]['budget_code_id'];
    }

    /**
     * Function to get Budget Code and Budget Code Name for the combination of Budget Code ID, Agency Id and Year ID
     * @param $budget_code_id
     * @param $agency_id
     * @param $year
     * @return array|null
     */
    public static function getBudgetCodeNameAndBudgetCode($budget_code_id, $agency_id, $year){
        $query = "SELECT DISTINCT budget_code, budget_code_name FROM budget
                  WHERE budget_code_id = ". $budget_code_id
                ." AND agency_id = ". $agency_id
                ." AND budget_fiscal_year_id = ". $year;
        $results = _checkbook_project_execute_sql_by_data_source($query);
        if(count($results) > 0){
            return array('budget_code' => $results[0]['budget_code'], 'budget_code_name' => $results[0]['budget_code_name']);
        }else{
            return null;
        }
    }

}
