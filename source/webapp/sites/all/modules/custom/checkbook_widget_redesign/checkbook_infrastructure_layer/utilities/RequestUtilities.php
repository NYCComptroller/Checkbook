<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Class RequestUtilities
 */
class RequestUtilities
{

    /**
     * Checks if the page is Checkbook or Checkbook OGE (EDC)
     * @return True if the page is EDC
     */
    public static function isEDCPage()
    {
        $database = RequestUtilities::get('datasource');
        if (isset($database)) {
            return true;
        } else {
            return false;
        }
    }

    //Returns the path of the current page

    /**
     * @return string
     */
    public static function _getCurrentPage()
    {
        $currentUrl = explode('/', $_SERVER['HTTP_REFERER']);
        return '/' . $currentUrl[3];
    }

    /**
     * Returns key value pair string is present in URL
     * @param string|array $key
     * @return string
     */
    public static function buildUrlFromParam($key)
    {
        if (is_array($key)) {
            $return = '';
            foreach ($key as $k) {
                $return .= self::buildUrlFromParam($k);
            }
            return $return;
        }

        $value = RequestUtilities::get($key);
        if (strpos($key, '|')) {
            $keys = explode('|', $key);
            $key = false;
            while(!$key){
                $key = array_pop($keys);
            }
        }
        if (!is_null($value)) {
            $url = '/' . $key;
            $url .= '/' . urlencode($value);
            return $url;
        }
        return '';
    }


    /**
     * Legacy
     * @TODO Remove any usage in json / evals
     * @param $key
     * @param null $key_alias
     * @return string
     */
    public static function _getUrlParamString($key, $key_alias = null)
    {
        if (!is_null($key_alias)) {
            $key .= '|' . $key_alias;
        }
        return self::buildUrlFromParam($key);
    }


    /**
     * LEGACY
     * @TODO: remove this function in json evals
     * @param $paramName
     * @param bool $fromRequestPath
     * @return array|mixed|null|string|string[]
     */
    public static function getRequestParamValue($paramName, $fromRequestPath = true)
    {
        if ('' === $paramName) {
            return NULL;
        }
        if ($fromRequestPath) {
            return self::get($paramName);
        }
        return filter_xss(htmlspecialchars_decode($_GET[$paramName], ENT_QUOTES));
    }

    /**
     * Get one or many url parameters from current request, with options to override values or use default values
     * or use specific query URL ('q' option) instead of drupal request 'q' param
     *
     * @param string|array $paramName
     * @param array $options ['override','default', 'q']
     * @return string|array value
     *
     * @example
     *
     * list($datasource, $dashboard) = RequestUtilities::get(['datasource', 'dashboard'], ['default' => ['dashboard' => 'ss']]);
     * or
     * $year = RequestUtilities::get('year|calyear');
     * or
     * $year = RequestUtilities::get('year',['q' => $_SERVER['HTTP_REFERER']]);
     */
    public static function get($paramName, $options = [])
    {
        if (is_array($paramName)) {
            $values = [];
            foreach ($paramName as $pName) {
                $values[] = self::getSingleParam($pName, $options);
            }
            return $values;
        }
        return self::getSingleParam($paramName, $options);
    }

    /**
     * @param $paramName
     * @param array $options
     * @return null|string
     */
    private static function getSingleParam($paramName, $options = [])
    {
        if ('' === trim($paramName)) {
            return null;
        }

//        You can specify param aliases separated by |
        if (strpos($paramName, '|')) {
            $params = explode('|', $paramName);
            foreach ($params as $param) {
                $value = self::getSingleParam($param, $options);
                if (!is_null($value)) {
                    return $value;
                }
            }
        }

        if (isset($options['override'][$paramName])) {
            return $options['override'][$paramName];
        }

        $value = self::getFilteredQueryParam($paramName, $options);

        if (is_null($value) && isset($options['default'][$paramName])) {
            return $options['default'][$paramName];
        }

        return $value;
    }

    /**
     * @param $paramName string
     * @param $options array
     * @return null|string
     */
    private static function getFilteredQueryParam($paramName, $options = [])
    {
        $urlPath = '';
        if (isset($_GET[$paramName])) {
            return htmlspecialchars_decode(trim(filter_xss($_GET[$paramName])), ENT_QUOTES);
        }

        if (isset($options['q'])) {
            $urlPath = $options['q'];
        } elseif(isset($_GET['q'])) {
            $urlPath = drupal_get_path_alias($_GET['q']);
        }
        $pathParams = explode('/', $urlPath);
        $index = array_search($paramName, $pathParams);
        if ($index !== FALSE && isset($pathParams[($index + 1)])) {
            $value = trim(filter_xss($pathParams[($index + 1)]));
            if ('' !== $value) {
                return htmlspecialchars_decode($value, ENT_QUOTES);
            }
        }

        if(isset($_GET[$paramName])){
            return filter_xss(htmlspecialchars_decode($_GET[$paramName], ENT_QUOTES));
        }

        return null;
    }

    /**
     * Adds mwbe, subvendor and datasource parameters to url.
     *
     * Precedence:
     *      $source > $overriden_params > requestparam
     *
     * @param null $source
     * @param array $override
     * @param bool $top_nav
     * @return string
     */
    public static function _appendMWBESubVendorDatasourceUrlParams($source = null, $override = [], $top_nav = false)
    {
        list($datasource, $mwbe, $dashboard) = RequestUtilities::get(['datasource', 'mwbe', 'dashboard'], ['override' => $override]);

        $url = "";
        $advanced_search = false;
        if (isset($datasource)) {
            $url = "/datasource/checkbook_oge";
        } else {
            $current_url = explode('/', $_SERVER['HTTP_REFERER']);
            if (($current_url[3] == 'contract' && ($current_url[4] == 'search' || $current_url[4] == 'all') && $current_url[5] == 'transactions')) {
                $advanced_search = true;
            }
            if (!$advanced_search) {
                if ($source) {
                    $source = explode("/", $source);
                    if (!in_array("mwbe", $source)) {
                        $url = isset($mwbe) ? "/mwbe/" . $mwbe : "";
                    }
                    if (!in_array("dashboard", $source)) {
                        $url = isset($dashboard) ? "/dashboard/" . $dashboard : "";
                    }
                } else {
                    if (!$top_nav || (isset($mwbe) && RequestUtilities::get('vendor') > 0 && RequestUtilities::get('dashboard') != "ms")) {
                        $url = isset($mwbe) ? "/mwbe/" . $mwbe : "";
                        $url .= isset($dashboard) ? "/dashboard/" . $dashboard : "";
                    }
                }
            }
        }
        return $url;
    }

    /** Checks if the current URL is opened in a new window */
    public static function isNewWindow()
    {
        return (bool)self::get('newwindow', ['q' => $_SERVER['HTTP_REFERER']]);
    }

    /**
     * @return bool
     */
    public function _checkbook_check_isEDCPage()
    {
        return (bool)RequestUtilities::get('datasource');
    }

    /**
     * @return bool
     */
    public static function is_ajax()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

//    /**
//     * @param $pathParams
//     * @param $key
//     * @param null $key_alias
//     * @return null|string
//     */
//    public static function get_url_param($pathParams, $key, $key_alias = null)
//    {
//
//        $keyIndex = array_search($key, $pathParams);
//        if ($keyIndex) {
//            if ($key_alias == null) {
//                return "/$key/" . $pathParams[($keyIndex + 1)];
//            } else {
//                return "/$key_alias/" . $pathParams[($keyIndex + 1)];
//            }
//        }
//        return NULL;
//    }

    /**
     * This function returns the current NYC year  ...
     * @return int year_id
     */
    public static function getCurrentYearID()
    {
        STATIC $currentNYCYear;
        if (!isset($currentNYCYear)) {
            if (variable_get('current_fiscal_year_id')) {
                $currentNYCYear = variable_get('current_fiscal_year_id');
            } else {
                $currentNYCYear = date("Y");
                $currentMonth = date("m");
                if ($currentMonth > 6) {
                    $currentNYCYear += 1;
                }
                $currentNYCYear = _getYearIDFromValue($currentNYCYear);
            }
        }
        return $currentNYCYear;
    }
}
