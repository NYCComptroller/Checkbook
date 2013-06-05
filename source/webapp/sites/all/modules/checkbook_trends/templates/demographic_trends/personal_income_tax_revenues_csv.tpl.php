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
		$table_rows[$row['display_order']]['area'] =  $row['area'];
		$table_rows[$row['display_order']]['fips'] =  $row['fips'];
		$table_rows[$row['display_order']]['line_code'] =  $row['line_code'];		
		$table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
		$table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
		$table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
		$table_rows[$row['display_order']][$row['fiscal_year']]['amount'] = $row['amount'];
		$years[$row['fiscal_year']] = 	$row['fiscal_year'];
	}
	sort($years);
    echo "CA1-3 Personal income summary"."\n" ."Bureau of Economic Analysis". "\n" ."\n";
	$header = 'FIPS';
	$header .= ',Area';
	$header .= ',LineCode';
	$header .= ',Description';
    foreach ($years as $year){
    	$header = $header .  "," . $year ;
    }
	echo $header . "\n";

    foreach( $table_rows as $row){
        $rowString = $row['fips'] ;
        $rowString .= ','  . '"'. $row['area'] . '"' ;
        $rowString .= ','  . '"'. $row['line_code']  . '"';
        $rowString .= ','  . '"'. $row['category']  . '"';
        foreach ($years as $year){
            $rowString .= ','  . '"'. $row[$year]['amount'] . '"';
        }
        echo $rowString . "\n";
   	}

echo "\n"."Legend / Footnotes:". "\n"
 .'"' ."1/ Census Bureau midyear population estimates. Estimates for 2000-2009 reflect county population estimates available as of April 2010. For more information see the explanatory note at: http://www.bea.gov/regional/docs/popnote.cfm.". '"' ."\n"
 .'"'. "2/ Per capita personal income was computed using Census Bureau midyear population estimates. Estimates for 2000-2009 reflect county population estimates available as of April 2010.".'"'."\n"
 .'"'."All state and local area dollar estimates are in current dollars (not adjusted for inflation).".'"'."\n"
 .'"'."Last updated: April 21, 2011 - new estimates for 2009; revised estimates for 2001-2008.".'"'."\n";
?>

