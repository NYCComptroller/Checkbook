<?php
namespace Drupal\checkbook_api\Criteria;

/**
 * Class that defines the validations.
 */
class ValidatorConfig
{
  static $domains = [
    'Budget',
    'Revenue',
    'Spending',
    'Payroll',
    'Contracts',
    'Spending_OGE',
    'Contracts_OGE',
    'Payroll_NYCHA',
    'Contracts_NYCHA',
    'Spending_NYCHA',
    'Budget_NYCHA',
    'Revenue_NYCHA',
  ];
  static $response_formats = ['xml', 'csv'];
  static $specialChars = "!\"#$%&'()*+,–./:;<=>@?[\\]^{}|~`";
  static $allow_special_chars_params = [
    'vendor',
    'budget_code_name',
    'payee_name',
    'budget_name',
    'vendor_name',
    'prime_vendor',
    'purpose',
    'minority_type_id',
    'mwbe_category',
    'title',
    'title_exact',
    'pin',
    'apt_pin',
    'expense_category',
    'vendor_customer_code',
    'payee_code',
    'budget_type',
    'capital_project_code'
  ];
}
