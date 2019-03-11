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

if(preg_match('/featuredtrends/',$_GET['q'])){
  
  $links = array(l(t('Home'), ''), l(t('Trends'), 'featured-trends'),
      '<a href="/featured-trends?slide=1">Property Tax Levies and Collections</a>',
      'Property Tax Levies and Collections Details');
  drupal_set_breadcrumb($links);
}
?>

<a class="trends-export" href="/export/download/trends_property_tax_levies_csv?dataUrl=/node/<?php echo $node->nid ?>">Export</a>

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
        <th class="number rowspan2Top"><div class="trendCen" >Fiscal</div></th>
        <th rowspan="2" class="number"><div class="trendCen" >Taxes Levied<br>for the<br>Fiscal Year</div></th>
        <th colspan="3" class="centrig bb"><div class="trendCen" >Collected Within the<br>Fiscal Year of the Levy</div></th>
        <th rowspan="2" class="number"><div class="trendCen" >Non-Cash<br>Liquidations<br>and Adjustments<br>to Levy<sup>(1)</sup></div></th>
        <th colspan="2" class="centrig bb"><div>Total Collections<br>and Adjustments to Date</div></th>
        <th rowspan="2" class="number"><div class="trendCen" >Remaining<br>Uncollected<br>JUNE 30, <?= $last_year ?></div></th>
        <th rowspan="2">&nbsp;</th>
    </tr>
    <tr>
	  <th class="number rowspan2Bot"><div class="trendCen" >Year</div></th>
      <th class="number"><div class="trendCen" >Amount</div></th>
      <th class="number"><div class="trendCen" >Percentage<br>of the Levy</div></th>
      <th class="number"'><div class="trendCen" >Collected<br>in Subsequent<br>Years</div></th>
      <th class="number"><div class="trendCen" >Amount</div></th>
      <th class="number"><div class="trendCen" >Percentage<br>of the Levy</div></th>
    </tr>
    </thead>

    <tbody>

    <?php
            $count = 1;
    		foreach( $node->data as $row){
                $dollar_sign = ($count == 1)?'<div class="dollarItem" >$</div>':'';
                $percent_sign = ($count == 1)?'<span class="endItem">%</span>' : '<span class="endItem" style="visibility:hidden;">%</span>';

			    echo "<tr><td class='number'><div class='tdCen'>" . $row['fiscal_year'] . "</div></td>";
    			echo "<td class='number '>" .$dollar_sign. "<div class='tdCen'>" .  number_format($row['tax_levied']) . "</div></td>";
    			echo "<td class='number '>" .$dollar_sign. "<div class='tdCen'>" .  number_format($row['amount']) . "</div></td>";
    			echo "<td class='number '><div class='tdCen'>" . number_format($row['percentage_levy'],2) .$percent_sign .  "</div></td>";
    			echo "<td class='number '>" .$dollar_sign. "<div class='tdCen'>" .  (($row['collected_subsequent_years'] > 0) ? number_format($row['collected_subsequent_years']) :'-') . "</div></td>";
    			echo "<td class='number '>" .$dollar_sign. "<div class='tdCen'>" .  number_format($row['levy_non_cash_adjustments']) . "</div></td>";
    			echo "<td class='number '>" .$dollar_sign. "<div class='tdCen'>" .  number_format( $row['collected_amount']) . "</div></td>";
    			echo "<td class='number '><div class='tdCen'>" .  number_format($row['collected_percentage_levy'],2) .$percent_sign. "</div></td>";
    			echo "<td class='number '>" .$dollar_sign. "<div class='tdCen'>" .  number_format($row['uncollected_amount']) . "</div></td>";
			    echo "<td>&nbsp;</td>";
    			echo "</tr>";

                $count++;
    		}
    ?>

    </tbody>
</table>
<div class="footnote">
<p>(1) Adjustments to Tax Levy are Non-Cash Liquidations and Cancellations of Real Property Tax and include School Tax Relief payments which are not included in the City Council Resolutions.</p>
<p>SOURCES: Resolutions of the City Council and other Department of Finance reports.</p>
</div>
<?php
	widget_data_tables_add_js($node);
?>
