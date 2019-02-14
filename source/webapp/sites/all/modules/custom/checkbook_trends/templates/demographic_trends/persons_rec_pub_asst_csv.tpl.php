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

	echo "\n".'2002-'.$last_year.' (Average Annual Recipients)'."\n"."\n";
    $header = 'Year';
    $header .=  ",Public Assistance (in thousands)" ;
    $header .=  ",SSI(a)" ;

	echo $header . "\n";


    foreach( $node->data as $row){
        $rowString = $row['fiscal_year'] ;
        $rowString .= ','  . '"'. $row['public_assistance'] . '"';
        $rowString .= ','  . '"'. (($row['ssi']>0)?number_format($row['ssi']) : 'NA') . '"';

        echo $rowString . "\n";
   	}

    echo "\n"."(a) The SSI data is for December of each year."."\n"."\n".
           "NA: Not Available."."\n"."\n".
           '"'. "Sources: The City of New York, Human Resources Administration and the U.S. Social Security Administration.".'"';
