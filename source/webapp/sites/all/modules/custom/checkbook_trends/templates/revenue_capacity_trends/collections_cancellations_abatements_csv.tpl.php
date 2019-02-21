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

    echo "\n".",,".'"'."Percent of Levy through June 30, {$last_year}".'"'."\n";
    $header = 'Fiscal year';
    $header .=  ",Tax Levy (in millions)(2)" ;
    $header .=  ",Collections,";
    $header .=  ",Cancellations,";
    $header .=  ",Abatements and Discounts(1),";
    $header .=  ",".'"'."Uncollected Balance June 30, {$last_year}".'"'.',';

	echo $header . "\n";

    $count = 1;
    foreach( $node->data as $row){
        $dollar_sign = ($count == 1) ? '$' : '';
        $percent_sign = ($count == 1) ? '%' : '';

        $rowString = $row['fiscal_year'] ;
        $rowString .= ',' . '"' .number_format($row['tax_levy'],1,'.',',').(($row['fiscal_year']=='2003')?'(2)':'') . '"';
        $rowString .= ',' . '"' . number_format($row['collection'],1) . '"'.','.$percent_sign;
        $rowString .= ',' . '"' . number_format($row['cancellations'],1) . '"'.','.$percent_sign;
        $rowString .= ',' . '"' . number_format($row['abatement_and_discounts_1'],1) . '"'.','.$percent_sign;
        $rowString .= ',' . '"' . number_format($row['uncollected_balance_percent'],1). '"'.','.$percent_sign;

        echo $rowString . "\n";
        $count++;
   	}

?>

"(1) Abatements and Discounts include SCRIE Abatements (Senior citizen rent increase exemption), J-51 Abatements,"
"Section 626 Abatements and other minor discounts offered by the City to property owners."

"(2) The Tax Levy amounts are the amount from the City Council Resolution. In 2005 an 18% surcharge was imposed"
"and is included in each year following."

"Notes: Total uncollected balance at June 30, <?= $last_year ?> less allowance for uncollectible amounts equals net realizable amount"
"(real estate taxes receivable)."

"Levy may total over 100 percent due to imposed charges that include ICIP deferred charges (Industrial and Commercial"
"Incentive Program), rebilling charges and other additional charges imposed by The Department of Finance(DOF). This"
"information is included in the FAIRTAX LEVY report."



