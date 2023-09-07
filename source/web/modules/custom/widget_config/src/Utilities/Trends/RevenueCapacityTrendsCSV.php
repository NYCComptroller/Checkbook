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

class RevenueCapacityTrendsCSV  {

  public static function assesedValAndEstdActValCsv($node){
    $header = '';
    $output ='';

    $last_year = end($node->data)['fiscal_year'];
    reset($node->data);

    $header = 'Fiscal year';

    $header .=  ",Class One" ;
    $header .=  ",Class Two" ;
    $header .=  ",Class Three";
    $header .=  ",Class Four";

    $header .=  ",Less Tax Exempt Property";
    $header .=  ",Total Taxable Assessed Value";
    $header .=  ",Total Direct Tax Rate(1)";
    $header .=  ",Estimated Actual Taxable Value";
    $header .=  ",Assessed Value as a Percentage of Actual Value,";
    $header .= "\n".",,,,,,,(AMOUNTS IN MILLIONS),,,,,";
    $output .= $header . "\n\n";

    $count = 1;
    foreach ($node->data as $row) {
      $dollar_sign = ($count == 1) ? '$' : '';
      $percent_sign = ($count == 1) ? '%' : '';

      $rowString = $row['fiscal_year'] ;
      $rowString .= ',' . '"' . FormattingUtilities::trendsNumberDisplay($row['class_one'],1,'.',',') . '"';
      $rowString .= ',' . '"' . FormattingUtilities::trendsNumberDisplay($row['class_two'],1,'.',',') . '"';
      $rowString .= ',' . '"' . FormattingUtilities::trendsNumberDisplay($row['class_three'],1,'.',',') . '"';
      $rowString .= ',' . '"' . FormattingUtilities::trendsNumberDisplay($row['class_four'],1,'.',',') . '"';
      $rowString .= ',' . '"' . FormattingUtilities::trendsNumberDisplay($row['less_tax_exempt_property'],1,'.',',') . '"';
      $rowString .= ',' . '"' . FormattingUtilities::trendsNumberDisplay($row['total_taxable_assesed_value'],1,'.',',') . '"';
      $rowString .= ',' . '"' . FormattingUtilities::trendsNumberDisplay($row['total_direct_tax_1'],2) . '"';
      $rowString .= ',' . '"' . FormattingUtilities::trendsNumberDisplay($row['estimated_actual_taxable_value'],1,'.',',') . '"';
      $rowString .= ',' . '"' . FormattingUtilities::trendsNumberDisplay($row['assesed_value_percentage'],2) .'"' .','.$percent_sign;

      $output .= $rowString . "\n";
      $count++;
    }

    $output .= "\n"."\n".'"'."(1) Property tax rate based on every $100 of assessed valuation".'"';
    $output .= "\n"."\n".'"'."Notes:".'"';
    $output .= "\n"."\n".'"'."The definitions of the four classes are as follows:".'"';
    $output .= "\n".'"'." Class One - One, two, and three family homes; single family homes on cooperatively owned land.  Condominiums with no more ".'"';
    $output .= "\n"."             than three dwelling units, provides such property was previously classified as Class One or no more than three stories in height ".'"';
    $output .= "\n"."             and built as condominiums. Mixed-use property with three units or less, provided 50 percent or more of the space is used for ".'"';
    $output .= "\n"."             residential purposes. Vacant land, primarily residentially zoned, except in Manhattan below 110th Street.".'"';
    $output .= "\n"."\n".'"'." Class Two - All other residential property not in Class One, except hotels and motels.  Mixed-use property with four or more units,".'"';
    $output .= "\n".'"'."             provided 50 percent or more of the space is used for residential  purposes.".'"';
    $output .= "\n"."\n".'"'." Class Three - Utility real property owned by utility corporations, except land and buildings.".'"';
    $output .= "\n"."\n".'"'." Class Four -  All other real property.".'"';
    $output .= "\n"."\n".'"'."Assesment Values are based on a percentage of the property's Fair Value.".'"';
    $output .= "\n"."The Department of Finance assigns fair values to all properties in New York City.".'"';
    $output .= "\n"."Fair Value is the worth of a property's tax class and the New York State Law requirements for determining fair value.".'"';
    $output .= "\n"."\n".'"'."Sources: Resolutions of the City Council and The Annual Report of The New York City Property Tax Fiscal Year ".$last_year.".".'"';
    return $output;
  }

  public static function propertyTaxRatesCsv($node) {
    $header = '';
    $output = '';

    $header = 'Fiscal year';
    $header .=  ",Basic Rate (1)" ;
    $header .=  ",Obligation Debt" ;
    $header .=  ",Total Direct";

    $output .= $header . "\n";
    $count = 1;

    foreach( $node->data as $row){
      $dollar_sign = ($count == 1)?"$":"";
      $rowString = $row['fiscal_year'] ;
      $rowString .= ',' . $row['basic_rate'];
      $rowString .= ',' . $row['obligation_debt'];
      $rowString .= ',' . $row['total_direct'];

      $output .= $rowString . "\n";
      $count++;
    }

    $output .= "\n"."\n".'"'."SOURCE: Resolutions of the City Council.".'"';
    $output .= "\n"."\n".'"'."Note: (1) Property tax rate based on every $100 of assessed valuations.".'"';
    return $output;
  }

  public static function propertyTaxLeviesCsv($node) {
    $header = '';
    $output = '';

    $last_year = end($node->data)['fiscal_year'];
    reset($node->data);

    $header = 'Fiscal year';
    $header .=  ",Taxes Levied for the Fiscal Year"  ;
    $header .=  ",Collected Within the Fiscal Year of the Levy - Amount"  ;
    $header .=  ",Collected Within the Fiscal Year of the Levy - Percentage of Levy,"  ;
    $header .=  ",Collected Within the Fiscal Year of the Levy - Collected in Subsequent Years"  ;
    $header .=  ",Non-Cash Liquidations and Adjustments to Levy(1)"  ;
    $header .=  ",Total Collections and Adjustments to Date - Amount"  ;
    $header .=  ",Total Collections and Adjustments to Date - Percentage of Levy,"  ;
    $header .=  ",".'"'."Remaining Uncollected JUNE 30, {$last_year}".'"'  ;
    $output .= $header . "\n";

    $count = 1;
    foreach( $node->data as $row){
      $dollar_sign = ($count == 1)?"$":"";
      $percent_sign = ($count == 1)?"%":"";

      $rowString = $row['fiscal_year'] ;
      $rowString .= ',' . '"'. FormattingUtilities::trendsNumberDisplay($row['tax_levied']).'"';
      $rowString .= ',' . '"'.FormattingUtilities::trendsNumberDisplay($row['amount']).'"';
      $rowString .= ',' . '"'.FormattingUtilities::trendsNumberDisplay($row['percentage_levy'],2).'"'. ',' . $percent_sign;
      $rowString .= ',' . '"'.(($row['collected_subsequent_years']>0)?FormattingUtilities::trendsNumberDisplay($row['collected_subsequent_years']):'-').'"';
      $rowString .= ',' .'"'. FormattingUtilities::trendsNumberDisplay($row['levy_non_cash_adjustments']).'"';
      $rowString .= ',' . '"'.FormattingUtilities::trendsNumberDisplay($row['collected_amount']).'"';
      $rowString .= ',' . '"'.FormattingUtilities::trendsNumberDisplay($row['collected_percentage_levy'],2).'"'. ',' . $percent_sign;
      $rowString .= ',' . '"'.FormattingUtilities::trendsNumberDisplay($row['uncollected_amount']).'"';

      $output .= $rowString . "\n";
      $count++;
    }

    $output .= "\n"."\n".'"'."(1) Adjustments to Tax Levy are Non-Cash Liquidations and Cancellations of Real Property Tax and include School Tax Relief payments which are not included in the City Council Resolutions.".'"';
    $output .= "\n"."\n".'"'."SOURCES: Resolutions of the City Council and other Department of Finance reports.".'"';
    return $output;
  }

  public static function assessedValAndTaxRateByClassCsv($node) {
    $header = '';
    $output = '';

    $table_rows = array();
    $years = array();

    foreach( $node->data as $row){

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
    $last_year = $years[0];

    $header = 'Type of Property';
    foreach ($years as $year){
      if($year == 2014){
        $header = $header .  ",Fiscal Year" . $year . " (3) - Assessed Value (in millions)";
        $header = $header .  ",Fiscal Year" . $year . " (3) - Percentage of Taxable Real Estate,";
        $header = $header .  ",Fiscal Year" . $year . " (3) - Direct Tax Rate (2)";
      }
      else{
        $header = $header .  ",Fiscal Year" . $year . " - Assessed Value (in millions)";
        $header = $header .  ",Fiscal Year" . $year . " - Percentage of Taxable Real Estate,";
        $header = $header .  ",Fiscal Year" . $year . " - Direct Tax Rate (2)";
      }
    }
    $output .= $header . "\n";

    $count = 1;
    foreach( $table_rows as $row){
      $dollar_sign = ($count == 2 || $count == count($table_rows))?"$":"";
      $percent_sign_1 = ($count == 2 || $count == count($table_rows))?"%":"";
      $percent_sign_2 = ($count == count($table_rows))?"%":"";
      $sup_script = ($row['amount_display_type'] == 'G') ? '(1)' : "";

      $rowString = '"'.$row['category'].'"' ;
      foreach ($years as $year){
        if(isset($row[$year]['assesed_value_million_amount'])){
          if($row[$year]['assesed_value_million_amount'] == -1)
            $row[$year]['assesed_value_million_amount'] = ' - ';
          else
            $row[$year]['assesed_value_million_amount'] = FormattingUtilities::trendsNumberDisplay($row[$year]['assesed_value_million_amount'], 1, '.',',');
        }else{
          $row[$year]['assesed_value_million_amount'] = '';
        }

        if(isset($row[$year]['percentage_taxable_real_estate'])){
          if($row[$year]['percentage_taxable_real_estate'] == -1)
            $row[$year]['percentage_taxable_real_estate'] = ' - ';
          else
            $row[$year]['percentage_taxable_real_estate'] = $row[$year]['percentage_taxable_real_estate'];
        }else{
          $row[$year]['percentage_taxable_real_estate'] = '';
        }

        if(isset($row[$year]['direct_tax_rate'])){
          if($row[$year]['direct_tax_rate'] == -1)
            $row[$year]['direct_tax_rate'] = ' - ';
          else
            $row[$year]['direct_tax_rate'] = $row[$year]['direct_tax_rate'];
        }else{
          $row[$year]['direct_tax_rate'] = '';
        }

        $rowString .=  ',' .'"' . $row[$year]['assesed_value_million_amount'].'"';
        $rowString .= ',' .$row[$year]['percentage_taxable_real_estate'].','.$percent_sign_1;
        //$rowString .= ',' .$row[$year]['direct_tax_rate'].$sup_script.''." ";
        $rowString .= ',' .$row[$year]['direct_tax_rate'].$sup_script.''." ";

      }
      $output .= $rowString . "\n";
      $count++;
    }

    $output .= "\n"."\n".'"'."(1) Represents the weighted average of the four classes of real property.".'"';
    $output .= "\n"."\n".'"'."(2) Property tax rate based on every $100 assessed valuation.".'"';
    $output .= "\n"."\n".'"'."(3) In fiscal year 2014 The Annual Report, the New York City Property Tax Fiscal Year 2014, reported various ".'"';
    $output .= "\n".'"'."    classifications of condos as class four real property for the first time.".'"';
    $output .= "\n"."\n".'"'."Note: Property in New York City is reassessed once a year. The City assesses property at approximately 40 percent of ".'"';
    $output .= "\n".'"'."      Market Value for commercial and industrial property and 20 percent of Market Value for residential property.".'"';
    $output .= "\n"."\n".'"'."Sources: Resolutions of the City Council and The Annual Report, The New York City Property Tax Fiscal Year ".$last_year.".".'"';
    return $output;
  }

  public static function collectionsCancellationsAbatementsCsv($node) {
    $header = '';
    $output = '';

    $last_year = end($node->data)['fiscal_year'];
    reset($node->data);

    $output .= "\n".",,".'"'."Percent of Levy through June 30, {$last_year}".'"'."\n";
    $header = 'Fiscal year';
    $header .=  ",Tax Levy (in millions)(2)" ;
    $header .=  ",Collections,";
    $header .=  ",Cancellations,";
    $header .=  ",Abatements and Discounts(1),";
    $header .=  ",".'"'."Uncollected Balance June 30, {$last_year}".'"'.',';

    $output .= $header . "\n";

    $count = 1;
    foreach( $node->data as $row){
      $dollar_sign = ($count == 1) ? '$' : '';
      $percent_sign = ($count == 1) ? '%' : '';

      $rowString = $row['fiscal_year'] ;
      $rowString .= ',' . '"' .FormattingUtilities::trendsNumberDisplay($row['tax_levy'],1,'.',',').(($row['fiscal_year']=='2003')?'(2)':'') . '"';
      $rowString .= ',' . '"' . FormattingUtilities::trendsNumberDisplay($row['collection'],1) . '"'.','.$percent_sign;
      $rowString .= ',' . '"' . FormattingUtilities::trendsNumberDisplay($row['cancellations'],1) . '"'.','.$percent_sign;
      $rowString .= ',' . '"' . FormattingUtilities::trendsNumberDisplay($row['abatement_and_discounts_1'],1) . '"'.','.$percent_sign;
      $rowString .= ',' . '"' . FormattingUtilities::trendsNumberDisplay($row['uncollected_balance_percent'],1). '"'.','.$percent_sign;

      $output .= $rowString . "\n";
      $count++;
    }

    $output .= "\n"."\n".'"'."(1) Abatements and Discounts include SCRIE Abatements (Senior citizen rent increase exemption), J-51 Abatements,".'"';
    $output .= "\n"."\n".'"'."Section 626 Abatements and other minor discounts offered by the City to property owners.".'"';
    $output .= "\n"."\n".'"'."(2) The Tax Levy amounts are the amount from the City Council Resolution. In 2003 an 18% surcharge was imposed".'"';
    $output .= "\n".'"'."and is included in each year following.".'"';
    $output .= "\n"."\n".'"'."Notes: Total uncollected balance at June 30, ".$last_year." less allowance for uncollectible amounts equals net realizable amount".'"';
    $output .= "\n".'"'."(real estate taxes receivable).".'"';
    $output .= "\n"."\n".'"'."Levy may total over 100 percent due to imposed charges that include ICIP deferred charges (Industrial and Commercial".'"';
    $output .= "\n".'"'."Incentive Program), rebilling charges and other additional charges imposed by The Department of Finance(DOF). This".'"';
    $output .= "\n".'"'."information is included in the FAIRTAX LEVY report.".'"';
    return $output;
  }

  public static function uncollectedParkingViolationFeeCsv($node) {
    $header = '';
    $output = '';

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

    $header = ',,,,,,,,Fiscal Year,,,,,,,'."\n";
    foreach ($years as $year){
      $header .=  "," . $year ;
    }
    $output .= $header . "\n";
    $output .= ",,,,,,(AMOUNTS IN MILLIONS),,,,,,,"."\n"."\n";

    $count = 1;
    foreach( $table_rows as $row){
      $dollar_sign = ($count == 1 || $count ==  count($table_rows)) ? '$' : '';
      $rowString = '"'.$row['category'].'"' ;
      foreach ($years as $year){
        $rowString .=  ',' . '"'. (($row[$year]['amount'] >0) ?FormattingUtilities::trendsNumberDisplay($row[$year]['amount']) : '') .'"';
      }
      $output .= $rowString . "\n";
      $count++;
    }

    $output .= "\n"."\n".'"'."(a) 	The summonses issued by various City agencies for parking violations are adjudicated and collected by the ".'"';
    $output .= "\n".'"'."       Parking Violations Bureau (PVB) of the City's Department of Finance.".'"';
    $output .= "\n"."\n".'"'."(b) 	Proposed ''write-offs'' are in accordance with a write-off policy implemented by PVB for summonses determined ".'"';
    $output .= "\n"."       to be legally uncollectible/unprocessable or for which all prescribed collection efforts are unsuccessful.".'"';
    $output .= "\n"."\n".'"'."(c) 	The Allowance for Uncollectible Amounts is calculated as follows: summonses which are over three years old are ".'"';
    $output .= "\n"."       fully (100%) reserved and 35% of summonses less than three years old are reserved.".'"';
    $output .= "\n"."\n".'"'."Note: Data does not include interest reflected on the books of PVB.".'"';
    $output .= "\n"."\n".'"'."Source: The City of New York, Department of Finance, Parking Violations Bureau.".'"';
    return $output;
  }

  public static function hudsonYardsInfraCorpCsv($node) {
    $header = '';
    $output = '';

    $header = 'Fiscal year';

    $header .=  ",DIB Revenue(1)";
    $header .=  ",TEP Revenue(2)";
    $header .=  ",ISP Revenue(3)";
    $header .=  ",PILOMRT(4)";
    $header .=  ",PILOT(5)";
    $header .=  ",Other(6)";
    $header .=  ",Investment Earnings";
    $header .=  ",Total Revenue";
    $header .=  ",Debt Service - Interest";
    $header .=  ",Debt Service - Principal";
    $header .=  ",Debt Service - Total";
    $header .=  ",Operating Expenses";
    $header .=  ",Total to be Covered";
    $header .=  ",Coverage on Total Revenue(7)(8)";

    $output .= $header . "\n";
    $output .= "(AMOUNTS IN THOUSANDS)" . "\n";


    $count = 1;
    foreach($node->data as $row){
      $dollar_sign = ($count == 1) ? '$':'';

      $rowString = $row['fiscal_year'] ;
      $rowString .=  ',' .(($row['dib_revenue_1']<>0)? ('"'.FormattingUtilities::trendsNumberDisplay($row['dib_revenue_1']).'"'):'-');
      $rowString .= ',' .(($row['tep_revenue_2']<>0)?('"'.FormattingUtilities::trendsNumberDisplay($row['tep_revenue_2']).'"'):'-');
      $rowString .= ',' .(($row['isp_revenue_3']<>0)?('"'.FormattingUtilities::trendsNumberDisplay($row['isp_revenue_3']).'"'):'-');
      $rowString .= ',' .(($row['pilomrt_payment']<>0)?('"'.FormattingUtilities::trendsNumberDisplay($row['pilomrt_payment']).'"'):'-');
      $rowString .= ',' .(($row['pilot']<>0)?('"'.FormattingUtilities::trendsNumberDisplay($row['pilot']).'"'):'-');
      $rowString .= ',' .(($row['other_4']<>0)?('"'.FormattingUtilities::trendsNumberDisplay($row['other_4']).'"'):'-').(in_array($row['fiscal_year'], ['2019', '2020'])? '(9)':'');
      $rowString .= ',' .(($row['investment_earnings']<>0)?('"'.FormattingUtilities::trendsNumberDisplay($row['investment_earnings']).'"'):'-');
      $rowString .= ',' .(($row['total_revenue']<>0)?('"'.FormattingUtilities::trendsNumberDisplay($row['total_revenue']).'"'):'-');
      $rowString .= ',' .(($row['interest']<>0)?('"'.FormattingUtilities::trendsNumberDisplay($row['interest']).'"'):'-');
      $rowString .= ',' .(($row['principal']<>0)?('"'.FormattingUtilities::trendsNumberDisplay($row['principal']).'"'):'-');
      $rowString .= ',' .(($row['total']<>0)?('"'.FormattingUtilities::trendsNumberDisplay($row['total']).'"'):'-');
      $rowString .= ',' .(($row['operating_expenses']<>0)?('"'.FormattingUtilities::trendsNumberDisplay($row['operating_expenses']).'"'):'-') . ((  $row['fiscal_year'] == '2012')? '(9)':'');
      $rowString .= ',' .(($row['total_to_be_covered']<>0)?('"'.FormattingUtilities::trendsNumberDisplay($row['total_to_be_covered']).'"'):'-');
      $rowString .= ',' . (($row['coverage_on_total_revenue_5'] > 0) ? $row['coverage_on_total_revenue_5'] : "(" . $row['coverage_on_total_revenue_5'] .")").(in_array($row['fiscal_year'], ['2009','2010','2011','2012'])? '(6)':'');

      $output .= $rowString . "\n";
      $count++;
    }

    $output .= "\n"."\n".'"'."HYIC issued its first bonds on December 21, 2006".'"';
    $output .= "\n"."\n".'"'."(1) District Improvement Bonuses (DIB)".'"';
    $output .= "\n".'"'."(2) Property Tax Equivalency Payments (TEP)".'"';
    $output .= "\n"."(3) Interest Support Payments (ISP)".'"';
    $output .= "\n"."(4) Payments in Lieu of the Mortgage Recording Tax (PILOMRT)".'"';
    $output .= "\n"."(5) Payments in Lieu of Real Estate Tax (PILOT)".'"';
    $output .= "\n"."(6) Grant from City".'"';
    $output .= "\n"."(7) ISPs are to be made by the City under the terms of Support and Development Agreement, which obligates the City to pay HYIC, subject to annual appropriation, an ISP amount equal to the difference between the amount of funds available to HYIC to pay interest on its current outstanding bonds and the amount of interest due on such bonds.".'"';
    $output .= "\n"."(8) Debt service payments are funded from excess prior years' revenues and from current year revenues.".'"';
    $output .= "\n"."(9) In December 2011, HYIC was obligated to make an arbitrage rebate payment to United States Treasury for $8.8M. In February 2019, the 8.8M Payment was refunded back to HYIC. ".'"';
    $output .= "\n"."\n".'"'."Source: Hudson Yards Infrastructure Corporation".'"';
    return $output;
  }

}
