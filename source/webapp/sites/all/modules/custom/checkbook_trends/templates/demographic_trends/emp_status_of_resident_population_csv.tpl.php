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

$first_year = $node->data[0]['fiscal_year'];
$last_year = end($node->data)['fiscal_year'];
reset($node->data);

    $header = ",,,{$first_year}-{$last_year},,,"."\n";
    $header .= 'year';

    $header .=  ",New York City Employed - Civilian Labor Force (in thousands)" ;
    $header .=  ",New York City Unemployed (a) - Civilian Labor Force (in thousands)"  ;

    $header .=  ",New York City Unemployment Rate,";
    $header .=  ",United States Unemployment Rate,";

	echo $header . "\n";
    $count = 1;
    foreach( $node->data as $row){
        $percent_sign = ($count == 1) ? '%' : '';
        $rowString = $row['fiscal_year'] ;
        $rowString .= ','  . '"'. number_format($row['civilian_labor_force_new_york_city_employed']) . '"';
        $rowString .= ','  . '"'. number_format($row['civilian_labor_force_unemployed']) . '"';
        $rowString .= ','  . '"'. number_format($row['unemployment_rate_city_percent'],1) . '"'.','.$percent_sign;
        $rowString .= ','  . '"'. number_format($row['unemployment_rate_united_states_percent'],1) . '"'.','.$percent_sign;

        echo $rowString . "\n";
        $count++;
   	}
?>

"(a) Unemployed persons are all civilians who had no employment during the survey week, were available for work, except "
"for temporary illness, and had made efforts to find employment some time during the prior four weeks. This includes "
"persons who were waiting to be recalled to a job from which they were laid off or were waiting to report to a new job "
"within 30 days."

"Note: Employment and unemployment information is not seasonally adjusted."

"Sources: U.S. Department of Labor, Bureau of Labor Statistics, and Office of the Comptroller, Fiscal and Budget Studies."
