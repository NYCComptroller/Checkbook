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

	$header = 'Fiscal year';

    $header .=  ",Rental Revenue" ;
    $header .=  ",Interest Revenue" ;
    $header .=  ",Total Revenue";

    $header .=  ",Debt Service - Interest";
    $header .=  ",Debt Service - Principal";
    $header .=  ",Debt Service - Total";
    $header .=  ",Operating Expenses";
    $header .=  ",Total to be Covered";
    $header .=  ",Coverage Ratio";

	echo $header . "\n";
	echo "(AMOUNTS IN THOUSANDS)" . "\n";
		
    $count = 1;
    foreach( $node->data as $row){
        $dollar_sign = ($count == 1) ? '$' : '';
        $rowString = $row['fiscal_year'] ;
        $rowString .= ',"' . number_format($row['rental_revenue']).'"';
        $rowString .= ',"' . number_format($row['interest_revenue']).'"';
        $rowString .= ',"' . number_format($row['total_revenue']).'"';
        $rowString .= ',"' . number_format($row['interest']).'"';
        $rowString .= ',"' . number_format($row['pricipal']).'"';
        $rowString .= ',"' . number_format($row['total']).'"';
        $rowString .= ',"' . number_format($row['operating_expenses']).'"';
        $rowString .= ',"' . number_format($row['total_to_be_covered']).'"';
        $rowString .= ',' .  number_format($row['coverage_ratio'],2);

        echo $rowString . "\n";
        $count++;
   	}

echo "\n".'(*),"' ."Interest of 8,919,000 was capitalized during Fiscal Year 2013 construction for year 2011 and 2010 bonds.". '"' ."\n".
     '"' ."In Fiscal Year 2014 ECF received $7 million in income for option for E. 57th development to extend lease beyond 99 years.".'"' ."\n".
     '"' ."Operating Expenses exclude Post Employment Benefits accrual.".'"' ."\n".
     '"' ."Principal in Fiscal Year 2016 does not include the redemption amount  of the 2005 bonds on October 1, 2015.".'"' ."\n".
     '"' ."In FY 2017 ECF received a $10 million Participation payment from E57th Street initial condo sales by the developer.".'"' ."\n".
     '"' ."Source: New York City Educational Construction Fund".'"';