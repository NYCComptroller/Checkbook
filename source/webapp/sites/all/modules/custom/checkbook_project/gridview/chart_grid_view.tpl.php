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
switch($node->widgetConfig->gridConfig>domain){
 
	case "spending":
		include_once('spending_title.php');
		break;
	case "contracts":
		include_once('contracts_title.php');
		break;
		
}

include_once('page_title.php');
include_once('export_link.php');
?>

<table id="table_<?php echo widget_unique_identifier($node);?>" class="<?php echo $node->widgetConfig->gridConfig->html_class; ?>">
  <thead>
    <tr>
    <?php 
    	foreach($node->widgetConfig->gridConfig->table_columns as $column){
			echo "<th class='" . $column->columnType . "'><div><span>" . $column->labelAlias . "</div></span></th>";
		}
    ?>
      <th>&nbsp</th>
    </tr>
  </thead>
  <tbody>
  <?php
  
        if (isset($node->widgetConfig->gridConfig->data) && is_array($node->widgetConfig->gridConfig->data)) {
            foreach ($node->widgetConfig->gridConfig->data as $datarow) {
              echo "<tr>";
              $index = 0;
              while($index <count($node->widgetConfig->gridConfig->table_columns)){
				if($node->widgetConfig->gridConfig->table_columns[$index]->formatType == "amount"){
					echo "<td>".$datarow[$index]."</td>";
				}else{
					echo "<td><div class='" .$node->widgetConfig->gridConfig->table_columns[$index]->columnType ."'>".$datarow[$index]."</div></td>";
				}
				$index +=1;	
			  }             
              echo "<td>&nbsp</td>";
              echo "</tr>";
            }
        }
  ?>
  </tbody>
</table>
<?php

	$index = 0;
	$aoColumnDefs = '';
	foreach($node->widgetConfig->gridConfig->table_columns as $column){
		if($column->formatType == 'amount'){
			$aoColumnDefs .= '
		                        {
		                        	"aTargets": [' . $index.'],
		                            "aExportFn":"function",
									"mDataProp": function ( source, type, val ) {
										if (type == "set") {
										source.total_amount = val;
										source.total_amount_display =  "<div>" + custom_number_format(val) + "</div>";
										return;
										}else if (type == "display") {
										return source.total_amount_display;
										}
										return source.total_amount;
									},
		 							"sClass":"' . $column->columnType .'",
		                            "asSorting": [ "asc","desc" ]
		                        },
		                    ';

		}else{

			$aoColumnDefs .= '
		                        {
		                        	"aTargets": [' . $index.'],
		                            "sClass":"' . $column->columnType .'",
		                            "asSorting": [ "asc","desc" ]
		                        },
		                    ';
		}
		$index +=1;
	}
	
	$aoColumnDefs .= '
	{
	"aTargets": [3],
	"sWidth":"15px"
	}
    ';		
    $dataTableOptions ='
                    {
                        "bFilter":false,
                        "bInfo":false,
                        "bLengthChange":false,
                        "iDisplayLength":10,
                        "aaSorting":[[ ' . ($index - 1)  .  ' ,"desc"]],
                        "bPaginate": false,
                        "sAltAjaxSource":"'. check_plain($_GET['q']) .'",
            			"fnDrawCallback"  :  function( oSettings ) {
            			addPaddingToDataCells(this);
            			},                                                
                        "aoColumnDefs": [
                            ' . $aoColumnDefs . '

                        ]
                    }
                    ';
  
    
$node->widgetConfig->gridConfig->dataTableOptions = $dataTableOptions;
widget_highcharts_add_datatable_js($node);
