<?php

class PayrollWidgetService extends WidgetSqlService implements IWidgetService {

    public function initializeDataService() {
        return new PayrollDataService();
    }

    /**
    * Function to be overridden by implementing class to apply customized formatting to the data
    * @param $column_name
    * @param $row
    * @return mixed
    */
    public function implementDerivedColumn($column_name, $row) {
    $value = null;
    $legacy_node_id = $this->getLegacyNodeId();
    switch($column_name) {
      case "":
        // url
        break;
    }

    if(isset($value)) {
      return $value;
    }

    return $value;
    }

    public function adjustParameters() {}

    public function getWidgetFooterUrl($parameters) {
        return BudgetUrlService::getFooterUrl($parameters,$this->getLegacyNodeId());
    }

}