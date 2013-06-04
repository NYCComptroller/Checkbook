<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php
    $header = ",,,1996-2010,,,"."\n";
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

echo "\n" .'"'."(a)  Unemployed persons are all civilians who had no employment during the survey week, were available for work, except for temporary illness, and had made efforts to find employment some time during the prior four weeks. This includes persons who were waiting to be recalled to a job from which they were laid off or were waiting to report to a new job within 30 days.".'"'."\n".
     "\n" .'"'."Note: Employment and unemployment information is not seasonally adjusted.".'"'."\n".
     "\n" .'"'."Sources: U.S. Department of Labor, Bureau of Labor Statistics, and Office of the Comptroller, Fiscal and Budget Studies.".'"'."\n";
?>

