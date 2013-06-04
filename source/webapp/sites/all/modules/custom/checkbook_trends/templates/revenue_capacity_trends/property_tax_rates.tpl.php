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

<a class="trends-export" href="/export/download/trends_property_tax_rates_csv?dataUrl=/node/<?php echo $node->nid ?>"
   xmlns="http://www.w3.org/1999/html">Export</a>
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
            <th class="number"><div class="trendCen" >Basic<br>Rate</div></th>
            <th class="number"><div class="trendCen" >Obligation<br>Debt</div></th>
            <th class="number"><div class="trendCen" >Total<br>Direct</div></th>
        </tr>
    </thead>

    <tbody>

    <?php
            $count = 1;
    		foreach( $node->data as $row){
                $dollar_sign = ($count == 1)?'<div class="dollarItem" >$</div>':'';
			    echo "<tr><td class='number'><div class='tdCen'>" . $row['fiscal_year'] . "</div></td>";
    			echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['basic_rate'],2) . "</div></td>";
    			echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  number_format($row['obligation_debt'],2) . "</div></td>";
    			echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  number_format($row['total_direct'],2) . "</div></td>";
			    echo "</tr>";
                $count++;
    		}
    ?>

    </tbody>
</table>
<div class="footnote">
    <p>Note: Property tax rate based on every $100 of assessed valuations.</p>
    <p>SOURCE: Resolutions of the City Council.</p>

</div>
<?php 
	widget_data_tables_add_js($node);
?>
