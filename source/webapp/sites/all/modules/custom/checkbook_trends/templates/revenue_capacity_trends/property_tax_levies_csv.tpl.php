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

    $last_year = end($node->data)['fiscal_year'];
    reset($node->data);

	$header = 'Fiscal year';
    $header .=  ",Taxes Levied for the Fiscal Year"  ;
    $header .=  ",Collected Within the Fiscal Year of the Levy - Amount"  ;
    $header .=  ",Collected Within the Fiscal Year of the Levy - Percentage of Levy,"  ;
    $header .=  ",Collected Within the Fiscal Year of the Levy - Collected in Subsequent Years"  ;
    $header .=  ",Non-Cash Liquidations and Adjustments to Levy(1)"  ;
    $header .=  ",Total Collections and Adjustments to Date - Amount"  ;
    $header .=  ",Total Collections and Adjustments to Date - Percentage of Levy,"  ;
    $header .=  ",".'"'."Remaining Uncollected JUNE 30, {$last_year}".'"'  ;
	echo $header . "\n";

    $count = 1;
    foreach( $node->data as $row){
        $dollar_sign = ($count == 1)?"$":"";
        $percent_sign = ($count == 1)?"%":"";

        $rowString = $row['fiscal_year'] ;
        $rowString .= ',' . '"'. number_format($row['tax_levied']).'"';
        $rowString .= ',' . '"'.number_format($row['amount']).'"';
        $rowString .= ',' . '"'.number_format($row['percentage_levy'],2).'"'. ',' . $percent_sign;
        $rowString .= ',' . '"'.(($row['collected_subsequent_years']>0)?number_format($row['collected_subsequent_years']):'-').'"';
        $rowString .= ',' .'"'. number_format($row['levy_non_cash_adjustments']).'"';
        $rowString .= ',' . '"'.number_format($row['collected_amount']).'"';
        $rowString .= ',' . '"'.number_format($row['collected_percentage_levy'],2).'"'. ',' . $percent_sign;
        $rowString .= ',' . '"'.number_format($row['uncollected_amount']).'"';

        echo $rowString . "\n";
        $count++;
   	}

   echo "\n".'"'."(1) Adjustments to Tax Levy are Non-Cash Liquidations and Cancellations of Real Property Tax and include School Tax Relief payments which are not included in the City Council Resolutions.".'"';
   echo "\n".'"'."SOURCES: Resolutions of the City Council and other Department of Finance reports.".'"';


