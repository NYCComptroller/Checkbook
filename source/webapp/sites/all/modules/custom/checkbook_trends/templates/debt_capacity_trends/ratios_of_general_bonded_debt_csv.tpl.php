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
    $header .=  ",General Bonded Debt (1)" ;
    $header .=  ",Debt Secure by Revenue other than property tax (2) (3)" ;
    $header .=  ",General Obligation Bonds" ;
    $header .=  ",Percentage of Actual Taxable Value of Property (4)," ;
    $header .=  ",Per Capita (5)";

	echo $header . "\n";

    $count = 1;
    foreach( $node->data as $row){
        $dollar_sign = ($count == 1) ? '$':'';
        $percent_sign = ($count == 1) ? '%':'';

        $rowString = $row['fiscal_year'] ;
        $rowString .= ',' . '"'. number_format($row['general_bonded_debt']) .'"';
        $rowString .= ',' . '"'. number_format($row['debt_by_revenue_ot_prop_tax']) .'"';
        $rowString .= ',' . '"'. number_format($row['general_obligation_bonds']) .'"';
        $rowString .= ',' . '"'. number_format($row['percentage_atcual_taxable_property'], 2).'"'.','.$percent_sign;
        $rowString .= ',' . '"'. number_format($row['per_capita_general_obligations']).'"';

        echo $rowString . "\n";
        $count++;
   	}
echo "\n"."SOURCES: Comprehensive Annual Financial Reports of the Comptroller" ."\n".
'"' ."(1) See Notes to Financial Statements (Note D.5), 'Changes in Long Term Liabilities'- Bonds and Notes Payable net of".'"' ."\n".
'"' ."premium and discount.".'"' ."\n".
'"' ."(2) Includes ECF, FSC, HYIC, IDA, STAR, TFA, NYCTLTs and TSASC.".'"' ."\n".
'"' ."(3) See Exhibit 'Pledged- Revenue Coverage', Part III-Statistical Information, CAFR".'"'."\n".
'"' ."(4) See Exhibit 'Assessed Value and Estimated Actual Value of Taxable Property-Ten Year Trend', Part III-Statistical".'"'."\n".
'"' ."Information, CAFR".'"'."\n".
'"' ."(5) See Exhibit 'Population-Ten Year Trend', Part III-Statistical Information, CAFR".'"';


?>

