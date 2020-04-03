<?php
/**
 * Created by PhpStorm.
 * User: sgade
 * Date: 04/03/2020
 * Time: 2:05 PM
 */

class NychaRevenueWidgetService extends WidgetDataService implements IWidgetService {

  /**
   * Function to allow the client to initialize the data service
   * @return mixed
   */
  public function initializeDataService() {
    return new NychaRevenueDataService();
  }

  public function implementDerivedColumn($column_name, $row) {
    $value = null;
    $legacy_node_id = $this->getLegacyNodeId();

    if(isset($value)) {
      return $value;
    }
    return $value;
  }

  public function getWidgetFooterUrl($parameters) {
    return NychaRevenueUrlService::getFooterUrl($parameters,$this->getLegacyNodeId());
  }
}
