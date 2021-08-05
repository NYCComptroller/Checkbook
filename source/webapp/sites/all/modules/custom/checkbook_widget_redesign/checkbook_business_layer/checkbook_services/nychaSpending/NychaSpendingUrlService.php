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
    public static function getFooterUrl($parameters = null): string
    {
        return "/panel_html/nycha_spending_transactions/nycha_spending/transactions"
            . RequestUtilities::buildUrlFromParam('year')
            . RequestUtilities::buildUrlFromParam('category')
            . RequestUtilities::buildUrlFromParam('agency')
            . RequestUtilities::buildUrlFromParam('vendor')
            . RequestUtilities::buildUrlFromParam('fundsrc')
            . RequestUtilities::buildUrlFromParam('industry')
            . RequestUtilities::buildUrlFromParam('datasource');
    }

  /**
   * @param $urlParamName
   * @param $urlParamValue
   * @param null $yearId
   * @return string
   */
    public static function generateLandingPageUrl($urlParamName, $urlParamValue, $yearId = null): string
    {
        $yearId = (isset($yearId)) ? $yearId : RequestUtilities::getRequestParamValue('year');
        $yearURL = '/year/'. ((isset($yearId)) ? $yearId : CheckbookDateUtil::getCurrentFiscalYearId(Datasource::NYCHA));
        return '/nycha_spending'
            . $yearURL
            . RequestUtilities::buildUrlFromParam('category')
            . RequestUtilities::buildUrlFromParam('agency')
            . RequestUtilities::buildUrlFromParam('vendor')
            . RequestUtilities::buildUrlFromParam('fundsrc')
            . RequestUtilities::buildUrlFromParam('industry')
            . RequestUtilities::buildUrlFromParam('datasource')
            . '/'.$urlParamName.'/'. $urlParamValue;
    }

    /** Gets the YTD Spending link in a generic way
    * @param $dynamic_parameter - custom dynamic parameters to be used in the URL
    * @param $widget
    * @return string
   */
    public static function ytdSpendingUrl($dynamic_parameter, $widget): string
      {
        $dynamic_parameter = $dynamic_parameter ?? '';
        return "/panel_html/nycha_spending_transactions/nycha_spending/transactions"
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
      }

  /**
   * @param $dynamic_parameter
   * @param $year_parameter
   * @param $widget
   * @return string
   */
  public static function idateSpendingUrl($dynamic_parameter,$year_parameter, $widget): string
  {
    $year_parameter = $year_parameter ?? '';
    $category = RequestUtilities::buildUrlFromParam('category');
    $category = str_replace("category", "category_inv", $category);
    $dynamic_parameter = $dynamic_parameter ?? '';
    return "/panel_html/nycha_spending_transactions/nycha_spending/transactions"
      . RequestUtilities::buildUrlFromParam('datasource')
      . $year_parameter.$category
      . RequestUtilities::buildUrlFromParam('vendor')
      . '/widget/'. $widget
      . $dynamic_parameter;
    }

    /** Gets the Invoice amount Spending link in a generic way for NYCHA Contracts
   * @param null $dynamic_parameter
   * @param $widget
   * @param null $agreement_type
   * @param null $tcode
   * @return string
   */
     public static function invContractSpendingUrl($dynamic_parameter = null , $widget = null,$agreement_type = null, $tcode = null ): string
      {
        $url = drupal_get_path_alias($_GET['q']);
        $year = RequestUtil::getRequestKeyValueFromURL('year', $url);
        $vendor = RequestUtilities::buildUrlFromParam('vendor');
        $industry = RequestUtilities::buildUrlFromParam('industry');
        $vendor = str_replace("vendor", "vendor_inv", $vendor);
        $industry = str_replace("industry", "industry_inv", $industry);
        $dynamic_parameter = $dynamic_parameter ?? '';
        $syear = "/syear/".$year;
        $agreement_type = $agreement_type ?? '';
        $newwindow='/newwindow'; // open content in new window and also strip menu contents
        $tcode = $tcode ?? '';
        return "/nycha_spending/transactions"
          . RequestUtilities::buildUrlFromParam('year')
          .$vendor.$industry
          . RequestUtilities::buildUrlFromParam('category')
          . RequestUtilities::buildUrlFromParam('agency')
          . RequestUtilities::buildUrlFromParam('dept')
          . RequestUtilities::buildUrlFromParam('resp_center')
          . RequestUtilities::buildUrlFromParam('csize')
          . RequestUtilities::buildUrlFromParam('awdmethod')
          . RequestUtilities::buildUrlFromParam('datasource')
          . $syear
          . '/widget/'. $widget
          . $dynamic_parameter.$agreement_type.$tcode.$newwindow;
      }
  /** Gets the Invoice amount Spending link in a generic way for NYCHA Contracts
   * @param null $dynamic_parameter
   * @param null $widget
   * @param null $agreement_type
   * @param null $tcode
   * @return string
   */
  public static function invIDContractSpendingUrl($dynamic_parameter = null , $widget = null, $agreement_type = null ,$tcode =null): string
  {
    $vendor = RequestUtilities::buildUrlFromParam('vendor');
    $industry = RequestUtilities::buildUrlFromParam('industry');
    $vendor = str_replace("vendor", "vendor_inv", $vendor);
    $industry = str_replace("industry", "industry_inv", $industry);
    $dynamic_parameter = $dynamic_parameter ?? '';
    $agreement_type = $agreement_type ?? '';
    $newwindow='/newwindow'; // open content in new window and also strip menu contents
    $tcode = $tcode ?? '';
    return "/nycha_spending/transactions"
      .$vendor.$industry
      . RequestUtilities::buildUrlFromParam('category')
      . RequestUtilities::buildUrlFromParam('agency')
      . RequestUtilities::buildUrlFromParam('dept')
      . RequestUtilities::buildUrlFromParam('resp_center')
      . RequestUtilities::buildUrlFromParam('csize')
      . RequestUtilities::buildUrlFromParam('awdmethod')
      . RequestUtilities::buildUrlFromParam('datasource')
      . '/widget/'. $widget
      . $dynamic_parameter.$agreement_type.$tcode.$newwindow;
  }

      /**
     * Builds Contract ID link for Spending widgets
     * @param $contract_id
     * @param null $year_id
     * @return string
     */
      public static function generateContractIdLink($contract_id, $year_id = null): string
      {
        $year_id = (isset($year_id)) ? $year_id : RequestUtilities::getRequestParamValue('year');
        $year_id = (isset($year_id)) ? $year_id : CheckbookDateUtil::getCurrentFiscalYear(Datasource::NYCHA);
        $class = "new_window";
        $url ='/nycha_contract_details' . '/year/'.$year_id.'/contract/' . $contract_id .'/newwindow';
        return "<a class='{$class}' href='{$url}'>{$contract_id}</a>";
      }
}
