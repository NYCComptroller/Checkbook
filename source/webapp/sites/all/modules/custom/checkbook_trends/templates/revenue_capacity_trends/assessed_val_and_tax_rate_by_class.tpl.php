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
$table_rows = array();
$years = array();

foreach( $node->data as $row){
	$length =  $row['indentation_level'];
	$spaceString = '&nbsp;';
	while($length > 0){
		$spaceString .= '&nbsp;';
		$length -=1;
	}
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
?>

<h3 xmlns="http://www.w3.org/1999/html"><?php echo $node->widgetConfig->table_title; ?></h3>

<a class="trends-export" href="/export/download/trends_tax_rate_by_class_csv?dataUrl=/node/<?php echo $node->nid ?>">Export</a>

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
            <th class="centrig"><div>&nbsp;</div></th>
            <?php
            foreach ($years as $year){
                if($year == 2014){
                    echo "<th rowspan='2'><div></div></th><th colspan=\"5\" class=\"centrig bb\"><div>Fiscal Year " . $year . " <sup>(3)</sup></div></th>";
                }
                else{
                    echo "<th rowspan='2'><div></div></th><th colspan=\"5\" class=\"centrig bb\"><div>Fiscal Year " . $year."</div></th>";
                }
                echo PHP_EOL;
            }
            ?>
            <th rowspan="2" >&nbsp;</th>
        </tr>
        <tr>
          <th class="text"><div>Type of Property</div></th>
            <?php foreach($years as $year){ ?>
                <th class="number"><div class="trendCen thAssess" >Assessed<br/>Value<br/>(in millions)</div></th>
                <th><div>&nbsp;</div></th><th class="number "><div class="trendCen thPercent" >Percentage<br>of Taxable<br>Real Estate</div></th>
                <th><div>&nbsp;</div></th><th class="number "><div class="trendCen thDirect" >Direct<br>Tax<br>Rate <sup>(2)</sup></div></th>
           <?php }?>
        </tr>
    </thead>

    <tbody>

    <?php
            $count = 1;
    		foreach( $table_rows as $row){
                $dollar_sign = ($count == 2 || $count == count($table_rows))?'<div class="dollarItem" >$</div>':'';
                $percent_sign_1 = ($count == 2 || $count == count($table_rows))?'<span class="endItem">%</span>' : '<span class="endItem" style="visibility:hidden;">%</span>';
                $percent_sign_2 = ($count == count($table_rows))?'<span class="endItem">%</span>' : '<span class="endItem" style="visibility:hidden;">%</span>';

    			$cat_class = "";
    			if( $row['highlight_yn'] == 'Y')
    				$cat_class = "highlight ";
    			$cat_class .= "level" . $row['indentation_level'];
    			$amount_class = "";
    			if($row['amount_display_type'])
    			    $amount_class = " amount-" . $row['amount_display_type'];

                $sup_script = ($row['amount_display_type'] == 'G') ? "<sup class='endItem'>(1)</sup>" : "<sup class='endItem' style='visibility: hidden;'>(1)</sup>";

                $amount_class .= ' number';
			    echo "<tr>
			    <td class='text " . $cat_class . "' ><div>" . (isset($row['category'])?$row['category']:'&nbsp;') . "</div></td>";
			    foreach ($years as $year){
                    if(isset($row[$year]['assesed_value_million_amount'])){
                        if($row[$year]['assesed_value_million_amount'] == -1)
                            $row[$year]['assesed_value_million_amount'] = ' - ';
                        else
                            $row[$year]['assesed_value_million_amount'] = number_format($row[$year]['assesed_value_million_amount'], 1, '.',',');
                    }else{
                        $row[$year]['assesed_value_million_amount'] = '&nbsp;';
                    }

                    if(isset($row[$year]['percentage_taxable_real_estate'])){
                        if($row[$year]['percentage_taxable_real_estate'] == -1)
                            $row[$year]['percentage_taxable_real_estate'] = ' - ';
                        else
                            $row[$year]['percentage_taxable_real_estate'] = $row[$year]['percentage_taxable_real_estate'];
                    }else{
                        $row[$year]['percentage_taxable_real_estate'] = '&nbsp;';
                    }

                    if(isset($row[$year]['direct_tax_rate'])){
                        if($row[$year]['direct_tax_rate'] == -1)
                            $row[$year]['direct_tax_rate'] = ' - ';
                        else
                            $row[$year]['direct_tax_rate'] = number_format($row[$year]['direct_tax_rate'],2);
                    }else{
                        $row[$year]['direct_tax_rate'] = '&nbsp;';
                    }

                    $sup_script2 = $sup_script;

			        echo "<td>$dollar_sign</td>"."<td class='" . $amount_class . " ' ><div class='tdCen assess'>" . $row[$year]['assesed_value_million_amount'] . "</div></td>";
			        echo "<td><div>&nbsp;</div></td>"."<td class='" . $amount_class . " ' ><div class='tdCen percent'>". $row[$year]['percentage_taxable_real_estate'] .$percent_sign_1."</div></td>";
			        echo "<td><div>&nbsp;</div></td>"."<td class='number $amount_class' ><div class='tdCen direct'>" . $row[$year]['direct_tax_rate'] . $sup_script2 ."</div></td>";
                }
                echo "<td>&nbsp;</td>";
			    echo "</tr>";

                $count++;
    		}
    ?>

    </tbody>
</table>

<?php
	widget_data_tables_add_js($node);

    if (isset($node->widgetConfig->table_footnote)) {
	    echo $node->widgetConfig->table_footnote;
	}
?>
<div class="footnote">
    <p>(1) Represents the weighted average of the four classes of real property.</p>
    <p>(2) Property tax rate based on every $100 assessed valuation.</p>
    <p>(3) In fiscal year 2014 The Annual Report, the New York City Property Tax Fiscal Year 2014, reported various classifications of
    condos as class four real property for the first time.</p>
    <p>Note: Property in New York City is reassessed once a year. The City assesses property at approximately 40
percent of Market Value for commercial and industrial property and 20 percent of Market Value for residential property.</p>
<p>Sources: Resolutions of the City Council and The Annual Report, The New York City Property Tax Fiscal Year <?= $last_year ?>. </p>
</div>

