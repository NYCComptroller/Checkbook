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


class GenerateRefDataClass{

    /*private $refDataQueries = array(
        'agency_code_list'=>"SELECT agency_code \"Agency Code\",agency_name \"Agency Name\"  FROM ref_agency where is_display = 'Y' ORDER BY agency_name",
        'vendor_code_list'=>"SELECT vendor_customer_code \"Vendor Code\", legal_name \"Vendor Name\" FROM vendor",
        'department_code_list'=>"SELECT d.department_code \"Department Code\", d.department_name \"Department Name\",a.agency_code \"Agency Code\", a.agency_name \"Agency Name\" FROM ref_department d LEFT OUTER JOIN ref_agency a  ON d.agency_id = a.agency_id ORDER BY d.department_name",

        //Budget
        'budget_code_list' => "SELECT budget_code \"Budget Code\",budget_code_name \"Budget Code Name\"  FROM ref_budget_code ORDER BY budget_code_name",
        'budget_expense_category_code_list' => "SELECT object_class_code \"Expense Category Code\",object_class_name \"Expense Category Name\"  FROM ref_object_class ORDER BY object_class_name",

        //Revenue
        'revneue_class_code_list' => "SELECT revenue_class_code \"Revneue Class Code\",revenue_class_name \"Revneue Class Name\"  FROM ref_revenue_class ORDER BY revenue_class_name",
        'fund_class_code_list' => "SELECT fund_class_code \"Fund Class Code\",fund_class_name \"Fund Class Name\"  FROM ref_fund_class ORDER BY fund_class_name",
        'funding_source_code_list' => "SELECT funding_class_code \"Funding Class Code\",funding_class_name \"Funding Class Name\"  FROM ref_funding_class ORDER BY funding_class_name",
        'revenue_category_code_list' => "SELECT revenue_category_code \"Revenue Category Code\",revenue_category_name \"Revenue Category Name\"  FROM ref_revenue_category ORDER BY revenue_category_name",
        'revenue_source_code_list' => "SELECT revenue_source_code \"Revenue Source Code\",revenue_source_name \"Revenue Source Name\"  FROM ref_revenue_source ORDER BY revenue_source_name",

        //Spending
        'payee_code_list' => "SELECT vendor_customer_code \"Payee Code\",legal_name \"Payee Name\"  FROM vendor ORDER BY legal_name",
        'expense_code_list' => "SELECT DISTINCT document_id \"Expense Id\"  FROM history_master_agreement ORDER BY document_id",
        'spending_expense_category_code_list' => "SELECT expenditure_object_code \"Expense Category Code\",expenditure_object_name \"Expense Catergory Name\" FROM ref_expenditure_object ORDER BY expenditure_object_name",
        'capital_project_code_list' => "SELECT DISTINCT reporting_code \"Capital Project Code\" FROM disbursement_line_item_details where coalesce(reporting_code,'') <> '' ORDER BY reporting_code",
        'document_id_code_list' => "SELECT DISTINCT disbursement_number \"Document Id\" FROM disbursement_line_item_details ORDER BY disbursement_number",
        'spending_category_code_list' => "SELECT DISTINCT spending_category_name \"Spending Category Name\", spending_category_code \"Spending Category Code\" FROM ref_spending_category");

    private function checkDirectory(){
        global $conf;

        $dir = variable_get('file_public_path','sites/default/files') .'/'. $conf['check_book']['data_feeds']['output_file_dir'] . '/' . $conf['check_book']['ref_data_dir'];

        if(!file_prepare_directory($dir,FILE_CREATE_DIRECTORY)){
            LogHelper::log_error("Could not prepare directory $dir for generating reference data.");
            return NULL;
        }

        if(!@chmod($dir,0777)){
            LogHelper::log_error("Could not change permissions to 777 for $dir.");
            return NULL;
        }

        return $dir;
    }

    public function execute($codeListName = NULL){
        $failure = -1;
        $success = 1;

        $output = $failure;
        $dir = $this->checkDirectory();

        if(!isset($dir)){
            return $output;
        }

        if(isset($this->refDataQueries[$codeListName])){
            try{
                $query  = $this->refDataQueries[$codeListName];
                $this->generateData($codeListName, $query, $dir);
                $output = $success;
            }catch(Exception $e){
                LogHelper::log_error("Error generating $codeListName ref data: ".$query .". Exception is: ".$e);
            }
        }else{
            foreach($this->refDataQueries as $fileName => $refDataQuery){
                try{
                    $this->generateData($fileName,$refDataQuery, $dir);
                }catch(Exception $e){
                    LogHelper::log_error("Error generating $fileName ref data: ".$refDataQuery .". Exception is: ".$e);
                }
            }
            $output = $success;
        }

        return $output;
    }

    private function generateData($fileName,$refDataQuery, $dir){
        global $conf;

        $connection = get_datafeed_connection();

        $file = $fileName . '.csv';
        $query =  "COPY( $refDataQuery ) TO '"
            . $conf['check_book']['data_feeds']['db_file_dir']
            .'/' . $conf['check_book']['ref_data_dir'] . '/' . $file
            . "'  WITH DELIMITER ',' CSV HEADER QUOTE '\"';";

        try{
            LogHelper::log_notice("DB query for generating $fileName ref data: ".$query);
            $connection->query($query);
            LogHelper::log_notice("Completed executing DB query for $fileName. " . (is_file($dir.'/'.$file) ? "Generated file Name : $file" : "Could not generated file for $fileName"));
        }catch(Exception $e){
            LogHelper::log_error("Error executing DB query for generating $fileName ref data: ".$query .". Exception is: ".$e);
            throw $e;
        }
    }
    */

    /*function checkbook_api_load_ref_data($codeListName){
        global $conf;

        $codeListName .= '_code_list';
        $fileName = $codeListName . '.csv';
        $file = variable_get('file_public_path','sites/default/files') .'/'. $conf['check_book']['data_feeds']['output_file_dir'] . '/' . $conf['check_book']['ref_data_dir'] . '/' .$fileName;

        drupal_add_http_header("Content-Type", "text/csv");
        drupal_add_http_header("Content-Disposition", "attachment; filename=$fileName");
        drupal_add_http_header("Pragma", "cache");
        drupal_add_http_header("Expires", "-1");

        if(!is_file($file)){
            $generateRefData = new GenerateRefData();
            $generateRefData->execute($codeListName);
        }

        if(is_file($file)){
            $data = file_get_contents($file);
            drupal_add_http_header("Content-Length",strlen($data));
            echo $data;
        }else{
            echo "Data is not generated. Please contact support team.";
        }
    }*/
}
