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

    $header .=  ",DIB Revenue(1)";
    $header .=  ",TEP Revenue(2)";
    $header .=  ",ISP Revenue(3)";
    $header .=  ",PILOMRT(4)";
    $header .=  ",PILOT(5)";
    $header .=  ",Other(6)";
    $header .=  ",Investment Earnings";
    $header .=  ",Total Revenue";
    $header .=  ",Debt Service - Interest";
    $header .=  ",Debt Service - Principal";
    $header .=  ",Debt Service - Total";
    $header .=  ",Operating Expenses";
    $header .=  ",Total to be Covered";
    $header .=  ",Coverage on Total Revenue(7)(8)";

	echo $header . "\n";
	echo "(AMOUNTS IN THOUSANDS)" . "\n";
	

     $count = 1;
    foreach($node->data as $row){
        $dollar_sign = ($count == 1) ? '$':'';

        $rowString = $row['fiscal_year'] ;
        $rowString .=  ',' .(($row['dib_revenue_1']>0)? ('"'.number_format($row['dib_revenue_1']).'"'):'-');
        $rowString .= ',' .(($row['tep_revenue_2']>0)?('"'.number_format($row['tep_revenue_2']).'"'):'-');
        $rowString .= ',' .(($row['isp_revenue_3']>0)?('"'.number_format($row['isp_revenue_3']).'"'):'-');
        $rowString .= ',' .(($row['pilomrt_payment']>0)?('"'.number_format($row['pilomrt_payment']).'"'):'-');
        $rowString .= ',' .(($row['pilot']>0)?('"'.number_format($row['pilot']).'"'):'-');
        $rowString .= ',' .(($row['other_4']>0)?('"'.number_format($row['other_4']).'"'):'-');
        $rowString .= ',' .(($row['investment_earnings']>0)?('"'.number_format($row['investment_earnings']).'"'):'-');
        $rowString .= ',' .(($row['total_revenue']>0)?('"'.number_format($row['total_revenue']).'"'):'-');
        $rowString .= ',' .(($row['interest']>0)?('"'.number_format($row['interest']).'"'):'-');
        $rowString .= ',' .(($row['principal']>0)?('"'.number_format($row['principal']).'"'):'-');
        $rowString .= ',' .(($row['total']>0)?('"'.number_format($row['total']).'"'):'-');
        //$rowString .= ',' .(($row['operating_expenses']>0)?('"'.number_format($row['operating_expenses']).'"'):'-');
        $rowString .= ',' .(($row['operating_expenses']>0)?('"'.number_format($row['operating_expenses']).'"'):'-') . ((  $row['fiscal_year'] == '2012')? '(9)':'');
        $rowString .= ',' .(($row['total_to_be_covered']>0)?('"'.number_format($row['total_to_be_covered']).'"'):'-');
        $rowString .= ',' . $row['coverage_on_total_revenue_5'].(in_array($row['fiscal_year'], ['2009','2010','2011','2012'])? '(6)':'');

        echo $rowString . "\n";
        $count++;
   	}
?>


"HYIC issued its first bonds on December 21, 2006"

"(1) District Improvement Bonuses (DIB)"
"(2) Property Tax Equivalency Payments (TEP)"
"(3) Interest Support Payments (ISP)"
"(4) Payments in Lieu of the Mortgage Recording Tax (PILOMRT)"
"(5) Payments in Lieu of Real Estate Tax (PILOT)"
"(6) Grant from City"
"(7) ISPs are to be made by the City under the terms of Support and Development Agreement, which obligates the City to pay HYIC, subject to annual appropriation, an ISP amount equal to the difference between the amount of funds available to HYIC to pay interest on its current outstanding bonds and the amount of interest due on such bonds."
"(8) Debt service payments are funded from excess prior years' revenues and from current year revenues."
"(9) In December 2011, HYIC was obligated to make an arbitrage rebate payment to United States Treasury for $8.8M "

"Source: Hudson Yards Infrastructure Corporation"
