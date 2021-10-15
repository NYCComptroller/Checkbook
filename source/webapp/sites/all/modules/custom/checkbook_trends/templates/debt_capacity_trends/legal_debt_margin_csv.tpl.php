<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
* 
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

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
	echo $header . "\n";

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
                   $amount = '"'. number_format($row[$year]['amount']) .'"';
                }else if($row[$year]['amount'] < 0){
                   $amount = '"' . "(" . number_format(abs($row[$year]['amount'])) . ")" . '"';
                }else if($row[$year]['amount'] == 0){
                    if(strpos($row['category'], ':'))
                        $amount = '';
                    else
                        $amount = '"-"';
                }
            }
            
            $rowString .= ',' . $amount;
        }
        echo $rowString . "\n";
        $count++;
   	}

?>


"Notes:"
"(1)   The Legal Debt Margin and the Net Debt Applicable to the Debt Limit as a Percentage of the Debt Limit are recalculated on July 1, the first day of each City fiscal year,"
"based on the new assessed value in accordance with the new year’s enacted tax fixing resolution."
"For fiscal year 2021, beginning July 1, 2020, the Legal Debt Margin and the Net Debt Applicable to the Debt Limit as a Percentage of the Debt Limit"
"are 45,813,177 and 62.76%, respectively. "
"(2) A five-year average of full valuations of taxable real estate from the Resolutions of the Council Fixing the Tax Rates for the fiscal year beginning on July 1, 2019 and ending on June 30, 2020."
"(3) The Constitution of the State of New York limits the general debt-incurring power of The City of New York to ten percent of the five-year average of full valuations of"
"taxable real estate."
"(4) Includes adjustments for Business Improvement Districts, Original Issue Discount, and cash on hand for defeasance."
"(5) To provide for the City's capital program, State legislation was enacted which created the Transitional Finance Authority (TFA)."
"TFA debt above 13.5 billion (Excludes TFA Building Aid Revenue bonds and Recovery Bonds) is subject to the general debt limit of the City."
"(6) Obligations for water supply and certain obligations for rapid transit are excluded pursuant to the State Constitution and in accordance with provisions of the State Local Finance Law."
"Resources of the General Debt Service Fund applicable to non-excluded debt and debt service appropriations for the redemption of such debt are deducted from the non-excluded funded debt"
"to arrive at the funded debt within the debt limit."
