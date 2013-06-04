<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/


/**
 * Class that defines the validations.
 */
class ValidatorConfig {
  static $domains = array('Budget','Revenue','Spending','Payroll','Contracts');
  static $response_formats = array('xml','csv');
  static $specialChars = "!\"#$%&'()*+,–./:;<=>@?[\\]^{}|~`";
}
