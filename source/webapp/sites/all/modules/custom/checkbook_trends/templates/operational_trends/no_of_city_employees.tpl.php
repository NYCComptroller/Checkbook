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
	$spaceString = '&nbsp';
	while($length > 0){
		$spaceString .= '&nbsp';
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
?>

<h3><?php echo $node->widgetConfig->table_title; ?></h3>

<a class="trends-export" href="/export/download/trends_no_of_city_employees_csv?dataUrl=/node/<?php echo $node->nid ?>">Export</a>
<table class="fy-ait">
  <tbody>
  <tr>
    <td style="width:225px;padding:0;">&nbsp;</td>
    <td class="bb" style="padding:10px 0;">Fiscal Year</td>
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
    <tr>
    <?php
    echo "<th><div><br/></div></th>";
    foreach ($years as $year){
        echo "<th><div></div></th>";
        echo "<th class='number'><div>" . $year . "</div></th>";
    }
    ?>
    <th>&nbsp;</th>
    </tr>
    </thead>

    <tbody>

    <?php
            $i = 1;
    		foreach($table_rows as $row){
    			$cat_class = "";
    			if( $row['highlight_yn'] == 'Y')
    				$cat_class = "highlight ";    			
    			$cat_class .= "level" . $row['indentation_level']; 
    			$amount_class = "";
    			if( $row['amount_display_type'] != "" )
    			$amount_class = "amount-" . $row['amount_display_type'];
    			$amount_class .= ' number';
                $row['category'] = str_replace('(a)','<sup style="text-transform: lowercase;">(a)</sup>', $row['category']);
                $row['category'] = (isset($row['category'])?$row['category']:'&nbsp;');
                
                if($row['category'] == "Percentage Increase (Decrease) from Prior Year"){
                	$row['category']  = "Percentage Increase (Decrease)<br><span style='padding-left:0px;'>from Prior Year</span>";
                }
                
			    echo "<tr>
			    <td class='text' ><div class='" . $cat_class . "' >" . $row['category'] . "</div></td>";
			    foreach ($years as $year){
                    if($i < count($table_rows)){
			            echo "<td><div>&nbsp;</div></td><td class='" . $amount_class . "' ><div>" . (($row[$year]['amount']>0)?number_format($row[$year]['amount']):'&nbsp;') . "</div></td>";
                    }
                    else{
                        if($row[$year]['amount'] < 0)
                            echo "<td><div>&nbsp;</div></td><td class='" . $amount_class . "' ><div>(" . abs($row[$year]['amount']) . "%)</div></td>";
                        else
                            echo "<td><div>&nbsp;</div></td><td class='" . $amount_class . "' ><div>" . number_format($row[$year]['amount'],1) . "%</div></td>";
                    }
                }
                echo "<td>&nbsp;</td>";
			    echo "</tr>";
                $i++;
    		}
    ?>

    </tbody>
</table>
<div class="footnote">
    <p>Sources: Financial Management System (FMS), Mayor's Office of Management and Budget, and Mayor's Office of Operations.</p>
</div>
<?php
	widget_data_tables_add_js($node);
?>
