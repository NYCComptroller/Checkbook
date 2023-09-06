<?php

namespace Drupal\checkbook_infrastructure_layer\Utilities;

use Drupal\checkbook_infrastructure_layer\Constants\Common\PageType;
use Drupal\Component\Utility\Xss;

/**
 * Class RequestUtilities
 */
class RequestUtilities {

  /**
   * returns first parameter value modified url
   */
  public static function resetUrl(){
    $current_path = \Drupal::service('path.current')->getPath();
    $update_path = str_replace(":", "/", $current_path);
    \Drupal::service('path.current')->setPath($update_path);
  }

  /**
   * @return string
   */
  public static function getCurrentPageUrl()
  {
    $url =\Drupal::service('path.current')->getPath();
    if (empty($url)) {
      // that's AJAX
      return self::getAjaxPath();
    }
    return $url;
  }

  /**
   * @return string
   */
  public static function getRequestUri()
  {
    return \Drupal::request()->getRequestUri();
  }

  /**
   * @return string
   */
  public static function getAjaxPath()
  {
    if(!empty(\Drupal::request()->server->get('HTTP_X_REQUESTED_WITH')) && strtolower(\Drupal::request()->server->get('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest') {
      return \Drupal::request()->server->get('HTTP_REFERER');
    }
    return null;
  }

  /**
   * LEGACY
   *
   * @TODO: Please use get() function instead getRequestParamValue. 'RequestUtilities::getRequestParamValue' need to be replaced in Drupal Interface
   * @param $paramName
   * @param bool $fromRequestPath
   *
   * @return array|mixed|null|string|string[]
   */
  public static function getRequestParamValue($paramName, bool $fromRequestPath = TRUE): mixed
  {
    return self::get($paramName);
  }

  /**
   * Get one or many url parameters from current request, with options to
   * override values or use default values or use specific query URL ('q'
   * option) instead of drupal request 'q' param
   *
   * @param string|array $paramName
   * @param array $options ['override','default', 'q']
   *
   * @return string|array value
   *
   * @example
   *
   * list($datasource, $dashboard) = RequestUtilities::get(['datasource',
   *   'dashboard'], ['default' => ['dashboard' => 'ss']]); or
   * $year = RequestUtilities::get('year|calyear');
   * or
   * $year = RequestUtilities::get('year',['q' => \Drupal::request()->server->get('HTTP_REFERER')]);
   *
   */
  public static function get($paramName, $options = []) {
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
   *
   * @return null|string
   */
  private static function getSingleParam($paramName, $options = []) {
    if ('' === trim($paramName)) {
      return NULL;
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
   *
   * @return null|string
   */
  private static function getFilteredQueryParam($paramName, $options = []) {
    $urlPath = '';

    if (isset($options['q'])) {
      $urlPath = $options['q'];
    } elseif(self::getRefUrl() !== null){
      //Gridviews
      $urlPath = self::getRefUrl();
    }elseif (\Drupal::service('path.current')->getPath() !== NULL) {
      //@TO DO: Revaluate if resetURL() can be removed
      self::resetUrl();
      $urlPath = \Drupal::service('path.current')->getPath();
    }
    $pathParams = explode('/', $urlPath);
    $index = array_search($paramName, $pathParams);
    if ($index !== FALSE && isset($pathParams[($index + 1)])) {
      $value = trim(Xss::filter($pathParams[($index + 1)]));
      if ('' !== $value) {
        return Xss::filter(htmlspecialchars_decode($value, ENT_QUOTES));
      }
    }

    if (\Drupal::request()->query->get($paramName) !== NULL) {
      return Xss::filter(htmlspecialchars_decode(\Drupal::request()->query->get($paramName), ENT_QUOTES));
    }

    return NULL;
  }


  public static function getParamValueFromCurrentURL(){

  }

  /**
   * @return mixed
   */
  public static function getBottomContUrl(): mixed
  {
    return \Drupal::request()->query->get('expandBottomContURL');
  }

  /**
   * @return mixed
   */
  public static function getRefUrl(): mixed
  {
    return \Drupal::request()->query->get('refURL');
  }

  /**
   * returns request parameter value from bottomURL
   * @param string $paramName
   * @param string $fromRequestPath
   * @return string
   */
  public static function _getRequestParamValueBottomURL($paramName, $fromRequestPath = 'TRUE'): ?string
  {
    if (empty($paramName)) {
      return NULL;
    }
    $value = NULL;
    if ($fromRequestPath) {
      $bottomURL = \Drupal::request()->query->get('expandBottomContURL');
      $pathParams = explode('/', $bottomURL);
      $index = array_search($paramName, $pathParams);
      if ($index !== FALSE) {
        $value = Xss::filter($pathParams[($index + 1)]);
      }
      if (trim($value) == "") {
        return NULL;
      }
      if (isset($value) || $fromRequestPath) {
        return Xss::filter(htmlspecialchars_decode($value, ENT_QUOTES));
      }
    } else {
      return Xss::filter(htmlspecialchars_decode(\Drupal::request()->query->get($paramName), ENT_QUOTES));
    }
    return $value;
  }

  /**
   * Checks if the page is Checkbook or Checkbook OGE (EDC)
   *
   * @return false if the page is EDC
   */
  public static function isEDCPage() {
    $database = RequestUtilities::get('datasource');
    if (isset($database)) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  //Returns the path of the current page
  /**
   * @return string
   */
  public static function _getCurrentPage() {
    //$currentUrl = explode('/', \Drupal::request()->server->get('HTTP_REFERER'));
    $currentUrl =  explode('/',self::getCurrentPageUrl());
    return '/' . $currentUrl[3];
  }

  /**
   * @return mixed
   */
  public static function getFrontPagePath(): mixed
  {
    if(PageType::isFrontPage()){
      return \Drupal::service('path.current')->getPath();
    }else{
      return false;
    }
  }

  /**
   * returns key-value pair string is present in URL
   * detects ajax automatically
   * @param $key
   * @param null $key_alias
   * @param bool $ajaxPath
   * @return string
   */
  public static function _checkbook_project_get_url_param_string($key, $key_alias = null, $ajaxPath = FALSE){
    return RequestUtilities::buildUrlFromParam("{$key}|{$key_alias}");
  }
  /**
   * Returns key value pair string is present in URL
   *
   * @param string|array $key
   * @return string
   *  TODO: Replace _checkbook_project_get_url_param_string function with this
   */
  public static function buildUrlFromParam($key) {
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
      $key = FALSE;
      while (!$key) {
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
   *
   * @TODO Remove any usage in json / evals
   *
   * @param $key
   * @param null $key_alias
   *
   * @return string
   */
  public static function _getUrlParamString($key, $key_alias = NULL): string
  {
    if (!is_null($key_alias)) {
      $key .= '|' . $key_alias;
    }
    return self::buildUrlFromParam($key);
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
   *
   * @return string
   */
  public static function _appendMWBESubVendorDatasourceUrlParams($source = NULL, $override = [], $top_nav = FALSE) {
    [$datasource, $mwbe, $dashboard] = RequestUtilities::get([
      'datasource',
      'mwbe',
      'dashboard',
    ], ['override' => $override]);

    $url = "";
    $advanced_search = FALSE;
    if (isset($datasource)) {
      $url = "/datasource/checkbook_oge";
    }
    else {
      $current_url = explode('/', \Drupal::request()->server->get('HTTP_REFERER'));
      if (count($current_url) > 3 && ($current_url[3] == 'contract' && ($current_url[4] == 'search' || $current_url[4] == 'all') && $current_url[5] == 'transactions')) {
        $advanced_search = TRUE;
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
        }
        else {
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
  public static function isNewWindow() {
    return (bool) self::get('newwindow', ['q' => \Drupal::request()->server->get('HTTP_REFERER')]);
  }

  /**
   * @return bool
   */
  public static function _checkbook_check_isEDCPage() {
    return (bool) RequestUtilities::get('datasource');
  }

  /**
   * @return bool
   */
  public static function _checkbook_current_request_is_ajax(){
    return (!empty(\Drupal::request()->server->get('HTTP_X_REQUESTED_WITH')) && strtolower(\Drupal::request()->server->get('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest');
  }

  /**
   * @return bool
   */
  public static function getTransactionsParams($value){
    if(self::getBottomContUrl()){
      $paramValue = self::_getRequestParamValueBottomURL($value);
    }
    else{
      $paramValue = self::get($value);
    }
   return $paramValue;
  }

  /**
  * @param $urlParams
  * @return array|string|string[]
  */
    public static function replaceSlash($urlParams){
      $Separator =':';
      return str_replace('/', $Separator, $urlParams);
    }
}
