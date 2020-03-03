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
     * @return mixed
     */
    public static function adjustBudgetCodeNameParameter(&$node, &$parameters){
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
     * @return
     */
    public static function getBudgetCodeId($budget_code_name, $budget_code, $year){
        $query = "SELECT DISTINCT budget_code_id FROM budget
                  WHERE budget_code = '". trim($budget_code). "'"
                ." AND budget_code_name ILIKE '". addslashes(trim($budget_code_name)) . "'"
                ." AND budget_fiscal_year_id = ".$year;

        $results = _checkbook_project_execute_sql_by_data_source($query,'checkbook');
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
        $results = _checkbook_project_execute_sql_by_data_source($query,'checkbook');
        if(count($results) > 0){
            return array('budget_code' => $results[0]['budget_code'], 'budget_code_name' => $results[0]['budget_code_name']);
        }else{
            return null;
        }
    }
    
}
