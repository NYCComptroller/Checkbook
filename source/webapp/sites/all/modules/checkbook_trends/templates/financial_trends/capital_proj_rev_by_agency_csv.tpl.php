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
	$header = ',,,,,,,,Fiscal Year,,,,,'."\n";
    foreach ($years as $year){
    	$header = $header .  ",," . $year ;
    }

    $header .= "\n".',,,,,,,,(in thousands),,,,,';
    echo $header . "\n";
    $count = 0;

    foreach($table_rows as $row){
        $count++;
        $dollar_sign = "";
        if($count == 2 || $count == count($table_rows)){
            $dollar_sign = "$";
        }

        $rowString = '"'.$row['category'].'"';
        foreach ($years as $year){
            $rowString .= ",".$dollar_sign;
            $amount = '';
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
            
            $rowString .= ',' . $amount;
        }
        echo $rowString . "\n";
   	}
echo "\n \n" . "Source: Comprehensive Annual Financial Reports of the Comptroller.";
?>

