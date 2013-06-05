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

<a class="trends-export" href="/export/download/trends_pledged_rev_cov_nyc_trans_csv?dataUrl=/node/<?php echo $node->nid ?>">Export</a>

<h5>(in thousands)<br/>New York City Transitional Finance Authority</h5>

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
        <th rowspan="2" class="number"><div class="trendCen">PIT<br>Revenue<sup>(1)</sup></div></th>
        <th rowspan="2" class="number"><div class="trendCen">Sales Tax<br>Revenue<sup>(2)</sup></div></th>
        <th rowspan="2" class="number"><div class="trendCen">Total<br>Receipt</div></th>
        <th rowspan="2" class="number"><div class="trendCen">Other<sup>(3)</sup></div></th>
        <th rowspan="2" class="number"><div class="trendCen">Investment<br>Earnings</div></th>
        <th rowspan="2" class="number"><div class="trendCen">Total<br>Revenue</div></th>
        <th colspan="3" class="centrig"><div class="trendCen">Future Tax Secured<br>Bonds Debt Service</div></th>
        <th rowspan="2" class="number"><div class="trendCen">Operating<br>Expenses</div></th>
        <th rowspan="2" class="number"><div class="trendCen">Total to be<br>Covered</div></th>
        <th rowspan="2">&nbsp;</th>
    </tr>
    <tr>
        <th class="number rowspan2Bot"><div class="trendCen">Year</div></th>
        <th class="number"><div class="trendCen">Interest</div></th>
        <th class="number"><div class="trendCen">Principal</div></th>
        <th class="number"><div class="trendCen">Total</div></th>
    </tr>
    </thead>

    <tbody>

    <?php
        $count = 1;
        foreach( $node->data as $row){

          $dollar_sign = ($count == 1)? '<div class="dollarItem" >$</div>':'';
          $count++;
          echo "<tr><td class='number'><div class='tdCen'>" . $row['fiscal_year'] . "</div></td>";
          echo "<td class='number'>" . $dollar_sign . "<div class='tdCen'>" . (($row['pit_revenue']>0)?number_format($row['pit_revenue']):'-') . "</div></td>";
          echo "<td class='number'>" . $dollar_sign . "<div class='tdCen'>" . (($row['sales_tax_revenue']>0)?number_format($row['sales_tax_revenue']):'-') . "</div></td>";
          echo "<td class='number'>" . $dollar_sign . "<div class='tdCen'>" . (($row['total_receipt']>0)?number_format($row['total_receipt']):'-') . "</div></td>";
          echo "<td class='number'>" . $dollar_sign . "<div class='tdCen'>" . (($row['other']>0)?number_format($row['other']):'-') . "</div></td>";
          echo "<td class='number'>" . $dollar_sign . "<div class='tdCen'>" . (($row['investment_earnings']>0)?number_format($row['investment_earnings']):'-') . "</div></td>";
          echo "<td class='number'>" . $dollar_sign . "<div class='tdCen'>" . (($row['total_revenue']>0)?number_format($row['total_revenue']):'-') . "</div></td>";
          echo "<td class='number'>" . $dollar_sign . "<div class='tdCen'>" . (($row['interest']>0)?number_format($row['interest']):'-') . "</div></td>";
          echo "<td class='number'>" . $dollar_sign . "<div class='tdCen'>" . (($row['pricipal']>0)?number_format($row['pricipal']):'-') . "</div></td>";
          echo "<td class='number'>" . $dollar_sign . "<div class='tdCen'>" . (($row['total']>0)?number_format($row['total']):'-') . "</div></td>";
          echo "<td class='number'>" . $dollar_sign . "<div class='tdCen'>" . (($row['operating_expenses']>0)?number_format($row['operating_expenses']):'-') . "</div></td>";
          echo "<td class='number'>" . $dollar_sign . "<div class='tdCen'>" . (($row['total_to_be_covered']>0)?number_format($row['total_to_be_covered']):'-') . "</div></td>";
          echo "<td>&nbsp;</td>";
          echo "</tr>";
        }
    ?>

    </tbody>
</table>
<div class="footnote">
  <p>(1) Personal income tax (PIT).</p>
    <p>(2) Sales tax revenue has not been required by the TFA. This amount is available to cover debt service if required.</p>
    <p>(3) Grant from City and Federal Subsidy.</p></div>
<?php 
  widget_data_tables_add_js($node);

?>
