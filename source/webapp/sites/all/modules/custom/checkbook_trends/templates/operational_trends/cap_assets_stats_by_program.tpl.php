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
<a class="trends-export" href="/export/download/trends_cap_assets_stats_by_program_csv?dataUrl=/node/<?php echo $node->nid ?>">Export</a>
<table class="fy-ait">
  <tbody>
  <tr>
    <td width="250">&nbsp;</td>
    <td class="bb">Fiscal Year</td>
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
            <th><br/></th>
            <?php
            foreach ($years as $year)
                echo "<th><div>&nbsp;</div></th><th class='number'><div>" . $year . "</div></th>";
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

                $amount_class .= " number";

                for($i=1;$i < 30;$i++){
                    $find = '('. $i . ')';
                    $replace = '<sup>('.$i .')</sup>';
                    $row['category'] = str_replace($find,$replace, $row['category']);
                }

                $row['category'] = (isset($row['category'])?$row['category']:'&nbsp;');
                
                
                $conditionCategory = $row['category'];
//                switch($conditionCategory){
//                	case "Correctional/Detention Centers<sup>(2)</sup><sup>(3)</sup>":
//                		$conditionCategory = "<div class='" . $cat_class . "'>Correctional/Detention<br><span style='padding-left:10px;'>Centers<sup>(2)</sup><sup>(3)</sup><span></div>";
//                		break;
//                	case "Intermediate/Junior High Schools<sup>(20)</sup>":
//                		$conditionCategory = "<div class='" . $cat_class ."'>Intermediate/Junior High<br><span style='padding-left:10px;'>Schools<sup>(20)</sup><span></div>";
//                		break;
//                	case "Vehicle Maintenance/Storage Facilities<sup>(13)</sup><sup>(22)</sup><sup>(26)</sup>":
//                		$conditionCategory = "<div class='" .$cat_class . "'>Vehicle Maintenance/Storage<br><span style='padding-left:10px;'>Facilities<sup>(13)</sup><sup>(22)</sup><sup>(26)</sup><span></div>";
//                		break;
//                	case "Parks, Recreation, and Cultural Activities:":
//                		$conditionCategory = "<div class='" .$cat_class . "'>Parks, Recreation, and<br>Cultural Activities:</div>";
//                		break;
//                	case "Vehicle Maintenance/Storage Facilities":
//                		$conditionCategory = "<div class='" .$cat_class . "'>Vehicle Maintenance/Storage<br><span style='padding-left:10px;'>Facilities<span></div>";
//                		break;
//                	default:
//                		$conditionCategory = "<div class='" . $cat_class . "' >" . $row['category'] . "</div>";
//                		break;
//                }
//
                $conditionCategory = "<div class='" . $cat_class . "' >" . $row['category'] . "</div>";
			    echo "<tr><td class='text'>" . /*$row['category']*/ $conditionCategory . "</td>";
			    if(strpos($row['category'], ':')){
			    	$hyphen = "";
			    }else{
			    	$hyphen = "-";
			    }
			    foreach ($years as $year)
			        echo "<td><div>&nbsp;</div></td><td class='" . $amount_class . "'><div>" . (($row[$year]['amount'] > 0)?number_format($row[$year]['amount']):'&nbsp;' . $hyphen) . "</div></td>";			    
			    echo "<td>&nbsp;</td>";
			    echo "</tr>";
    		}
    ?>

    </tbody>
</table>
<div class="footnote">
    <p>(1) FY 2010 includes various other facilities with active enrollment relating to primary, intermediate and high school. This includes minischools, transportables and leased space. Also, multiple district schools may be operated in a single school building. </p>
  <p>Sources: Various City Agencies</p>
</div>
<?php 
	widget_data_tables_add_js($node);