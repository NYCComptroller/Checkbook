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
$last_year = $years[0];

	$header = 'Type of Property';
    foreach ($years as $year){
        if($year == 2014){
            $header = $header .  ",Fiscal Year" . $year . " (3) - Assessed Value (in millions)";
            $header = $header .  ",Fiscal Year" . $year . " (3) - Percentage of Taxable Real Estate,";
            $header = $header .  ",Fiscal Year" . $year . " (3) - Direct Tax Rate (2)";
        }
        else{
            $header = $header .  ",Fiscal Year" . $year . " - Assessed Value (in millions)";
            $header = $header .  ",Fiscal Year" . $year . " - Percentage of Taxable Real Estate,";
            $header = $header .  ",Fiscal Year" . $year . " - Direct Tax Rate (2)";
        }
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

             $rowString .=  ',' .'"' . $row[$year]['assesed_value_million_amount'].'"';
			 $rowString .= ',' .$row[$year]['percentage_taxable_real_estate'].','.$percent_sign_1;
			 //$rowString .= ',' .$row[$year]['direct_tax_rate'].$sup_script.''." ";
			 $rowString .= ',' .$row[$year]['direct_tax_rate'].$sup_script.''." ";

        }
        echo $rowString . "\n";
        $count++;
   	}
?>


"(1) Represents the weighted average of the four classes of real property."

"(2) Property tax rate based on every $100 assessed valuation."

"(3) In fiscal year 2014 The Annual Report, the New York City Property Tax Fiscal Year 2014, reported various "
"    classifications of condos as class four real property for the first time."

"Note: Property in New York City is reassessed once a year. The City assesses property at approximately 40 percent of "
"      Market Value for commercial and industrial property and 20 percent of Market Value for residential property."

"Sources: Resolutions of the City Council and The Annual Report, The New York City Property Tax Fiscal Year <?= $last_year ?>. "
