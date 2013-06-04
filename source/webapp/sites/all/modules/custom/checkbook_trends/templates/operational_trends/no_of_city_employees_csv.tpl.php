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
   
	$header = ',,,,,,,Fiscal Year,,,,,'."\n";
    foreach ($years as $year){
    	$header = $header .  "," . $year ;
    }
	echo $header . "\n";

    $i = 1;
    foreach( $table_rows as $row){
        $rowString = '"'.$row['category'].'"' ;
        foreach ($years as $year){
            if($i < count($table_rows)){
                $rowString .= ',' .'"'. (!(strpos($row['category'],':'))?number_format($row[$year]['amount']):'') .'"';
            }
            else{
                if($row[$year]['amount'] < 0)
                     $rowString .= ',' . "(". abs($row[$year]['amount']) . ')%';
                else
                    $rowString .= ',' . $row[$year]['amount'] . "%";
            }
        }
        echo $rowString . "\n";
        $i++;
   	}

echo "\n".'"'."(a) Effective July 2003, certain employees of the education area were reclassified from part-time to full-time status.".'"'
     ."\n\n".'"'."Sources: Financial Management System (FMS), Mayor's Office of Management and Budget, and Mayor's Office of Operations.".'"'."\n";

?>

