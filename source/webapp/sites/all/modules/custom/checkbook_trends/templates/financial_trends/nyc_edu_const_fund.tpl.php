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
<h1 id="page-titleSpecial" class="title">New York City Educational Construction Fund<sup class="title-sup">*</sup></h1>
<a class="trends-export" href="/export/download/trends_nyc_edu_const_fund_csv?dataUrl=/node/<?php echo $node->nid ?>">Export</a>
<h5>(AMOUNTS IN THOUSANDS)</h5>
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

        <th rowspan="2" class="number"><div class="trendCen" >Rental<br>Revenue</div></th>

        <th rowspan="2" class="number"><div class="trendCen" >Interest<br>Revenue</div></th>

        <th rowspan="2" class="number"><div class="trendCen" >Other<br>Income</div></th>

        <th rowspan="2" class="number"><div class="trendCen" >Total<br>Revenue</div></th>
        <th colspan="3" class="centrig bb"><div>Debt Service</div></th>

        <th rowspan="2" class="number"><div class="trendCen" >Operating<br>Expenses</div></th>

        <th rowspan="2"class="number"><div class="trendCen" >Total to<br>be Covered</div></th>
        <th rowspan="2"class="number"><div class="trendCen" >Coverage<br>Ratio</div></th>
    </tr>
    <tr class="second-row">

        <th class="number"><div class="trendCen" >Interest</div></th>

        <th class="number"><div class="trendCen" >Principal</div></th>

        <th class="number"><div class="trendCen" >Total</div></th>
    </tr>
    </thead>

    <tbody>

    <?php
        $count = 1;
        foreach( $node->data as $row){
            $dollar_sign = ($count == 1) ? '<div class="dollarItem" >$</div>':'';
            if($count % 2){$trclass = ' class="odd"';} else {$trclass = ' class="even"';}
            echo "<tr$trclass>";
            echo "<td class='number'><div class='tdCen'>" . $row['fiscal_year'] . "</div></td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['rental_revenue']) . "</div></td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['interest_revenue']) . "</div></td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . ($row['other_income']?number_format($row['other_income']):'-') . "</div></td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['total_revenue']) . "</div></td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['interest']) . "</div></td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['pricipal']) . "</div></td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['total']) . "</div></td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['operating_expenses']) . "</div></td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['total_to_be_covered']) . "</div></td>";
            echo "<td class='number'><div class='tdCen'>" .  number_format($row['coverage_ratio'],2) . "</div></td>";
            echo "</tr>";
            $count++;
        }
    ?>

    </tbody>
</table>
<div class="footnote "><p>(*) Interest of 8,919,000 was capitalized during Fiscal Year 2013 construction for year 2011 and 2010 bonds.</p>
    <p>In Fiscal Year 2014 ECF received $7 million in income for option for E. 57th development to extend lease beyond 99 years.</p>
    <p>Operating Expenses exclude Post Employment Benefits accrual.</p>
    <p>Principal in Fiscal Year 2016 does not include the redemption amount  of the 2005 bonds on October 1, 2015.</p>
    <p>In FY 2017 ECF received a $10 million Participation payment from E57th Street initial condo sales by the developer.</p>
    <br/>
    <p>Source: New York City Educational Construction Fund</p>
</div>
<?php
	widget_data_tables_add_js($node);