<?php

/**
 * Function to adjust the parameters before for the api sql call for payroll
 *
 * @param $data_set
 * @param $parameters
 * @param $criteria
 */
function checkbook_api_adjustPayrollParameterFilters(&$data_set, &$parameters, $criteria)
{
  if (isset($parameters['civil_service_title_exact'])) {
    $parameters['civil_service_title'] = data_controller_get_operator_factory_instance()->initiateHandler(WildcardOperatorHandler::$OPERATOR__NAME, array($parameters['civil_service_title_exact'], FALSE, FALSE,));
    unset($parameters['civil_service_title_exact']);
  }
}

/***
 * @param $sql_query
 * @return string
 */
function checkbook_api_adjustPayrollSql(&$sql_query, $criteria)
{

  $sql_parts = explode("FROM", $sql_query);
  $select_part = $sql_parts[0];
  $from_part = $sql_parts[1];
  $select_part = str_replace("amount_basis_id", "CASE WHEN amount_basis_id = 1 THEN 'SALARIED' ELSE 'NON-SALARIED' END AS amount_basis_id", $select_part);
  if (strtolower($criteria['global']['response_format']) == 'csv') {
    $select_part = preg_replace('/\bsalaried_amount\b/', 'CASE WHEN amount_basis_id != 1 THEN  CAST(\'-\' AS text) ELSE CAST(salaried_amount AS Text) END AS salaried_amount', $select_part);
    $select_part = preg_replace('/\bnon_salaried_amount\b/', 'CASE WHEN amount_basis_id = 1 THEN  CAST(\'-\' AS text) ELSE CAST(non_salaried_amount AS Text) END AS non_salaried_amount', $select_part);
  }
  $sql_query = $select_part . "FROM" . $from_part;
}

/***
 * @param $sql_query
 * @return string
 */
function checkbook_api_adjustNYCHAPayrollSql(&$sql_query, $criteria)
{
  $sql_parts = explode("FROM", $sql_query);
  $select_part = $sql_parts[0];
  $from_part = $sql_parts[1];
  $select_part = str_replace("amount_basis_id", "CASE WHEN amount_basis_id = 1 THEN 'SALARIED' ELSE 'NON-SALARIED' END AS amount_basis_id", $select_part);
  if (strtolower($criteria['global']['response_format']) == 'csv') {
    $select_part = preg_replace('/\bsalaried_amount\b/', 'CASE WHEN amount_basis_id != 1 THEN  CAST(\'-\' AS text) ELSE CAST(salaried_amount AS Text) END AS salaried_amount', $select_part);
    $select_part = preg_replace('/\bnon_salaried_amount\b/', 'CASE WHEN amount_basis_id = 2 AND non_salaried_amount > 0 THEN  CAST(non_salaried_amount AS text) ELSE CAST(\'-\' AS Text) END AS non_salaried_amount', $select_part);
    $select_part = str_replace("hourly_rate", "CASE WHEN amount_basis_id = 3 AND non_salaried_amount > 0 THEN  CAST(non_salaried_amount AS text) ELSE CAST('-' AS Text) END AS hourly_rate", $select_part);
  }
  $sql_query = $select_part . "FROM" . $from_part;
}
