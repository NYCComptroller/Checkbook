<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE 
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/
?>
<?php

$hidePrevLabel = (isset($node->widgetConfig->chartConfig->series[0]->showInLegend) && ($node->widgetConfig->chartConfig->series[0]->showInLegend == false) );

$SeriesPreviousYearLabel = $node->widgetConfig->chartConfig->series[0]->name;
$SeriesCurrentYearLabel = $node->widgetConfig->chartConfig->series[1]->name;

include_once('spending_title.php');
include_once('page_title.php');
include_once('export_link.php');
?>


<table id="table_<?php echo widget_unique_identifier($node) ?>" class="<?php echo $node->widgetConfig->gridConfig->html_class ?>">
    <thead>
    <?php
       echo "<tr><th class='text'><div><span>Month</span></div></th>"
           . ( $hidePrevLabel ? "" : "<th class='number'><div><span>$SeriesPreviousYearLabel</span></div></th>")
           . "<th class='number'><div><span>$SeriesCurrentYearLabel</span></div></th>
       <th>&nbsp</th>
       </tr>\n";
    ?>
    </thead>

    <tbody>
    <?php
        $months = array();
        if (isset($node->data) && is_array($node->data)) {
            $cnt =1;
            foreach ($node->data as $datarow) {
                $months[] = $datarow['month_month_month_name'];
                echo "<tr>";
                   echo '<td>' . $cnt . '</td>';
                   echo ( $hidePrevLabel ? '' : ('<td>' . $datarow['previous_spending'] . '</td>') );
                   echo '<td>' . $datarow['current_spending'] . '</td>';
                   echo "<td>&nbsp</td>";
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
                                        source.month_display = "<div>" + monthList[(val-1)] + "</div>";
                                        return;
                                    }else if (type == "display") {
                                        return source.month_display;
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
                                        source.previous_spending = val;
                                        source.previous_spending_display =  "<div>" + custom_number_format(val) + "</div>";
                                        return;
                                    }else if (type == "display") {
                                        return source.previous_spending_display;
                                    }
                                    return source.previous_spending;
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
                                        source.current_spending = val;
                                        source.current_spending_display =  "<div>" + custom_number_format(val) + "</div>";
                                        return;
                                    }else if (type == "display") {
                                        return source.current_spending_display;
                                    }
                                    return source.current_spending;
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
                                        source.month_display = "<div>" + monthList[(val-1)] + "</div>";
                                        return;
                                    }else if (type == "display") {
                                        return source.month_display;
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
                                        source.current_spending = val;
                                        source.current_spending_display =  "<div>" + custom_number_format(val) + "</div>";
                                        return;
                                    }else if (type == "display") {
                                        return source.current_spending_display;
                                    }
                                    return source.current_spending;
                                },
                                "sClass":"number",
                                "asSorting": [ "desc", "asc" ],
                                "sWidth":"300px"
                            },
                            {
                              "aTargets": [2],
                              "sWidth":"15px"
                            }

                        ]
                    }
                    ';
}
$node->widgetConfig->gridConfig->dataTableOptions = $dataTableOptions;
widget_highcharts_add_datatable_js($node);
