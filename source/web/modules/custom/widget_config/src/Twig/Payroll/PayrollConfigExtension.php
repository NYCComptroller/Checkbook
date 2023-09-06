<?php

namespace Drupal\widget_config\Twig\Payroll;

use Drupal\checkbook_custom_breadcrumbs\PayrollBreadcrumbs;
use Drupal\checkbook_infrastructure_layer\Constants\Common\Datasource;
use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\CommonUtilities\CheckbookDateUtil;
use Drupal\checkbook_project\CommonUtilities\CustomURLHelper;
use Drupal\checkbook_project\PayrollUtilities\PayrollType;
use Drupal\checkbook_project\PayrollUtilities\PayrollUtil;
use Drupal\checkbook_project\WidgetUtilities\WidgetUtil;
use Drupal\widget_config\Utilities\PayrollAgencySummary;
use Drupal\widget_config\Utilities\PayrollGrid;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PayrollConfigExtension extends AbstractExtension
{
  public function getFunctions()
  {
    return [
      'payrolljs' => new TwigFunction('payrolljs', [
        $this,
        'payrolljs',
      ]),
      'payrollDataProcess' => new TwigFunction('payrollDataProcess', [
        $this,
        'payrollDataProcess',
      ]),
      'payrollToggle' => new TwigFunction('payrollToggle', [
        $this,
        'payrollToggle',
      ]),
      'payrollAgencyUrl' => new TwigFunction('payrollAgencyUrl', [
        $this,
        'payrollAgencyUrl',
      ]),
      'payrollTitleUrl' => new TwigFunction('payrollTitleUrl', [
        $this,
        'payrollTitleUrl',
      ]),
      'payrollGridTitle' => new TwigFunction('payrollGridTitle', [
        $this,
        'payrollGridTitle',
      ]),
      'payrollMonthSummary' => new TwigFunction('payrollMonthSummary', [
        $this,
        'payrollMonthSummary',
      ]),
      'payrollGross' => new TwigFunction('payrollGross', [
        $this,
        'payrollGross',
      ]),
      'payrollSummary' => new TwigFunction('payrollSummary', [
      $this,
        'payrollSummary',
      ]),
      'payrollEmpSummary' => new TwigFunction('payrollEmpSummary', [
        $this,
        'payrollEmpSummary',
      ])

    ];
  }
  public function payrollDataProcess($node,$type)
  {
    $all_data = array();
    switch ($type) {
      case 'agency':
        foreach($node->data as $data){
          $record = array();
          $year = $data['fiscal_year_id'];
          $year_type = $data['type_of_year'];
          $employment_type = $data['type_of_employment'];
          $agency_name = FormattingUtilities::_shorten_word_with_tooltip(strtoupper($data['agency_name']),25);
          $data_source = RequestUtilities::_getUrlParamString('datasource');
          $record['total_annual_salary'] = $node->total_annual_salary ?? null;
          $record['total_gross_pay'] = $data['total_gross_pay'] ?? null;
          $record['total_base_pay'] = $data['total_base_pay'] ?? null;
          $record['total_other_payments'] = $data['total_other_payments'] ?? null;
          $record['total_overtime_pay'] = $data['total_overtime_pay'] ?? null;
          $record['total_employees'] = $data['total_employees'] ?? null;
          $record['total_overtime_employees'] = $data['total_overtime_employees'] ?? null;
          $record['number_employees'] = $data['number_employees'] ?? null;
          $record['agency_name'] = $data['agency_name'] ?? null;
          $record['agency_url'] = "<a href='/payroll/agency_landing/yeartype/".$year_type."/year/".$year.$data_source."/agency/".$data['agency_id']."'>".$agency_name."</a>";
          $record['agencyval'] = Datasource::isNycha() ? $agency_name : $record['agency_url'];
          $all_data[$employment_type][] = $record;
        }
        break;
      case 'title':
        foreach($node->data as $data){
          $record = array();
          $year = $data['year_year'];
          $year_type = $data['year_type_year_type'];
          $employment_type = $data['type_of_employment'];
          $record['total_annual_salary'] = $node->total_annual_salary?? null;
          $record['total_gross_pay'] = $data['total_gross_pay']?? null;
          $record['total_base_pay'] = $data['total_base_pay']?? null;
          $record['total_other_payments'] = $data['total_other_payments']?? null;
          $record['total_overtime_pay'] = $data['total_overtime_pay']?? null;
          $record['total_employees'] = $data['total_employees']?? null;
          $record['number_employees'] = $data['number_employees']?? null;
          $record['total_overtime_employees'] = $data['total_overtime_employees']?? null;
          $all_data[$employment_type][] = $record;
        }
        break;
      case 'emp_agency':
        $year_temp = RequestUtilities::getTransactionsParams('year');
        $calyear_temp = RequestUtilities::getTransactionsParams('calyear');
        $year = $year_temp?$year_temp:$calyear_temp;
        $year_type = RequestUtilities::getTransactionsParams('yeartype');
        $employeeID = RequestUtilities::getTransactionsParams('abc');
        $agencyId = RequestUtilities::getTransactionsParams('agency');
        $data_source = RequestUtilities::getTransactionsParams('datasource');
        $original_title= PayrollUtil::getTitleByEmployeeId($employeeID,$agencyId,$year_type,$year);
        $titleLatest = mb_convert_case(strtolower($original_title), MB_CASE_TITLE, "UTF-8");
        foreach($node->data as $data) {
          $record = array();
          $amount_basis_id = $data['amount_basis_id_amount_basis_id']?? null;
          $employment_type = $amount_basis_id == 1 ? PayrollType::$SALARIED : PayrollType::$NON_SALARIED;

          $array_year = RequestUtilities::getTransactionsParams('year');
          if ($array_year > 0) {
            $year = $array_year;
          }
          $array_calyear = RequestUtilities::getTransactionsParams('calyear');
          if ($array_calyear > 0) {
            $year = $array_calyear;
          }
          $year_type = RequestUtilities::getTransactionsParams('yeartype');

          $original_title = $data['civil_service_title_civil_service_title']?? null;
          $title = mb_convert_case(strtolower($original_title), MB_CASE_TITLE, "UTF-8");
          $agency_name = FormattingUtilities::_shorten_word_with_tooltip(strtoupper($data['agency_name_agency_name']?? null), 25);
          $record['title'] = $title;
          $record['agency_name'] = $data['agency_name_agency_name']?? null;
          $record['agency_url'] = "<a href='/payroll/agency_landing/yeartype/$year_type/year/$year/datasource/{$data_source}/agency/{$data['agency_agency']}'>{$agency_name}</a>";
          $record['employment_type'] = $employment_type;
          $record['max_annual_salary'] = $data['max_annual_salary']?? null;
          $record['pay_frequency'] = $data['pay_frequency_pay_frequency']?? null;
          $record['total_gross_pay'] = $data['total_gross_pay']?? null;
          $record['total_base_salary'] = $data['total_base_salary']?? null;
          $record['total_other_payments'] = $data['total_other_payments']?? null;
          $record['total_overtime_amount'] = $data['total_overtime_amount']?? null;
          $record['titleLatest'] = $titleLatest;
          $all_data[$employment_type][] = $record;
        }
        //Order data by pay frequency
        foreach($all_data as $employment_type => $employment_data) {

          $ordered_data = array();
          $data = PayrollUtil::getDataByPayFrequency("BI-WEEKLY",$employment_data);
          if(isset($data)) $ordered_data[] = $data;
          $data = PayrollUtil::getDataByPayFrequency("SEMI-MONTHLY",$employment_data);
          if(isset($data)) $ordered_data[] = $data;
          $data = PayrollUtil::getDataByPayFrequency("WEEKLY",$employment_data);
          if(isset($data)) $ordered_data[] = $data;
          $data = PayrollUtil::getDataByPayFrequency("DAILY",$employment_data);
          if(isset($data)) $ordered_data[] = $data;
          $data = PayrollUtil::getDataByPayFrequency("HOURLY",$employment_data);
          if(isset($data)) $ordered_data[] = $data;
          $data = PayrollUtil::getDataByPayFrequency("SUPPLEMENTAL",$employment_data);
          if(isset($data)) $ordered_data[] = $data;
          $all_data[$employment_type] = $ordered_data;
        }
        break;
      default:
    }

    return $all_data;
  }

  public function payrolljs($all_data)
  {
    $salaried_count = is_array($all_data[PayrollType::$SALARIED]) ? count($all_data[PayrollType::$SALARIED]) : 0;
    $non_salaried_count = is_array($all_data[PayrollType::$NON_SALARIED]) ? count($all_data[PayrollType::$NON_SALARIED]) : 0;
    //Default view based on salamttype in url
    $default_view = $salaried_count > 0 ? PayrollType::$SALARIED : PayrollType::$NON_SALARIED;

    $salamttype = RequestUtilities::getTransactionsParams('salamttype');

   //Default view based on payroll type in url
    $payroll_type = PayrollUtil::getPayrollType();
    if (isset($salamttype)) {
      $salamttype = explode('~', $salamttype);
      if (!in_array(1, $salamttype)) {
        $default_view = PayrollType::$NON_SALARIED;
      }
    }
    if ($payroll_type == PayrollType::$NON_SALARIED) {
      $default_view = PayrollType::$NON_SALARIED;
    }


    $js = "";
    $employeeData = '<div class="payroll-emp-wrapper">';
    foreach ($all_data as $employment_type => $employment_data) {

      if (($employment_type == PayrollType::$SALARIED && $salaried_count > 1)
        || ($employment_type == PayrollType::$NON_SALARIED && $non_salaried_count > 1)) {
        $class = strtolower($employment_type);
        $js .= "

                jQuery(document).ready(function() {
                    if (jQuery('#emp-agency-detail-records-$class').filter(':first').length > 0) {
                        jQuery('#emp-agency-detail-records-$class').filter(':first')
                            .cycle({
                                slideExpr:'.emp-agency-detail-record',
                                prev: '#prev-emp-$class',
                                next: '#next-emp-$class',
                                fx: 'scrollVert',
                                speed: 0,
                                width:'640px',
                                timeout: 0
                            });
                    }
                });";
      }
    }

    if ($default_view == PayrollType::$SALARIED) {
      $showhide = "
        jQuery('.emp-record-salaried').show();
        jQuery('.emp-record-non-salaried').hide();
    ";
    } else {
      $showhide = "
        jQuery('.emp-record-salaried').hide();
        jQuery('.emp-record-non-salaried').show();
    ";
    }
    $js .= "
        function toggleEmployee() {
            jQuery('.emp-record-salaried').toggle();
            jQuery('.emp-record-non-salaried').toggle();
        };
        jQuery(document).ready(function() {
          jQuery('.toggleEmployee').click(toggleEmployee);
          $showhide
        });
    ";
    return "<script type='text/javascript'>".$js."</script>";
  }

  public function payrollToggle($all_data)
  {
    $salaried_count = is_array($all_data[PayrollType::$SALARIED]) ? count($all_data[PayrollType::$SALARIED]) : 0;
    $non_salaried_count = is_array($all_data[PayrollType::$NON_SALARIED]) ? count($all_data[PayrollType::$NON_SALARIED]) : 0;
    $output = '';
    if ($salaried_count && $non_salaried_count) {
      $output .= "<div id='toggle-employee-salaried' class='emp-record-salaried toggleEmployee'>
                            <strong>Viewing Salaried Details</strong>&nbsp;|&nbsp;
                            <a>View Non-salaried Details</a>
                          </div>";
      $output .= "<div id='toggle-employee-non-salaried' class='emp-record-non-salaried toggleEmployee'>
                            <a>View Salaried Details</a>&nbsp;|&nbsp;
                            <strong>Viewing Non-salaried Details</strong>
                          </div>";
    }
    return $output;
  }
  public function payrollAgencyUrl($agency_name,$agency_url)
  {
    $agency_url = Datasource::isNycha() ? $agency_name : $agency_url;
    return $agency_url;
  }

  public function payrollTitleUrl($original_title)
  {
    $title = strtolower($original_title);
    $title = mb_convert_case($title, MB_CASE_TITLE, "UTF-8");
    $titleUrl  = "<a href='/payroll".CustomURLHelper::_checkbook_project_get_year_url_param_string() ."/title/" . urlencode($original_title) . "'>".$title."</a>";
    return $titleUrl;
  }

  public function payrollGross($node)
  {
    $grossot = PayrollGrid::payrollGrossOt($node);
    return $grossot;
  }

  public function payrollMonthSummary($node)
  {
    if(is_array($node->data) && count($node->data) > 0){

      print  '<div class="payroll-emp-wrapper">';
      $employeeData = '';


      $employeeData .= "<div id='emp-agency-detail-records'>";

      foreach($node->data as $results) {

        $employment_type = $results['type_of_employment'];
        $class = strtolower($employment_type);
        $total_annual_salary = FormattingUtilities::custom_number_formatter_format($node->total_annual_salary,2,'$');
        $total_gross_pay = FormattingUtilities::custom_number_formatter_format($results['total_gross_pay'],2,'$');
        $total_base_pay = FormattingUtilities::custom_number_formatter_format($results['total_base_pay'],2,'$');
        $total_other_payments = FormattingUtilities::custom_number_formatter_format($results['total_other_payments'],2,'$');
        $total_overtime_pay = FormattingUtilities::custom_number_formatter_format($results['total_overtime_pay'],2,'$');
        $number_employees = number_format($results['number_employees']);
        $total_employees =  number_format($node->total_employees);

        //Amount labels
        $lbl_annual_salary = WidgetUtil::getLabel('combined_annual_salary');
        $lbl_gross_pay = WidgetUtil::getLabel('combined_gross_pay');
        $lbl_base_pay = WidgetUtil::getLabel('combined_base_pay');
        $lbl_other_pay = WidgetUtil::getLabel('combined_other_pay');
        $lbl_overtime_pay = WidgetUtil::getLabel('combined_overtime_pay');

        $year = $results['fiscal_year_id'];
        $month_id = $results['month_id'];
        $month_name = $results['month_name'];
        $type_of_year = $results['type_of_year'];
        $year_value = CheckbookDateUtil::_getYearValueFromID($year);
        $year_type = 'FY';
        $js ="";
        if($type_of_year == 'C') {
          $year_type = 'CY';
        }
        if( $employment_type== PayrollType::$SALARIED){
          $salaried_count = $number_employees;
        }


        //Default view based on salamttype in url
        $default_view = $salaried_count > 0 ? PayrollType::$SALARIED : PayrollType::$NON_SALARIED;

        $array_smnid = RequestUtilities::_getRequestParamValueBottomURL('smnid');
        $array_smnid = $array_smnid ?? RequestUtilities::get('smnid');
        if($array_smnid == 491 || $array_smnid == 492) {
          $total_overtime_employees_label = WidgetUtil::getLabel('total_no_of_ot_employees').':';
          $overtime_employees_value = number_format($results['total_overtime_employees']);
        }
        $table = "<div class='emp-agency-detail-record'><table id='emp-agency-detail-record-table' class='emp-record-$class'>

                <div class='payroll-year-month emp-record-$class'>
                    <span class='label'>". WidgetUtil::getLabel('month') .": </span><span class='data'> {$month_name} </span>
                     &nbsp;&nbsp;|&nbsp;&nbsp;
                    <span class='label'>".WidgetUtil::getLabel('year') .":</span><span class='data'> {$year_type} {$year_value}</span>
                </div>
                ";
        if($employment_type == PayrollType::$SALARIED) {
          $table .= "
                <tr>
                    <td width='40%'><strong>". $lbl_annual_salary ."</strong>: {$total_annual_salary}</td>
                    <td><strong>". WidgetUtil::getLabel('payroll_type') ."</strong>: ". strtoupper($employment_type)."</td>

                </tr>
                <tr>
                    <td><strong>". $lbl_gross_pay ."</strong>: {$total_gross_pay}</td>
                    <td width='50%'><strong>". WidgetUtil::getLabel('total_no_of_employees') ."</strong>: {$total_employees}</td>

                </tr>
                <tr>
                    <td><strong>". $lbl_base_pay ."</strong>: {$total_base_pay}</td>
                    <td><strong>". WidgetUtil::getLabel('total_no_of_sal_employees') ."</strong>: {$number_employees}</td>";
          $table .= "</tr>
                <tr>
                    <td><strong>". $lbl_other_pay ."</strong>: {$total_other_payments}</td>";
          if(isset($overtime_employees_value)){
            $table .= "<td><strong>{$total_overtime_employees_label} </strong> {$overtime_employees_value}</td>";
          }else{
            $table .= "<td></td>";
          }
          $table .="</tr>
                <tr>
                    <td><strong>". $lbl_overtime_pay ."</strong>: {$total_overtime_pay}</td>
                    <td></td>
                </tr>";
        } else {
          $table .= "
                <tr>
                    <td width='50%'><strong>". $lbl_gross_pay ."</strong>: {$total_gross_pay}</td>
                    <td><strong>". WidgetUtil::getLabel('payroll_type') ."</strong>: ". strtoupper($employment_type)."</td>

                </tr>
                <tr>
                    <td><strong>". $lbl_base_pay ."</strong>:{$total_base_pay}</td>
                    <td width='50%'><strong>". WidgetUtil::getLabel('total_no_of_employees') ."</strong>: {$total_employees}</td>

                </tr>
                <tr>
                    <td><strong>". $lbl_other_pay ."</strong>: {$total_other_payments}</td>
                    <td><strong>". WidgetUtil::getLabel('total_no_of_non_sal_employees') ."</strong>: {$number_employees}</td>";
          $table .= "</tr>
                <tr>
                    <td><strong>". $lbl_overtime_pay ."</strong>: {$total_overtime_pay}</td>";
          if(isset($overtime_employees_value)){
            $table .= "<td><strong>{$total_overtime_employees_label} </strong> {$overtime_employees_value}</td>";
          }else{
            $table .= "<td></td>";
          }

          $table .="</tr>";
        }
        $table .= "</table></div>";
        $employeeData .= $table;
      }
      if ($default_view == PayrollType::$SALARIED) {
        $showhide = "
        jQuery('.emp-record-salaried').show();
        jQuery('.emp-record-non-salaried').hide();
    ";
      } else {
        $showhide = "
        jQuery('.emp-record-salaried').hide();
        jQuery('.emp-record-non-salaried').show();
    ";
      }
      $js .= "
        function toggleEmployee() {
            jQuery('.emp-record-salaried').toggle();
            jQuery('.emp-record-non-salaried').toggle();
        };
        jQuery(document).ready(function() {
          jQuery('.toggleEmployee').click(toggleEmployee);
          $showhide
        });
    ";


        print "<script type='text/javascript'>" . $js . "</script>";

      if (is_array($node->data)  && count($node->data) > 1) {
        $employeeData .= "<div id='toggle-employee-salaried' class='emp-record-salaried toggleEmployee'>
                            <strong>Viewing Salaried Details</strong>&nbsp;|&nbsp;
                            <a>View Non-salaried Details</a>
                          </div>";
        $employeeData .= "<div id='toggle-employee-non-salaried' class='emp-record-non-salaried toggleEmployee'>
                            <a>View Salaried Details</a>&nbsp;|&nbsp;
                            <strong>Viewing Non-salaried Details</strong>
                          </div>";
        $employeeData .= "</div></div>";
      }
      else {
        $employeeData .= "</div></div>";
      }

      return $employeeData;
    }
  }

  /**
   * @return string|null
   */
  public static function payrollGridTitle(): ?string
  {
    return PayrollBreadcrumbs::getPayrollPageTitle(RequestUtilities::getRefUrl());
  }

  public static function payrollSummary($node)
  {
    return PayrollAgencySummary::payrollAgency($node);
  }

  public static function payrollEmpSummary($node)
  {
    return PayrollAgencySummary::payrollEmpAgency($node);
  }

}
