<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


/**
 * Class for messages provided by API
 */
class Messages {
  static public $message = array(
    1 => "The request is a success but there are no results found for given search criteria.",
    // Global validations  1000 - 1100:
    1000 => "Required parameter(s) '@paramName' is not provided.",
    1001 => "Invalid value '@value' is provided for '@paramName'. Valid values are '@validValues'.",
    1002 => "Provided value '@value' for '@paramName' is not an integer. Valid value is a number without decimal places.",
    1003 => "Total number of requested records '@requestedRecords' exceeds allowed limit of '@allowedLimit' records.",
    // Common validations 1101 - 1200:
    1101 => "Provided request parameter '@paramName' is not valid for '@domain' domain. Valid values are '@validValues'.",
    1102 => "No value is provided for request parameter '@paramName'.",
    1103 => "Provided value '@paramValue' for request parameter '@paramName' is not valid @dataType.",
    // Value based:
    1104 => "Provided '@limit' value '@paramValue' for request parameter '@paramName' is not valid @dataType.",
    // Range based:
    1105 => "Provided @starLimit value '@startValue' is greater than @endLimit value '@endValue' for request parameter '@paramName'.",
    1106 => "Provided response column '@responseColumn' value is not allowed for '@domain' domain. Valid values are '@validValues'.",
    1107 => "Provided value for criteria (@number) type element '@typeValue' is invalid. Valid values are 'value, range'",
    1108 => "Provided value '@paramValue' for request parameter '@paramName' exceeds maximum allowed '@maxAllowedCharacters' characters.",
    // Value based:
    1109 => "Provided '@limit' value '@paramValue' for request parameter '@paramName' exceeds maximum allowed '@maxAllowedCharacters' characters.",
    // Range based:
    1110 => "Request parameter '@paramName' do not support range values.",
    1111 => "Special characters are not allowed for request parameter '@paramName' with value @paramValue.",
    1112 => "Cannot provide parameters '@parameterNames' in same request. Only one of the parameters '@possibleParameters' can be provided.",
  );

  //Sample Message
  /*
    1 => "The status is still a success but there are no results found for given search criteria.",

   //Global validations  1000 - 1100
   1000 => "Required parameter 'type_of_data' is not provided.",
   1001 => "Invalid value 'BudgetRequest' is provided for 'type_of_data'. Valid values are 'Budget,Spending,Revenue'.",
   1002 => "Provided value '2 1' for 'records_from' is not an integer. Valid value is a number values without decimal places.",
   1003 => "Total number of requested records '1001' exceeds allowed limit of '1000' records.",

   //Common validations 1101 - 1200
   1101 => "Provided request parameter 'date' is not valid for 'Budget' domain. Valid values are 'fiscal_year,agency_code,department_code,budget_code,expense_category,adopted_budget,current_modified_budget,pre_encumbered,encumbered,cash_expense,post_adjustment,accrued_expense'.",
   1102 => "No value is provided for request parameter 'fiscal_year'.",
   1103 => "Provided value '20000' for request parameter 'fiscal_year' is not valid year.",
   1104 => "Provided 'start' value '200s' for request parameter 'adopted_budget' is not valid amount.",
   1105 => "Provided start value '1000' is greater than end value '500' for request parameter 'adopted_budget'.",
   1106 => "Provided response column 'agencyname' value is not allowed for 'Budget' domain. Valid values are 'agency_name,department_name,expense_category,budget_code,budget_code_name,current_modified_budget,adopted_budget,pre_encumbered,encumbered,cash_expense,post_adjustment,accrued_expense'.",
   1107 => "Provided Value for type 'value2' is invalid. Valid Values are 'value, range'",
   1108 => "Provided value 'abcd' for request parameter 'agency_code' exceeds maximum allowed '3' characters.",
   1109 => "Provided 'start' value '1234567890' for request parameter 'adopted_budget' exceeds maximum allowed '5' characters.",
   1110 => "Request parameter 'agency_code' do not support range values.",
   1111 => "Special characters are not allowed for request parameter 'agency_code' with value 1@2.",
   1112 => "Cannot provide parameters 'fiscal_year,calendar_year' in same request. Only one of the parameters 'fiscal_year,calendar_year' can be provided.",
  */

}
