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

class OperationalTrendsCSV  {

  public static function capAssetsStatsByProgramCsv($node){
    $header = '';
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
      $header .=  "," . $year ;
    }
    $output .= $header . "\n";

    foreach( $table_rows as $row){
      $category = $row['category'];
      if(strpos($category, "—")){
        $category = str_replace("—","--",$category);
      }
      $rowString = '"'.$category.'"' ;
      if(strpos($row['category'], ':')){
        $hyphen = "";
      }else{
        $hyphen = "-";
      }
      foreach ($years as $year){
        $rowString .= ',' . '"'. (($row[$year]['amount'] > 0)?FormattingUtilities::trendsNumberDisplay($row[$year]['amount']):$hyphen) .'"';
      }
      $output .= $rowString . "\n";
    }

    $output .= "\n"."\n".'"'."(1)The Fire Vehicles for FY 2018 were corrected from 2,616 to 2,641".'"';
    $output .= "\n"."(2) The Street Lights for FY 2017 were corrected from 396,572 to 321,807 and FY 2018 from 414,938 to 323,195 due to re-evaluation of items included in street lighting Inventory".'"';
    $output .= "\n"."(3)The Lane miles Resurfaced for FY 2018 were corrected from 1,300 to 1,321 ".'"';
    $output .= "\n"."\n".'"'."Sources: Various City Agencies".'"';
    return $output;
  }

  public static function noOfCityEmployeesCsv($node) {
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

    $header = ',,,,,,,Fiscal Year,,,,,'."\n";
    foreach ($years as $year){
      $header = $header .  "," . $year ;
    }
    $output .= $header . "\n";

    $i = 1;
    foreach( $table_rows as $row){
      $rowString = '"'.$row['category'].'"' ;
      foreach ($years as $year){
        if($i < count($table_rows)){
          $rowString .= ',' .'"'. (!(strpos($row['category'],':'))?FormattingUtilities::trendsNumberDisplay($row[$year]['amount']):'') .'"';
        }
        else{
          if($row[$year]['amount'] < 0)
            $rowString .= ',' . "(". abs($row[$year]['amount']) . ')%';
          else
            $rowString .= ',' . $row[$year]['amount'] . "%";
        }
      }
      $output .= $rowString . "\n";
      $i++;
    }

    $output .= "\n"."\n".'"'."Sources: Financial Management System (FMS), Mayor's Office of Management and Budget, and Mayor's Office of Operations.".'"';
    return $output;
  }

}
