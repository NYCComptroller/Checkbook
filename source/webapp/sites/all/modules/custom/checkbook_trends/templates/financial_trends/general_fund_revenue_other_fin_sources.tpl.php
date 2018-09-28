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
$table_rows = array();
$years = array();
foreach( $node->data as $row){
	$length =  $row['indentation_level'];
	$spaceString = '&nbsp;';
	while($length > 0){
		$spaceString .= '&nbsp;';
		$length -=1;
	}
	$table_rows[$row['display_order']]['category'] =  $row['category'];
	$table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
	$table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
	$table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
	$table_rows[$row['display_order']][$row['fiscal_year']]['amount'] = $row['amount'];
	$years[$row['fiscal_year']] = 	$row['fiscal_year'];
}
rsort($years);
if(preg_match('/featuredtrends/',$_GET['q'])){
  $links = array(l(t('Home'), ''), l(t('Trends'), 'featured-trends'),l(t('General Fund Revenues and General Fund Expenditures'), 'featured-trends'),'General Fund Revenues and Other Financing Sources Details');
  drupal_set_breadcrumb($links);
}
?>

<?php if($node->widgetConfig->table_title){echo '<h3>'.$node->widgetConfig->table_title.'</h3>';} ?>

<a class="trends-export" href="/export/download/trends_general_fund_revenue_other_fin_sources_csv?dataUrl=/node/<?php echo $node->nid ?>">Export</a>
<table class="fy-ait">
  <tbody>
  <tr>
    <td style="width:300px;padding:0"><div>&nbsp;</div></td>
    <td class="bb"><div>Fiscal Year<br>(Amounts in Thousands)</td>
  </tr>
  </tbody>
</table>
<table id="table_<?php echo widget_unique_identifier($node) ?>"  style='display:none' class="trendsShowOnLoad <?php echo $node->widgetConfig->html_class ?>">
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
        foreach( $table_rows as $row){
            $cat_class = "";
            $dollar_sign = "";
            $count++;
            if($count == 2 || $count == count($table_rows)){
                $dollar_sign = "<div class='dollarItem' >$</div>";
            }

            if($row['highlight_yn'] == 'Y')
                $cat_class = "highlight ";
            $cat_class .= "level" . $row['indentation_level'];
            $amount_class = "number";

            if($row['amount_display_type'])
                $amount_class .= " amount-" . $row['amount_display_type'];

            $row['category'] = (isset($row['category'])?$row['category']:'&nbsp;');

            echo "<tr>
            <td class='text " . $cat_class . "' ><div>" . $row['category'] . "</div></td>";
            foreach ($years as $year){
                echo "<td><div></div></td>";
                if($row[$year]['amount'] > 0){
                    echo "<td class='" . $amount_class . " ' >" . $dollar_sign . "<div>" . number_format($row[$year]['amount']) . "</div></td>";
                }else if($row[$year]['amount'] < 0){
                   echo "<td class='" . $amount_class . " ' >" . $dollar_sign . "<div>(" . number_format(abs($row[$year]['amount'])) . ")</div></td>";
                }else{
                    if(strpos($row['category'], ':'))
                        echo "<td class='" . $amount_class . " ' ><div>" . '&nbsp;' . "</div></td>";
                     else
                        echo "<td class='" . $amount_class . " ' ><div>" . '-' . "</div></td>";
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
