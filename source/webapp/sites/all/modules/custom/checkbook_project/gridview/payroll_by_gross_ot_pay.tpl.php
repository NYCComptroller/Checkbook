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

$isByGrossPay = ($node->nid == 324 || $node->nid == 331);

$hidePrevLabel = (isset($node->widgetConfig->chartConfig->series[0]->showInLegend) && ($node->widgetConfig->chartConfig->series[0]->showInLegend == false) );

$SeriesPreviousYearLabel = $node->widgetConfig->chartConfig->series[0]->name;
$SeriesCurrentYearLabel = $node->widgetConfig->chartConfig->series[1]->name;

include_once('payroll_title.php');
include_once('page_title.php');
include_once('export_link.php');
?>

<table id="table_<?php echo widget_unique_identifier($node) ?>" class="<?php echo $node->widgetConfig->gridConfig->html_class ?>">
    <thead>
      <tr>
            <th class='text'><?php echo WidgetUtil::generateLabelMapping("month");?></th>
            <?php echo ( $hidePrevLabel ? "" : "<th class='number'><div><span>$SeriesPreviousYearLabel</span></div></th>");?>
            <th class='number'><div><span><?php echo $SeriesCurrentYearLabel;?></span></div></th>
            <th>&nbsp;</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $months = array();
    if (isset($node->data) && is_array($node->data)) {
        $cnt = 1;
        foreach ($node->data as $datarow) {
            $months[] = $datarow['month_month_month_name'];
            echo "<tr>";
            echo '<td>' . $cnt . '</td>';
            echo ( $hidePrevLabel ? '' : ('<td>' . ($isByGrossPay ? $datarow['previous_gross_pay'] : $datarow['previous_overtime_pay']) . '</td>') );
            echo '<td>' . ($isByGrossPay ? $datarow['current_gross_pay'] : $datarow['current_overtime_pay']) . '</td>';
            echo '<td>&nbsp;</td>';
            echo "</tr>";
            $cnt++;
        }
    }
    ?>
    </tbody>
</table>
<?php
echo eval($node->widgetConfig->gridConfig->footer);

if(!$hidePrevLabel){
$dataTableOptions ='
                    {
                        "bFilter":false,
                        "bInfo":false,
                        "bLengthChange":false,
                        "iDisplayLength":12,
                        "aaSorting":[[0,"asc"]],
                        "bPaginate": false,
                        "sAltAjaxSource":"'. check_plain($_GET['q']) .'",
                        "fnDrawCallback"  :  function( oSettings ) {
            			    addPaddingToDataCells(this);
            			},
                        "aoColumnDefs": [
                            {
                                "aTargets": [0],
                                "mDataProp": function ( source, type, val ) {
                                    if (type == "set") {
                                        var monthList = ["'. implode("\",\"",$months) .'"];
                                        source.month = val;
                                        source.month_display = monthList[(val-1)];
                                        return;
                                    }else if (type == "display") {
                                        return "<div>"+source.month_display+"</div>";
                                    }else if (type == "sort") {
                                        return source.month;
                                    }
                                    return source.month;
                                },
                                "sClass":"text",
                                "asSorting": [ "asc","desc" ],
                                "sWidth":"180px"
                            },
                            {
                                "aTargets": [1],
                                "aExportFn":"function",
                                "mDataProp": function ( source, type, val ) {
                                    if (type == "set") {
                                        source.current_gross_pay = val;
                                        source.current_gross_pay_display = "<div>" + custom_number_format(val) + "</div>";
                                        return;
                                    }else if (type == "display") {
                                        return source.current_gross_pay_display;
                                    }
                                    return source.current_gross_pay;
                                },
                                "sClass":"number",
                                "asSorting": [ "desc", "asc" ],
                                "sWidth":"300px"
                            },
                            {
                                "aTargets": [2],
                                "aExportFn":"function",
                                "mDataProp": function ( source, type, val ) {
                                    if (type == "set") {
                                        source.previous_gross_pay = val;
                                        source.previous_gross_pay_display = "<div>" + custom_number_format(val) + "</div>";
                                        return;
                                    }else if (type == "display") {
                                        return source.previous_gross_pay_display;
                                    }
                                    return source.previous_gross_pay;
                                },
                                "sClass":"number",
                                "asSorting": [ "desc", "asc" ]
                            },
                            {
                              "aTargets": [3],
                              "sWidth":"15px"
                            }

                        ]
                    }
                    ';
}else{
    $dataTableOptions ='
                    {
                        "bFilter":false,
                        "bInfo":false,
                        "bLengthChange":false,
                        "iDisplayLength":12,
                        "aaSorting":[[0,"asc"]],
                        "bPaginate": false,
                        "sAltAjaxSource":"'. $_GET['q'] .'",
                        "fnDrawCallback"  :  function( oSettings ) {
            			    addPaddingToDataCells(this);
            			},
                        "aoColumnDefs": [
                            {
                                "aTargets": [0],
                                "mDataProp": function ( source, type, val ) {
                                    if (type == "set") {
                                        var monthList = ["'. implode("\",\"",$months) .'"];
                                        source.month = val;
                                        source.month_display = monthList[(val-1)];
                                        return;
                                    }else if (type == "display") {
                                        return "<div>"+source.month_display+"</div>";
                                    }else if (type == "sort") {
                                        return source.month;
                                    }
                                    return source.month;
                                },
                                "sClass":"text",
                                "asSorting": [ "asc","desc" ]
                            },
                            {
                                "aTargets": [1],
                                "aExportFn":"function",
                                "mDataProp": function ( source, type, val ) {
                                    if (type == "set") {
                                        source.previous_gross_pay = val;
                                        source.previous_gross_pay_display = "<div>" + custom_number_format(val) + "</div>";
                                        return;
                                    }else if (type == "display") {
                                        return source.previous_gross_pay_display;
                                    }
                                    return source.previous_gross_pay;
                                },
                                "sClass":"number",
                                "asSorting": [ "desc", "asc" ]
                            },
                            {
                              "aTargets": [2]
                            }

                        ]
                    }
                    ';
}
$node->widgetConfig->gridConfig->dataTableOptions = $dataTableOptions;
widget_highcharts_add_datatable_js($node);
