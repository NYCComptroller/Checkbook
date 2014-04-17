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
<div>
<div class="tableHeader"><h3>Vendor Infromation</h3> <span class="contCount">Number of Vendors: <?php echo count($node->vendors_list);?> </span></div>

<table class="dataTable outerTable oge-cta-vendor-info">
    <thead>
    <tr>
      <th class="text"><?php echo WidgetUtil::generateLabelMapping("vendor_name"); ?></th>
      <th class="number"><div><span><?php echo $node->widget_count_label; ?></span></div></th>
      <th class="number"><?php echo WidgetUtil::generateLabelMapping("spent_to_date"); ?></th>
      <th class="text endCol"><?php echo WidgetUtil::generateLabelMapping("vendor_address"); ?></th>
    </tr>
    </thead>
   <tbody>
   <?php 
	$vendor_cont_count = array();   
   	foreach($node->vendor_contracts_count as $vendor_cont){
		$vendor_cont_count[$vendor_cont['vendor_id']]['count'] = $vendor_cont['count'];
		$vendor_cont_count[$vendor_cont['vendor_id']]['count'] = $vendor_cont['count'];
	}

    
   	foreach($node->vendors_list as $vendor){
		$spending_link = "/spending/transactions/vendor/" . $vendor['vendor_id'] . "/datasource/checkbook_oge/newwindow";
		echo "<tr>";
		echo "<td class='text'><div><a href='/contracts_landing/status/A/year/" . _getCurrentYearID() . "/yeartype/B/agency/" . $vendor['agency_id'] .
				 "/datasource/checkbook_oge/vendor/" . $vendor['vendor_id']  . "?expandBottomCont=true'>" . 
								$vendor['vendor_name']  . "</div></a></td>";
		echo "<td class='number'><div>" . $vendor_cont_count[$vendor['vendor_id']]['count']  . "</div></td>";
		echo "<td class='number'><div><a target='_new' href='" . $spending_link . "'>" . custom_number_formatter_format($vendor['check_amount_sum'], 2, '$')  . "</a></div></td>";
		echo "<td class='text endCol'><div>" . $vendor['address']  . "</div></td>";
		echo "</tr>";
	}
   ?>
   
 </tbody>
 </table>  
</div>
<?php

