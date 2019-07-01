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


class WidgetUtil
{

    static function getLabel($labelAlias){
        $dynamic_labelAlias = array("current_modified","previous_modified","previous_1_modified","previous_2_modified",
                                    "recognized_current","recognized_1","recognized_2","recognized_3");
        $labelAliases_br = array("spent_to_date", "original_amount", "current_amount", "dollar_diff", "percent_diff",
                                 "no_of_contracts", "num_sub_contracts", "ytd_spending", "total_contract_amount",
                                 "percent_spending", "ytd_spending_sub_vendors", "sub_vendors_percent_paid",
                                 "num_sub_vendors", "ytd_spending_agency");
        if(in_array($labelAlias,$dynamic_labelAlias)){
            $year = _getYearValueFromID(RequestUtilities::get('year'));
            $dynamic_labels = array("current_modified" => "Modified<br/>".$year,
                                    "previous_modified" => "Modified<br/>".($year-1),
                                    "previous_1_modified" => "Modified<br/>".($year-2),
                                    "previous_2_modified" => "Modified<br/>".($year-3),
                                    "recognized_current" => "Recognized<br/>".$year,
                                    "recognized_1" => "Recognized<br/>".($year+1),
                                    "recognized_2" => "Recognized<br/>".($year+2),
                                    "recognized_3" => "Recognized<br/>".($year+3));

            return str_replace(array('<br/>','<br>'),' ',$dynamic_labels[$labelAlias]);
        }else if(in_array($labelAlias,$labelAliases_br)){
            return self::$labels[$labelAlias];
        }else{
            return str_replace(array('<br/>','<br>'),' ',self::$labels[$labelAlias]);
        }
    }

    static function getLabelDiv($labelAlias) {
        return self::generateLabelMapping($labelAlias);
    }

    static function generateLabelMapping($labelAlias, $labelOnly = false){
        $dynamic_labelAlias = array("current_modified","previous_modified","previous_1_modified","previous_2_modified",
                                    "recognized_current","recognized_1","recognized_2","recognized_3");
        if(in_array($labelAlias,$dynamic_labelAlias)){
            $year = _getYearValueFromID(RequestUtilities::get('year'));
            $dynamic_labels = array("current_modified" => "Modified<br/>".$year,
                                    "previous_modified" => "Modified<br/>".($year-1),
                                    "previous_1_modified" => "Modified<br/>".($year-2),
                                    "previous_2_modified" => "Modified<br/>".($year-3),
                                    "recognized_current" => "Recognized<br/>".$year,
                                    "recognized_1" => "Recognized<br/>".($year+1),
                                    "recognized_2" => "Recognized<br/>".($year+2),
                                    "recognized_3" => "Recognized<br/>".($year+3));

            $label = "<div><span>". $dynamic_labels[$labelAlias] ."</span></div>";

        }else{
            $label = NULL;
            if($labelOnly){
                $label = self::getLabel($labelAlias);
            }else{
                $label  = "<div><span>". self::$labels[$labelAlias] ."</span></div>";
            }
        }

        return $label;
    }

    static $labels = array(
        "contract_id" => "Contract<br/>ID",
        "contract_type" => "Contract<br/>Type",
        "contract_purpose" => "Purpose",
        "contract_agency" => "Contracting<br/>Agency",
        "vendor_name" => "Vendor<br/>Name",
        "original_amount" => "Original<br/>Amount",
        "current_amount" => "Current<br/>Amount",
        "spent_to_date" => "Spent To<br/>Date",
        "paid_to_date" => "Paid To<br/>Date",
        "dollar_diff" => "Dollar<br/>Difference",
        "percent_diff" => "Percent<br/>Difference",
        "no_of_contracts" => "Number of<br/>Contracts",
        "no_of_modifications" => "Number of<br/>Modifications",
        "award_method" => "Award<br/>Method",
        "dept_name" => "Department",
        "agency_name" => "Agency",
        "contract_size" => "Contract<br/>Size",
        "contract_industry" => "Industry",
        "prime_contract_industry" => "Prime Contract Industry",
        "sub_contract_industry" => "Sub Contract Industry",
        "title" => "Title",
        "vendor"=>"Vendor",
        "month" => "Month",
        "amount" => "Amount",
        "payroll_type" => "Payroll<br/>Type",
        "annual_salary" => "Annual<br/>Salary",
        "combined_annual_salary" => "Combined<br/>Annual Salary",
        "total_combined_annual_salary" => "Total Combined Annual<br/>Salary",
        "hourly_rate" => "Hourly<br/>Rate",
        "daily_wage" => "Daily<br/>Wage",
        "pay_rate" => "Pay<br/>Rate",
        "pay_frequency" => "Pay<br/>Frequency",
        "pay_date" => "Pay<br/>Date",
        "gross_pay_ytd" => "Gross<br/>Pay YTD",
        "combined_gross_pay_ytd" => "Combined<br/>Gross Pay YTD",
        "combined_gross_pay" => "Combined<br/>Gross Pay",
        "total_combined_gross_pay_ytd" => "Total Combined Gross<br/>Pay YTD",
        "gross_pay" => "Gross<br/>Pay",
        "base_pay_ytd" => "Base<br/>Pay YTD",
        "combined_base_pay_ytd" => "Combined<br/>Base Pay YTD",
        "combined_base_pay" => "Combined<br/>Base Pay",
        "total_combined_base_pay_ytd" => "Total Combined Base<br/>Pay YTD",
        "base_pay" => "Base<br/>Pay",
        "other_pay_ytd" => "Other<br/>Payments YTD",
        "combined_other_pay_ytd" => "Combined Other<br/>Payments YTD",
        "combined_other_pay" => "Combined Other<br/>Payments",
        "total_combined_other_pay_ytd" => "Total Combined Other<br/>Payments YTD",
        "other_pay_1_ytd" => "Other<br/>Payment YTD",
        "other_pays" => "Other<br/>Payments",
        "other_pay" => "Other<br/>Payment",
        "overtime_pay_ytd" => "Overtime<br/>Payments YTD",
        "combined_overtime_pay_ytd" => "Combined Overtime<br/>Payments YTD",
        "combined_overtime_pay" => "Combined Overtime<br/>Payments",
        "total_combined_overtime_pay_ytd" => "Total Combined Overtime<br/>Payments YTD",
        "overtime_pay_1_ytd" => "Overtime<br/>Payment YTD",
        "overtime_pay" => "Overtime<br/>Payment",
        "no_of_ot_employees" => "Number of<br/>Overtime Employees",
        "total_no_of_ot_employees" => "Total Number of<br/>Overtime Employees",
        "no_of_sal_employees" => "Number of<br/>Salaried Employees",
        "total_no_of_sal_employees" => "Total Number of<br/>Salaried Employees",
        "no_of_non_sal_employees" => "Number of<br/>Non-Salaried Employees",
        "total_no_of_non_sal_employees" => "Total Number of<br/>Non-Salaried Employees",
        "no_of_employees" => "Number of<br/>Employees",
        "total_no_of_employees" => "Total Number of<br/>Employees",
        "ytd_spending" => "YTD<br/>Spending",
        "total_contract_amount" => "Total Contract<br/>Amount",
        "expense_category" => "Expense<br/>Category",
        "fiscal_year" => "Fiscal<br/>Year",
        "budget_fiscal_year" => "Budget Fiscal<br/>Year",
        "no_of_mod" => "Number Of<br/>Modifications",
        "version_number" => "Version<br/>Number",
        "revision_number" => "Revision<br/>Number",
        "revision_total_amount" => "Revision<br/>Total Amount",
        "version" => "Version",
        "start_date" => "Start<br/>Date",
        "end_date" => "End<br/>Date",
        "reg_date" => "Registration<br/>Date",
        "recv_date" => "Received<br/>Date",
        "last_mod_date" => "Last Modified<br/>Date",
        "increase_decrease" => "Increase/<br>Decrease",
        "version_status" => "Version<br/>Status",
        "release_status" => "Release<br/>Status",
        "transaction_status" => "Transaction<br/>Status",
        "contract_status" => "Status",
        "document_id" => "Document<br/>ID",
        "check_amount" => "Check<br/>Amount",
        "amount_spent" => "Amount<br/>Spent",
        "no_of_transactions" => "Number Of<br/>Transactions",
        "pin" => "PIN",
        "apt_pin" => "APT<br/>PIN",
        "fms_doc_id" => "FMS Document/<br/>Parent Contract ID",
        "orig_or_mod" => "Original/<br/>Modified",
        "worksites_name"=>"Location of Work Site",
        "voucher_amount"=>"Voucher Amount",
        "encumbered_amount"=>"Encumbered<br/>Amount",
        "loc_site"=>"Location of Work Site",
        "sol_per_cont"=>"# of Solicitations per Contract",
        "fms_doc"=>"FMS Document",
        "resp_per_sol"=>"# of Responses per Solicitation",
        "payee_name"=>"Payee<br/>Name",
        "issue_date"=>"Issue<br/>Date",
        "date"=>"Date",
        "item_description"=>"Item<br/>Description",
        "item_category"=>"Item<br/>Category",
        "capital_project"=>"Capital<br/>Project",
        "spending_category"=>"Spending<br/>Category",
        "spending_amount"=>"Amount",
        "adopted"=>"Adopted",
        "modified"=>"Modified",
        "committed"=>"Committed",
        "remaining"=>"Remaining",
        "pre_encumbered"=>"Pre<br/>Encumbered",
        "encumbered"=>"Encumbered",
        "accrued_expense"=>"Accrued<br/>Expense",
        "cash_payments"=>"Cash<br/>Payments",
        "post_adjustments"=>"Post<br/>Adjustments",
        "budget_code_category"=>"Expense Budget<br>Category",
        "budget_code_code"=>"Expense Budget<br>Code",
        "recognized"=>"Recognized",
        "revenue_category"=>"Revenue<br/>Category",
        "revenue_class"=>"Revenue<br/>Class",
        "revenue_source"=>"Revenue<br/>Source",
        "funding_class"=>"Funding<br/>Class",
        "fund_class"=>"Fund<br/>Class",
        "funding_source"=>"Funding<br/>Source",
        "cls_classification_name"=>"Closing Classification<br/>Name",
        "other_years"=>"Other<br/>Years",
        "year"=>"Year",
        "budget_name"=>"Budget<br/>Name",
        "budget_code"=>"Budget<br/>Code",
        "commodity_line"=>"Commodity<br/>Line",
        "entity_contact_num"=>"Entity<br/>Contract # ",
        "prime_vendor_name"=>"Prime Vendor<br/>Name",
        "prime_vendor"=>"Prime<br/>Vendor",
        "tot_edc_con"=>"Total number of<br/>NYC EDC Contracts",
        "total_number_nycha_contracts"=>"Total number of<br/>NYCHA Contracts",
        "vendor_address"=>"Vendor<br/>Address",
        "address"=>"Address",
        "prime_vendor_address"=>"Prime Vendor<br/>Address",
        "percent_spending"=>"%<br/>Spending",
        "sub_vendor_name"=>"Sub<br/>Vendor",
        "sub_vendor_nowrap"=>"Sub Vendor",
        "is_sub_vendor"=>"Sub<br/>Vendor",
        "industry_name"=>"Industry",
        "sub_contract_reference_id"=>"Sub Contract<br/>Reference ID",
        "mwbe_category"=>"M/WBE<br/>Category",
        "prime_mwbe_category"=>"Prime Vendor<br/>M/WBE Category",
        "sub_mwbe_category"=>"Sub Vendor<br/>M/WBE Category",
        "num_sub_vendors"=>"Number of<br/>Sub Vendors",
        "num_sub_contracts"=>"Number of<br/>Sub Contracts",
        "ytd_spending_sub_vendors"=>"YTD Spending<br/>Sub Vendors",
        "ytd_spending_agency"=>"YTD Spending<br/>Agency",
        "sub_vendors_percent_paid"=>"% Paid<br/>Sub Vendors",
        "mwbe_ethnicity"=>"M/WBE<br/>Ethnicity",
        "sub_contract_purpose" => "Sub Contract<br/>Purpose",
        "associated_prime_vendor" => "Associated Prime<br/>Vendor",
        "employee_number" => "Employee Number",
        "contract_includes_sub" => "Contract Includes<br/>Sub Vendors",
        "not_required_by_prime" => "Response Not<br/>Required by Prime",
        "no_response_status" => "No Response<br/>by Prime",
        "reported_sub_count" => "Contracts With<br/>Sub Vendors Reported",
        "reported_no_sub_count" => "Contracts Without<br/>Sub Vendors Reported",
        "reported_no_details_count" => "Sub Vendors Reported<br/>Without Details",
        "contracts_reported_with_subvendors" => "Contracts with<br/>Sub Vendors Reported",
        "no_of_subvendor_contracts_submitted" => "Number of Sub Vendor<br/>Contracts Submitted",
        "acco_reviewing" => "Acco<br/>Reviewing",
        "acco_approved" => "Acco<br/>Approved",
        "acco_rejected" => "Acco<br/>Rejected",
        "acco_cancelled" => "Acco<br/>Canceled",
        "assoc_releases" => "Assoc. Releases",
        "subvendor_mwbe_category" => "Sub Vendor's M/WBE<br/>Category",
        "subvendor_status_pip" => "Sub Vendor<br/>Status in PIP",
        "spend_to_date" => "Spend to<br/> Date",
        "delta_amount" => "Delta<br/>Amount",
        "po_number"=>"PO Number",
        "pa_number"=>"PA Number",
        "ba_number"=>"BA Number",
        "grant_name"=>"Grant<br/>Name",
        "quantity_ordered"=>"Quantity<br/>Ordered",
        "purchase_order_number" => "Purchase<br/>Order Number",
        "purchase_order_type" => "Purchase<br/>Order Type",
        "release_number" => "Release<br/>Number",
        "shipment_number" => "Shipment<br/>Number",
        "distribution_number" => "Distribution<br/>Number",
        "line_number" => "Line<br/>Number",
        "line_current_amount" => "Line Current<br/>Amount",
        "line_original_amount" => "Line Original <br/> Amount",
        "line_spend_to_date" => "Line Invoiced<br/>Amount",
        "agreement_start_date" => "Agreement<br/>Start Date",
        "agreement_end_date" => "Agreement<br/>End Date",
        "agreement_current_amount"=>"Agreement<br/>Current Amount",
        "agreement_original_amount"=>"Agreement<br/>Original Amount",
        "agreement_spend_to_date"=>"Agreement<br/>Spent to Date",
        "contract_current_amount"=>"Contract Current<br/>Amount",
        "contract_original_amount"=>"Contract Original<br/>Amount",
        "contract_spend_to_date"=>"Contract Spent to<br/>Date",
        "release_current_amount" => "Release Current<br/>Amount",
        "release_original_amount" => "Release Original<br/>Amount",
        "release_spend_to_date" => "Release Spent to<br/>Date",
        "approved_date" => "Approved<br/>Date",
        "release_approved_date" => "Approved<br/>Date",
        "canceled_date" => "Canceled<br/>Date",
        "location" => "Location",
        "grant" => "Grant<br/>Name",
        "expenditure_type" => "Expenditure<br/>Type",
        "funding_source" => "Funding<br/>Source",
        "responsibility_center" => "Responsibility<br/>Center",
        "program" => "Program",
        "project" => "Project",
        "commodity_category" => "Commodity<br/>Category",
        "non_salary_rate" => "Non-Salaried<br/>Rates",
        "nycha_payment" => "NYCHA<br/>Payment",
        "category_type" => "Category<br/>Type",
        "record_type" => "Record<br/>Type",
        "number_of_releases" => "Number of<br/>Releases",
    );

    //For number cols, need to find out if they are uniform number of digits for formatting
    static function populateMaxColumnLength(&$rows) {

        $number_rows = count($rows);
        $number_columns = count($rows[0]['columns']);
        $col_index = 0;

        while ($col_index < $number_columns) {
            $max_length = 0;
            for ($row_index = 0; $row_index < $number_rows; $row_index++) {
                if(isset($rows[$row_index]['columns'])) {
                    $column_length = strlen($rows[$row_index]['columns'][$col_index]['value']);
                    $max_length = $column_length > $max_length ? $column_length : $max_length;
                }
            }
            for ($row_index = 0; $row_index < $number_rows; $row_index++)
                if(isset($rows[$row_index]['columns']))
                    $rows[$row_index]['columns'][$col_index]['max_column_length'] = $max_length;
            $col_index++;
        }
    }

    static function generateTable($table_definition, $is_main = true) {

        $html = '';
        $border = "";
//        $border = 'border: 1px solid black !important; ';

        $header_title = isset($table_definition["header"]["title"]) ? $table_definition["header"]["title"] : null;
        $header_columns = isset($table_definition["header"]["columns"]) ? $table_definition["header"]["columns"] : null;
        $table_body = isset($table_definition["body"]) ? $table_definition["body"] : null;
        $table_rows = isset($table_definition["body"]["rows"]) ? $table_definition["body"]["rows"] : null;


        //Title
        if($header_title)
            $html .= $header_title;

        if($is_main)
            $html .= "<table class='dataTable outerTable' style='border: 1px solid #CACACA;'>";
        else
            $html .= "<table class='sub-table dataTable'>";

        //Header row
        if($header_columns) {
            $html .= "<thead>";
            $html .= "<tr>";
//            $html .= "<tr style='border: 1px solid black !important;'>";
            $html .= WidgetUtil::generateHeaderColumns($header_columns);
            $html .= "</tr>";
            $html .= "</thead>";
        }

        //Table body
        if(isset($table_body)) {
            $html .= "<tbody>";
            //Table rows
            if(isset($table_rows)) {
                $alternating_row_count = 0;
                $inner_tbl_count = 0;
                $outer_table_count = 0;
                for ($row_index = 0; $row_index < count($table_rows); $row_index++) {
                    if(isset($table_rows[$row_index]['columns'])) {
                        if ($alternating_row_count % 2 == 0)
                            $class = "even outer";
                        else
                            $class = "odd outer";
                        $alternating_row_count++;
                        WidgetUtil::populateMaxColumnLength($table_rows);
//                        $html .= "<tr class='".$class."' style='border: 1px solid black !important;'>";
                        $html .= "<tr class='".$class."' >";
                        $html .= WidgetUtil::generateColumns($table_rows[$row_index]['columns']);
                        $html .= "</tr>";
                    }
                    elseif(isset($table_rows[$row_index]['child_tables'])) {
                    	$display_main = $outer_table_count > 0 ? "display: none;" : "";
                    	$outer_table_count++;
                        $col_span = count($table_rows[$row_index-1]['columns']);
                        $html .= "<tr class='showHide' style='" .$display_main . "'>";
                        $html .= "<td colspan='".$col_span."'>";
                        $html .= "<div>";
                        for ($tbl_index = 0; $tbl_index < count($table_rows[$row_index]['child_tables']); $tbl_index++) {
//                            $html .= "<tr class='showHide' style='border: 1px solid black !important;'>";
                        	$html .=  WidgetUtil::generateTable($table_rows[$row_index]['child_tables'][$tbl_index]);
                        }
                        $html .= "</div>";
                        $html .= "</td>";
                        $html .= "</tr>";
                    }
                    elseif(isset($table_rows[$row_index]['embed_node'])) {
                        $display_main = $outer_table_count > 0 ? "display: none;" : "";
                        $outer_table_count++;
                        $col_span = count($table_rows[$row_index-1]['columns']);
                        $html .= "<tr class='showHide' style='" .$display_main . "'>";
                        $html .= "<td colspan='".$col_span."'>";
                        $html .= "<div>";
                        for ($tbl_index = 0; $tbl_index < count($table_rows[$row_index]['embed_node']); $tbl_index++) {
//                            $html .= "<tr class='showHide' style='border: 1px solid black !important;'>";
                            $html .=  $table_rows[$row_index]['embed_node'][$tbl_index];
                        }
                        $html .= "</div>";
                        $html .= "</td>";
                        $html .= "</tr>";
                    }
                    elseif(isset($table_rows[$row_index]['inner_table'])) {
                        $display = $inner_tbl_count > 0 ? "display: none;" : "";
                        $inner_tbl_count++;
                        $col_span = count($table_rows[$row_index-1]['columns']);
//                        $html .= "<tr class='showHide' style='".$display." border: 1px solid black !important;'>";
                        $html .= "<tr class='showHide' style='".$display."'>";
                        $html .= "<td colspan='".$col_span."'>";
                        $html .= "<div class='scroll'>";
                        $html .=  WidgetUtil::generateTable($table_rows[$row_index]['inner_table'],false);
                        $html .= "</div>";
                        $html .= "</td>";
                        $html .= "</tr>";
                    }
                }
            }
            $html .= "</tbody>";
        }
        else {
            $html .= "<tbody>";
            $html .= "<tr class='odd'>";
            $html .= "<td class='dataTables_empty' valign='top' colspan='" . count($header_columns). "'>";
            $html .= "<div id='no-records-datatable' class='clearfix'>";
            $html .= "<span>No Matching Records Found</span>";
            $html .= "</div>";
            $html .= "</td>";
            $html .= "</tr>";
            $html .= "</tbody>";
        }
        $html .= "</table>";

        return $html;
    }

    static function generateHeaderColumns($header_columns) {
        $html = "";
        $border = "";
//        $border = 'border: 1px solid black !important; ';

        foreach($header_columns as $index => $header_column) {

            $value = $header_column['value'];
            $type = $header_column['type'];
            $first_column = $index == 0;

            if($first_column) {
                $html .= '<th style="'.$border.'text-align: left !important; vertical-align: middle;">';
                $html .= '<span style="margin:8px 0px 8px 15px!important; display:inline-block; text-align: center !important;">'. $value .'</span></th>';
            }
            else {
                if($type == 'number') {
                    $html .= '<th style="'.$border.'text-align: center !important; vertical-align: middle; padding-right:6% !important">';
                    $html .= '<span style="margin:8px 0px 8px 0px!important;display:inline-block; text-align: center !important;">'. $value .'</span></th>';
                }
                else {
                    $html .= '<th style="'.$border.'text-align: left !important; vertical-align: middle; padding-left:10px !important">';
                    $html .= '<span style="margin:8px 0px 8px 0px!important;display:inline-block; text-align: center !important;">'. $value .'</span></th>';
                }
            }
        }
        return $html;
    }

    static function generateColumns($columns) {

        $html = "";
        $border = "";
//        $border = 'border: 1px solid black !important; ';

        foreach($columns as $index => $column) {

            $value = $column['value'];
            $type = $column['type'];
            $max_column_length = $column['max_column_length'];
            $first_column = $index == 0;

            if($first_column) {
                $html .= '<td style="'.$border.'text-align: left !important; vertical-align: middle; padding: 10px 5px !important;">';
                $html .= '<span style="margin:8px 0px 8px 15px!important; display:inline-block; text-align: left !important;">'. $value .'</span></td>';
            }
            else {
                //Numbers are center aligned and padded to align right
                if($type == 'number') {
                    $column_length = strlen($value);
                    if($column_length < $max_column_length) {
                        $value = str_pad($value, $max_column_length, " ", STR_PAD_LEFT);
                        $value = str_replace(" ", "&nbsp;", $value);
                    }
                    $html .= '<td style="'.$border.'text-align: center !important; vertical-align: middle; padding-right:6% !important">';
                    $html .= '<span style="display:inline-block; text-align: right !important;">'. $value .'</span></td>';
                }
                else if($type == 'number_link') {
                    $link_value = $column['link_value'];
                    $actual_value = $value;
                    $column_length = strlen($value);
                    if($column_length < $max_column_length) {
                        $value = str_pad($value, $max_column_length, " ", STR_PAD_LEFT);
                        $value = str_replace(" ", "&nbsp;", $value);
                        $value = str_replace($actual_value, $value, $link_value);
                    } else {
                        $value = $link_value;
                    }
                    $html .= '<td style="'.$border.'text-align: center !important; vertical-align: middle; padding-right:6% !important">';
                    $html .= '<span style="display:inline-block; text-align: right !important;">'. $value .'</span></td>';
                }
                //Text is left aligned
                else {
                    $html .= '<td style="'.$border.'text-align: left !important; vertical-align: middle; padding-left:10px !important">';
                    $html .= '<span style="display:inline-block; text-align: left !important;">'. $value .'</span></td>';
                }
            }
        }
        return $html;
    }

    static function generateLabelMappingNoDiv($labelAlias, $labelOnly = false){
        $dynamic_labelAlias = array("current_modified","previous_modified","previous_1_modified","previous_2_modified",
            "recognized_current","recognized_1","recognized_2","recognized_3");
        if(in_array($labelAlias,$dynamic_labelAlias)){
            $year = _getYearValueFromID(RequestUtilities::get('year'));
            $dynamic_labels = array("current_modified" => "Modified<br/>".$year,
                "previous_modified" => "Modified<br/>".($year-1),
                "previous_1_modified" => "Modified<br/>".($year-2),
                "previous_2_modified" => "Modified<br/>".($year-3),
                "recognized_current" => "Recognized<br/>".$year,
                "recognized_1" => "Recognized<br/>".($year+1),
                "recognized_2" => "Recognized<br/>".($year+2),
                "recognized_3" => "Recognized<br/>".($year+3));

            $label = $dynamic_labels[$labelAlias];

        }else{
            $label = NULL;
            if($labelOnly){
                $label = self::getLabel($labelAlias);
            }else{
                $label  = self::$labels[$labelAlias];
            }
        }

        return $label;
    }
}
