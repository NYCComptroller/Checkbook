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


    $header .= ",,,2000-{$last_year}*,,,\n";
    $header .= 'Year';
    $header .=  ",United States" ;
    $header .=  ",Percentage Change from Prior Period," ;
    $header .=  ",City of New York";
    $header .=  ",Percentage Change from Prior Period,";

	echo $header . "\n";

    $count = 1;
    foreach($node->data as $row){
        $percent_sign = ($count == 1 ) ? '%' : '';
        $rowString = $row['fiscal_year'] ;
        $rowString .= ',' . '"' . (($row['united_states']>0)?number_format($row['united_states']):' - ') .'"';
        $rowString .= ',' . '"' . (($row['percentage_change_from_prior_period']>0)?number_format($row['percentage_change_from_prior_period'],2):' - ') .'"'.','.$percent_sign;
        $rowString .= ',' . '"' . (($row['city_of_new_york']>0)?number_format($row['city_of_new_york']):' - ') .'"';
        $rowString .= ',' . '"' . (($row['percentage_change_prior_period']!=0)?number_format($row['percentage_change_prior_period'],2):' - ').'"'.','.$percent_sign;

        echo $rowString . "\n";
        $count++;
   	}
?>

"*Amounts as of March 28, <?= $last_year ?>"

"Source: U.S Department of Commerce, Bureau of Economic Analysis. US Census Bureau and American Fact Finder."
