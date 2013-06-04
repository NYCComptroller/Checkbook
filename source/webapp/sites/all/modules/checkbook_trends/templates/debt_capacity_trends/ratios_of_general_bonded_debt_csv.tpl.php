<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
	$header = 'Fiscal Year';

    $header .=  ",,General Obligation Bonds (in millions)" ;
    $header .=  ",Percentage of Actual Taxable Value of Property," ;
    $header .=  ",,Per Capita General Obligations";

	echo $header . "\n";

    $count = 1;
    foreach( $node->data as $row){
        $dollar_sign = ($count == 1) ? '$':'';
        $percent_sign = ($count == 1) ? '%':'';

        $rowString = $row['fiscal_year'] ;
        $rowString .= ','.$dollar_sign.',' . '"'. number_format($row['general_obligation_bonds']) .'"';
        $rowString .= ',' . '"'. number_format($row['percentage_atcual_taxable_property'], 2).'"'.','.$percent_sign;
        $rowString .= ','.$dollar_sign.',' . '"'. number_format($row['per_capita_general_obligations']).'"';

        echo $rowString . "\n";
        $count++;
   	}
echo "\n\n"."Sources: Comprehensive Annual Financial Reports of the Comptroller\n\n";
?>

