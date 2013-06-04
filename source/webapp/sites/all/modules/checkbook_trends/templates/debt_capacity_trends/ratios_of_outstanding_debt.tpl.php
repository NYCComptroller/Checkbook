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
if(preg_match('/featuredtrends/',$_GET['q'])){
  $links = array(l(t('Home'), ''), l(t('Trends'), 'featured-trends'),
      '<a href="/featured-trends?slide=4">Ratios of Outstanding Debt</a>',
      'Ratios of Outstanding Debt by Type Details');
  drupal_set_breadcrumb($links);
}
?>

<a class="trends-export" href="/export/download/trends_ratios_of_outstanding_debt_csv?dataUrl=/node/<?php echo $node->nid ?>">Export</a>
<h4>(dollars in millions)</h4>
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
            <th class="number"><div class="trendCen" >Fiscal<br>year</div></th>
            <th class="number"><div class="trendCen" >General<br>Obligation<br>Bonds</div></th>
            <th class="number"><div class="trendCen" >Revenue<br>Bonds</div></th>
            <th class="number"><div class="trendCen" >ECF</div></th>
            <th class="number"><div class="trendCen" >MAC<br>Debt</div></th>
            <th class="number"><div class="trendCen" >TFA</div></th>
            <th class="number"><div class="trendCen" >TSASC<br>Debt</div></th>
            <th class="number"><div class="trendCen" >STAR</div></th>
            <th class="number"><div class="trendCen" >FSC</div></th>
            <th class="number"><div class="trendCen" >SFC<br>Debt</div></th>
            <th class="number"><div class="trendCen" >HYIC<br>Bonds and<br>Notes</div></th>
            <th class="number"><div class="trendCen" >Capital<br>Leases<br>Obligations</div></th>
            <th class="number"><div class="trendCen" >IDA<br>Bonds</div></th>
            <th class="number"><div class="trendCen" >Treasury<br>Obligations</div></th>
            <th class="number"><div class="trendCen" >Total<br>Primary<br>Government</div></th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>

    <?php
        $count = 1;
        foreach( $node->data as $row){
            $dollar_sign = ($count == 1) ? '<div class="dollarItem" >$</div>':'';
            $count++;
            echo "<tr><td class='number'><div class='tdCen'>" . $row['fiscal_year'] . "</div></td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  (($row['general_obligation_bonds']>0)?number_format($row['general_obligation_bonds']):'-') . "</div></td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . (($row['revenue_bonds']>0)?number_format($row['revenue_bonds']):'-') . "</div></td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . (($row['ecf']>0)?number_format($row['ecf']):'-') . "</div></td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . (($row['mac_debt']>0)?number_format($row['mac_debt']):'-') . "</div></td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . (($row['tfa']>0)?number_format($row['tfa']):'-') . "</div></td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . (($row['tsasc_debt']>0)?number_format($row['tsasc_debt']):'-') . "</div></td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . (($row['star']>0)?number_format($row['star']):'-') . "</div></td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . (($row['fsc']>0)?number_format($row['fsc']):'-') . "</div></td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . (($row['sfc_debt']>0)?number_format($row['sfc_debt']):'-') . "</div></td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . (($row['hyic_bonds_notes']>0)?number_format($row['hyic_bonds_notes']):'-') . "</div></td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . (($row['capital_leases_obligations']>0)?number_format($row['capital_leases_obligations']):'-') . "</div></td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" . (($row['ida_bonds']>0)?number_format($row['ida_bonds']):'-') . "</div></td>";
            if($row['treasury_obligations'] < 0 )
                echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>(" . number_format(abs($row['treasury_obligations'])) . ")</div></td>";
            else if ($row['treasury_obligations'] == 0)
                echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>-</div></td>";
            else
                echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>" .  number_format($row['treasury_obligations']) . "</div></td>";
            echo "<td class='number'>" .$dollar_sign. "<div class='tdCen'>". (($row['total_primary_government']>0)?number_format($row['total_primary_government']):'-') . "</div></td>";
            echo "<td>&nbsp;</td>";
            echo "</tr>";
        }
    ?>

    </tbody>
</table>
<div class="footnote"><p>Sources: Comprehensive Annual Financial Reports of the Comptroller</p>
<p>Note: Gross Debt, Percentage of Personal Income and Per Capital Gross Debt columns had to be removed. The figures changed year by year and they would not match the figures shown when that years CAFR was released.</p></div>
<?php 
	widget_data_tables_add_js($node);
?>
