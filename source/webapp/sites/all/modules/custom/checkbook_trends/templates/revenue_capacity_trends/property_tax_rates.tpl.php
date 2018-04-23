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
            <th class="number"><div class="trendCen" >Basic<br>Rate<sup>(1)</sup></div></th>
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
    <p>SOURCE: Resolutions of the City Council.</p>
    <p>Note: (1) Property tax rate based on every $100 of assessed valuations.</p>

</div>
<?php 
	widget_data_tables_add_js($node);