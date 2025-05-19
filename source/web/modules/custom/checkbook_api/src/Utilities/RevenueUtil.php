<?php
namespace Drupal\checkbook_api\Utilities;
use Drupal\data_controller\Datasource\Operator\Handler\RegularExpressionOperatorHandler;

class RevenueUtil {
  /**
   * @param $sql_query
   * @return void
   */
  public static function checkbook_api_adjustNYCHARevenueSql(&$sql_query) {
    $sql_parts = explode("WHERE", $sql_query);
    $select_part = $sql_parts[0];
    $where_part = $sql_parts[1];
    $select_part = str_replace("modified_amount", "adopted_amount AS modified_amount", $select_part);
    $sql_query = (count($sql_parts) > 1) ? implode(' WHERE ', [
      $select_part,
      $where_part
    ]) : $select_part;
  }

  /**
   * @param $data_set
   * @param $parameters
   * @param $criteria
   * @return void
   */
  public static function checkbook_api_adjustNYCHARevenueParameters(&$data_set, &$parameters, $criteria){
    if (isset($parameters['program_phase_code']) || isset($parameters['gl_project_code'])) {
      $validateParams = array('program_phase_code', 'gl_project_code');
      $data_controller_instance = data_controller_get_operator_factory_instance();
      foreach ($validateParams as $key => $value) {
        if(isset($parameters[$value]) && trim($parameters[$value], '0') === ''){
          $parameters["$value"] = $data_controller_instance ? $data_controller_instance->initiateHandler(RegularExpressionOperatorHandler::$OPERATOR__NAME, "(^UNMATCHABLE_PATTERN$)"): "";
        }
      }
    }
  }
}
