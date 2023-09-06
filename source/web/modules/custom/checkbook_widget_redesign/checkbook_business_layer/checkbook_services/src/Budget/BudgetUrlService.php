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

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\CustomURLHelper;

class BudgetUrlService {

    /**
     * Function to build the footer url for the budget widgets
     * @param null $legacy_node_id
     * @return string
     */
    static function getFooterUrl($legacy_node_id = null) {
        $legacy_node_id = isset($legacy_node_id) ? '/dtsmnid/'.$legacy_node_id : '';
        $url = '/budget/transactions/budget_transactions'
            . $legacy_node_id
            .RequestUtilities::buildUrlFromParam('agency')
            .RequestUtilities::buildUrlFromParam('dept')
            .RequestUtilities::buildUrlFromParam('expcategory')
            . CustomURLHelper::_checkbook_project_get_year_url_param_string();

        return $url;
    }

    /**
     * Function to build the footer url for the budget widgets
     * @param $footerUrl
     * @param $widget
     * @return string
     */
    static function getPercentDiffFooterUrl($footerUrl, $widget){
        $url = null;
        switch($widget){
            case "departments":
                $url = "/department_budget_details/budget/dept_details";
                break;
            case "agencies":
                $url = "/budget_agency_perecent_difference_transactions/budget/agency_details";
                break;
            case "expense_categories":
                $url = "/expense_category_budget_details/budget/expcategory_details";
                break;
        }
        if(isset($url)){
            return str_replace("/budget/transactions/budget_transactions", $url, $footerUrl);
        }else{
            return $footerUrl;
        }
    }

    /**
     * Gets the Committed Budget link in a generic way
     * @param $dynamic_parameter
     * @param null $legacy_node_id
     * @return string
     */
    static function committedBudgetUrl($dynamic_parameter, $legacy_node_id = null) {

        $legacy_node_id = isset($legacy_node_id) ? '/smnid/'.$legacy_node_id : '';
        $dynamic_parameter = $dynamic_parameter ?? '';

        $url = "/budget/transactions/budget_transactions"
            . $legacy_node_id
            .RequestUtilities::buildUrlFromParam('agency')
            .RequestUtilities::buildUrlFromParam('dept')
            .RequestUtilities::buildUrlFromParam('expcategory')
            .CustomURLHelper::_checkbook_project_get_year_url_param_string()
            . $dynamic_parameter;

        return $url;
    }

    /**
     * @param $department_id
     * @return string
     */
    static function departmentUrl($department_id) {
        $url =   "/budget"
                .RequestUtilities::buildUrlFromParam('year')
                .RequestUtilities::buildUrlFromParam('agency')
                .RequestUtilities::buildUrlFromParam('expcategory')
                .'/dept/'.$department_id;
        return $url;
    }

    /**
     * Function to build the expense category name url
     * @param $expense_category_id
     * @return string
     */
    static function expenseCategoryUrl($expense_category_id) {
        $url =   "/budget"
            .RequestUtilities::buildUrlFromParam('year')
            .RequestUtilities::buildUrlFromParam('agency')
            .RequestUtilities::buildUrlFromParam('expcategory')
            .RequestUtilities::buildUrlFromParam('dept')
            .'/expcategory/'.$expense_category_id;
        return $url;
    }

    static function agencyNameUrl($agency_id) {
        $url = "/budget"
            .RequestUtilities::buildUrlFromParam('yeartype')
            .RequestUtilities::buildUrlFromParam('year')
            .RequestUtilities::buildUrlFromParam('agency')
            .RequestUtilities::buildUrlFromParam('expcategory')
            ."/agency/".$agency_id;
        return $url;
    }
}
