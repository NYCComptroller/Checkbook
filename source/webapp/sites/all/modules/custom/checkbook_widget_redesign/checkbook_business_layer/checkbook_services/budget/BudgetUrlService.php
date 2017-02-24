<?php


class BudgetUrlService {

    /**
     * @param $parameters
     * @param null $legacy_node_id
     * @return string
     */
    static function getFooterUrl($parameters,$legacy_node_id = null) {
        $url = "";
        return $url;
    }

    /**
     * Gets the Committed Budget link in a generic way
     * @param $dynamic_parameter
     * @param null $legacy_node_id
     * @return string
     */
    static function committedBudgetUrl($dynamic_parameter, $legacy_node_id = null) {

        $legacy_node_id = isset($legacy_node_id) ? '/smnid/'.$legacy_node_id : '';
        $dynamic_parameter = isset($dynamic_parameter) ? $dynamic_parameter : '';

        $url = "/panel_html/budget_transactions/budget/transactions"
            . $legacy_node_id
            .RequestUtilities::_getUrlParamString("year")
            .RequestUtilities::_getUrlParamString("agency")
            .RequestUtilities::_getUrlParamString("dept")
            .RequestUtilities::_getUrlParamString("expcategory")
            . _checkbook_project_get_year_url_param_string()
            . $dynamic_parameter;

        return $url;
    }

    /**
     * @param $department_id
     * @return string
     */
    static function departmentUrl($department_id) {
        $url =   "/budget"
                .RequestUtilities::_getUrlParamString("year")
                .RequestUtilities::_getUrlParamString("agency")
                .RequestUtilities::_getUrlParamString("expcategory")
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
            .RequestUtilities::_getUrlParamString("year")
            .RequestUtilities::_getUrlParamString("agency")
            .RequestUtilities::_getUrlParamString("expcategory")
            .'/expcategory/'.$expense_category_id;
        return $url;
    }

    static function agencyNameUrl($agency_id) {
        $url = "/budget"
            .RequestUtilities::_getUrlParamString("yeartype")
            .RequestUtilities::_getUrlParamString("year")
            .RequestUtilities::_getUrlParamString("agency")
            ."/agency/".$agency_id;
        return $url;
    }
}