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
	$header = '';
    $header .= ',,,,,,1997-2011,,,,,'."\n";
    $header .= ',,,,,,(average annual employment in thousands),,,,,'."\n";

    foreach ($years as $year){
        if($year == 2011)
    	    $header = $header .  ",," . $year .'(b)' ;
        else
    	    $header = $header .  ",," . $year ;
    }
	echo $header . "\n";
    $i = 0;
    foreach($table_rows as $row){
        $rowString= null;
        foreach ($years as $year){
            if($i == count($table_rows)-1){
                if($row[$year]['amount'] > 0)
                    $amount = $row[$year]['amount'] . "%";
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
            
            $rowString .= ',,' . $amount;
        }
        $i++;
        echo '"'.$row['category'].'"'.$rowString . "\n";
   	}

echo "\n"."\n"."(a) Includes rounding  adjustments"."\n"."\n"
        ."(b) Six month average"."\n"."\n"
        ."NA:Not Available"."\n\n"
.'"'."Notes: This schedule is provided in lieu of a schedule of principal employees because it provides more meaningful information. Other than the City of New York, no single
employer employs more than 2 percent of total nonagricultural employees.".'"'."\n\n".
"Data are not seasonally adjusted."."\n\n".'"'.
"Source: New York State Department of Labor, Division of Research and Statistics.".'"';
?>

