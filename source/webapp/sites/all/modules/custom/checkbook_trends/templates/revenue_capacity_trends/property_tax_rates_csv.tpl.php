<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php

    $header = 'Fiscal year';
    $header .=  ",,Basic Rate" ;
    $header .=  ",,Obligation Debt" ;
    $header .=  ",,Total Direct";
    
	echo $header . "\n";
    $count = 1;

    foreach( $node->data as $row){
        $dollar_sign = ($count == 1)?"$":"";
        $rowString = $row['fiscal_year'] ;
        $rowString .= ',' . $dollar_sign .',' . $row['basic_rate'];
        $rowString .= ',' . $dollar_sign .',' . $row['obligation_debt'];
        $rowString .= ',' . $dollar_sign .',' . $row['total_direct'];

        echo $rowString . "\n";
        $count++;
   	}

  
   echo "\n"."\n"."Note: Property tax rate based on every $100 of assessed valuations.";
   echo "\n"."\n"."SOURCE: Resolutions of the City Council.";
?>

