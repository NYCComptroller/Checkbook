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
        $bottomContainerReloadClass = "bottomContainerReload";
        $legacy_node_id = $this->getLegacyNodeId();
        switch($column_name) {
            case "agency_name_link":
                $column = $row['agency_name'];
                $url = PayrollUrlService::agencyNameUrl($row['agency']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "total_gross_pay_link":
                $column = $row['total_gross_pay'];
                $url = PayrollUrlService::payUrl($row['agency'], $legacy_node_id);
                $value = "<a class='{$bottomContainerReloadClass}' href='{$url}'>{$column}</a>";
                break;
            case "total_overtime_pay_link":
                $column = $row['total_overtime_pay'];
                $url = PayrollUrlService::payUrl($row['agency'], $legacy_node_id);
                $value = "<a class='{$bottomContainerReloadClass}' href='{$url}'>{$column}</a>";
                break;
            case "max_annual_salary_link":
                $column = $row['max_annual_salary'];
                $employee = $row['employee_id'];
                $agency = $row['agency'];
                $url = PayrollUrlService::annualSalaryUrl($agency, $employee);
                $value = "<a class='{$bottomContainerReloadClass}' href='{$url}'>{$column}</a>";
                break;
            case "max_annual_salary_per_agency_link":
                $column = $row['max_annual_salary'];
                $employee = $row['employee_id'];
                $agency = $row['agency_id'];
                $url = PayrollUrlService::annualSalaryPerAgencyUrl($agency, $employee);
                $value = "<a class='{$bottomContainerReloadClass}' href='{$url}'>{$column}</a>";
                break;
            case "non_salary_per_agency_link":
                $column = $row['non_salaried_rate'];
                $employee = $row['employee_id'];
                $agency = $row['agency_id'];
                $url = PayrollUrlService::annualSalaryPerAgencyUrl($agency, $employee);
                $value = "<a class='{$bottomContainerReloadClass}' href='{$url}'>{$column}</a>";
                break;
            case "title_agency_link":
                $column = $row['civil_service_title'];
                $agency = $row['agency_id'];
                $title = $row['civil_service_title_code'];
                $url = PayrollUrlService::titleAgencyUrl($agency, $title);
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "title_link":
                $column = $row['civil_service_title'];
                $title = $row['civil_service_title_code'];
                $url = PayrollUrlService::titleUrl($title);
                $value = "<a href='{$url}'>{$column}</a>";
                break;
        }

        return $value;
    }

    public function adjustParameters($parameters, $urlPath) {
        return $parameters;
    }

    public function getWidgetFooterUrl($parameters) {
        return PayrollUrlService::getFooterUrl($parameters,$this->getLegacyNodeId(),$this->getParamName());
    }

}
