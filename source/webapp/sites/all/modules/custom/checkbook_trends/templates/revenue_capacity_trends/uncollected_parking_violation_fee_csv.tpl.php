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

	$header = ',,,,,,,,Fiscal Year,,,,,,,'."\n";
    foreach ($years as $year){
    	$header .=  "," . $year ;
    }
	echo $header . "\n";
    echo ",,,,,,(AMOUNTS IN MILLIONS),,,,,,,"."\n"."\n";

    $count = 1;
    foreach( $table_rows as $row){
        $dollar_sign = ($count == 1 || $count ==  count($table_rows)) ? '$' : '';
        $rowString = '"'.$row['category'].'"' ;
        foreach ($years as $year){
            $rowString .=  ',' . '"'. (($row[$year]['amount'] >0) ?number_format($row[$year]['amount']) : '') .'"';
        }
        echo $rowString . "\n";
        $count++;
   	}

    echo "\n".'"'."(a) 	The summonses issued by various City agencies for parking violations are adjudicated and collected by the Parking Violations Bureau (PVB) of the City’s Department of Finance.".'"'
        ."\n".'"'."(b) 	Proposed “write-offs” are in accordance with a write-off policy implemented by PVB for summonses determined to be legally uncollectible/unprocessable or for which all prescribed collection efforts are unsuccessful.".'"'
        ."\n".'"'."(c) 	The Allowance for Uncollectible Amounts is calculated as follows: summonses which are over three years old are fully (100%) reserved and 35% of summonses less than three years old are reserved.".'"'
        ."\n".'"'."Note: Data does not include interest reflected on the books of PVB.".'"'
        ."\n".'"'."Source: The City of New York, Department of Finance, Parking Violations Bureau.".'"';