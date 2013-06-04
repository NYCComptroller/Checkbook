<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php  
    $header .= ",,,,,,,,".'"'.'(dollars in millions)'.'"'.",,,,". "\n";
	$header .= 'Fiscal year';
    $header .=  ",,General Obligation Bonds"  ;
    $header .=  ",,Revenue Bonds"  ;
    $header .=  ",,ECF"  ;
    $header .=  ",,MAC Debt"  ;
    $header .=  ",,TFA"  ;
    $header .=  ",,TSASC Debt"  ;
    $header .=  ",,STAR"  ;
    $header .=  ",,FSC"  ;
    $header .=  ",,SFC Debt"  ;
    $header .=  ",,HYIC Bonds and Notes"  ;
    $header .=  ",,Capital Leases Obligations"  ;
    $header .=  ",,IDA Bonds"  ;
    $header .=  ",,Treasury Obligations"  ;
    $header .=  ",,Total Primary Government"  ;
	echo $header . "\n";

        $count = 1;
        foreach( $node->data as $row){
            $dollar_symbol = ($count ==1 )? '$':'';
            $count++;
        $rowString = $row['fiscal_year'] ;
        $rowString .= ','.$dollar_symbol.','  . '"' . (($row['general_obligation_bonds']>0)?number_format($row['general_obligation_bonds']):'-') . '"';
        $rowString .= ','.$dollar_symbol.','  . '"'. (($row['revenue_bonds']>0)?number_format($row['revenue_bonds']):'-') . '"';
        $rowString .= ','.$dollar_symbol.','  . '"'. (($row['ecf']>0)?number_format($row['ecf']):'-') . '"';
        $rowString .= ','.$dollar_symbol.','  . '"'. (($row['mac_debt']>0)?number_format($row['mac_debt']):'-') . '"';
        $rowString .= ','.$dollar_symbol.','  . '"'. (($row['tfa']>0)?number_format($row['tfa']):'-') . '"';
        $rowString .= ','.$dollar_symbol.','  . '"'. (($row['tsasc_debt']>0)?number_format($row['tsasc_debt']):'-') . '"';
        $rowString .= ','.$dollar_symbol.','  . '"'. (($row['star']>0)?number_format($row['star']):'-') . '"';
        $rowString .= ','.$dollar_symbol.','  . '"'. (($row['fsc']>0)?number_format($row['fsc']):'-') . '"';
        $rowString .= ','.$dollar_symbol.','  . '"'. (($row['sfc_debt']>0)?number_format($row['sfc_debt']):'-') . '"';
        $rowString .= ','.$dollar_symbol.','  . '"'. (($row['hyic_bonds_notes']>0)?number_format($row['hyic_bonds_notes']):'-') . '"';
        $rowString .= ','.$dollar_symbol.','  . '"'. (($row['capital_leases_obligations']>0)?number_format($row['capital_leases_obligations']):'-') . '"';
        $rowString .= ','.$dollar_symbol.','  . '"'. (($row['ida_bonds']>0)?number_format($row['ida_bonds']):'-') . '"';
        if($row['treasury_obligations'] < 0 )
            $rowString .= ','.$dollar_symbol.','  . '"'. "(" . number_format(abs($row['treasury_obligations'])) . ")" . '"';
        else if ($row['treasury_obligations'] == 0)
            $rowString .= ','.$dollar_symbol.',' .  "-";
        else
            $rowString .= ','.$dollar_symbol.','  . '"'. number_format($row['treasury_obligations']) . '"';

        $rowString .= ','.$dollar_symbol.','  . '"'. (($row['total_primary_government']>0)?number_format($row['total_primary_government']):'-') . '"';
        echo $rowString . "\n";
   	}

echo "\n"."\n".'"'."Sources: Comprehensive Annual Financial Reports of the Comptroller".'"';
echo "\n"."\n".'"'."Note: Gross Debt, Percentage of Personal Income and Per Capital Gross Debt columns had to be removed. The figures changed year by year and they would not match the figures shown when that years CAFR was released.".'"'
?>

