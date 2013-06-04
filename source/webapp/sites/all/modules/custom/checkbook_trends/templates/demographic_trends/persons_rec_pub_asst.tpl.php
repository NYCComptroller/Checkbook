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

<a class="trends-export" href="/export/download/trends_persons_rec_pub_asst_csv?dataUrl=/node/<?php echo $node->nid ?>">Export</a>

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
    <tr class="first-row">
        <th colspan="3" class="centrig bb"><div>2002-2011<br>(annual averages in thousands)</div></th>
    </tr>
	<tr class="second-row">
        <th class="number" ><div class="trendCen">Year</div></th>
        <th class="number" ><div class="trendCen">Public<br>Assistance</div></th>
        <th class="number" ><div class="trendCen">SSI<sup>(a)</sup></div></th>
    </tr>
    </thead>

    <tbody>

    <?php 
    		foreach( $node->data as $row){
			    echo "<tr><td class='number'><div  class='tdCen'>" . $row['fiscal_year'] . "</div></td>";
    			echo "<td class='number'><div  class='tdCen'>" . number_format($row['public_assistance']) . "</div></td>";
    			echo "<td class='number'><div  class='tdCen'>" . (($row['ssi'])?number_format($row['ssi']) : 'NA') . "</div></td>";
			    echo "</tr>";
    		}
    ?>

    </tbody>
</table>
    <div class="footnote"><p>(A) The SSI data is for December of each year.</p>
          <p>NA: Not Available.</p>
          <p>Sources: The City of New York, Human Resources Administration and the U.S. Social Security Administration.</p>
    </div>
<?php 
	widget_data_tables_add_js($node);
?>
