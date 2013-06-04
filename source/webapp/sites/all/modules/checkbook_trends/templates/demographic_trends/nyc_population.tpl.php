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

<a class="trends-export" href="/export/download/trends_nyc_population_csv?dataUrl=/node/<?php echo $node->nid ?>">Export</a>

<table id="table_<?php echo widget_unique_identifier($node) ?>" class="<?php echo $node->widgetConfig->html_class ?>">
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
    	<th>&nbsp;</th>
    	<th colspan="4" class="centrig bb"><div>2000-2011<sup>*</sup></div></th>
    </tr>
	<tr>
        <th class="number" ><div class="trendCen">Year</div></th>
        <th class="number"><div class="trendCen">United States</div></th>
        <th class="number"><div class="trendCen">Percentage<br>Change from<br>Prior Period</div></th>
        <th class="number"><div class="trendCen">City of<br>New York</div></th>
        <th class="number"><div class="trendCen">Percentage<br>Change from<br>Prior Period</div></th>
    </tr>
    </thead>

    <tbody>

    <?php
            $count = 1;
    		foreach($node->data as $row){
                $percent_sign = ($count == 1 ) ? '<span class="endItem">%</span>' : '<span class="endItem" style="visibility:hidden;">%</span>';

			    echo "<tr>";
			    echo "<td class='number '><div  class='tdCen'>" . $row['fiscal_year'] . "</div></td>";
    			echo "<td class='number '><div  class='tdCen'>" . (($row['united_states']>0)?number_format($row['united_states']):' - ') . "</div></td>";
    			echo "<td class='number '><div  class='tdCen'>" . (($row['percentage_change_from_prior_period']>0)?(number_format($row['percentage_change_from_prior_period'],2)):' - ') . $percent_sign. "</div></td>";
    			echo "<td class='number '><div  class='tdCen'>" . (($row['city_of_new_york']>0)?number_format($row['city_of_new_york']):' - '). "</div></td>";
    			echo "<td class='number '><div  class='tdCen'>" . (($row['percentage_change_prior_period']>0)?(number_format($row['percentage_change_prior_period'],2)):' - '). $percent_sign. "</div></td>";
			    echo "</tr>";
                $count++;
    		}
    ?>

    </tbody>
</table>
    <div class="footnote">
         <p>Note: Data Not Available</p>
         <p>Source: U.S Department of Commerce,Bureau of Economic Analysis.</p>
    </div>
<?php 
	widget_data_tables_add_js($node);

    if (isset($node->widgetConfig->table_footnote)) {
	    echo $node->widgetConfig->table_footnote;
	}
?>
