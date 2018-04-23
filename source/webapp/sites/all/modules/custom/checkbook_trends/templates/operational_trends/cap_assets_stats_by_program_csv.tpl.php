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
    	$header .=  "," . $year ;
    }
	echo $header . "\n";

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
            $rowString .= ',' . '"'. (($row[$year]['amount'] > 0)?number_format($row[$year]['amount']):$hyphen) .'"';
        }
        echo $rowString . "\n";
   	}
?>

"(1) FY 2010 includes various other facilities with active enrollment relating to primary, intermediate and high school. This includes minischools, transportables and leased space. Also, multiple district schools may be operated in a single school building."

"Sources: Various City Agencies"