<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php  
echo eval($node->widgetConfig->header);  
?>

<a class="trends-export" href="/export/download/trends_ratios_of_general_bonded_debt_csv?dataUrl=/node/<?php echo $node->nid ?>">Export</a>

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
        <th class="number"><div class="trendCen" >General<br>Obligation<br>Bonds<br/>(in millions)</div></th>
        <th class="number"><div class="trendCen" >Percentage of<br>Actual Taxable<br>Value of Property</div></th>
        <th class="number"><div class="trendCen" >Per<br>Capita<br/>General<br>Obligations</div></th>
    </tr>
    </thead>

    <tbody>

    <?php   $count = 1;
    		foreach( $node->data as $row){
                $dollar_sign = ($count == 1) ? '<div class="dollarItem" >$</div>':'';
                $percent_sign = ($count == 1) ? '<span class="endItem">%</span>' : '<span class="endItem" style="visibility:hidden;">%</span>';

			    echo "<tr><td class='number bonded'><div class='tdCen'>" . $row['fiscal_year'] . "</div></td>";
    			echo "<td class='number bonded'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['general_obligation_bonds']) . "</div></td>";
    			echo "<td class='number bonded'><div class='tdCen'>" . number_format($row['percentage_atcual_taxable_property'],2) . $percent_sign. "</div></td>";
    			echo "<td class='number bonded'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['per_capita_general_obligations']) . "</div></td>";
			    echo "</tr>";
                $count++;
    		}
    ?>

    </tbody>
</table>
    <div class='footnote'><p>Sources: Comprehensive Annual Financial Reports of the Comptroller</p></div>
<?php 
	widget_data_tables_add_js($node);
?>
