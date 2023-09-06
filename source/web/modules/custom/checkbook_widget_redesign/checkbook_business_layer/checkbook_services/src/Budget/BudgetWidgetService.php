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

namespace Drupal\checkbook_services\Budget;

use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\BudgetUtilities\BudgetUtil;
use Drupal\checkbook_services\Widget\IWidgetService;
use Drupal\checkbook_services\Widget\WidgetDataService;

class BudgetWidgetService extends WidgetDataService implements IWidgetService {

    /**
     * Function to allow the client to initialize the data service
     * @return mixed
     */
    public function initializeDataService() {
        return new BudgetDataService();
    }

    public function implementDerivedColumn($column_name, $row) {
        $value = null;
        $legacy_node_id = $this->getLegacyNodeId();
        switch ($column_name) {
            case "agency_name_link":
                $column = $row['agency_name'];
                $url = BudgetUrlService::agencyNameUrl($row['agency_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "department_name_link":
                $column = $row['department_name'];
                $url = BudgetUrlService::departmentUrl($row['department_code']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;
            case "expense_category_name_link":
                $column = $row['expense_category_name'];
                $url = BudgetUrlService::expenseCategoryUrl($row['expense_category_id']);
                $value = "<a href='{$url}'>{$column}</a>";
                break;

            // Committed budget link variations
            case "expense_category_committed_budget_link":
                $column = $row['committed_budget'];
                $class = "bottomContainerReload";
                $dynamic_parameter = "/expcategory/" . $row["expense_category_id"];
                $url = BudgetUrlService::committedBudgetUrl($dynamic_parameter, $this->getLegacyNodeId());
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "agency_committed_budget_link":
                $column = $row['committed_budget'];
                $class = "bottomContainerReload";
                $dynamic_parameter = "/agency/" . $row["agency_id"];
                $url = BudgetUrlService::committedBudgetUrl($dynamic_parameter, $this->getLegacyNodeId());
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "dept_committed_budget_link":
                $column = $row['budget_committed'];
                $class = "bottomContainerReload";
                $dynamic_parameter = "/dept/" . $row["department_code"];
                $url = BudgetUrlService::committedBudgetUrl($dynamic_parameter, $this->getLegacyNodeId());
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
            case "expense_bdg_cat_committed_budget_link":
                $column = $row['budget_committed'];
                $class = "bottomContainerReload";

                $dynamic_parameter = "/bdgcode/" . $row["budget_code_id"];
                $budget_code_values = BudgetUtil::getBudgetCodeNameAndBudgetCode($row['budget_code_id'],RequestUtilities::get('agency'),RequestUtilities::get('year'));
                $dynamic_parameter .= isset($budget_code_values['budget_code']) ? "/bdgcode_code/" . $budget_code_values["budget_code"] : '';
                $dynamic_parameter .= isset($budget_code_values['budget_code_name']) ? "/bdgcodenm/" . urlencode(FormattingUtilities::checkbook_replaceSlash($budget_code_values["budget_code_name"])) : '';

                $url = BudgetUrlService::committedBudgetUrl($dynamic_parameter, $this->getLegacyNodeId(), $row);
                $value = "<a class='{$class}' href='{$url}'>{$column}</a>";
                break;
        }

        if(isset($value)) {
            return $value;
        }
        return $value;
    }

    public function adjustParameters($parameters, $urlPath) {
        //'filter_type' is used for 'Percent Difference' widgets
        if(isset($parameters['filter_type'])){
            $type_filter = array();
            $agency = RequestUtilities::get('agency');
            $dept = RequestUtilities::get('dept');
            $expcategory = RequestUtilities::get('expcategory');

            if($agency || $parameters['filter_type'] == 'A'){
                $type_filter[] = 'A';
            }
            if($dept || $parameters['filter_type'] == 'D'){
                $type_filter[] = 'D';
            }
            if($expcategory || $parameters['filter_type'] == 'O'){
                $type_filter[] = 'O';
            }
            $parameters['filter_type'] = implode("", $type_filter);
        }

        return $parameters;
    }

    public function getWidgetFooterUrl($parameters) {
        return BudgetUrlService::getFooterUrl($this->getLegacyNodeId());
    }
}
