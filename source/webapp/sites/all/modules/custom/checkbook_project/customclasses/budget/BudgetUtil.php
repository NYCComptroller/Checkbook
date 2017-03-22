<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class BudgetUtil{
    /**
        * Function to handle Budget Code Name paramenter
        * Adjust Budget Code Name paramenter to do wildcard search on 'Budget Code Name' or 'Budget Code' columns
        * @param $node
        * @param $parameters
    */
    static public function adjustBudgetCodeNameParameter(&$node, &$parameters){
        if(isset($parameters['budget_code_name'])){
            $data_controller_instance = data_controller_get_operator_factory_instance();
            $budget_code = $parameters['budget_code_name'][0];
            $parameters['budget_code'] = $data_controller_instance->initiateHandler(WildcardOperatorHandler::$OPERATOR__NAME, array($budget_code, false, true));
            $node->widgetConfig->logicalOrColumns[] = array('budget_code_name','budget_code');
        }
        return $parameters;
    }
    
    
    /**
        * Function to get Budget Code Id for the combination of Budget Code, Budget Code Name and Year
        * @param $budget_code_name
        * @param $budget_code
        * @param $year
    */
    static public function getBudgetCodeId($budget_code_name, $budget_code, $year){
        $query = "SELECT DISTINCT budget_code_id FROM budget
                  WHERE budget_code = ". $budget_code
                ." AND lower(budget_code_name) = '". strtolower($budget_code_name) . "'"
                ." AND budget_fiscal_year_id = ".$year;

        $results = _checkbook_project_execute_sql_by_data_source($query,'checkbook');
        return $results[0]['budget_code_id'];
    }
    
}