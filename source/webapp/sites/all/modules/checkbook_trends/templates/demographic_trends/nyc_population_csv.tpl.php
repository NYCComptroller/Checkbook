<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php

    $header .= ",,,2000-2011*,,,"."\n";
    $header .= 'Year';
    $header .=  ",United States" ;
    $header .=  ",Percentage Change from Prior Period," ;
    $header .=  ",City of New York";
    $header .=  ",Percentage Change from Prior Period,";

	echo $header . "\n";

    $count = 1;
    foreach($node->data as $row){
        $percent_sign = ($count == 1 ) ? '%' : '';
        $rowString = $row['fiscal_year'] ;
        $rowString .= ',' . '"' . (($row['united_states']>0)?number_format($row['united_states']):' - ') .'"';
        $rowString .= ',' . '"' . (($row['percentage_change_from_prior_period']>0)?number_format($row['percentage_change_from_prior_period'],2):' - ') .'"'.','.$percent_sign;
        $rowString .= ',' . '"' . (($row['city_of_new_york']>0)?number_format($row['city_of_new_york']):' - ') .'"';
        $rowString .= ',' . '"' . (($row['percentage_change_prior_period']>0)?number_format($row['percentage_change_prior_period'],2):' - ').'"'.','.$percent_sign;

        echo $rowString . "\n";
        $count++;
   	}

   echo "\n\n"."Note:Data Not Available\n\n";
   echo "\n\n"."Source:U.S Department of Commerce,Bureau of Economic Analysis.\n\n";


?>

