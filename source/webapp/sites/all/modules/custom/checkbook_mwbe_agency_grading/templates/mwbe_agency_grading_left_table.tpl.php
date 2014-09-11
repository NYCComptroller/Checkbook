<?php 

	_widget_highcharts_include_plugin();

?>
<div class="checkbook-grading-left">
<table>
	<th>
		<tr>
			<td class="sortable"><a href="">Agency</a></td>
			<td>Spending Chart</td>
			<td class="sortable"><a href="">Spending</a></td>
		</tr>
	</th>
	
	<tbody>
		<?php 
			$id = 0;
			foreach($left_agencies_data as $row){
				$agency = $row['agency_name'];
				$chart = theme('mwbe_agency_grading_row_chart',array('id'=>$id, 'data_row'=>$row['data_row']));
				$spending = $row['agency'];
				$spending = '0';
				
				echo "<tr>
					<td>" . $agency . " </td>
					<td>" . $chart . "  </td>
					<td>" . custom_number_formatter_format($row['spending_amount'],2,'$') . "  </td>
					</tr>	
				"	;
				
				$id +=1;
			}		
		?>
	</tbody>
	
</table>

</div>