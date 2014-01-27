<?php
/**
* This file is part of the Checkbook NYC financial transparency software.
* 
* Copyright (C) 2012, 2013 New York City
* 
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
* 
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
?>
<?php  
    $header .= ",,,,".'"'.'(AMOUNTS IN MILLIONS)'.'"'.",,,,". "\n";
	$header .= 'Fiscal year';
    $header .=  ",General Obligation Bonds"  ;
    $header .=  ",Revenue Bonds"  ;
    $header .=  ",ECF"  ;
    $header .=  ",MAC Debt"  ;
    $header .=  ",TFA"  ;
    $header .=  ",TSASC Debt"  ;
    $header .=  ",STAR"  ;
    $header .=  ",FSC"  ;
    $header .=  ",SFC Debt"  ;
    $header .=  ",HYIC Bonds and Notes"  ;
    $header .=  ",Capital Leases Obligations"  ;
    $header .=  ",IDA Bonds"  ;
    $header .=  ",Treasury Obligations"  ;
    $header .=  ",Total Primary Government"  ;
	echo $header . "\n";

        $count = 1;
        foreach( $node->data as $row){
            $dollar_symbol = ($count ==1 )? '$':'';
            $count++;
        $rowString = $row['fiscal_year'] ;
        $rowString .= ','  . '"' . (($row['general_obligation_bonds']>0)?number_format($row['general_obligation_bonds']):'-') . '"';
        $rowString .= ','  . '"'. (($row['revenue_bonds']>0)?number_format($row['revenue_bonds']):'-') . '"';
        $rowString .= ','  . '"'. (($row['ecf']>0)?number_format($row['ecf']):'-') . '"';
        $rowString .= ','  . '"'. (($row['mac_debt']>0)?number_format($row['mac_debt']):'-') . '"';
        $rowString .= ','  . '"'. (($row['tfa']>0)?number_format($row['tfa']):'-') . '"';
        $rowString .= ','  . '"'. (($row['tsasc_debt']>0)?number_format($row['tsasc_debt']):'-') . '"';
        $rowString .= ','  . '"'. (($row['star']>0)?number_format($row['star']):'-') . '"';
        $rowString .= ','  . '"'. (($row['fsc']>0)?number_format($row['fsc']):'-') . '"';
        $rowString .= ','  . '"'. (($row['sfc_debt']>0)?number_format($row['sfc_debt']):'-') . '"';
        $rowString .= ','  . '"'. (($row['hyic_bonds_notes']>0)?number_format($row['hyic_bonds_notes']):'-') . '"';
        $rowString .= ','  . '"'. (($row['capital_leases_obligations']>0)?number_format($row['capital_leases_obligations']):'-') . '"';
        $rowString .= ','  . '"'. (($row['ida_bonds']>0)?number_format($row['ida_bonds']):'-') . '"';
        if($row['treasury_obligations'] < 0 )
            $rowString .= ','  . '"'. "(" . number_format(abs($row['treasury_obligations'])) . ")" . '"';
        else if ($row['treasury_obligations'] == 0)
            $rowString .= ',' .  "-";
        else
            $rowString .= ','  . '"'. number_format($row['treasury_obligations']) . '"';

        $rowString .= ','  . '"'. (($row['total_primary_government']>0)?number_format($row['total_primary_government']):'-') . '"';
        echo $rowString . "\n";
   	}

echo "\n"."\n".'"'."Sources: Comprehensive Annual Financial Reports of the Comptroller".'"';
echo "\n"."\n".'"'."Note: Gross Debt, Percentage of Personal Income and Per Capital Gross Debt columns had to be removed. The figures changed year by year and they would not match the figures shown when that years CAFR was released.".'"'
?>

