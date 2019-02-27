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
        if($year == 2016)
    	    $header = $header .  "," . $year .'(b)' ;
        else
    	    $header = $header .  "," . $year ;
    }
	echo $header . "\n";
    $i = 0;
    foreach($table_rows as $row){
        $rowString= null;
        foreach ($years as $year){
            if($i == count($table_rows)-1){
                if($row[$year]['amount'] > 0){
                    if($year == 2016)
                        $amount = $row[$year]['amount'] . "(b)%";
                    else
                       $amount = $row[$year]['amount'] . "%";
                }
                else if($row[$year]['amount'] < 0)
                    $amount = '"' . "(" . abs($row[$year]['amount']) . "%)" . '"';
                else
                    $amount = "NA";
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

        $i++;
        echo '"'.$row['category'].'"'.$rowString . "\n";
   	}

echo "\n"."\n"."(a) Includes rounding  adjustments"."\n"."\n"
        ."(b) Six month average"."\n"."\n";

//.'"'."Notes: This schedule is provided in lieu of a schedule of principal employees because it provides more meaningful information. Other than the City of New York, no single
//employer employs more than 2 percent of total nonagricultural employees.".'"'."\n\n".
//"Data are not seasonally adjusted."."\n\n".'"'.
//"Source: New York State Department of Labor, Division of Research and Statistics.".'"'
