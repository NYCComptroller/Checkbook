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

    $header .=  ",,Class One" ;
    $header .=  ",,Class Two" ;
    $header .=  ",,Class Three";
    $header .=  ",,Class Four";

    $header .=  ",,Less Tax Exempt Property";
    $header .=  ",,Total Taxable Assessed Value";
    $header .=  ",,Total Direct Tax Rate(1)";
    $header .=  ",,Estimated Actual Taxable Value";
    $header .=  ",Assessed Value as a Percentage of Actual Value,";
    $header .= "\n".",,,,,,,,,,,(in millions),,,,,,,,";
	echo $header . "\n\n";

    $count = 1;
    foreach ($node->data as $row) {
        $dollar_sign = ($count == 1) ? '$' : '';
        $percent_sign = ($count == 1) ? '%' : '';

        $rowString = $row['fiscal_year'] ;
        $rowString .= ','.$dollar_sign.',' . '"' . number_format($row['class_one'],1,'.',',') . '"';
        $rowString .= ','.$dollar_sign.',' . '"' . number_format($row['class_two'],1,'.',',') . '"';
        $rowString .= ','.$dollar_sign.',' . '"' . number_format($row['class_three'],1,'.',',') . '"';
        $rowString .= ','.$dollar_sign.',' . '"' . number_format($row['class_four'],1,'.',',') . '"';
        $rowString .= ','.$dollar_sign.',' . '"' . number_format($row['less_tax_exempt_property'],1,'.',',') . '"';
        $rowString .= ','.$dollar_sign.',' . '"' . number_format($row['total_taxable_assesed_value'],1,'.',',') . '"';
        $rowString .= ','.$dollar_sign.',' . '"' . number_format($row['total_direct_tax_1'],2) . '"';
        $rowString .= ','.$dollar_sign.',' . '"' . number_format($row['estimated_actual_taxable_value'],1,'.',',') . '"';
        $rowString .= ',' . '"' . number_format($row['assesed_value_percentage'],2) .'"' .','.$percent_sign;

        echo $rowString . "\n";
        $count++;
   	}

    echo "\n"."(1) Property tax rate based on every $100 of assessed valuation".
         "\n\n"."Notes:".
         "\n\n"."The definitions of the four classes are as follows:".
         "\n"."   Class One -  ,". '"'
         ."One, two, and three family homes; single family homes on cooperatively owned land.  Condominiums with no more than three dwelling units, provided such property was previously classified as
         Class One or no more than three stories in height and built as condominiums.
         Mixed-use property with three units or less, provided 50 percent or more of the space is used for residential purposes.
         Vacant land, primarily residentially zoned, except in Manhattan below 110th Street.".'"'.
         "\n"."   Class Two -  ,". '"'.
         "All other residential property not in Class One, except hotels and motels.  Mixed-use property with four or more units, provided 50 percent or more of the space is used for residential  purposes.".'"'.
         "\n"."   Class Three -  ,". '"'."Utility real property owned by utility corporations, except land and buildings.".'"'.
         "\n"."   Class Four -  ,". '"'."All other real property.".'"'.
         "\n\n"."Classes One to Four amounts include Tax Exempt Property.".
         "\n\n"."Property in New York City is reassessed every year. The City assesses property at approximately 40 percent of Market Value for
commercial and industrial property and 20 percent of Market Value for residential property.".
         "\n\n"."SOURCES: Resolutions of the City Council and The Annual Report of The New York City Property Tax Fiscal Year 2011.";

?>

