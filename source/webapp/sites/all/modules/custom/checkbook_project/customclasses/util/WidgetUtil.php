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
        if(in_array($labelAlias,$dynamic_labelAlias)){
            $year = _getYearValueFromID(_getRequestParamValue('year'));
            $dynamic_labels = array("current_modified" => "Modified<br/>".$year,
                                    "previous_modified" => "Modified<br/>".($year-1),
                                    "previous_1_modified" => "Modified<br/>".($year-2),
                                    "previous_2_modified" => "Modified<br/>".($year-3),
                                    "recognized_current" => "Recognized<br/>".$year,
                                    "recognized_1" => "Recognized<br/>".($year+1),
                                    "recognized_2" => "Recognized<br/>".($year+2),
                                    "recognized_3" => "Recognized<br/>".($year+3));

            return str_replace(array('<br/>','<br>'),' ',$dynamic_labels[$labelAlias]);
        }else{
            return str_replace(array('<br/>','<br>'),' ',self::$labels[$labelAlias]);
        }
    }

    static function generateLabelMapping($labelAlias, $labelOnly = false){
        $dynamic_labelAlias = array("current_modified","previous_modified","previous_1_modified","previous_2_modified",
                                    "recognized_current","recognized_1","recognized_2","recognized_3");
        if(in_array($labelAlias,$dynamic_labelAlias)){
            $year = _getYearValueFromID(_getRequestParamValue('year'));
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
        "vendor_name" => "Vendor",
        "original_amount" => "Original<br/>Amount",
        "current_amount" => "Current<br/>Amount",
        "spent_to_date" => "Spent To<br/>Date",
        "dollar_diff" => "Dollar<br/>Difference",
        "percent_diff" => "Percent<br/>Difference",
        "no_of_contracts" => "Number of<br/>Contracts",
        "award_method" => "Award<br/>Method",
        "dept_name" => "Department",
        "agency_name" => "Agency",
        "contract_size" => "Contract<br/>Size",
        "contract_industry" => "Contract<br/>Industry",
        "title" => "Title",
        "month" => "Month",
        "amount" => "Amount",
        "payroll_type" => "Payroll<br/>Type",
        "annual_salary" => "Annual<br/>Salary",
        "hourly_rate" => "Hourly<br/>Rate",
        "pay_rate" => "Pay<br/>Rate",
        "pay_frequency" => "Pay<br/>Frequency",
        "pay_date" => "Pay<br/>Date",
        "gross_pay_ytd" => "Gross<br/>Pay YTD",
        "gross_pay" => "Gross<br/>Pay",
        "base_pay_ytd" => "Base<br/>Pay YTD",
        "base_pay" => "Base<br/>Pay",
        "other_pay_ytd" => "Other<br/>Payments YTD",
        "other_pay_1_ytd" => "Other<br/>Payment YTD",
        "other_pays" => "Other<br/>Payments",
        "other_pay" => "Other<br/>Payment",
        "overtime_pay_ytd" => "Overtime<br/>Payments YTD",
        "overtime_pay_1_ytd" => "Overtime<br/>Payment YTD",
        "overtime_pay" => "Overtime<br/>Payment",
        "no_of_ot_employees" => "Number of<br/>Overtime Employees",
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
        "version" => "Version",
        "start_date" => "Start<br/>Date",
        "end_date" => "End<br/>Date",
        "reg_date" => "Registration<br/>Date",
        "recv_date" => "Received<br/>Date",
        "last_mod_date" => "Last Modified<br/>Date",
        "increase_decrease" => "Increase/<br>Decrease",
        "version_status" => "Version<br/>Status",
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
        "cls_classification_name"=>"Closing Classification<br/>Name",
        "other_years"=>"Other<br/>Years",
        "year"=>"Year",
        "budget_name"=>"Budget Name",
        "commodity_line "=>"Commodity Line",
        "entity_contact_num"=>"Entity Contract # "
    );
}
