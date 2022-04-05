<?php

/**
 * @param $data_set
 * @param $parameters
 * @param $criteria
 * @param null $datasource
 */
function checkbook_api_adjustContractParameterFilters(&$data_set, &$parameters, $criteria, $datasource = null)
{
  $contract_status = $criteria['value']['status'];
  $category = $criteria['value']['category'];
  $type_of_data = ($datasource == Datasource::OGE) ? "contracts_oge" : "";

  //Display contracts which are active from 2011
  if ($datasource != Datasource::OGE && $contract_status != 'pending') {
    $parameters['is_active_eft_2011'] = 1;
  }

  //For Active/Registered Expense Contracts -- CityWide
  if (($type_of_data != "contracts_oge") && ($category == "expense" || $category == "all") && ($contract_status == "active" || $contract_status == "registered")) {
    checkbook_api_adjust_expense_contracts_params($parameters, $criteria, $contract_status, $category);
  } else {
    //For Active/Registered Expense Contracts -- OGE
    switch ($contract_status) {
      case "registered":
      case "active":
        $data_controller_instance = data_controller_get_operator_factory_instance();

        // Either Fiscal or Calendar year is provided:
        $fy = checkbook_api_change_datasource($datasource, $parameters['fiscal_year@checkbook:all_contracts_coa_aggregates']);
        if (isset($fy)) {

          if ($contract_status == 'registered') {
            if (strtolower($type_of_data) == 'contracts_oge')
              $parameters[checkbook_api_change_datasource($datasource, 'status_flag')] = 'R';
            else
              $parameters[checkbook_api_change_datasource($datasource, 'status_flag@checkbook:all_contracts_coa_aggregates')] = 'R';
          } else {
            if ($contract_status == 'active') {
              if (strtolower($type_of_data) == 'contracts_oge')
                $parameters[checkbook_api_change_datasource($datasource, 'status_flag')] = 'A';
              else
                $parameters[checkbook_api_change_datasource($datasource, 'status_flag@checkbook:all_contracts_coa_aggregates')] = 'A';
            }
          }

          if (strtolower($type_of_data) == 'contracts_oge') {
            // Adjust year and year type for OGE
            $req_year = $parameters[checkbook_api_change_datasource($datasource, 'fiscal_year')];
            $req_years = _checkbook_project_querydataset('checkbook_oge:year', array('year_id', 'year_value'), array('year_value' => $req_year));
            $req_year = $req_years[0]['year_id'];
            $parameters[checkbook_api_change_datasource($datasource, 'type_of_year')] = 'B';

            //Set vendor flag for OGE
            $parameters['is_vendor_flag'] = 'N';
          } else {
            // Adjust year and year type for Citywide
            $req_year = $parameters[checkbook_api_change_datasource($datasource, 'fiscal_year@checkbook:all_contracts_coa_aggregates')];
            $parameters[checkbook_api_change_datasource($datasource, 'type_of_year@checkbook:all_contracts_coa_aggregates')] = 'B';
          }

          if (isset($req_year)) {
            $ge_condition = $data_controller_instance->initiateHandler(GreaterOrEqualOperatorHandler::$OPERATOR__NAME, $req_year);
            $le_condition = $data_controller_instance->initiateHandler(LessOrEqualOperatorHandler::$OPERATOR__NAME, $req_year);
            if (strtolower($type_of_data) == 'contracts_oge') {
              $parameters['starting_year_id'] = $le_condition;
              $parameters['ending_year_id'] = $ge_condition;
            } else {
              $parameters['starting_year'] = $le_condition;
              $parameters['ending_year'] = $ge_condition;
            }
            if ($contract_status == 'registered') {
              if (strtolower($type_of_data) == 'contracts_oge')
                $parameters['registered_year_id'] = $req_year;
              else
                $parameters['registered_year'] = $req_year;
            } else {
              if ($contract_status == 'active') {
                if (strtolower($type_of_data) == 'contracts_oge') {
                  $parameters['effective_begin_year_id'] = $le_condition;
                  $parameters['effective_end_year_id'] = $ge_condition;
                } else {
                  $parameters['effective_begin_year'] = $le_condition;
                  $parameters['effective_end_year'] = $ge_condition;
                }
              }
            }
          }
        } else {
          // All years:
          $parameters['latest_flag'] = 'Y';
        }
        break;

      case "pending":
        //FIX ME: Hard coded the contract type code for 'Construction' for now since it is not matching with reference table value
        if (isset($parameters['cont_type_code']) && $parameters['cont_type_code'] == "05") {
          $parameters['cont_type_code'] = "5";
        }
        break;

      default:
        break;
    }

    //Set Document Code for OGE
    $doc_code = strtolower($type_of_data) == 'contracts_oge' ? "document_code" : "document_code@checkbook:ref_document_code";

    if ($category == 'all') {
      $parameters[checkbook_api_change_datasource($datasource, $doc_code)] = array("MMA1", "MA1", "CT1", "DO1", "CTA1", "MAR", "CTR", "RCT1",);
    } else if ($category == 'revenue') {
      $parameters[checkbook_api_change_datasource($datasource, $doc_code)] = 'RCT1';
    } else if ($category == 'expense') {
      $parameters[checkbook_api_change_datasource($datasource, $doc_code)] = array("MMA1", "MA1", "CT1", "DO1", "CTA1", "MAR", "CTR",);
    }

    //Process Minority Type Id parameter
    if (isset($parameters['minority_type_id'])) {
      $parameters['minority_type_id'] = explode('~', $parameters['minority_type_id']);
    }

    //Set Agency id for the selected OGE Agency code
    if (isset($parameters['agency_code@checkbook_oge:oge_agency'])) {
      $agency_id = _checkbook_project_querydataset('checkbook_oge:agency', array('agency_id'), array('agency_code' => $parameters['agency_code@checkbook_oge:oge_agency']));
      $parameters['agency_id'] = $agency_id[0]['agency_id'];
      unset($parameters['agency_code@checkbook_oge:oge_agency']);
    }

    //Set Award method id for the selected OGE Award method code
    if (isset($parameters['award_method_code@checkbook_oge:award_method'])) {
      $award_method_id = _checkbook_project_querydataset('checkbook_oge:award_method', array('award_method_id'), array('award_method_code' => $parameters['award_method_code@checkbook_oge:award_method']));
      $parameters['award_method_id'] = $award_method_id[0]['award_method_id'];
      unset($parameters['award_method_code@checkbook_oge:award_method']);
    }
  }
}

/**
 * Updates the parameters for Active/Registered Expense Contracts
 * @param $parameters
 * @param $criteria
 * @param $contract_status
 * @param $category
 */
function checkbook_api_adjust_expense_contracts_params(&$parameters, $criteria, $contract_status, $category)
{
  $data_controller_instance = data_controller_get_operator_factory_instance();

  $req_year = $criteria['value']['fiscal_year'];
  $req_years = _checkbook_project_querydataset('checkbook:year', array('year_id', 'year_value'), array('year_value' => $req_year));
  $req_year = $req_years[0]['year_id'];

  if (isset($req_year)) {
    $parameters['type_of_year@checkbook:contracts_coa_aggregates_datafeeds'] = 'B';
    $geCondition = $data_controller_instance->initiateHandler(GreaterOrEqualOperatorHandler::$OPERATOR__NAME, array($req_year));
    $leCondition = $data_controller_instance->initiateHandler(LessOrEqualOperatorHandler::$OPERATOR__NAME, array($req_year));
    $parameters['starting_year_id'] = $leCondition;
    $parameters['ending_year_id'] = $geCondition;

    switch ($contract_status) {
      case "active":
        $parameters['status_flag@checkbook:contracts_coa_aggregates_datafeeds'] = 'A';
        $parameters['effective_begin_year_id'] = $leCondition;
        $parameters['effective_end_year_id'] = $geCondition;
        break;
      case "registered":
        $parameters['status_flag@checkbook:contracts_coa_aggregates_datafeeds'] = 'R';
        $parameters['registered_year_id'] = array($req_year);
        break;
    }
  } else {
    $parameters['latest_flag'] = 'Y';
  }

  //Document Code
  switch ($category) {
    case "expense":
      $parameters['document_code'] = array(
        "MMA1",
        "MA1",
        "CT1",
        "DO1",
        "CTA1",
        "MAR",
        "CTR",
      );
      break;
    case "all":
      $parameters['document_code'] = array(
        "MMA1",
        "MA1",
        "CT1",
        "DO1",
        "CTA1",
        "MAR",
        "CTR",
        "RCT1",
      );
      break;
  }

  //Current Amount
  if (isset($parameters['amount_id'])) {
    $parameters['maximum_contract_amount'] = $parameters['amount_id'];
    unset($parameters['amount_id']);
  }

  //Update columns which will be applied as an OR condition
  $logicalOrColumns = array();

  //Vendor
  if (isset($parameters['vendor_code'])) {
    $logicalOrColumns[] = array("prime_vendor_code", "sub_vendor_code");
    $parameters['prime_vendor_code'] = $parameters['vendor_code'];
    $parameters['sub_vendor_code'] = $parameters['vendor_code'];
    unset($parameters['vendor_code']);
  }
  //Purpose
  if (isset($parameters['purpose'])) {
    $logicalOrColumns[] = array("prime_purpose", "sub_purpose");
    $parameters['prime_purpose'] = $parameters['purpose'];
    $parameters['sub_purpose'] = $parameters['purpose'];
    unset($parameters['purpose']);
  }
  //M/WBE Category
  if (isset($parameters['minority_type_id'])) {
    $logicalOrColumns[] = array("prime_mwbe_adv_search_id", "sub_minority_type_id");
    $minority_type_id = explode('~', $parameters['minority_type_id']);
    $condition = $data_controller_instance->initiateHandler(EqualOperatorHandler::$OPERATOR__NAME, array($minority_type_id));
    $parameters['prime_mwbe_adv_search_id'] = $condition;
    $parameters['sub_minority_type_id'] = $condition;
    unset($parameters['minority_type_id']);
  }

  if (count($logicalOrColumns) > 0) {
    $parameters['logicalOrColumns'] = $logicalOrColumns;
  }
}

/**
 * Used to adjust columns in the data set after sql call.  This is to handle derived columns
 * @param $data_records
 */
function checkbook_api_adjustContractDataSetResults(&$data_records)
{
  foreach ($data_records as $key => $data_record) {
    //Derive minority category from minority_type_id
    if (isset($data_record['minority_type_name'])) {
      $data_records[$key]['minority_type_name'] = MappingUtil::getMinorityCategoryByName($data_record['minority_type_name']);
    }
    //Derive sub vendor column from vendor type
    if (isset($data_record['vendor_type'])) {
      $data_records[$key]['vendor_type'] = (preg_match('/S/', $data_record['vendor_type'])) ? 'Yes' : 'No';
      //If Sub vendor field = "Yes" following should be "N/A": Expense Category
      if ($data_records[$key]['vendor_type'] == 'Yes') {
        $data_records[$key]['expenditure_object_name'] = 'N/A';
      } else { //If Sub vendor field = "No" following should be "N/A": Associated Prime Vendor
        $data_records[$key]['prime_vendor_name'] = 'N/A';
      }
    }
    if ($data_record['scntrc_status_name'] == null) {
      $data_records[$key]['scntrc_status_name'] = 'N/A';
    }
  }
}

/***
 * @param $sql_query
 * @return string
 */
function checkbook_api_adjustContractSql(&$sql_query)
{
  $sql_parts = explode("WHERE", $sql_query);
  $select_part = $sql_parts[0];
  $order_by = explode("ORDER BY", $sql_parts[1]);
  $order_by_part = $order_by[1];
  if (strpos($select_part, 'l1.') !== false) {
    $select_part = str_replace("l1.prime_vendor_name", "CASE WHEN l1.is_prime_or_sub = 'S' THEN l1.prime_vendor_name ELSE 'N/A' END AS prime_vendor_name", $select_part);
    $select_part = str_replace("l1.vendor_type", "CASE WHEN l1.is_prime_or_sub = 'P' THEN 'No' ELSE 'Yes' END AS vendor_type", $select_part);
    $select_part = str_replace("l1.expenditure_object_names", "CASE WHEN l1.is_prime_or_sub = 'P' THEN l1.expenditure_object_names ELSE 'N/A' END AS expenditure_object_name", $select_part);
  } else if (strpos($select_part, 'l4.') !== false) {
    $select_part = str_replace("l4.prime_vendor_name", "CASE WHEN l4.is_prime_or_sub = 'S' THEN l4.prime_vendor_name ELSE 'N/A' END AS prime_vendor_name", $select_part);
    $select_part = str_replace("l4.vendor_type", "CASE WHEN l4.is_prime_or_sub = 'P' THEN 'No' ELSE 'Yes' END AS vendor_type", $select_part);
    $select_part = str_replace("l4.expenditure_object_names", "CASE WHEN l4.is_prime_or_sub = 'P' THEN l4.expenditure_object_names ELSE 'N/A' END AS expenditure_object_name", $select_part);
  } else {
    $select_part = str_replace("prime_vendor_name", "CASE WHEN is_prime_or_sub = 'S' THEN prime_vendor_name ELSE 'N/A' END AS prime_vendor_name", $select_part);
    $select_part = str_replace("vendor_type", "CASE WHEN is_prime_or_sub = 'P' THEN 'No' ELSE 'Yes' END AS vendor_type", $select_part);
    $select_part = str_replace("expenditure_object_names", "CASE WHEN is_prime_or_sub = 'P' THEN expenditure_object_names ELSE 'N/A' END AS expenditure_object_name", $select_part);
  }

  // When fiscal year is set make sure order by contract_number is l4.contract_number
  if (strpos($sql_parts[1], 'fiscal_year') !== false) {
    if (strpos($order_by_part, 'contract_number') !== false) {
      $order_by_part = str_replace("contract_number", "l4.contract_number", $order_by_part);
    }
    $where_part = $order_by[0] . ' ORDER BY ' . $order_by_part;
  } else {
    $where_part = $sql_parts[1];
  }
  $sql_query = (count($sql_parts) > 1) ? implode(' WHERE ', array($select_part, $where_part)) : $select_part;
}

/***
 * @param $sql_query
 * @return string
 */
function checkbook_api_adjustContractActiveSql(&$sql_query)
{
  $sql_parts = explode("ORDER BY", $sql_query);
  $select_part = $sql_parts[0];
  $order_by_part = $sql_parts[1];
  if (strpos($select_part, 'l1.') !== false) {
    $order_by_part = str_replace("contract_number", "l1.contract_number", $order_by_part);
  } else {
    $order_by_part = str_replace("contract_number", "l4.contract_number", $order_by_part);
  }
  $sql_query = (count($sql_parts) > 1) ? implode(' ORDER BY ', array($select_part, $order_by_part)) : $select_part;
}

/**
 * @param $data_set
 * @param $parameters
 * @param $criteria
 */
function checkbook_api_adjustNYCHAContractsParams(&$data_set, &$parameters, $criteria)
{
  if (isset($parameters['release_approved_year'])) {
    $year = $parameters['release_approved_year'];
    $data_controller_instance = data_controller_get_operator_factory_instance();
    $parameters['agreement_start_year'] = $data_controller_instance->initiateHandler(LessOrEqualOperatorHandler::$OPERATOR__NAME, $year);
    $parameters['agreement_end_year'] = $data_controller_instance->initiateHandler(GreaterOrEqualOperatorHandler::$OPERATOR__NAME, $year);
    unset($parameters['release_approved_year']);
  }
}

/***
 * @param $sql_query
 * @return string
 */
function checkbook_api_adjustNYCHAContractSql(&$sql_query, $criteria)
{
  $year = NULL;
  $sql_parts = explode("WHERE", $sql_query);
  $select_part = $sql_parts[0];
  $where_part = $sql_parts[1];
  $where_parts = explode('AND', $where_part);

  //Get Year value from WHERE
  foreach ($where_parts as $key => $value) {
    if (strpos($value, 'agreement_start_year') !== false) {
      $values = explode('<=', $value);
      $year = trim(str_replace(')', '', $values[1]));
    }
  }

  // Alter SELECT columns
  if (isset($year)) {
    $select_part = str_replace("release_approved_year", $year . " AS release_approved_year", $select_part);
  }
  if (strtolower($criteria['global']['response_format']) == 'csv') {
    $agreement_level_hyphens = array('release_number', 'line_number', 'item_description', 'commodity_category_descr', 'item_qty_ordered',
      'shipment_number', 'responsibility_center_descr', 'release_approved_date', 'release_line_total_amount',
      'release_line_original_amount', 'release_line_spend_to_date', 'release_total_amount', 'release_original_amount',
      'release_spend_to_date', 'location_descr', 'grant_name', 'expenditure_type_descr', 'display_funding_source_descr',
      'program_phase_descr', 'gl_project_descr');
    $release_level_hyphens = array('number_of_releases', 'line_number', 'item_description', 'commodity_category_descr', 'item_qty_ordered',
      'shipment_number', 'responsibility_center_descr', 'release_line_total_amount',
      'release_line_original_amount', 'release_line_spend_to_date', 'agreement_total_amount', 'agreement_original_amount',
      'agreement_spend_to_date', 'location_descr', 'grant_name', 'expenditure_type_descr', 'display_funding_source_descr',
      'program_phase_descr', 'gl_project_descr');
    $line_level_hyphens = array('number_of_releases', 'release_total_amount', 'release_original_amount', 'release_spend_to_date', 'agreement_total_amount',
      'agreement_original_amount', 'agreement_spend_to_date');

    $text_columns = array('number_of_releases', 'release_number', 'line_number', 'shipment_number', 'item_qty_ordered',
      'release_line_total_amount', 'release_line_original_amount', 'release_line_spend_to_date',
      'release_total_amount', 'release_original_amount', 'release_spend_to_date',
      'agreement_total_amount', 'agreement_original_amount', 'agreement_spend_to_date',
      'po_header_id');

    foreach ($text_columns as $key => $value) {
      $select_part = str_replace($value, "CASE WHEN ((LOWER(record_type) = 'agreement' AND " . ((in_array($value, $agreement_level_hyphens)) ? 'TRUE' : 'FALSE') . ") OR
                                                            (LOWER(record_type) = 'release' AND " . ((in_array($value, $release_level_hyphens)) ? 'TRUE' : 'FALSE') . ") OR
                                                            (LOWER(record_type) = 'line' AND " . ((in_array($value, $line_level_hyphens)) ? 'TRUE' : 'FALSE') . ")) AND " . $value . " IS NULL
                                                        THEN '-'
                                                      ELSE CAST(" . $value . " AS TEXT)
                                                  END AS " . $value, $select_part);
    }

    // Set release_approved_date to hyphen when NULL (only at Agreement level)
    $select_part = str_replace('release_approved_date', "CASE WHEN record_type = 'Agreement' AND release_approved_date IS NULL THEN '-'
                                                    ELSE TO_CHAR(release_approved_date, 'MM/DD/YYYY')
                                                   END AS release_approved_date", $select_part);
    //Set '-' to Start Date and End Date to POs
    $po_date_columns = array('agreement_start_date', 'agreement_end_date');
    foreach ($po_date_columns as $key => $value) {
      $select_part = str_replace($value, "CASE WHEN (upper(agreement_type_name) = 'PURCHASE ORDER') THEN '-'
                                                    ELSE TO_CHAR(" . $value . ",'MM/DD/YYYY')
                                                   END AS " . $value, $select_part);
    }
  }
  $sql_query = (count($sql_parts) > 1) ? implode(' WHERE ', array($select_part, $where_part)) : $select_part;
}

function checkbook_api_change_datasource($datasource, $param)
{
  if ($datasource != null) {
    return str_replace("checkbook:", $datasource . ":", $param);
  } else {
    return $param;
  }
}
