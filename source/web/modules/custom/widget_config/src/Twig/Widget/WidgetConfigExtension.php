<?php

namespace Drupal\widget_config\Twig\Widget;

use Drupal\checkbook_custom_breadcrumbs\ContractsBreadcrumbs;
use Drupal\checkbook_custom_breadcrumbs\PayrollBreadcrumbs;
use Drupal\checkbook_custom_breadcrumbs\SpendingBreadcrumbs;
use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\checkbook_project\CommonUtilities\RequestUtil;
use Drupal\checkbook_project\ContractsUtilities\ContractURLHelper;
use Drupal\checkbook_project\WidgetUtilities\WidgetUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class WidgetConfigExtension extends AbstractExtension
{

  /**
   * Generates a list of all Twig functions that this extension defines.
   *
   * @return array
   *   A key/value array that defines custom Twig functions. The key denotes the
   *   function name used in the tag, e.g.:
   * @code
   *   {{ testfunc() }}
   * @endcode
   *
   *   The value is a standard PHP callback that defines what the function does.
   */
  public function getFunctions()
  {
    return [
      'customAmount' => new TwigFunction('customAmount', [
        $this,
        'customAmount',
      ]),
      'customNumber' => new TwigFunction('customNumber', [
        $this,
        'customNumber',
      ]),
      'customPercDiff' => new TwigFunction('customPercDiff', [
        $this,
        'customPercDiff',
      ]),
      'customFunctionEvaluate' => new TwigFunction('customFunctionEvaluate', [
        $this,
        'customFunctionEvaluate',
      ]),
      'customGetYear' => new TwigFunction('customGetYear', [
        $this,
        'customGetYear',
      ]),
      'customWidgetUtilGetLabel' => new TwigFunction('customWidgetUtilGetLabel', [
        $this,
        'customWidgetUtilGetLabel',
      ]),
      'customWidgetLabel' => new TwigFunction('customWidgetLabel', [
        $this,
        'customWidgetLabel',
      ]),
      'customWidgetLabelNoDiv' => new TwigFunction('customWidgetLabelNoDiv', [
        $this,
        'customWidgetLabelNoDiv',
      ]),
      'customHeaderColumns' => new TwigFunction('customHeaderColumns', [
        $this,
        'customHeaderColumns',
      ]),
      'customColumns' => new TwigFunction('customColumns', [
        $this,
        'customColumns',
      ]),
      'customStringToDate' => new TwigFunction('customStringToDate', [
        $this,
        'customStringToDate',
      ]),
      'getWidgetNodeView' => new TwigFunction('getWidgetNodeView', [
        $this,
        'getWidgetNodeView',
      ]),

      'pageGridTitle' => new TwigFunction('pageGridTitle', [
        $this,
        'pageGridTitle',
      ]),
      'gridPrintExport' => new TwigFunction('gridPrintExport', [
        $this,
        'gridPrintExport',
      ]),
      'chartGrid' => new TwigFunction('chartGrid', [
        $this,
        'chartGrid',
      ]),
      'chartGridTitle' => new TwigFunction('chartGridTitle', [
        $this,
        'chartGridTitle',
      ]),

      'isPrimeMwbe' => new TwigFunction('isPrimeMwbe', [
        $this,
        'isPrimeMwbe',
      ]),
      'isMwbeAgencyGrading' => new TwigFunction('isMwbeAgencyGrading', [
        $this,
        'isMwbeAgencyGrading',
      ]),

      'RequestGet' => new TwigFunction('RequestGet', [
        $this,
        'RequestGet',
      ]),
      'RequestHas' => new TwigFunction('RequestHas', [
        $this,
        'RequestHas',
      ]),
      'pregReplace' => new TwigFunction('pregReplace', [
        $this,
        'pregReplace',
      ]),
      //pregMatch
      'pregMatch' => new TwigFunction('pregMatch', [
        $this,
        'pregMatch',
      ]),

      'checkbook_checkEDC' => new TwigFunction('checkbook_checkEDC', [
        $this,
        'checkbook_checkEDC',
      ]),

      'thirdBottomSlider' => new TwigFunction('thirdBottomSlider', [
        $this,
        'thirdBottomSlider',
      ]),

      'checkbook_vendor_link' => new TwigFunction('checkbook_vendor_link', [
        $this,
        'checkbook_vendor_link',
      ]),
      'getKey' => new TwigFunction('getKey', [
        $this,
        'getKey',
      ]),
      'getMonthDetailFromURL' => new TwigFunction('getMonthDetailFromURL', [
        $this,
        'getMonthDetailFromURL',
      ]),
      'getTooltipMarkup' => new TwigFunction('getTooltipMarkup', [
        $this,
        'getTooltipMarkup',
      ]),
      'nodeDataExists' => new TwigFunction('nodeDataExists', [
        $this,
        'nodeDataExists',
      ]),
      'checkbook_agency_link' => new TwigFunction('checkbook_agency_link', [
        $this,
        'checkbook_agency_link',
      ])
    ];
  }

  public function getKey($value) {
    return key($value);
  }

  public function nodeDataExists($node) {
    if(is_array($node->data) and count($node->data) > 0) {
      return true;
    } else {
      return false;
    }
  }

  public function getMonthDetailFromURL() {
    $month = '';
    $monthDetails = CheckbookDateUtil::getMonthDetails(RequestUtilities::get('month'));
    if(isset($monthDetails)){
      $month = strtoupper($monthDetails[0]['month_name']);
    }
    return $month;
  }

  public function getTooltipMarkup($text, $length = 20, $no_of_lines = 2) {
    return FormattingUtilities::_get_tooltip_markup($text, $length, $no_of_lines);
  }

  public function customAmount($amount, $digits, $digival)
  {
    return FormattingUtilities::custom_number_formatter_format($amount, $digits, $digival);
  }
  public function customNumber($digits,$decimals =null)
  {
    return number_format($digits,$decimals);
  }

  public function customPercDiff($percentValue)
  {
    return round($percentValue,2). '%';
  }

  public function customFunctionEvaluate($func)
  {
    return eval($func);
  }

  public function customGetYear($type = null)
  {
    $year = CheckbookDateUtil::_getYearValueFromID(RequestUtilities::get('year') + $type);
    return $year;
  }

  public function customWidgetUtilGetLabel($name) {
    return WidgetUtil::getLabel($name);
  }

  public function customWidgetLabel($name) {
    return WidgetUtil::generateLabelMapping($name);
  }

  public function customWidgetLabelNoDiv($name) {
    return WidgetUtil::generateLabelMappingNoDiv($name);
  }

  public function customHeaderColumns($columns) {
    return WidgetUtil::generateHeaderColumns($columns);
  }

  public function customColumns($columns) {
    return WidgetUtil::generateColumns($columns);
  }

  public function customStringToDate($string) {
    return FormattingUtilities::format_string_to_date($string);
  }

  public function getWidgetNodeView($id) {
    $node = _widget_node_load_file($id);
    $node = widget_node_view($node);
    //Render the custom template
    if(isset($node->widgetConfig->template)) {
      return [
        '#theme' => $node->widgetConfig->template,
        '#node' => $node,
      ];
    }else {
      // display the node body content
      return $node->content['body'];
    }
  }

  //Grid View Title
  public function pageGridTitle($domain)
  {

    //$refURL = Drupal::request()->server->get('refURL');
    $refURL = RequestUtilities::getRefUrl();
    if (!(RequestUtil::isPendingExpenseContractPath($refURL) || RequestUtil::isPendingRevenueContractPath($refURL))) {
      //$pageTitle = '<h3 class="grid_year_title">' . (isset($domain) ? ($domain . ' ' . CheckbookDateUtil::_getFullYearString()) : CheckbookDateUtil::_getFullYearString()) . '</h3>';
      $yearId = RequestUtilities::get('year', ['q' => $refURL]);
      $yearId = empty(((empty($yearId))) ? RequestUtilities::get('calyear', ['q' => $refURL]) : $yearId) ? CheckbookDateUtil::getCurrentFiscalYearId() :  $yearId;
      $yearType = RequestUtilities::get('yeartype', ['q' => $refURL]);
      $yearType = (empty($yearType)) ? 'B' : $yearType;
      $datasource = RequestUtilities::get('datasource', ['q' => $refURL]);
      $pageTitle = '<h3 class="grid_year_title">' . (isset($domain) ? ($domain . ' ' . CheckbookDateUtil::getFullYearString($yearId,$yearType, $datasource)) : CheckbookDateUtil::getFullYearString($yearId,$yearType, $datasource)) . '</h3>';
    } else {
      //For UI alignment purpose
      $pageTitle = '<h3 class="grid_year_title">&nbsp;</h3>';
    }
    return $pageTitle;
  }

  //Grid View / print / export
  public function gridPrintExport($node)
  {
    //var_dump($node->widgetConfig->gridConfig->domain);
    $output = '<span class="grid_print" id="printgrid" title="Display a printer-friendly version of this page.">Printer Friendly Version</span>
     <div class="grid">
     <span class="grid_export" exportid="'.$node->nid.'">EXPORT</span>
    </div>';
    return $output;

  }

  //Grid View custom
  public function chartGrid($node)
  {
    $output = \Drupal\widget_config\Utilities\ChartGrid::chartGridDisplay($node);
    return $output;
  }
  public function chartGridTitle($domain)
  {
    $refURL = RequestUtilities::getRefUrl();
    $datasource = RequestUtilities::get('datasource', ['q' => $refURL]);
    return match (strtolower($domain)) {
      "spending" => SpendingBreadcrumbs::getSpendingPageTitle(),
      "payroll" => PayrollBreadcrumbs::getPayrollPageTitle(RequestUtilities::getRefUrl()),
      "contracts" => isset($datasource) ? ContractsBreadcrumbs::getNychaContractsPageTitle(): ContractsBreadcrumbs::getContractsPageTitle(),
    };
  }

  public function isMwbeAgencyGrading($page = '')
  {
    $currentUri = RequestUtilities::getRequestUri();
    return strpos($currentUri, '/mwbe_agency_grading/' . $page) !== FALSE;
  }

  public function RequestGet($paramName, $options = [])
  {
    $bottomUrl = RequestUtilities::getBottomContUrl();
    return isset($bottomUrl) ? RequestUtilities::_getRequestParamValueBottomURL($paramName):RequestUtilities::get($paramName);
  }
  public function RequestHas($paramName)
  {
    $paramValue = self::RequestGet($paramName);
    if (isset($paramValue)) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  public function pregReplace($pattern, $replace, $aliase)
  {
    return preg_replace($pattern, $replace, $aliase);
  }

  public function pregMatch($pattern, $aliase)
  {
    return preg_match($pattern, $aliase);
  }

  public function checkbook_checkEDC()
  {
    return RequestUtilities::_checkbook_check_isEDCPage();
  }

  public function checkbook_vendor_link($vendor_id, $prime=FALSE) {
    return ContractURLHelper::_checkbook_vendor_link($vendor_id,$prime);
  }

  public function checkbook_agency_link($vendor_id, $prime=FALSE) {
    return ContractURLHelper::_checkbook_agency_link($vendor_id,$prime);
  }

} //CLASS ENDS
