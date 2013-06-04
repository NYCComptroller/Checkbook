<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
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

