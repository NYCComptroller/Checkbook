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
?>
<h1 id="page-titleSpecial" class="title">Hudson Yards Infrastructure Corporation</h1>
<a class="trends-export" href="/export/download/trends_hudson_yards_infra_corp_csv?dataUrl=/node/<?php echo $node->nid ?>">Export</a>
<h5>(AMOUNTS IN THOUSANDS)</h5>
<div class="dataTable_wrapper">
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
        <th rowspan="2" class="number"><div class="trendCen" >DIB<br>Revenue<sup>(1)</sup></div></th>
        <th rowspan="2" class="number"><div class="trendCen" >TEP<br>Revenue<sup>(2)</sup></div></th>
        <th rowspan="2" class="number"><div class="trendCen" >ISP<br>Revenue<sup>(3)</sup></div></th>
        <th rowspan="2" class="number"><div class="trendCen" >PILOMRT<sup>(4)</sup></div></th>
        <th rowspan="2" class="number"><div class="trendCen" >PILOT<sup>(5)</sup></div></th>
        <th rowspan="2" class="number"><div class="trendCen" >Other<sup>(6)</sup></div></th>
        <th rowspan="2" class="number"><div class="trendCen" >Investment<br>Earnings</div></th>
        <th rowspan="2" class="number"><div class="trendCen" >Total<br>Revenue</div></th>
        <th colspan="3" class="centrig bb"><div>Debt Service</div></th>
        <th rowspan="2" class="number"><div class="trendCen" >Operating<br>Expenses</div></th>
        <th rowspan="2" class="number"><div class="trendCen" >Total to be<br>Covered</div></th>
        <th rowspan="2" class="number"><div class="trendCen" >Coverage on<br>Total Revenue<sup>(7)</sup><sup>(8)</sup></div></th>
        <th rowspan="2" >&nbsp;</th>
    </tr>
	<tr>
        <th class="number rowspan2Bot"><div class="trendCen" >Year</div></th>
        <th class="number"><div class="trendCen" >Interest</div></th>
        <th class="number"><div class="trendCen" >Principal</div></th>
        <th class="number"><div class="trendCen" >Total</div></th>
    </tr>    
    </thead>

    <tbody>

    <?php
        $count = 1;
        foreach($node->data as $row){
            $dollar_sign = ($count == 1 ? '<div class="dollarItem" >$</div>':'');
            echo "<tr>";
            
            echo "<td class='number'><div class='tdCen'>" . $row['fiscal_year'] . "</td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['dib_revenue_1']>0)?number_format($row['dib_revenue_1']):'-') . "</td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['tep_revenue_2']>0)?number_format($row['tep_revenue_2']):'-') . "</td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['isp_revenue_3']>0)?number_format($row['isp_revenue_3']):'-') . "</td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['pilomrt_payment']>0)?number_format($row['pilomrt_payment']):'-') . "</td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['pilot']>0)?number_format($row['pilot']):'-') . "</td>";            //PILOT DATA
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['other_4']>0)?number_format($row['other_4']):'-') . "</td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['investment_earnings']>0)?number_format($row['investment_earnings']):'-') . "</td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['total_revenue']>0)?number_format($row['total_revenue']):'-') . "</td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['interest']>0)?number_format($row['interest']):'-') . "</td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['principal']>0)?number_format($row['principal']):'-') . "</td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['total']>0)?number_format($row['total']):'-') . "</td>";
            //echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['operating_expenses']>0)?number_format($row['operating_expenses']):'-') . "</td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['operating_expenses']>0)?number_format($row['operating_expenses']):'-') . ((  $row['fiscal_year'] == '2012')? "<sup class='endItem'>(9)</sup>":"<sup class='endItem' style='visibility: hidden;'>(9)</sup>"). "</td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['total_to_be_covered']>0)?number_format($row['total_to_be_covered']):'-') . "</td>";
            //echo "<td class='number ' ><div class='tdCen'>" . $row['coverage_on_total_revenue_5'] . (($row['fiscal_year'] == '2009' || $row['fiscal_year'] == '2010' || $row['fiscal_year'] == '2011' || $row['fiscal_year'] == '2012')? "<sup class='endItem'>(7)</sup>":"<sup class='endItem' style='visibility: hidden;'>(7)</sup>") . "</div></td>";
            echo "<td class='number ' ><div class='tdCen'>" . $row['coverage_on_total_revenue_5'] . "</td>";
            echo "<td>&nbsp;</td>";
            echo "</tr>";
            $count++;
        }
    ?>

    </tbody>
</table>
</div>
<div class="footnote"><!-- p>(*) Date of inception of Hudson Yards Infrastructure Corporation was August 19, 2004.</p -->
    <p>HYIC issued its first bonds on December 21, 2006.</p>
    <p>(1) District Improvement Bonuses (DIB)</p>
    <p>(2) Property Tax Equivalency Payments (TEP)</p>
    <p>(3) Interest Support Payments (ISP)</p>
    <p>(4) Payments in Lieu of the Mortgage Recording Tax (PILOMRT)</p>
    <p>(5) Payments in Lieu of Real Estate Tax (PILOT)</p>
    <p>(6) Grant from City</p>
    <p>(7) ISPs are to be made by the City under the terms of Support and Development Agreement, which obligates the City to pay HYIC, subject to annual appropriation, an ISP amount equal to the
        difference between the amount of funds available to HYIC to pay interest on its current outstanding bonds and the amount of interest due on such bonds.</p>
    <p>(8) Debt service payments are funded from excess prior years’ revenues and from current year revenues.</p>
    <p>(9) In December 2011, HYIC was obligated to make an arbitrage rebate payment to United States Treasury for $8.8M </p>
    <p>Source: Hudson Yards Infrastructure Corporation</p>
</div>
<?php
	widget_data_tables_add_js($node);
