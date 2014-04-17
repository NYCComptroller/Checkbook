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


//log_error($node->results_contract_history);
//log_error($node->results_spending);

$vendor_contract_summary = array();
$vendor_contract_yearly_summary = array();

foreach($node->results_contract_history as $contract_row){
	if(!isset($vendor_contract_summary[$contract_row['vendor_name']]['current_amount'])){
		$vendor_contract_summary[$contract_row['vendor_name']]['current_amount'] = $contract_row['current_amount_commodity_level'];
	}
	if(!isset($vendor_contract_summary[$contract_row['vendor_name']]['original_amount'])){
		$vendor_contract_summary[$contract_row['vendor_name']]['original_amount'] = $contract_row['original_amount'];
	}
	
	if(!isset($vendor_contract_yearly_summary[$contract_row['vendor_name']][$contract_row['document_fiscal_year']]['current_amount'])){
		$vendor_contract_yearly_summary[$contract_row['vendor_name']][$contract_row['document_fiscal_year']]['current_amount'] = $contract_row['current_amount_commodity_level'];
	}
	if(!isset($vendor_contract_yearly_summary[$contract_row['vendor_name']][$contract_row['document_fiscal_year']]['original_amount'])){
		$vendor_contract_yearly_summary[$contract_row['vendor_name']][$contract_row['document_fiscal_year']]['original_amount'] = $contract_row['original_amount'];
	}
	$vendor_contract_yearly_summary[$contract_row['vendor_name']][$contract_row['document_fiscal_year']]['no_of_mods'] +=1;	
}



$vendor_spending_yearly_summary = array();
foreach($node->results_spending as $spending_row){
	$vendor_spending_yearly_summary[$spending_row['vendor_name']][$spending_row['fiscal_year']]['no_of_trans'] += 1;
	$vendor_spending_yearly_summary[$spending_row['vendor_name']][$spending_row['fiscal_year']]['amount_spent'] += $spending_row['check_amount'];
	$vendor_contract_summary[$spending_row['vendor_name']]['check_amount'] += $spending_row['check_amount'];
}

//log_error($vendor_contract_yearly_summary);
?>

<div class="oge-cta-details " >
<h3>SPENDING BY PRIME VENDOR</h3>
  <table class="dataTable cta-history outerTable">
    <thead>
    <tr>
      <th class="text"><?php echo WidgetUtil::generateLabelMapping("prime_vendor_name"); ?></th>
      <th class="number"><?php echo WidgetUtil::generateLabelMapping("original_amount"); ?></th>
      <th class="number"><?php echo WidgetUtil::generateLabelMapping("current_amount"); ?></th>
      <th class="number endCol"><?php echo WidgetUtil::generateLabelMapping("amount_spent"); ?></th>
    </tr>
    </thead>
   <tbody>
   <?php 
    $count1 =  0;
    $hide_text1 = "";
    $open1 = "";
	foreach($vendor_contract_summary as $vendor=>$vendor_summary){
		if ($count1 % 2 == 0) {
			$class1 = "class=\"even outer\"";
		}
		else {
			$class1 = "class=\"odd outer\"";
		}
		$count1 +=1;
		echo "<tr " . $class1 . ">";
		echo "<td class='text'><div><a class='showHide " . $open1 . " expandTwo'></a>"  . $vendor . "</div></td>";
		echo "<td class='number'><div>"  . custom_number_formatter_format($vendor_summary['current_amount'], 2, '$') . "</div></td>";
		echo "<td class='number'><div>"  . custom_number_formatter_format($vendor_summary['original_amount'] , 2, '$'). "</div></td>";
		echo "<td class='number endCol'><div>"  . custom_number_formatter_format($vendor_summary['check_amount'], 2, '$') . "</div></td>" ;
		echo "</tr>";
		
  ?>
				
			<tr class='showHide' <?php echo $hide_text1; ?> >
			<td colspan='4' >
			<div>
			<!--    start  CONTRACT HISTORY BY PRIME VENDOR --> 
			<h3>CONTRACT HISTORY BY PRIME VENDOR</h3>
			<table class='col5 dataTable outerTable'>
		

			<thead>
			    <tr class="outer">
			      <th class="text"><?php echo WidgetUtil::generateLabelMapping("fiscal_year"); ?></th>
			      <th class="number"><?php echo WidgetUtil::generateLabelMapping("no_of_mod"); ?></th>
			      <th class="number"><?php echo WidgetUtil::generateLabelMapping("current_amount"); ?></th>
			      <th class="number"><?php echo WidgetUtil::generateLabelMapping("original_amount"); ?></th>
			      <th class="number endCol"><?php echo WidgetUtil::generateLabelMapping("increase_decrease"); ?></th>	      
			    </tr>
		    </thead><tbody>
				
			<?php 
				$open1 = "open";
				$hide_text1 = "style=display:none";
				$hide_text2 = "";
    			$open2 = "";
				$count2 = 0;
				foreach($vendor_contract_yearly_summary[$vendor] as $year=>$results_contract_history_fy){
					if ($count2 % 2 == 0) {
						$class2 = "class=\"even  outer\"";
					}
					else {
						$class2 = "class=\"odd  outer\"";
					}
					$count2 +=1;
					echo "<tr " . $class2 .">";
					echo "<td class='text'><div><a class='showHide " . $open2 . "' ></a>FY "  . $year . "</div></td>";
					echo "<td class='number'><div>"  . $results_contract_history_fy['no_of_mods'] . "</div></td>";
					echo "<td class='number'><div>"  . custom_number_formatter_format($results_contract_history_fy['current_amount'], 2, '$') . "</div></td>";
					echo "<td class='number'><div>"  . custom_number_formatter_format($results_contract_history_fy['original_amount'] , 2, '$'). "</div></td>";
					echo "<td class='number endCol'><div>"  . custom_number_formatter_format($results_contract_history_fy['current_amount'] - $results_contract_history_fy['original_amount'], 2, '$') . "</div></td>";
					echo "</tr>";
					
					
					/// start level 3
						echo "<tr class='showHide' " . $hide_text2 . ">";
						echo "<td colspan='5' >";
						echo "<div class='scroll'>";
						echo "<table class='sub-table col7 dataTable'>";
	
						echo "<thead>
	                  	<tr>
	                    <th class='text thStartDate'>".WidgetUtil::generateLabelMapping("start_date")."</th>
	                    <th class='text thEndDate'>".WidgetUtil::generateLabelMapping("end_date")."</th>
	                    <th class='text purpose'>".WidgetUtil::generateLabelMapping("contract_purpose")."</th>
	                    <th class='number thVNum'>".WidgetUtil::generateLabelMapping("commodity_line")."</th>
	                    <th class='number thCurAmt'>".WidgetUtil::generateLabelMapping("current_amount")."</th>
	                    <th class='number thOrigAmt'>".WidgetUtil::generateLabelMapping("original_amount")."</th>
	                    <th class='number thIncDec'>".WidgetUtil::generateLabelMapping("increase_decrease")."</th>
	                  	</tr></thead><tbody>";
						$open2 = "open";
						$hide_text2 = "style=display:none";
						foreach($node->results_contract_history as $contract_history){
							$count3 = 0;
							if($contract_history['document_fiscal_year'] == $year && $contract_history['vendor_name'] == $vendor){
								if ($count3 % 2 == 0) {
									$class3 = "class=\"even  \"";
								}
								else {
									$class3 = "class=\"odd  \"";
								}
								$count3 +=1;
								echo "
					                  	<tr " . $class3 ." >
					                    <td class='text thStartDate'><div>".$contract_history['start_date']."</div></td>
					                    <td class='text thEndDate'><div>".$contract_history['end_date']."</div></td>
					                    <td class='text purpose'><div>".$contract_history['description']."</div></td>
					                    <td class='number thVNum'><div>".$contract_history['fms_commodity_line']."</div></td>
					                    <td class='number thCurAmt'><div>".custom_number_formatter_format($contract_history['current_amount_commodity_level'], 2, '$')."</div></td>
					                    <td class='number thOrigAmt'><div>".custom_number_formatter_format($contract_history['original_amount'], 2, '$')."</div></td>
					                    <td class='number thCommodLvl endCol'><div>".custom_number_formatter_format($contract_history['current_amount_commodity_level']-$contract_history['original_amount'], 2, '$')."</div></td>
					                  	</tr>";
							}

						}
						echo "</tbody></table>
						</div>
						</td>
						</tr>"	;
					/// end level 3
				}		
			echo "</tbody></table>
			";
			?>
		<!--  // end CONTRACT HISTORY BY PRIME VENDOR   -->	
		
		
		
		<!--    start  Spendinng HISTORY BY PRIME VENDOR --> 		

			<h3>SPENDING TRANSACTIONS BY PRIME VENDOR</h3>
			<table class='col3 dataTable outerTable'>
		

			<thead>
			    <tr  class="outer">
			      <th class="text"><?php echo WidgetUtil::generateLabelMapping("fiscal_year"); ?></th>
			      <th class="number"><?php echo WidgetUtil::generateLabelMapping("no_of_transactions"); ?></th>
			      <th class="number endCol"><?php echo WidgetUtil::generateLabelMapping("amount_spent"); ?></th>
			    </tr>
		    </thead><tbody>
				
			<?php 
				$hide_text2 = "";
				$open2 = "";
				$count2 = 0;
				if(count($vendor_spending_yearly_summary[$vendor]) > 0){
					foreach($vendor_spending_yearly_summary[$vendor] as $year=>$results_spending_history_fy){
						if ($count2 % 2 == 0) {
							$class2 = "class=\"even  outer\"";
						}
						else {
							$class2 = "class=\"odd  outer\"";
						}
						$count2 +=1;
						echo "<tr " . $class2 .">";
						echo "<td  class='text'><div><a class='showHide " . $open2 . " '></a>FY "  . $year . "</div></td>";
						echo "<td class='number'><div>"  . $results_spending_history_fy['no_of_trans'] . "</div></td>";
						echo "<td class='number endCol'><div>"  . custom_number_formatter_format($results_spending_history_fy['amount_spent'], 2, '$') . "</div></td>";
						echo "</tr>";
						
						
						/// start level 3
							echo "<tr class='showHide' "  . $hide_text2 . " >";
							echo "<td colspan='3' >";
							echo "<div class='scroll'>";
							echo "<table class='sub-table col5 dataTable'>";
		
							echo "<thead>
		                  	<tr>
		                    <th class='text thStartDate'>".WidgetUtil::generateLabelMapping("start_date")."</th>
		                    <th class='number thCurAmt'>".WidgetUtil::generateLabelMapping("check_amount")."</th>
		                    <th class='text purpose'>".WidgetUtil::generateLabelMapping("expense_category")."</th>
		                    <th class='text thVNum'>".WidgetUtil::generateLabelMapping("agency_name")."</th>
		                    <th class='text thCurAmt'>".WidgetUtil::generateLabelMapping("dept_name")."</th>
		                  	</tr></thead><tbody>";
							$open2 = "open";
							$hide_text2 = "style=display:none";
								foreach($node->results_spending as $contract_spending){
									$class3 = 0;
									if($contract_spending['fiscal_year'] == $year && $contract_spending['vendor_name'] == $vendor){
										if ($count3 % 2 == 0) {
											$class3 = "class=\"even \"";
										}
										else {
											$class3 = "class=\"odd \"";
										}
										$count3 +=1;
										echo "
							                  	<tr " . $class3 . ">
							                    <td class='text '><div>".$contract_spending['issue_date']."</div></td>
							                    <td class='number'><div>".custom_number_formatter_format($contract_spending['check_amount'], 2, '$')."</div></td>
							                    <td class='text '><div>".$contract_spending['expenditure_object_name']."</div></td>
							                    <td class='text '><div>".$contract_spending['agency_name']."</div></td>
							                    <td class='text endCol'><div>".$contract_spending['department_name']."</div></td>
							                  	</tr>";
									}
		
								}
							
							echo "</tbody></table>
							</div>
							</td>
							</tr>"	;
						/// end level 3
					}	
				}else {
					echo '<tr class="odd">';
					echo '<td class="dataTables_empty" valign="top" colspan="3">' .
							'<div id="no-records-datatable" class="clearfix">
					                 <span>No Matching Records Found</span>
					           </div>' . '</td>';
					echo '</tr>';
				}
			echo "</tbody></table>
			</div>
			</td>
			</tr>";
			?>
		<!--  // end CONTRACT HISTORY BY PRIME VENDOR   -->	
		
	<?php 			
	}
   ?>
 		
 		
   </tbody>
   </table> 

</div>



