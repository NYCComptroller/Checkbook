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

<a class="trends-export"
   href="/export/download/trends_assesed_val_and_estd_act_val_csv?dataUrl=/node/<?php echo $node->nid ?>">Export</a>
<table class="fy-ait">
  <tbody>
  <tr>
    <td style="width:100px;padding:0;">&nbsp;</td>
    <td class="bb">(Amounts in Millions)</td>
  </tr>
  </tbody>
</table>
<table id="table_<?php echo widget_unique_identifier($node) ?>" style='display:none' class="trendsShowOnLoad <?php echo $node->widgetConfig->html_class ?>">
  <?php
  if (isset($node->widgetConfig->caption_column)) {
    echo '<caption>' . $node->data[0][$node->widgetConfig->caption_column] . '</caption>';
  }
  else {
    if (isset($node->widgetConfig->caption)) {
      echo '<caption>' . $node->widgetConfig->caption . '</caption>';
    }
  }
  ?>
  <thead>
  <tr>
    <th class="number"><div class="trendCen" >Fiscal<br>Year</div></th>
    <th class="number"><div class="trendCen" >Class<br>One</div></th>
    <th class="number"><div class="trendCen" >Class<br>Two</div></th>
    <th class="number"><div class="trendCen" >Class<br>Three</div></th>
    <th class="number"><div class="trendCen" >Class<br>Four</div></th>
    <th class="number"><div class="trendCen" >Less<br>Tax Exempt<br>Property</div></th>
    <th class="number"><div class="trendCen" >Total Taxable<br>Assessed<br>Value</div></th>
    <th class="number"><div class="trendCen" >Total<br>Direct<br>Tax<br>Rate<sup>(1)</sup></div></th>
    <th class="number"><div class="trendCen" >Estimated<br>Actual<br>Taxable<br>Value</div></th>
    <th class="number"><div class="trendCen" >Assessed<br>Value as a<br>Percentage of<br>Actual Value</div></th>
    <th>&nbsp;</th>
  </tr>
  </thead>

  <tbody>

  <?php
  $count = 1;
  foreach ($node->data as $row) {
    $dollar_sign = ($count == 1) ? '<div class="dollarItem" >$</div>':'';
    $percent_sign = ($count == 1) ? '<span class="endItem">%</span>' : '<span class="endItem" style="visibility:hidden;">%</span>';

    echo "<tr><td class='number'><div class='tdCen'>" . $row['fiscal_year'] . "</div></td>";
    echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['class_one'],1,'.',',') . "</div></td>";
    echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['class_two'],1,'.',',') . "</div></td>";
    echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['class_three'],1,'.',',') . "</div></td>";
    echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['class_four'],1,'.',',') . "</div></td>";
    echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['less_tax_exempt_property'],1,'.',',') . "</div></td>";
    echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['total_taxable_assesed_value'],1,'.',',') . "</div></td>";
    echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['total_direct_tax_1'],2) . "</div></td>";
    echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . number_format($row['estimated_actual_taxable_value'],1,'.',',') . "</div></td>";
    echo "<td class='number'><div class='tdCen'>" . number_format($row['assesed_value_percentage'],2) .$percent_sign. "</div></td>";
    echo "<td>&nbsp;</td>";
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
  <p><sup>(1)</sup> Property tax rate based on every $100 of assessed valuation.</p>

  <p>Notes:</p>

  <p style="margin:0;">The definitions of the four classes are as follows:</p>
  <table>
    <tr>
      <td class="class-name">Class One -</td>
      <td class="description">One, two, and three family homes; single family homes on cooperatively owned land.
        Condominiums with no more than three dwelling units, provided such property was previously classified as
        Class One or no more than three stories in height and built as condominiums.
        Mixed-use property with three units or less, provided 50 percent or more of the space is used for residential
        purposes. Vacant land, primarily residentially zoned, except in Manhattan below 110th Street.
      </td>
    </tr>
    <tr>
      <td class="class-name">Class Two -</td>
      <td class="description">All other residential property not in Class One, except hotels and motels. Mixed-use
        property with four or more units, provided 50 percent or more of the space is used for residential purposes.
      </td>
    </tr>
    <tr>
      <td class="class-name">Class Three -</td>
      <td class="description">Utility real property owned by utility corporations, except land and buildings.</td>
    </tr>
    <tr>
      <td class="class-name">Class Four -</td>
      <td class="description">All other real property.</td>
    </tr>
  </table>
  <p>Classes One to Four amounts include Tax Exempt Property.</p>

  <p>Property in New York City is reassessed every year. The City assesses property at approximately 40 percent of
    Market Value for commercial and industrial property and 20 percent of Market Value for residential property.</p>

  <p><span style="font-variant: small-caps">Sources</span>: Resolutions of the City Council and The Annual Report of The
    New York City Property Tax Fiscal Year 2011.</p>
</div>