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

class DemographicTrendsCSV  {

  public static function nycPopulationCsv($node){
    $header = '';
    $output ='';

    $last_year = end($node->data)['fiscal_year'];
    reset($node->data);


    $header .= ",,2001-{$last_year}*,,\n";
    $header .= 'Year';
    $header .=  ",United States" ;
    $header .=  ",Percentage Change from Prior Period" ;
    $header .=  ",City of New York";
    $header .=  ",Percentage Change from Prior Period";

    $output .= $header . "\n";

    $count = 1;
    foreach($node->data as $row){
      $percent_sign = ($count == 1 ) ? '%' : '';
      $rowString = $row['fiscal_year'] ;
      $rowString .= ',' . '"' . (($row['united_states']>0)?FormattingUtilities::trendsNumberDisplay($row['united_states']):' - ') .'"';
      $rowString .= ',' . '"' . (($row['percentage_change_from_prior_period']>0)?FormattingUtilities::trendsNumberDisplay($row['percentage_change_from_prior_period'],2):' - ') .'"'.$percent_sign;
      $rowString .= ',' . '"' . (($row['city_of_new_york']>0)?FormattingUtilities::trendsNumberDisplay($row['city_of_new_york']):' - ') .'"';
      $rowString .= ',' . '"' . (($row['percentage_change_prior_period']!=0)?FormattingUtilities::trendsNumberDisplay($row['percentage_change_prior_period'],2):' - ').'"'.$percent_sign;

      $output .= $rowString . "\n";
      $count++;
    }

    $output .= "\n"."\n"."Source: Bureau of Economic Analysis and US Census Bureau."."\n".'"'."*Figures as of July 2022".'"';
    return $output;
  }

  public static function personalIncomeTaxRevenuesCsv($node) {
    $header = '';
    $output = '';

    $table_rows = array();
    $years = array();
    foreach( $node->data as $row){
      $table_rows[$row['display_order']]['area'] =  $row['area'];
      $table_rows[$row['display_order']]['fips'] =  $row['fips'];
      $table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
      $table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
      $table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
      $table_rows[$row['display_order']][$row['fiscal_year']]['amount'] = $row['amount'];
      $years[$row['fiscal_year']] = 	$row['fiscal_year'];
    }
    sort($years);
    $output .= "CA1-3 Personal income summary"."\n" ."Bureau of Economic Analysis". "\n" ;
    $output .= "(AMOUNTS IN THOUSANDS)\n" ."\n";
    $header = 'FIPS';
    $header .= ',Area';
    foreach ($years as $year){
      $header = $header .  "," . $year ;
    }
    $output .= $header . "\n";

    $count = 1;
    foreach( $table_rows as $row){
      $dollar_sign = ($count == 1 ) ? '$' : '';
      $count++;
      $rowString = $row['fips'] ;
      $rowString .= ','  . '"'. $row['area'] . '"' ;
      foreach ($years as $year){
        $rowString .= ','  . '"'. FormattingUtilities::trendsNumberDisplay($row[$year]['amount'], 0, $dollar_sign) . '"';
      }
      $output .= $rowString . "\n";
    }

    $output .= "\n"."\n".'"'."Legend / Footnotes:".'"';
    $output .= "\n"."\n".'"'."Note-- All state and local area dollar estimates are in current dollars (not adjusted for inflation).".'"';
    $output .= "\n"."\n".'"'."Last updated: November 26, 2012 - new estimates for 2011; revised estimates for 2009-2010. For more information see the explanatory note at: http://www.bea.gov/regional/docs/popnote.cfm.".'"';
    return $output;
  }

  public static function nonAgrEmploymentCsv($node) {
    $header = '';
    $output = '';

    $table_rows = [];
    $years = [];
    foreach( $node->data as $row){
      $table_rows[$row['display_order']]['category'] = $row['category'];
      $table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
      $table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
      $table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
      $table_rows[$row['display_order']][$row['fiscal_year']]['amount'] = $row['amount'];
      $years[$row['fiscal_year']] = 	$row['fiscal_year'];
    }
    rsort($years);
    $header = '';
    $header .= ',,,,,,'.end($years).'-'.$years[0].',,,,,'."\n";
    $header .= ',,,,,,(average annual employment in thousands),,,,,'."\n";

    foreach ($years as $year){
      if($year == 2021 || $year == 2022 || $year == 2023) {
        $header = $header .  "," . $year .'(b)' ;
      }
      else {
        $header = $header .  "," . $year ;
      }
    }
    $output .= $header . "\n";
    $i = 0;
    foreach($table_rows as $row){
      $rowString= null;
      foreach ($years as $year){
        if($i == count($table_rows)-1){
          if($row[$year]['amount'] > 0){
            $amount = $row[$year]['amount'] . "%";
          }
          else if($row[$year]['amount'] < 0) {
            $amount = '"' . "(" . abs($row[$year]['amount']) . "%)" . '"';
          }
          else {
            $amount = "NA";
          }
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

      $i++;
      $output .= '"'.$row['category'].'"'.$rowString . "\n";
    }

    $output .= "\n"."\n".'"'."(a) Includes rounding adjustment".'"';
    $output .= "\n".'"'."(b) Six months average".'"';
    $output .= "\n"."\n".'"'."NOTES: This Schedule is provided in lieu of a schedule of principal employee because it provides more meaningful information.".'"';
    $output .= "\n".'"'."Other than the City of New York, no single employer employs more than 2 percent of total non agricultural employees.".'"';
    $output .= "\n"."\n".'"'."Data are not seasonally adjusted.".'"';
    $output .= "\n"."\n".'"'."Source: New York State Department of Labor, Division of Research and Statistics.".'"';
    return $output;
  }

  public static function personsRecPubAsstCsv($node) {
    $header = '';
    $output = '';

    $last_year = end($node->data)['fiscal_year'];
    reset($node->data);

    $output .= "\n".'2002-'.$last_year.' (annual averages in thousands)'."\n"."\n";
    $header = 'Year';
    $header .=  ",Public Assistance" ;
    $header .=  ",SSI(a)" ;

    $output .= $header . "\n";

    foreach( $node->data as $row){
      $rowString = $row['fiscal_year'] ;
      $rowString .= ','  . '"'. $row['public_assistance'] . '"';
      $rowString .= ','  . '"'. (($row['ssi']>0)?FormattingUtilities::trendsNumberDisplay($row['ssi']) : 'NA') . '"';

      $output .= $rowString . "\n";
    }

    $output .= "\n"."\n".'"'."(a) The SSI data is for December of each year.".'"';
    $output .= "\n"."\n".'"'."NA: Not Available.".'"';
    $output .= "\n"."\n".'"'."Sources: The City of New York, Human Resources Administration and the U.S. Social Security Administration.".'"';
    return $output;
  }

  public static function empStatusOfResidentPopulationCsv($node) {
    $header = '';
    $output = '';

    $first_year = $node->data[0]['fiscal_year'];
    $last_year = end($node->data)['fiscal_year'];
    reset($node->data);

    $header = ",,{$first_year}-{$last_year},,"."\n";
    $header .= 'Year';

    $header .=  ",New York City Employed - Civilian Labor Force (in thousands)" ;
    $header .=  ",New York City Unemployed (a) - Civilian Labor Force (in thousands)"  ;

    $header .=  ",New York City Unemployment Rate";
    $header .=  ",United States Unemployment Rate";

    $output .= $header . "\n";
    $count = 1;
    foreach( $node->data as $row){
      $percent_sign = ($count == 1) ? '%' : '';
      $rowString = $row['fiscal_year'] ;
      $rowString .= ','  . '"'. FormattingUtilities::trendsNumberDisplay($row['civilian_labor_force_new_york_city_employed']) . '"';
      $rowString .= ','  . '"'. FormattingUtilities::trendsNumberDisplay($row['civilian_labor_force_unemployed']) . '"';
      $rowString .= ','  . '"'. FormattingUtilities::trendsNumberDisplay($row['unemployment_rate_city_percent'],1) . '"'.$percent_sign;
      $rowString .= ','  . '"'. FormattingUtilities::trendsNumberDisplay($row['unemployment_rate_united_states_percent'],1) . '"'.$percent_sign;

      $output .= $rowString . "\n";
      $count++;
    }

    $output .= "\n"."\n".'"'."(a) Unemployed persons are all civilians who had no employment during the survey week, were available for work, except ".'"';
    $output .= "\n".'"'."for temporary illness, and had made efforts to find employment some time during the prior four weeks. This includes ".'"';
    $output .= "\n".'"'."persons who were waiting to be recalled to a job from which they were laid off or were waiting to report to a new job ".'"';
    $output .= "\n".'"'."within 30 days.".'"';
    $output .= "\n"."\n".'"'."Note: Employment and unemployment information is not seasonally adjusted.".'"';
    $output .= "\n"."\n".'"'."Sources: U.S. Department of Labor, Bureau of Labor Statistics, and Office of the Comptroller, Fiscal and Budget Studies.".'"';
    return $output;
  }

}
