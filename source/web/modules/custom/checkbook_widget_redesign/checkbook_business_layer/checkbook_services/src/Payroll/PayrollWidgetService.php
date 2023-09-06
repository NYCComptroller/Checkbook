<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\checkbook_services\Payroll;

use Drupal\checkbook_services\Widget\IWidgetService;
use Drupal\checkbook_services\Widget\WidgetDataService;

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
