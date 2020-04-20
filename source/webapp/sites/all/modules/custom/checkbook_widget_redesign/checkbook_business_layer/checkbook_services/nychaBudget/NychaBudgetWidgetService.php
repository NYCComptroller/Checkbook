<?php
/**
 * Created by PhpStorm.
 * User: sgade
 * Date: 03/30/2020
 * Time: 2:05 PM
 */

class NychaBudgetWidgetService extends WidgetDataService implements IWidgetService {

  /**
   * Function to allow the client to initialize the data service
   * @return mixed
   */
  public function initializeDataService() {
    return new NychaBudgetDataService();
  }

  public function implementDerivedColumn($column_name, $row) {
    $value = null;
    if(isset($value)) {
      return $value;
    }
    return $value;
  }
  public function adjustParameters($parameters, $urlPath) {
    return $parameters;
  }

  public function getWidgetFooterUrl($parameters) {
    return NychaBudgetUrlService::getFooterUrl($parameters);
  }
}
