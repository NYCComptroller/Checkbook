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
<div class="footnote "><p>(*)
    The 2005A Bonds were issued on January 5, 2005 to refinance the 1994 Bonds.</p>
    <p>The 2007A bonds were issued on January 18, 2007.</p>
    <p>Capitalized interest of $1,037,000.00 was not included on interest expense for year 2009 for the 2007A Bonds.</p>
    <p>The 2010A Bonds were issued on April 28, 2010 for capital purposes.</p>
    <p>Capitalized interest of $1,969,000 was not included on interest expense for year 2010 for the 2007A Bonds and $289,000 was not included on interest expense for year 2010 for the 2010A Bonds.</p>
    <p>The 2011A Bonds were issued on January 25, 2011 for capital purposes.</p>
    <p>Capitalized interest of $1,936,000 was included on interest expense for year 2011 for the 2011 and 2010 Bonds.</p>
    <p>Source: New York City Educational Construction Fund</p>
</div>
<?php 
	widget_data_tables_add_js($node);
?>
