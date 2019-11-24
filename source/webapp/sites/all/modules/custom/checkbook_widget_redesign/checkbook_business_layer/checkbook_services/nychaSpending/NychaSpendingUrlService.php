<?php
/**
 * Created by PhpStorm.
 * User: sgade
 * Date: 05/06/19
 * Time: 10:26 AM
 */

class NychaSpendingUrlService{
    /**
     * @param $parameters
     * @return string
     */
    static function getFooterUrl($parameters = null)
    {
        $url = "/panel_html/nycha_spending_transactions/nycha_spending/transactions"
            . RequestUtilities::buildUrlFromParam('year')
            . RequestUtilities::buildUrlFromParam('category')
            . RequestUtilities::buildUrlFromParam('agency')
            . RequestUtilities::buildUrlFromParam('vendor')
            . RequestUtilities::buildUrlFromParam('fundsrc')
            . RequestUtilities::buildUrlFromParam('industry')
            . RequestUtilities::buildUrlFromParam('datasource');
        return $url;
    }

    /**
     * Returns NYCHA Spending Landing page URL
     * @param $urlParamName
     * @param $urlParamValue
     * @return string
     */
    static function generateLandingPageUrl($urlParamName, $urlParamValue)
    {
        $url = '/nycha_spending'
            . RequestUtilities::buildUrlFromParam('year')
            . RequestUtilities::buildUrlFromParam('category')
            . RequestUtilities::buildUrlFromParam('agency')
            . RequestUtilities::buildUrlFromParam('vendor')
            . RequestUtilities::buildUrlFromParam('fundsrc')
            . RequestUtilities::buildUrlFromParam('industry')
            . RequestUtilities::buildUrlFromParam('datasource')
            . '/'.$urlParamName.'/'. $urlParamValue;

        return $url;
    }
  /**
   * Returns NYCHA Spending Landing page URL vendorname
   * @param $urlParamName
   * @param $urlParamValue
   * @return string
   */
  static function generateVendorLandingPageUrl($urlParamName, $urlParamValue)
  {
    $url = RequestUtilities::buildUrlFromParam('year')
      . RequestUtilities::buildUrlFromParam('category')
      . RequestUtilities::buildUrlFromParam('agency')
      . RequestUtilities::buildUrlFromParam('vendor')
      . RequestUtilities::buildUrlFromParam('fundsrc')
      . RequestUtilities::buildUrlFromParam('industry')
      . RequestUtilities::buildUrlFromParam('datasource')
      . '/'.$urlParamName.'/'. $urlParamValue;

    return $url;
  }

    /* Gets the YTD Spending link in a generic way
    * @param $dynamic_parameter - custom dynamic parameters to be used in the URL
    * @param null $legacy_node_id
    * @return string
    */
      static function ytdSpendingUrl($dynamic_parameter, $widget) {
        $dynamic_parameter = isset($dynamic_parameter) ? $dynamic_parameter : '';
        $url = "/panel_html/nycha_spending_transactions/nycha_spending/transactions"
          . RequestUtilities::buildUrlFromParam('year')
          . RequestUtilities::buildUrlFromParam('issue_date')
          . RequestUtilities::buildUrlFromParam('category')
          . RequestUtilities::buildUrlFromParam('agency')
          . RequestUtilities::buildUrlFromParam('vendor')
          . RequestUtilities::buildUrlFromParam('fundsrc')
          . RequestUtilities::buildUrlFromParam('industry')
          . RequestUtilities::buildUrlFromParam('datasource')
          . '/widget/'. $widget
          . $dynamic_parameter;

          return $url;
      }

      /* Gets the Invoice amount Spending link in a generic way for NYCHA Contracts
    * @param $dynamic_parameter - custom dynamic parameters to be used in the URL
    * @param null $legacy_node_id
    * @return string
    */
      static function invContractSpendingUrl($dynamic_parameter, $widget,$agreement_type,$tcode) {
        //$year_id = RequestUtil::getRequestKeyValueFromURL('year', $bottomURL);
        //RequestUtilities::buildUrlFromParam('year')
        $url = drupal_get_path_alias($_GET['q']);
        $year = RequestUtil::getRequestKeyValueFromURL('year', $url);
        $dynamic_parameter = isset($dynamic_parameter) ? $dynamic_parameter : '';
        if ($agreement_type != null){
          $syear = "/syear/".$year;
        }
        $agreement_type = isset($agreement_type) ? $agreement_type : '';
        $newwindow='/newwindow'; // open content in new window and also strip menu contents
        $tcode = isset($tcode) ? $tcode : '';
        $url = "/nycha_spending/transactions"
          . RequestUtilities::buildUrlFromParam('year')
          . RequestUtilities::buildUrlFromParam('category')
          . RequestUtilities::buildUrlFromParam('agency')
          . RequestUtilities::buildUrlFromParam('vendor')
          . RequestUtilities::buildUrlFromParam('fundsrc')
          . RequestUtilities::buildUrlFromParam('industry')
          . RequestUtilities::buildUrlFromParam('datasource')
          . $syear
          . '/widget/'. $widget
          . $dynamic_parameter.$agreement_type.$tcode.$newwindow;

        return $url;
      }

      /** Builds Contract ID link for Spending widgets
       * @param $contract_id contract number
       ***/
      static function generateContractIdLink($contract_id){
        $year_id = RequestUtilities::getRequestParamValue('year');
        $year_id = (isset($year_id)) ? $year_id : CheckbookDateUtil::getCurrentFiscalYear(Datasource::NYCHA);
        $class = "new_window";
        $url ='/nycha_contract_details' . '/year/'.$year_id.'/contract/' . $contract_id .'/newwindow';
        $value = "<a class='{$class}' href='{$url}'>{$contract_id}</a>";
        return $value;
      }
}
