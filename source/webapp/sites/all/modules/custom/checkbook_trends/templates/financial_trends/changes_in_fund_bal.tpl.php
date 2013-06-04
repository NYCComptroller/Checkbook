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
$table_rows = array();
$years = array();
foreach( $node->data as $row){	
	$length =  $row['indentation_level'];
	$spaceString = '&nbsp';
	while($length > 0){
		$spaceString .= '&nbsp';
		$length -=1;
	}
	$table_rows[$row['display_order']]['category'] =  $row['category'];
	$table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
	$table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
	$table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
	$table_rows[$row['display_order']]['currency_symbol'] = $row['currency_symbol'];
	$table_rows[$row['display_order']][$row['fiscal_year']]['amount'] = $row['amount'];
	$years[$row['fiscal_year']] = 	$row['fiscal_year'];
}
rsort($years);
?>

<h3><?php echo $node->widgetConfig->table_title; ?></h3>

<a class="trends-export" href="/export/download/trends_changes_in_fund_bal_csv?dataUrl=/node/<?php echo $node->nid ?>">Export</a>
<table class="fy-ait">
  <tbody>
  <tr>
    <td style="width:300px;padding:0;">&nbsp;</td>
    <td class="bb">Fiscal Year<br>(Amounts in Thousands)</td>
  </tr>
  </tbody>
</table>
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
        <tr class="second-row">
            <th><div><br/></div></th>
            <?php
            foreach ($years as $year)
                echo "<th><div>&nbsp;</div></th><th class='number'><div>" . $year . "</div></th>";
            ?>
            <th>&nbsp;</th>
        </tr>
    </thead>

    <tbody>

    <?php
        $count = 0;
        foreach($table_rows as $row){
            $cat_class = "";
            $dollar_sign = "";
            $count++;

            $dollar_sign = ($row['currency_symbol'] == 'Y')?"<div class='dollarItem' >$</div>" : '';

            if($row['highlight_yn'] == 'Y')
                $cat_class = "highlight ";
            $cat_class .= "level" . $row['indentation_level'];
            $amount_class = "number";

            if($row['amount_display_type']){
                $amount_class .= " amount-" . $row['amount_display_type'];
                $cat_class .= " cat-" . $row["amount_display_type"];
            }

            echo "<tr>
            <td class='text " . $cat_class . "' ><div>" . $row['category'] . "</div></td>";

            foreach ($years as $year){
                echo "<td><div></div></td>";
                
                if($count == count($table_rows)){
                    echo "<td class='" . $amount_class . " ' ><div>" . $row[$year]['amount'] . "%</div></td>";
                }else{
                    if($row[$year]['amount'] > 0){
                        echo "<td class='" . $amount_class . " ' >". $dollar_sign ."<div>". number_format($row[$year]['amount']) . "</div></td>";
                    }else if($row[$year]['amount'] < 0){
                       echo "<td class='" . $amount_class . " ' >". $dollar_sign ."<div>(" . number_format(abs($row[$year]['amount'])) . ")</div></td>";
                    }else if($row[$year]['amount'] == 0){
                         if(strpos($row['category'], ':') || strtolower($row['category']) == 'less capital outlays')
                            echo "<td class='" . $amount_class . " ' ><div>" . '&nbsp;' . "</div></td>";
                         else
                            echo "<td class='" . $amount_class . " ' ><div>" . '-' . "</div></td>";
                    }
                }
            }
            echo "<td>&nbsp;</td>";
            echo "</tr>";
        }
    ?>

    </tbody>
</table>
<?php
	widget_data_tables_add_js($node);
?>
<div class="footnote">
<p>Source: Comprehensive Annual Financial Reports of the Comptroller.</p>
</div>
