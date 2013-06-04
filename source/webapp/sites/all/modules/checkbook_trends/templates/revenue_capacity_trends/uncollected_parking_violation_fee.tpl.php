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

<a class="trends-export" href="/export/download/trends_uncollected_parking_violation_fee_csv?dataUrl=/node/<?php echo $node->nid ?>">Export</a>
<table class="fy-ait">
  <tbody>
  <tr>
    <td style="width:250px;padding:0;">&nbsp;</td>
    <td class="bb">Fiscal Year<br>(Amounts in Millions)</td>
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
        <th><div><br/></div></th>
        <?php
        foreach ($years as $year){
            echo "<th><div>&nbsp;</div></th>";
            echo "<th class='number'><div>" . $year . "</div></th>";
        }
        ?>
        <th>&nbsp;</th>
        </tr>
        </thead>

    <tbody>

    <?php
            $count = 1;
    		foreach( $table_rows as $row){
                $dollar_sign = ($count == 1 || $count ==  count($table_rows)) ? '<div class="dollarItem" >$</div>' : '';
    			$cat_class = "";
    			if( $row['highlight_yn'] == 'Y')
    				$cat_class = "highlight ";    			
    			$cat_class .= "level" . $row['indentation_level']; 
    			$amount_class = " number";
    			if($row['amount_display_type'])
    			    $amount_class .= " amount-" . $row['amount_display_type'];
                if($row['category'] == 'Write offs, Adjustments and Dispositions (b)')
                  $row['category'] = 'Write offs, Adjustments<br>and Dispositions (b)';
                if($row['category'] == 'Allowance for Uncollectible Amounts (c)')
                  $row['category'] = 'Allowance for<br>Uncollectible Amounts (c)';
                $row['category'] = str_replace('(a)','<sup>(a)</sup>', $row['category']);
                $row['category'] = str_replace('(b)','<sup>(b)</sup>', $row['category']);
                $row['category'] = str_replace('(c)','<sup>(c)</sup>', $row['category']);


                $conditionCategory = ($row['category']?$row['category']:'&nbsp;');
                switch($conditionCategory){
                	case "Write offs, Adjustments<br>and Dispositions<sup>(b)</sup>":
                		$conditionCategory = "<div class='" . $cat_class . "'>Write offs, Adjustments and <br><span style='padding-left:10px;'>Dispositions<sup>(b)</sup><span></div>";
                		break;
                	case "Less:":
                		$conditionCategory = "<div class='" . $cat_class ."'><span style='padding-left:10px;'>Less:</span></div>";
                		break;
                	case "Summonses Uncollected - June 30th":
                		$conditionCategory = "<div class='" .$cat_class . "'>Summonses Uncollected -<br><span style='padding-left:10px;'> June 30th<span></div>";
                		break;
                	default:
                		$conditionCategory = "<div class='" . $cat_class . "' >" . $row['category'] . "</div>";
                		break;
                }
                
                
			    echo "<tr><td>" . $conditionCategory . "</td>";
			    foreach ($years as $year){
                    echo "<td><div>&nbsp;</div></td>";
			        echo "<td class='" . $amount_class . "' >" . $dollar_sign . "<div>" . (($row[$year]['amount'] > 0) ? number_format($row[$year]['amount']) : '&nbsp;') . "</div></td>";
                }
                echo "<td>&nbsp;</td>";
			    echo "</tr>";
                $count++;
    		}
    ?>

    </tbody>
</table>
<div class="footnote">
     <p>(A) The summonses issued by various City agencies for parking violations are adjudicated and collected by the Parking Violations Bureau (PVB) of the City’s Department of Finance.</p>
     <p>(B) Proposed “write-offs” are in accordance with a write-off policy implemented by PVB for summonses determined to be legally uncollectible/unprocessable or for which all prescribed collection efforts are unsuccessful.</p>
     <p>(C) The Allowance for Uncollectible Amounts is calculated as follows: summonses which are over three years old are fully (100%) reserved and 35% of summonses less than three years old are reserved.</p>
     <p>Note: Data does not include interest reflected on the books of PVB.</p>
     <p>Source: The City of New York, Department of Finance, Parking Violations Bureau.</p>
</div>
<?php 
	widget_data_tables_add_js($node);
?>
