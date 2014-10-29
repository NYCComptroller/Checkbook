<?php 

	_widget_highcharts_include_plugin();
	
	$download_link = "/mwbe_agency_grading_csv/year/" . _getRequestParamValue("year") . "/yeartype/" .  _getRequestParamValue("yeartype") ;
	
?>
<div class="download_link"><a href="<?php echo $download_link; ?>"><span class="export">Export</span></a></div>

<div class="checkbook-grading-left">
<table id="grading_table">
	<thead>
		<tr id="scroll_wrapper_head">
			<th><div><span>Agency</span></div></th>
			<th><div><span>Spending<br/> Chart</span></div></th>
			<th><div><span>YTD<br/>Spending</span></div></th>
			<th>&nbsp</th>
		</tr>
	</thead>
	<tbody>
		<?php 
			$id = 0;
			foreach($left_agencies_data as $row){
				$agency = $row['agency_name'];
				$chart = theme('mwbe_agency_grading_row_chart',array('id'=>$id, 'data_row'=>$row['data_row']));
				if( $row['spending_amount'] > 0){
					$link = "/spending_landing/year/" . _getRequestParamValue("year") . 
							"/yeartype/" .  _getRequestParamValue("yeartype") . "/agency/" .  $row["agency_id"] . "/dashboard/mp/mwbe/" . MappingUtil::$total_mwbe_cats; 				
					echo "<tr>
						<td><div><a href=\"" .  $link . "\">" . $agency . "</a></div></td>
						<td>" . $chart . "  </td>
						<td>" . $row['spending_amount'] . "  </td>
						<td></td>
						</tr>	
					"	;
				}
				
				$id +=1;
			}		
		?>
	</tbody>
	
</table>
</div>



<script>

    var oTable;
    jQuery(document).ready(function() {
        oTable = jQuery('#grading_table').dataTable(
        		{
        			"bFilter": false,
        	        "bPaginate": true,
        	        "iDisplayLength":25,
        	        "sPaginationType":"full_numbers",
        	        "bLengthChange": false,
        	        "sDom":"<pr><t><ip>",
        	        "oLanguage": {
        	                "sInfo": "Displaying transactions _START_ - _END_ of _TOTAL_",
        	                "sProcessing":"<img src='/sites/all/themes/checkbook/images/loading_large.gif' title='Processing...'/>"
        	        },
        	        "bInfo": true,
        	        "aaSorting":[[2,"desc"]],
        	        "fnInitComplete":function () { fnCustomInitComplete();},  
        	        "sScrollX": "100%",      	        
        	        "aoColumnDefs": [
        	                         {
     		                        	"aTargets": [0],
     		                        	"sClass":"text"
     		                         },
        	                         {
      		                        	"aTargets": [1],
      		                        	"asSorting": [  ],
     		                        	"sClass":"text"
      		                         },
      		                         {
      		                        	"aTargets": [2],
     		                        	"sClass":"number",
	      		      					"aExportFn":"function",
	      		      					"mDataProp": function ( source, type, val ) {
	      		      							if (type == "set") {
	      		      							source.total_contracts = val;
	      		      							source.total_contracts_display =  "<div>" + custom_number_format(val) + "</div>";
	      		      							return;
	      		      					}else if (type == "display") {
	      		      						return source.total_contracts_display;
	      		      					}
	      		      					return source.total_contracts;
	      		      					}	      		      						      		      					
      		                         }
        	                        ]
        		}
                );
	} );


    function fnCustomInitComplete() {
        var topSpacing = <?php echo (user_is_logged_in() ? 66 : 0);  ?>

        var tableOffsetTop = jQuery('#grading_table').offset().top;
        var tableHeight = jQuery('#grading_table').height();
        var docHeight = jQuery(document).height();
        var bottomSpacing = docHeight - (tableOffsetTop + tableHeight) ;
 //       bottomSpacing = 500;
//        alert(docHeight);
//        alert(tableOffsetTop);
//        alert(tableHeight);
//		alert(topSpacing);
//		alert(bottomSpacing);
        jQuery('.dataTables_scrollHead').sticky({ getWidthFrom:'#scroll_wrapper_head',topSpacing: <?php echo "topSpacing"; ?>, bottomSpacing: <?php echo "bottomSpacing"; ?>});

    }	
</script>





