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
echo eval($node->widgetConfig->header);  
?>

<a class="trends-export" href="/export/download/trends_ratios_of_general_bonded_debt_csv?dataUrl=/node/<?php echo $node->nid ?>">Export</a>
<h5>(DOLLARS IN MILLIONS EXCEPT PER CAPITA)</h5>
<table id="table_<?php echo widget_unique_identifier($node) ?>" style='display:none' class="trendsShowOnLoad <?php echo $node->widgetConfig->html_class ?>">
    <?php
    if (isset($node->widgetConfig->caption_column)) {
        echo '<caption>' . $node->data[0][$node->widgetConfig->caption_column] . '</caption>';
    }
    else if (isset($node->widgetConfig->caption)) {
        echo '<caption>' . $node->widgetConfig->caption . '</caption>';
    }
    ?>
    <thead>
	<tr>
        <th class="number"><div class="trendCen" >Fiscal<br>Year</div></th>
        <th class="number"><div class="trendCen" >General<br>Bonded<br>Debt<br>(1)</div></th>
        <th class="number"><div class="trendCen" >Debt Secure<br>by Revenue<br>other than<br>property tax <br>(2) (3)</div></th>
        <th class="number"><div class="trendCen" >City Net<br />General<br />Obligation<br />Bonded Debt</div></th>
        <th class="number"><div class="trendCen" >City Net General<br />Obligation Bonded<br />Debt as a<br />Percentage of<br />Assessed Taxable<br />Value of Property<br />(4)</div></th>
        <th class="number"><div class="trendCen" >Per Capita <br>(5)</div></th>
        <th class="number"><div class="trendCen" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></th>
    </tr>
    </thead>

    <tbody>

    <?php   $count = 1;
    		foreach( $node->data as $row){
                $dollar_sign = ($count == 1) ? '<div class="dollarItem" >$</div>':'';
                $percent_sign = ($count == 1) ? '<span class="endItem">%</span>' : '<span class="endItem" style="visibility:hidden;">%</span>';

			    echo "<tr><td class='number bonded'><div class='tdCen'>" . $row['fiscal_year'] . "</div></td>";
                echo "<td class='number bonded' style='padding-left: 30px;'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['general_bonded_debt']) . "</div></td>";
                echo "<td class='number bonded'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['debt_by_revenue_ot_prop_tax']) . "</div></td>";
    			echo "<td class='number bonded'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['general_obligation_bonds']) . "</div></td>";
    			echo "<td class='number bonded' style='padding-right: 30px;'><div class='tdCen'>" . number_format($row['percentage_atcual_taxable_property'],2) . $percent_sign. "</div></td>";
    			echo "<td class='number bonded' style='padding-right: 20px;'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['per_capita_general_obligations']) . "</div></td>";
                echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
			    echo "</tr>";
                $count++;
    		}
    ?>

    </tbody>
</table>
    <div class='footnote'>
        <p>Sources: Comprehensive Annual Financial Reports of the Comptroller</p>
        <p>(1) See Notes to Financial Statements (Note D.5), "Changes in Long Term Liabilities" - Bonds and Notes Payable net of premium and discount.</p>
        <p>(2) Includes ECF, FSC, HYIC, IDA, STAR, TFA , NYCTLTs and TSASC.</p>
        <p>(3) See Exhibit "Pledged-Revenue Coverage", Part III- Statistical Information, CAFR</p>
        <p>(4) See Exhibit "Assessed Value and Estimated Actual Value of Taxable Property - Ten Year Trend",  Part III- Statistical Information, CAFR</p>
        <p>(5) See Exhibit "Population - Ten Year Trend", Part III- Statistical Information, CAFR</p>
    </div>
<?php 
	widget_data_tables_add_js($node);
