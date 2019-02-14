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

    $header .=  ",Class One" ;
    $header .=  ",Class Two" ;
    $header .=  ",Class Three";
    $header .=  ",Class Four";

    $header .=  ",Less Tax Exempt Property";
    $header .=  ",Total Taxable Assessed Value";
    $header .=  ",Total Direct Tax Rate(1)";
    $header .=  ",Estimated Actual Taxable Value";
    $header .=  ",Assessed Value as a Percentage of Actual Value,";
    $header .= "\n".",,,,,,,(AMOUNTS IN MILLIONS),,,,,";
	echo $header . "\n\n";

    $count = 1;
    foreach ($node->data as $row) {
        $dollar_sign = ($count == 1) ? '$' : '';
        $percent_sign = ($count == 1) ? '%' : '';

        $rowString = $row['fiscal_year'] ;
        $rowString .= ',' . '"' . number_format($row['class_one'],1,'.',',') . '"';
        $rowString .= ',' . '"' . number_format($row['class_two'],1,'.',',') . '"';
        $rowString .= ',' . '"' . number_format($row['class_three'],1,'.',',') . '"';
        $rowString .= ',' . '"' . number_format($row['class_four'],1,'.',',') . '"';
        $rowString .= ',' . '"' . number_format($row['less_tax_exempt_property'],1,'.',',') . '"';
        $rowString .= ',' . '"' . number_format($row['total_taxable_assesed_value'],1,'.',',') . '"';
        $rowString .= ',' . '"' . number_format($row['total_direct_tax_1'],2) . '"';
        $rowString .= ',' . '"' . number_format($row['estimated_actual_taxable_value'],1,'.',',') . '"';
        $rowString .= ',' . '"' . number_format($row['assesed_value_percentage'],2) .'"' .','.$percent_sign;

        echo $rowString . "\n";
        $count++;
   	}
?>


"(1) Property tax rate based on every $100 of assessed valuation"


"Notes:"

"The definitions of the four classes are as follows:"

" Class One - One, two, and three family homes; single family homes on cooperatively owned land.  Condominiums with no more "
"             than three dwelling units, provided such property was previously classified as Class One or no more than three stories in height "
"             and built as condominiums. Mixed-use property with three units or less, provided 50 percent or more of the space is used for "
"             residential purposes. Vacant land, primarily residentially zoned, except in Manhattan below 110th Street."

" Class Two - All other residential property not in Class One, except hotels and motels.  Mixed-use property with four or more units,"
"             provided 50 percent or more of the space is used for residential  purposes."

" Class Three - Utility real property owned by utility corporations, except land and buildings."

" Class Four -  All other real property."

"Property in New York City is reassessed every year. The City assesses property at approximately 40 percent of Market Value for"
" commercial and industrial property and 20 percent of Market Value for residential property."

"Sources: Resolutions of the City Council and The Annual Report of The New York City Property Tax Fiscal Year <?= $last_year ?>."
