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
    $header .=  ",Basic Rate (1)" ;
    $header .=  ",Obligation Debt" ;
    $header .=  ",Total Direct";
    
	echo $header . "\n";
    $count = 1;

    foreach( $node->data as $row){
        $dollar_sign = ($count == 1)?"$":"";
        $rowString = $row['fiscal_year'] ;
        $rowString .= ',' . $row['basic_rate'];
        $rowString .= ',' . $row['obligation_debt'];
        $rowString .= ',' . $row['total_direct'];

        echo $rowString . "\n";
        $count++;
   	}

  

echo "\n"."\n"."SOURCE: Resolutions of the City Council.";
echo "\n"."\n"."Note: (1) Property tax rate based on every $100 of assessed valuations.";

