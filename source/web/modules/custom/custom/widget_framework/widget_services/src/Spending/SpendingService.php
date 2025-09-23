<?php
namespace Drupal\widget_services\Spending;

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\CustomURLHelper;
use Drupal\checkbook_project\ContractsUtilities\ContractUtil;
use Drupal\checkbook_project\MwbeUtilities\VendorType;

require_once(dirname(__FILE__) . "/../../../../checkbook_project/includes/checkbook_project.inc");

class SpendingService  {



  /**
   * Function to allow adjustParameters
   * @return mixed
   */
    public static function adjustParameters($node,$parameters)
    {
      $subvendor = RequestUtilities::get('subvendor');
      $mwbe = RequestUtilities::get('mwbe');
      if(isset($subvendor)) {
        $data_controller_instance = data_controller_get_operator_factory_instance();
        $parameters['prime_vendor_id'] = $data_controller_instance->initiateHandler(\Drupal\data_controller\Datasource\Operator\Handler\NotEqualOperatorHandler::$OPERATOR__NAME, NULL);
        if($subvendor == all) {
          $parameters['vendor_id'] = $data_controller_instance->initiateHandler(\Drupal\data_controller\Datasource\Operator\Handler\NotEqualOperatorHandler::$OPERATOR__NAME, NULL);
        }
        $parameters['is_prime_or_sub'] = 'S';
      }
      else if(isset($mwbe)) {
        if(isset($parameters['prime_vendor_id'])) {
          $parameters['vendor_id'] = $parameters['prime_vendor_id'];
          unset($parameters['prime_vendor_id']);
        }
      }

      if(isset($parameters['vendor_type'])){
        $parameters['vendor_type'] = VendorType::getVendorTypeValue($parameters['vendor_type']);
      }

      //Adjust Certification parameters
      $parameters = ContractUtil::adjustCertificationFacetParameters($node,$parameters);

      $adjustedParameters = $parameters;
      //if(function_exists('_checkbook_project_applyParameterFilters')){
        $adjustedParameters = CustomURLHelper::_checkbook_project_applyParameterFilters($node,$parameters);
      //}
      return $adjustedParameters;
    }

}
