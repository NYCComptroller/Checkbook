<?php
/**
 * Created by PhpStorm.
 * User: sgade
 * Date: 02/22/2017
 * Time: 1:16 PM
 */
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
     * @param $parameters
     * @param null $legacy_node_id
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
    
}