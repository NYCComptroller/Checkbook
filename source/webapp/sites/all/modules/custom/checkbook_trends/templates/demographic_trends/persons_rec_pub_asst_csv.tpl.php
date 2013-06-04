<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
	echo "\n".'2002-2011 (annual averages in thousands)'."\n"."\n";
    $header = 'Year';
    $header .=  ",Public Assistance" ;
    $header .=  ",SSI(a)" ;

	echo $header . "\n";


    foreach( $node->data as $row){
        $rowString = $row['fiscal_year'] ;
        $rowString .= ','  . '"'. $row['public_assistance'] . '"';
        $rowString .= ','  . '"'. (($row['ssi']>0)?number_format($row['ssi']) : 'NA') . '"';

        echo $rowString . "\n";
   	}

    echo "\n"."(a) The SSI data is for December of each year."."\n"."\n".
           "NA: Not Available."."\n"."\n".
           '"'. "Sources: The City of New York, Human Resources Administration and the U.S. Social Security Administration.".'"';
?>

