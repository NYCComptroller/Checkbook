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

<a class="trends-export" href="/export/download/trends_emp_status_of_resident_population_csv?dataUrl=/node/<?php echo $node->nid ?>">Export</a>

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
    	<th rowspan="3" class="number"><div class="trendCen">Year</div></th>
    	<th class="centrig bb" colspan="5"><div>1996-2010</div></th>
    </tr>
    <tr>

        <th colspan="2" class="centrig bb"><div>Civilian Labor Force<br>(in thousands)</div></th>
        <th rowspan="2"><div class="">&nbsp;</div></th>
        <th colspan="2" class="centrig bb"><div>Unemployment Rate</div></th>
    </tr>
	<tr>
		
        <th class="number" ><div class="trendCen">New York City<br>Employed</div></th>
        <th class="number" ><div class="trendCen">New York City<br>Unemployed<sup>(a)</sup></div></th>
        <th class="number" ><div class="trendCen">New York<br>City</div></th>
        <th class="number" ><div class="trendCen">United<br>States</div></th>
    </tr>    
    </thead>

    <tbody>

    <?php
            $count = 1;
    		foreach( $node->data as $row){
                $percent_sign = ($count == 1) ? '<span class="endItem">%</span>' : '<span class="endItem" style="visibility:hidden;">%</span>';
			    echo "<tr><td class='number'><div  class='tdCen'>" . $row['fiscal_year'] . "</div></td>";
    			echo "<td class='number'><div  class='tdCen'>" . number_format($row['civilian_labor_force_new_york_city_employed']) . "</div></td>";
    			echo "<td class='number'><div  class='tdCen'>" . number_format($row['civilian_labor_force_unemployed']) . "</div></td>";
                echo "<td><div>&nbsp;</div></td>";
    			echo "<td class='number'><div  class='tdCen'>" . number_format($row['unemployment_rate_city_percent'],1) . $percent_sign ."</div></td>";
    			echo "<td class='number'><div class='tdCen'>" . number_format($row['unemployment_rate_united_states_percent'],1) . $percent_sign. "</div></td>";
			    echo "</tr>";
                $count++;
    		}
    ?>

    </tbody>
</table>
<div class="footnote">
  <table>
    <tbody>
    <tr>
      <td>(A)</td>
      <td>Unemployed persons are all civilians who had no employment during the survey week, were available for work, except for temporary illness, and had made efforts to find employment some time during the prior four weeks.
        This includes persons who were waiting to be recalled to a job from which they were laid off or were waiting to report to a new job within 30 days.</td>
    </tr>
    </tbody>
  </table>
<p>Note: Employment and unemployment information is not seasonally adjusted.</p>
<p>Sources: U.S. Department of Labor, Bureau of Labor Statistics, and Office of the Comptroller, Fiscal and Budget Studies.</p>
</div>
<?php 
	widget_data_tables_add_js($node);
?>
