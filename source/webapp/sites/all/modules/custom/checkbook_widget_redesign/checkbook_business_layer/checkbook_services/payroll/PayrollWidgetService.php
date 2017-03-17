<?php

class PayrollWidgetService extends WidgetDataService implements IWidgetService {

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
            case "agency_name_link":
                $column = $row['agency_name'];
                $url = PayrollUrlService::agencyNameUrl($row['agency']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "total_gross_pay_link":
                $column = $row['total_gross_pay'];
                $dynamic_parameter = "/agency/" . $row['agency'];
                $url = PayrollUrlService::payUrl($dynamic_parameter, $this->getLegacyNodeId());
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "total_overtime_pay_link":
                $column = $row['total_overtime_pay'];
                $dynamic_parameter = "/agency/" . $row['agency'];
                $url = PayrollUrlService::payUrl($dynamic_parameter, $this->getLegacyNodeId());
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "max_annual_salary_link":
                $column = $row['max_annual_salary'];
                $dynamic_parameter = "/agency/" . $row['agency'];
                $url = PayrollUrlService::annualSalaryUrl($dynamic_parameter, $this->getLegacyNodeId());
                $value = "<a href='{$url}'>{$column}</a>";
                break;
        }

        if(isset($value)) {
          return $value;
        }

        return $value;
    }

    public function adjustParameters($parameters, $urlPath) {
        return $parameters;
    }

    public function getWidgetFooterUrl($parameters) {
        return PayrollUrlService::getFooterUrl($parameters,$this->getLegacyNodeId());
    }

}