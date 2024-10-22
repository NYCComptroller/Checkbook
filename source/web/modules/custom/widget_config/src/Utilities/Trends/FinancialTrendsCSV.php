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

class FinancialTrendsCSV  {

  const FISCAL_YEAR = ',,,,,,,,Fiscal Year,,,,,';

  const AMOUNTS_IN_THOUSANDS = ',,,,,,,,(AMOUNTS IN THOUSANDS),,,,,';

  const INFO_1 = 'Source: Annual Comprehensive Financial Reports of the Comptroller.';
  const INFO_2 = 'Source: Annual Comprehensive Financial Report (ACFR).';

  public static function changesInNetAssetsCsv($node){
    $output ='';

    $table_rows = array();
    $years = array();
    foreach ($node->data as $row) {
      $table_rows[$row['display_order']]['category'] = $row['category'];
      $table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
      $table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
      $table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
      $table_rows[$row['display_order']][$row['fiscal_year']]['amount'] = $row['amount'];
      $years[$row['fiscal_year']] = $row['fiscal_year'];
    }
    rsort($years);
    $header = self::FISCAL_YEAR . "\n" . self::AMOUNTS_IN_THOUSANDS . "\n";
    foreach ($years as $year) {
      $header = $header . "," . $year;
    }

    $output .= $header . "\n";

    $count = 0;

    foreach ($table_rows as $row) {
      $count++;
      $dollar_sign = "";
      if ($count == 3 || $count == count($table_rows)) {
        $dollar_sign = "$";
      }
      $rowString = '"' . $row['category'] . '"';
      foreach ($years as $year) {
        $amount = '';
        if ($row[$year]['amount'] != 0) {
          $amount = FormattingUtilities::trendsNumberDisplay($row[$year]['amount'], 0, $dollar_sign);
        } else {
          if (strpos($row['category'], ':')) {
            $amount = '';
          }
          else {
            $amount = '-';
            switch ($row['category']) {
              case 'Business-type activities':
              case 'Business-type activities capital grants and contributions':
              case 'Total business-type activities program revenues':
              case 'Total primary government program revenues':
              case 'Government Activities Net Expenses':
              case 'Busines-type activities net expenses':
              case 'Business-type activities changes in net position':
                $amount = 'N/A';
                break;
            }
          }
        }

        $rowString .= ',"' . $amount . '"';
      }
      $output .= $rowString . "\n";
    }

    $output .= "\n"."\n";
    $output .= '"Note: The City implemented GASB Statement No. 80 Blending Requirement for Certain Component Units - an amendment of GASB"'."\n";
    $output .= '"Statement No. 14 in Fiscal Year 2017 with a restatement of fiscal year 2016, which established business-type activities.  Therefore, there is no"'."\n";
    $output .= '"activity presented prior to the implementation fiscal year."'."\n";
    $output .= "N/A: Not Available"."\n";
    $output .= '"'.self::INFO_1.'"';
    return $output;
  }

  public static function fundBalGovtFundsCsv($node) {
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
    $header = self::FISCAL_YEAR . "\n" . self::AMOUNTS_IN_THOUSANDS . "\n";
    foreach ($years as $year){
      $header = $header .  "," . $year ;
    }

    $output .= $header . "\n";
    $count = 0;
    foreach( $table_rows as $row){
      $count++;
      $dollar_sign = "";
      if($count == 1 || $count == count($table_rows)){
        $dollar_sign = "$";
      }
      $rowString = '"'.$row['category'].'"';
      foreach ($years as $year){
        $amount = '';
        if($row[$year]['amount'] != 0){
          $amount = FormattingUtilities::trendsNumberDisplay($row[$year]['amount'], 0, $dollar_sign);
        }else{
          if(strpos($row['category'], ':')) {
            $amount = '';
          }
          else {
            $amount = '-';
          }
        }

        $rowString .= ',' . '"' . $amount . '"';
      }
      $output .= $rowString . "\n";
    }

    $output .= "\n"."\n".'"'.self::INFO_1.'"';
    $output .= "\n"."\n".'"'."Note: In fiscal year 2010, the Fund balance classifications were changed to conform to the requirements of GASB54.".'"';
    return $output;
  }

  public static function changesInFundBalCsv($node) {
    $header = '';
    $output = '';

    $table_rows = array();
    $years = array();
    foreach( $node->data as $row){
      $table_rows[$row['display_order']]['category'] = $row['category'];
      $table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
      $table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
      $table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
      $table_rows[$row['display_order']]['currency_symbol'] = $row['currency_symbol'];
      $table_rows[$row['display_order']][$row['fiscal_year']]['amount'] = $row['amount'];
      $years[$row['fiscal_year']] = 	$row['fiscal_year'];
    }
    rsort($years);
    $header = self::FISCAL_YEAR . "\n" . self::AMOUNTS_IN_THOUSANDS . "\n";
    foreach ($years as $year){
      $header = $header .  "," . $year ;
    }

    $output .= $header . "\n";
    $count = 0;

    foreach($table_rows as $row){
      $count++;
      $dollar_sign =  $row['currency_symbol'] == 'Y' ? '$' : '';
      $rowString = '"'.$row['category'].'"';
      foreach ($years as $year){
        $amount = '';
        if($count == count($table_rows)){
          $amount = $row[$year]['amount'] . '%';
        }
        else{
          if($row[$year]['amount'] != 0){
            $amount = FormattingUtilities::trendsNumberDisplay($row[$year]['amount'], 0, $dollar_sign);
          }else{
            if(strpos($row['category'], ':')|| strtolower($row['category']) == 'less capital outlays') {
              $amount = '';
            }
            else {
              $amount = '-';
            }
          }
        }
        $rowString .= ',' . '"' . $amount . '"';
      }

      $output .= $rowString . "\n";
    }

    $output .= "\n"."\n".'"'.self::INFO_1.'"';
    return $output;
  }

  public static function generalFundRevenueOtherFinSourcesCsv($node) {
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
    $header = self::FISCAL_YEAR . "\n" . self::AMOUNTS_IN_THOUSANDS . "\n";
    foreach ($years as $year){
      $header = $header .  "," . $year ;
    }

    $output .= $header . "\n";
    $count = 0;

    foreach( $table_rows as $row){
      $count++;
      $dollar_sign = "";
      if($count == 2 || $count == count($table_rows)){
        $dollar_sign = "$";
      }

      $rowString = '"'.$row['category'].'"';
      foreach ($years as $year){
        $amount = '';
        if ($row[$year]['amount'] != 0) {
          $amount = FormattingUtilities::trendsNumberDisplay($row[$year]['amount'], 0, $dollar_sign);
        }
        else {
          if (strpos($row['category'], ':')) {
            $amount = '';
          }
          else {
            $amount = '-';
          }
        }

        $rowString .= ',' . '"' . $amount . '"';
      }
      $output .= $rowString . "\n";
    }

    $output .= "\n"."\n".'"'.self::INFO_1.'"';
    return $output;
  }

  public static function generalFundExpendOtherFinSourcesCsv($node) {
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
    $header = self::FISCAL_YEAR. "\n" . self::AMOUNTS_IN_THOUSANDS . "\n";
    foreach ($years as $year){
      $header = $header .  "," . $year ;
    }

    $output .= $header . "\n";
    $count = 0;

    foreach( $table_rows as $row){
      $count++;
      $dollar_sign = "";
      if($count == 2 || $count == count($table_rows)){
        $dollar_sign = "$";
      }

      $rowString = '"'.htmlspecialchars_decode($row['category']).'"';
      foreach ($years as $year){
        $amount = '';
        if ($row[$year]['amount'] != 0) {
          $amount = FormattingUtilities::trendsNumberDisplay($row[$year]['amount'], 0, $dollar_sign) ;
        }
        else {
          if(strpos($row['category'], ':')) {
            $amount = '';
          }
          else {
            $amount = '-';
          }
        }

        $rowString .= ',' . '"' . $amount . '"' ;
      }
      $output .= $rowString . "\n";
    }

    $output .= "\n"."\n".'"'.self::INFO_1.'"';
    return $output;
  }

  public static function capitalProjRevByAgencyCsv($node) {
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
    $header = self::FISCAL_YEAR . "\n" . self::AMOUNTS_IN_THOUSANDS . "\n";
    foreach ($years as $year){
      $header = $header .  "," . $year ;
    }

    $output .= $header . "\n";
    $count = 0;

    foreach($table_rows as $row){
      $count++;
      $dollar_sign = "";
      if($count == 2 || $count == count($table_rows)){
        $dollar_sign = "$";
      }

      $rowString = '"'.$row['category'].'"';
      foreach ($years as $year){
        $amount = '';
        if($row[$year]['amount'] != 0){
          $amount = '"'. FormattingUtilities::trendsNumberDisplay($row[$year]['amount'], 0, $dollar_sign) .'"';
        }else if($row[$year]['amount'] == 0){
          if(strpos($row['category'], ':')) {
            $amount = '';
          }
          else {
            $amount = '"-"';
          }
        }

        $rowString .= ',' . $amount;
      }
      $output .= $rowString . "\n";
    }

    $output .= "\n"."\n".'"'.self::INFO_2.'"';
    return $output;
  }

  public static function nycEduConstFundCsv($node) {
    $header = '';
    $output = '';

    $header = 'Fiscal year';

    $header .=  ",Rental Revenue" ;
    $header .=  ",Investment Earnings" ;
    $header .=  ",Other Income";
    $header .=  ",Total Revenue";

    $header .=  ",Debt Service - Interest";
    $header .=  ",Debt Service - Principal";
    $header .=  ",Debt Service - Total";
    $header .=  ",Operating Expenses";
    $header .=  ",Total to be Covered";
    $header .=  ",Coverage Ratio";

    $output .= "\n" . $header . "\n";

    $count = 1;
    foreach( $node->data as $row){
      $dollar_sign = ($count == 1) ? '$' : '';
      $rowString = $row['fiscal_year'] ;
      $rowString .= ',"' . FormattingUtilities::trendsNumberDisplay($row['rental_revenue'], 0, $dollar_sign).'"';
      $rowString .= ',"' . FormattingUtilities::trendsNumberDisplay($row['interest_revenue'], 0, $dollar_sign).'"';
      $rowString .= ',"' . ($row['other_income'] ? FormattingUtilities::trendsNumberDisplay($row['other_income'], 0, $dollar_sign) : '-').'"';
      $rowString .= ',"' . FormattingUtilities::trendsNumberDisplay($row['total_revenue'], 0, $dollar_sign).'"';
      $rowString .= ',"' . FormattingUtilities::trendsNumberDisplay($row['interest'], 0, $dollar_sign).'"';
      $rowString .= ',"' . FormattingUtilities::trendsNumberDisplay($row['pricipal'], 0, $dollar_sign).'"';
      $rowString .= ',"' . FormattingUtilities::trendsNumberDisplay($row['total'], 0, $dollar_sign).'"';
      $rowString .= ',"' . FormattingUtilities::trendsNumberDisplay($row['operating_expenses'], 0, $dollar_sign).'"';
      $rowString .= ',"' . FormattingUtilities::trendsNumberDisplay($row['total_to_be_covered'], 0, $dollar_sign).'"';
      $rowString .= ',' .  FormattingUtilities::trendsNumberDisplay($row['coverage_ratio'],2);

      $output .= $rowString . "\n";
      $count++;
    }

    $output .= "\n"."\n".'"'."(*) Interest of $8,919,000 was capitalized during FY13 construction for year 2011 and 2010 bonds.".'"';
    $output .= "\n".'"'."In FY2014 ECF received $7 million in income for option for E. 57th development to extend lease beyond 99 years.".'"';
    $output .= "\n".'"'."Operating Expenses exclude Post Employment Benefits accrual.".'"';
    $output .= "\n".'"'."Principal in 2016 does not include the redemption amount of the 2005 bonds on October 1, 2015.".'"';
    $output .= "\n".'"'."In FY2017 and FY2018, ECF received participation payments from E57th Street condo sales by the developer of $10 million and $18.7 million, respectively.".'"';
    $output .= "\n".'"'."Prinicpal in FY2019 and FY2021 does not include redemption amount of the 2007 bonds and 2011 bonds, respectively.".'"';
    $output .= "\n"."\n".'"'."Source: New York City Educational Construction Fund.".'"';
    return $output;
  }

}
