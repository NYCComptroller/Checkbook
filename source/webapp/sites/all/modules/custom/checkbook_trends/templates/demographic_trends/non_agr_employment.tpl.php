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
	$table_rows[$row['display_order']][$row['fiscal_year']]['amount'] = $row['amount'];
	$years[$row['fiscal_year']] = 	$row['fiscal_year'];
}
rsort($years);
?>

<h3 xmlns="http://www.w3.org/1999/html"><?php echo $node->widgetConfig->table_title; ?></h3>

<a class="trends-export" href="/export/download/trends_non_agr_employment_csv?dataUrl=/node/<?php echo $node->nid ?>">Export</a>
<table class="fy-ait">
  <tbody>
  <tr>
    <td width="240"><div>&nbsp;</div></td>
    <td class="bb"><div>1997-2011<br>(average annual employment in thousands)</div></td>
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
            <th><div>&nbsp;</div></th>
            <?php
            foreach ($years as $year){
                echo "<th></th>";
                if($year == 2011)
                    echo "<th class='number'><div>" . $year . "<sup>(b)</sup></div></th>";
                else
                    echo "<th class='number'><div>" . $year . "</div></th>";
            }
            ?>
            <th>&nbsp;</th>
        </tr>
    </thead>

    <tbody>

    <?php
        $i = 0;
        foreach($table_rows as $row){
            $cat_class = "";
            if($row['highlight_yn'] == 'Y')
                $cat_class = "highlight ";
            $cat_class .= "level" . $row['indentation_level'];
            $amount_class = "";
            if( $row['amount_display_type'] != "" )
            $amount_class = "amount-" . $row['amount_display_type'];
            $amount_class .= " number ";

            $row['category'] = (isset($row['category'])?$row['category']:'&nbsp;');
            
            $conditionCategory = $row['category'];
            switch($conditionCategory){
            	case "Transportation, Warehousing and Utilities":
            		$conditionCategory = "<div class='" . $cat_class . "'>Transportation, Warehousing<br><span style='padding-left:10px;'>and Utilities</span></div>";
            		break;
            	case "Percentage Increase (Decrease) from Prior Year":
            		$conditionCategory = "<div class='" . $cat_class ."'>Percentage Increase (Decrease)<br><span style='padding-left:10px;'>from Prior Year</span></div>";
            		break;
            	default:
            		$conditionCategory = "<div class='" . $cat_class . "' >" . str_replace('(a)','<sup>(a)</sup>',$row['category'])  . "</div>";
            		break;
            }
            
            
            echo "<tr><td class='text'>" . $conditionCategory . "</td>";

            foreach ($years as $year){
                if($i == count($table_rows)-1){
                    if($row[$year]['amount'] > 0)
                        echo "<td><div></div></td><td class='" . $amount_class . "'><div>" . $row[$year]['amount'] . "%</div></td>";
                    else if($row[$year]['amount'] < 0)
                       echo "<td><div></div></td><td class='" . $amount_class . "' ><div>(" . abs($row[$year]['amount']) . "%)</div></td>";
                    else
                        echo "<td><div></div></td><td class='" . $amount_class . "' ><div>" . "NA" . "</div></td>";
                }else{
                    if($row[$year]['amount'] > 0){
                        echo "<td><div></div></td><td class='" . $amount_class . "' ><div>" . number_format($row[$year]['amount']) . "</div></td>";
                    }else if($row[$year]['amount'] < 0){
                       echo "<td><div></div></td><td class='" . $amount_class . "' ><div>(" . number_format(abs($row[$year]['amount'])) . ")</div></td>";
                    }else if($row[$year]['amount'] == 0){
                         if(strpos($row['category'], ':'))
                            echo "<td><div></div></td><td class='" . $amount_class . "' ><div>" . '&nbsp;' . "</div></td>";
                         else
                            echo "<td><div></div></td><td class='" . $amount_class . "' ><div>" . '-' . "</div></td>";
                    }
                }
            }
            $i++;
            echo "<td>&nbsp;</td>";
            echo "</tr>";
        }
    ?>

    </tbody>
</table>
    <div class="footnote">
        <p>(A) Includes rounding  adjustments</p>
        <p>(B) Six month average</p>
        <p>NA:Not Available</p>

        <p>Notes: This schedule is provided in lieu of a schedule of principal employees because it provides more meaningful information. Other than the City of New York, no single
employer employs more than 2 percent of total nonagricultural employees.</p>
<p>Data are not seasonally adjusted.</p>
<p>Source: New York State Department of Labor, Division of Research and Statistics.</p>
    </div>
<?php
	widget_data_tables_add_js($node);
?>
