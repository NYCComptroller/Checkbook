<?php
namespace Drupal\widget_config\Twig\Sitewide;

use Drupal\checkbook_infrastructure_layer\Constants\Common\CheckbookDomain;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Constants\Common\PageType;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\checkbook_project\CommonUtilities\RequestUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Drupal\Component\Utility\Xss;

class YearListConfigExtension extends AbstractExtension
{
public function getFunctions()
{
      return [
      'generateYearList' => new TwigFunction('generateYearList', [
        $this,
        'generateYearList',
          ])
        ];
      }

  public function generateYearList() {
    //Hide the Date Filter
    //on Spending Advanced Search page when 'Check Date' parameter is present in the URL &
    //on Pending Contracts Advanced Search page
    $current_path_url =  RequestUtilities::getCurrentPageUrl();
    $year_param = RequestUtilities::get('year');

    if((preg_match('/^\/spending\/search\/transactions/',$current_path_url) && (RequestUtilities::get('chkdate') || !$year_param))
      || RequestUtilities::get('contstatus') == 'P' || preg_match('/^\/contract\/all\/transactions/',$current_path_url) || preg_match('/^\/nycha_contracts\/all\/transactions/',$current_path_url)
      || (preg_match('/^\/nycha_spending\/search\/transactions/',$current_path_url) && (RequestUtilities::get('issue_date') || !$year_param))){
      return;
    }

    $url = request_uri();
    $url_parts = parse_url($url);
    $urlPath = RequestUtilities::getFrontPagePath() ? RequestUtilities::getFrontPagePath() : $url_parts['path'];
    $urlQuery = $url_parts['query'] ?? null;


//Set $yearParamValue and $yearTypeParamValue from the current URL
    if(RequestUtilities::get('year')){
      $yearParamValue = RequestUtilities::get('year');
    }else if(RequestUtilities::get('calyear')){
      $yearParamValue = RequestUtilities::get('calyear');
    }else if(str_contains($url, "contracts_pending_exp_landing") || str_contains($url, "contracts_pending_rev_landing")){
      //Set $year_id_value to current Fiscal Year ID for Pending Contracts
      $yearParamValue = CheckbookDateUtil::getCurrentFiscalYearId();
    }
    $yearTypeParamValue = (RequestUtilities::get('yeartype')) ? RequestUtilities::get('yeartype') : 'B';
//Pending Contracts do not have year filter applicable, so the Date Filter options are set to navigate to Active Expense Contracts landing page for the latest Fiscal Year
    if(str_contains($url, "contracts_pending_exp_landing")){
      $urlPath = preg_replace("/contracts_pending_exp_landing/","contracts_landing/status/A", $urlPath);
    }elseif(str_contains($url, "contracts_pending_rev_landing")){
      $urlPath = preg_replace("/contracts_pending_rev_landing/","contracts_landing/status/A", $urlPath);
    }elseif(PageType::getCurrent() == PageType::TRENDS_PAGE || PageType::getCurrent() == PageType::FEATURED_TRENDS_PAGE){
      $trends = true;
      $urlPath = "/spending_landing/yeartype/B/year/";
    }

    $dataSource = Datasource::getCurrent();
    $domain = CheckbookDomain::getCurrent();
    $fiscalYears = CheckbookDateUtil::getFiscalYearOptionsRange($dataSource, $domain);
    $calendarYears = CheckbookDateUtil::getCalendarYearOptionsRange($dataSource);
    $fyDisplayData = array();
    $cyDisplayData = array();
    $yearListOptions = array();

    $current_year_id = CheckbookDateUtil::getCurrentFiscalYearId();
//Fiscal Year options (We do not need to calculate Fiscal Years for NYCHA Payroll)
    if(!($domain == CheckbookDomain::$PAYROLL && $dataSource == Datasource::NYCHA)) {
      foreach ($fiscalYears as $key => $value) {
        $selectedFY = (isset($yearParamValue) && $value['year_id'] == $yearParamValue && 'B' == $yearTypeParamValue) ? 'selected = yes' : "";

        //For TrendsNYCCHKBK-9474, set the default year value to current NYC fiscal year
        if (isset($trends) && $trends) {
          //For Trends, append the year value for 'Spending' link
          $yearOptionUrl = $urlPath . $value['year_id'];
          if ($value['year_id'] == $current_year_id) {
            $selectedFY = 'selected = yes';
          }
        } else {
          $yearOptionUrl = preg_replace("/\/year\/[^\/]*/", "/year/" . $value['year_id'], $urlPath);
          //Year Option changes for bottom container
          if (isset($urlQuery)) {
            $bottomURLOption = preg_replace("/\/year\/[^\/]*/", "/year/" . $value['year_id'], $urlQuery);
            //For charts with months links, we need to persist the month param for the newly selected year
            if (preg_match('/month/', $urlQuery)) {
              $oldMonthId = RequestUtil::getRequestKeyValueFromURL("month", $urlQuery);
              if (isset($oldMonthId) && isset($value['year_id'])) {
                $newMonthId = CheckbookDateUtil::_translateMonthIdByYear($oldMonthId, $value['year_id']);
                $bottomURLOption = preg_replace("/\/month\/[^\/]*/", "/month/" . $newMonthId, $bottomURLOption);
              }
            }
            //For NYCHA Spending Transaction pages handle 'issue date' parameter
            if ($dataSource == Datasource::NYCHA && (str_contains($urlQuery, "wt_issue_date"))) {
              $oldIssueDate = RequestUtil::getRequestKeyValueFromURL("issue_date", $urlQuery);
              $oldIssueDateParts = explode("~", $oldIssueDate);
              $month = date("n", strtotime($oldIssueDateParts[0]));
              $newIssueDate = $value['year_value'] . "-" . $month . "-01~" . $value['year_value'] . "-" . $month . "-31";
              $bottomURLOption = preg_replace("/\/issue_date\/[^\/]*/", "issue_date/" . $newIssueDate, $bottomURLOption);
            }
            $yearOptionUrl = $yearOptionUrl . '?' . $bottomURLOption;
          }
        }

        //Set year type 'B' for all Fiscal year options
        $yearOptionUrl = preg_replace("/yeartype\/./", "yeartype/B", $yearOptionUrl);
        $displayText = ($dataSource == Datasource::NYCHA) ? 'FY ' . $value['year_value'] . ' (Jan 1, ' . $value['year_value'] . ' - Dec 31, ' . $value['year_value'] . ')' :
          'FY ' . $value['year_value'] . ' (Jul 1, ' . ($value['year_value'] - 1) . ' - Jun 30, ' . $value['year_value'] . ')';
        $fyDisplayData[] = array('display_text' => $displayText,
          'link' => Xss::filter($yearOptionUrl),
          'value' => $value['year_id'] . '~B',
          'selected' => $selectedFY
        );
      }
    }

//Calendar Year options: Required only for Payroll domain (Citywide and NYCHA)
    if($domain == CheckbookDomain::$PAYROLL) {
      foreach ($calendarYears as $key => $value) {
        $selectedCY = ($value['year_id'] == $yearParamValue && 'C' == $yearTypeParamValue) ? 'selected = yes' : "";
        $yearOptionUrl = preg_replace("/\/year\/[^\/]*/","/year/" .  $value['year_id'], $urlPath);

        if (isset($urlQuery)) {
          $bottomURLOption = preg_replace("/\/year\/[^\/]*/","/year/" .  $value['year_id'], $urlQuery);
          //For charts with months links, we need to persist the month param for the newly selected year
          if (str_contains($urlQuery, 'month')) {
            $oldMonthId = RequestUtil::getRequestKeyValueFromURL("month", $urlQuery);
            if (isset($oldMonthId) && isset($value['year_id'])) {
              $newMonthId = CheckbookDateUtil::_translateMonthIdByYear($oldMonthId, $value['year_id']);
              $bottomURLOption = preg_replace("/\/month\/[^\/]*/","/month/" .  $newMonthId, $bottomURLOption);
            }
          }
          $yearOptionUrl = $yearOptionUrl . '?' . $bottomURLOption;
        }

        //Set year type 'C' for all calendar year options
        $yearOptionUrl = preg_replace("/yeartype\/./", "yeartype/C", $yearOptionUrl);
        $displayText = 'CY '.$value['year_value'].' (Jan 1, '.$value['year_value'].' - Dec 31, '.$value['year_value'].')';
        $cyDisplayData[] = array('display_text' => $displayText,
          'value' => $value['year_id'] . '~C',
          'link' => $yearOptionUrl,
          'selected' => $selectedCY
        );
      }
    }

//Merge Fiscal Year and Calendar Year Options
    $yearListOptions = array_merge($cyDisplayData, $fyDisplayData);


//HTML for Date Filter
    $year_list = "<span class='filter' >Filter: </span><select id='year_list'>";
    foreach($yearListOptions as $year){
      $year_list .= "<option ".$year['selected']." value=".$year['value']." link='" . $year['link'] . "'  >".$year['display_text']."</option>";
    }
    $year_list .= "</select>";

    return $year_list;
  }
}
