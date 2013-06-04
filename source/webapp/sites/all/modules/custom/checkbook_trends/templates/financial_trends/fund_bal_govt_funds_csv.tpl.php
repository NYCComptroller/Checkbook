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
	$header = ',,,,,,,,Fiscal Year,,,,,'."\n";
    foreach ($years as $year){
    	$header = $header .  ",," . $year ;
    }

    $header .= "\n".',,,,,,,,(in thousands),,,,,';
    echo $header . "\n";
    $count = 0;
    foreach( $table_rows as $row){
        $count++;
        $dollar_sign = "";
        if($count == 1 || $count == count($table_rows)){
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
 echo "\n \n" . "Source: Comprehensive Annual Financial Reports of the Comptroller."
       ."\n" . "Note: In fiscal year 2010, the Fund balance classifications were changed to conform to the requirements of GASB54.";
?>

