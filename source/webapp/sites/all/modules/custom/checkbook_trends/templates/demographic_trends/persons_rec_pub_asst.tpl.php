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

$last_year = end($node->data)['fiscal_year'];
reset($node->data);

?>

<a class="trends-export" href="/export/download/trends_persons_rec_pub_asst_csv?dataUrl=/node/<?php echo $node->nid ?>">Export</a>

<table id="table_<?php echo widget_unique_identifier($node) ?>" class="<?php echo $node->widgetConfig->html_class ?>">
    <?php
    if (isset($node->widgetConfig->caption_column)) {
        echo '<caption>' . $node->data[0][$node->widgetConfig->caption_column] . '</caption>';
    }
    else if (isset($node->widgetConfig->caption)) {
        echo '<caption>' . $node->widgetConfig->caption . '</caption>';
    }
    ?>
    <thead>
    <tr class="first-row">
        <th colspan="3" class="centrig bb"><div>2002-<?= $last_year ?><br>(Average Annual Recipients)</div></th>
    </tr>
	<tr class="second-row">
        <th class="number" ><div class="trendCen">Year</div></th>
        <th class="number" ><div class="trendCen">Public<br>Assistance<br>(in thousands)</div></th>
        <th class="number" ><div class="trendCen">SSI<sup style="text-transform: lowercase;">(a)</sup></div></th>
    </tr>
    </thead>

    <tbody>

    <?php 
    		foreach( $node->data as $row){
			    echo "<tr><td class='number'><div  class='tdCen'>" . $row['fiscal_year'] . "</div></td>";
    			echo "<td class='number'><div  class='tdCen'>" . number_format($row['public_assistance']) . "</div></td>";
    			echo "<td class='number'><div  class='tdCen'>" . (($row['ssi'])?number_format($row['ssi']) : 'NA') . "</div></td>";
			    echo "</tr>";
    		}
    ?>

    </tbody>
</table>
    <div class="footnote"><p>(a) The SSI data is for December of each year.</p>
          <p>NA: Not Available.</p>
          <p>Sources: The City of New York, Human Resources Administration and the U.S. Social Security Administration.</p>
    </div>
<?php 
	widget_data_tables_add_js($node);
?>
