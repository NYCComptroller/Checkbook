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

    $header .=  ",,Rental Revenue" ;
    $header .=  ",,Interest Revenue" ;
    $header .=  ",,Total Revenue";

    $header .=  ",,Debt Service - Interest";
    $header .=  ",,Debt Service - Principal";
    $header .=  ",,Debt Service - Total";
    $header .=  ",,Operating Expenses";
    $header .=  ",,Total to be Covered";
    $header .=  ",Coverage Ratio";

	echo $header . "\n";
    $count = 1;
    foreach( $node->data as $row){
        $dollar_sign = ($count == 1) ? '$' : '';
        $rowString = $row['fiscal_year'] ;
        $rowString .= ','.$dollar_sign.',"' . number_format($row['rental_revenue']).'"';
        $rowString .= ','.$dollar_sign.',"' . number_format($row['interest_revenue']).'"';
        $rowString .= ','.$dollar_sign.',"' . number_format($row['total_revenue']).'"';
        $rowString .= ','.$dollar_sign.',"' . number_format($row['interest']).'"';
        $rowString .= ','.$dollar_sign.',"' . number_format($row['pricipal']).'"';
        $rowString .= ','.$dollar_sign.',"' . number_format($row['total']).'"';
        $rowString .= ','.$dollar_sign.',"' . number_format($row['operating_expenses']).'"';
        $rowString .= ','.$dollar_sign.',"' . number_format($row['total_to_be_covered']).'"';
        $rowString .= ',' .  number_format($row['coverage_ratio'],2);

        echo $rowString . "\n";
        $count++;
   	}

echo "\n".'(*),"' ."The 2005A Bonds were issued on January 5, 2005 to refinance the 1994 Bonds.". '"' ."\n".
    ",".'"' ."The 2007A bonds were issued on January 18, 2007.".'"' ."\n".
    ",".'"' ."Capitalized interest of $1,037,000.00 was not included on interest expense for year 2009 for the 2007A Bonds.".'"' ."\n".
    ",".'"' ."The 2010A Bonds were issued on April 28, 2010 for capital purposes.".'"' ."\n".
    ",".'"' ."Capitalized interest of $1,969,000 was not included on interest expense for year 2010 for the 2007A Bonds and $289,000 was not included on interest expense for year 2010 for the 2010A Bonds.".'"' ."\n".
    ",".'"' ."The 2011A Bonds were issued on January 25, 2011 for capital purposes.".'"' ."\n".
    ",".'"' ."Capitalized interest of $1,936,000 was included on interest expense for year 2011 for the 2011 and 2010 Bonds.".'"' ."\n".
    '"' ."Source: New York City Educational Construction Fund".'"';

?>

