<?php

namespace Drupal\checkbook_api\Utilities;

use Drupal\checkbook_project\MwbeUtilities\MappingUtil;

class SpendingUtil {

  /**
   * Function to adjust the parameters before for the api sql call for spending
   *
   * @param $data_set
   * @param $parameters
   * @param $criteria
   */
  public static function checkbook_api_adjustSpendingParameterFilters(&$data_set, &$parameters, $criteria) {
    if (isset($parameters['minority_type_id'])) {
      $parameters['minority_type_id'] = explode('~', $parameters['minority_type_id']);
    }
    if (isset($parameters['vendor_customer_code']) && strtolower($parameters['vendor_customer_code']) == 'n/a') {
      unset($parameters['vendor_customer_code']);
      $parameters['vendor_name'] = "N/A (PRIVACY/SECURITY)";
    }

  }

  /**
   * Used to adjust columns in the data set after sql call.
   * This is to handle derived columns
   *
   * @param $data_records
   */
  public static function checkbook_api_adjustSpendingDataSetResults(&$data_records) {
    //Derive minority category from minority_type_id
    foreach ($data_records as $key => $data_record) {
      //Derive minority category
      if (isset($data_record['minority_type_name'])) {
        $data_records[$key]['minority_type_name'] = MappingUtil::getMinorityCategoryByName($data_record['minority_type_name']);
      }

      //Derive sub vendor column from vendor type
      if (isset($data_record['vendor_type'])) {
        $data_records[$key]['vendor_type'] = (preg_match('/S/i', $data_record['vendor_type'])) ? 'Yes' : 'No';
        //If Sub vendor field = "Yes" following should be "N/A": Capital Project,Department,Document ID,Expense Category, Associated Prime Vendor
        if ($data_records[$key]['vendor_type'] == 'Yes') {
          $data_records[$key]['reporting_code'] = 'N/A';
          $data_records[$key]['department_name'] = 'N/A';
          $data_records[$key]['disbursement_number'] = 'N/A';
          $data_records[$key]['expenditure_object_name'] = 'N/A';
        }
        else { //If Sub vendor field = "No" following should be "N/A": Associated Prime Vendor
          $data_records[$key]['prime_vendor_name'] = 'N/A';
        }
      }
    }

  }

  /***
   * @param $sql_query
   *
   * @return string
   */
  public static function checkbook_api_adjustSpendingSql(&$sql_query) {
    $alias = '';
    if (strpos($sql_query, 'l1.') !== FALSE) {
      $alias = 'l1.';
    }
    else {
      if (strpos($sql_query, 'l3.') !== FALSE) {
        $alias = 'l3.';
      }
    }

    $sql_parts = explode("WHERE", $sql_query);
    $select_part = $sql_parts[0];
    $where_part = $sql_parts[1] ?? '';
    $select_part = str_replace("{$alias}prime_vendor_name", "CASE WHEN {$alias}is_prime_or_sub = 'S' THEN {$alias}prime_vendor_name ELSE 'N/A' END AS prime_vendor_name", $select_part);
    $select_part = str_replace("{$alias}vendor_type", "CASE WHEN {$alias}is_prime_or_sub = 'P' THEN 'No' ELSE 'Yes' END AS vendor_type", $select_part);
    $select_part = str_replace("{$alias}reporting_code", "CASE WHEN {$alias}is_prime_or_sub = 'P' THEN {$alias}reporting_code ELSE 'N/A' END AS reporting_code", $select_part);
    $select_part = str_replace("{$alias}department_name", "CASE WHEN {$alias}is_prime_or_sub = 'P' THEN {$alias}department_name ELSE 'N/A' END AS department_name", $select_part);
    $select_part = str_replace("{$alias}disbursement_number", "CASE WHEN {$alias}is_prime_or_sub = 'P' THEN {$alias}disbursement_number ELSE 'N/A' END AS disbursement_number", $select_part);
    $select_part = str_replace("{$alias}expenditure_object_name", "CASE WHEN {$alias}is_prime_or_sub = 'P' THEN {$alias}expenditure_object_name ELSE 'N/A' END AS expenditure_object_name", $select_part);

    $column = 'minority_type_name';
    $minority_check = <<<SQLEND
  CASE
  WHEN {$alias}{$column}= 2 THEN 'Black American'
WHEN {$alias}{$column}= 3 THEN 'Hispanic American'
WHEN {$alias}{$column}= 4 THEN 'Asian American'
WHEN {$alias}{$column}= 5 THEN 'Asian American'
WHEN {$alias}{$column}= 10 THEN 'Asian American'
WHEN {$alias}{$column}= 7 THEN 'Non-M/WBE'
WHEN {$alias}{$column}= 9 THEN 'Women'
WHEN {$alias}{$column}= 11 THEN 'Individuals and Others'
WHEN {$alias}{$column}= 'African American' THEN 'Black American'
WHEN {$alias}{$column}= 'Hispanic American' THEN 'Hispanic American'
WHEN {$alias}{$column}= 'Asian-Pacific' THEN 'Asian American'
WHEN {$alias}{$column}= 'Asian-Indian' THEN 'Asian American'
WHEN {$alias}{$column}= 'Non-Minority' THEN 'Non-M/WBE'
WHEN {$alias}{$column}= 'Caucasian Woman' THEN 'Women'
WHEN {$alias}{$column}= 'Individuals & Others' THEN 'Individuals and Others'
END
SQLEND;
    $select_part = str_replace("{$alias}minority_type_name", $minority_check . " AS minority_type_name", $select_part);
    $sql_query = (count($sql_parts) > 1) ? implode(' WHERE ', [
      $select_part,
      $where_part
    ]) : $select_part;
    return $sql_query;

  }

  /***
   * @param $sql_query
   */
  public static function checkbook_api_adjustNYCHASpendingSql(&$sql_query, $criteria) {
    $year = NULL;
    if (strpos($sql_query, 'WHERE')) {
      $sql_parts = explode("WHERE", $sql_query);
      $select_part = $sql_parts[0];
      $where_part = $sql_parts[1];
    }
    else {//When there is no where condition, avoid replacing ORDER BY columns with data formatting conditions
      $sql_parts = explode("ORDER BY", $sql_query);
      $select_part = $sql_parts[0];
      $where_part = $sql_parts[1];
    }
    // Add hyphens only for csv
    if (strtolower($criteria['global']['response_format']) == 'csv') {
      $select_part = str_replace("document_id", "CASE WHEN document_id IS NULL THEN 'N/A' ELSE document_id END AS document_id", $select_part);
      $spending_sect8_other_hyphens = [
        'agreement_type_name',
        'contract_id',
        'release_number',
        'contract_purpose',
        'display_industry_type_name'
      ];
      $spending_payroll_hyphens = [
        'agreement_type_name',
        'contract_id',
        'release_number',
        'invoice_number',
        'contract_purpose',
        'distribution_line_number',
        'display_industry_type_name',
        'display_funding_source_descr',
        'expenditure_type_description',
        'responsibility_center_description',
        'program_phase_description',
        'gl_project_description'
      ];

      $text_columns = [
        'agreement_type_name',
        'contract_id',
        'release_number',
        'invoice_number',
        'contract_purpose',
        'distribution_line_number',
        'display_industry_type_name',
        'display_funding_source_descr',
        'expenditure_type_description',
        'responsibility_center_description',
        'program_phase_description',
        'gl_project_description'
      ];

      foreach ($text_columns as $key => $value) {
        $select_part = str_replace($value, "CASE WHEN (((display_spending_category_name = 'Other') OR (display_spending_category_name = 'Section 8')) AND " . ((in_array($value, $spending_sect8_other_hyphens)) ? 'TRUE' : 'FALSE') . ") OR
(display_spending_category_name = 'Payroll' AND " . ((in_array($value, $spending_payroll_hyphens)) ? 'TRUE' : 'FALSE') . ") AND " . $value . " IS NULL
THEN '-'
ELSE CAST(" . $value . " AS TEXT)
END AS " . $value, $select_part);
      }
      //Set '-' IF null or less than 0 for amount columns
      $amount_columns = [
        'check_amount',
        'adj_distribution_line_amount',
        'invoice_line_number'
      ];
      foreach ($amount_columns as $key => $value) {
        $select_part = str_replace($value, "CASE WHEN " . $value . " IS NULL THEN CAST ('-' AS TEXT)
ELSE CAST(" . $value . " AS TEXT)
END AS " . $value, $select_part);
      }
    }
    if (strpos($sql_query, 'WHERE')) {
      $sql_query = (count($sql_parts) > 1) ? implode(' WHERE ', [
        $select_part,
        $where_part
      ]) : $select_part;
    }
    else {
      $sql_query = (count($sql_parts) > 1) ? implode(' ORDER BY  ', [
        $select_part,
        $where_part
      ]) : $select_part;
    }
  }

}
