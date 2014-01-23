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
	$header = 'Fiscal Year';

    $header .=  ",General Obligation Bonds (in millions)" ;
    $header .=  ",Percentage of Actual Taxable Value of Property," ;
    $header .=  ",Per Capita General Obligations";

	echo $header . "\n";

    $count = 1;
    foreach( $node->data as $row){
        $dollar_sign = ($count == 1) ? '$':'';
        $percent_sign = ($count == 1) ? '%':'';

        $rowString = $row['fiscal_year'] ;
        $rowString .= ',' . '"'. number_format($row['general_obligation_bonds']) .'"';
        $rowString .= ',' . '"'. number_format($row['percentage_atcual_taxable_property'], 2).'"'.','.$percent_sign;
        $rowString .= ',' . '"'. number_format($row['per_capita_general_obligations']).'"';

        echo $rowString . "\n";
        $count++;
   	}
echo "\n\n"."Sources: Comprehensive Annual Financial Reports of the Comptroller\n\n";
?>

