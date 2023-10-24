<?php

namespace Drupal\widget_config\Twig\Trends;


use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CapacityTrendsConfigExtension extends AbstractExtension
{
  const DOLLAR_SIGN = '<div class="dollarItem" >$</div>';

  public function getFunctions()
  {
    return [
      'ratiosOutstandingDebt' => new TwigFunction('ratiosOutstandingDebt', [
        $this,
        'ratiosOutstandingDebt',
      ]),
      'ratiosOutstandingDebtCsv' => new TwigFunction('ratiosOutstandingDebtCsv', [
        $this,
        'ratiosOutstandingDebtCsv',
      ]),
      'ratiosGeneralBond' => new TwigFunction('ratiosGeneralBond', [
        $this,
        'ratiosGeneralBond',
      ]),
      'pledgeRevConNyc' => new TwigFunction('pledgeRevConNyc', [
        $this,
        'pledgeRevConNyc',
      ]),
      'legalDebtMargin' => new TwigFunction('legalDebtMargin', [
        $this,
        'legalDebtMargin',
      ]),

      'assesedValEstdAct' => new TwigFunction('assesedValEstdAct', [
        $this,
        'assesedValEstdAct',
      ]),
      'propertyTaxRates' => new TwigFunction('propertyTaxRates', [
        $this,
        'propertyTaxRates',
      ]),
      'propertyTaxLevies' => new TwigFunction('propertyTaxLevies', [
        $this,
        'propertyTaxLevies',
      ]),
      'assesedValTaxRateClassTop' => new TwigFunction('assesedValTaxRateClassTop', [
        $this,
        'assesedValTaxRateClassTop',
      ]),
      'assesedValTaxRateClass' => new TwigFunction('assesedValTaxRateClass', [
        $this,
        'assesedValTaxRateClass',
      ]),
      'collectionsCancellationsAbatements' => new TwigFunction('collectionsCancellationsAbatements', [
        $this,
        'collectionsCancellationsAbatements',
      ]),
      'uncollectedParkingViolationFeeTop' => new TwigFunction('uncollectedParkingViolationFeeTop', [
        $this,
        'uncollectedParkingViolationFeeTop',
      ]),
      'uncollectedParkingViolationFee' => new TwigFunction('uncollectedParkingViolationFee', [
        $this,
        'uncollectedParkingViolationFee',
      ]),
      'hudsonYardsInfraCorp' => new TwigFunction('hudsonYardsInfraCorp', [
        $this,
        'hudsonYardsInfraCorp',
      ]),
      'capAssetsStatsProgramTop' => new TwigFunction('capAssetsStatsProgramTop', [
        $this,
        'capAssetsStatsProgramTop',
      ]),
      'capAssetsStatsProgram' => new TwigFunction('capAssetsStatsProgram', [
        $this,
        'capAssetsStatsProgram',
      ]),
      'noCityEmployeesTop' => new TwigFunction('noCityEmployeesTop', [
        $this,
        'noCityEmployeesTop',
      ]),
      'noCityEmployees' => new TwigFunction('noCityEmployees', [
        $this,
        'noCityEmployees',
      ]),
      'changesNetAssetsTop' => new TwigFunction('changesNetAssetsTop', [
        $this,
        'changesNetAssetsTop',
      ]),
      'changesNetAssets' => new TwigFunction('changesNetAssets', [
        $this,
        'changesNetAssets',
      ]),
      'fundBalGovtFundsTop' => new TwigFunction('fundBalGovtFundsTop', [
        $this,
        'fundBalGovtFundsTop',
      ]),
      'fundBalGovtFunds' => new TwigFunction('fundBalGovtFunds', [
        $this,
        'fundBalGovtFunds',
      ]),
      'changesFundBalTop' => new TwigFunction('changesFundBalTop', [
        $this,
        'changesFundBalTop',
      ]),
      'changesFundBal' => new TwigFunction('changesFundBal', [
        $this,
        'changesFundBal',
      ]),
      'generalFundRevenueOtherFinSourcesTop' => new TwigFunction('generalFundRevenueOtherFinSourcesTop', [
        $this,
        'generalFundRevenueOtherFinSourcesTop',
      ]),
      'generalFundRevenueOtherFinSources' => new TwigFunction('generalFundRevenueOtherFinSources', [
        $this,
        'generalFundRevenueOtherFinSources',
      ]),
      'generalFundExpendOtherFinSourcesTop' => new TwigFunction('generalFundExpendOtherFinSourcesTop', [
        $this,
        'generalFundExpendOtherFinSourcesTop',
      ]),
      'generalFundExpendOtherFinSources' => new TwigFunction('generalFundExpendOtherFinSources', [
        $this,
        'generalFundExpendOtherFinSources',
      ]),
      'capitalProjRevAgencyTop' => new TwigFunction('capitalProjRevAgencyTop', [
        $this,
        'capitalProjRevAgencyTop',
      ]),
      'capitalProjRevAgency' => new TwigFunction('capitalProjRevAgency', [
        $this,
        'capitalProjRevAgency',
      ]),
      'nycEduConstFund' => new TwigFunction('nycEduConstFund', [
        $this,
        'nycEduConstFund',
      ]),
      'nycPopulation' => new TwigFunction('nycPopulation', [
        $this,
        'nycPopulation',
      ]),
      'personalIncomeTaxRevenuesTop' => new TwigFunction('personalIncomeTaxRevenuesTop', [
        $this,
        'personalIncomeTaxRevenuesTop',
      ]),
      'personalIncomeTaxRevenues' => new TwigFunction('personalIncomeTaxRevenues', [
        $this,
        'personalIncomeTaxRevenues',
      ]),
      'nonAgrEmploymentTop' => new TwigFunction('nonAgrEmploymentTop', [
        $this,
        'nonAgrEmploymentTop',
      ]),
      'nonAgrEmployment' => new TwigFunction('nonAgrEmployment', [
        $this,
        'nonAgrEmployment',
      ]),
      'personsRecPubAsst' => new TwigFunction('personsRecPubAsst', [
        $this,
        'personsRecPubAsst',
      ]),
      'empStatusResidentPopulation' => new TwigFunction('empStatusResidentPopulation', [
        $this,
        'empStatusResidentPopulation',
      ]),
    ];
  }

  public static function  ratiosOutstandingDebt($node){
    $count = 1;
    foreach( $node->data as $row){
      $dollar_sign = ($count == 1) ? self::DOLLAR_SIGN : '';
      $count++;
      echo "<tr><td class='number'><div class='tdCen'>" . $row['fiscal_year'] . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['general_obligation_bonds']>0)?number_format($row['general_obligation_bonds']):'-') . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . (($row['revenue_bonds']>0)?number_format($row['revenue_bonds']):'-') . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . (($row['ecf']>0)?number_format($row['ecf']):'-') . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . (($row['mac_debt']>0)?number_format($row['mac_debt']):'-') . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . (($row['tfa']>0)?number_format($row['tfa']):'-') . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . (($row['tsasc_debt']>0)?number_format($row['tsasc_debt']):'-') . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . (($row['star']>0)?number_format($row['star']):'-') . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . (($row['fsc']>0)?number_format($row['fsc']):'-') . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . (($row['sfc_debt']>0)?number_format($row['sfc_debt']):'-') . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . (($row['hyic_bonds_notes']>0)?number_format($row['hyic_bonds_notes']):'-') . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . (($row['capital_leases_obligations']>0)?number_format($row['capital_leases_obligations']):'-') . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . (($row['ida_bonds']>0)?number_format($row['ida_bonds']):'-') . "</div></td>";
      if($row['treasury_obligations'] < 0 ) {
        echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>(" . number_format(abs($row['treasury_obligations'])) . ")</div></td>";
      }
      else if ($row['treasury_obligations'] == 0) {
        echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>-</div></td>";
      }
      else {
        echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  number_format($row['treasury_obligations']) . "</div></td>";
      }
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>". (($row['total_primary_government']>0)?number_format($row['total_primary_government']):'-') . "</div></td>";
      echo "<td>&nbsp;</td>";
      echo "</tr>";
    }
  }

  public static function  ratiosGeneralBond($node){
    $count = 1;
    foreach( $node->data as $row){
      $dollar_sign = ($count == 1) ? self::DOLLAR_SIGN : '';
      $percent_sign = ($count == 1) ? '<span class="endItem">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;%</span>' : '<span class="endItem" style="visibility:hidden;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;%</span>';

      echo "<tr><td class='number bonded'><div class='tdCen'>" . $row['fiscal_year'] . "</div></td>";
      echo "<td class='number bonded' style='padding-left: 30px;'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['general_bonded_debt']) . "</div></td>";
      echo "<td class='number bonded'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['debt_by_revenue_ot_prop_tax']) . "</div></td>";
      echo "<td class='number bonded'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['general_obligation_bonds']) . "</div></td>";
      echo "<td class='number bonded' style='padding-right: 30px;'><div class='tdCen'>" . number_format($row['percentage_atcual_taxable_property'],2) . $percent_sign. "</div></td>";
      echo "<td class='number bonded' style='padding-right: 20px;'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['per_capita_general_obligations']) . "</div></td>";
      echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
      echo "</tr>";
      $count++;
    }
  }

  public static function  pledgeRevConNyc($node){
    $count = 1;
    foreach( $node->data as $row){

      $dollar_sign = ($count == 1)? self::DOLLAR_SIGN : '';
      $count++;
      echo "<tr><td class='number'><div class='tdCen'>" . $row['fiscal_year'] . "</div></td>";
      echo "<td class='number'>" . $dollar_sign . "<div class='tdCen'>" . (($row['pit_revenue']<>0)?FormattingUtilities::trendsNumberDisplay($row['pit_revenue']):'-') . "</div></td>";
      echo "<td class='number'>" . $dollar_sign . "<div class='tdCen'>" . (($row['sales_tax_revenue']<>0)?FormattingUtilities::trendsNumberDisplay($row['sales_tax_revenue']):'-') . "</div></td>";
      echo "<td class='number'>" . $dollar_sign . "<div class='tdCen'>" . (($row['total_receipt']<>0)?FormattingUtilities::trendsNumberDisplay($row['total_receipt']):'-') . "</div></td>";
      echo "<td class='number'>" . $dollar_sign . "<div class='tdCen'>" . (($row['other']<>0)?FormattingUtilities::trendsNumberDisplay($row['other']):'-') . "</div></td>";
      echo "<td class='number'>" . $dollar_sign . "<div class='tdCen'>" . (($row['investment_earnings']<>0)?FormattingUtilities::trendsNumberDisplay($row['investment_earnings']):'-') . "</div></td>";
      echo "<td class='number'>" . $dollar_sign . "<div class='tdCen'>" . (($row['total_revenue']<>0)?FormattingUtilities::trendsNumberDisplay($row['total_revenue']):'-') . "</div></td>";
      echo "<td class='number'>" . $dollar_sign . "<div class='tdCen'>" . (($row['interest']<>0)?FormattingUtilities::trendsNumberDisplay($row['interest']):'-') . "</div></td>";
      echo "<td class='number'>" . $dollar_sign . "<div class='tdCen'>" . (($row['pricipal']<>0)?FormattingUtilities::trendsNumberDisplay($row['pricipal']):'-') . "</div></td>";
      echo "<td class='number'>" . $dollar_sign . "<div class='tdCen'>" . (($row['total']<>0)?FormattingUtilities::trendsNumberDisplay($row['total']):'-') . "</div></td>";
      echo "<td class='number'>" . $dollar_sign . "<div class='tdCen'>" . (($row['operating_expenses']<>0)?FormattingUtilities::trendsNumberDisplay($row['operating_expenses']):'-') . "</div></td>";
      echo "<td class='number'>" . $dollar_sign . "<div class='tdCen'>" . (($row['total_to_be_covered']<>0)?FormattingUtilities::trendsNumberDisplay($row['total_to_be_covered']):'-') . "</div></td>";
      echo "<td>&nbsp;</td>";
      echo "</tr>";
    }
  }
  public static function  legalDebtMargin($node){
    $table_rows = array();
    $years = array();
    foreach( $node->data as $row){
      $length =  $row['indentation_level'];
      $spaceString = '&nbsp;';
      while($length > 0){
        $spaceString .= '&nbsp;';
        $length -=1;
      }
      $table_rows[$row['display_order']]['category'] =  $row['category'];
      $table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
      $table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
      $table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
      $table_rows[$row['display_order']][$row['fiscal_year']]['amount'] = $row['amount'];
      $years[$row['fiscal_year']] = 	$row['fiscal_year'];
    }
    rsort($years);

    // Display Header
    foreach ($years as $year){
      echo "<th class='dt'><div>&nbsp;</div></th>";
      echo "<th class='number'><div>" . $year . "</div></th>";
    }
    echo "<th>&nbsp;</th></tr></thead><tbody>";
    // Display Main Content
    $count = 1;
    foreach($table_rows as $row){
      $dollar_sign = ($count == 1 || strtolower($row['category']) == 'legal debt margin') ? self::DOLLAR_SIGN : '';

      $cat_class = "";
      if($row['highlight_yn'] == 'Y') {
        $cat_class = "highlight ";
      }

      $cat_class .= "level" . $row['indentation_level'];
      $amount_class = "";
      if($row['category'] == 'Anticipated TSASC debt incurring power') {
        $row['category'] = 'Anticipated TSASC debt incurring<br>power';
      }
      if($row['category'] == 'Total net debt applicable to the limit as a percentage of debt limit') {
        $row['category'] = 'Total net debt applicable to the limit<br>as a percentage of debt limit';
      }
      if($row['amount_display_type']) {
        $amount_class = " amount-" . $row['amount_display_type'];
      }

      $amount_class .= " number ";
      $row['category'] = str_replace('(1)','<sup>(1)</sup>', $row['category']);
      $row['category'] = str_replace('(2)','<sup>(2)</sup>', $row['category']);
      $row['category'] = str_replace('(3)','<sup>(3)</sup>', $row['category']);

      $row['category'] = (isset($row['category'])?$row['category']:'&nbsp;');

      $conditionCategory = $row['category'];
      switch($conditionCategory){
        case "Debt limit (10% of assessed value)":
          $conditionCategory = "<div class='" . $cat_class . "'>Debt limit (10% of<br><span style='padding-left:0px;'>assessed value)<span></div>";
          break;
        case "Service fund and appropriations for redemption of non-excluded debt":
          $conditionCategory = "<div class='" . $cat_class ."'>Service fund and<br><span style='padding-left:0px;'>appropriations for</span><br><span style='padding-left:0px;'>redemption of</span><br><span style='padding-left:0px;'>non-excluded debt</span></div>";
          break;
        case "Anticipated TSASC debt incurring<br>power":
          $conditionCategory = "<div class='" .$cat_class . "'>Anticipated TSASC debt<br><span style='padding-left:0px;'>incurring power<span></div>";
          break;
        case "Contract, land acquisition and other liabilities":
          $conditionCategory = "<div class='" .$cat_class . "'>Contract, land acquisition<br><span style='padding-left:0px;'>and other liabilities</span></div>";
          break;
        case "Total net debt applicable to limit":
          $conditionCategory = "<div class='" .$cat_class . "'>Total net debt applicable<br>to limit</div>";
          break;
        case "Total net debt applicable to the limit<br>as a percentage of debt limit":
          $conditionCategory = "<div class='" .$cat_class . "'>Total net debt applicable to<br><span style='padding-left:0px;'>the limit as a percentage</span><br><span style='padding-left:0px;'>of debt limit<span></div>";
          break;
        case "Anticipated TFA financing<sup>(3)</sup>":
          $conditionCategory = "<div class='" .$cat_class . "'>Anticipated TFA financing</div>";
          break;
        default:
          $conditionCategory = "<div class='" . $cat_class . "' >" . $row['category'] . "</div>";
          break;
      }

      echo "<tr><td class='text'>" . $conditionCategory . "</td>";

      foreach ($years as $year){
        if($count == count($table_rows)){
          $amount = isset($row[$year]['amount']) ? $row[$year]['amount'] : '&nbsp;';
          echo "<td><div>&nbsp;</div></td>"
            ."<td class='" . $amount_class . "' ><div>" . $amount . "&nbsp;&nbsp;%</div></td>";
        }
        else{
          if($row[$year]['amount'] > 0){
            $amount = isset($row[$year]['amount']) ? number_format($row[$year]['amount']) : '&nbsp;';
            echo "<td><div>&nbsp;</div></td>"
              ."<td class='" . $amount_class . "' >" .$dollar_sign. "<div>" . $amount . "</div></td>";
          }else if($row[$year]['amount'] < 0){
            $amount = isset($row[$year]['amount']) ? number_format(abs($row[$year]['amount'])) : '&nbsp;';
            echo "<td><div></div></td>"
              ."<td class='" . $amount_class . "' >" .$dollar_sign. "<div>(" . $amount . ")</div></td>";
          }else if($row[$year]['amount'] == 0){
            if(strpos($row['category'], ':')) {
              echo "<td><div>&nbsp;</div></td>"."<td class='" . $amount_class . "' ><div>" . '&nbsp;' . "</div></td>";
            }
            else {
              echo "<td><div>&nbsp;</div></td>"."<td class='" . $amount_class . "' ><div>" . '-' . "</div></td>";
            }
          }
        }
      }
      echo "<td>&nbsp;</td>";
      echo "</tr>";
      $count++;
    }
  }

  public static function assesedValEstdAct($node){
    $count = 1;
    foreach ($node->data as $row) {
      $dollar_sign = ($count == 1) ? self::DOLLAR_SIGN : '';
      $percent_sign = ($count == 1) ? '<span class="endItem">%</span>' : '<span class="endItem" style="visibility:hidden;">%</span>';

      echo "<tr><td class='number'><div class='tdCen'>" . $row['fiscal_year'] . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['class_one'],1,'.',',') . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['class_two'],1,'.',',') . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['class_three'],1,'.',',') . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['class_four'],1,'.',',') . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['less_tax_exempt_property'],1,'.',',') . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['total_taxable_assesed_value'],1,'.',',') . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['total_direct_tax_1'],2) . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['estimated_actual_taxable_value'],1,'.',',') . "</div></td>";
      echo "<td class='number'><div class='tdCen'>" . number_format($row['assesed_value_percentage'],2) .$percent_sign. "</div></td>";
      echo "<td>&nbsp;</td>";
      echo "</tr>";

      $count++;
    }
  }

  public static function propertyTaxRates($node){
    $count = 1;
    foreach( $node->data as $row){
      $dollar_sign = ($count == 1) ? self::DOLLAR_SIGN : '';
      echo "<tr><td class='number'><div class='tdCen'>" . $row['fiscal_year'] . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['basic_rate'],2) . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  number_format($row['obligation_debt'],2) . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  number_format($row['total_direct'],2) . "</div></td>";
      echo "</tr>";
      $count++;
    }
  }

  public static function propertyTaxLevies($node){
    $count = 1;
    foreach( $node->data as $row){
      $dollar_sign = ($count == 1) ? self::DOLLAR_SIGN :'';
      $percent_sign = ($count == 1) ? '<span class="endItem">%</span>' : '<span class="endItem" style="visibility:hidden;">%</span>';

      echo "<tr><td class='number'><div class='tdCen'>" . $row['fiscal_year'] . "</div></td>";
      echo "<td class='number '>" .$dollar_sign. "<div class='tdCen'>" .  number_format($row['tax_levied']) . "</div></td>";
      echo "<td class='number '>" .$dollar_sign. "<div class='tdCen'>" .  number_format($row['amount']) . "</div></td>";
      echo "<td class='number '><div class='tdCen'>" . number_format($row['percentage_levy'],2) .$percent_sign .  "</div></td>";
      echo "<td class='number '>" .$dollar_sign. "<div class='tdCen'>" .  (($row['collected_subsequent_years'] > 0) ? number_format($row['collected_subsequent_years']) :'-') . "</div></td>";
      echo "<td class='number '>" .$dollar_sign. "<div class='tdCen'>" .  number_format($row['levy_non_cash_adjustments']) . "</div></td>";
      echo "<td class='number '>" .$dollar_sign. "<div class='tdCen'>" .  number_format( $row['collected_amount']) . "</div></td>";
      echo "<td class='number '><div class='tdCen'>" .  number_format($row['collected_percentage_levy'],2) .$percent_sign. "</div></td>";
      echo "<td class='number '>" .$dollar_sign. "<div class='tdCen'>" .  number_format($row['uncollected_amount']) . "</div></td>";
      echo "<td>&nbsp;</td>";
      echo "</tr>";

      $count++;
    }
  }

  public static function assesedValTaxRateClassTop($node){
    $table_rows = array();
    $years = array();

    foreach( $node->data as $row){
      $length =  $row['indentation_level'];
      $spaceString = '&nbsp;';
      while($length > 0){
        $spaceString .= '&nbsp;';
        $length -=1;
      }
      $table_rows[$row['display_order']]['category'] =  $row['category'];
      $table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
      $table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
      $table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
      $table_rows[$row['display_order']][$row['fiscal_year']]['assesed_value_million_amount'] = $row['assesed_value_million_amount'];
      $table_rows[$row['display_order']][$row['fiscal_year']]['percentage_taxable_real_estate'] = $row['percentage_taxable_real_estate'];
      $table_rows[$row['display_order']][$row['fiscal_year']]['direct_tax_rate'] = $row['direct_tax_rate'];
      $years[$row['fiscal_year']] = 	$row['fiscal_year'];
    }
    rsort($years);
    return [
      'table_rows' => $table_rows,
      'years' => $years,
    ];
  }

  public static function assesedValTaxRateClass($table_rows, $years){
    $count = 1;
    foreach( $table_rows as $row){
      $dollar_sign = ($count == 2 || $count == count($table_rows)) ? self::DOLLAR_SIGN : '';
      $percent_sign_1 = ($count == 2 || $count == count($table_rows))?'<span class="endItem">%</span>' : '<span class="endItem" style="visibility:hidden;">%</span>';
      $percent_sign_2 = ($count == count($table_rows))?'<span class="endItem">%</span>' : '<span class="endItem" style="visibility:hidden;">%</span>';

      $cat_class = "";
      if( $row['highlight_yn'] == 'Y') {
        $cat_class = "highlight ";
      }
      $cat_class .= "level" . $row['indentation_level'];
      $amount_class = "";
      if($row['amount_display_type']) {
        $amount_class = " amount-" . $row['amount_display_type'];
      }

      $sup_script = ($row['amount_display_type'] == 'G') ? "<sup class='endItem'>(1)</sup>" : "<sup class='endItem' style='visibility: hidden;'>(1)</sup>";

      $amount_class .= ' number';
      echo "<tr>
			    <td class='text " . $cat_class . "' ><div>" . (isset($row['category'])?$row['category']:'&nbsp;') . "</div></td>";
      foreach ($years as $year){
        if(isset($row[$year]['assesed_value_million_amount'])){
          if($row[$year]['assesed_value_million_amount'] == -1) {
            $row[$year]['assesed_value_million_amount'] = ' - ';
          }
          else {
            $row[$year]['assesed_value_million_amount'] = number_format($row[$year]['assesed_value_million_amount'], 1, '.',',');
          }
        }else{
          $row[$year]['assesed_value_million_amount'] = '&nbsp;';
        }

        if(isset($row[$year]['percentage_taxable_real_estate'])){
          if($row[$year]['percentage_taxable_real_estate'] == -1) {
            $row[$year]['percentage_taxable_real_estate'] = ' - ';
          }
          else {
            $row[$year]['percentage_taxable_real_estate'] = $row[$year]['percentage_taxable_real_estate'];
          }
        }else{
          $row[$year]['percentage_taxable_real_estate'] = '&nbsp;';
        }

        if(isset($row[$year]['direct_tax_rate'])){
          if($row[$year]['direct_tax_rate'] == -1) {
            $row[$year]['direct_tax_rate'] = ' - ';
          }
          else {
            $row[$year]['direct_tax_rate'] = number_format($row[$year]['direct_tax_rate'],2);
          }
        }else{
          $row[$year]['direct_tax_rate'] = '&nbsp;';
        }

        $sup_script2 = $sup_script;

        echo "<td>$dollar_sign</td>"."<td class='" . $amount_class . " ' ><div class='tdCen assess'>" . $row[$year]['assesed_value_million_amount'] . "</div></td>";
        echo "<td><div>&nbsp;</div></td>"."<td class='" . $amount_class . " ' ><div class='tdCen percent'>". $row[$year]['percentage_taxable_real_estate'] .$percent_sign_1."</div></td>";
        echo "<td><div>&nbsp;</div></td>"."<td class='number $amount_class' ><div class='tdCen direct'>" . $row[$year]['direct_tax_rate'] . $sup_script2 ."</div></td>";
      }
      echo "<td>&nbsp;</td>";
      echo "</tr>";

      $count++;
    }
  }

  public static function collectionsCancellationsAbatements($node){
    $count = 1;
    foreach( $node->data as $row){
      $dollar_sign = ($count == 1) ? self::DOLLAR_SIGN :'';
      $percent_sign = ($count == 1) ? '<span class="endItem">%</span>' : '<span class="endItem" style="visibility:hidden;">%</span>';

      echo "<tr><td class='number'><div class='tdCen'>" . $row['fiscal_year'] . "</div></td>";
      echo "<td class='number '>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['tax_levy'],1,'.',',') . (($row['fiscal_year']=='2003')?"<sup class='endItem'>(2)</sup>":"<sup class='endItem' style='visibility: hidden;'>(1)</sup>") . "</div></td>";
      echo "<td class='number'><div class='tdCen'>" .  number_format($row['collection'],1) . $percent_sign . "</div></td>";
      echo "<td class='number'><div class='tdCen'>" .  number_format($row['cancellations'],1) . $percent_sign ."</div></td>";
      echo "<td class='number'><div class='tdCen'>" .  number_format($row['abatement_and_discounts_1'],1) . $percent_sign ."</div></td>";
      echo "<td class='number'><div class='tdCen'>" .  number_format($row['uncollected_balance_percent'],1) . $percent_sign ."</div></td>";
      echo "</tr>";

      $count++;
    }
  }

  public static function uncollectedParkingViolationFeeTop($node){
    $table_rows = array();
    $years = array();
    foreach( $node->data as $row){
      $length =  $row['indentation_level'];
      $spaceString = '&nbsp;';
      while($length > 0){
        $spaceString .= '&nbsp;';
        $length -=1;
      }
      $table_rows[$row['display_order']]['category'] =  $row['category'];
      $table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
      $table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
      $table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
      $table_rows[$row['display_order']][$row['fiscal_year']]['amount'] = $row['amount'];
      $years[$row['fiscal_year']] = 	$row['fiscal_year'];
    }
    rsort($years);
    return [
      'table_rows' => $table_rows,
      'years' => $years,
    ];
  }

  public static function uncollectedParkingViolationFee($table_rows, $years){
    $count = 1;
    foreach( $table_rows as $row){
      $dollar_sign = ($count == 1 || $count ==  count($table_rows)) ? self::DOLLAR_SIGN : '';
      $cat_class = "";
      if( $row['highlight_yn'] == 'Y') {
        $cat_class = "highlight ";
      }
      $cat_class .= "level" . $row['indentation_level'];
      $amount_class = " number";
      if($row['amount_display_type']) {
        $amount_class .= " amount-" . $row['amount_display_type'];
      }
      if($row['category'] == 'Write offs, Adjustments and Dispositions (b)') {
        $row['category'] = 'Write offs, Adjustments<br>and Dispositions (b)';
      }
      if($row['category'] == 'Allowance for Uncollectible Amounts (c)') {
        $row['category'] = 'Allowance for<br>Uncollectible Amounts (c)';
      }
      $row['category'] = str_replace('(a)','<sup style="text-transform: lowercase;">(a)</sup>', $row['category']);
      $row['category'] = str_replace('(b)','<sup style="text-transform: lowercase;">(b)</sup>', $row['category']);
      $row['category'] = str_replace('(c)','<sup style="text-transform: lowercase;">(c)</sup>', $row['category']);

      $conditionCategory = ($row['category']?$row['category']:'&nbsp;');
      switch($conditionCategory){
        case "Write offs, Adjustments<br>and Dispositions<sup>(b)</sup>":
          $conditionCategory = "<div class='" . $cat_class . "'>Write offs, Adjustments and <br><span style='padding-left:10px;'>Dispositions<sup>(b)</sup><span></div>";
          break;
        case "Less:":
          $conditionCategory = "<div class='" . $cat_class ."'><span style='padding-left:0px;'>Less:</span></div>";
          break;
        case "Summonses Uncollected - June 30th":
          $conditionCategory = "<div class='" .$cat_class . "'>Summonses Uncollected -<br><span style='padding-left:0px;'> June 30th<span></div>";
          break;
        default:
          $conditionCategory = "<div class='" . $cat_class . "' >" . $row['category'] . "</div>";
          break;
      }

      echo "<tr><td class='text'>" . $conditionCategory . "</td>";
      foreach ($years as $year){
        echo "<td><div>&nbsp;</div></td>";
        echo "<td class='" . $amount_class . "' >" . $dollar_sign . "<div>" . (($row[$year]['amount'] > 0) ? number_format($row[$year]['amount']) : '&nbsp;') . "</div></td>";
      }
      echo "<td>&nbsp;</td>";
      echo "</tr>";
      $count++;
    }
  }

  public static function hudsonYardsInfraCorp($node){
    $count = 1;
    foreach($node->data as $row){
      $dollar_sign = ($count == 1 ? self::DOLLAR_SIGN : '');
      echo "<tr>";
      echo "<td class='number'><div class='tdCen'>" . $row['fiscal_year'] . "</td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['dib_revenue_1']<>0)?FormattingUtilities::trendsNumberDisplay($row['dib_revenue_1']):'-') . "</td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['tep_revenue_2']<>0)?FormattingUtilities::trendsNumberDisplay($row['tep_revenue_2']):'-') . "</td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['isp_revenue_3']<>0)?FormattingUtilities::trendsNumberDisplay($row['isp_revenue_3']):'-') . "</td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['pilomrt_payment']<>0)?FormattingUtilities::trendsNumberDisplay($row['pilomrt_payment']):'-') . "</td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['pilot']<>0)?FormattingUtilities::trendsNumberDisplay($row['pilot']):'-') . "</td>";            //PILOT DATA
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['other_4']<>0)?FormattingUtilities::trendsNumberDisplay($row['other_4']):'-') . ((in_array($row['fiscal_year'], ['2019', '2020']))? "<sup class='endItem'>(9)</sup>":"<sup class='endItem' style='visibility: hidden;'>(9)</sup>") . "</td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['investment_earnings']<>0)?FormattingUtilities::trendsNumberDisplay($row['investment_earnings']):'-') . "</td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['total_revenue']<>0)?FormattingUtilities::trendsNumberDisplay($row['total_revenue']):'-') . "</td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['interest']<>0)?FormattingUtilities::trendsNumberDisplay($row['interest']):'-') . "</td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['principal']<>0)?FormattingUtilities::trendsNumberDisplay($row['principal']):'-') . "</td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['total']<>0)?FormattingUtilities::trendsNumberDisplay($row['total']):'-') . "</td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['operating_expenses']<>0)?FormattingUtilities::trendsNumberDisplay($row['operating_expenses']):'-') . ((  $row['fiscal_year'] == '2012')? "<sup class='endItem'>(9)</sup>":"<sup class='endItem' style='visibility: hidden;'>(9)</sup>"). "</td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['total_to_be_covered']<>0)?FormattingUtilities::trendsNumberDisplay($row['total_to_be_covered']):'-') . "</td>";
      echo "<td class='number ' ><div class='tdCen'>" . (($row['coverage_on_total_revenue_5'] > 0) ? $row['coverage_on_total_revenue_5'] : "(" . $row['coverage_on_total_revenue_5'] .")") . (in_array($row['fiscal_year'], ['2009','2010', '2011','2012'])? "<sup class='endItem'>(6)</sup>":"<sup class='endItem' style='visibility: hidden;'>(6)</sup>") . "</div></td>";
      echo "<td>&nbsp;</td>";
      echo "</tr>";
      $count++;
    }
  }

  public static function capAssetsStatsProgramTop($node){
    $table_rows = array();
    $years = array();
    foreach( $node->data as $row){
      $length =  $row['indentation_level'];
      $spaceString = '&nbsp;';
      while($length > 0){
        $spaceString .= '&nbsp;';
        $length -=1;
      }
      $table_rows[$row['display_order']]['category'] =  $row['category'];
      $table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
      $table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
      $table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
      $table_rows[$row['display_order']][$row['fiscal_year']]['amount'] = $row['amount'];
      $years[$row['fiscal_year']] = 	$row['fiscal_year'];
    }
    rsort($years);
    return [
      'table_rows' => $table_rows,
      'years' => $years,
    ];
  }

  public static function capAssetsStatsProgram($table_rows, $years){
    foreach( $table_rows as $row){
      $cat_class = "";
      if( $row['highlight_yn'] == 'Y') {
        $cat_class = "highlight ";
      }
      $cat_class .= "level" . $row['indentation_level'];
      $amount_class = "";
      $amount_class .= " number";

      for($i=1;$i < 30;$i++){
        $find = '('. $i . ')';
        $replace = '<sup>('.$i .')</sup>';
        $row['category'] = str_replace($find,$replace, $row['category']);
      }

      $row['category'] = (isset($row['category'])?$row['category']:'&nbsp;');

      $conditionCategory = $row['category'];
//                switch($conditionCategory){
//                	case "Correctional/Detention Centers<sup>(2)</sup><sup>(3)</sup>":
//                		$conditionCategory = "<div class='" . $cat_class . "'>Correctional/Detention<br><span style='padding-left:10px;'>Centers<sup>(2)</sup><sup>(3)</sup><span></div>";
//                		break;
//                	case "Intermediate/Junior High Schools<sup>(20)</sup>":
//                		$conditionCategory = "<div class='" . $cat_class ."'>Intermediate/Junior High<br><span style='padding-left:10px;'>Schools<sup>(20)</sup><span></div>";
//                		break;
//                	case "Vehicle Maintenance/Storage Facilities<sup>(13)</sup><sup>(22)</sup><sup>(26)</sup>":
//                		$conditionCategory = "<div class='" .$cat_class . "'>Vehicle Maintenance/Storage<br><span style='padding-left:10px;'>Facilities<sup>(13)</sup><sup>(22)</sup><sup>(26)</sup><span></div>";
//                		break;
//                	case "Parks, Recreation, and Cultural Activities:":
//                		$conditionCategory = "<div class='" .$cat_class . "'>Parks, Recreation, and<br>Cultural Activities:</div>";
//                		break;
//                	case "Vehicle Maintenance/Storage Facilities":
//                		$conditionCategory = "<div class='" .$cat_class . "'>Vehicle Maintenance/Storage<br><span style='padding-left:10px;'>Facilities<span></div>";
//                		break;
//                	default:
//                		$conditionCategory = "<div class='" . $cat_class . "' >" . $row['category'] . "</div>";
//                		break;
//                }
//
      $conditionCategory = "<div class='" . $cat_class . "' >" . $row['category'] . "</div>";
      echo "<tr><td class='text'>" . /*$row['category']*/ $conditionCategory . "</td>";
      if(strpos($row['category'], ':')){
        $hyphen = "";
      }else{
        $hyphen = "-";
      }
      foreach ($years as $year) {
        echo "<td><div>&nbsp;</div></td><td class='" . $amount_class . "'><div>" . (($row[$year]['amount'] > 0)?number_format($row[$year]['amount']):'&nbsp;' . $hyphen) . "</div></td>";
      }
      echo "<td>&nbsp;</td>";
      echo "</tr>";
    }
  }

  public static function noCityEmployeesTop($node){
    $table_rows = array();
    $years = array();
    foreach( $node->data as $row){
      $length =  $row['indentation_level'];
      $spaceString = '&nbsp;';
      while($length > 0){
        $spaceString .= '&nbsp;';
        $length -=1;
      }
      $table_rows[$row['display_order']]['category'] =  $row['category'];
      $table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
      $table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
      $table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
      $table_rows[$row['display_order']][$row['fiscal_year']]['amount'] = $row['amount'];
      $years[$row['fiscal_year']] = 	$row['fiscal_year'];
    }
    rsort($years);
    return [
      'table_rows' => $table_rows,
      'years' => $years,
    ];
  }

  public static function noCityEmployees($table_rows, $years){
    $i = 1;
    foreach($table_rows as $row){
      $cat_class = "";
      if( $row['highlight_yn'] == 'Y') {
        $cat_class = "highlight ";
      }
      $cat_class .= "level" . $row['indentation_level'];
      $amount_class = "";
      if( $row['amount_display_type'] != "" ) {
        $amount_class = "amount-" . $row['amount_display_type'];
      }
      $amount_class .= ' number';
      $row['category'] = str_replace('(a)','<sup style="text-transform: lowercase;">(a)</sup>', $row['category']);
      $row['category'] = (isset($row['category'])?$row['category']:'&nbsp;');

      if($row['category'] == "Percentage Increase (Decrease) from Prior Year"){
        $row['category']  = "Percentage Increase (Decrease)<br><span style='padding-left:0px;'>from Prior Year</span>";
      }

      echo "<tr>
			    <td class='text' ><div class='" . $cat_class . "' >" . $row['category'] . "</div></td>";
      foreach ($years as $year){
        if($i < count($table_rows)){
          echo "<td><div>&nbsp;</div></td><td class='" . $amount_class . "' ><div>" . (($row[$year]['amount']>0)?number_format($row[$year]['amount']):'&nbsp;') . "</div></td>";
        }
        else{
          if($row[$year]['amount'] < 0) {
            echo "<td><div>&nbsp;</div></td><td class='" . $amount_class . "' ><div>(" . abs($row[$year]['amount']) . "%)</div></td>";
          }
          else {
            echo "<td><div>&nbsp;</div></td><td class='" . $amount_class . "' ><div>" . number_format($row[$year]['amount'],1) . "%</div></td>";
          }
        }
      }
      echo "<td>&nbsp;</td>";
      echo "</tr>";
      $i++;
    }
  }

  public static function changesNetAssetsTop($node){
    $table_rows = array();
    $years = array();

    foreach ($node->data as $row) {
      $length = $row['indentation_level'];
      $spaceString = '&nbsp;';
      while ($length > 0) {
        $spaceString .= '&nbsp;';
        $length -= 1;
      }
      $table_rows[$row['display_order']]['category'] = $row['category'];
      $table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
      $table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
      $table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
      $table_rows[$row['display_order']][$row['fiscal_year']]['amount'] = $row['amount'];
      $years[$row['fiscal_year']] = $row['fiscal_year'];

    }
    rsort($years);
    return [
      'table_rows' => $table_rows,
      'years' => $years,
    ];
  }

  public static function changesNetAssets($table_rows, $years){
    $count = 0;
    foreach ($table_rows as $row) {
      $cat_class = "";
      $dollar_sign = "&nbsp;";
      $count++;
      if ($count == 3 || $count == count($table_rows)) {
        $dollar_sign = "<div class='dollarItem' >$</div>";
      }

      if ($row['highlight_yn'] == 'Y') {
        $cat_class = "highlight ";
      }
      $cat_class .= "level" . $row['indentation_level'];

      $amount_class = "number ";
      if ($row['amount_display_type']) {
        $amount_class .= " amount-" . $row['amount_display_type'];
      }

      $row['category'] = (isset($row['category']) ? $row['category'] : '&nbsp;');

      echo "<tr>
            <td class='text " . $cat_class . "' ><div>" . $row['category'] . "</div></td>";

      foreach ($years as $year) {
        echo "<td>&nbsp;</td>";
        if ($row[$year]['amount'] > 0) {
          echo "<td class='" . $amount_class . " ' >" . $dollar_sign . "<div>" . number_format($row[$year]['amount']) . "</div></td>";
        } else if ($row[$year]['amount'] < 0) {
          echo "<td class='" . $amount_class . " ' >" . $dollar_sign . "<div>(" . number_format(abs($row[$year]['amount'])) . ")</div></td>";
        } else if ($row[$year]['amount'] == 0) {
          if (strpos($row['category'], ':')) {
            echo "<td class='" . $amount_class . " ' ><div>" . '&nbsp;' . "</div></td>";
          }
          else {
            echo "<td class='" . $amount_class . " ' ><div>" . '-' . "</div></td>";
          }
        }
      }
      echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
      echo "</tr>";
    }
  }

  public static function fundBalGovtFundsTop($node){
    $table_rows = array();
    $years = array();
    foreach( $node->data as $row){
      $length =  $row['indentation_level'];
      $spaceString = '&nbsp;';
      while($length > 0){
        $spaceString .= '&nbsp;';
        $length -=1;
      }
      $table_rows[$row['display_order']]['category'] =  $row['category'];
      $table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
      $table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
      $table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
      $table_rows[$row['display_order']][$row['fiscal_year']]['amount'] = $row['amount'];
      $years[$row['fiscal_year']] = 	$row['fiscal_year'];
    }
    rsort($years);
    return [
      'table_rows' => $table_rows,
      'years' => $years,
    ];
  }

  public static function fundBalGovtFunds($table_rows, $years){
    $count = 0;
    foreach( $table_rows as $row){
      $cat_class = "";
      $dollar_sign = "&nbsp;";
      $count++;
      if($count == 1 || $count == count($table_rows)){
        $dollar_sign = "<div class='dollarItem' >$</div>";
      }

      if($row['highlight_yn'] == 'Y') {
        $cat_class = "highlight ";
      }
      $cat_class .= "level" . $row['indentation_level'];
      $amount_class = "number ";

      if( $row['amount_display_type']) {
        $amount_class .= " amount-" . $row['amount_display_type'];
      }

      $row['category'] = (isset($row['category'])?$row['category']:'&nbsp;');

      echo "<tr>
            <td class='text " . $cat_class . "' ><div>" . $row['category'] . "</div></td>";
      foreach ($years as $year){
        echo "<td><div>&nbsp;</div></td>";
        if($row[$year]['amount'] > 0){
          echo "<td class='" . $amount_class . " ' >" . $dollar_sign .  "<div>" . number_format($row[$year]['amount']) . "</div></td>";
        }else if($row[$year]['amount'] < 0){
          echo "<td class='" . $amount_class . " ' >" . $dollar_sign . "<div>(" .  number_format(abs($row[$year]['amount'])) . ")</div></td>";
        }else{
          if(strpos($row['category'], ':')) {
            echo "<td class='" . $amount_class . " ' ><div>" . '&nbsp;' . "</div></td>";
          }
          else {
            echo "<td class='" . $amount_class . " ' >" . $dollar_sign . "<div>" . '-' . "</div></td>";
          }
        }
      }
      echo "<td>&nbsp;</td>";
      echo "</tr>";
    }
  }

  public static function changesFundBalTop($node){
    $table_rows = array();
    $years = array();
    foreach( $node->data as $row){
      if(isset($row['category']) || isset($row['amount'])) {
        $length = $row['indentation_level'];
        $spaceString = '&nbsp;';
        while ($length > 0) {
          $spaceString .= '&nbsp;';
          $length -= 1;
        }
        $table_rows[$row['display_order']]['category'] = $row['category'];
        $table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
        $table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
        $table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
        $table_rows[$row['display_order']]['currency_symbol'] = $row['currency_symbol'];
        $table_rows[$row['display_order']][$row['fiscal_year']]['amount'] = $row['amount'];
        $years[$row['fiscal_year']] = $row['fiscal_year'];
      }
    }
    rsort($years);
    return [
      'table_rows' => $table_rows,
      'years' => $years,
    ];
  }

  public static function changesFundBal($table_rows, $years){
    $count = 0;
    foreach($table_rows as $row){
      $cat_class = "";
      $dollar_sign = "";
      $count++;

      $dollar_sign = ($row['currency_symbol'] == 'Y')?"<div class='dollarItem' >$</div>" : '';

      if($row['highlight_yn'] == 'Y') {
        $cat_class = "highlight ";
      }
      $cat_class .= "level" . $row['indentation_level'];
      $amount_class = "number";

      if($row['amount_display_type']){
        $amount_class .= " amount-" . $row['amount_display_type'];
        $cat_class .= " cat-" . $row["amount_display_type"];
      }

      echo "<tr>
            <td class='text " . $cat_class . "' ><div>" . $row['category'] . "</div></td>";

      foreach ($years as $year){
        echo "<td><div></div></td>";
        if($count == count($table_rows)){
          echo "<td class='" . $amount_class . " ' ><div>" . $row[$year]['amount'] . "%</div></td>";
        }else{
          if($row[$year]['amount'] > 0){
            echo "<td class='" . $amount_class . " ' >". $dollar_sign ."<div>". number_format($row[$year]['amount']) . "</div></td>";
          }else if($row[$year]['amount'] < 0){
            echo "<td class='" . $amount_class . " ' >". $dollar_sign ."<div>(" . number_format(abs($row[$year]['amount'])) . ")</div></td>";
          }else if($row[$year]['amount'] == 0){
            if(strpos($row['category'], ':') || strtolower($row['category']) == 'less capital outlays') {
              echo "<td class='" . $amount_class . " ' ><div>" . '&nbsp;' . "</div></td>";
            }
            else {
              echo "<td class='" . $amount_class . " ' ><div>" . '-' . "</div></td>";
            }
          }
        }
      }
      echo "<td>&nbsp;</td>";
      echo "</tr>";
    }
  }

  public static function generalFundRevenueOtherFinSourcesTop($node){
    $table_rows = array();
    $years = array();
    foreach( $node->data as $row){
      $length =  $row['indentation_level'];
      $spaceString = '&nbsp;';
      while($length > 0){
        $spaceString .= '&nbsp;';
        $length -=1;
      }
      $table_rows[$row['display_order']]['category'] =  $row['category'];
      $table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
      $table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
      $table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
      $table_rows[$row['display_order']][$row['fiscal_year']]['amount'] = $row['amount'];
      $years[$row['fiscal_year']] = 	$row['fiscal_year'];
    }
    rsort($years);
    return [
      'table_rows' => $table_rows,
      'years' => $years,
    ];
  }

  public static function generalFundRevenueOtherFinSources($table_rows, $years){
    $count = 0;
    foreach( $table_rows as $row){
      $cat_class = "";
      $dollar_sign = "";
      $count++;
      if($count == 2 || $count == count($table_rows)){
        $dollar_sign = "<div class='dollarItem' >$</div>";
      }

      if($row['highlight_yn'] == 'Y') {
        $cat_class = "highlight ";
      }
      $cat_class .= "level" . $row['indentation_level'];
      $amount_class = "number";

      if($row['amount_display_type']) {
        $amount_class .= " amount-" . $row['amount_display_type'];
      }

      $row['category'] = (isset($row['category'])?$row['category']:'&nbsp;');

      echo "<tr>
            <td class='text " . $cat_class . "' ><div>" . $row['category'] . "</div></td>";
      foreach ($years as $year){
        echo "<td><div></div></td>";
        if($row[$year]['amount'] > 0){
          echo "<td class='" . $amount_class . " ' >" . $dollar_sign . "<div>" . number_format($row[$year]['amount']) . "</div></td>";
        }else if($row[$year]['amount'] < 0){
          echo "<td class='" . $amount_class . " ' >" . $dollar_sign . "<div>(" . number_format(abs($row[$year]['amount'])) . ")</div></td>";
        }else{
          if(strpos($row['category'], ':')) {
            echo "<td class='" . $amount_class . " ' ><div>" . '&nbsp;' . "</div></td>";
          }
          else {
            echo "<td class='" . $amount_class . " ' ><div>" . '-' . "</div></td>";
          }
        }
      }
      echo "<td>&nbsp;</td>";
      echo "</tr>";
    }
  }

  public static function generalFundExpendOtherFinSourcesTop($node){
    $table_rows = array();
    $years = array();
    foreach( $node->data as $row){
      $length =  $row['indentation_level'];
      $spaceString = '&nbsp;';
      while($length > 0){
        $spaceString .= '&nbsp;';
        $length -=1;
      }
      $table_rows[$row['display_order']]['category'] =  $row['category'];
      $table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
      $table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
      $table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
      $table_rows[$row['display_order']][$row['fiscal_year']]['amount'] = $row['amount'];
      $years[$row['fiscal_year']] = 	$row['fiscal_year'];
    }
    rsort($years);
    return [
      'table_rows' => $table_rows,
      'years' => $years,
    ];
  }

  public static function generalFundExpendOtherFinSources($table_rows, $years){
    $count = 0;
    foreach( $table_rows as $row){
      $cat_class = "";
      $dollar_sign = "";
      $count++;
      if($count == 2 || $count == count($table_rows)){
        $dollar_sign = "<div class='dollarItem' >$</div>";
      }

      if($row['highlight_yn'] == 'Y') {
        $cat_class = "highlight ";
      }
      $cat_class .= "level" . $row['indentation_level'];
      $amount_class = "number";

      if($row['amount_display_type']) {
        $amount_class .= " amount-" . $row['amount_display_type'];
      }

      $row['category'] = (isset($row['category'])?$row['category']:'&nbsp;');

      echo "<tr>
            <td class='text " . $cat_class . "' ><div>" . $row['category'] . "</div></td>";
      foreach ($years as $year){
        echo "<td><div></div></td>";
        if($row[$year]['amount'] > 0){
          echo "<td class='" . $amount_class . " ' >" .$dollar_sign ."<div>" . number_format($row[$year]['amount']) . "</div></td>";
        }else if($row[$year]['amount'] < 0){
          echo "<td class='" . $amount_class . " ' >" .$dollar_sign ."<div>(" . number_format(abs($row[$year]['amount'])) . ")</div></td>";
        }else{
          if(strpos($row['category'], ':')) {
            echo "<td class='" . $amount_class . " ' ><div>" . '&nbsp;' . "</div></td>";
          }
          else {
            echo "<td class='" . $amount_class . " ' ><div>" . '-' . "</div></td>";
          }
        }
      }
      echo "<td>&nbsp;</td>";
      echo "</tr>";
    }
  }

  public static function capitalProjRevAgencyTop($node){
    $table_rows = array();
    $years = array();
    foreach( $node->data as $row){
      $length =  $row['indentation_level'];
      $spaceString = '&nbsp;';
      while($length > 0){
        $spaceString .= '&nbsp;';
        $length -=1;
      }
      $table_rows[$row['display_order']]['category'] =  $row['category'];
      $table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
      $table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
      $table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
      $table_rows[$row['display_order']][$row['fiscal_year']]['amount'] = $row['amount'];
      $years[$row['fiscal_year']] = 	$row['fiscal_year'];
    }
    rsort($years);
    return [
      'table_rows' => $table_rows,
      'years' => $years,
    ];
  }

  public static function capitalProjRevAgency($table_rows, $years){
    $count = 0;
    foreach($table_rows as $row){
      $cat_class = "";
      $dollar_sign = "";
      $count++;
      if($count == 2 || $count == count($table_rows)){
        $dollar_sign = "<div class='dollarItem' >$</div>";
      }

      if($row['highlight_yn'] == 'Y') {
        $cat_class = "highlight ";
      }
      $cat_class .= "level" . $row['indentation_level'];
      $amount_class = "number";

      if( $row['amount_display_type']) {
        $amount_class .= " amount-" . $row['amount_display_type'];
      }

      $row['category'] = (isset($row['category'])?$row['category']:'&nbsp;');

      $conditionCategory = ($row['category']?$row['category']:'&nbsp;');
      switch($conditionCategory){
        /*
        case "Department of Small Business Services":
          $conditionCategory = "<div class='" . $cat_class . "'>Department of Small<br><span style='padding-left:10px;'>Business Services<span></div>";
          break;
        case "Department of Citywide Administrative Services":
          $conditionCategory = "<div class='" . $cat_class ."'>Department of Citywide<br><span style='padding-left:10px;'>Administrative Services</span></div>";
          break;
        case "Department of Information Technology and Telecommunications":
          $conditionCategory = "<div class='" .$cat_class . "'>Department of Information<br><span style='padding-left:10px;'>Technology and<span><br><span style='padding-left:10px;'>Telecommunications</span></div>";
          break;
        case "Total General Government":
          $conditionCategory = "<div class='level5'>Total General<br><span style='padding-left:10px;'>Government<span></div>";
          break;
        case "Total Public Safety and Judicial":
          $conditionCategory = "<div class='level5'>Total Public Safety<br><span style='padding-left:10px;'>and Judicial<span></div>";
          break;
        case "Department of Environmental Protection":
          $conditionCategory = "<div class='" . $cat_class . "'>Department of Environmental<br><span style='padding-left:10px;'>Protection<span></div>";
          break;
        case "Total Environmental Protection":
          $conditionCategory = "<div class='level4'>Total Environmental<br><span style='padding-left:10px;'>Protection<span></div>";
          break;
        case "Department of Transportation":
          $conditionCategory = "<div class='" . $cat_class . "'>Department of<br><span style='padding-left:10px;'>Transportation<span></div>";
          break;
        case "Total Transportation Services":
          $conditionCategory = "<div class='level4'>Total Transportation<br><span style='padding-left:10px;'>Services<span></div>";
          break;
        case "Parks, Recreation, and Cultural Activities:":
          $conditionCategory = "<div class='" . $cat_class . "'>Parks, Recreation, and<br>Cultural Activities:</div>";
          break;
        case "Department of Parks and Recreation":
          $conditionCategory = "<div class='" . $cat_class . "'>Department of Parks<br><span style='padding-left:10px;'>and Recreation<span></div>";
          break;
        case "Department of Cultural Affairs":
          $conditionCategory = "<div class='" . $cat_class . "'>Department of Cultural<br><span style='padding-left:10px;'>Affairs<span></div>";
          break;
        case "Total Parks, Recreation, and Cultural Activities":
          $conditionCategory = "<div class='" . $cat_class . "'>Total Parks, Recreation,<br><span style='padding-left:10px;'>and Cultural Activities<span></div>";
          break;
        case "Department of Housing Preservation and Development":
          $conditionCategory = "<div class='" . $cat_class . "'>Department of Housing<br><span style='padding-left:10px;'>Preservation and</span><br><span style='padding-left:10px;'>Development<span></div>";
          break;
        */
        default:
          $conditionCategory = "<div class='" . $cat_class . "' >" . $row['category'] . "</div>";
          break;
      }

      echo "<tr>
            <td class='text' >" . $conditionCategory . "</td>";

      foreach ($years as $year){
        echo "<td><div>&nbsp;</div></td>";

        if($row[$year]['amount'] > 0){
          echo "<td class='" . $amount_class . " ' >".$dollar_sign. "<div>" . number_format($row[$year]['amount']) . "</div></td>";
        }else if($row[$year]['amount'] < 0){
          echo "<td class='" . $amount_class . " ' >".$dollar_sign. "<div>(" . number_format(abs($row[$year]['amount'])) . ")</div></td>";
        }else if($row[$year]['amount'] == 0){
          if(strpos($row['category'], ':')) {
            echo "<td class='" . $amount_class . " ' ><div>" . '&nbsp;' . "</div></td>";
          }
          else {
            echo "<td class='" . $amount_class . " ' ><div>" . '-' . "</div></td>";
          }
        }
      }
      echo "<td>&nbsp;</td>";
      echo "</tr>";
    }
  }

  public static function nycEduConstFund($node){
    $count = 1;
    foreach( $node->data as $row){
      $dollar_sign = ($count == 1) ? self::DOLLAR_SIGN : '';
      if ($count % 2){
        $trclass = ' class="odd"';
      } else {
        $trclass = ' class="even"';
      }
      echo "<tr$trclass>";
      echo "<td class='number'><div class='tdCen'>" . $row['fiscal_year'] . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['rental_revenue']) . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['interest_revenue']) . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . ($row['other_income']?number_format($row['other_income']):'-') . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['total_revenue']) . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['interest']) . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['pricipal']) . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['total']) . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['operating_expenses']) . "</div></td>";
      echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['total_to_be_covered']) . "</div></td>";
      echo "<td class='number'><div class='tdCen'>" .  number_format($row['coverage_ratio'],2) . "</div></td>";
      echo "</tr>";
      $count++;
    }
  }

  public static function nycPopulation($node){
    $count = 1;
    foreach($node->data as $row){
      $percent_sign = ($count == 1 ) ? '<span class="endItem">%</span>' : '<span class="endItem" style="visibility:hidden;">%</span>';
      $open_parentheses_col3 = "";
      $closed_parentheses_col3 = "";
      $open_parentheses_col5 = "";
      $closed_parentheses_col5 = "";

      if(isset($row['percentage_change_from_prior_period']) && $row['percentage_change_from_prior_period'] < 0){
        $open_parentheses_col3 = "(";
        $closed_parentheses_col3 = ")";
        $row['percentage_change_from_prior_period'] = $row['percentage_change_from_prior_period']*-1;
      }

      if(isset($row['percentage_change_prior_period']) && $row['percentage_change_prior_period'] < 0){
        $open_parentheses_col5 = "(";
        $closed_parentheses_col5 = ")";
        $row['percentage_change_prior_period'] = $row['percentage_change_prior_period']*-1;
      }

      echo "<tr>";
      echo "<td class='number '><div  class='tdCen'>" . $row['fiscal_year'] . "</div></td>";
      echo "<td class='number '><div  class='tdCen'>" . (($row['united_states']>0)?number_format($row['united_states']):' - ') . "</div></td>";
      echo "<td class='number '><div  class='tdCen'>" . $open_parentheses_col3. (($row['percentage_change_from_prior_period']>0)? (number_format($row['percentage_change_from_prior_period'],2)):' - ') . $closed_parentheses_col3 . $percent_sign . "</div></td>";
      echo "<td class='number '><div  class='tdCen'>" . (($row['city_of_new_york']>0)?number_format($row['city_of_new_york']):' - '). "</div></td>";
      echo "<td class='number '><div  class='tdCen'>" . $open_parentheses_col5 . (($row['percentage_change_prior_period']!=0)?(number_format($row['percentage_change_prior_period'],2)):' - ') . $closed_parentheses_col5 . $percent_sign . "</div></td>";
      echo "</tr>";
      $count++;
    }
  }

  public static function personalIncomeTaxRevenuesTop($node){
    $table_rows = array();
    $years = array();
    foreach( $node->data as $row){
      $length =  $row['indentation_level'];
      $spaceString = '&nbsp;';
      while($length > 0){
        $spaceString .= '&nbsp;';
        $length -=1;
      }
      $table_rows[$row['display_order']]['fips'] =  $row['fips'];
      $table_rows[$row['display_order']]['area'] =  $row['area'];
      $table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
      $table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
      $table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
      $table_rows[$row['display_order']][$row['fiscal_year']]['amount'] = $row['amount'];
      $years[$row['fiscal_year']] = 	$row['fiscal_year'];
    }
    sort($years);
    return [
      'table_rows' => $table_rows,
      'years' => $years,
    ];
  }

  public static function personalIncomeTaxRevenues($table_rows, $years){
    $dollar_div = "<div class='dollarItem'>$</div>";
    foreach( $table_rows as $row){
      $cat_class = "";
      if( $row['highlight_yn'] == 'Y') {
        $cat_class = "highlight ";
      }
      $cat_class .= "level" . $row['indentation_level'];
      $amount_class = "";
      if( $row['amount_display_type'] != "" ) {
        $amount_class = "amount-" . $row['amount_display_type'];
      }
      $amount_class .= " number";

      switch($row['category']){
        case "Personal income (thousands of dollars)":
          $row['category'] = "Personal income<br/>(thousands of dollars)";
          break;
        case "Per capita personal income (dollars) 2/":
          $row['category'] = "Per capita personal<br/>income (dollars) 2/";
          break;
        default:
          break;
      }

      echo "<tr><td class='number'><div class='tdCen'>" . (isset($row['fips'])?$row['fips'] :'&nbsp;') . "</div></td>";
      echo "<td class='text'><div>" . (isset($row['area'])?$row['area'] :'&nbsp;') . "</div></td>";

      foreach ($years as $year) {
        echo "<td class='" . $amount_class . "'>$dollar_div<div>" . (isset($row[$year]['amount']) ? number_format($row[$year]['amount']) : '&nbsp;') . "</div></td>";
      }
      echo "<td>&nbsp;</td>";
      echo "</tr>";
      $dollar_div = "";
    }
  }

  public static function nonAgrEmploymentTop($node){
    $table_rows = [];
    $years = [];

    foreach( $node->data as $row){
      $length =  $row['indentation_level'];
      $spaceString = '&nbsp;';
      while($length > 0){
        $spaceString .= '&nbsp;';
        $length -=1;
      }
      $table_rows[$row['display_order']]['category'] =  $row['category'];
      $table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
      $table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
      $table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
      $table_rows[$row['display_order']][$row['fiscal_year']]['amount'] = $row['amount'];
      $years[$row['fiscal_year']] = 	$row['fiscal_year'];
    }
    rsort($years);
    return [
      'table_rows' => $table_rows,
      'years' => $years,
    ];
  }

  public static function nonAgrEmployment($table_rows, $years){
    $i = 0;

    foreach($table_rows as $row){
      $cat_class = "";
      if($row['highlight_yn'] == 'Y') {
        $cat_class = "highlight ";
      }
      $cat_class .= "level" . $row['indentation_level'];
      $amount_class = "";
      if( $row['amount_display_type'] != "" ) {
        $amount_class = "amount-" . $row['amount_display_type'];
      }
      $amount_class .= " number ";
      $row['category'] = (isset($row['category'])?$row['category']:'&nbsp;');

      $conditionCategory = $row['category'];
      switch($conditionCategory){
        case "Transportation, Warehousing and Utilities":
          $conditionCategory = "<div class='" . $cat_class . "'>Transportation, Warehousing<br><span style='padding-left:10px;'>and Utilities</span></div>";
          break;
        case "Percentage Increase (Decrease) from Prior Year":
          $conditionCategory = "<div class='" . $cat_class ."'>Percentage Increase (Decrease)<br><span style='padding-left:10px;'>from Prior Year</span></div>";
          break;
        default:
          $conditionCategory = "<div class='" . $cat_class . "' >" . str_replace('(a)','<sup style="text-transform: lowercase">(a)</sup>',$row['category'])  . "</div>";
          break;
      }


      echo "<tr><td class='text'>" . $conditionCategory . "</td>";

      foreach ($years as $year){
        if($i == count($table_rows)-1){
          if($row[$year]['amount'] > 0) {
            echo "<td><div></div></td><td class='" . $amount_class . "'><div>" . $row[$year]['amount'] . "%</div></td>";
          }
          else if($row[$year]['amount'] < 0) {
            echo "<td><div></div></td><td class='" . $amount_class . "' ><div>(" . abs($row[$year]['amount']) . "%)" . (($year == 2020) ? '(b)' : '') . "</div></td>";
          }
          else {
            echo "<td><div></div></td><td class='" . $amount_class . "' ><div>" . "NA" . "</div></td>";
          }
        }else{
          if($row[$year]['amount'] > 0){
            echo "<td><div></div></td><td class='" . $amount_class . "' ><div>" . number_format($row[$year]['amount']) . "</div></td>";
          }else if($row[$year]['amount'] < 0){
            echo "<td><div></div></td><td class='" . $amount_class . "' ><div>(" . number_format(abs($row[$year]['amount'])) . ")</div></td>";
          }else if($row[$year]['amount'] == 0){
            if(strpos($row['category'], ':')) {
              echo "<td><div></div></td><td class='" . $amount_class . "' ><div>" . '&nbsp;' . "</div></td>";
            }
            else {
              echo "<td><div></div></td><td class='" . $amount_class . "' ><div>" . '-' . "</div></td>";
            }
          }
        }
      }
      $i++;
      echo "<td>&nbsp;</td>";
      echo "</tr>";
    }
  }

  public static function personsRecPubAsst($node){
    foreach( $node->data as $row){
      echo "<tr><td class='number'><div  class='tdCen'>" . $row['fiscal_year'] . "</div></td>";
      echo "<td class='number'><div  class='tdCen'>" . number_format($row['public_assistance']) . "</div></td>";
      echo "<td class='number'><div  class='tdCen'>" . (($row['ssi'])?number_format($row['ssi']) : 'NA') . "</div></td>";
      echo "</tr>";
    }
  }

  public static function empStatusResidentPopulation($node){
    $count = 1;
    foreach( $node->data as $row){
      $percent_sign = ($count == 1) ? '<span class="endItem">%</span>' : '<span class="endItem" style="visibility:hidden;">%</span>';
      echo "<tr><td class='number'><div  class='tdCen'>" . $row['fiscal_year'] . "</div></td>";
      echo "<td class='number'><div  class='tdCen'>" . number_format($row['civilian_labor_force_new_york_city_employed']) . "</div></td>";
      echo "<td class='number'><div  class='tdCen'>" . number_format($row['civilian_labor_force_unemployed']) . "</div></td>";
      echo "<td><div>&nbsp;</div></td>";
      echo "<td class='number'><div  class='tdCen'>" . number_format($row['unemployment_rate_city_percent'],1) . $percent_sign ."</div></td>";
      echo "<td class='number'><div class='tdCen'>" . number_format($row['unemployment_rate_united_states_percent'],1) . $percent_sign. "</div></td>";
      echo "</tr>";
      $count++;
    }
  }

  // CSV Generation
}
