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
		$table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
		$table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
		$table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
		$table_rows[$row['display_order']][$row['fiscal_year']]['amount'] = $row['amount'];
		$years[$row['fiscal_year']] = 	$row['fiscal_year'];
	}
	rsort($years);

	$header = ',,,,,,,,Fiscal Year,,,,,,,'."\n";
    foreach ($years as $year){
    	$header .=  ",," . $year ;
    }
	echo $header . "\n";
    echo ",,,,,,,,,(in millions),,,,,,,"."\n"."\n";

    $count = 1;
    foreach( $table_rows as $row){
        $dollar_sign = ($count == 1 || $count ==  count($table_rows)) ? '$' : '';
        $rowString = '"'.$row['category'].'"' ;
        foreach ($years as $year){
            $rowString .= ',' .$dollar_sign . ',' . '"'. (($row[$year]['amount'] >0) ?number_format($row[$year]['amount']) : '') .'"';
        }
        echo $rowString . "\n";
        $count++;
   	}

    echo "\n".'"'."(a) The summonses issued by various City agencies for parking violations are adjudicated and collected by the Parking Violations Bureau (PVB) of the City's Department of Finance.".'"'
        ."\n".'"'."(b) Proposed "."\"write-offs\" are in accordance with a write-off policy implemented by PVB for summonses determined to be legally uncollectible/unprocessable or for which all prescribed collection efforts are unsuccessful.".'"'
        ."\n".'"'."(c) The Allowance for Uncollectible Amounts is calculated as follows: summonses which are over three years old are fully (100%) reserved and 35% of summonses less than three years old are reserved.".'"'
        ."\n".'"'."Note: Data does not include interest reflected on the books of PVB.".'"'
        ."\n".'"'."Source: The City of New York, Department of Finance, Parking Violations Bureau.".'"';


?>

