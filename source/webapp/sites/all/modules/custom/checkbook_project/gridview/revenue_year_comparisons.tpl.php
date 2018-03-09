<?php
/**
*	GNU AFFERO GENERAL PUBLIC LICENSE
*	   Version 3, 19 November 2007
* This software is licensed under the GNU AGPL Version 3
* 	(see the file LICENSE for details)
*/



include_once('revenue_title.php');
include_once('page_title.php');
include_once('export_link.php');

?>


<table id="table_<?php echo widget_unique_identifier($node) ?>" class="<?php echo $node->widgetConfig->gridConfig->html_class ?>">
    <thead>
    <?php
        echo "<tr><th class='number'>" . WidgetUtil::generateLabelMapping("fiscal_year") . "</th>
        <th class='number'>"  .WidgetUtil::generateLabelMapping("recognized")  .  " </th>
        <th class='number'>" . WidgetUtil::generateLabelMapping("remaining"). "</th>
        <th>&nbsp</th>
        </tr>\n";
    ?>
    </thead>

    <tbody>
    <?php
        if (isset($node->data) && is_array($node->data)) {
            foreach ($node->data as $datarow) {
                echo "<tr>";
                   echo '<td>' . $datarow['year_year_year_value'] . '</td>';
                   echo '<td>' . $datarow['revenue_amount_sum'] . '</td>';
                   echo '<td>' . $datarow['remaining_amount'] . '</td>';
                   echo "<td>&nbsp</td>";
                echo "</tr>";
            }
        }
    ?>
    </tbody>
</table>
<?php
echo eval($node->widgetConfig->gridConfig->footer);

 $dataTableOptions ='
                    {
                        "bFilter":false,
                        "bInfo":false,
                        "bLengthChange":false,
                        "iDisplayLength":1,
                        "aaSorting":[[0,"desc"]],
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
                                        source.year_year_year_value = val;
                                        source.year_year_display = "<div>" + val + "</div>";
                                        return;
                                    }else if (type == "display") {
                                        return source.year_year_display;
                                    }else if (type == "sort") {
                                        return source.year_year_year_value;
                                    }
                                    return source.year_year_year_value;
                                },
                                "asSorting": [ "desc","asc" ],
                                "sWidth":"15px",
                                "sClass":"number"
                            },
                            {
                                "aTargets": [1],
                                "aExportFn":"function",
                                "mDataProp": function ( source, type, val ) {
                                    if (type == "set") {
                                        source.revenue_amount_sum = val;
                                        source.recognized_display = "<div>" + custom_number_format(val) + "</div>";
                                        return;
                                    }else if (type == "display") {
                                        return source.recognized_display;
                                    }else if (type == "sort") {
                                        return source.revenue_amount_sum;
                                    }
                                    return source.revenue_amount_sum;
                                },
                                "asSorting": [ "asc","desc" ],
                                "sClass":"number"
                            },
                            {
                                "aTargets": [2],
                                "aExportFn":"function",
                                "mDataProp": function ( source, type, val ) {
                                    if (type == "set") {
                                        source.remaining_amount = val;
                                        source.remaining_display =  "<div>" + custom_number_format(val) + "</div>";
                                        return;
                                    }else if (type == "display") {
                                        return source.remaining_display;
                                    }else if (type == "sort") {
                                        return source.remaining_amount;
                                    }
                                    return source.remaining_amount;
                                },
                                "asSorting": [ "asc","desc" ],
                                "sClass":"number"
                            },
                            {
                              "aTargets": [3],
                              "sWidth":"15px"
                            }

                        ]
                    }
                    ';

$node->widgetConfig->gridConfig->dataTableOptions = $dataTableOptions;
widget_highcharts_add_datatable_js($node);