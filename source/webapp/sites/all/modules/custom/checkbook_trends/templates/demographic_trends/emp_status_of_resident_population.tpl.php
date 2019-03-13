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

$first_year = $node->data[0]['fiscal_year'];
$last_year = end($node->data)['fiscal_year'];
reset($node->data);
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
    	<th class="centrig bb" colspan="5"><div><?= $first_year ?>-<?= $last_year ?></div></th>
    </tr>
    <tr>

        <th colspan="2" class="centrig bb"><div>Civilian Labor Force<br>(in thousands)</div></th>
        <th rowspan="2"><div class="">&nbsp;</div></th>
        <th colspan="2" class="centrig bb"><div>Unemployment Rate</div></th>
    </tr>
	<tr>
		
        <th class="number" ><div class="trendCen">New York City<br>Employed</div></th>
        <th class="number" ><div class="trendCen">New York City<br>Unemployed<sup style="text-transform: lowercase;">(a)</sup></div></th>
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
    <p>(a) Unemployed persons are all civilians who had no employment during the survey week, were available for work,
        except for temporary illness, and had made efforts to find employment some time during the prior four weeks.
        This includes persons who were waiting to be recalled to a job from which they were laid off or were
        waiting to report to a new job within 30 days.</p>
    <br />
<p>Note: Employment and unemployment information is not seasonally adjusted.</p>
    <br />
<p>Sources: U.S. Department of Labor, Bureau of Labor Statistics, and Office of the Comptroller, Fiscal and Budget Studies.</p>
</div>
<?php
	widget_data_tables_add_js($node);
