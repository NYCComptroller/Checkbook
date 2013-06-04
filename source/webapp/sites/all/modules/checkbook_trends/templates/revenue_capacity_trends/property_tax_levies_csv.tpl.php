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
    $header .=  ",,Taxes Levied for the Fiscal Year"  ;
    $header .=  ",,Collected Within the Fiscal Year of the Levy - Amount"  ;
    $header .=  ",Collected Within the Fiscal Year of the Levy - Percentage of Levy,"  ;
    $header .=  ",,Collected in Subsequent Years"  ;
    $header .=  ",,Non-Cash Liquidations and Adjustments to Levy(1)"  ;
    $header .=  ",,Total Collections and Adjustments to Date - Amount"  ;
    $header .=  ",Total Collections and Adjustments to Date - Percentage of Levy,"  ;
    $header .=  ",,".'"'."Remaining Uncollected July 1, 2011".'"'  ;
	echo $header . "\n";

    $count = 1;
    foreach( $node->data as $row){
        $dollar_sign = ($count == 1)?"$":"";
        $percent_sign = ($count == 1)?"%":"";

        $rowString = $row['fiscal_year'] ;
        $rowString .= ',' .$dollar_sign .',' . '"'. number_format($row['tax_levied']).'"';
        $rowString .= ',' .$dollar_sign .',' . '"'.number_format($row['amount']).'"';
        $rowString .= ',' . '"'.number_format($row['percentage_levy'],2).'"'. ',' . $percent_sign;
        $rowString .= ',' .$dollar_sign .',' . '"'.(($row['collected_subsequent_years']>0)?number_format($row['collected_subsequent_years']):'-').'"';
        $rowString .= ',' .$dollar_sign .',' .'"'. number_format($row['levy_non_cash_adjustments']).'"';
        $rowString .= ',' .$dollar_sign .',' . '"'.number_format($row['collected_amount']).'"';
        $rowString .= ',' . '"'.number_format($row['collected_percentage_levy'],2).'"'. ',' . $percent_sign;
        $rowString .= ',' .$dollar_sign .',' . '"'.number_format($row['uncollected_amount']).'"';

        echo $rowString . "\n";
        $count++;
   	}

   echo "\n".'"'."(1) Adjustments to Tax Levy are Non-Cash Liquidations and Cancellations of Real Property Tax and include School Tax Relief payments which are not included in the City Council Resolutions.".'"';
   echo "\n".'"'."SOURCES: Resolutions of the City Council and other Department of Finance reports.".'"';
?>

