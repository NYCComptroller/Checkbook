<?php


class BudgetUrlService {

    /**
     * Function to build the footer url for the budget widgets
     * @param $parameters
     * @param null $legacy_node_id
     * @return string
     */
    static function getFooterUrl($parameters,$legacy_node_id = null) {
        $legacy_node_id = isset($legacy_node_id) ? '/dtsmnid/'.$legacy_node_id : '';
        $url = '/panel_html/budget_transactions/budget/transactions'
            . $legacy_node_id
            .RequestUtilities::buildUrlFromParam('agency')
            .RequestUtilities::buildUrlFromParam('dept')
            .RequestUtilities::buildUrlFromParam('expcategory')
            . _checkbook_project_get_year_url_param_string();

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
                $url = "/panel_html/deppartment_budget_details/budget/dept_details";
                break;
            case "agencies":
                $url = "/panel_html/budget_agency_perecent_difference_transactions/budget/agency_details";
                break;
            case "expense_categories":
                $url = "/panel_html/expense_category_budget_details/budget/expcategory_details";
                break;
        }
        if(isset($url)){
            return str_replace("/panel_html/budget_transactions/budget/transactions", $url, $footerUrl);
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
        $dynamic_parameter = isset($dynamic_parameter) ? $dynamic_parameter : '';

        $url = "/panel_html/budget_transactions/budget/transactions"
            . $legacy_node_id
            .RequestUtilities::buildUrlFromParam('agency')
            .RequestUtilities::buildUrlFromParam('dept')
            .RequestUtilities::buildUrlFromParam('expcategory')
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
            ."/agency/".$agency_id;
        return $url;
    }
}
