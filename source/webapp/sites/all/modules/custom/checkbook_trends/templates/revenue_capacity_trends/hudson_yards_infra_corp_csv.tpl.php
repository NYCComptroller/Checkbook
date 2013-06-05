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

    $header = 'Fiscal year';

    $header .=  ",,DIB Revenue(1)";
    $header .=  ",,TEP Revenue(2)";
    $header .=  ",,ISP Revenue(3)";
    $header .=  ",,Other(4)";
    $header .=  ",,Investment Earnings";
    $header .=  ",,Total Revenue";
    $header .=  ",,Debt Service - Interest";
    $header .=  ",,Debt Service - Principal";
    $header .=  ",,Debt Service - Total";
    $header .=  ",,Operating Expenses";
    $header .=  ",,Total to be Covered";
    $header .=  ",Coverage on Total Revenue(5)";

	echo $header . "\n";

     $count = 1;
    foreach($node->data as $row){
        $dollar_sign = ($count == 1) ? '$':'';

        $rowString = $row['fiscal_year'] ;
        $rowString .= ',' . $dollar_sign. ',' .(($row['dib_revenue_1']>0)? ('"'.number_format($row['dib_revenue_1']).'"'):'-');
        $rowString .= ',' . $dollar_sign.',' .(($row['tep_revenue_2']>0)?('"'.number_format($row['tep_revenue_2']).'"'):'-');
        $rowString .= ',' . $dollar_sign.',' .(($row['isp_revenue_3']>0)?('"'.number_format($row['isp_revenue_3']).'"'):'-');
        $rowString .= ',' . $dollar_sign.',' .(($row['other_4']>0)?('"'.number_format($row['other_4']).'"'):'-');
        $rowString .= ',' . $dollar_sign.',' .(($row['investment_earnings']>0)?('"'.number_format($row['investment_earnings']).'"'):'-');
        $rowString .= ',' . $dollar_sign.',' .(($row['total_revenue']>0)?('"'.number_format($row['total_revenue']).'"'):'-');
        $rowString .= ',' . $dollar_sign.',' .(($row['interest']>0)?('"'.number_format($row['interest']).'"'):'-');
        $rowString .= ',' . $dollar_sign.',' .(($row['principal']>0)?('"'.number_format($row['principal']).'"'):'-');
        $rowString .= ',' . $dollar_sign.',' .(($row['total']>0)?('"'.number_format($row['total']).'"'):'-');
        $rowString .= ',' . $dollar_sign.',' .(($row['operating_expenses']>0)?('"'.number_format($row['operating_expenses']).'"'):'-');
        $rowString .= ',' . $dollar_sign.',' .(($row['total_to_be_covered']>0)?('"'.number_format($row['total_to_be_covered']).'"'):'-');
        $rowString .= ',' . $row['coverage_on_total_revenue_5'].(($row['fiscal_year'] == '2009' || $row['fiscal_year'] == '2010' || $row['fiscal_year'] == '2011')?'(6)':'');

        echo $rowString . "\n";
        $count++;
   	}

    echo "\n".'"'."(*) Date of inception of Hudson Yards Infrastructure Corporation was August 19, 2004.".'"'
        ."\n".'"'."HYIC first DIB collection was on September 21, 2005 and issued its first bonds on December 21, 2006.".'"'
        ."\n".'"'."(1) District Improvement Bonuses (DIB)".'"'
        ."\n".'"'."(2) Property Tax Equivalency Payments (TEP)".'"'
        ."\n".'"'."(3) Interest Support Payments (ISP)".'"'
        ."\n".'"'."(4) Grant from City".'"'
        ."\n".'"'."(5) ISPs are to be made by the City under the terms of Support and Development Agreement, which obligates the City to pay HYIC, subject to annual appropriation, an ISP amount equal to the difference between the amount of funds available to HYIC to pay interest on its current outstanding bonds and the amount of interest due on such bonds.".'"'
        ."\n".'"'."(6) Debt service payments are funded from excess prior years' revenues and from current year revenues.".'"'
        ."\n\n".'"'."Source: Hudson Yards Infrastructure Corporation".'"';
?>
