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
<h1 id="page-titleSpecial" class="title">Hudson Yards Infrastructure Corporation<sup class="title-sup">*</sup></h1>
<a class="trends-export" href="/export/download/trends_hudson_yards_infra_corp_csv?dataUrl=/node/<?php echo $node->nid ?>">Export</a>

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
        <th rowspan="2" class="number"><div class="trendCen" >Other<sup>(4)</sup></div></th>
        <th rowspan="2" class="number"><div class="trendCen" >Investment<br>Earnings</div></th>
        <th rowspan="2" class="number"><div class="trendCen" >Total<br>Revenue</div></th>
        <th colspan="3" class="centrig bb"><div>Debt Service</div></th>
        <th rowspan="2" class="number"><div class="trendCen" >Operating<br>Expenses</div></th>
        <th rowspan="2" class="number"><div class="trendCen" >Total to be<br>Covered</div></th>
        <th rowspan="2" class="number"><div class="trendCen" >Coverage on<br>Total Revenue<sup>(5)</sup></div></th>
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
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['other_4']>0)?number_format($row['other_4']):'-') . "</td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['investment_earnings']>0)?number_format($row['investment_earnings']):'-') . "</td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['total_revenue']>0)?number_format($row['total_revenue']):'-') . "</td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['interest']>0)?number_format($row['interest']):'-') . "</td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['principal']>0)?number_format($row['principal']):'-') . "</td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['total']>0)?number_format($row['total']):'-') . "</td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['operating_expenses']>0)?number_format($row['operating_expenses']):'-') . "</td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['total_to_be_covered']>0)?number_format($row['total_to_be_covered']):'-') . "</td>";
            echo "<td class='number ' ><div class='tdCen'>" . $row['coverage_on_total_revenue_5'] . (($row['fiscal_year'] == '2009' || $row['fiscal_year'] == '2010' || $row['fiscal_year'] == '2011')? "<sup class='endItem'>(6)</sup>":"<sup class='endItem' style='visibility: hidden;'>(6)</sup>") . "</div></td>";
            echo "<td>&nbsp;</td>";
            echo "</tr>";
            $count++;
        }
    ?>

    </tbody>
</table>
</div>
<div class="footnote"><p>(*) Date of inception of Hudson Yards Infrastructure Corporation was August 19, 2004.</p>
    <p>HYIC first DIB collection was on September 21, 2005 and issued its first bonds on December 21, 2006.</p>
    <p>(1) District Improvement Bonuses (DIB)</p>
    <p>(2) Property Tax Equivalency Payments (TEP)</p>
    <p>(3) Interest Support Payments (ISP)</p>
    <p>(4) Grant from City</p>
    <p>(5) ISPs are to be made by the City under the terms of Support and Development Agreement, which obligates the City to pay HYIC, subject to annual appropriation, an ISP amount equal to the difference between the amount of funds available to HYIC to pay interest on its current outstanding bonds and the amount of interest due on such bonds.</p>
    <p>(6) Debt service payments are funded from excess prior years’ revenues and from current year revenues.</p>
    <p>Source: Hudson Yards Infrastructure Corporation</p>
</div>
<?php 
	widget_data_tables_add_js($node);
?>
