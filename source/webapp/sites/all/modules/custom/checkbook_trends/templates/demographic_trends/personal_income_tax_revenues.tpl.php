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
	$table_rows[$row['display_order']]['fips'] =  $row['fips'];
	$table_rows[$row['display_order']]['area'] =  $row['area'];
	$table_rows[$row['display_order']]['highlight_yn'] = $row['highlight_yn'];
	$table_rows[$row['display_order']]['indentation_level'] = $row['indentation_level'];
	$table_rows[$row['display_order']]['amount_display_type'] = $row['amount_display_type'];
	$table_rows[$row['display_order']][$row['fiscal_year']]['amount'] = $row['amount'];
	$years[$row['fiscal_year']] = 	$row['fiscal_year'];
}
sort($years);
if(preg_match('/featuredtrends/',$_GET['q'])){
  $links = array(l(t('Home'), ''), l(t('Trends'), 'featured-trends'),
    '<a href="/featured-trends?slide=3">Personal Income</a>',
    'Personal Income Details');
  drupal_set_breadcrumb($links);
}
?>

<h3><?php echo $node->widgetConfig->table_title; ?></h3>

<a class="trends-export" href="/export/download/trends_personal_income_tax_revenues_csv?dataUrl=/node/<?php echo $node->nid ?>">Export</a>

<h5>CA1-3 Personal income summary</h5>
<h6>Bureau of Economic Analysis</h6>
    <h5>(AMOUNTS IN THOUSANDS)</h5>
<table id="table_<?php echo widget_unique_identifier($node) ?>" style="display:none" class="trendsShowOnLoad <?php echo $node->widgetConfig->html_class ?>">
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
            <th class="number"><div class="trendCen">FIPS</div></th>
            <th class="text"><div>Area</div></th>
            <?php
            foreach ($years as $year)
                echo "<th class='number'><div>" . $year . "</div></th>";
            ?>
            <th>&nbsp;</th>
        </tr>
    </thead>

    <tbody>

    <?php
            $dollar_div = "<div class='dollarItem'>$</div>";
    		foreach( $table_rows as $row){
    			$cat_class = "";
    			if( $row['highlight_yn'] == 'Y')
    				$cat_class = "highlight ";
    			$cat_class .= "level" . $row['indentation_level'];
    			$amount_class = "";
    			if( $row['amount_display_type'] != "" )
    			$amount_class = "amount-" . $row['amount_display_type'];
                $amount_class .= " number";

                switch($row['category']){
                    case "Personal income (thousands of dollars)":
                        $row['category'] = "Personal income<br/>(thousands of dollars)";
                        break;
                    case "Per capita personal income (dollars) 2/":
                        $row['category'] = "Per capita personal<br/>income (dollars) 2/";
                        break;
                    default:
                        break;
                }

                echo "<tr><td class='number'><div class='tdCen'>" . (isset($row['fips'])?$row['fips'] :'&nbsp;') . "</div></td>";
			    echo "<td class='text'><div>" . (isset($row['area'])?$row['area'] :'&nbsp;') . "</div></td>";

			    foreach ($years as $year)
			        echo "<td class='" . $amount_class . "'>$dollar_div<div>" . (isset($row[$year]['amount'])?number_format($row[$year]['amount']) :'&nbsp;') . "</div></td>";
			    echo "<td>&nbsp;</td>";
			    echo "</tr>";
			    $dollar_div = "";
    		}
    ?>

    </tbody>
</table>
  <div class="footnote">
<h5>Legend / Footnotes:</h5>
<p>Note-- All state and local area dollar estimates are in current dollars (not adjusted for inflation).</p>
<p>Last updated: November 26, 2012 - new estimates for 2011; revised estimates for 2009-2010. For more information see the explanatory note at: http://www.bea.gov/regional/docs/popnote.cfm.</p>
</div>
<?php
	widget_data_tables_add_js($node);
?>
