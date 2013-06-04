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
	$table_rows[$row['display_order']]['area'] =  $row['area'];
	$table_rows[$row['display_order']]['fips'] =  $row['fips'];
	$table_rows[$row['display_order']]['line_code'] =  $row['line_code'];
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
            <th class="number"><div class="trendCen">FIPS</div></th>
            <th class="text"><div>Area</div></th>
            <th class="centrig"><div>LineCode</div></th>
            <th class="text"><div>Description</div></th>
            <?php
            foreach ($years as $year)
                echo "<th class='number'><div>" . $year . "</div></th>";
            ?>
            <th>&nbsp;</th>
        </tr>
    </thead>

    <tbody>

    <?php 
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
			    echo "<td class='number centrig'><div>" . (isset($row['line_code'])?$row['line_code'] :'&nbsp;') . "</div></td>";
			    echo "<td class='text'><div>" . (isset($row['category'])?$row['category'] :'&nbsp;') . "</div></td>";
			    foreach ($years as $year)
			        echo "<td class='" . $amount_class . "'><div>" . (isset($row[$year]['amount'])?number_format($row[$year]['amount']) :'&nbsp;') . "</div></td>";
			    echo "<td>&nbsp;</td>";
			    echo "</tr>";
    		}
    ?>

    </tbody>
</table>
  <div class="footnote">
<h5>Legend / Footnotes:</h5>
<p>1/ Census Bureau midyear population estimates. Estimates for 2000-2009 reflect county population estimates available as of April 2010. For more information see the explanatory note at: http://www.bea.gov/regional/docs/popnote.cfm.</p>
<p>2/ Per capita personal income was computed using Census Bureau midyear population estimates. Estimates for 2000-2009 reflect county population estimates available as of April 2010.</p>
<p>All state and local area dollar estimates are in current dollars (not adjusted for inflation).</p>
<p>Last updated: April 21, 2011 - new estimates for 2009; revised estimates for 2001-2008.</p>
</div>
<?php 
	widget_data_tables_add_js($node);
?>
