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


global $conf, $databases;

$success = 1;
$failure = -1;

$ref_data_queries = array(
  'agency_code_list' => "SELECT agency_code \\\"Agency Code\\\",agency_name \\\"Agency Name\\\"  FROM ref_agency where is_display = 'Y' ORDER BY agency_name",
  'vendor_code_list' => "SELECT vendor_customer_code \\\"Vendor Code\\\", legal_name \\\"Vendor Name\\\" FROM vendor",
  'department_code_list' => "SELECT distinct d.department_code \\\"Department Code\\\", d.department_name \\\"Department Name\\\",a.agency_code \\\"Agency Code\\\", a.agency_name \\\"Agency Name\\\" FROM ref_department d LEFT OUTER JOIN ref_agency a  ON d.agency_id = a.agency_id ORDER BY d.department_name",
  'mwbe_code_list' => "SELECT DISTINCT minority_type_name \\\"Minority Type Name\\\", minority_type_id \\\"Minority Type Id\\\" FROM ref_minority_type",
  'industry_code_list' => "SELECT DISTINCT industry_type_name \\\"Industry Type Name\\\", industry_type_id \\\"Industry Type Id\\\" FROM ref_industry_type",

  // Budget:
  'budget_code_list' => "SELECT distinct budget_code \\\"Budget Code\\\",attribute_name \\\"Budget Code Name\\\"  FROM ref_budget_code ORDER BY attribute_name",
  'budget_expense_category_code_list' => "SELECT distinct object_class_code \\\"Expense Category Code\\\",object_class_name \\\"Expense Category Name\\\"  FROM ref_object_class ORDER BY object_class_name",

  // Revenue:
  'revenue_class_code_list' => "SELECT distinct revenue_class_code \\\"Revneue Class Code\\\",revenue_class_name \\\"Revneue Class Name\\\"  FROM ref_revenue_class ORDER BY revenue_class_name",
  'fund_class_code_list' => "SELECT distinct fund_class_code \\\"Fund Class Code\\\",fund_class_name \\\"Fund Class Name\\\"  FROM ref_fund_class where fund_class_name = 'General Fund' ORDER BY fund_class_name",
  'funding_source_code_list' => "SELECT distinct funding_class_code \\\"Funding Class Code\\\",funding_class_name \\\"Funding Class Name\\\"  FROM ref_funding_class ORDER BY funding_class_name",
  'revenue_category_code_list' => "SELECT distinct revenue_category_code \\\"Revenue Category Code\\\",revenue_category_name \\\"Revenue Category Name\\\"  FROM ref_revenue_category ORDER BY revenue_category_name",
  'revenue_source_code_list' => "SELECT distinct revenue_source_code \\\"Revenue Source Code\\\",revenue_source_name \\\"Revenue Source Name\\\"  FROM ref_revenue_source ORDER BY revenue_source_name",

  // Spending:
  'payee_code_list' => "SELECT vendor_customer_code \\\"Payee Code\\\",legal_name \\\"Payee Name\\\"  FROM vendor ORDER BY legal_name",
  'expense_code_list' => "SELECT DISTINCT document_id \\\"Expense Id\\\"  FROM history_master_agreement ORDER BY document_id",
  'spending_expense_category_code_list' => "SELECT DISTINCT expenditure_object_code \\\"Expense Category Code\\\",expenditure_object_name \\\"Expense Catergory Name\\\" FROM ref_expenditure_object ORDER BY expenditure_object_name",
  'capital_project_code_list' => "SELECT DISTINCT reporting_code \\\"Capital Project Code\\\" FROM disbursement_line_item_details where coalesce(reporting_code,'') <> '' ORDER BY reporting_code",
  'document_id_code_list' => "SELECT DISTINCT disbursement_number \\\"Document Id\\\" FROM disbursement_line_item_details ORDER BY disbursement_number",
  'spending_category_code_list' => "SELECT DISTINCT spending_category_name \\\"Spending Category Name\\\", spending_category_code \\\"Spending Category Code\\\" FROM ref_spending_category",

);

$dir = variable_get('file_public_path', 'sites/default/files') . '/' . $conf['check_book']['data_feeds']['output_file_dir'];
if (!file_prepare_directory($dir, FILE_CREATE_DIRECTORY)) {
  LogHelper::log_error("Could not prepare directory $dir for generating reference data.");
  echo $failure;
  return;
}
/*if(!is_link($dir) && !@chmod($dir,0777)){
    LogHelper::log_error("Could not change permissions to 777 for $dir.");
    echo $failure;
    return;
}*/

$dir .= '/' . $conf['check_book']['ref_data_dir'];
if (!file_prepare_directory($dir, FILE_CREATE_DIRECTORY)) {
  LogHelper::log_error("Could not prepare directory $dir for generating reference data.");
  echo $failure;
  return;
}
/*if(!is_link($dir) && !@chmod($dir,0777)){
  LogHelper::log_error("Could not change permissions to 777 for $dir.");
  echo $failure;
  return;
}*/

foreach ($ref_data_queries as $file_name => $ref_data_query) {
  $file = DRUPAL_ROOT . '/' . $dir . '/' . $file_name . '.csv';
  $command = _checkbook_psql_command();
  $command .= " -c \"\\\\COPY (" . $ref_data_query . ") TO '"
    . $file
    . "'  WITH DELIMITER ',' CSV HEADER QUOTE '\\\"' ESCAPE '\\\"' \" ";

  try {
    LogHelper::log_notice("Command for generating $file_name ref data: " . $command);
    shell_exec($command);
    LogHelper::log_notice("Completed executing DB query for $file_name. " . (is_file($file) ? "Generated file : $file" : "Could not generate file for $file_name"));
  }
  catch (Exception $e) {
    $value = TextLogMessageTrimmer::$LOGGED_TEXT_LENGTH__MAXIMUM;
    TextLogMessageTrimmer::$LOGGED_TEXT_LENGTH__MAXIMUM = NULL;
    LogHelper::log_error($e);
    LogHelper::log_error("Erorr executing DB query for generating $file_name ref data: " . $command . ". Exception is: " . $e);
    TextLogMessageTrimmer::$LOGGED_TEXT_LENGTH__MAXIMUM = $value;

    echo $failure;
    return;
  }
}

echo $success;
