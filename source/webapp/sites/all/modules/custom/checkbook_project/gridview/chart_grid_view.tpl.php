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

switch($node->widgetConfig->gridConfig->domain){

	case "spending":
		include_once('spending_title.php');
		break;
	case "contracts":
		include_once('contracts_title.php');
		break;
  case "payroll":
    include_once('payroll_title.php');
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
      <th>&nbsp;</th>
    </tr>
  </thead>
  <tbody>
  <?php


        if (isset($node->widgetConfig->gridConfig->data) && is_array($node->widgetConfig->gridConfig->data)) {
            foreach ($node->widgetConfig->gridConfig->data as $datarow) {
              echo "<tr>";
              $index = 0;
              while($index < count($node->widgetConfig->gridConfig->table_columns)){
				if($node->widgetConfig->gridConfig->table_columns[$index]->formatType == "amount" || $node->widgetConfig->gridConfig->table_columns[$index]->formatType == "number"
                || $node->widgetConfig->gridConfig->table_columns[$index]->formatType == "month" ||$node->widgetConfig->gridConfig->table_columns[$index]->formatType == "monthfy"){
					echo "<td>".$datarow[$index]."</td>";
				}
				else{
					echo "<td><div class='" .$node->widgetConfig->gridConfig->table_columns[$index]->columnType ."'>".$datarow[$index]."</div></td>";
				}

				$index +=1;
			  }
              echo "<td>&nbsp;</td>";
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
										source.total_amount' . $index . ' = val;
										source.total_amount_display' . $index . ' =  "<div>" + custom_number_format(val) + "</div>";
										return;
										}else if (type == "display") {
										return source.total_amount_display' . $index . ';
										}
										return source.total_amount' . $index . ';
									},
		 							"sClass":"' . $column->columnType .'",
		                            "asSorting": [ "asc","desc" ]
		                        },
		                    ';

		}elseif($column->formatType == 'number'){

			$aoColumnDefs .= '	{
				"aTargets": [' . $index.'],
					"aExportFn":"function",
					"mDataProp": function ( source, type, val ) {
							if (type == "set") {
							source.total_contracts' . $index . ' = val;
							source.total_contracts_display' . $index . ' =  "<div>" + addCommas(val) + "</div>";
							return;
					}else if (type == "display") {
						return source.total_contracts_display' . $index . ';
					}
					return source.total_contracts' . $index . ';
					},
					"sClass":"' . $column->columnType .'",
					"asSorting": [ "desc", "asc" ]
					},
				';
		}elseif($column->formatType == 'month' && RequestUtilities::get('yeartype') == 'C'){

			$aoColumnDefs .= '	{
				"aTargets": [' . $index.'],
					"aExportFn":"function",
					"mDataProp": function ( source, type, val ) {
							if (type == "set") {
            				var months = {
						            January: 12, February: 11, March: 10, April: 9, May: 8, June: 7,
						            July: 6, August: 5, September: 4, October: 3, November:2, December:1
						        };
							source.month' . $index . ' = months[val];
							source.month_display' . $index . ' =  "<div class=\"text\">" + val + "</div>";
							return;
					}else if (type == "display") {
						return source.month_display' . $index . ';
					}
					return source.month' . $index . ';
					},
					"sClass":"' . $column->columnType .'",
					"asSorting": [ "desc", "asc" ]
					},
				';
		}elseif(($column->formatType == 'monthfy' || $column->formatType == 'month') && RequestUtilities::get('yeartype') == 'B'){

			$aoColumnDefs .= '	{
				"aTargets": [' . $index.'],
					"aExportFn":"function",
					"mDataProp": function ( source, type, val ) {
						if (type == "set") {
            				var months_fy = {
									January: 6, February: 5, March: 4, April: 3, May: 2, June: 1,
						            July: 12, August: 11, September: 10, October: 9, November:8, December:7
						        };
							source.month' . $index . ' = months_fy[val];
							source.month_display' . $index . ' =  "<div class=\"text\">" + val + "</div>";
							return;
						}else if (type == "display") {
							return source.month_display' . $index . ';
						}
						return source.month' . $index . ';
					},
					"sClass":"' . $column->columnType .'",
					"asSorting": [ "desc", "asc" ]
					},
				';
		}elseif($column->formatType == 'padding'){

			$aoColumnDefs .= '
				{
					"aTargets": [' . $index . '],
					"sWidth":"' . $column->sWidth . '"
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
	"aTargets": [' . $index . '],
	"sWidth":"15px"
	}
    ';

	$sortOrder = (isset($node->widgetConfig->gridConfig->sortOrder))? $node->widgetConfig->gridConfig->sortOrder:"desc";
	$sortColumn = (isset($node->widgetConfig->gridConfig->sortColumn))? $node->widgetConfig->gridConfig->sortColumn:$index - 1;
    $dataTableOptions ='
                    {
                        "bFilter":false,
                        "bInfo":false,
                        "bLengthChange":false,
                        "iDisplayLength":10,
                        "aaSorting":[[ ' . ($sortColumn)  .  ' ,"' . $sortOrder . '"]],
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
