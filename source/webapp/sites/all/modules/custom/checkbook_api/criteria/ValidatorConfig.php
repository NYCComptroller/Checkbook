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


/**
 * Class that defines the validations.
 */
class ValidatorConfig {
  static $domains = array('Budget','Revenue','Spending','Payroll','Contracts','Spending_OGE','Contracts_OGE', 'Payroll_NYCHA', 'Contracts_NYCHA');
  static $response_formats = array('xml','csv');
  static $specialChars = "!\"#$%&'()*+,–./:;<=>@?[\\]^{}|~`";
  static $allow_special_chars_params = array('vendor', 'budget_code_name','payee_name','budget_name','vendor_name','prime_vendor','purpose','minority_type_id','mwbe_category','title','title_exact','apt_pin','expense_category', 'vendor_customer_code', 'payee_code');
}
