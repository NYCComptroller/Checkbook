<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php  
	$table_rows = array();
	$years = array();
foreach( $node->data as $row){

	$table_rows[$row['display_order']]['category'] =  $row['category'];
	$table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
	$table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
	$table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
	$table_rows[$row['display_order']][$row['fiscal_year']]['assesed_value_million_amount'] = $row['assesed_value_million_amount'];
	$table_rows[$row['display_order']][$row['fiscal_year']]['percentage_taxable_real_estate'] = $row['percentage_taxable_real_estate'];
	$table_rows[$row['display_order']][$row['fiscal_year']]['direct_tax_rate'] = $row['direct_tax_rate'];
	$years[$row['fiscal_year']] = 	$row['fiscal_year'];
}
rsort($years);
	$header = 'Type of Property';
    foreach ($years as $year){
    	$header = $header .  ",,Fiscal Year" . $year . " - Assessed Value (in millions)";
    	$header = $header .  ",Fiscal Year" . $year . " - Percentage of Taxable Real Estate,";
    	$header = $header .  ",Fiscal Year" . $year . " - Direct Tax Rate,";
    }
	echo $header . "\n";

    $count = 1;
    foreach( $table_rows as $row){
        $dollar_sign = ($count == 2 || $count == count($table_rows))?"$":"";
        $percent_sign_1 = ($count == 2 || $count == count($table_rows))?"%":"";
        $percent_sign_2 = ($count == count($table_rows))?"%":"";
        $sup_script = ($row['amount_display_type'] == 'G') ? '(1)' : "";
        
        $rowString = '"'.$row['category'].'"' ;
        foreach ($years as $year){
            if(isset($row[$year]['assesed_value_million_amount'])){
                if($row[$year]['assesed_value_million_amount'] == -1)
                    $row[$year]['assesed_value_million_amount'] = ' - ';
                else
                    $row[$year]['assesed_value_million_amount'] = number_format($row[$year]['assesed_value_million_amount'], 1, '.',',');
            }else{
                $row[$year]['assesed_value_million_amount'] = '';
            }

            if(isset($row[$year]['percentage_taxable_real_estate'])){
                if($row[$year]['percentage_taxable_real_estate'] == -1)
                    $row[$year]['percentage_taxable_real_estate'] = ' - ';
                else
                    $row[$year]['percentage_taxable_real_estate'] = $row[$year]['percentage_taxable_real_estate'];
            }else{
                $row[$year]['percentage_taxable_real_estate'] = '';
            }

            if(isset($row[$year]['direct_tax_rate'])){
                if($row[$year]['direct_tax_rate'] == -1)
                    $row[$year]['direct_tax_rate'] = ' - ';
                else
                    $row[$year]['direct_tax_rate'] = $row[$year]['direct_tax_rate'];
            }else{
                $row[$year]['direct_tax_rate'] = '';
            }

             $rowString .= ',' . $dollar_sign . ',' .'"' . $row[$year]['assesed_value_million_amount'].'"';
			 $rowString .= ',' .$row[$year]['percentage_taxable_real_estate'].','.$percent_sign_1;
			 $rowString .= ',' .$row[$year]['direct_tax_rate'].$sup_script.','." ";
        }
        echo $rowString . "\n";
        $count++;
   	}

 echo "\n\n"." (1) Represents the weighted average of the four classes of real property."."\n".
    "Note: Property in New York City is reassessed once every year on average. The City assesses property at approximately 40 percent of Market Value for commercial and industrial property and 20 percent of Market Value for residential property."."\n".
'"'."SOURCES: Resolutions of the City Council and The Annual Report, The New York City Property Tax Fiscal Year 2011.".'"';
?>

