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
    $last_year = end($node->data)['fiscal_year'];
    reset($node->data);
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
    	<th colspan="4" class="centrig bb"><div>2000-<?= $last_year ?><sup>*</sup></div></th>
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
    			echo "<td class='number '><div  class='tdCen'>" . (($row['percentage_change_prior_period']!=0)?(number_format($row['percentage_change_prior_period'],2)):' - '). $percent_sign. "</div></td>";
			    echo "</tr>";
                $count++;
    		}
    ?>

    </tbody>
</table>
    <div class="footnote">
         <p>*Amounts as of March 28, <?= $last_year ?></p>
         <p>Source: U.S Department of Commerce, Bureau of Economic Analysis. US Census Bureau and American Fact Finder.</p>
    </div>
<?php 
	widget_data_tables_add_js($node);

    if (isset($node->widgetConfig->table_footnote)) {
	    echo $node->widgetConfig->table_footnote;
	}
