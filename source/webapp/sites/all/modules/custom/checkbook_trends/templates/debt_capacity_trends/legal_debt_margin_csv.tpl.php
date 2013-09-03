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
?>
<?php
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
    	$header .= ",," . $year ;
    }
    $header .= "\n".',,,,,,(in thousands),,,,,'."\n";
	echo $header . "\n";

    $count = 1;
    foreach($table_rows as $row){
        $dollar_sign = ($count == 1 || strtolower($row['category']) == 'legal debt margin') ? '$':'';
        
        $rowString = '"'.$row['category'].'"';
        foreach ($years as $year){
            $amount = '';
            if($count == count($table_rows)){
                $amount = $dollar_sign.','.$row[$year]['amount'] . " %";
            }else{
                if($row[$year]['amount'] > 0){
                   $amount = $dollar_sign.','.'"'. number_format($row[$year]['amount']) .'"';
                }else if($row[$year]['amount'] < 0){
                   $amount = $dollar_sign.','.'"' . "(" . number_format(abs($row[$year]['amount'])) . ")" . '"';
                }else if($row[$year]['amount'] == 0){
                    if(strpos($row['category'], ':'))
                        $amount = $dollar_sign.','.'';
                    else
                        $amount = $dollar_sign.','.'"-"';
                }
            }
            
            $rowString .= ',' . $amount;
        }
        echo $rowString . "\n";
        $count++;
   	}

echo "\n".'"'."(1) Includes adjustments for Business Improvement Districts, Original Issue Discount, Capital Appreciation Bonds Discounts and cash on hand for defeasance.".'"'.
     "\n"."\n".'"'."(2) TFA Debt Outstanding above 13.5 billion.".'"'.
     "\n"."\n".'"'."(3) Excludes TFA Building Aid Revenue bond financing.".'"'.
     "\n"."\n".'"'."The Constitution of the State of New York limits the general debt-incurring power of The City of New York to ten percent of the five-year average of full valuations of taxable real estate.".'"'.
     "\n"."\n".'"'."Obligations for water supply and certain obligations for rapid transit and sewage are excluded pursuant to the State Constitution and in accordance with provisions of the State Local Finance Law. Resources of the General Debt Service Fund applicable to non-excluded debt and debt service appropriations for the redemption of such debt are deducted from the non-excluded funded debt to arrive at the funded debt within the debt limit.".'"'.
     "\n"."\n".'"'."To provide for the City's capital program, State legislation was enacted which created the Transitional Finance Authority (TFA) and TSASC Inc. (TSASC). The new authorization as of July 2009 provides that TFA debt above $13.5 billion is subject to the general debt limit of the City. Without the TFA and TSASC, new contractual commitments for the City’s general obligation financed capital program could not continue to be made. The debt-incurring power of TFA and TSASC has permitted the City to continue to enter into new contractual commitments.
".'"';
?>

