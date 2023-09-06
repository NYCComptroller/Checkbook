<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\widget_config\Utilities\Trends;

use Drupal\checkbook_infrastructure_layer\Utilities\FormattingUtilities;

class DebtCapacityTrendsCSV  {

  public static function ratiosOutstandingDebtCsv($node){
    $header = '';
    $output ='';
    $header .= ",,,,".'"'.'(AMOUNTS IN MILLIONS)'.'"'.",,,,". "\n";
    $header .= 'Fiscal year';
    $header .=  ",General Obligation Bonds"  ;
    $header .=  ",Revenue Bonds"  ;
    $header .=  ",ECF"  ;
    $header .=  ",MAC Debt"  ;
    $header .=  ",TFA Bonds"  ;
    $header .=  ",TSASC Debt"  ;
    $header .=  ",STAR"  ;
    $header .=  ",FSC"  ;
    $header .=  ",SFC Debt"  ;
    $header .=  ",HYIC Bonds and Notes"  ;
    $header .=  ",Capital Leases Obligations"  ;
    $header .=  ",IDA Bonds"  ;
    $header .=  ",Treasury Obligations"  ;
    $header .=  ",Total Primary Government"  ;
    $output =  $header . "\n";

    $count = 1;
    foreach( $node->data as $row){
      $dollar_symbol = ($count ==1 )? '$':'';
      $count++;
      $rowString = $row['fiscal_year'] ;
      $rowString .= ','  . '"' . (($row['general_obligation_bonds']>0)?FormattingUtilities::trendsNumberDisplay($row['general_obligation_bonds']):'-') . '"';
      $rowString .= ','  . '"'. (($row['revenue_bonds']>0)?FormattingUtilities::trendsNumberDisplay($row['revenue_bonds']):'-') . '"';
      $rowString .= ','  . '"'. (($row['ecf']>0)?FormattingUtilities::trendsNumberDisplay($row['ecf']):'-') . '"';
      $rowString .= ','  . '"'. (($row['mac_debt']>0)?FormattingUtilities::trendsNumberDisplay($row['mac_debt']):'-') . '"';
      $rowString .= ','  . '"'. (($row['tfa']>0)?FormattingUtilities::trendsNumberDisplay($row['tfa']):'-') . '"';
      $rowString .= ','  . '"'. (($row['tsasc_debt']>0)?FormattingUtilities::trendsNumberDisplay($row['tsasc_debt']):'-') . '"';
      $rowString .= ','  . '"'. (($row['star']>0)?FormattingUtilities::trendsNumberDisplay($row['star']):'-') . '"';
      $rowString .= ','  . '"'. (($row['fsc']>0)?FormattingUtilities::trendsNumberDisplay($row['fsc']):'-') . '"';
      $rowString .= ','  . '"'. (($row['sfc_debt']>0)?FormattingUtilities::trendsNumberDisplay($row['sfc_debt']):'-') . '"';
      $rowString .= ','  . '"'. (($row['hyic_bonds_notes']>0)?FormattingUtilities::trendsNumberDisplay($row['hyic_bonds_notes']):'-') . '"';
      $rowString .= ','  . '"'. (($row['capital_leases_obligations']>0)?FormattingUtilities::trendsNumberDisplay($row['capital_leases_obligations']):'-') . '"';
      $rowString .= ','  . '"'. (($row['ida_bonds']>0)?FormattingUtilities::trendsNumberDisplay($row['ida_bonds']):'-') . '"';
      if($row['treasury_obligations'] < 0 ) {
        $rowString .= ','  . '"'. "(" . FormattingUtilities::trendsNumberDisplay(abs($row['treasury_obligations'])) . ")" . '"';
      }
      else if ($row['treasury_obligations'] == 0) {
        $rowString .= ',' .  "-";
      }
      else {
        $rowString .= ','  . '"'. FormattingUtilities::trendsNumberDisplay($row['treasury_obligations']) . '"';
      }

      $rowString .= ','  . '"'. (($row['total_primary_government']>0)?FormattingUtilities::trendsNumberDisplay($row['total_primary_government']):'-') . '"';
      $output .= $rowString . "\n";
    }

    $output .= "\n"."\n".'"'."Sources:  Annual Comprehensive Financial Reports of the Comptroller".'"';
    $output .= "\n"."\n".'"'."(1) Includes Direct Borrowings and Direct Placements. See Notes to Financial Statements (Note D.5), “Changes in Long Term Liabilities”".'"';
    $output .= "\n"."\n".'"'."Note: Gross Debt, Percentage of Personal Income and Per Capital Gross Debt columns had to be removed. The figures changed year by year and they would not match the figures shown when that years ACFR was released.".'"';
    return $output;
  }

 public static function ratiosGeneralBondeDebt($node)
 {
    $header = '';
    $output ='';
    $header = 'Fiscal Year';
    $header .=  ",General Bonded Debt (1)" ;
    $header .=  ",Debt Secure by Revenue other than property tax (2) (3)" ;
    $header .=  ",City Net General Obligation Bonded Debt" ;
    $header .=  ",City Net General Obligation Bonded Debt as a Percentage of  Assessed Taxable Value of Property (4)," ;
    $header .=  ",Per Capita (5)";
    $output .=  $header . "\n";

      $count = 1;
      foreach( $node->data as $row){
        $dollar_sign = ($count == 1) ? '$':'';
        $percent_sign = ($count == 1) ? '%':'';

        $rowString = $row['fiscal_year'] ;
        $rowString .= ',' . '"'. FormattingUtilities::trendsNumberDisplay($row['general_bonded_debt']) .'"';
        $rowString .= ',' . '"'. FormattingUtilities::trendsNumberDisplay($row['debt_by_revenue_ot_prop_tax']) .'"';
        $rowString .= ',' . '"'. FormattingUtilities::trendsNumberDisplay($row['general_obligation_bonds']) .'"';
        $rowString .= ',' . '"'. FormattingUtilities::trendsNumberDisplay($row['percentage_atcual_taxable_property'], 2).'"'.','.$percent_sign;
        $rowString .= ',' . '"'. FormattingUtilities::trendsNumberDisplay($row['per_capita_general_obligations']).'"';

        $output .=  $rowString . "\n";
        $count++;
      }
    $output .="(1) See Notes to financial Statements (Note D.5), \"Changes in Long Term Liabilities\"___Bonds and Notes Payable net of premium and discount.".'"';
    $output .="(2) Includes ECF,FSC,HYIC,IDA,STAR,TFA,NYCTLT, and TSASC.".'"';
    $output .="(3) See Exhibit \"Pledge -Revenue Coverage\", Part III __ Statistical Information, ACFR. ".'"';
    $output .="(4)See Exhibit \"Assessed Value and Estimated Actual Value of Taxable Property_{}Ten Year Trend\", Part III{}_ Statistical Information, ACFR.".'"';
    $output .="(5) See Exhibit \"Population___ Ten Year Trend\", Part III___ Statistical Information, ACFR\"".'"';
    $output .="SOURCES:  Annual Comprehensive Financial Reports of the Comptroller".'"';
      return $output;
   }
  public static function pledgedRevCovNyc($node)
  {
    $output ='';
    $output .= ",,,,,,New york City Transitional Finance Authority". "\n";
    $output .= ",,,,,,(AMOUNTS IN THOUSANDS)". "\n";

    $header = 'Fiscal year';
    $header .=  ",PIT Revenue(1)"  ;
    $header .=  ",Sales Tax Revenue(2)"  ;
    $header .=  ",Total Receipt"  ;
    $header .=  ",Other(3)"  ;
    $header .=  ",Investment Earnings(4)"  ;
    $header .=  ",Total Revenue"  ;
    $header .=  ",Future Tax Secured Bonds Debt Service - Interest"  ;
    $header .=  ",Future Tax Secured Bonds Debt Service - Principal"  ;
    $header .=  ",Future Tax Secured Bonds Debt Service - Total"  ;
    $header .=  ",Operating Expenses"  ;
    $header .=  ",Total to be Covered"  ;

    $output .= $header . "\n";

    $count = 1;
    foreach( $node->data as $row){
      $dollar_sign = ($count == 1)? '$':'';
      $count++;
      $rowString = $row['fiscal_year'] ;
      $rowString .=  ','.'"'. (($row['pit_revenue']<>0)?FormattingUtilities::trendsNumberDisplay($row['pit_revenue']):'-').'"';
      $rowString .= ','.'"'. (($row['sales_tax_revenue']<>0)?FormattingUtilities::trendsNumberDisplay($row['sales_tax_revenue']):'-').'"';
      $rowString .= ','.'"'. (($row['total_receipt']<>0)?FormattingUtilities::trendsNumberDisplay($row['total_receipt']):'-').'"';
      $rowString .= ','.'"'. (($row['other']<>0)?FormattingUtilities::trendsNumberDisplay($row['other']):'-').'"';
      $rowString .= ','.'"'. (($row['investment_earnings']<>0)?FormattingUtilities::trendsNumberDisplay($row['investment_earnings']):'-').'"';
      $rowString .= ','.'"' . (($row['total_revenue']<>0)?FormattingUtilities::trendsNumberDisplay($row['total_revenue']):'-').'"';
      $rowString .= ','.'"' . (($row['interest']<>0)?FormattingUtilities::trendsNumberDisplay($row['interest']):'-').'"';
      $rowString .= ','.'"' . (($row['pricipal']<>0)?FormattingUtilities::trendsNumberDisplay($row['pricipal']):'-').'"';
      $rowString .= ','.'"' . (($row['total']<>0)?FormattingUtilities::trendsNumberDisplay($row['total']):'-').'"';
      $rowString .= ','.'"' . (($row['operating_expenses']<>0)?FormattingUtilities::trendsNumberDisplay($row['operating_expenses']):'-').'"';
      $rowString .= ','.'"' . (($row['total_to_be_covered']<>0)?FormattingUtilities::trendsNumberDisplay($row['total_to_be_covered']):'-').'"';

      $output .= $rowString . "\n";
    }
    $output .= "\n"."\n".'"'."(1) Personal income tax (PIT).".'"'
      ."\n".'"'."(2) Sales tax revenue has not been required by the TFA. This amount is available to cover debt service if required.".'"'
      ."\n".'"'."(3) Grant from City and Federal Subsidy.".'"'
      ."\n".'"'."(4) Net of fair market value adjustment.".'"';
    return $output;
  }

  public static function legalDebtMargin($node)
  {
    $output ='';
      $table_rows = array();
      $years = array();
      foreach( $node->data as $row){
        $table_rows[$row['display_order']]['category'] = $row['category'];
        $table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
        $table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
        $table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
        $table_rows[$row['display_order']][$row['fiscal_year']]['amount'] = $row['amount'];
        $years[$row['fiscal_year']] = 	$row['fiscal_year'];
      }
      rsort($years);
      $header = ',,,,,,Fiscal Year,,,,,'."\n";
      foreach ($years as $year){
        $header .= "," . $year ;
      }
      $header .= "\n".',,,,,,(AMOUNTS IN THOUSANDS),,,,,'."\n";
      $output .= $header . "\n";

      $count = 1;
      foreach($table_rows as $row){
        $dollar_sign = ($count == 1 || strtolower($row['category']) == 'legal debt margin') ? '$':'';

        $rowString = '"'.$row['category'].'"';
        foreach ($years as $year){
          $amount = '';
          if($count == count($table_rows)){
            $amount = $row[$year]['amount'] . " %";
          }else{
            if($row[$year]['amount'] > 0){
              $amount = '"'. FormattingUtilities::trendsNumberDisplay($row[$year]['amount']) .'"';
            }else if($row[$year]['amount'] < 0){
              $amount = '"' . "(" . FormattingUtilities::trendsNumberDisplay(abs($row[$year]['amount'])) . ")" . '"';
            }else if($row[$year]['amount'] == 0){
              if(strpos($row['category'], ':')) {
                $amount = '';
              }
              else {
                $amount = '"-"';
              }
            }
          }

          $rowString .= ',' . $amount;
        }
        $output .= $rowString . "\n";
        $count++;
      }

    $output .="Notes:".'"'
    ."\n".'"'."(1)   The Legal Debt Margin and the Net Debt Applicable to the Debt Limit as a Percentage of the Debt Limit are recalculated on July 1, the first day of each City fiscal year,".'"'
    ."\n".'"'."based on the new assessed value in accordance with the new year’s enacted tax fixing resolution.".'"'
    ."\n".'"'."For fiscal year 2022, beginning July 1, 2021, the Legal Debt Margin and the Net Debt Applicable to the Debt Limit as a Percentage of the Debt Limit".'"'
    ."\n".'"'."are $47,697,902 and 62.55%, respectively. ".'"'
    ."\n".'"'."(2) A five-year average of full valuations of taxable real estate from the Resolutions of the Council Fixing the Property Tax Rates for the fiscal year beginning on July 1, 2020 and ending on June 30, 2021.".'"'
    ."\n".'"'."(3) The Constitution of the State of New York limits the general debt-incurring power of The City of New York to ten percent of the five-year average of full valuations of".'"'
    ."\n".'"'."taxable real estate.".'"'
    ."\n".'"'."(4) Includes adjustments for Business Improvement Districts, Original Issue Discount, and cash on hand for defeasance.".'"'
    ."\n".'"'."(5) To provide for the City's capital program, State legislation was enacted which created the Transitional Finance Authority (TFA).".'"'
    ."\n".'"'."TFA debt above 13.5 billion (Excludes TFA Building Aid Revenue bonds and Recovery Bonds) is subject to the general debt limit of the City.".'"'
    ."\n".'"'."(6) Obligations for water supply and certain obligations for rapid transit are excluded pursuant to the State Constitution and in accordance with provisions of the State Local Finance Law.".'"'
    ."\n".'"'."Resources of the General Debt Service Fund applicable to non-excluded debt and debt service appropriations for the redemption of such debt are deducted from the non-excluded funded debt".'"'
    ."\n".'"'."to arrive at the funded debt within the debt limit.".'"';
    return $output;
  }

}
