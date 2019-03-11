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
    $last_year = end($node->data)['fiscal_year'];
    reset($node->data);
?>

<a class="trends-export" href="/export/download/trends_collections_cancellations_abatements_csv?dataUrl=/node/<?php echo $node->nid ?>">Export</a>

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
        <th rowspan="2" class="number"><div class="trendCen" >Fiscal<br>year</div></th>
        <th rowspan="2" class="number"><div class="trendCen" >Tax Levy<br>(in millions)<sup>(2)</sup></div></th>
        <th colspan="3" class="centrig bb"><div>Percent of Levy through June 30, <?= $last_year ?></div></th>
        <th rowspan="2" class="number"><div class="trendCen" >Uncollected<br>Balance<br/> June 30, <?= $last_year ?></div></th>
    </tr>
    <tr class="second-row">
        <th class="number"><div class="trendCen" >Collections</div></th>
        <th class="number"><div class="trendCen" >Cancellations</div></th>
        <th class="number"><div class="trendCen" >Abatements<br>and Discounts<sup>(1)</sup></div></th>
    </tr>    
    </thead>

    <tbody>

    <?php
            $count = 1;
    		foreach( $node->data as $row){
                $dollar_sign = ($count == 1) ? '<div class="dollarItem" >$</div>':'';
                $percent_sign = ($count == 1) ? '<span class="endItem">%</span>' : '<span class="endItem" style="visibility:hidden;">%</span>';

			    echo "<tr><td class='number'><div class='tdCen'>" . $row['fiscal_year'] . "</div></td>";
    			echo "<td class='number '>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['tax_levy'],1,'.',',') . (($row['fiscal_year']=='2003')?"<sup class='endItem'>(2)</sup>":"<sup class='endItem' style='visibility: hidden;'>(1)</sup>") . "</div></td>";
    			echo "<td class='number'><div class='tdCen'>" .  number_format($row['collection'],1) . $percent_sign . "</div></td>";
    			echo "<td class='number'><div class='tdCen'>" .  number_format($row['cancellations'],1) . $percent_sign ."</div></td>";
    			echo "<td class='number'><div class='tdCen'>" .  number_format($row['abatement_and_discounts_1'],1) . $percent_sign ."</div></td>";
    			echo "<td class='number'><div class='tdCen'>" .  number_format($row['uncollected_balance_percent'],1) . $percent_sign ."</div></td>";
			    echo "</tr>";

                $count++;
    		}
    ?>

    </tbody>
</table>
<?php
	widget_data_tables_add_js($node);
?>
<div class="footnote">
<p>(1) Abatements and Discounts include SCRIE Abatements (Senior citizen rent increase exemption), J-51 Abatements,
    Section 626 Abatements and other minor discounts offered by the City to property owners.</p>
<p>
    (2) The Tax Levy amounts are the amount from the City Council Resolution. In 2005 an 18% surcharge was imposed
    and is included in each year following.
</p>
<p>Notes: Total uncollected balance at June 30, <?= $last_year ?> less allowance for uncollectible amounts equals net realizable amount
    (real estate taxes receivable).</p>
<p>Levy may total over 100 percent due to imposed charges that include ICIP deferred charges (Industrial and Commercial
    Incentive Program), rebilling charges and other additional charges imposed by The Department of Finance(DOF). This
    information is included in the FAIRTAX LEVY report.</p>
</div>
