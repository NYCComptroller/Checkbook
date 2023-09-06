<?php
namespace Drupal\widget_services\Contracts;

use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\checkbook_services\Contracts\ContractsUrlService;

class ContractService{

  /**
   * Function to formatted_spent/paid_to_date link
   * @param $master_agreement_yn
   * @param $original_agreement_id
   * @param $prime_rfed_amount
   * @param $sub_rfed_amount
   * @return string
   */
    public static function formatted_spent_paid_link($master_agreement_yn,$original_agreement_id,$prime_rfed_amount,$sub_rfed_amount): string
    {
      return '<a href='.'/spending/transactions'
         .  ($master_agreement_yn == 'Y' ? '/magid/' : '/agid/') . $original_agreement_id
         . RequestUtilities::_checkbook_project_get_url_param_string("dashboard")
         . RequestUtilities::_checkbook_project_get_url_param_string("mwbe")
         .'/year/'. CheckbookDateUtil::_getFiscalYearID() .'/syear/'. CheckbookDateUtil::_getFiscalYearID()
         .'/newwindow class="new_window"/>'
         .FormattingUtilities::custom_number_formatter_basic_format($prime_rfed_amount ?? $sub_rfed_amount) . '</a>';
  }

  /**
   * Function to prime_vendor_name_link
   * @param $document_code
   * @param $prime_vendor_id
   * @param $effective_end_year_id
   * @param $prime_vendor_name_formatted
   * @return string
   */
  public static function prime_vendor_name_link($document_code,$prime_vendor_id,$effective_end_year_id,$prime_vendor_name_formatted): string
  {
    return '<a href=\''
          .ContractsUrlService::applyLandingParameter($document_code)
          .ContractsUrlService::primeVendorUrl($prime_vendor_id, Requestutilities::get('year'),$effective_end_year_id, false)
          .'\'>'. $prime_vendor_name_formatted . '</a>';
  }

}
