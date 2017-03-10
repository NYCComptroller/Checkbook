<?php

class PayrollUrlService {

  /**
   * @param $parameters
   * @param null $legacy_node_id
   * @return string
   */
  static function getFooterUrl($legacy_node_id = null) {
    $url = '/panel_html/payroll_nyc_transactions'.'/smnid/'.$legacy_node_id;
    $url .= RequestUtilities::_getUrlParamString('yeartype');
    $url .= RequestUtilities::_getUrlParamString('year');
    return $url;
  }

}
