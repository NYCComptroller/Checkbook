<?php
/**
 * @param $data_set
 * @param $parameters
 * @param $criteria
 * @param null $datasource
 */
function checkbook_api_adjustBudgetParameterFilters(&$data_set, &$parameters, $criteria, $datasource = null)
{
  $data_controller_instance = data_controller_get_operator_factory_instance();
  if (!isset($parameters['budget_code']) && isset($parameters['budget_code_name'])) {
    $logicalOrColumns[] = array("budget_code", "budget_code_name");
    $parameters['budget_code'] = $data_controller_instance->initiateHandler(EqualOperatorHandler::$OPERATOR__NAME, $parameters['budget_code_name']);
    $parameters['budget_code_name'] = $data_controller_instance->initiateHandler(RegularExpressionOperatorHandler::$OPERATOR__NAME, "(^" . _checkbook_regex_replace_pattern($parameters['budget_code_name']) . "$)");
  } else if (isset($parameters['budget_code_name'])) {
    $parameters['budget_code_name'] = $data_controller_instance->initiateHandler(RegularExpressionOperatorHandler::$OPERATOR__NAME, "(^" . _checkbook_regex_replace_pattern($parameters['budget_code_name']) . "$)");
  }
  if (isset($logicalOrColumns) && count($logicalOrColumns) > 0) {
    $parameters['logicalOrColumns'] = $logicalOrColumns;
  }
}

/***
 * @param $sql_query
 * @return string
 */
function checkbook_api_adjustNYCHABudgetSql(&$sql_query)
{
  $sql_parts = explode("WHERE", $sql_query);
  $select_part = $sql_parts[0];
  $where_part = $sql_parts[1];
  $select_part = str_replace("modified_budget", "adopted_budget AS modified_budget", $select_part);
  $sql_query = (count($sql_parts) > 1) ? implode(' WHERE ', array($select_part, $where_part)) : $select_part;
}
