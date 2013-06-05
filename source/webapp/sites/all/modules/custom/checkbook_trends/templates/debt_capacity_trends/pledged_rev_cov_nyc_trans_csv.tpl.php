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

   echo ",,,,,,New York City Transitional Finance Authority". "\n";
   echo ",,,,,,(in thousands)". "\n";

	$header = 'Fiscal year';
    $header .=  ",,PIT Revenue(1)"  ;
    $header .=  ",,Sales Tax Revenue(2)"  ;
    $header .=  ",,Total Receipt"  ;
    $header .=  ",,Other(3)"  ;
    $header .=  ",,Investment Earnings"  ;
    $header .=  ",,Total Revenue"  ;
    $header .=  ",,Future Tax Secured Bonds Debt Service - Interest"  ;
    $header .=  ",,Future Tax Secured Bonds Debt Service - Principal"  ;
    $header .=  ",,Future Tax Secured Bonds Debt Service - Total"  ;
    $header .=  ",,Operating Expenses"  ;
    $header .=  ",,Total to be Covered"  ;

	echo $header . "\n";

    $count = 1;
    foreach( $node->data as $row){
        $dollar_sign = ($count == 1)? '$':'';
        $count++;
        $rowString = $row['fiscal_year'] ;
        $rowString .= ','.$dollar_sign . ','.'"'. (($row['pit_revenue']>0)?number_format($row['pit_revenue']):'-').'"';
        $rowString .= ','.$dollar_sign .','.'"'. (($row['sales_tax_revenue']>0)?number_format($row['sales_tax_revenue']):'-').'"';
        $rowString .= ','.$dollar_sign .','.'"'. (($row['total_receipt']>0)?number_format($row['total_receipt']):'-').'"';
        $rowString .= ','.$dollar_sign .','.'"'. (($row['other']>0)?number_format($row['other']):'-').'"';
        $rowString .= ','.$dollar_sign .','.'"'. (($row['investment_earnings']>0)?number_format($row['investment_earnings']):'-').'"';
        $rowString .= ','.$dollar_sign .','.'"' . (($row['total_revenue']>0)?number_format($row['total_revenue']):'-').'"';
        $rowString .= ','.$dollar_sign .','.'"' . (($row['interest']>0)?number_format($row['interest']):'-').'"';
        $rowString .= ','.$dollar_sign .','.'"' . (($row['pricipal']>0)?number_format($row['pricipal']):'-').'"';
        $rowString .= ','.$dollar_sign .','.'"' . (($row['total']>0)?number_format($row['total']):'-').'"';
        $rowString .= ','.$dollar_sign .','.'"' . (($row['operating_expenses']>0)?number_format($row['operating_expenses']):'-').'"';
        $rowString .= ','.$dollar_sign .','.'"' . (($row['total_to_be_covered']>0)?number_format($row['total_to_be_covered']):'-').'"';
    			        
        echo $rowString . "\n";
   	}

echo "\n"."\n".'"'."(1) Personal income tax (PIT).".'"'
    ."\n".'"'."(2) Sales tax revenue has not been required by the TFA. This amount is available to cover debt service if required.".'"'
    ."\n".'"'."(3) Grant from City and Federal Subsidy.".'"';
?>

