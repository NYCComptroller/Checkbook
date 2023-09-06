<?php
/**
 * This file is part of the Checkbook NYC financial transparency software.
 *
 * Copyright (c) 2012 – 2023 New York City
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Drupal\widget_config\Utilities;

use Drupal\checkbook_infrastructure_layer\Utilities\RequestUtilities;
use Drupal\checkbook_project\WidgetUtilities\WidgetUtil;

class ChartGrid
{
  public static function chartGridDisplay($node){
    $output = "<thead><tr>";
    $yearType = RequestUtilities::get('yeartype');
    	foreach($node->widgetConfig->gridConfig->table_columns as $column){
        $colTitle = WidgetUtil::generateLabelMappingNoDiv($column->labelAlias);
        $colTitle = $colTitle ?? $column->labelAlias;
			  $output .= "<th class='" . $column->columnType . "'><div><span>" . $colTitle . "</div></span></th>";
		}

      $output .= "<th>&nbsp;</th>
    </tr>
  </thead>
  <tbody>";
        if (isset($node->widgetConfig->gridConfig->data) && is_array($node->widgetConfig->gridConfig->data)) {
            foreach ($node->widgetConfig->gridConfig->data as $datarow) {
              $output .= "<tr>";
              $index = 0;
              while($index < count($node->widgetConfig->gridConfig->table_columns)){
				if($node->widgetConfig->gridConfig->table_columns[$index]->formatType == "amount" || $node->widgetConfig->gridConfig->table_columns[$index]->formatType == "number"
                || $node->widgetConfig->gridConfig->table_columns[$index]->formatType == "month" ||$node->widgetConfig->gridConfig->table_columns[$index]->formatType == "monthfy"){
          $output .= "<td>".$datarow[$index]."</td>";
				}
				else{
          $output .= "<td><div class='" .$node->widgetConfig->gridConfig->table_columns[$index]->columnType ."'>".$datarow[$index]."</div></td>";
				}

				$index +=1;
			  }
              $output .= "<td>&nbsp;</td>";
              $output .= "</tr>";
            }
        }
    $output .= "</tbody>
</table>";

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
		}elseif(($column->formatType == 'monthfy' || $column->formatType == 'month') && $yearType == 'B'){
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
                        "sAltAjaxSource":"'.\Drupal::request()->query->get('q') .'",
            			"fnDrawCallback"  :  function( oSettings ) {
            			addPaddingToDataCells(this);
            			},
                        "aoColumnDefs": [
                            ' . $aoColumnDefs . '

                        ]
                    }
                    ';
    $node->widgetConfig->gridConfig->dataTableOptions = $dataTableOptions;
    return $output;
  }

}
