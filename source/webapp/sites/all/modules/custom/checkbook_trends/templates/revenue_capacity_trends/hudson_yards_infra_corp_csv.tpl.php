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
